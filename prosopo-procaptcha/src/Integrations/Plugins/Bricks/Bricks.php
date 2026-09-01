<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Bricks;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

final class Bricks extends Plugin_Integration_Base {
	const THEME_NAME = 'bricks';

	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Bricks';
		$about->docs_url = self::get_docs_url( 'bricks' );

		return $about;
	}

	public function is_active(): bool {
		// Bricks is a theme, so it's not loaded yet on the 'plugins_loaded' hook, hence the template check.
		return self::THEME_NAME === get_template();
	}

	protected function get_hookable_integrations(): array {
		return array(
			new Bricks_Form_Integration( $this->widget ),
		);
	}
}
