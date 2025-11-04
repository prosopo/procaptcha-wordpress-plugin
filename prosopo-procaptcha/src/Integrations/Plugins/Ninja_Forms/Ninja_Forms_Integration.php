<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms\Ninja_Forms_Form_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

final class Ninja_Forms_Integration extends Plugin_Integration_Base implements Hookable {
	public function get_about_integration(): About_Module_Integration {
		$about = new About_Module_Integration();

		$about->name     = 'Ninja Forms';
		$about->docs_url = self::get_docs_url( 'ninja-forms' );

		return $about;  }

	public function is_active(): bool {
		return class_exists( 'Ninja_Forms' );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

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
				$this->widget->get_field_name() => new Ninja_Forms_Form_Integration(),
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

	protected function get_external_integrations(): array {
		return array(
			Ninja_Forms_Form_Integration::class,
		);
	}
}
