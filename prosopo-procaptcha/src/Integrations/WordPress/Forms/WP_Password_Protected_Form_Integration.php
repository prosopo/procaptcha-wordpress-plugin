<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integrations\WordPress\WP_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Post;

final class WP_Password_Protected_Form_Integration extends WP_Form_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		add_filter( 'the_password_form', array( $this, 'add_form_field' ), 10, 2 );
		add_action( 'login_form_postpass', array( $this, 'verify_submission' ) );
	}

	public function add_form_field( string $output, WP_Post $post ): string {
		$form_field = $this->widget->print_form_field(
			array(
				Widget_Settings::IS_DESIRED_ON_GUESTS => true,
				Widget_Settings::IS_RETURN_ONLY       => true,
			)
		);

		return str_replace( '</form>', $form_field . '</form>', $output );
	}

	public function verify_submission(): void {
		$widget = $this->widget;

		if ( ! $widget->is_protection_enabled() ||
		$widget->is_verification_token_valid() ) {
			return;
		}

		wp_die(
			esc_html( $widget->get_validation_error_message() ),
			'Procaptcha',
			array(
				'back_link' => true,
				'response'  => 303,
			)
		);
	}
}
