<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Simple_Membership\Forms;

defined( 'ABSPATH' ) || exit;

class SM_Reset_Password_Form_Integration extends SM_Form_Integration_Base {
	// In this form, click on the submit button causes 'sending' label appearance, even if the submission is prevented.
	// Displaying it along with the client validation may confuse users.
	protected bool $is_without_client_validation = true;

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'swpm_before_pass_reset_form_submit_button', array( $this, 'print_captcha_widget' ) );
		add_filter( 'swpm_validate_pass_reset_form_submission', array( $this, 'verify_submission' ) );
	}
}
