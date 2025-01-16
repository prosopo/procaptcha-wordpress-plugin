<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Tabs;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\Tab\Procaptcha_Settings_Tab;

class Woo_Commerce_Procaptcha_Settings extends Procaptcha_Settings_Tab {
	const IS_ON_CHECKOUT       = 'is_on_checkout';
	const IS_ON_ORDER_TRACKING = 'is_on_order_tracking';

	public function get_tab_title(): string {
		return __( 'WooCommerce', 'prosopo-procaptcha' );
	}

	public function get_tab_name(): string {
		return 'woocommerce';
	}

	protected function get_option_name(): string {
		return self::OPTION_PREFIX . 'woocommerce';
	}

	protected function get_bool_settings(): array {
		return array(
			self::IS_ON_CHECKOUT       => __( 'Protect the checkout form', 'prosopo-procaptcha' ),
			self::IS_ON_ORDER_TRACKING => __( 'Protect the order tracking form', 'prosopo-procaptcha' ),
		);
	}
}
