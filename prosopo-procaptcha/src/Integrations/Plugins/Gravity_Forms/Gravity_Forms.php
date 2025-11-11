<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

final class Gravity_Forms extends Plugin_Integration_Base {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Gravity Forms';
		$about->docs_url = self::get_docs_url( 'gravity-forms' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'GF_Fields' );
	}

	protected function get_external_integrations(): array {
		return array(
			Gravity_Field::class,
		);
	}

	protected function get_hookable_integrations(): array {
		return array(
			new Gravity_Field_Integration(),
		);
	}
}
