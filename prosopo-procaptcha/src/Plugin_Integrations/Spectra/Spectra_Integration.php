<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Spectra;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Procaptcha_Plugin_Integration;

final class Spectra_Integration extends Procaptcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array( 'UAGB_Block' );
	}

	public function get_form_integrations(): array {
		return array(
			Spectra_Form_Integration::class,
		);
	}
}
