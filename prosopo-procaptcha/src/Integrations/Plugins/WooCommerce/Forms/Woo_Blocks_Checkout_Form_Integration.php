<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;
use Automattic\WooCommerce\Blocks\Package;
use Exception;
use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WC_Order;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

final class Woo_Blocks_Checkout_Form_Integration extends Widget_Integration {
	private string $field_id;
	private string $field_location;

	public function __construct( Widget $widget ) {
		parent::__construct( $widget );

		$this->field_id       = sprintf(
			'%s/%s',
			Procaptcha_Plugin::PLUGIN_SLUG,
			$widget->get_field_name()
		);
		$this->field_location = 'order';
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'woocommerce_init', array( $this, 'register_field' ) );

		add_filter(
			'render_block_woocommerce/checkout-additional-information-block',
			fn( string $content )=>$content . $this->render_field(),
			// It's necessary to use priority, otherwise for some reason Woo prints this block multiple times.
			999
		);

		add_action(
		// Unlike 'validate_callback' option of an additional block checkout field, this hook is called only once.
			'woocommerce_store_api_checkout_order_processed',
			function ( WC_Order $order ) {
				if ( $this->is_field_in_submission() ) {
					$this->verify_order_submission( $order );
				}
			}
		);
	}

	public function register_field(): void {
		if ( function_exists( 'woocommerce_register_additional_checkout_field' ) ) {
			// https://developer.woocommerce.com/docs/cart-and-checkout-additional-checkout-fields/.
			woocommerce_register_additional_checkout_field(
				array(
					'id'                         => $this->field_id,
					'label'                      => $this->widget->get_field_name(),
					'location'                   => $this->field_location,
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
	 * @throws Exception
	 */
	public function verify_order_submission( WC_Order $order ): void {
		if ( $this->is_verified_order_submission( $order ) ) {
			return;
		}

		$widget                   = $this->widget;
		$validation_error_message = $widget->get_validation_error_message();

		throw new Exception( esc_html( $validation_error_message ) );
	}

	protected function render_field(): string {
		$this->load_integration_assets();

		return $this->get_field_markup();
	}

	protected function get_field_markup(): string {
		// always print, since it's handled via Rest API without the Auth cookie, so we can't get the user id.
		$form_field = $this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:-30px 0 30px',
				),
				Widget_Settings::IS_RETURN_ONLY     => true,
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);

		$form_field .= '<prosopo-procaptcha-woo-checkout-form></prosopo-procaptcha-woo-checkout-form>';

		return $form_field;
	}

	protected function load_integration_assets(): void {
		$widget = $this->widget;

		$widget->load_plugin_integration_script( 'woocommerce/woocommerce-blocks-checkout-integration.min.js' );

		$widget->add_integration_css( '.wc-block-components-address-form__prosopo-procaptcha-prosopo_procaptcha { display: none; }' );
	}

	protected function is_field_in_submission(): bool {
		// https://developer.woocommerce.com/docs/cart-and-checkout-additional-checkout-fields/#accessing-values.
		$checkout_fields = Package::container()->get(
			CheckoutFields::class
		);

		$additional_fields = $checkout_fields->get_additional_fields();

		return key_exists( $this->field_id, $additional_fields );
	}

	protected function get_order_submission_token( WC_Order $order ): string {
		// https://developer.woocommerce.com/docs/cart-and-checkout-additional-checkout-fields/#accessing-values.
		$checkout_fields = Package::container()->get(
			CheckoutFields::class
		);

		$additional_field_values = $checkout_fields->get_all_fields_from_object(
			$order,
			$this->field_location
		);

		return string( $additional_field_values, $this->field_id );
	}

	protected function is_verified_order_submission( WC_Order $order ): bool {
		$token = $this->get_order_submission_token( $order );

		$widget = $this->widget;

		// Without checking ->is_present() because this Woo Rest API doesn't pass the Auth cookie.
		return $widget->is_verification_token_valid( $token );
	}
}
