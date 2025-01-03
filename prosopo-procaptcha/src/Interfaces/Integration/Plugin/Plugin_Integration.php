<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha;
use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Tab;

interface Plugin_Integration {
	public static function make_instance( Captcha $captcha ): self;

	/**
	 * 1. Using classes instead of plugin names, since is_active_plugin doesn't work on the front.
	 * 2. Support multiple, since one plugin can have both Lite and Pro versions.
	 *
	 * @return string[]
	 */
	public function get_target_plugin_classes(): array;

	public function requires_late_hooking(): bool;

	/**
	 * @return class-string<Form_Integration>[]
	 */
	public function get_form_integrations( Settings_Storage $settings_storage ): array;

	/**
	 * @return class-string<Settings_Tab>[]
	 */
	public function get_setting_tab_classes(): array;

	// Only for exceptional cases, when we have to put classes into the global namespace,
	// see the User_Registration integration for example.
	public function include_form_integrations(): void;
}
