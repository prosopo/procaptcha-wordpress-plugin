<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integration_Base;

class Fluent_Forms_Integration extends Plugin_Integration_Base implements Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void {
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
	public function get_vendor_classes(): array {
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
