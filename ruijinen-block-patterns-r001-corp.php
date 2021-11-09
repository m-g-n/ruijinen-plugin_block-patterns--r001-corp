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


//TODO: 各ファイルの翻訳を作る

namespace Ruijinen\Pattern\R001CORP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * declaration constant.
 */
define( 'RJE_R001CORP_KEY', 'RJE_R001CORP' ); // どの類人猿プロダクトなのかを示すキー
define( 'RJE_R001CORP_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) . '/' ); // このプラグインのURL
define( 'RJE_R001CORP_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' ); // このプラグインのパス
define( 'RJE_R001CORP_BASENAME', plugin_basename( __FILE__ ) ); // このプラグインのベースネーム.
define( 'RJE_R001CORP_TEXTDOMAIN', 'ruijinen-r001-corp' ); //テキストドメイン名.

/**
 * include files.
 */
require_once(RJE_R001CORP_PATH . 'vendor/autoload.php'); //アップデート用composer.

//各処理用のクラスを読み込む
foreach (glob(RJE_R001CORP_PATH.'App/**/*.php') as $filename) {
	require_once $filename;
}

/**
 * 初期設定.
 */
class Bootstrap {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'bootstrap' ] );
		add_action( 'plugins_loaded', [ $this, 'register_patterns' ] );
		// add_action( 'plugins_loaded', [ $this, 'test_remove_hook' ], 20 ); //TODO:テスト
	}

	/**
	 * Bootstrap.
	 */
	public function bootstrap() {
		//クラスオブジェクト作成
		new App\Setup\ActivatePlugin();
		// new App\Setup\AutoUpdate(); //TODO：リリースされたらコメントアウト外す
		new App\Setup\TextDomain();
		new App\Setup\Assets();
	}

	/**
	 * Register Block Patterns.
	 */
	public function register_patterns() {
		//TODO：グローバル変数ではなく、静的メソッドとして呼び出したい
		global $rje_r001corp_patterns;
		$rje_r001corp_patterns = new App\Patterns\RegisterPatterns();
		new App\Patterns\RegisterCategory();
	}


	/**
	 * remove テスト.
	 */
	public function test_remove_hook () {
		//TODO：静的メソッドでフックの関数のエリアを指定したい
		global $rje_r001corp_patterns;
		// remove_filter( 'rje_register_patterns_args', array( $rje_r001corp_patterns, 'layered1' ), 10 );
	}
}

new Bootstrap();
