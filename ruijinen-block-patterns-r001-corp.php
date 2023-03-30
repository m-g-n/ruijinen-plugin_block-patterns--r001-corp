<?php
/**
 * Plugin name: 類人猿ブロックパターン：企業サイト向けパターン集
 * Description: 類人猿ブロックパターン 企業サイト向けパターンアドオンです
 * Version: 3.1.3
 * Tested up to: 6.2
 * Requires at least: 6.2
 * Author: mgn Inc.,
 * Author URI: https://rui-jin-en.com/
 * License: GPL-2.0+
 *
 * @package ruijinen-block-patterns
 */

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
	}

	/**
	 * Bootstrap.
	 */
	public function bootstrap() {
		new App\Setup\AutoUpdate();
		new App\Setup\InPluginUpdateMessage();

		//アクティベートチェックを行い問題がある場合はメッセージを出し離脱する.
		$activate_check = new App\Setup\ActivateCheck();
		if ( !empty( $activate_check->messages ) ) {
			add_action('admin_notices', array( $activate_check,'make_alert_message'));
			return;
		}

		//アクティブテーマ名を取得
		$this->get_activate_theme();

		// コーポレートパターン用の汎用CSS・JS読み込み・テンプレートの読み込み
		add_theme_support( 'editor-styles' );
		new App\Setup\Assets();
		$this->register_patterns();
	}

	/**
	 * Load Textdomain.
	 */
	public function load_textdomain() {
		new App\Setup\TextDomain();
	}

	/**
	 * Get Activate Themes.
	 */
	public function get_activate_theme() {
		if ( defined ('RJE_ACTIVATE_THEME') ) { return; } //アクティブテーマ用の定数が存在する場合は離脱
		$theme = wp_get_theme( get_template() );
		if ( 'snow-monkey/resources' === $theme->template ) { //古いバージョンのSnow Monkey対応
			$name = 'snow-monkey';
		} else {
			$name = $theme->template;
		}
		define( 'RJE_ACTIVATE_THEME', $name );
	}

	/**
	 * Register Block Patterns.
	 */
	public function register_patterns() {
		global $rje_r001corp_patterns;
		$rje_r001corp_patterns = new App\Patterns\RegisterPatterns('rje_r001corp_patterns');
		new App\Patterns\RegisterCategory();
	}
}

new Bootstrap();
