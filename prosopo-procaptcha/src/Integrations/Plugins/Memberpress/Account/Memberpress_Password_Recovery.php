<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Account;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Memberpress_Password_Recovery extends Widget_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'mepr-forgot-password-form',
			function () {
				$this->widget->print_form_field(
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
				$widget = $this->widget;

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
