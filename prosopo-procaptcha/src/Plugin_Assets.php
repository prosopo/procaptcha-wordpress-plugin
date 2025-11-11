<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Utils\Assets\Assets_Loader;
use Io\Prosopo\Procaptcha\Utils\Assets\Assets_Resolver;
use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Plugin_Assets implements Hookable {
	const DEV_HOST            = 'http://localhost:5173';
	const DEV_RELOADER_SCRIPT = '@vite/client';
	const DIST_FOLDER         = 'dist';
	const SRC_FOLDER          = 'src';

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

	public function set_hooks( Screen_Detector $screen_detector ): void {
		$this->assets_loader->set_hooks( $screen_detector );

		if ( $this->is_dev_mode ) {
			$hook = $screen_detector->is_admin_area() ?
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
		$dev_reloader_script_url = self::DEV_HOST . '/' . self::DEV_RELOADER_SCRIPT;

		$this->assets_loader->load_script( self::DEV_RELOADER_SCRIPT, $dev_reloader_script_url );
	}

	protected function create_dev_assets_resolver(): Assets_Resolver {
		$base_assets_url = sprintf( '%s/%s', self::DEV_HOST, self::SRC_FOLDER );

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
			sprintf( '%s/%s', self::DIST_FOLDER, $this->plugin_version );

		return new Assets_Resolver( $base_assets_url );
	}
}
