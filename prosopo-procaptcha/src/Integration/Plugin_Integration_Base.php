<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

defined( 'ABSPATH' ) || exit;

abstract class Plugin_Integration_Base extends Widget_Integration_Base implements Plugin_Integration {
	protected static function get_docs_url( string $slug ): string {
		return sprintf( '%s/%s', Procaptcha_Plugin::DOCS_URL_BASE, $slug );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		foreach ( $this->get_hookable_integrations() as $hookable_integration ) {
			$hookable_integration->set_hooks( $screen_detector );
		}
	}

	/**
	 * @return Hookable[]
	 */
	protected function get_hookable_integrations(): array {
		return array();
	}
}
