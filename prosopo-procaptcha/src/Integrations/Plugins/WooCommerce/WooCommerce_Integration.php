<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Module\Configurable_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Blocks_Checkout_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Login_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Lost_Password_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Order_Tracking_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Register_Form_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\WordPress_Integration_Settings;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\{Woo_Classic_Checkout_Form_Integration};
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class WooCommerce_Integration extends Plugin_Integration_Base implements Configurable_Module_Integration {
	private WordPress_Integration_Settings $account_forms_tab;
	private WooCommerce_Integration_Settings $woo_settings_tab;

	public function __construct(
		Widget $widget,
		WordPress_Integration_Settings $account_forms_tab,
		WooCommerce_Integration_Settings $woo_settings_tab
	) {
		parent::__construct( $widget );

		$this->account_forms_tab = $account_forms_tab;
		$this->woo_settings_tab  = $woo_settings_tab;
	}
	public function get_about(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'WooCommerce';
		$about->docs_url = self::get_docs_url( 'woocommerce' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'WooCommerce' );
	}

	public function get_settings_tab(): Settings_Tab {
		return new WooCommerce_Integration_Settings();
	}

	protected function get_hookable_integrations(): array {
		$account_forms            = $this->account_forms_tab->get_settings();
		$woo_settings             = $this->woo_settings_tab->get_settings();
		$is_on_woo_checkout       = bool( $woo_settings, WooCommerce_Integration_Settings::IS_ON_CHECKOUT );
		$is_on_woo_order_tracking = bool( $woo_settings, WooCommerce_Integration_Settings::IS_ON_ORDER_TRACKING );
		$is_on_wp_login           = bool( $account_forms, WordPress_Integration_Settings::IS_ON_WP_LOGIN_FORM );
		$is_on_wp_register        = bool( $account_forms, WordPress_Integration_Settings::IS_ON_WP_REGISTER_FORM );
		$is_on_wp_lost_pass       = bool( $account_forms, WordPress_Integration_Settings::IS_ON_WP_LOST_PASSWORD_FORM );

		$integrations = array();

		if ( $is_on_woo_checkout ) {
			$integrations[] = new Woo_Blocks_Checkout_Form_Integration( $this->widget );
			$integrations[] = new Woo_Classic_Checkout_Form_Integration( $this->widget );
		}

		if ( $is_on_woo_order_tracking ) {
			$integrations[] = new Woo_Order_Tracking_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_login ) {
			$integrations[] = new Woo_Login_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_register ) {
			$integrations[] = new Woo_Register_Form_Integration( $this->widget );
		}

		if ( $is_on_wp_lost_pass ) {
			$integrations[] = new Woo_Lost_Password_Form_Integration( $this->widget );
		}

		return $integrations;
	}
}
