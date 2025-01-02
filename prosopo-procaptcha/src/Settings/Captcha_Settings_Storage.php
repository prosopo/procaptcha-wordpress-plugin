<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Tab;

class Captcha_Settings_Storage implements Settings_Storage {
	/**
	 * @var array<string,Settings_Tab>
	 */
	private array $settings_tabs;

	public function __construct() {
		$this->settings_tabs = array();
	}

	/**
	 * @param class-string<Settings_Tab> $item_class
	 *
	 * @return Settings_Tab
	 */
	public function get( string $item_class ): Settings_Tab {
		if ( key_exists( $item_class, $this->settings_tabs ) ) {
			return $this->settings_tabs[ $item_class ];
		}

		$this->settings_tabs[ $item_class ] = new $item_class();

		return $this->settings_tabs[ $item_class ];
	}
}
