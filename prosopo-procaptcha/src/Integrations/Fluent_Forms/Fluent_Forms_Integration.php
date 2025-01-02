<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Definition\Hookable;
use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;

class Fluent_Forms_Integration extends Captcha_Plugin_Integration implements Hookable {
	public function set_hooks( bool $is_admin_area ): void {
		add_action(
			'fluentform/loaded',
			function () {
				new Fluent_Forms_Field();
			}
		);
	}

	public function get_form_integrations( Settings_Storage $settings_storage ): array {
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
