<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Formidable_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

final class Formidable_Forms extends Plugin_Integration_Base {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Formidable Forms';
		$about->docs_url = self::get_docs_url( 'formidable' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'FrmAppHelper' );
	}

	protected function get_external_integrations(): array {
		return array(
			Formidable_Field::class,
		);
	}

	protected function get_hookable_integrations(): array {
		return array(
			new Formidable_Field_Integration( $this->widget ),
		);
	}
}
