<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Gravity_Forms;

defined( 'ABSPATH' ) || exit;

use GF_Fields;
use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integration_Base;

class Gravity_Forms_Integration extends Plugin_Integration_Base implements Hookable {
	public function get_vendor_classes(): array {
		return array(
			'GF_Fields',
		);
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		if ( class_exists( 'GF_Fields' ) &&
		is_callable( array( 'GF_Fields', 'register' ) ) ) {
			// While we create the object ourselves, don't pass objects directly, as GravityForms will save its class,
			// and then create instances itself on the fly.
			GF_Fields::register( new Gravity_Forms_Form_Integration() );
		}
	}

	protected function get_form_integrations(): array {
		return array(
			Gravity_Forms_Form_Integration::class,
		);
	}
}
