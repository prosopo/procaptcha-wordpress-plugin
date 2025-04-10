<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;
use Automattic\WooCommerce\Blocks\Package;
use Exception;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WC_Order;
use WP_Error;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Woo_Checkout_Form_Integration extends Hookable_Form_Integration_Base {

	private string $block_checkout_field_id;
	private string $block_checkout_field_location;

	public function construct(): void {
		parent::construct();

		$widget = self::get_form_helper()->get_widget();

		$this->block_checkout_field_id       = sprintf(
			'%s/%s',
			Procaptcha_Plugin::SLUG,
			$widget->get_field_name()
		);
		$this->block_checkout_field_location = 'order';
	}

	public function print_classic_field(): void {
		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_protection_enabled() ) {
			return;
		}

		$widget->print_form_field(
			array(
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);
	}

	/**
	 * @param array<string,mixed> $data
	 */
	public function verify( array $data, WP_Error $errors ): void {
		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_protection_enabled() ||
			$widget->is_verification_token_valid() ) {
			return;
		}

		$widget->get_validation_error( $errors );
	}


	/**
	 * @param array<string,mixed> $block
	 */
	public function print_blocks_checkout_field( string $content, array $block ): string {
		$suffix     = '';
		$block_name = $block['blockName'] ?? '';

		if ( 'woocommerce/checkout-additional-information-block' === $block_name ) {
			// always print, since it's handled via Rest API without the Auth cookie, so we can't get the user id.
			$suffix  = self::get_form_helper()->get_widget()->print_form_field(
				array(
					Widget_Settings::ELEMENT_ATTRIBUTES => array(
						'style' => 'margin:-30px 0 30px',
					),
					Widget_Settings::IS_RETURN_ONLY     => true,
					Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
				)
			);
			$suffix .= '<prosopo-procaptcha-woo-checkout-form></prosopo-procaptcha-woo-checkout-form>';

			$widget = self::get_form_helper()->get_widget();

			$widget->load_plugin_integration_script( 'woocommerce/woocommerce-blocks-checkout-integration.min.js' );
			$widget->add_integration_css( '.wc-block-components-address-form__prosopo-procaptcha-prosopo_procaptcha { display: none; }' );
		}

		return $content . $suffix;
	}

	public function register_block_checkout_field(): void {
		if ( function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
			// https://developer.woocommerce.com/docs/cart-and-checkout-additional-checkout-fields/.
			woocommerce_register_additional_checkout_field(
				array(
					'id'                         => $this->block_checkout_field_id,
					'label'                      => self::get_form_helper()->get_widget()->get_field_name(),
					'location'                   => $this->block_checkout_field_location,
					'required'                   => true,
					'show_in_order_confirmation' => false,
					'type'                       => 'text',
					// do not use the 'validate_callback' option
					// since Woo 9.8, it's called even for the initial, empty state
					// (as part of '__experimental_calc_totals' in the StoreApi/.../Checkout.php).
				)
			);
		}
	}

	/**
	 * Unlike 'validate_callback' option of an additional block checkout field,
	 * 'woocommerce_store_api_checkout_order_processed' hook is called only once.
	 *
	 * @throws Exception
	 */
	public function validate_block_checkout_order_submission( WC_Order $order ): void {
		// https://developer.woocommerce.com/docs/cart-and-checkout-additional-checkout-fields/#accessing-values.
		$checkout_fields   = Package::container()->get(
			CheckoutFields::class
		);
		$additional_fields = $checkout_fields->get_additional_fields();

		if ( ! key_exists( $this->block_checkout_field_id, $additional_fields ) ) {
			return;
		}

		$additional_field_values = $checkout_fields->get_all_fields_from_object(
			$order,
			$this->block_checkout_field_location
		);

		$token = string( $additional_field_values, $this->block_checkout_field_id );

		$widget = self::get_form_helper()->get_widget();

		// Without checking ->is_present() because this Woo Rest API doesn't pass the Auth cookie.
		if ( $widget->is_verification_token_valid( $token ) ) {
			return;
		}

		$error_message = $widget->get_validation_error_message();

		throw new Exception( esc_html( $error_message ) );
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'print_classic_field' ) );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'verify' ), 10, 2 );

		// It's necessary to use priority, otherwise for some reason Woo prints this block multiple times.
		add_filter( 'render_block', array( $this, 'print_blocks_checkout_field' ), 999, 2 );

		add_action( 'woocommerce_init', array( $this, 'register_block_checkout_field' ) );
		add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'validate_block_checkout_order_submission' ), );
	}
}
