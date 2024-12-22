<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Tab_Interface;

class Settings_Storage implements Settings_Storage_Interface {
	/**
	 * @var array<string,Settings_Tab_Interface>
	 */
	private array $settings_tabs;

	public function __construct() {
		$this->settings_tabs = array();
	}

	/**
	 * @param class-string<Settings_Tab_Interface> $item_class
	 *
	 * @return Settings_Tab_Interface
	 */
	public function get( string $item_class ): Settings_Tab_Interface {
		if ( key_exists( $item_class, $this->settings_tabs ) ) {
			return $this->settings_tabs[ $item_class ];
		}

		$this->settings_tabs[ $item_class ] = new $item_class();

		return $this->settings_tabs[ $item_class ];
	}
}
