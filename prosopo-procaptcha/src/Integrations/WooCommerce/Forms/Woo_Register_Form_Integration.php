<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;

class Woo_Register_Form_Integration extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		self::get_form_helper()->get_widget()->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	public function verify_submission( WP_Error $error ): WP_Error {
		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_human_made_request() ) {
			$widget->add_validation_error( $error );
		}

		return $error;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_register_form', array( $this, 'print_field' ) );

		add_filter( 'woocommerce_process_registration_errors', array( $this, 'verify_submission' ) );
	}
}
