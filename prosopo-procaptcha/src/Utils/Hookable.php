<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Utils;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

interface Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void;
}
