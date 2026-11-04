<?php
/**
 * @package ruijinen-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Setup;

class Assets {
	public $sm_style_handles  = ''; //Snow Monkey のメインスタイルのハンドルを格納.

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'get_sm_style_handles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_common_style' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_common_style' ] );
	}

	/**
	 * get Snow Monkey Style Hadles.
	 */
	public function get_sm_style_handles () {
		//Snow Monkeyテーマからメインスタイルのハンドルを取得
		if ( method_exists('\Framework\Helper', 'get_main_style_handle') ) {
			$this->sm_style_handles = \Framework\Helper::get_main_style_handle();
		}
	}

	/**
	 * Enqueue common style (front / block editor).
	 */
	public function enqueue_common_style() {
		$path = 'dist/css/common.css';
		if ( ! file_exists( RJE_R001CORP_PATH . $path ) ) {
			return;
		}

		// Snow Monkey 以外のテーマではハンドルが空になるため、空の依存関係を渡さない（未登録の依存があると読み込まれない）.
		$deps = array_filter( (array) $this->sm_style_handles );

		wp_enqueue_style( RJE_R001CORP_KEY . '_common', RJE_R001CORP_URL . $path, $deps, filemtime( RJE_R001CORP_PATH . $path ) );
	}
}
