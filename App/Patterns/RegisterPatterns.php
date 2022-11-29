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
	public function __construct( $variable_name ) {

		//フィルターに登録したいメソッドの情報
		$methods = array(
			array(
				'name' => 'layered1',
				'label' => '重なりキャッチ1',
				'priority' => 10
			),
			array(
				'name' => 'layered2',
				'label' => '重なりキャッチ2',
				'priority' => 10
			),
			array(
				'name' => 'person',
				'label' => '人物キャッチ',
				'priority' => 10
			),
			array(
				'name' => 'recent_posts',
				'label' => '最新の投稿',
				'priority' => 10
			),
			array(
				'name' => 'tax_posts',
				'label' => '任意のタクソノミーの投稿',
				'priority' => 10
			),
			array(
				'name' => 'simple_items',
				'label' => 'シンプルな項目',
				'priority' => 10
			),
			array(
				'name' => 'items_with_bg',
				'label' => '背景画像あり項目',
				'priority' => 10
			),
			array(
				'name' => 'one_column_items',
				'label' => '1カラムの項目',
				'priority' => 10
			),
			array(
				'name' => 'steps',
				'label' => '流れ',
				'priority' => 10
			),
			array(
				'name' => 'induction',
				'label' => '任意ページへ誘導',
				'priority' => 10
			),
			array(
				'name' => 'infomation',
				'label' => 'シンプルな情報',
				'priority' => 10
			),
			array(
				'name' => 'chronology',
				'label' => '年表',
				'priority' => 10
			),
			array(
				'name' => 'accordion',
				'label' => 'アコーディオン',
				'priority' => 10
			),
			array(
				'name' => 'history',
				'label' => '沿革',
				'priority' => 10
			),
			array(
				'name' => 'access',
				'label' => 'アクセスMAP',
				'priority' => 10
			),
			array(
				'name' => 'banners',
				'label' => 'バナー',
				'priority' => 10
			),
			array(
				'name' => 'child_pages',
				'label' => '子ページ一覧',
				'priority' => 10
			),
			array(
				'name' => 'cta',
				'label' => 'お問い合わせ誘導',
				'priority' => 10
			)
		);
		//パターンの情報をフィルターに登録
		foreach ( $methods as $method ) {
			add_filter( 'rje_register_patterns_args', array( $this, $method['name'] ), $method['priority'] );
		}
		//無効化のオプションページに情報を登録
		if ( has_filter( 'rje_option_unregister_args' ) ) {
			add_filter(
				'rje_option_unregister_args',
				function ( $args ) use ( $variable_name, $methods ) {
					$corp = array(
						'section_id'   => $variable_name,
						'section_name' => '企業サイト向けパターン集',
						'fields' => $methods
					);
					array_push( $args, $corp );
					return $args;
				}
			);
		}
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

	/**
	 * 人物キャッチ.
	 */
	public function person( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_person',
			'title'          => '人物キャッチ',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_person' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 最近の投稿一覧.
	 */
	public function recent_posts( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_recent_posts',
			'title'          => '最新の投稿',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_recent_posts' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * タクソノミー投稿一覧.
	 */
	public function tax_posts( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_tax_posts',
			'title'          => '任意のタクソノミーの投稿',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_recent_posts' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * シンプルな項目.
	 */
	public function simple_items( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_simple_items',
			'title'          => 'シンプルな項目',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_simple_items' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 背景画像あり項目.
	 */
	public function items_with_bg( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_items_with_bg',
			'title'          => '背景画像あり項目',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_items_with_bg' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 1カラムの項目.
	 */
	public function one_column_items( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_one_column_items',
			'title'          => '1カラムの項目',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => true,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_one_column_item' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 流れ.
	 */
	public function steps( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_steps',
			'title'          => '流れ',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_steps' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 任意ページへ誘導.
	 */
	public function induction( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_induction',
			'title'          => '任意ページへ誘導',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_induction' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * シンプルな情報.
	 */
	public function infomation( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_infomation',
			'title'          => 'シンプルな情報',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_infomation' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 年表.
	 */
	public function chronology( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_chronology',
			'title'          => '年表',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_chronology' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * アコーディオン.
	 */
	public function accordion( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_accordion',
			'title'          => 'アコーディオン',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_accordion' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 沿革.
	 */
	public function history( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_history',
			'title'          => '沿革',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_history_year', RJE_R001CORP_KEY . '_history_month' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * アクセスMAP.
	 */
	public function access( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_access',
			'title'          => 'アクセスMAP',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_access' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * バナー.
	 */
	public function banners( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_banners',
			'title'          => 'バナー',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_banners' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * 子ページ一覧.
	 */
	public function child_pages( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_child_pages',
			'title'          => '子ページ一覧',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_child_pages' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}

	/**
	 * お問い合わせ誘導.
	 */
	public function cta( $args ) {
		$args[] = array(
			'key'            => RJE_R001CORP_KEY . '_cta',
			'title'          => 'お問い合わせ誘導',
			'cat'            => array( RJE_R001CORP_KEY ),
			'specific-style' => false,
			'specific-script' => false,
			'block-style'    => array( RJE_R001CORP_KEY . '_cta' ),
			'path'           => RJE_R001CORP_PATH,
		);
		return $args;
	}
}
