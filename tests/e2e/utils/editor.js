/**
 * パターン編集性テスト用のエディター操作ヘルパー.
 *
 * WordPress 本体の UI ラベルは日本語・英語のどちらでも動くように正規表現で持つ。
 */
const path = require("node:path");

const LABEL = {
	replace: /^(置換|Replace)$/,
	editPattern: /^(パターンを編集|Edit pattern)$/,
	mediaLeft: /^(メディアを左に表示|Show media on left)$/,
	mediaRight: /^(メディアを右に表示|Show media on right)$/,
};

const REPLACE_IMAGE = path.join(__dirname, "../fixtures/rje-e2e-replace.jpg");
const REPLACE_IMAGE_SLUG = "rje-e2e-replace";

/**
 * 選択中ブロックのツールバー（ブロック上に浮かぶもの・上部固定のどちらでも同じクラス）.
 *
 * @param {import('@playwright/test').Page} page
 */
const blockToolbar = (page) =>
	page.locator(".block-editor-block-contextual-toolbar").first();

/** 画像の位置を表す属性名（ブロックごとに異なる） */
const POSITION_ATTRIBUTES = ["mediaPosition", "imagePosition"];

/**
 * パターンを挿入する（ブロック挿入パネルからパターンを選んだときと同じブロックを挿入する）.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string}                          name パターン名（例: RJE-pattern/RJE_R001CORP_layered1）
 * @return {Promise<boolean>} パターンが登録されていない場合は false
 */
async function insertPattern(page, name) {
	const available = await page
		.waitForFunction(
			(patternName) =>
				window.wp.data
					.select("core/block-editor")
					.__experimentalGetAllowedPatterns()
					.some((pattern) => pattern.name === patternName),
			name,
			{ timeout: 15_000 },
		)
		.then(() => true)
		.catch(() => false);
	if (!available) {
		return false;
	}

	await page.evaluate((patternName) => {
		const { select, dispatch } = window.wp.data;
		const pattern = select("core/block-editor")
			.__experimentalGetAllowedPatterns()
			.find((item) => item.name === patternName);
		dispatch("core/block-editor").resetBlocks([]);
		dispatch("core/block-editor").insertBlocks(
			pattern.blocks.map((block) => window.wp.blocks.cloneBlock(block)),
		);
		dispatch("core/block-editor").clearSelectedBlock();
	}, name);
	return true;
}

/**
 * 挿入済みブロックをツリー順に平坦化して返す.
 *
 * @param {import('@playwright/test').Page} page
 */
async function getFlatBlocks(page) {
	return page.evaluate((positionAttributes) => {
		const { select } = window.wp.data;
		const result = [];
		const walk = (blocks, depth) => {
			for (const block of blocks) {
				const type = window.wp.blocks.getBlockType(block.name);
				const positionAttribute = positionAttributes.find(
					(key) => type?.attributes?.[key],
				);
				result.push({
					clientId: block.clientId,
					name: block.name,
					title: type?.title ?? block.name,
					depth,
					isValid: block.isValid,
					validationIssues: (block.validationIssues ?? []).map((issue) =>
						String(issue?.args?.[0] ?? issue),
					),
					positionAttribute: positionAttribute ?? null,
					position: positionAttribute
						? (block.attributes[positionAttribute] ??
							type.attributes[positionAttribute].default ??
							null)
						: null,
				});
				walk(block.innerBlocks, depth + 1);
			}
		};
		walk(select("core/block-editor").getBlocks(), 0);
		return result;
	}, POSITION_ATTRIBUTES);
}

/**
 * ブロックを選択し、ブロックツールバーと設定サイドバーを表示する.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string}                          clientId
 */
async function selectBlock(page, clientId) {
	await page.evaluate((id) => {
		const { dispatch } = window.wp.data;
		dispatch("core/block-editor").selectBlock(id);
		dispatch("core/edit-post").openGeneralSidebar("edit-post/block");
	}, clientId);
	await blockToolbar(page).waitFor();
}

/**
 * 選択中ブロックのツールバーから、名前に一致するボタンを返す（表示されなければ null）.
 *
 * @param {import('@playwright/test').Page} page
 * @param {RegExp}                          name
 */
async function findToolbarButton(page, name) {
	const button = blockToolbar(page).getByRole("button", { name }).first();
	return (await button
		.waitFor({ state: "visible", timeout: 2_000 })
		.then(() => true)
		.catch(() => false))
		? button
		: null;
}

/**
 * パターンの「パターンを編集」を押し、コンテンツのみの編集モードを解除する.
 *
 * @param {import('@playwright/test').Page} page
 * @return {Promise<boolean>} ボタンが無い場合は false
 */
async function enterPatternEditMode(page) {
	const rootClientId = await page.evaluate(
		() => window.wp.data.select("core/block-editor").getBlocks()[0]?.clientId,
	);
	if (!rootClientId) {
		return false;
	}
	await selectBlock(page, rootClientId);
	const button = page.getByRole("button", { name: LABEL.editPattern }).first();
	if (!(await button.isVisible())) {
		return false;
	}
	await button.click();
	return true;
}

/**
 * ツールバーのボタンを取得する。挿入直後の状態で見つからなければ「パターンを編集」後に再取得する.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string}                          clientId
 * @param {RegExp}                          name
 * @return {Promise<{ button: import('@playwright/test').Locator | null, mode: 'default' | 'pattern-edit' | null }>}
 */
async function findButtonWithFallback(page, clientId, name) {
	await selectBlock(page, clientId);
	let button = await findToolbarButton(page, name);
	if (button) {
		return { button, mode: "default" };
	}
	if (await enterPatternEditMode(page)) {
		await selectBlock(page, clientId);
		button = await findToolbarButton(page, name);
		if (button) {
			return { button, mode: "pattern-edit" };
		}
	}
	return { button: null, mode: null };
}

/**
 * ブロック自身が持つ画像（リッチテキスト内のインライン画像を除く）の src を返す.
 *
 * @param {import('@playwright/test').FrameLocator} canvas
 * @param {string}                                  clientId
 */
async function getBlockImageSources(canvas, clientId) {
	return canvas
		.locator(`[data-block="${clientId}"] img`)
		.evaluateAll((images) =>
			images
				.filter((img) => !img.closest('[contenteditable="true"]'))
				.map((img) => img.currentSrc || img.src),
		);
}

module.exports = {
	LABEL,
	POSITION_ATTRIBUTES,
	REPLACE_IMAGE,
	REPLACE_IMAGE_SLUG,
	insertPattern,
	getFlatBlocks,
	selectBlock,
	findToolbarButton,
	enterPatternEditMode,
	findButtonWithFallback,
	getBlockImageSources,
};
