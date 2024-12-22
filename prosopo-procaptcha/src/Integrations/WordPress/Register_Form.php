<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

defined( 'ABSPATH' ) || exit;

use WP_Error;

class Register_Form extends WordPress_Form {
	public function verify_submission( WP_Error $errors ): WP_Error {
		$captcha = self::get_form_helper()->get_captcha();

		if ( ! $captcha->human_made_request() ) {
			$captcha->add_validation_error( $errors );
		}

		return $errors;
	}

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		add_filter( 'registration_errors', array( $this, 'verify_submission' ) );
	}

	protected function get_print_field_action(): string {
		return 'register_form';
	}
}
