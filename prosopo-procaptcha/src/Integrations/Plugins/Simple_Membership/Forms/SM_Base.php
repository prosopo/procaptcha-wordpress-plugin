<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

abstract class SM_Base extends Widget_Integration_Base {
	protected bool $is_without_client_validation = false;

	public function print_captcha_widget( string $before_submit_area ): string {
		$widget = $this->widget;

		if ( $widget->is_protection_enabled() ) {
			$before_submit_area .= $widget->print_form_field(
				array(
					Widget_Settings::ELEMENT_ATTRIBUTES => array(
						'style' => 'margin:15px 0 0;',
					),
					Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => $this->is_without_client_validation,
				)
			);
		}

		return $before_submit_area;
	}

	/**
	 * @param mixed $response
	 *
	 * @return mixed
	 */
	public function verify_submission( $response ) {
		$widget = $this->widget;

		$should_abort_request = $widget->is_protection_enabled() &&
			! $widget->is_verification_token_valid();

		$response = $should_abort_request ?
			$widget->get_validation_error_message() :
			$response;

		return $response;
	}
}
