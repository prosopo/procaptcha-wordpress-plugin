<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration;
use Io\Prosopo\Procaptcha\Widget\Widget;

class Plugin_Integrator {
	public function integration_active( Plugin_Integration $plugin_integration ): bool {
		$plugin_classes = $plugin_integration->get_target_plugin_classes();

		// No target classes means core WP integration.
		return array() === $plugin_classes ||
			$this->is_one_of_classes_is_loaded( $plugin_classes );
	}

	/**
	 * @param class-string<Form_Integration>[] $form_integrations
	 */
	public function inject_widget(
		array $form_integrations,
		Widget $widget
	): void {
		array_map(
		/**
		 * @param class-string<Form_Integration> $form_integration
		 */
			function ( string $form_integration ) use ( $widget ) {
				$form_integration::set_widget( $widget );
			},
			$form_integrations
		);
	}

	/**
	 * @param class-string<Form_Integration>[] $form_integrations
	 *
	 * @return Hookable_Form_Integration[]
	 */
	public function create_hookable_form_integrations( array $form_integrations ): array {
		/**
		 * @var class-string<Hookable_Form_Integration>[] $hookable_classes
		 */
		$hookable_classes = array_filter(
			$form_integrations,
			function ( string $form_integration ) {
				$class_implements = class_implements( $form_integration );
				$class_implements = is_array( $class_implements ) ?
				$class_implements :
				array();

				return in_array( Hookable_Form_Integration::class, $class_implements, true );
			}
		);

		$hookable_instances = array_map(
			function ( string $hookable_class ) {
				return $hookable_class::make_instance();
			},
			$hookable_classes
		);

		return array_values( $hookable_instances );
	}

	/**
	 * @param Hookable_Form_Integration[] $hookable_form_instances
	 */
	public function set_hooks_for_hookable_form_instances( array $hookable_form_instances, bool $is_admin_area ): void {
		array_map(
			function ( Hookable_Form_Integration $hookable_form_instance ) use ( $is_admin_area ) {
				$hookable_form_instance->set_hooks( $is_admin_area );
			},
			$hookable_form_instances
		);
	}

	/**
	 * @param string[] $classes
	 */
	protected function is_one_of_classes_is_loaded( array $classes ): bool {
		foreach ( $classes as $class ) {
			if ( ! class_exists( $class, false ) ) {
				continue;
			}

			return true;
		}

		return false;
	}
}
