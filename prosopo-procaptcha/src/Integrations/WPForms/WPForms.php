<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WPForms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;

class WPForms extends Plugin_Integration implements Hooks_Interface {
	public function get_target_plugin_classes(): array {
		return array(
			'WPForms\WPForms',
		);
	}

	public function get_form_integrations( Settings_Storage_Interface $settings_storage ): array {
		return array(
			WPForms_Field::class,
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		new WPForms_Field();
	}
}
