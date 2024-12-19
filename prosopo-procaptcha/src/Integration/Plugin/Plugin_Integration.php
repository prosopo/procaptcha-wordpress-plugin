<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Integration_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Integration\Plugin\Plugin_Integration_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;

abstract class Plugin_Integration implements Plugin_Integration_Interface {
	private Captcha_Interface $captcha;

	public static function make_instance( Captcha_Interface $captcha ): Plugin_Integration_Interface {
		return new static( $captcha );
	}

	final public function __construct( Captcha_Interface $captcha ) {
		$this->captcha = $captcha;
	}

	public function requires_late_hooking(): bool {
		return false;
	}

	public function get_setting_tab_classes(): array {
		return array();
	}

	public function include_form_integrations(): void {
	}

	protected function get_captcha(): Captcha_Interface {
		return $this->captcha;
	}

	/**
	 * @return array<class-string<Form_Integration_Interface>, bool>
	 */
	protected function get_conditional_integrations( Settings_Storage_Interface $settings_storage ): array {
		return array();
	}

	/**
	 * @return class-string<Form_Integration_Interface>[]
	 */
	protected function get_active_conditional_integrations( Settings_Storage_Interface $settings_storage ): array {
		$active_integrations = array_filter( $this->get_conditional_integrations( $settings_storage ) );

		return array_keys( $active_integrations );
	}
}
