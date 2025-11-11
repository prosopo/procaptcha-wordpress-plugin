<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms\Ninja_Field;
use Io\Prosopo\Procaptcha\Utils\Hookable;

final class Ninja_Forms extends Plugin_Integration_Base implements Hookable {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Ninja Forms';
		$about->docs_url = self::get_docs_url( 'ninja-forms' );

		return $about;  }

	public function is_active(): bool {
		return class_exists( 'Ninja_Forms' );
	}

	protected function get_external_integrations(): array {
		return array(
			Ninja_Field::class,
		);
	}

	protected function get_hookable_integrations(): array {
		return array(
			new Ninja_Field_Integration( $this->widget ),
		);
	}
}
