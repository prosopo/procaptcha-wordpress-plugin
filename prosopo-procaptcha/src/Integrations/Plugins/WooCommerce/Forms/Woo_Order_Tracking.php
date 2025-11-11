<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Query_Arguments;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

class Woo_Order_Tracking extends Widget_Integration_Base {
	private bool $is_invalid = false;

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'woocommerce_order_tracking_form', array( $this, 'print_field' ) );
		add_filter( 'pre_do_shortcode_tag', array( $this, 'maybe_verify_submission' ), 10, 2 );
		add_filter( 'do_shortcode_tag', array( $this, 'maybe_add_error' ), 10, 2 );
	}

	public function print_field(): void {
		$widget = $this->widget;

		if ( ! $widget->is_protection_enabled() ) {
			return;
		}

		$this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:0 0 10px',
				),
			)
		);
	}

	/**
	 * @param string|false $output
	 * @param string $tag
	 *
	 * @return string|false
	 */
	public function maybe_verify_submission( $output, string $tag ) {
		if ( 'woocommerce_order_tracking' !== $tag ) {
			return $output;
		}

		$order_id = Query_Arguments::get_non_action_string( 'orderid', Query_Arguments::POST );
		$widget   = $this->widget;

		if ( '' === $order_id ||
		! $widget->is_protection_enabled() ||
		$widget->is_verification_token_valid() ) {
			return $output;
		}

		$this->is_invalid = true;

		// Unset, to prevent form processing by Woo.
		unset( $_REQUEST['orderid'], $_POST['orderid'] );

		return $output;
	}

	public function maybe_add_error( string $output, string $tag ): string {
		if ( 'woocommerce_order_tracking' !== $tag ||
			! $this->is_invalid ) {
			return $output;
		}

		$prefix = '';

		if ( function_exists( 'wc_print_notice' ) ) {
			$validation_error_message = $this->widget->get_validation_error_message();
			$prefix                   = wc_print_notice( $validation_error_message, 'error', array(), true );
		}

		// todo log if function is missing (for some reason).

		return $prefix . $output;
	}
}
