<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Integration;

interface Plugin_Integration extends Integration {
	public function is_active(): bool;
}
