<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use WP_Error;
use WP_User;

defined( 'ABSPATH' ) || exit;

class Login_Form extends WordPress_Form_Base {
	/**
	 * @param WP_User|WP_Error|null $user_or_error
	 *
	 * @return WP_User|WP_Error|null
	 */
	public function verify_submission( $user_or_error ) {
		$captcha = self::get_form_helpers()->get_captcha();

		if ( ! $captcha->human_made_request() ) {
			$error_instance = $user_or_error instanceof WP_Error ?
				$user_or_error :
				null;

			$user_or_error = $captcha->add_validation_error( $error_instance );
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

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		// Called if auth happens via wp_authenticate_username_password(), wp_authenticate() and other functions.
		add_filter( 'wp_authenticate_user', array( $this, 'verify_submission' ), );

		// Ignore bbPress reset password form.
		add_action( 'login_form', array( $this, 'maybe_print_field' ) );
	}
}
