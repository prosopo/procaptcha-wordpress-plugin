<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Error;

final class Woo_Classic_Checkout_Form_Integration extends Widget_Integration {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'print_field' ) );
		add_action(
			'woocommerce_after_checkout_validation',
			fn( array $data, WP_Error $errors )=>$this->verify_submission( $errors ),
			10,
			2
		);
	}

	public function print_field(): void {
		$widget = $this->widget;

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
		$widget = $this->widget;

		if ( ! $widget->is_protection_enabled() ||
			$widget->is_verification_token_valid() ) {
			return;
		}

		$widget->get_validation_error( $errors );
	}
}
