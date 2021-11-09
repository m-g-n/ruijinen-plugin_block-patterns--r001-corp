<?php
/**
 * @package ruijinen-skin-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Setup;

class TextDomain{
	/**
	 * Constructor.
	 */
	public function __construct() {
		load_plugin_textdomain( RJE_R001CORP_TEXTDOMAIN, false, RJE_R001CORP_PATH . '/languages' );
		add_filter( 'load_textdomain_mofile', [ $this, 'load_textdomain_mofile' ], 10, 2 );
	}

	/**
	 * When local .mo file exists, load this.
	 *
	 * @param string $mofile Path to the MO file.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 * @return string
	 */
	public function load_textdomain_mofile( $mofile, $domain ) {
		if ( RJE_R001CORP_TEXTDOMAIN === $domain ) {
			$mofilename   = basename( $mofile );
			$local_mofile = RJE_R001CORP_PATH . '/languages/' . $mofilename;
			if ( file_exists( $local_mofile ) ) {
				return $local_mofile;
			}
		}
		return $mofile;
	}
}