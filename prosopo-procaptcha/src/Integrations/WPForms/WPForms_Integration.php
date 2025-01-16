<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WPForms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;

class WPForms_Integration extends Procaptcha_Plugin_Integration implements Hookable {
	public function get_target_plugin_classes(): array {
		return array(
			'WPForms\WPForms',
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		new WPForms_Form_Integration();
	}

	protected function get_form_integrations(): array {
		return array(
			WPForms_Form_Integration::class,
		);
	}
}
