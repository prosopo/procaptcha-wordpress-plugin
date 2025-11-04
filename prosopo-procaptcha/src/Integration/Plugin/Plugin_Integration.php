<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\Module_Integration;

interface Plugin_Integration extends Module_Integration {
	public function is_active(): bool;
}
