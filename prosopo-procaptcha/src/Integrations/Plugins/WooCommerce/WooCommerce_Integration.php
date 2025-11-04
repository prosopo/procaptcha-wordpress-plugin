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
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\{Woo_Classic_Checkout_Form_Integration};
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class WooCommerce_Integration extends Plugin_Integration_Base implements Configurable_Module_Integration {
	private Account_Form_Settings $account_form_settings;
	private WooCommerce_Integration_Settings $settings_tab;

	public function __construct(
		Widget $widget,
		Account_Form_Settings $account_form_settings
	) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
		$this->settings_tab          = new WooCommerce_Integration_Settings();
	}
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'WooCommerce';
		$about->docs_url = self::get_docs_url( 'woocommerce' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'WooCommerce' );
	}

	public function get_settings_tab(): Settings_Tab {
		return $this->settings_tab;
	}

	protected function get_hookable_integrations(): array {
		$woo_settings             = $this->settings_tab->get_settings();
		$is_on_woo_checkout       = bool( $woo_settings, WooCommerce_Integration_Settings::IS_ON_CHECKOUT );
		$is_on_woo_order_tracking = bool( $woo_settings, WooCommerce_Integration_Settings::IS_ON_ORDER_TRACKING );
		$integrations             = array();

		if ( $is_on_woo_checkout ) {
			$integrations[] = new Woo_Blocks_Checkout_Form_Integration( $this->widget );
			$integrations[] = new Woo_Classic_Checkout_Form_Integration( $this->widget );
		}

		if ( $is_on_woo_order_tracking ) {
			$integrations[] = new Woo_Order_Tracking_Form_Integration( $this->widget );
		}

		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new Woo_Login_Form_Integration( $this->widget );
		}

		if ( $this->account_form_settings->is_registration_protected() ) {
			$integrations[] = new Woo_Register_Form_Integration( $this->widget );
		}

		if ( $this->account_form_settings->is_password_recovery_protected() ) {
			$integrations[] = new Woo_Lost_Password_Form_Integration( $this->widget );
		}

		return $integrations;
	}
}
