<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Beaver_Builder;

use FLBuilderModel;
use Io\Prosopo\Procaptcha\Query_Arguments;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\objectOrNull;
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

	/**
	 * @param callable(object $module): void $validate_submission
	 */
	public static function extend_module_submit_validation( string $ajax_hook, callable $validate_submission ): void {
		$run_validation = function () use ( $validate_submission ) {
			$submitted_module = self::resolve_submitted_module();

			if ( is_object( $submitted_module ) ) {
				$validate_submission( $submitted_module );
			}

			// todo log if not object, it means breaking changes.
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

	protected static function resolve_submitted_module(): ?object {
		$node_id          = Query_Arguments::get_non_action_string( 'node_id', Query_Arguments::POST );
		$template_id      = Query_Arguments::get_non_action_string( 'template_id', Query_Arguments::POST );
		$template_node_id = Query_Arguments::get_non_action_string( 'template_node_id', Query_Arguments::POST );

		return strlen( $template_id ) > 0 ?
			self::resolve_template_module( $template_id, $template_node_id ) :
			self::resolve_node_module( $node_id );
	}

	protected static function resolve_node_module( string $node_id ): ?object {
		$node_module = is_callable( array( 'FLBuilderModel', 'get_module' ) ) ?
			FLBuilderModel::get_module( $node_id ) :
			false;

		// todo log if not callable, it means breaking changes.

		return objectOrNull( $node_module );
	}

	protected static function resolve_template_module( string $template_id, string $template_node_id ): ?object {
		$data = null;

		if ( is_callable( array( 'FLBuilderModel', 'get_node_template_post_id' ) ) &&
			is_callable( array( 'FLBuilderModel', 'get_layout_data' ) ) ) {
			$post_id = FLBuilderModel::get_node_template_post_id( $template_id );
			$data    = FLBuilderModel::get_layout_data( 'published', $post_id );
		}

		// todo log if not callable, it means breaking changes.

		return objectOrNull( $data, $template_node_id );
	}
}
