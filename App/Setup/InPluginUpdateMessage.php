<?php
/**
 * @package ruijinen-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Setup;

class InPluginUpdateMessage {

	/**
	 * 更新通知用 JSON の URL.
	 */
	const NOTICE_URL = 'https://rui-jin-en.com/update-notice/' . RJE_R001CORP_KEY . '.json';

	//初期処理
	public function __construct() {
		add_action( 'in_plugin_update_message-' . RJE_R001CORP_BASENAME, array( $this, 'in_plugin_update_message' ), 10, 2 );
	}

	/**
	 * 更新画面のアラートボックスにメッセージを追加
	 *
	 * @param array  $data     プラグインのメタデータ.
	 * @param object $response 更新情報.
	 */
	public function in_plugin_update_message( $data, $response ) {
		if ( empty( $data['new_version'] ) ) {
			return;
		}

		$notice = $this->get_the_notice( $data['new_version'] );
		if ( empty( $notice['message'] ) ) {
			return;
		}

		echo '<br>' . wp_kses_post( $notice['message'] );
		if ( ! empty( $notice['url'] ) ) {
			printf(
				'<a href="%s" target="_blank" rel="noopener"> &#62;&#62;詳細を見る</a>',
				esc_url( $notice['url'] )
			);
		}
	}

	/**
	 * JSONからデータを取得して指定バージョンのメッセージ情報を返す
	 *
	 * JSON の形式: [ { "<version>": { "message": "...", "url": "..." } }, ... ]
	 *
	 * @param string $version バージョン.
	 * @return array|false メッセージ情報。取得できない場合は false.
	 */
	private function get_the_notice( $version ) {
		$transient_key = RJE_R001CORP_KEY . '_update_notice';
		$json          = get_transient( $transient_key );
		if ( false === $json ) {
			$response = wp_remote_get( self::NOTICE_URL, array( 'timeout' => 5 ) );
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				// 取得失敗時も短時間キャッシュし、プラグイン画面表示のたびに通信しないようにする.
				set_transient( $transient_key, '', 10 * MINUTE_IN_SECONDS );
				return false;
			}
			$json = wp_remote_retrieve_body( $response );
			set_transient( $transient_key, $json, 12 * HOUR_IN_SECONDS );
		}
		if ( '' === $json ) {
			return false;
		}

		$json = mb_convert_encoding( $json, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN' ); //文字コードをUTF-8に変換
		$arr  = json_decode( $json, true );
		if ( ! is_array( $arr ) ) {
			return false;
		}

		//階層が１つ深いため配列の階層を1つ浅くする
		$notices = array();
		foreach ( $arr as $item ) {
			if ( is_array( $item ) ) {
				$notices = array_merge( $notices, $item );
			}
		}

		return ( isset( $notices[ $version ] ) && is_array( $notices[ $version ] ) ) ? $notices[ $version ] : false;
	}
}
