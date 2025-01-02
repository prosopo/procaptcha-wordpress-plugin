<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Definition\Settings;

defined( 'ABSPATH' ) || exit;

interface Settings_Storage {
	/**
	 * @param class-string<Settings_Tab> $item_class
	 *
	 * @return Settings_Tab
	 */
	public function get( string $item_class ): Settings_Tab;
}
