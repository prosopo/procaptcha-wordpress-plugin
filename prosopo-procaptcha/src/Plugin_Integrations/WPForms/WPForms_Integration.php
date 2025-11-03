<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WPForms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;

class WPForms_Integration extends Plugin_Integration_Base implements Hookable {
	public function get_vendor_classes(): array {
		return array(
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
			'WPForms_Field',
		);
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		// translations are used in the constructor, which aren't available before this hook.
		add_action(
			'init',
			function () {
				new WPForms_Form_Integration();
			}
		);
	}

	protected function get_form_integrations(): array {
		return array(
			WPForms_Form_Integration::class,
		);
	}
}
