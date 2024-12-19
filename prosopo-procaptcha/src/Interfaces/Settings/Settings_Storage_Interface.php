<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Settings;

defined( 'ABSPATH' ) || exit;

interface Settings_Storage_Interface {
	/**
	 * @param class-string<Settings_Tab_Interface> $item_class
	 *
	 * @return Settings_Tab_Interface
	 */
	public function get( string $item_class ): Settings_Tab_Interface;
}
