<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Spectra;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integration_Base;

final class Spectra_Integration extends Plugin_Integration_Base {
	public function get_vendor_classes(): array {
		return array( 'UAGB_Block' );
	}

	public function get_form_integrations(): array {
		return array(
			Spectra_Form_Integration::class,
		);
	}
}
