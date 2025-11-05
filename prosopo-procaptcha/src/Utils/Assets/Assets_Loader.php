<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Utils\Assets;

use Io\Prosopo\Procaptcha\Utils\Assets\Assets_Resolver;
use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

defined( 'ABSPATH' ) || exit;

final class Assets_Loader implements Hookable {
	private Assets_Resolver $assets_resolver;
	/**
	 * @var array<int,string>
	 */
	private array $loaded_assets;
	/**
	 * @var array<int,string>
	 */
	private array $loaded_script_handles;

	public function __construct( Assets_Resolver $assets_resolver ) {
		$this->assets_resolver       = $assets_resolver;
		$this->loaded_assets         = array();
		$this->loaded_script_handles = array();
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
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
		$this->loaded_script_handles[] = $handle;

		$has_data         = array() !== $data;
		$has_dependencies = array() !== $dependencies;

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
			// the version is a part of the file path (dist/x/settings).
            // @phpcs:ignore
			null,
			$script_settings
		);

		if ( $has_data ) {
			wp_localize_script( $handle, $data_object_name, $data );
		}
	}

	public function mark_asset_as_loaded( string $relative_asset_path ): void {
		$this->loaded_assets[] = $relative_asset_path;
	}

	/**
	 * @param array<int, string> $dependency_scripts
	 * @param array<string, mixed> $data
	 */
	public function load_script_asset(
		string $relative_script_path,
		array $dependency_scripts = array(),
		string $data_object_name = '',
		array $data = array()
	): string {
		$script_url = $this->assets_resolver->resolve_asset_url( $relative_script_path );
		$handle     = $this->get_asset_handle( $relative_script_path );

		$dependency_handles = array_map(
			function ( string $dependency_script ) {
				return $this->get_asset_handle( $dependency_script );},
			$dependency_scripts
		);

		$this->mark_asset_as_loaded( $relative_script_path );

		$this->load_script( $handle, $script_url, $dependency_handles, $data_object_name, $data );

		return $script_url;
	}

	public function add_module_attribute_for_loaded_script( string $script_tag, string $script_handle ): string {
		if ( $this->is_script_handle_loaded( $script_handle ) ) {
			// for old WP versions.
			$script_tag = str_replace( ' type="text/javascript"', '', $script_tag );

			$script_tag = str_replace( 'src=', 'type="module" src=', $script_tag );
		}

		return $script_tag;
	}

	/**
	 * @return array<int,string>
	 */
	public function get_loaded_assets(): array {
		return $this->loaded_assets;
	}

	protected function is_script_handle_loaded( string $script_handle ): bool {
		return in_array( $script_handle, $this->loaded_script_handles, true );
	}

	protected function get_asset_handle( string $relative_asset_path ): string {
		return 'prosopo-procaptcha-' . $relative_asset_path;
	}
}
