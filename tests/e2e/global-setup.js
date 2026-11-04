/**
 * テスト前にログインしてストレージステート（Cookie / nonce）を保存する.
 */
const { RequestUtils } = require("@wordpress/e2e-test-utils-playwright");

module.exports = async function globalSetup(config) {
	const { baseURL, storageState } = config.projects[0].use;
	const requestUtils = await RequestUtils.setup({
		baseURL,
		storageStatePath: storageState,
		user: {
			username: process.env.WP_USERNAME,
			password: process.env.WP_PASSWORD,
		},
	});

	try {
		await requestUtils.setupRest();
	} catch (error) {
		throw new Error(
			`${baseURL} にユーザー "${process.env.WP_USERNAME}" でログインできませんでした。` +
				"wp-env を起動し、`npm run test:e2e:user` でテスト用ユーザーを作成してください。\n" +
				error.message,
		);
	} finally {
		await requestUtils.request.dispose();
	}
};
