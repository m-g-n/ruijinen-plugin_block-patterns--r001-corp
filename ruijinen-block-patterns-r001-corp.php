<?php
/**
 * Plugin name: 類人猿ブロックパターン：コーポレートサイト向けパターン集
 * Description: 類人猿ブロックパターン コーポレートサイト向けパターンアドオンです
 * Version: 0.0.0.0.4
 * Tested up to: 5.9
 * Requires at least: 5.9
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

define( 'RJE_R001CORP_DIRNAME', basename(__DIR__) ); //テキストドメイン名.


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
		add_action( 'init', [ $this, 'load_textdomain' ] );
		add_theme_support( 'editor-styles' ); //TODO: あとでちゃんと別の場所に移動する
	}

	/**
	 * Bootstrap.
	 */
	public function bootstrap() {
		new App\Setup\AutoUpdate();

		//アクティベートチェックを行い問題がある場合はメッセージを出し離脱する.
		$activate_check = new App\Setup\ActivateCheck();
		if ( !empty( $activate_check->messages ) ) {
			add_action('admin_notices', array( $activate_check,'make_alert_message'));
			return;
		}

		// コーポレートパターン用の汎用CSS・JS読み込み・テンプレートの読み込み
		new App\Setup\Assets();
		$this->register_patterns();

		// add_action( 'plugins_loaded', [ $this, 'test_remove_hook' ], 20 ); //TODO:テスト
	}

	/**
	 * Load Textdomain.
	 */
	public function load_textdomain() {
		new App\Setup\TextDomain();
	}

	/**
	 * Register Block Patterns.
	 */
	public function register_patterns() {
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
		remove_filter( 'rje_register_patterns_args', array( $rje_r001corp_patterns, 'layered1' ), 10 );
	}
}

new Bootstrap();
