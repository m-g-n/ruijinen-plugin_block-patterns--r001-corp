<?php
/**
 * @package ruijinen-skin-r002-lp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Setup;

class Assets {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
	}

	/**
	 * Enqueue front assets
	 */
	public function wp_enqueue_scripts() {
		$path = 'dist/css/common.css';
		wp_enqueue_style( RJE_R001CORP_KEY . '_common', RJE_R001CORP_URL . $path, [ \Framework\Helper::get_main_style_handle() ], filemtime( RJE_R001CORP_PATH . $path ) );
	}

	/**
	 * Enqueue Block Editor Assets
	 */
	public function enqueue_block_editor_assets() {
		$path = 'dist/css/common.css';
		wp_enqueue_style( RJE_R001CORP_KEY . '_common', RJE_R001CORP_URL . $path, [ \Framework\Helper::get_main_style_handle() ], filemtime( RJE_R001CORP_PATH . $path ) );
	}
}
