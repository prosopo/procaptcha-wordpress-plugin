<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\About_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

final class Fluent_Forms_Integration extends Plugin_Integration_Base {
	public function get_about(): About_Integration {
		$about = new About_Integration();

		$about->name     = 'Fluent Forms';
		$about->docs_url = self::get_docs_url( 'fluent-forms' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( '\FluentForm\App\Services\FormBuilder\BaseFieldManager' );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'fluentform/loaded',
			function () {
				new Fluent_Forms_Form_Integration();
			}
		);
	}

	protected function get_external_integrations(): array {
		return array(
			Fluent_Forms_Form_Integration::class,
		);
	}
}
