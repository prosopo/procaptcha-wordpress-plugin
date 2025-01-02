<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\User_Registration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration_Base;

class UR_Login_Form extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		self::get_form_helpers()->get_captcha()->print_form_field();
	}

	// Only print, without validation, as the UR plugin uses the auth hook, so WordPress/Login_Form.php will handle it.

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'user_registration_login_form_before_submit_button', array( $this, 'print_field' ) );
	}
}
