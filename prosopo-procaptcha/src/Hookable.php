<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

interface Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void;
}
