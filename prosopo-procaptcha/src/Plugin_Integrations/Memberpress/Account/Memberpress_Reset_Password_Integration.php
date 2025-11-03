<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Account;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Memberpress_Reset_Password_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'mepr-forgot-password-form',
			function () {
				self::get_widget()->print_form_field(
					array(
						Widget_Settings::ELEMENT_ATTRIBUTES => array(
							'style' => 'margin:0 0 10px',
						),
					)
				);
			}
		);

		add_filter(
			'mepr-validate-forgot-password',
			/**
			 * @param string[] $errors
			 */
			function ( array $errors ): array {
				$widget = self::get_widget();

				if ( $widget->is_verification_token_valid() ) {
					return $errors;
				}

				return array_merge(
					$errors,
					array( $widget->get_validation_error_message() )
				);
			}
		);
	}
}
