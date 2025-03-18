<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

class Woo_Order_Tracking_Form_Integration extends Hookable_Form_Integration_Base {
	private bool $is_invalid = false;

	public function print_field(): void {
		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_protection_enabled() ) {
			return;
		}

		self::get_form_helper()->get_widget()->print_form_field(
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

		$order_id = self::get_form_helper()->get_query_arguments()
			->get_string_for_non_action( 'orderid', Query_Arguments::POST );
		$widget   = self::get_form_helper()->get_widget();

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
			$validation_error_message = self::get_form_helper()->get_widget()->get_validation_error_message();
			$prefix                   = wc_print_notice( $validation_error_message, 'error', array(), true );
		}

		// todo log if function is missing (for some reason).

		return $prefix . $output;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_order_tracking_form', array( $this, 'print_field' ) );
		add_filter( 'pre_do_shortcode_tag', array( $this, 'maybe_verify_submission' ), 10, 2 );
		add_filter( 'do_shortcode_tag', array( $this, 'maybe_add_error' ), 10, 2 );
	}
}
