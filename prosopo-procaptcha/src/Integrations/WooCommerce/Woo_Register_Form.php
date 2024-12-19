<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration;
use WP_Error;

class Woo_Register_Form extends Hookable_Form_Integration {
	public function print_field(): void {
		self::get_form_helper()->get_captcha()->print_form_field(
			array(
				Widget_Arguments::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	public function verify_submission( WP_Error $error ): WP_Error {
		$captcha = self::get_form_helper()->get_captcha();

		if ( false === $captcha->is_human_made_request() ) {
			$captcha->add_validation_error( $error );
		}

		return $error;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_register_form', array( $this, 'print_field' ) );

		add_filter( 'woocommerce_process_registration_errors', array( $this, 'verify_submission' ) );
	}
}
