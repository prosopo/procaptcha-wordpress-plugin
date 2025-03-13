<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Integrations\WooCommerce\Forms\{Woo_Checkout_Form_Integration,
	Woo_Login_Form_Integration,
	Woo_Lost_Password_Form_Integration,
	Woo_Order_Tracking_Form_Integration,
	Woo_Register_Form_Integration};
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use Io\Prosopo\Procaptcha\Settings\Account_Forms_Settings_Tab;
use Io\Prosopo\Procaptcha\Settings\WooCommerce_Settings_Tab;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class WooCommerce_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array( 'WooCommerce' );
	}

	public function get_setting_tab_classes(): array {
		return array(
			WooCommerce_Settings_Tab::class,
		);
	}

	protected function get_conditional_form_integrations( Settings_Storage $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Settings_Tab::class )->get_settings();
		$woo_settings  = $settings_storage->get( WooCommerce_Settings_Tab::class )->get_settings();

		return array(
			Woo_Checkout_Form_Integration::class       => bool( $woo_settings, WooCommerce_Settings_Tab::IS_ON_CHECKOUT ),
			Woo_Login_Form_Integration::class          => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_LOGIN_FORM ),
			Woo_Lost_Password_Form_Integration::class  => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_LOST_PASSWORD_FORM ),
			Woo_Order_Tracking_Form_Integration::class => bool( $woo_settings, WooCommerce_Settings_Tab::IS_ON_ORDER_TRACKING ),
			Woo_Register_Form_Integration::class       => bool( $account_forms, Account_Forms_Settings_Tab::IS_ON_WP_REGISTER_FORM ),
		);
	}
}
