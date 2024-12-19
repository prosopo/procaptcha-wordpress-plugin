<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration;
use Io\Prosopo\Procaptcha\Query_Arguments;

class Woo_Order_Tracking_Form extends Hookable_Form_Integration {
	private bool $is_invalid = false;

	public function print_field(): void {
		$captcha = self::get_form_helper()->get_captcha();

		if ( false === $captcha->is_present() ) {
			return;
		}

		self::get_form_helper()->get_captcha()->print_form_field(
			array(
				Widget_Arguments::ELEMENT_ATTRIBUTES => array(
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
		$captcha  = self::get_form_helper()->get_captcha();

		if ( '' === $order_id ||
		false === $captcha->is_present() ||
		true === $captcha->is_human_made_request() ) {
			return $output;
		}

		$this->is_invalid = true;

		// Unset, to prevent form processing by Woo.
		unset( $_REQUEST['orderid'], $_POST['orderid'] );

		return $output;
	}

	public function maybe_add_error( string $output, string $tag ): string {
		if ( 'woocommerce_order_tracking' !== $tag ||
			false === $this->is_invalid ) {
			return $output;
		}

		$prefix = '';

		if ( true === function_exists( 'wc_print_notice' ) ) {
			$validation_error_message = self::get_form_helper()->get_captcha()->get_validation_error_message();
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
