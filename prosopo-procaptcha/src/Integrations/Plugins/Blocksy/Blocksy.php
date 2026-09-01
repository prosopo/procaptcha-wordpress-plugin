<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Blocksy;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Blocksy\Forms\Blocksy_Newsletter;

/**
 * The Blocksy account modal forms are covered by the WordPress Core Forms integration,
 * as Blocksy renders them with the native account hooks and processes them with the native flow.
 */
final class Blocksy extends Plugin_Integration_Base {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Blocksy';
		$about->docs_url = self::get_docs_url( 'blocksy' );

		return $about;
	}

	public function is_active(): bool {
		return defined( 'BLOCKSY_PATH' );
	}

	protected function get_hookable_integrations(): array {
		return array(
			new Blocksy_Newsletter( $this->widget ),
		);
	}
}
