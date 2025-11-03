<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Screen_Detector;

defined( 'ABSPATH' ) || exit;

interface Screen_Detector {
	public function is_admin_area(): bool;
}
