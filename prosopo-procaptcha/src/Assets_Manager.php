<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Assets_Manager_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use WP_Filesystem_Base;

class Assets_Manager implements Assets_Manager_Interface, Hooks_Interface {
	private string $plugin_file;
	private string $version;
	private WP_Filesystem_Base $wp_filesystem;
	/**
	 * @var string[]
	 */
	private array $module_handles;

	public function __construct( string $plugin_file, string $version, WP_Filesystem_Base $wp_filesystem ) {
		$this->plugin_file    = $plugin_file;
		$this->version        = $version;
		$this->wp_filesystem  = $wp_filesystem;
		$this->module_handles = array();
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'script_loader_tag', array( $this, 'add_module_attr_for_modules' ), 10, 2 );
	}

	public function get_asset_url( string $asset ): string {
		return plugin_dir_url( $this->plugin_file ) . 'dist/' . $asset;
	}

	public function get_asset_content( string $asset ): string {
		$file = plugin_dir_path( $this->plugin_file ) . 'dist/' . $asset;

		if ( ! $this->wp_filesystem->exists( $file ) ) {
			return '';
		}

		return (string) $this->wp_filesystem->get_contents( $file );
	}

	public function get_assets_version(): string {
		return $this->version;
	}

	public function enqueue_module_js_asset(
		string $asset,
		array $dependency_assets = array(),
		string $data_object_name = '',
		array $data = array()
	): void {
		$asset_url    = $this->get_asset_url( $asset );
		$asset_handle = $this->get_asset_handle( $asset );

		$has_data         = array() !== $data;
		$has_dependencies = array() !== $dependency_assets;
		$script_settings  = array(
			'in_footer' => true,
			'strategy ' => $has_dependencies ?
				'defer' :
				'async',
		);

		$dependency_handles = array_map(
			function ( string $asset ) {
				return $this->get_asset_handle( $asset );
			},
			$dependency_assets
		);

		$this->module_handles[] = $asset_handle;

		// wp_enqueue_module doesn't have 'in_footer' setting.
		wp_enqueue_script(
			$asset_handle,
			$asset_url,
			$dependency_handles,
			$this->get_assets_version(),
			$script_settings
		);

		if ( $has_data ) {
			wp_localize_script( $asset_handle, $data_object_name, $data );
		}
	}

	public function add_module_attr_for_modules( string $tag, string $handle ): string {
		if ( ! in_array( $handle, $this->module_handles, true ) ) {
			return $tag;
		}

		return $this->add_module_attr_when_missing( $tag );
	}

	public function add_module_attr_when_missing( string $tag ): string {
		if (
			// make sure we don't make it twice if other Procaptcha integrations are present.
			false !== strpos( 'type="module"', $tag )
		) {
			return $tag;
		}

		// for old WP versions.
		$tag = str_replace( ' type="text/javascript"', '', $tag );

		return str_replace( 'src', 'type="module" src', $tag );
	}

	protected function get_asset_handle( string $asset ): string {
		return 'prosopo-procaptcha-' . $asset;
	}
}
