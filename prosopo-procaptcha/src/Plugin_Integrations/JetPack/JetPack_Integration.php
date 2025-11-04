<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\JetPack;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\About_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

final class JetPack_Integration extends Plugin_Integration_Base {
	public function get_about(): About_Integration {
		$about = new About_Integration();

		$about->name     = 'JetPack';
		$about->docs_url = self::get_docs_url( 'jetpack' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'Jetpack' );
	}

	protected function get_hookable_integrations(): array {
		return array(
			new JetPack_Form_Integration( $this->widget ),
		);
	}
}
