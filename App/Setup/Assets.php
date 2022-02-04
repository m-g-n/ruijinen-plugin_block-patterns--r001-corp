<?php
/**
 * @package ruijinen-skin-r002-lp
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
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
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
	 * Enqueue front assets
	 */
	public function wp_enqueue_scripts() {
		$path = 'dist/css/common.css';
		wp_enqueue_style( RJE_R001CORP_KEY . '_common', RJE_R001CORP_URL . $path, [ $this->sm_style_handles ], filemtime( RJE_R001CORP_PATH . $path ) );
	}

	/**
	 * Enqueue Block Editor Assets
	 */
	public function enqueue_block_editor_assets() {
		$path = 'dist/css/common.css';
		wp_enqueue_style( RJE_R001CORP_KEY . '_common', RJE_R001CORP_URL . $path, [ $this->sm_style_handles ], filemtime( RJE_R001CORP_PATH . $path ) );
	}
}
