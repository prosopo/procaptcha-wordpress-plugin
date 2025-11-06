<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WPForms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class WPForms_Field_Integration implements Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		// translations are used in the constructor, which aren't available before this hook.
		add_action(
			'init',
			function () {
				new WPForms_Field();
			}
		);
	}
}
