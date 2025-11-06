<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\WordPress\Forms\WP_Form_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use WP_Error;
use WP_User;

final class WP_Password_Recovery extends WP_Form_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		add_action( 'lostpassword_post', array( $this, 'verify_submission' ), 10, 2 );
		// bbPress reset password form.
		add_action( 'login_form', array( $this, 'maybe_print_field' ) );
	}

	/**
	 * @param WP_Error $errors
	 * @param WP_User|false $user_data
	 */
	public function verify_submission( WP_Error $errors, $user_data ): void {
		$widget = $this->widget;

		if ( $widget->is_verification_token_valid() ) {
			return;
		}

		$widget->get_validation_error( $errors );
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

	protected function get_print_field_action(): string {
		return 'lostpassword_form';
	}
}
