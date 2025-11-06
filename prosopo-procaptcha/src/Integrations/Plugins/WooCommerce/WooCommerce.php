<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Module\Configurable_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Blocks_Checkout;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Login;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Password_Recovery;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Order_Tracking;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\Woo_Registration;
use Io\Prosopo\Procaptcha\Settings\Account_Form_Settings;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms\{Woo_Classic_Checkout};
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

final class WooCommerce extends Plugin_Integration_Base implements Configurable_Module_Integration {
	private Account_Form_Settings $account_form_settings;
	private WooCommerce_Tab $settings;

	public function __construct(
		Widget $widget,
		Account_Form_Settings $account_form_settings
	) {
		parent::__construct( $widget );

		$this->account_form_settings = $account_form_settings;
		$this->settings              = new WooCommerce_Tab();
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
		return $this->settings;
	}

	protected function get_hookable_integrations(): array {
		$woo_settings             = $this->settings->get_settings();
		$is_on_woo_checkout       = bool( $woo_settings, WooCommerce_Tab::IS_ON_CHECKOUT );
		$is_on_woo_order_tracking = bool( $woo_settings, WooCommerce_Tab::IS_ON_ORDER_TRACKING );
		$integrations             = array();

		if ( $is_on_woo_checkout ) {
			$integrations[] = new Woo_Blocks_Checkout( $this->widget );
			$integrations[] = new Woo_Classic_Checkout( $this->widget );
		}

		if ( $is_on_woo_order_tracking ) {
			$integrations[] = new Woo_Order_Tracking( $this->widget );
		}

		if ( $this->account_form_settings->is_login_protected() ) {
			$integrations[] = new Woo_Login( $this->widget );
		}

		if ( $this->account_form_settings->is_registration_protected() ) {
			$integrations[] = new Woo_Registration( $this->widget );
		}

		if ( $this->account_form_settings->is_password_recovery_protected() ) {
			$integrations[] = new Woo_Password_Recovery( $this->widget );
		}

		return $integrations;
	}
}
