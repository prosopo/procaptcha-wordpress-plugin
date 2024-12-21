<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;
use Io\Prosopo\Procaptcha\Settings\Tabs\Account_Forms_Settings;
use Io\Prosopo\Procaptcha\Settings\Tabs\Woo_Commerce_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class WooCommerce extends Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array( 'WooCommerce' );
	}

	public function get_form_integrations( Settings_Storage_Interface $settings_storage ): array {
		return $this->get_active_conditional_integrations( $settings_storage );
	}

	public function get_setting_tab_classes(): array {
		return array(
			Woo_Commerce_Settings::class,
		);
	}

	protected function get_conditional_integrations( Settings_Storage_Interface $settings_storage ): array {
		$account_forms = $settings_storage->get( Account_Forms_Settings::class )->get_settings();
		$woo_settings  = $settings_storage->get( Woo_Commerce_Settings::class )->get_settings();

		return array(
			Woo_Checkout_Form::class       => bool( $woo_settings, Woo_Commerce_Settings::IS_ON_CHECKOUT ),
			Woo_Login_Form::class          => bool( $account_forms, Account_Forms_Settings::IS_ON_WP_LOGIN_FORM ),
			Woo_Lost_Password_Form::class  => bool( $account_forms, Account_Forms_Settings::IS_ON_WP_LOST_PASSWORD_FORM ),
			Woo_Order_Tracking_Form::class => bool( $woo_settings, Woo_Commerce_Settings::IS_ON_ORDER_TRACKING ),
			Woo_Register_Form::class       => bool( $account_forms, Account_Forms_Settings::IS_ON_WP_REGISTER_FORM ),
		);
	}
}
