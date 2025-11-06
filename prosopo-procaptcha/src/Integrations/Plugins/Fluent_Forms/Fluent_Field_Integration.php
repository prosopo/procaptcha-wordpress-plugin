<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Fluent_Field_Integration implements Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'fluentform/loaded',
			function () {
				new Fluent_Field();
			}
		);
	}
}
