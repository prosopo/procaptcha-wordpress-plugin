<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\User_Registration\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;

class UR_Lost_Password_Form_Integration extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		self::get_widget()->print_form_field();
	}

	// Only print, without validation, as the UR plugin uses the 'lostpassword_post' hook, so WordPress/Lost_Password_Form.php will handle it.

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'user_registration_lostpassword_form', array( $this, 'print_field' ) );
	}
}
