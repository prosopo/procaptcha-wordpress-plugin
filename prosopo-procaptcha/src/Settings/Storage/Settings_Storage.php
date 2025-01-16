<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Storage;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;

interface Settings_Storage {
	/**
	 * @param class-string<Settings_Tab> $item_class
	 *
	 * @return Settings_Tab
	 */
	public function get( string $item_class ): Settings_Tab;
}
