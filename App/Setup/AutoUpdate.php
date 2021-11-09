<?php
/**
 * @package ruijinen-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Setup;

use Inc2734\WP_GitHub_Plugin_Updater\Bootstrap as Updater;

/**
 * アップデートの有無の検知及び実施
 */
class AutoUpdate {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'activate_autoupdate' ) );
	}

	/**
	 * Activate auto update using GitHub,
	 *
	 * @return void
	 */
	public function activate_autoupdate() {
		new Updater(
			RJE_R001CORP_BASENAME,
			'm-g-n',
			'ruijinen-plugin_block-patterns--r001-corp',
			[
				'homepage' => 'https://rui-jin-en.com',
			]
		);
	}
}