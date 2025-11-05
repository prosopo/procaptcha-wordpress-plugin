<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms;

defined( 'ABSPATH' ) || exit;

use GF_Fields;
use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms\Gravity_Forms_Form_Integration;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Gravity_Forms_Integration extends Plugin_Integration_Base {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Gravity Forms';
		$about->docs_url = self::get_docs_url( 'gravity-forms' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'GF_Fields' );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		if ( class_exists( 'GF_Fields' ) &&
		is_callable( array( 'GF_Fields', 'register' ) ) ) {
			// While we create the object ourselves, don't pass objects directly, as GravityForms will save its class,
			// and then create instances itself on the fly.
			GF_Fields::register( new Gravity_Forms_Form_Integration() );
		}
	}

	protected function get_external_integrations(): array {
		return array(
			Gravity_Forms_Form_Integration::class,
		);
	}
}
