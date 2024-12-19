<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Everest_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage_Interface;

class Everest_Forms extends Plugin_Integration implements Hooks_Interface {
	/**
	 * @param string[] $fields
	 *
	 * @return string[]
	 */
	public function register_field( array $fields ): array {
		return array_merge(
			$fields,
			array(
				Everest_Forms_Field::class,
			)
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'everest_forms_fields', array( $this, 'register_field' ) );
	}

	public function get_form_integrations( Settings_Storage_Interface $settings_storage ): array {
		return array(
			Everest_Forms_Field::class,
		);
	}

	public function get_target_plugin_classes(): array {
		return array(
			'EverestForms',
		);
	}
}
