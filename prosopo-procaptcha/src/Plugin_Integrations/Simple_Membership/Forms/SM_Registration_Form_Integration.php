<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Simple_Membership\Forms;

defined( 'ABSPATH' ) || exit;

class SM_Registration_Form_Integration extends SM_Form_Integration_Base {
	// This form doesn't support our JS submit interception.
	protected bool $is_without_client_validation = true;

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'swpm_before_registration_submit_button', array( $this, 'print_captcha_widget' ) );
		add_filter( 'swpm_validate_registration_form_submission', array( $this, 'verify_submission' ) );
	}
}
