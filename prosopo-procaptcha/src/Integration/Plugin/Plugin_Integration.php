<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Plugin\About_Plugin_Integration;

interface Plugin_Integration extends Hookable {
	public function get_about(): About_Plugin_Integration;

	public function is_active(): bool;
}
