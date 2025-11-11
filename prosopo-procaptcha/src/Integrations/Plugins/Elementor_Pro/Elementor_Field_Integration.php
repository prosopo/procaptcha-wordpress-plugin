<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar;
use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Elementor_Field_Integration implements Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'elementor_pro/forms/fields/register',
			function ( Form_Fields_Registrar $registrar ) {
				$registrar->register( new Elementor_Field() );
			}
		);
	}
}
