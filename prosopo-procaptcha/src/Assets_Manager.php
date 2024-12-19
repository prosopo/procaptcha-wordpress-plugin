<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Assets_Manager_Interface;
use WP_Filesystem_Base;

class Assets_Manager implements Assets_Manager_Interface {
	private string $plugin_file;
	private string $version;
	private WP_Filesystem_Base $wp_filesystem;

	public function __construct( string $plugin_file, string $version, WP_Filesystem_Base $wp_filesystem ) {
		$this->plugin_file   = $plugin_file;
		$this->version       = $version;
		$this->wp_filesystem = $wp_filesystem;
	}

	public function get_asset_url( string $asset ): string {
		return plugin_dir_url( $this->plugin_file ) . 'dist/' . $asset;
	}

	public function get_asset_content( string $asset ): string {
		$file = plugin_dir_path( $this->plugin_file ) . 'dist/' . $asset;

		if ( false === $this->wp_filesystem->exists( $file ) ) {
			return '';
		}

		return (string) $this->wp_filesystem->get_contents( $file );
	}

	public function get_assets_version(): string {
		return $this->version;
	}

	protected function get_wp_filesystem(): WP_Filesystem_Base {
		global $wp_filesystem;

		require_once ABSPATH . 'wp-admin/includes/file.php';

		WP_Filesystem();

		return $wp_filesystem;
	}
}
