<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WPForms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Definition\Hookable;
use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;

class WPForms_Integration extends Captcha_Plugin_Integration implements Hookable {
	public function get_target_plugin_classes(): array {
		return array(
			'WPForms\WPForms',
		);
	}

	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array(
			WPForms_Field::class,
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		new WPForms_Field();
	}
}
