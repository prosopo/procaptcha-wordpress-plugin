<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\JetPack;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integration_Base;

class JetPack_Integration extends Plugin_Integration_Base {
	public function get_vendor_classes(): array {
		return array(
			'Jetpack',
		);
	}

	protected function get_form_integrations(): array {
		return array(
			JetPack_Form_Integration::class,
		);
	}
}
