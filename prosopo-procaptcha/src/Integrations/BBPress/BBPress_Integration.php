<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;

class BBPress_Integration extends Procaptcha_Plugin_Integration {
	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array(
			BBPress_Forum_Integration::class,
		);
	}

	public function get_target_plugin_classes(): array {
		return array(
			'bbPress',
		);
	}
}
