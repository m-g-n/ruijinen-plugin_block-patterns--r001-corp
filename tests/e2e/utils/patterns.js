/**
 * テスト対象のパターン一覧.
 */

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

/**
 * パターンの登録名を返す.
 *
 * @param {string} key パターンのキー（例: layered1）
 */
const patternName = (key) => `RJE-pattern/RJE_R001CORP_${key}`;

module.exports = { PATTERNS, patternName };
