<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Integrations\Integrations_Loader;
use Io\Prosopo\Procaptcha\Integrations\Plugins\BBPress\BBPress;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Beaver_Builder;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro\Elementor_Pro;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Everest_Forms\Everest_Forms;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Fluent_Forms\Fluent_Forms;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Formidable_Forms\Formidable_Forms;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms\Gravity_Forms;
use Io\Prosopo\Procaptcha\Integrations\Plugins\JetPack\JetPack;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Memberpress;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms\Ninja_Forms;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Simple_Membership;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Spectra\Spectra;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\User_Registration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\WooCommerce;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WPForms\WPForms;
use Io\Prosopo\Procaptcha\Integrations\WordPress\WordPress;
use Io\Prosopo\Procaptcha\Plugin_Assets;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Assets_Loader;
use Io\Prosopo\Procaptcha\Widget\Procaptcha_Widget;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Integrations\Plugins\{Contact_Form_7};
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceConfig;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewTemplateRenderer;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\ViewsManager;
use Io\Prosopo\Procaptcha\Settings\{Account_Form_Settings,
	General\Active_Integrations\Active_Integrations_Tab,
	General\Tab\General_Settings_Tab,
	Procaptcha_Settings,
	Settings_Page,
	Statistics\Statistics_Settings_Tab
};

final class Procaptcha_Plugin {
	const PLUGIN_SLUG              = 'prosopo-procaptcha';
	const SERVICE_SCRIPT_URL       = 'https://js.prosopo.io/js/procaptcha.bundle.js';
	const ACCOUNT_API_ENDPOINT_URL = 'https://api.prosopo.io/sites/wp-details';
	const DOCS_URL_BASE            = 'https://docs.prosopo.io/en/wordpress-plugin';
	const SUPPORT_FORUM_URL        = 'https://wordpress.org/support/plugin/prosopo-procaptcha/';
	const TRANSLATIONS_FOLDER      = 'lang';
	const VIEWS_ROOT_DIR           = __DIR__;
	const VIEWS_ROOT_NAMESPACE     = 'Io\\Prosopo\\Procaptcha';

	private string $plugin_file;
	private Widget $widget;
	private Widget_Assets_Loader $widget_assets_manager;
	private Integrations_Loader $integrations_loader;
	private Settings_Page $settings_page;
	private Plugin_Assets $plugin_assets;
	private Account_Form_Settings $account_form_settings;
	private Procaptcha_Settings $procaptcha_settings;
	private ViewsManager $views_manager;
	/**
	 * @var Settings_Tab[]
	 */
	private array $standalone_settings_tabs;

	public function __construct( string $plugin_file, bool $is_dev_mode = false ) {
		$this->plugin_file = $plugin_file;

		$this->procaptcha_settings = new General_Settings_Tab();
		$this->views_manager       = $this->get_views_manager();
		$this->plugin_assets       = new Plugin_Assets(
			$this->plugin_file,
			$this->detect_current_version_number(),
			$is_dev_mode
		);

		$this->load_widget();

		$this->settings_page       = new Settings_Page(
			$this,
			$this->widget,
			$this->views_manager,
			$this->views_manager,
			$this->plugin_assets->get_resolver(),
			$this->plugin_assets->get_loader()
		);
		$this->integrations_loader = new Integrations_Loader( $this->settings_page );

		$this->load_settings_tabs();

		$this->load_integrations();
	}

	public function set_hooks(): void {
		$screen_detector = Screen_Detector_Base::load();

		$hookable = array(
			$this->settings_page,
			$this->widget_assets_manager,
			$this->plugin_assets,
			$this->integrations_loader,
		);

		foreach ( $hookable as $hookable_item ) {
			$hookable_item->set_hooks( $screen_detector );
		}

		add_action(
			'init',
			array( $this, 'load_translations' )
		);
	}

	public function clear_data(): void {
		$settings_tabs = array_merge(
			$this->standalone_settings_tabs,
			$this->integrations_loader->get_all_settings_tabs()
		);

		foreach ( $settings_tabs as $settings_tab ) {
			$settings_tab->clear_data();
		}
	}

