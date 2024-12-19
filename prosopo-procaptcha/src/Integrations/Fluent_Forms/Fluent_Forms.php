<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;

class Fluent_Forms extends Plugin_Integration implements Hooks_Interface {
	public function set_hooks( bool $is_admin_area ): void {
		add_action(
			'fluentform/loaded',
			function () {
				new Fluent_Forms_Field();
			}
		);
	}

	public function get_form_integrations( Settings_Storage_Interface $settings_storage ): array {
		return array(
			Fluent_Forms_Field::class,
		);
	}

	/**
	 * @return string[]
	 */
	public function get_target_plugin_classes(): array {
		return array(
			'\FluentForm\App\Services\FormBuilder\BaseFieldManager',
		);
	}
}
