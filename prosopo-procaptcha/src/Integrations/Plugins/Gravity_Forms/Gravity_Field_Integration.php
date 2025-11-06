<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms;

defined( 'ABSPATH' ) || exit;

use GF_Fields;
use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Gravity_Field_Integration implements Hookable {

	public function set_hooks( Screen_Detector $screen_detector ): void {
		if ( class_exists( 'GF_Fields' ) &&
			is_callable( array( 'GF_Fields', 'register' ) ) ) {
			// While we create the object ourselves, don't pass objects directly, as GravityForms will save its class,
			// and then create instances itself on the fly.
			GF_Fields::register( new Gravity_Field() );
		}
	}
}
