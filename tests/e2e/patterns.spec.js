/**
 * 企業サイト向けパターンの編集性テスト.
 *
 * パターンごとに、ブロック挿入パネルから挿入したときと同じ状態で次を確認する。
 * - ブロックの妥当性: 挿入したブロックが「予期しないコンテンツ」になっていない
 * - テキストの差し替え: 編集可能なテキストをすべて書き換えられる
 * - 画像の差し替え: ブロックの画像を「置換」から別の画像に差し替えられる
 * - 画像とテキストの左右入れ替え: 画像の位置を左右反転でき、エディター・フロントの両方で並びが入れ替わる
 *
 * WordPress 7 以降、挿入したパターンは「コンテンツのみ」の編集モードになる。
 * 挿入直後の状態で操作できない場合は「パターンを編集」を押してから再試行し、
 * どちらの状態で操作できたかを annotation（HTML レポートの各テスト）に記録する。
 */
const { test, expect } = require("@wordpress/e2e-test-utils-playwright");
const {
	LABEL,
	REPLACE_IMAGE,
	REPLACE_IMAGE_SLUG,
	insertPattern,
	getFlatBlocks,
	findButtonWithFallback,
	getBlockImageSources,
} = require("./utils/editor");

/** App/Patterns/RegisterPatterns.php で登録しているパターン */
const PATTERNS = [
	{ key: "layered1", title: "重なりキャッチ1" },
	{ key: "layered2", title: "重なりキャッチ2" },
	{ key: "person", title: "人物キャッチ" },
	{ key: "recent_posts", title: "最新の投稿", snowMonkeyOnly: true },
	{ key: "tax_posts", title: "任意のタクソノミーの投稿", snowMonkeyOnly: true },
	{ key: "simple_items", title: "シンプルな項目" },
	{ key: "items_with_bg", title: "背景画像あり項目" },
	{ key: "one_column_items", title: "1カラムの項目" },
	{ key: "steps", title: "流れ" },
	{ key: "induction", title: "任意ページへ誘導" },
	{ key: "infomation", title: "シンプルな情報" },
	{ key: "chronology", title: "年表" },
	{ key: "accordion", title: "アコーディオン" },
	{ key: "history", title: "沿革" },
	{ key: "access", title: "アクセスMAP" },
	{ key: "banners", title: "バナー" },
	{ key: "child_pages", title: "子ページ一覧", snowMonkeyOnly: true },
	{ key: "cta", title: "お問い合わせ誘導" },
];

const MODE_LABEL = {
	default: "挿入直後のまま操作可",
	"pattern-edit": "「パターンを編集」後に操作可",
};

/**
 * パターンを挿入する。登録されていなければテストをスキップする.
 *
 * @param {import('@playwright/test').Page} page
 * @param {{ key: string, snowMonkeyOnly?: boolean }} pattern
 */
async function insertOrSkip(page, pattern) {
	const inserted = await insertPattern(
		page,
		`RJE-pattern/RJE_R001CORP_${pattern.key}`,
	);
	test.skip(
		!inserted,
		pattern.snowMonkeyOnly
			? "Snow Monkey テーマ専用のパターンのため、現在のテーマでは登録されていません"
			: "パターンが登録されていません（類人猿ブロックパターン本体・Snow Monkey Blocks が有効か確認してください）",
	);
}

/**
 * 結果を annotation と添付ファイル（表形式のテキスト）に記録する.
 *
 * @param {import('@playwright/test').TestInfo} testInfo
 * @param {string}                              title
 * @param {string[]}                            lines
 */
async function report(testInfo, title, lines) {
	for (const line of lines) {
		testInfo.annotations.push({ type: title, description: line });
	}
	await testInfo.attach(title, {
		body: lines.join("\n") || "(なし)",
		contentType: "text/plain",
	});
}

