<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Utils\Screen_Detector;

use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

defined( 'ABSPATH' ) || exit;

final class Screen_Detector_Base implements Screen_Detector {
	public bool $is_admin_area;

	public static function load(): self {
		$detector = new Screen_Detector_Base();

		$detector->is_admin_area = is_admin();

		return $detector;
	}

	public function is_admin_area(): bool {
		return $this->is_admin_area;
	}
}
