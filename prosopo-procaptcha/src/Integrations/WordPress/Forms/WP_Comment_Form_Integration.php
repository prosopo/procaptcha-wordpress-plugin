<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\WordPress\WP_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;

final class WP_Comment_Form_Integration extends WP_Form_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		add_filter( 'comment_form_submit_field', array( $this, 'include_captcha_field' ), 10, 2 );
		add_filter( 'pre_comment_approved', array( $this, 'verify_submission' ), 10, 2 );
	}

	/**
	 * @param string $submit_field
	 * @param array<string,mixed> $args
	 *
	 * @return string
	 */
	public function include_captcha_field( string $submit_field, array $args ): string {
		$widget = $this->widget;

		return $widget->print_form_field(
			array(
				Widget_Settings::IS_DESIRED_ON_GUESTS => true,
				Widget_Settings::IS_RETURN_ONLY       => true,
			)
		) . $submit_field;
	}

	/**
	 * @param int|string|WP_Error $approved
	 * @param array<string,mixed> $comment_data
	 *
	 * @return int|string|WP_Error
	 */
	public function verify_submission( $approved, array $comment_data ) {
		$widget = $this->widget;

		if ( $widget->is_protection_enabled() &&
			! $widget->is_verification_token_valid() ) {
			$error = $approved instanceof WP_Error ?
				$approved :
				null;

			$approved = $widget->get_validation_error( $error );
		}

		return $approved;
	}
}