test.describe("企業サイト向けパターンの編集性", () => {
	/** テストで作成した投稿 ID（後片付け用） */
	const createdPostIds = [];

	test.beforeEach(async ({ admin }) => {
		await admin.createNewPost();
	});

	test.afterAll(async ({ requestUtils }) => {
		// 差し替えテストでアップロードした画像を削除する（他のメディアには触れない）.
		const media = await requestUtils.rest({
			path: "/wp/v2/media",
			params: { search: REPLACE_IMAGE_SLUG, per_page: 100, context: "edit" },
		});
		for (const item of media) {
			if (item.slug.startsWith(REPLACE_IMAGE_SLUG)) {
				await requestUtils.rest({
					method: "DELETE",
					path: `/wp/v2/media/${item.id}`,
					params: { force: true },
				});
			}
		}
		// 保存した下書きに加え、エディターの自動保存で下書きになった投稿も削除する.
		// 対象はテスト用ユーザーが作成した下書きのみ（他のユーザーの投稿には触れない）.
		const me = await requestUtils.rest({ path: "/wp/v2/users/me" });
		const drafts = await requestUtils.rest({
			path: "/wp/v2/posts",
			params: {
				author: me.id,
				status: "draft",
				per_page: 100,
				context: "edit",
			},
		});
		const ids = new Set([...createdPostIds, ...drafts.map((post) => post.id)]);
		for (const id of ids) {
			await requestUtils
				.rest({
					method: "DELETE",
					path: `/wp/v2/posts/${id}`,
					params: { force: true },
				})
				.catch(() => {});
		}
	});

	for (const pattern of PATTERNS) {
		test.describe(`${pattern.title}（${pattern.key}）`, () => {
			test("ブロックの妥当性", async ({ page }) => {
				await insertOrSkip(page, pattern);
				const blocks = await getFlatBlocks(page);
				expect(blocks.length).toBeGreaterThan(0);

				const invalid = blocks.filter((block) => !block.isValid);
				expect(
					invalid.map(
						(block) => `${block.name}: ${block.validationIssues.join(" / ")}`,
					),
					"予期しないコンテンツになっているブロック",
				).toEqual([]);
			});

			test("テキストの差し替え", async ({ page, editor }, testInfo) => {
				await insertOrSkip(page, pattern);

				// 編集できない表示テキスト（HTML ブロック・動的ブロックなど）を記録する.
				const fixedTexts = await editor.canvas
					.locator(".is-root-container")
					.evaluate((root) => {
						const texts = [];
						const walker = root.ownerDocument.createTreeWalker(
							root,
							NodeFilter.SHOW_TEXT,
						);
						while (walker.nextNode()) {
							const node = walker.currentNode;
							const text = node.textContent.trim();
							const parent = node.parentElement;
							if (
								text &&
								parent &&
								!parent.closest('[contenteditable="true"]') &&
								parent.checkVisibility() &&
								// ドロップゾーン等のエディター UI の文言は除外する.
								!parent.closest(
									".components-drop-zone, .components-placeholder__instructions",
								) &&
								!["SCRIPT", "STYLE"].includes(parent.tagName)
							) {
								texts.push(text.slice(0, 60));
							}
						}
						return [...new Set(texts)];
					});
				await report(testInfo, "編集できない表示テキスト", fixedTexts);

				const fields = editor.canvas.locator(
					'.is-root-container [contenteditable="true"]',
				);
				// ブロックを選択すると空の入力欄（サブタイトル等）が増えて順番がずれるため、
				// 編集前に表示されている欄へ目印を付けて、目印で特定する.
				const count = await fields.evaluateAll((elements) => {
					elements.forEach((el, i) =>
						el.setAttribute("data-rje-e2e-field", String(i)),
					);
					return elements.length;
				});
				test.skip(
					count === 0,
					"編集可能なテキストがないパターンです（動的ブロックのみ）",
				);

				const tokens = [];
				let hidden = 0;
				for (let i = 0; i < count; i++) {
					const field = editor.canvas.locator(`[data-rje-e2e-field="${i}"]`);
					if (!(await field.isVisible())) {
						hidden++;
						continue;
					}
					const token = `RJE-E2E-TEXT-${i}`;
					// キーボードの全選択は複数行のリッチテキストで効かない場合があるため、
					// 要素の内容をまるごと置き換える fill() で書き換える（ポップオーバーにも遮られない）.
					await field.fill(token);
					tokens.push(token);
				}
				if (hidden) {
					await report(testInfo, "非表示のため未検証のテキスト", [
						`${hidden} 件（アコーディオンの閉じた中身など）`,
					]);
				}

				expect(tokens.length, "書き換えたテキスト数").toBeGreaterThan(0);
				const content = await editor.getEditedPostContent();
				for (const token of tokens) {
					expect
						.soft(content, `${token} が保存内容に反映される`)
						.toContain(token);
				}
			});

			test("画像の差し替え", async ({ admin, page, editor }, testInfo) => {
				await insertOrSkip(page, pattern);

				// 画像を持つブロック（インライン画像を除く）をツリー順のインデックスで特定する.
				const blocks = await getFlatBlocks(page);
				const ownerIds = await editor.canvas
					.locator(".is-root-container")
					.evaluate((root) => [
						...new Set(
							[...root.querySelectorAll("img")]
								.filter((img) => !img.closest('[contenteditable="true"]'))
								.map((img) => img.closest("[data-block]")?.dataset.block)
								.filter(Boolean),
						),
					]);
				const owners = ownerIds.map((id) =>
					blocks.findIndex((block) => block.clientId === id),
				);
				const inlineImages = await editor.canvas
					.locator('.is-root-container [contenteditable="true"] img')
					.count();
				if (inlineImages) {
					await report(testInfo, "未検証のインライン画像", [
						`${inlineImages} 件（ボタンや段落の文字中に挿入されたアイコン画像。リッチテキストの画像は置換ではなく削除・再挿入で差し替える）`,
					]);
				}
				test.skip(owners.length === 0, "差し替え対象の画像がないパターンです");

				const results = [];
				for (const [n, index] of owners.entries()) {
					await test.step(`${n + 1}枚目: ${blocks[index].title}`, async () => {
						// 「パターンを編集」の状態は同じ投稿内で引き継がれるため、毎回新しい投稿に挿入し直す.
						await admin.createNewPost();
						await insertPattern(
							page,
							`RJE-pattern/RJE_R001CORP_${pattern.key}`,
						);
						const { clientId, title } = (await getFlatBlocks(page))[index];
						const before = await getBlockImageSources(editor.canvas, clientId);

						const { button, mode } = await findButtonWithFallback(
							page,
							clientId,
							LABEL.replace,
						);
						if (!button) {
							results.push(`NG ${title}: 「置換」ボタンが表示されない`);
							return;
						}
						await button.click();
						await page
							.locator(
								'.block-editor-media-replace-flow__options input[type="file"]',
							)
							.setInputFiles(REPLACE_IMAGE);

						const replaced = await expect
							.poll(
								async () =>
									(await getBlockImageSources(editor.canvas, clientId)).some(
										(src) =>
											src.includes(REPLACE_IMAGE_SLUG) && !before.includes(src),
									),
								{ timeout: 30_000 },
							)
							.toBe(true)
							.then(() => true)
							.catch(() => false);
						await page.keyboard.press("Escape");
						results.push(
							replaced
								? `OK ${title}: ${MODE_LABEL[mode]}`
								: `NG ${title}: アップロード後も画像が差し替わらない`,
						);
					});
				}

				await report(testInfo, "画像の差し替え結果", results);
				expect(
					results.filter((line) => line.startsWith("NG")),
					"差し替えできない画像",
				).toEqual([]);
			});

			test("画像とテキストの左右入れ替え", async ({
				admin,
				page,
				editor,
			}, testInfo) => {
				await insertOrSkip(page, pattern);

				const blocks = await getFlatBlocks(page);
				const targets = blocks
					.map((block, index) => ({ ...block, index }))
					.filter((block) => ["left", "right"].includes(block.position));
				test.skip(
					targets.length === 0,
					"画像の左右を切り替えられるブロックがないパターンです",
				);

				const results = [];
				const swapped = [];
				for (const target of targets) {
					await test.step(`${target.title}（${target.position} → 反転）`, async () => {
						await admin.createNewPost();
						await insertPattern(
							page,
							`RJE-pattern/RJE_R001CORP_${pattern.key}`,
						);
						const { clientId } = (await getFlatBlocks(page))[target.index];
						const next = target.position === "right" ? "left" : "right";

						const { button, mode } = await findButtonWithFallback(
							page,
							clientId,
							next === "left" ? LABEL.mediaLeft : LABEL.mediaRight,
						);
						if (!button) {
							results.push(
								`NG ${target.title}: 左右切り替えボタンが表示されない`,
							);
							return;
						}
						await button.click();

						const position = await page.evaluate(
							({ id, key }) =>
								window.wp.data
									.select("core/block-editor")
									.getBlockAttributes(id)[key],
							{ id: clientId, key: target.positionAttribute },
						);
						if (position !== next) {
							results.push(
								`NG ${target.title}: 属性 ${target.positionAttribute} が ${position} のまま`,
							);
							return;
						}

						const layout = await measureLayout(
							editor.canvas.locator(`[data-block="${clientId}"]`),
						);
						results.push(
							layout === next
								? `OK ${target.title}: ${MODE_LABEL[mode]}（エディターで画像が${next === "left" ? "左" : "右"}）`
								: `NG ${target.title}: 属性は変わったがエディターの表示で画像が${next === "left" ? "左" : "右"}にならない（判定: ${layout}）`,
						);
						swapped.push({ ...target, next });
					});
				}

				// 反転したブロックを含む状態で保存し、フロント（プレビュー）でも並びを確認する.
				if (swapped.length) {
					await test.step("フロントでの表示確認", async () => {
						await admin.createNewPost();
						await insertPattern(
							page,
							`RJE-pattern/RJE_R001CORP_${pattern.key}`,
						);
						await page.evaluate((items) => {
							const { select, dispatch } = window.wp.data;
							const flat = [];
							const walk = (list) =>
								list.forEach((block) => {
									flat.push(block);
									walk(block.innerBlocks);
								});
							walk(select("core/block-editor").getBlocks());
							for (const item of items) {
								dispatch("core/block-editor").updateBlockAttributes(
									flat[item.index].clientId,
									{
										[item.positionAttribute]: item.next,
									},
								);
							}
						}, swapped);
						// editor.saveDraft() は英語 UI のラベル前提のため、データ API で下書き保存する.
						const postId = await page.evaluate(async () => {
							const { select, dispatch } = window.wp.data;
							await dispatch("core/editor").savePost();
							return select("core/editor").getCurrentPostId();
						});
						createdPostIds.push(postId);

						await page.goto(`/?p=${postId}&preview=true`);
						for (const item of swapped) {
							// フロントの DOM 順はブロックのツリー順と同じなので、同種ブロック内の順番で特定する.
							const sameTypeIndex = blocks.filter(
								(block, index) =>
									block.name === item.name && index < item.index,
							).length;
							const selector = FRONT_SELECTORS[item.name];
							if (!selector) {
								results.push(
									`-- ${item.title}: フロントの確認対象外のブロック`,
								);
								continue;
							}
							const layout = await measureLayout(
								page.locator(selector).nth(sameTypeIndex),
							);
							results.push(
								layout === item.next
									? `OK ${item.title}: フロントで画像が${item.next === "left" ? "左" : "右"}`
									: `NG ${item.title}: フロントで画像が${item.next === "left" ? "左" : "右"}にならない（判定: ${layout}）`,
							);
						}
					});
				}

				await report(testInfo, "左右入れ替え結果", results);
				expect(
					results.filter((line) => line.startsWith("NG")),
					"左右を入れ替えられないブロック",
				).toEqual([]);
			});
		});
	}
});

