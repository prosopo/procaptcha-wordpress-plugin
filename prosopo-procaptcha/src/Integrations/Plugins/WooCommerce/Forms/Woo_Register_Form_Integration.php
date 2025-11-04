<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;

final class Woo_Register_Form_Integration extends Widget_Integration {
	public function print_field(): void {
		$this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	public function verify_submission( WP_Error $error ): WP_Error {
		$widget = $this->widget;

		if ( ! $widget->is_verification_token_valid() ) {
			$widget->get_validation_error( $error );
		}

		return $error;
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'woocommerce_register_form', array( $this, 'print_field' ) );

		add_filter( 'woocommerce_process_registration_errors', array( $this, 'verify_submission' ) );
	}
}
