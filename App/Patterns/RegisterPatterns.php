<?php
/**
 * @package ruijinen-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Patterns;

class RegisterPatterns {
	/**
	 * Constructor.
	 */
	//TODO:できればpublic static化して静的メソッドとして呼び出したい
	public function __construct() {
		// 登録するパターンをhookに追加
		add_filter( 'rje_register_patterns_args', array( $this, 'layered1' ), 10 );
		add_filter( 'rje_register_patterns_args', array( $this, 'layered2' ), 10 );
	}

	/**
	 * 重なりキャッチ1.
	 */
	public function layered1( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_layered1',
			'title'          => '重なりキャッチ1',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_layered1' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 重なりキャッチ2.
	 */
	public function layered2( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_layered2',
			'title'          => '重なりキャッチ2',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_layered2' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}
}
