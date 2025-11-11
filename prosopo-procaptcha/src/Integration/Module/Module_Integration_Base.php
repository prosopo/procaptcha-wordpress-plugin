<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Module;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration;
use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

abstract class Module_Integration_Base extends Widget_Integration_Base implements Module_Integration {
	protected static function get_docs_url( string $slug ): string {
		return sprintf( '%s/%s', Procaptcha_Plugin::DOCS_URL_BASE, $slug );
	}

	final public function set_hooks( Screen_Detector $screen_detector ): void {
		$this->load();

		foreach ( $this->get_external_integrations() as $external_integration ) {
			$external_integration::set_widget( $this->widget );
		}

		foreach ( $this->get_hookable_integrations() as $hookable_integration ) {
			$hookable_integration->set_hooks( $screen_detector );
		}
	}

	protected function load(): void {
		// e.g. for manual class files loading, like in User_Registration.
	}

	/**
	 * @return class-string<External_Widget_Integration>[]
	 */
	protected function get_external_integrations(): array {
		return array();
	}

	/**
	 * @return Hookable[]
	 */
	protected function get_hookable_integrations(): array {
		return array();
	}
}
