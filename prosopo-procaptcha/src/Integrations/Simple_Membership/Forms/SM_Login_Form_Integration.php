<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Simple_Membership\Forms;

defined( 'ABSPATH' ) || exit;


class SM_Login_Form_Integration extends SM_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'swpm_before_login_form_submit_button', array( $this, 'print_captcha_widget' ) );
		add_filter( 'swpm_validate_login_form_submission', array( $this, 'verify_submission' ) );
	}
}
