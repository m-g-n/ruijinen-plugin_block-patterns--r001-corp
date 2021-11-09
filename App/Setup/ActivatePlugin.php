<?php
/**
 * @package ruijinen-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Setup;

class ActivatePlugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init',  array( $this, 'plugin_activate' ) );
	}

	/**
	 * Check the required environmen and Plugin Activation.
	 */
	public function plugin_activate() {
		if ( !is_admin() || !current_user_can( 'activate_plugins' ) ) {
			return;
		}
		//ブロックパターンプラグイン本体のアクティベートチェック
		if ( !is_plugin_active( 'ruijinen-block-patterns/ruijinen-block-patterns.php' ) ) {
			deactivate_plugins( RJE_R001CORP_BASENAME );
			add_action( 'admin_notices', array ( $this, 'alert_notice_petterns_master' ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}

	/**
	 * 類人猿パターンプラグイン本体が有効でない場合のメッセージ.
	 */
	public function alert_notice_petterns_master() {
		?>
	<div class="error">
		<p><?php esc_html_e( '[RUI-JIN-EN Block Patterns] To use the LP patterns, you need the rui-jin-en Block Patterns plugin.', 'ruijinen-block-patterns-r001-corp' ); ?></p>
	</div>
		<?php
	}
}