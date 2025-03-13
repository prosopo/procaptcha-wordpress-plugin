<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;

class Fluent_Forms_Integration extends Procaptcha_Plugin_Integration implements Hookable {
	public function set_hooks( bool $is_admin_area ): void {
		add_action(
			'fluentform/loaded',
			function () {
				new Fluent_Forms_Form_Integration();
			}
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

	protected function get_form_integrations(): array {
		return array(
			Fluent_Forms_Form_Integration::class,
		);
	}
}
