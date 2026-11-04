/**
 * E2E テスト用 Playwright 設定.
 *
 * プロジェクト
 *   editing : パターンの編集性テスト（patterns.spec.js）      npm run test:e2e
 *   visual  : パターンの見た目の比較テスト（visual.spec.js）  npm run test:vrt / test:vrt:update
 *
 * 既定では `npm run start` で起動した wp-env の開発環境（http://localhost:1011）を対象にする。
 * 接続先・ユーザーは環境変数で上書きできる。
 *   WP_BASE_URL  (既定: http://localhost:1011)
 *   WP_USERNAME  (既定: e2e)       ※ `npm run test:e2e:user` で作成するテスト専用ユーザー
 *   WP_PASSWORD  (既定: password)
 */
const path = require("node:path");
const { defineConfig, devices } = require("@playwright/test");

process.env.WP_BASE_URL ??= "http://localhost:1011";
process.env.WP_USERNAME ??= "e2e";
process.env.WP_PASSWORD ??= "password";
process.env.STORAGE_STATE_PATH ??= path.join(
	__dirname,
	"artifacts/storage-states/admin.json",
);

module.exports = defineConfig({
	testDir: __dirname,
	outputDir: path.join(__dirname, "artifacts/test-results"),
	// 比較の基準画像（OS・ブラウザごとに異なるため Git 管理しない）.
	snapshotPathTemplate: path.join(
		__dirname,
		"__screenshots__/{platform}/{arg}{ext}",
	),
	globalSetup: require.resolve("./global-setup.js"),
	// 開発環境の DB を共有するため直列実行にする.
	fullyParallel: false,
	workers: 1,
	retries: 0,
	timeout: 120_000,
	expect: {
		timeout: 10_000,
		toHaveScreenshot: {
			// BackstopJS 時代の misMatchThreshold 0.1% 相当.
			maxDiffPixelRatio: 0.001,
			animations: "disabled",
			caret: "hide",
		},
	},
	reporter: [
		["list"],
		[
			"html",
			{ outputFolder: path.join(__dirname, "artifacts/report"), open: "never" },
		],
	],
	use: {
		...devices["Desktop Chrome"],
		baseURL: process.env.WP_BASE_URL,
		storageState: process.env.STORAGE_STATE_PATH,
		viewport: { width: 1920, height: 1080 },
		locale: "ja-JP",
		trace: "retain-on-failure",
		screenshot: "only-on-failure",
	},
	projects: [
		{ name: "editing", testMatch: "patterns.spec.js" },
		{ name: "visual", testMatch: "visual.spec.js" },
	],
});
