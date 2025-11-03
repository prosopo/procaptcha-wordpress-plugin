<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WordPress\Forms;

defined( 'ABSPATH' ) || exit;

use WP_Error;

class WP_Register_Form_Integration extends WP_Form_Integration_Base {
	public function verify_submission( WP_Error $errors ): WP_Error {
		$widget = self::get_widget();

		if ( ! $widget->is_verification_token_valid() ) {
			$widget->get_validation_error( $errors );
		}

		return $errors;
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $is_admin_area );

		add_filter( 'registration_errors', array( $this, 'verify_submission' ) );
	}

	protected function get_print_field_action(): string {
		return 'register_form';
	}
}
