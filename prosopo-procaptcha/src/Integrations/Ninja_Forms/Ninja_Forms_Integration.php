<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Hookable;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;

class Ninja_Forms_Integration extends Captcha_Plugin_Integration implements Hookable {
	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array(
			Ninja_Form_Field::class,
		);
	}

	public function get_target_plugin_classes(): array {
		return array(
			'Ninja_Forms',
		);
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'ninja_forms_register_fields', array( $this, 'register_field' ) );
		add_filter( 'ninja_forms_field_template_file_paths', array( $this, 'register_templates_path' ) );
	}

	/**
	 * @param array<string,mixed> $fields
	 *
	 * @return array<string,mixed>
	 */
	public function register_field( array $fields ): array {
		/**
		 * @var array<string,mixed>
		 */
		return array_merge(
			$fields,
			array(
				$this->get_captcha()->get_field_name() => new Ninja_Form_Field(),
			)
		);
	}

	/**
	 * @param string[] $paths
	 *
	 * @return string[]
	 */
	public function register_templates_path( array $paths ): array {
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

		return $paths;
	}
}
