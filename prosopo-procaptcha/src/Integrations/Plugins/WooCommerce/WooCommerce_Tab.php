<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab_Base;

final class WooCommerce_Tab extends Settings_Tab_Base {
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
