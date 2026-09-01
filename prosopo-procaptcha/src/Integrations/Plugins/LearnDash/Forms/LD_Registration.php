<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\LearnDash\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Query_Arguments;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;

final class LD_Registration extends Widget_Integration_Base {
	private bool $is_submission_rejected = false;

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'learndash_registration_form', array( $this, 'print_form_field' ) );

		add_filter( 'registration_errors', array( $this, 'verify_submission' ) );

		// LearnDash prints its own error list, so the WP_Error message alone may stay unnoticed.
		add_filter( 'learndash_registration_errors', array( $this, 'add_validation_error_message' ) );
	}

	public function print_form_field(): void {
		$this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	public function verify_submission( WP_Error $errors ): WP_Error {
		$widget = $this->widget;

		if ( ! $this->is_learndash_submission() ||
			$widget->is_verification_token_valid() ) {
			return $errors;
		}

		$this->is_submission_rejected = true;

		return $widget->get_validation_error( $errors );
	}

	/**
	 * @param mixed $registration_errors
	 *
	 * @return array<int,string>
	 */
	public function add_validation_error_message( $registration_errors ): array {
		$registration_errors = is_array( $registration_errors ) ?
			array_map(
				/**
				 * @param mixed $registration_error
				 */
				fn( $registration_error ): string => is_scalar( $registration_error ) ?
					(string) $registration_error :
					'',
				array_values( $registration_errors )
			) :
			array();

		$error_message = $this->widget->get_validation_error_message();

		if ( ! $this->is_submission_rejected ||
			in_array( $error_message, $registration_errors, true ) ) {
			return $registration_errors;
		}

		$registration_errors[] = $error_message;

		return $registration_errors;
	}

	protected function is_learndash_submission(): bool {
		// LearnDash checks its own nonce.
		return Query_Arguments::has_non_action_arg(
			'learndash-registration-form',
			Query_Arguments::POST
		);
	}
}
