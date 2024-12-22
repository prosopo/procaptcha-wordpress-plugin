<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

use WP_Error;
use WP_User;

defined( 'ABSPATH' ) || exit;

class Lost_Password_Form extends WordPress_Form {
	/**
	 * @param WP_Error $errors
	 * @param WP_User|false $user_data
	 */
	public function verify_submission( WP_Error $errors, $user_data ): void {
		$captcha = self::get_form_helper()->get_captcha();

		if ( $captcha->human_made_request() ) {
			return;
		}

		$captcha->add_validation_error( $errors );
	}

	/**
	 * @param mixed $type
	 */
	public function maybe_print_field( $type = null ): void {
		if ( 'resetpass' !== $type ) {
			return;
		}

		$this->print_form_field();
	}

	public function set_hooks( bool $is_admin_area ): void {
		parent::set_hooks( $is_admin_area );

		add_action( 'lostpassword_post', array( $this, 'verify_submission' ), 10, 2 );
		// bbPress reset password form.
		add_action( 'login_form', array( $this, 'maybe_print_field' ) );
	}

	protected function get_print_field_action(): string {
		return 'lostpassword_form';
	}
}
