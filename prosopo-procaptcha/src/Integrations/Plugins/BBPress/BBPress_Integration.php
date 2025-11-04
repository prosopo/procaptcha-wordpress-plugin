<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

class BBPress_Integration extends Plugin_Integration_Base {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'BBPress';
		$about->docs_url = self::get_docs_url( 'bbpress' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'bbPress' );
	}

	protected function get_hookable_integrations(): array {
		return array(
			new BBPress_Forum_Integration( $this->widget ),
		);
	}
}
