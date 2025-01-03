<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;

class BBPress_Integration extends Captcha_Plugin_Integration {
	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array(
			BBPress_Forum::class,
		);
	}

	public function get_target_plugin_classes(): array {
		return array(
			'bbPress',
		);
	}
}
