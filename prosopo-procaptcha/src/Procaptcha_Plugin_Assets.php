<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha;

use Io\Prosopo\Procaptcha\Assets\Assets_Loader;
use Io\Prosopo\Procaptcha\Assets\Assets_Resolver;

defined( 'ABSPATH' ) || exit;

final class Procaptcha_Plugin_Assets implements Hookable {
	private string $dev_host            = 'http://localhost:5173';
	private string $dev_reloader_script = '@vite/client';

	private string $plugin_file;
	private string $plugin_version;
	private Assets_Resolver $assets_resolver;
	private Assets_Loader $assets_loader;
	private bool $is_dev_mode;

	public function __construct( string $plugin_file, string $plugin_version, bool $is_dev_mode ) {
		$this->plugin_file    = $plugin_file;
		$this->plugin_version = $plugin_version;
		$this->is_dev_mode    = $is_dev_mode;

		$this->assets_resolver = $is_dev_mode ?
			$this->create_dev_assets_resolver() :
			$this->create_assets_resolver();

		$this->assets_loader = new Assets_Loader( $this->assets_resolver );
	}

	public function set_hooks( bool $is_admin_area ): void {
		$this->assets_loader->set_hooks( $is_admin_area );

		if ( $this->is_dev_mode ) {
			$hook = $is_admin_area ?
				'admin_print_footer_scripts' :
				'wp_print_footer_scripts';

			// priority must be less than 10, to make sure the wp_enqueue_script still has effect.
			add_action( $hook, array( $this, 'load_dev_assets_reloader' ), 1 );
		}
	}

	public function get_resolver(): Assets_Resolver {
		return $this->assets_resolver;
	}

	public function get_loader(): Assets_Loader {
		return $this->assets_loader;
	}

	public function load_dev_assets_reloader(): void {
		if ( $this->is_any_asset_loaded() ) {
			$this->load_dev_reloader_script();
		}
	}

	protected function is_any_asset_loaded(): bool {
		$loaded_assets_count = count( $this->assets_loader->get_loaded_assets() );

		return $loaded_assets_count > 0;
	}

	protected function load_dev_reloader_script(): void {
		$dev_reloader_script_url = $this->dev_host . '/' . $this->dev_reloader_script;

		$this->assets_loader->load_script( $this->dev_reloader_script, $dev_reloader_script_url );
	}

	protected function create_dev_assets_resolver(): Assets_Resolver {
		$base_assets_url = sprintf( '%s/src', $this->dev_host );

		$assets_resolver = new Assets_Resolver( $base_assets_url );

		$assets_resolver
			->set_url_extensions_map(
				array(
					'.min.css' => '.css',
					'.min.js'  => '.ts',
				)
			);

		return $assets_resolver;
	}

	protected function create_assets_resolver(): Assets_Resolver {
		$base_assets_url = plugin_dir_url( $this->plugin_file ) .
			sprintf( 'dist/%s', $this->plugin_version );

		return new Assets_Resolver( $base_assets_url );
	}
}
