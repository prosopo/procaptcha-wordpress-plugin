<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

class Woo_Lost_Password_Form_Integration extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		self::get_form_helper()->get_widget()->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
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
