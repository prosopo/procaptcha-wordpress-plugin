<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder\Forms;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

defined( 'ABSPATH' ) || exit;

final class Beaver_Contact_Form_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		add_filter(
			'fl_builder_register_module_settings_form',
			fn ( array $form, string $slug ) =>'contact-form' === $slug ? $this->add_field( $form ) : $form,
			10,
			2
		);

		add_filter(
			'fl_builder_contact_form_fields',
			function ( array $fields, object $form_settings ) {

				$is_enabled = 'enabled' === string( $form_settings, 'procaptcha' );

				var_dump( $is_enabled );// fixme save as a var

				return $fields;
			},
			10,
			2
		);

		add_action(
			'fl_builder_render_module_html_before',
			function ( $type, $settings, $module ) {

				if ( 'button' === $type ) {
					echo 'here';// fixme print
				}
			},
			10,
			3
		);
	}

	/**
	 * @param array<string,mixed> $form
	 *
	 * @return array<string,mixed>
	 */
	protected function add_field( array $form ): array {
		$widget     = self::get_form_helper()->get_widget();
		$field_name = $widget->get_field_name();

		// fixme harness phpTyped package
        /*setItem(
			$form,
			array( 'general', 'sections', 'general', 'fields', $field_name ),
			$this->get_field_settings()
		);*/

        return $form;
	}

	/**
	 * @return array<string,mixed>
	 */
	protected function get_field_settings(): array {
		return array(
			'default' => 'disabled',
			'label'   => __( 'Procaptcha protection', 'prosopo-procaptcha' ),
			'options' => array(
				'off' => __( 'Disabled', 'prosopo-procaptcha' ),
				'on'  => __( 'Enabled', 'prosopo-procaptcha' ),
			),
			'type'    => 'select',
		);
	}
}
