<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integration_Base;

class Ninja_Forms_Integration extends Plugin_Integration_Base implements Hookable {
	public function get_vendor_classes(): array {
		return array(
			'Ninja_Forms',
		);
	}

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
				$this->get_widget()->get_field_name() => new Ninja_Forms_Form_Integration(),
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

	protected function get_form_integrations(): array {
		return array(
			Ninja_Forms_Form_Integration::class,
		);
	}
}
