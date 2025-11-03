<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;

interface Plugin_Integration extends Hookable {
	public function get_about(): About_Plugin_Integration;

	public function is_active(): bool;
}
