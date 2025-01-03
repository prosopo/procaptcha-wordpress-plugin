<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces;

defined( 'ABSPATH' ) || exit;

interface Hookable {
	public function set_hooks( bool $is_admin_area ): void;
}
