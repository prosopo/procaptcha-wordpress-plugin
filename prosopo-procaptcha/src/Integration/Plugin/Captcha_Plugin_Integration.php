<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Definition\Captcha\Captcha;
use Io\Prosopo\Procaptcha\Definition\Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Definition\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Storage;

abstract class Captcha_Plugin_Integration implements Plugin_Integration {
	private Captcha $captcha;

	public static function make_instance( Captcha $captcha ): Plugin_Integration {
		return new static( $captcha );
	}

	final public function __construct( Captcha $captcha ) {
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

	protected function get_captcha(): Captcha {
		return $this->captcha;
	}

	/**
	 * @return array<class-string<Form_Integration>, bool>
	 */
	protected function get_conditional_integrations( Settings_Storage $settings_storage ): array {
		return array();
	}

	/**
	 * @return class-string<Form_Integration>[]
	 */
	protected function get_active_conditional_integrations( Settings_Storage $settings_storage ): array {
		$active_integrations = array_filter( $this->get_conditional_integrations( $settings_storage ) );

		return array_keys( $active_integrations );
	}
}
