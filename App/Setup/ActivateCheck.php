<?php
/**
 * @package ruijinen-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Setup;

class ActivateCheck {
	//プロパティ
	public $messages   = array();

	//初期処理
	public function __construct() {
		$this->check_rje_block_patterns_activate();
	}

	//類人猿ブロックパターンプラグイン本体が有効かチェック.
	public function check_rje_block_patterns_activate () {
		if ( !class_exists('\Ruijinen\Pattern\RegisterBlockPatterns') ) {
			$this->messages['rje_r000_master'] = '類人猿ブロックパターンプラグイン本体が必要です';
		}
	}

	//必要なパッケージがアクティベートされてない場合のエラーメッセージ
	public function make_alert_message() {
		$alert_html = '<div class="notice notice-warning is-dismissible"><p><strong>[類人猿コーポレートサイトパターン集]</strong></p>';
		foreach ( $this->messages as $text ) {
			$alert_html .= '<p>'.$text.'</p>';
		}
		$alert_html .= '</div>';
		echo $alert_html;
	}
}