<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Simple_Membership\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

class SM_Login_Form_Integration extends Hookable_Form_Integration_Base {

	public function print_captcha_widget( string $before_submit_area ): string {
		$widget = self::get_form_helper()->get_widget();

		if ( $widget->is_present() ) {
			$before_submit_area .= $widget->print_form_field(
				array(
					Widget_Settings::ELEMENT_ATTRIBUTES => array(
						'style' => 'margin:15px 0 0;',
					),
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
		$widget = self::get_form_helper()->get_widget();

		$should_abort_request = $widget->is_present() &&
			! $widget->is_human_made_request();

		$response = $should_abort_request ?
			$widget->get_validation_error_message() :
			$response;

		return $response;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'swpm_before_login_form_submit_button', array( $this, 'print_captcha_widget' ) );
		add_filter( 'swpm_validate_login_form_submission', array( $this, 'verify_submission' ) );
	}
}
