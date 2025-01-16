<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use Io\Prosopo\Procaptcha\Widget\Widget;

abstract class Procaptcha_Plugin_Integration implements Plugin_Integration {
	private Widget $widget;

	public static function make_instance( Widget $widget ): Plugin_Integration {
		return new static( $widget );
	}

	final public function __construct( Widget $widget ) {
		$this->widget = $widget;
	}

	public function requires_late_hooking(): bool {
		return false;
	}

	public function get_setting_tab_classes(): array {
		return array();
	}

	public function include_form_integrations(): void {
	}

	protected function get_widget(): Widget {
		return $this->widget;
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
