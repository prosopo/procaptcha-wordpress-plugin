<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;

class Fluent_Forms_Integration extends Procaptcha_Plugin_Integration implements Hookable {
	public function set_hooks( bool $is_admin_area ): void {
		add_action(
			'fluentform/loaded',
			function () {
				new Fluent_Forms_Form_Integration();
			}
		);
	}

	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array(
			Fluent_Forms_Form_Integration::class,
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
