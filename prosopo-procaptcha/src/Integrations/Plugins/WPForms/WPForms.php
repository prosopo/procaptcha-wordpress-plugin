<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WPForms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

final class WPForms extends Plugin_Integration_Base {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'WP Forms';
		$about->docs_url = self::get_docs_url( 'wpforms' );

		return $about;
	}

	public function is_active(): bool {
		/**
		 * Instead of the main plugin class, we use the main field class.
		 *
		 * The reason is that the primary plugin class, 'WPForms\WPForms', itself is always loaded for all requests,
		 * but since recently contains the 'is_restricted_heartbeat()' method,
		 * which skips loading of the rest classes for most of the WordPress heartbeat requests.
		 *
		 * In this way, usage of the primary class would always create the Integration instances,
		 * which would try to extend the not-existing 'WPForms_Field' class.
		 */
		return class_exists( 'WPForms_Field' );
	}

	protected function get_hookable_integrations(): array {
		return array(
			new WPForms_Field_Integration(),
		);
	}

	protected function get_external_integrations(): array {
		return array(
			WPForms_Field::class,
		);
	}
}
