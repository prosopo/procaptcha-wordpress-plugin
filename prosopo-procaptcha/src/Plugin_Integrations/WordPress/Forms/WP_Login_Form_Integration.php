<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WordPress\Forms;

use WP_Error;
use WP_User;

defined( 'ABSPATH' ) || exit;

class WP_Login_Form_Integration extends WP_Form_Integration_Base {
	/**
	 * @param WP_User|WP_Error|null $user_or_error
	 *
	 * @return WP_User|WP_Error|null
	 */
	public function verify_submission( $user_or_error ) {
		$widget = self::get_widget();

		if ( ! $widget->is_verification_token_valid() ) {
			$error_instance = $user_or_error instanceof WP_Error ?
				$user_or_error :
				null;

			$user_or_error = $widget->get_validation_error( $error_instance );
		}

		return $user_or_error;
	}

	/**
	 * @param mixed $type
	 */
	public function maybe_print_field( $type = null ): void {
		if ( 'resetpass' === $type ) {
			return;
		}

		$this->print_form_field();
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $is_admin_area );

		// Called if auth happens via wp_authenticate_username_password(), wp_authenticate() and other functions.
		add_filter( 'wp_authenticate_user', array( $this, 'verify_submission' ), );

		// Ignore bbPress reset password form.
		add_action( 'login_form', array( $this, 'maybe_print_field' ) );
	}
}
