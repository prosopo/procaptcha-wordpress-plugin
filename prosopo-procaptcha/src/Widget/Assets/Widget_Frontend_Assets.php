<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Widget\Assets;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Frontend_Assets;
use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin\Assets\Plugin_Frontend_Assets;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;

class Widget_Frontend_Assets extends Frontend_Assets implements Hookable {
	const WIDGET_JS_HANDLE             = 'prosopo-procaptcha-widget';
	const INTEGRATION_JS_HANDLE_PREFIX = 'prosopo-procaptcha-integration-';

	private Settings_Tab $general_settings;
	private Plugin_Frontend_Assets $plugin_assets_manager;
	private Widget_Assets $widget_assets;

	private string $service_script_url;
	private string $service_script_handle;

	private bool $is_in_use;
	/**
	 * Some JS-based forms (like NinjaForms) require a separate JS integration.
	 *
	 * @var string[]
	 */
	private array $integration_js_files;
	private string $integrations_css_code;

	public function __construct(
		string $service_script_url,
		string $service_script_handle,
		Plugin_Frontend_Assets $plugin_assets_manager,
		Settings_Tab $general_settings,
		Widget_Assets $widget_assets
	) {
		$this->service_script_url    = $service_script_url;
		$this->service_script_handle = $service_script_handle;

		$this->general_settings      = $general_settings;
		$this->plugin_assets_manager = $plugin_assets_manager;
		$this->widget_assets         = $widget_assets;

		$this->is_in_use             = false;
		$this->integration_js_files  = array();
		$this->integrations_css_code = '';
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'script_loader_tag', array( $this, 'add_module_tag_attribute_to_widget_scripts' ), 10, 2 );

		$hook = $is_admin_area ?
			'admin_print_footer_scripts' :
			'wp_print_footer_scripts';

		// priority must be less than 10, to make sure the wp_enqueue_script still has effect.
		add_action( $hook, array( $this, 'enqueue_assets_when_in_use' ), 1 );
	}

	public function add_integration_js( string $integration_name ): void {
		$this->integration_js_files[] = $integration_name;
	}

	public function add_integration_css( string $css_code ): void {
		$this->integrations_css_code .= $css_code;
	}

	public function add_widget(): void {
		$this->is_in_use = true;
	}

	public function enqueue_assets_when_in_use(): void {
		if ( ! $this->is_in_use ) {
			return;
		}

		$this->widget_assets->enqueue_service_js( $this->service_script_handle, $this->service_script_url );
		$this->widget_assets->enqueue_widget_js(
			self::WIDGET_JS_HANDLE,
			$this->plugin_assets_manager,
			$this->general_settings->get_settings()
		);

		$this->widget_assets->enqueue_integration_js_files(
			self::INTEGRATION_JS_HANDLE_PREFIX,
			$this->plugin_assets_manager,
			$this->integration_js_files
		);

		if ( '' !== $this->integrations_css_code ) {
			$this->widget_assets->print_css_code( $this->integrations_css_code );
		}
	}


	// We have to manually add the module attribute for our scripts,
	// as wp_enqueue_script_module() doesn't work on the login screens.
	public function add_module_tag_attribute_to_widget_scripts( string $tag, string $handle ): string {
		if ( ! $this->is_widget_related_script( $handle ) ||
		$this->is_script_tag_with_module_attribute( $tag ) ) {
			return $tag;
		}

		return $this->add_module_script_tag_attribute( $tag );
	}

	protected function is_widget_related_script( string $handle ): bool {
		return in_array( $handle, array( $this->service_script_handle, self::WIDGET_JS_HANDLE ), true ) ||
				0 === strpos( $handle, self::INTEGRATION_JS_HANDLE_PREFIX );
	}
}
