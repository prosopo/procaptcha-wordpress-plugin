<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Module\Configurable_Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Module\Module_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Settings\Settings_Page;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;

defined( 'ABSPATH' ) || exit;

final class Integrations_Loader implements Hookable {
	/**
	 * @var Module_Integration[]
	 */
	private array $module_integrations;
	/**
	 * @var Plugin_Integration[]
	 */
	private array $plugin_integrations;
	/**
	 * @var Module_Integration[]
	 */
	private array $loaded_integrations;

	private Settings_Page $settings_page;


	public function __construct( Settings_Page $settings_page ) {
		$this->module_integrations = array();
		$this->plugin_integrations = array();
		$this->loaded_integrations = array();

		$this->settings_page = $settings_page;
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		$this->load_module_integrations( $screen_detector );

		/**
		 * Since 'plugins_loaded' hook we can judge if some plugin is available.
		 *
		 * We use "before hook" and "after hook" priorities as some plugins,
		 * e.g. NinjaForms registers fields in 'plugins_loaded', and we need to add hooks before it.
		 *
		 * Meanwhile, others, e.g. Elementor Pro, only load their field classes on the 'plugins_loaded' hook,
		 * so we need to execute after them.
		 */
		$priorities = array( -999, 999 );

		foreach ( $priorities as $priority ) {
			add_action(
				'plugins_loaded',
				fn() => $this->load_plugin_integrations( $screen_detector ),
				$priority
			);
		}
	}

	/**
	 * @param Module_Integration[] $module_integrations
	 */
	public function set_module_integrations( array $module_integrations ): void {
		$this->module_integrations = $module_integrations;
	}

	/**
	 * @param Plugin_Integration[] $plugin_integrations
	 */
	public function set_plugin_integrations( array $plugin_integrations ): void {
		$this->plugin_integrations = $plugin_integrations;
	}

	/**
	 * @return Settings_Tab[]
	 */
	public function get_all_settings_tabs(): array {
		$available_integrations = array_merge(
			$this->module_integrations,
			$this->plugin_integrations,
			$this->loaded_integrations
		);

		$settings_tabs = array();

		foreach ( $available_integrations as $available_integration ) {
			if ( $available_integration instanceof Configurable_Module_Integration ) {
				$settings_tabs[] = $available_integration->get_settings_tab();
			}
		}

		return $settings_tabs;
	}

	protected function load_plugin_integrations( Screen_Detector $screen_detector ): void {
		$active_integrations = array_filter(
			$this->plugin_integrations,
			fn( Plugin_Integration $integration ) => $integration->is_active()
		);

		foreach ( $active_integrations as $integration ) {
			$this->load_integration( $integration, $screen_detector );
		}

		$this->plugin_integrations = array_diff( $this->plugin_integrations, $active_integrations );
	}

	protected function load_module_integrations( Screen_Detector $screen_detector ): void {
		foreach ( $this->module_integrations as $integration ) {
			$this->load_integration( $integration, $screen_detector );
		}

		$this->module_integrations = array();
	}

	protected function load_integration( Module_Integration $integration, Screen_Detector $screen_detector ): void {
		$integration->set_hooks( $screen_detector );

		if ( $integration instanceof Configurable_Module_Integration ) {
			$settings_tab = $integration->get_settings_tab();

			$this->settings_page->add_tab( $settings_tab );
		}

		$this->loaded_integrations[] = $integration;
	}
}
