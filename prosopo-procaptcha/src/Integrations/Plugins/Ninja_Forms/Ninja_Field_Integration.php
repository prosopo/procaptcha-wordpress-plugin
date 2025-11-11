<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Ninja_Field_Integration extends Widget_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
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
				$this->widget->get_field_name() => new Ninja_Field(),
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