/** フロントで各ブロックを特定するセレクター */
const FRONT_SELECTORS = {
	"snow-monkey-blocks/media-text": ".smb-media-text",
	"snow-monkey-blocks/section-break-the-grid": ".smb-section-break-the-grid",
	"core/media-text": ".wp-block-media-text",
};

/**
 * ブロック内の画像がテキストより左右どちらにあるかを判定する.
 *
 * @param {import('@playwright/test').Locator} block
 * @return {Promise<'left' | 'right' | 'stacked' | 'unknown'>}
 */
async function measureLayout(block) {
	await block.scrollIntoViewIfNeeded();
	return block.evaluate((element) => {
		const visible = (el) => {
			const rect = el.getBoundingClientRect();
			return rect.width > 0 && rect.height > 0;
		};
		const area = (el) => {
			const rect = el.getBoundingClientRect();
			return rect.width * rect.height;
		};
		const largest = (elements) =>
			elements.reduce(
				(max, el) => (!max || area(el) > area(max) ? el : max),
				null,
			);
		// 装飾用の小さな画像（見出し横の線など）を拾わないよう、面積が最大の画像・テキストで判定する.
		const image = largest(
			[...element.querySelectorAll("img, video")].filter(
				(el) => visible(el) && !el.closest('[contenteditable="true"]'),
			),
		);
		const text = largest(
			[
				...element.querySelectorAll(
					'h1, h2, h3, h4, h5, h6, p, li, [contenteditable="true"]',
				),
			].filter(
				(el) =>
					visible(el) && el.textContent.trim() && !el.querySelector("img"),
			),
		);
		if (!image || !text) {
			return "unknown";
		}
		const imageRect = image.getBoundingClientRect();
		const textRect = text.getBoundingClientRect();
		// 縦方向に重なりがない（縦積み）場合は左右を判定しない.
		// 画像とテキストが重なるデザイン（ブロークングリッド等）もあるため、中心の x 座標で比較する.
		if (imageRect.bottom <= textRect.top || textRect.bottom <= imageRect.top) {
			const describe = (el, r) =>
				`${el.tagName.toLowerCase()}.${[...el.classList].join(".")}[y=${Math.round(r.top)}-${Math.round(r.bottom)}]`;
			return `stacked: ${describe(image, imageRect)} / ${describe(text, textRect)}`;
		}
		const imageCenter = imageRect.left + imageRect.width / 2;
		const textCenter = textRect.left + textRect.width / 2;
		return imageCenter < textCenter ? "left" : "right";
	});
}
