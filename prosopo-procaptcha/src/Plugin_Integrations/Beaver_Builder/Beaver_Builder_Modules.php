<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

use WP_Error;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\setItem;

defined( 'ABSPATH' ) || exit;

class Beaver_Builder_Modules {
	/**
	 * @param string[] $setting_path
	 * @param array<string,mixed> $setting_options
	 */
	public function add_module_setting( string $module, array $setting_path, array $setting_options ): void {
		add_filter(
			'fl_builder_register_module_settings_form',
			function ( array $form, string $slug ) use ( $module, $setting_path, $setting_options ): array {
				if ( $module === $slug ) {
					setItem(
						$form,
						$setting_path,
						$setting_options
					);
				}

				return $form;
			},
			10,
			2
		);
	}

	/**
	 *
	 * @param callable():void $on_render
	 */
	public function on_module_render( string $module, callable $on_render ): void {
		add_action(
			'fl_builder_render_module_html_before',
			function ( string $module_slug ) use ( $module, $on_render ) {
				if ( $module_slug === $module ) {
					$on_render();
				}
			}
		);
	}

	/**
	 *
	 * @param callable(object $form): void $on_render
	 */
	public function on_form_render( callable $on_render ): void {
		add_filter(
			'fl_builder_contact_form_fields',
			function ( array $fields, object $form ) use ( $on_render ): array {
				$on_render( $form );

				return $fields;
			},
			10,
			2
		);
	}

	/**
	 * @param callable(object $form): ?WP_Error $validate_form
	 */
	public function add_form_validation( callable $validate_form ): void {
		add_action(
			'fl_module_contact_form_before_send',
			function ( string $mailto, string $subject, string $template, array $headers, object $form ) use ( $validate_form ) {
				$validation_error = $validate_form( $form );

				if ( $validation_error instanceof WP_Error ) {
					wp_send_json(
						array(
							'error'   => true,
							'message' => $validation_error->get_error_message(),
						)
					);
				}
			},
			10,
			5
		);
	}
}
