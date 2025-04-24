<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

use FLBuilderModel;
use WP_Error;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\object;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\setItem;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

defined( 'ABSPATH' ) || exit;

final class Beaver_Modules {
	/**
	 * @param string[] $setting_path
	 * @param array<string,mixed> $setting_options
	 */
	public static function add_module_setting( string $module, array $setting_path, array $setting_options ): void {
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
	 * @param callable(object $module): void $on_render
	 */
	public static function on_module_render( callable $on_render, ?string $target_module_slug = null ): void {
		add_action(
			'fl_builder_before_render_module',
			function ( object $module ) use ( $on_render, $target_module_slug ) {
				$module_slug = string( $module, 'slug' );

				if ( is_null( $target_module_slug ) ||
					$module_slug === $target_module_slug ) {
					$on_render( $module );
				}
			}
		);
	}

	/**
	 *
	 * @param callable(object $module, object $item): void $on_render
	 */
	public static function on_module_item_render(
		callable $on_render,
		?string $target_module_slug = null,
		?string $target_item_slug = null
	): void {
		$rendering_module = null;

		self::on_module_render(
			function ( object $module ) use ( &$rendering_module ) {
				$rendering_module = $module;
			}
		);

		add_action(
			'fl_builder_render_module_html_before',
			function ( string $item_slug, object $item ) use ( $on_render, &$rendering_module, $target_module_slug, $target_item_slug ) {
				$is_target_module = is_null( $target_module_slug ) ||
					string( $rendering_module, 'slug' ) === $target_module_slug;
				$is_target_item   = is_null( $target_item_slug ) ||
					$item_slug === $target_item_slug;

				if ( is_object( $rendering_module ) &&
					$is_target_module &&
					$is_target_item ) {
					$on_render( $rendering_module, $item );
				}
			},
			10,
			2
		);
	}

	// fixme remove
	/**
	 * @param callable(object $module): void $validate_submission
	 */
	public static function extend_module_submit_validation( string $ajax_hook, callable $validate_submission ): void {
		$run_validation = function () use ( $validate_submission ) {
			$module = self::get_submitted_module();
			$validate_submission( $module );
		};

		$hook_names = array(
			sprintf( 'wp_ajax_%s', $ajax_hook ),
			sprintf( 'wp_ajax_nopriv_%s', $ajax_hook ),
		);

		foreach ( $hook_names as $hook_name ) {
			// Priority must be lower than 10, to be before the default Beaver handler.
			add_action( $hook_name, $run_validation, 1 );
		}
	}

	/**
	 * @param callable(object $form_settings): ?WP_Error $validate_form
	 */
	protected static function extend_contact_form_validation( callable $validate_form ): void {
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

	protected static function get_submitted_module(): object {
		// fixme
		$node_id          = isset( $_POST['node_id'] ) ? sanitize_text_field( $_POST['node_id'] ) : false;
		$template_id      = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : false;
		$template_node_id = isset( $_POST['template_node_id'] ) ? sanitize_text_field( $_POST['template_node_id'] ) : false;

		$module = $template_id ?
			self::get_template_module( $template_id, $template_node_id ) :
			FLBuilderModel::get_module( $node_id );

		return object( $module );
	}

	protected static function get_template_module( string $template_id, string $template_node_id ): object {
		$post_id = FLBuilderModel::get_node_template_post_id( $template_id );
		$data    = FLBuilderModel::get_layout_data( 'published', $post_id );

		return object( $data, $template_node_id );
	}
}
