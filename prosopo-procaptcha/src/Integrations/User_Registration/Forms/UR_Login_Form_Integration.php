<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\User_Registration\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Hookable\Hookable_Form_Integration_Base;

class UR_Login_Form_Integration extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		self::get_form_helper()->get_widget()->print_form_field();
	}

	// Only print, without validation, as the UR plugin uses the auth hook, so WordPress/Login_Form.php will handle it.

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'user_registration_login_form_before_submit_button', array( $this, 'print_field' ) );
	}
}
