<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;

class Woo_Classic_Checkout_Form_Integration extends Hookable_Form_Integration_Base {
	public function print_field(): void {
		$widget = self::get_widget();

		if ( ! $widget->is_protection_enabled() ) {
			return;
		}

		$widget->print_form_field(
			array(
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);
	}

	public function verify_submission( WP_Error $errors ): void {
		$widget = self::get_widget();

		if ( ! $widget->is_protection_enabled() ||
			$widget->is_verification_token_valid() ) {
			return;
		}

		$widget->get_validation_error( $errors );
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'print_field' ) );
		add_action(
			'woocommerce_after_checkout_validation',
			fn( array $data, WP_Error $errors )=>$this->verify_submission( $errors ),
			10,
			2
		);
	}
}
