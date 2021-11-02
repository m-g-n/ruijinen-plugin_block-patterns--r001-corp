<?php
/**
 * Plugin name: 類人猿ブロックパターン：コーポレートサイト向けパターン集
 * Description: 類人猿ブロックパターン コーポレートサイト向けパターンアドオンです
 * Version: 0.0.0.1
 * Tested up to: 5.8.1
 * Requires at least: 5.8.1
 * Author: mgn Inc.,
 * Author URI: https://rui-jin-en.com/
 * License: GPL-2.0+
 *
 * @package ruijinen-block-patterns
 */

/**
 * 定数・グローバル変数
 */
define( 'RJE_R001CORP_KEY', 'RJE_R001CORP' ); // どの類人猿プロダクトなのかを示すキー
define( 'RJE_R001CORP_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) . '/' ); // このプラグインのURL
define( 'RJE_R001CORP_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' ); // このプラグインのパス
define( 'RJE_R001CORP_BASENAME', plugin_basename( __FILE__ ) ); // このプラグインのベースネーム.


/**
 * テキストドメインを宣言
 */
add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'ruijinen-block-patterns-r002-corp', false, dirname( RJE_R001CORP_BASENAME ) . '/languages/' );
	}
);


/**
* 各ファイルの読み込み
*/
require_once RJE_R001CORP_PATH . 'inc/activate.php'; // 類人猿ブロックパターン本体 が有効化されていない場合の処理.
require_once RJE_R001CORP_PATH . 'inc/auto-update.php'; // 自動アップデート.
require_once RJE_R001CORP_PATH . 'vendor/autoload.php'; // 自動アップデート処理用のcomposer読み込み.

/**
* LPブロックパターン共通のスタイル・スクリプトの読み込み
*/
add_action(
	'wp_enqueue_scripts',
	function () {
		$path = 'dist/css/common.css';
		wp_enqueue_style( RJE_R001CORP_KEY . 'common', RJE_R001CORP_URL . $path, [ \Framework\Helper::get_main_style_handle() ], filemtime( RJE_R001CORP_PATH . $path ) );
	},
	10
);
add_action(
	'enqueue_block_editor_assets',
	function () {
		$path = 'dist/css/common.css';
		wp_enqueue_style( RJE_R001CORP_KEY . 'common', RJE_R001CORP_URL . $path, [ \Framework\Helper::get_main_style_handle() ], filemtime( RJE_R001CORP_PATH . $path ) );
	},
	10
);


/**
* LPブロックパターン用のカテゴリを登録
*/
add_action(
	'init',
	function () {
		register_block_pattern_category( RJE_R001CORP_KEY, array( 'label' => '[類人猿] LPサイト' ) );
	},
	10
);


/**
* フィルターフックにてブロックパターン登録内容を追加
*
* @param array $args すでに設定されている各パターン登録内容。
*/
add_action( 'plugins_loaded', 'RJE_R001CORP_register_patterns' );
function RJE_R001CORP_register_patterns() {
	// 登録するパターンをhookに追加
	// add_filter( 'rje_register_patterns_args', 'RJE_R001CORP_hero_media_and_text', 10 );
}
function RJE_R001CORP_hero_media_and_text( $args ) {
	$args[] = array(
		'key'            => RJE_R001CORP_KEY . '_hero_media_and_text',
		'title'          => 'Heroイメージ（メディアと文章)',
		'cat'            => array( RJE_R001CORP_KEY ),
		'specific-style' => false,
		'specific-script' => false,
		'block-style'    => array( RJE_R001CORP_KEY . '_hero_media_and_text', RJE_R001CORP_KEY . '_spacer_top', RJE_R001CORP_KEY . '_spacer_bottom' ), //汎用スペーサースタイルもここで読み込む
		'path'           => RJE_R001CORP_PATH,
	);
	return $args;
}
