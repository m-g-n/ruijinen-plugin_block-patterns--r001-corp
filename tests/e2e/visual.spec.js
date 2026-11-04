/**
 * パターンの見た目の比較テスト（ビジュアルリグレッションテスト）.
 *
 * パターンごとに公開ページを一時的に作成し、ログアウト状態のフロント表示を
 * 4 つの画面幅で撮影して、基準画像（tests/e2e/__screenshots__/）と比較する。
 *
 *   npm run test:vrt:update  … 現在の表示を基準画像として保存（変更前に実行）
 *   npm run test:vrt         … 基準画像と比較（変更後に実行）
 *
 * 作成したページはテスト終了時に削除する。
 */
const { test, expect } = require("@wordpress/e2e-test-utils-playwright");
const { PATTERNS, patternName } = require("./utils/patterns");

/** BackstopJS で使っていた画面幅 */
const VIEWPORTS = [
	{ label: "sp", width: 375, height: 667 },
	{ label: "md", width: 768, height: 1024 },
	{ label: "lg", width: 1024, height: 768 },
	{ label: "xg", width: 1280, height: 768 },
];

/** 撮影対象（Snow Monkey のコンテンツ領域。他テーマでは main） */
const TARGET_SELECTOR = ".l-contents__container";
const FALLBACK_SELECTOR = "main";

/** 撮影前に非表示にする要素（パンくず・ページタイトル） */
const HIDDEN_SELECTORS = [".p-breadcrumbs-wrapper", ".c-entry__header"];

const PAGE_SLUG_PREFIX = "rje-vrt-";

test.describe("パターンの見た目の比較", () => {
	// 管理バーなどが写らないようログアウト状態で表示する.
	test.use({ storageState: { cookies: [], origins: [] } });

	/** パターンのキー → 作成したページの URL */
	const pages = new Map();

	const deleteTestPages = async (requestUtils) => {
		const existing = await requestUtils.rest({
			path: "/wp/v2/pages",
			params: {
				search: PAGE_SLUG_PREFIX,
				status: "publish,draft",
				per_page: 100,
				context: "edit",
			},
		});
		for (const page of existing) {
			if (page.slug.startsWith(PAGE_SLUG_PREFIX)) {
				await requestUtils.rest({
					method: "DELETE",
					path: `/wp/v2/pages/${page.id}`,
					params: { force: true },
				});
			}
		}
	};

	test.beforeAll(async ({ requestUtils }) => {
		// 前回中断したときの残りを片付けてから作成する.
		await deleteTestPages(requestUtils);

		const registered = await requestUtils.rest({
			path: "/wp/v2/block-patterns/patterns",
		});
		for (const pattern of PATTERNS) {
			const found = registered.find(
				(item) => item.name === patternName(pattern.key),
			);
			if (!found) {
				continue;
			}
			const page = await requestUtils.rest({
				method: "POST",
				path: "/wp/v2/pages",
				data: {
					title: `${PAGE_SLUG_PREFIX}${pattern.key}`,
					slug: `${PAGE_SLUG_PREFIX}${pattern.key}`,
					status: "publish",
					content: found.content,
				},
			});
			pages.set(pattern.key, page.link);
		}
	});

	test.afterAll(async ({ requestUtils }) => {
		await deleteTestPages(requestUtils);
	});

	for (const pattern of PATTERNS) {
		test.describe(`${pattern.title}（${pattern.key}）`, () => {
			for (const viewport of VIEWPORTS) {
				test(`${viewport.label}（${viewport.width}px）`, async ({ page }) => {
					test.skip(
						!pages.has(pattern.key),
						pattern.snowMonkeyOnly
							? "Snow Monkey テーマ専用のパターンのため、現在のテーマでは登録されていません"
							: "パターンが登録されていません",
					);

					await page.setViewportSize({
						width: viewport.width,
						height: viewport.height,
					});
					await page.goto(pages.get(pattern.key), { waitUntil: "networkidle" });
					await page.addStyleTag({
						content: `${HIDDEN_SELECTORS.join(",")}{display:none!important}`,
					});
					// 遅延読み込みの画像・Web フォントの読み込みを待つ.
					await page.evaluate(async () => {
						document.querySelectorAll('img[loading="lazy"]').forEach((img) => {
							img.loading = "eager";
						});
						await Promise.all(
							[...document.images]
								.filter((img) => !img.complete)
								.map(
									(img) =>
										new Promise((resolve) => {
											img.addEventListener("load", resolve, { once: true });
											img.addEventListener("error", resolve, { once: true });
										}),
								),
						);
						await document.fonts.ready;
					});

					const target = (await page.locator(TARGET_SELECTOR).count())
						? page.locator(TARGET_SELECTOR).first()
						: page.locator(FALLBACK_SELECTOR).first();
					await expect(target).toHaveScreenshot(
						`${pattern.key}-${viewport.label}.png`,
						{
							// 外部の地図など、読み込みのたびに変わりうる埋め込みは塗りつぶす.
							mask: [page.locator("iframe")],
						},
					);
				});
			}
		});
	}
});
