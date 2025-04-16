<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms\Beaver_Contact_Form_Integration;

defined( 'ABSPATH' ) || exit;

final class Beaver_Builder_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array( 'FLBuilder' );
	}

	protected function get_form_integrations(): array {
		return array(
			Beaver_Contact_Form_Integration::class,
		);
	}
}
