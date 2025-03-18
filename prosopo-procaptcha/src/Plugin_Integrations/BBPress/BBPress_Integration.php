<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;

class BBPress_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array(
			'bbPress',
		);
	}

	protected function get_form_integrations(): array {
		return array(
			BBPress_Forum_Integration::class,
		);
	}
}