	public function load_translations(): void {
		$translations_folder = sprintf(
			'%s/%s',
			dirname( $this->get_basename() ),
			self::TRANSLATIONS_FOLDER
		);

		load_plugin_textdomain(
			self::PLUGIN_SLUG,
			false,
			$translations_folder
		);
	}

	/**
	 * @return string prosopo-procaptcha/prosopo-procaptcha.php
	 */
	public function get_basename(): string {
		return plugin_basename( $this->plugin_file );
	}

	protected function get_views_manager(): ViewsManager {
		$view_template_renderer = new ViewTemplateRenderer();

		$namespace_config = ( new ViewNamespaceConfig( $view_template_renderer ) )
			->setTemplatesRootPath( self::VIEWS_ROOT_DIR )
			->setTemplateFileExtension( '.blade.php' )
			->setTemplateErrorHandler(
				function ( array $event_details ) {
					// todo log.
				}
			);

		$namespace_config
			->getModules()
			->setEventDispatcher( $view_template_renderer->getModules()->getEventDispatcher() );

		$views_manager = new ViewsManager();
		$views_manager->registerNamespace( self::VIEWS_ROOT_NAMESPACE, $namespace_config );

		return $views_manager;
	}

	protected function load_widget(): void {
		$this->widget_assets_manager = new Widget_Assets_Loader(
			self::SERVICE_SCRIPT_URL,
			'prosopo-procaptcha',
			$this->plugin_assets->get_loader(),
			$this->procaptcha_settings
		);

		$this->widget = new Procaptcha_Widget(
			$this->widget_assets_manager,
			$this->views_manager,
			$this->procaptcha_settings
		);
	}

	protected function load_settings_tabs(): void {
		$general_tab             = new General_Settings_Tab();
		$active_integrations_tab = new Active_Integrations_Tab( $this->integrations_loader );
		$statistics_tab          = new Statistics_Settings_Tab( $this->procaptcha_settings, $this->views_manager );

		$this->standalone_settings_tabs = array( $general_tab, $active_integrations_tab, $statistics_tab );

		$this->settings_page->add_tab(
			$general_tab,
			Settings_Page::TAB_POSITION_BEGIN
		);

		$this->settings_page->add_tab(
			$active_integrations_tab,
			Settings_Page::TAB_POSITION_END
		);

		$this->settings_page->add_tab(
			$statistics_tab,
			Settings_Page::TAB_POSITION_END
		);
	}

	protected function load_integrations(): void {
		$wordpress_integration = new WordPress( $this->widget );

		$this->account_form_settings = $wordpress_integration->get_account_form_settings();

		$this->integrations_loader->set_module_integrations( array( $wordpress_integration ) );
		$this->integrations_loader->set_plugin_integrations( $this->get_plugin_integrations() );
	}

	protected function detect_current_version_number(): string {
        // @phpcs:ignore
        $plugin_file_content = (string)file_get_contents($this->plugin_file);

		preg_match( '/Version:(.*)/', $plugin_file_content, $matches );

		$current_version_number = $matches[1] ?? '1.0.0';

		return trim( $current_version_number );
	}

	/**
	 * @return Plugin_Integration[]
	 */
	protected function get_plugin_integrations(): array {
		return array(
			new BBPress( $this->widget ),
			new Contact_Form_7( $this->widget ),
			new Elementor_Pro( $this->widget, $this->account_form_settings ),
			new Everest_Forms( $this->widget ),
			new Fluent_Forms( $this->widget ),
			new Formidable_Forms( $this->widget ),
			new Gravity_Forms( $this->widget ),
			new JetPack( $this->widget ),
			new Ninja_Forms( $this->widget ),
			new Spectra( $this->widget ),
			new User_Registration( $this->widget, $this->account_form_settings ),
			new WPForms( $this->widget ),
			new WooCommerce( $this->widget, $this->account_form_settings ),
			new Simple_Membership( $this->widget, $this->account_form_settings ),
			new Beaver_Builder( $this->widget, $this->account_form_settings ),
			new Memberpress( $this->widget, $this->account_form_settings ),
		);
	}
}
