<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration;

class Woo_Lost_Password_Form extends Hookable_Form_Integration {
	public function print_field(): void {
		self::get_form_helper()->get_captcha()->print_form_field(
			array(
				Widget_Arguments::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_lostpassword_form', array( $this, 'print_field' ) );

		// validation happens in the WordPress/Lost_Password_Form class.
	}
}