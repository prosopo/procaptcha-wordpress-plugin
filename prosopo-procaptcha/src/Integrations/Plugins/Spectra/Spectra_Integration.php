<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Spectra;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Spectra\Spectra_Form_Integration;

final class Spectra_Integration extends Plugin_Integration_Base {
	public function get_about(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Spectra';
		$about->docs_url = self::get_docs_url( 'spectra' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'UAGB_Block' );
	}

	public function get_hookable_integrations(): array {
		return array(
			new Spectra_Form_Integration( $this->widget ),
		);
	}
}
