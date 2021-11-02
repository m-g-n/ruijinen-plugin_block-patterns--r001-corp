<?php
/**
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R002_LP;

class PluginActivate {

	//初期処理
	public function __construct() {
		add_action( 'admin_init',  array( $this, 'this_plugin_activate' ) );
	}

	//アクティベートチェック
	public function this_plugin_activate() {
		if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
			if ( !is_plugin_active( 'ruijinen-block-patterns/ruijinen-block-patterns.php' ) ) {
				deactivate_plugins( RJE_R001CORP_BASENAME );
				add_action( 'admin_notices', array ( $this, 'alert_notice_petterns_master' ) );
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
	}

	//類人猿パターンプラグインベータ版がアクティベートされてた場合のエラーメッセージ
	public function alert_notice_petterns_master() {
		?>
	<div class="error">
		<p><?php esc_html_e( '[RUI-JIN-EN Block Patterns] To use the LP patterns, you need the rui-jin-en Block Patterns plugin.', 'ruijinen-block-patterns-r002-corp' ); ?></p>
	</div>
		<?php
	}
}

new PluginActivate();