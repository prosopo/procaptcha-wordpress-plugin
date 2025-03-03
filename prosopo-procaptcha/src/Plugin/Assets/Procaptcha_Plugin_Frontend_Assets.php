<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin\Assets;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Frontend_Assets;
use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin\Assets\Plugin_Frontend_Assets;
use WP_Filesystem_Base;

class Procaptcha_Plugin_Frontend_Assets extends Frontend_Assets implements Plugin_Frontend_Assets, Hookable {
	private string $plugin_file;
	private string $version;
	private WP_Filesystem_Base $wp_filesystem;
	/**
	 * @var string[]
	 */
	private array $integration_javascript_tag_handles;

	public function __construct( string $plugin_file, string $version, WP_Filesystem_Base $wp_filesystem ) {
		$this->plugin_file                        = $plugin_file;
		$this->version                            = $version;
		$this->wp_filesystem                      = $wp_filesystem;
		$this->integration_javascript_tag_handles = array();
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'script_loader_tag', array( $this, 'add_module_tag_attribute_to_integration_script' ), 10, 2 );
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

	public function enqueue_plugin_javascript_file(
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

		$this->integration_javascript_tag_handles[] = $asset_handle;

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

	public function add_module_tag_attribute_to_integration_script( string $tag, string $handle ): string {
		if ( ! in_array( $handle, $this->integration_javascript_tag_handles, true ) ||
		$this->is_script_tag_with_module_attribute( $tag ) ) {
			return $tag;
		}

		return $this->add_module_script_tag_attribute( $tag );
	}

	protected function get_asset_handle( string $asset ): string {
		return 'prosopo-procaptcha-' . $asset;
	}
}
