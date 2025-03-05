<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Assets;

use Io\Prosopo\Procaptcha\Hookable;

defined( 'ABSPATH' ) || exit;

final class Assets_Loader implements Hookable {
	/**
	 * @var array<int,string>
	 */
	private array $loaded_script_handles;
	private Assets_Resolver $assets_resolver;

	public function __construct( Assets_Resolver $assets_resolver ) {
		$this->loaded_script_handles = array();
		$this->assets_resolver       = $assets_resolver;
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'script_loader_tag', array( $this, 'add_module_attribute_for_loaded_script' ), 10, 2 );
	}

	/**
	 * @param array<int, string> $dependencies
	 * @param array<string, mixed> $data
	 */
	public function load_script(
		string $handle,
		string $url,
		array $dependencies = array(),
		string $data_object_name = '',
		array $data = array()
	): void {
		$has_data         = array() !== $data;
		$has_dependencies = array() !== $dependencies;

		$this->loaded_script_handles[] = $handle;

		$script_settings = array(
			'in_footer' => true,
			'strategy ' => $has_dependencies ?
				'defer' :
				'async',
		);

		// do not use wp_enqueue_module because:
		// 1. it doesn't work on the login screens
		// 2. doesn't have 'in_footer' setting.

		wp_enqueue_script(
			$handle,
			$url,
			$dependencies,
			// when set, the version is a part of the url.
			null,
			$script_settings
		);

		if ( $has_data ) {
			wp_localize_script( $handle, $data_object_name, $data );
		}
	}

	/**
	 * @param array<int, string> $dependency_scripts
	 * @param array<string, mixed> $data
	 */
	public function load_plugin_script(
		string $relative_path,
		array $dependency_scripts = array(),
		string $data_object_name = '',
		array $data = array()
	): void {
		$url    = $this->assets_resolver->resolve_asset_url( $relative_path );
		$handle = $this->get_plugin_script_handle( $relative_path );

		$dependency_handles = array_map(
			function ( string $dependency_script ) {
				return $this->get_plugin_script_handle( $dependency_script );},
			$dependency_scripts
		);

		$this->load_script( $handle, $url, $dependency_handles, $data_object_name, $data );
	}

	public function add_module_attribute_for_loaded_script( string $script_tag, string $script_handle ): string {
		if ( $this->is_loaded_plugin_script( $script_handle ) ) {
			// for old WP versions.
			$script_tag = str_replace( ' type="text/javascript"', '', $script_tag );

			$script_tag = str_replace( 'src=', 'type="module" src=', $script_tag );
		}

		return $script_tag;
	}

	protected function is_loaded_plugin_script( string $script_handle ): bool {
		return in_array( $script_handle, $this->loaded_script_handles, true );
	}

	protected function get_plugin_script_handle( string $relative_path ): string {
		return 'prosopo-procaptcha-' . $relative_path;
	}
}
