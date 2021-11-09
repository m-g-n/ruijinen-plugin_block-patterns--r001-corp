<?php
/**
 * @package ruijinen-r001-corp
 * @author mgn
 * @license GPL-2.0+
 */

namespace Ruijinen\Pattern\R001CORP\App\Patterns;

class RegisterCategory {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action(
			'init',
			function() {
				register_block_pattern_category( RJE_R001CORP_KEY, array( 'label' => '[類人猿] コーポレート' ) );
			}
		);
	}
}