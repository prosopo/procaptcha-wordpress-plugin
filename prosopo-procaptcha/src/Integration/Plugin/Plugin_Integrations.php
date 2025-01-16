<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Plugin;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Form\Helper\Form_Integration_Helper;
use Io\Prosopo\Procaptcha\Settings\Settings_Page;
use Io\Prosopo\Procaptcha\Settings\Storage\Settings_Storage;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Widget\Widget;

class Plugin_Integrations {
	private Plugin_Integrator $plugin_integrator;
	private Settings_Storage $settings_storage;
	private Form_Integration_Helper $form_helper;
	private Settings_Page $settings_page;
	private bool $is_admin_area;

	public function __construct(
		Plugin_Integrator $plugin_integrator,
		Settings_Storage $settings_storage,
		Form_Integration_Helper $form_helper,
		Settings_Page $settings_page,
		bool $is_admin_area
	) {
		$this->plugin_integrator = $plugin_integrator;
		$this->settings_storage  = $settings_storage;
		$this->form_helper       = $form_helper;
		$this->settings_page     = $settings_page;
		$this->is_admin_area     = $is_admin_area;
	}

	/**
	 * @param class-string<Plugin_Integration>[] $plugin_integration_classes
	 *
	 * @return Plugin_Integration[]
	 */
	public function make_plugin_integrations( array $plugin_integration_classes, Widget $widget ): array {
		return array_map(
		/**
		 * @param class-string<Plugin_Integration> $plugin_integration_class
		 */
			function ( string $plugin_integration_class ) use ( $widget ) {
				return $plugin_integration_class::make_instance( $widget );
			},
			$plugin_integration_classes
		);
	}

	/**
	 * @param Plugin_Integration[] $plugin_integrations
	 */
	public function initialize_integrations( array $plugin_integrations ): void {
		$std_plugin_integrations  = array_filter(
			$plugin_integrations,
			function ( $plugin_integration ) {
				return ! $plugin_integration->requires_late_hooking();
			}
		);
		$late_plugin_integrations = array_filter(
			$plugin_integrations,
			function ( $plugin_integration ) {
				return $plugin_integration->requires_late_hooking();
			}
		);

		if ( array() !== $std_plugin_integrations ) {
			// Used -999 priority, as some plugins, like e.g. NinjaForms registers fields here, and we need to add hooks before it.
			$this->initialize_integrations_with_priority( $std_plugin_integrations, -999 );
		}

		if ( array() !== $late_plugin_integrations ) {
			// Some plugins (like Elementor Pro) load their right on the 'plugins_loaded' hook, so we need to load them after.
			$this->initialize_integrations_with_priority( $late_plugin_integrations, 999 );
		}
	}

	/**
	 * @param Plugin_Integration[] $plugin_integrations
	 *
	 * @return class-string<Settings_Tab>[]
	 */
	public function get_setting_tabs( array $plugin_integrations ): array {
		$setting_tab_classes = array_map(
			function ( Plugin_Integration $plugin_integration ) {
				return $plugin_integration->get_setting_tab_classes();
			},
			$plugin_integrations
		);

		return array_merge( ...$setting_tab_classes );
	}

	/**
	 * @param Plugin_Integration[] $plugin_integrations
	 */
	protected function initialize_integrations_with_priority( array $plugin_integrations, int $hook_priority ): void {
		$item_init = function ( Plugin_Integration $plugin_integration ) {
			if ( ! $this->plugin_integrator->integration_active( $plugin_integration ) ) {
				return;
			}

			$this->initialize_integration( $plugin_integration );
		};

		// Since this hooks we can judge if some plugin is available.
		add_action(
			'plugins_loaded',
			function () use ( $item_init, $plugin_integrations ) {
				array_map( $item_init, $plugin_integrations );
			},
			$hook_priority
		);
	}

	protected function initialize_integration( Plugin_Integration $plugin_integration ): void {
		$plugin_integration->include_form_integrations();

		$form_integrations = $plugin_integration->get_form_integrations( $this->settings_storage );

		$this->plugin_integrator->inject_form_helper( $form_integrations, $this->form_helper );

		$hookable_form_integrations = $this->plugin_integrator->create_hookable_form_integrations( $form_integrations );
		$this->plugin_integrator->set_hooks_for_hookable_form_instances( $hookable_form_integrations, $this->is_admin_area );

		if ( $plugin_integration instanceof Hookable ) {
			$plugin_integration->set_hooks( $this->is_admin_area );
		}

		$setting_tab_classes = $plugin_integration->get_setting_tab_classes();

		if ( array() !== $setting_tab_classes ) {
			$this->settings_page->add_setting_tabs( $setting_tab_classes );
		}
	}
}
