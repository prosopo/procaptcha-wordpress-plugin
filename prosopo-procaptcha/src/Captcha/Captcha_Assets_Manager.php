<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Captcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Assets_Manager_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Assets_Manager_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Hooks_Interface;
use Io\Prosopo\Procaptcha\Interfaces\Settings\Settings_Tab_Interface;

class Captcha_Assets_Manager implements Hooks_Interface, Captcha_Assets_Manager_Interface {
	const WIDGET_JS_HANDLE             = 'prosopo-procaptcha-widget';
	const INTEGRATION_JS_HANDLE_PREFIX = 'prosopo-procaptcha-integration-';

	private Settings_Tab_Interface $general_settings;
	private Assets_Manager_Interface $assets_manager;
	private Captcha_Assets $captcha_scripts;

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
		Assets_Manager_Interface $assets_manager,
		Settings_Tab_Interface $general_settings,
		Captcha_Assets $captcha_scripts
	) {
		$this->service_script_url    = $service_script_url;
		$this->service_script_handle = $service_script_handle;

		$this->general_settings = $general_settings;
		$this->assets_manager   = $assets_manager;
		$this->captcha_scripts  = $captcha_scripts;

		$this->is_in_use             = false;
		$this->integration_js_files  = array();
		$this->integrations_css_code = '';
	}

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'script_loader_tag', array( $this, 'add_module_attrs_for_target' ), 10, 2 );

		$hook = true === $is_admin_area ?
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
		if ( false === $this->is_in_use ) {
			return;
		}

		$captcha_scripts = $this->captcha_scripts;

		$captcha_scripts->enqueue_service_js( $this->service_script_handle, $this->service_script_url );
		$captcha_scripts->enqueue_widget_js(
			self::WIDGET_JS_HANDLE,
			$this->assets_manager,
			$this->general_settings->get_settings()
		);

		$captcha_scripts->enqueue_integration_js_files(
			self::INTEGRATION_JS_HANDLE_PREFIX,
			$this->assets_manager,
			$this->integration_js_files
		);

		if ( '' !== $this->integrations_css_code ) {
			$captcha_scripts->print_css_code( $this->integrations_css_code );
		}
	}


	// We have to manually add the module attribute for our scripts,
	// as wp_enqueue_script_module() doesn't work on the login screens.
	public function add_module_attrs_for_target( string $tag, string $handle ): string {
		if ( false === $this->is_target_script( $handle ) ) {
			return $tag;
		}

		return $this->captcha_scripts->add_module_attr_when_missing( $tag );
	}

	protected function is_target_script( string $handle ): bool {
		return true === in_array( $handle, array( $this->service_script_handle, self::WIDGET_JS_HANDLE ), true ) ||
				0 === strpos( $handle, self::INTEGRATION_JS_HANDLE_PREFIX );
	}
}
