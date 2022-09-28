<?php
/**
 * 1カラムの項目固有のスタイルを登録
 *
 * @package ruijinen-block-patterns
 * @author mgn
 * @license GPL-2.0+
 */

//ファイルパス（プラグインのルートから相対）
$basename = basename( __DIR__ );
$front_filename = 'dist/css/patterns/' . $basename . '/style-front.css';
$editor_filename = 'dist/css/patterns/' . $basename . '/style-editor.css';
$front_filetime = ( file_exists( RJE_R001CORP_PATH . $front_filename ) ) ? filemtime( RJE_R001CORP_PATH . $front_filename ) : NULL;
$editor_filetime = ( file_exists( RJE_R001CORP_PATH . $editor_filename ) ) ? filemtime( RJE_R001CORP_PATH . $editor_filename ) : NULL;

//フロント・エディター用のCSSファイルを登録
wp_register_style( $basename . '-editor', RJE_R001CORP_URL . $editor_filename, $this->sm_style_handles, $front_filetime );
