<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

class Woo_Login_Form_Integration extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		self::get_widget()->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px;',
				),
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_login_form', array( $this, 'print_field' ) );

		// validation happens in the WordPress/Login_Form class.
	}
}
