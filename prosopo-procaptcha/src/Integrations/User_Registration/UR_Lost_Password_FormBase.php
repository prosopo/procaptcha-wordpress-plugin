<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\User_Registration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration_Base;

class UR_Lost_Password_FormBase extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		self::get_form_helpers()->get_captcha()->print_form_field();
	}

	// Only print, without validation, as the UR plugin uses the 'lostpassword_post' hook, so WordPress/Lost_Password_Form.php will handle it.

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'user_registration_lostpassword_form', array( $this, 'print_field' ) );
	}
}
