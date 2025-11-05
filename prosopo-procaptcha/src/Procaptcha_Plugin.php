<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration;
use Io\Prosopo\Procaptcha\Integrations\Integrations_Loader;
use Io\Prosopo\Procaptcha\Integrations\Plugins\BBPress\BBPress_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Beaver_Builder\Beaver_Builder_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro\Elementor_Pro_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Everest_Forms\Everest_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Fluent_Forms\Fluent_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Formidable_Forms\Formidable_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Gravity_Forms\Gravity_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\JetPack\JetPack_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Memberpress_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Ninja_Forms\Ninja_Forms_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Simple_Membership\Simple_Membership_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Spectra\Spectra_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration\User_Registration_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\WooCommerce_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\WPForms\WPForms_Integration;
use Io\Prosopo\Procaptcha\Integrations\WordPress\WordPress_Integration;
use Io\Prosopo\Procaptcha\Plugin_Assets;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Assets_Loader;
use Io\Prosopo\Procaptcha\Widget\Procaptcha_Widget;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Integrations\Plugins\{Contact_Form_7_Integration};
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewNamespaceConfig;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\View\ViewTemplateRenderer;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\ViewsManager;
use Io\Prosopo\Procaptcha\Settings\{Account_Form_Settings,
	Compatible_Plugins\Compatible_Plugins_Tab,
	General\General_Settings_Tab,
	Procaptcha_Settings,
	Settings_Page,
	Statistics\Statistics_Settings_Tab
};

final class Procaptcha_Plugin {
	const PLUGIN_SLUG              = 'prosopo-procaptcha';
	const SERVICE_SCRIPT_URL       = 'https://js.prosopo.io/js/procaptcha.bundle.js';
	const ACCOUNT_API_ENDPOINT_URL = 'https://api.prosopo.io/sites/wp-details';
	const DOCS_URL_BASE            = 'https://docs.prosopo.io/en/wordpress-plugin';
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

		$this->views_manager = $this->get_views_manager();

		$this->procaptcha_settings = new General_Settings_Tab();

		$this->plugin_assets = new Plugin_Assets(
			$this->plugin_file,
			$this->detect_current_version_number(),
			$is_dev_mode
		);

		$this->load_widget();

		$this->load_settings_page();

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

	protected function load_settings_page(): void {
		$this->settings_page = new Settings_Page(
			$this,
			$this->widget,
			$this->views_manager,
			$this->views_manager,
			$this->plugin_assets->get_resolver(),
			$this->plugin_assets->get_loader()
		);

		$this->standalone_settings_tabs = $this->get_standalone_settings_tabs();

		foreach ( $this->standalone_settings_tabs as $standalone_settings_tab ) {
			$this->settings_page->add_tab( $standalone_settings_tab );
		}
	}

	protected function load_integrations(): void {
		$wordpress_integration = new WordPress_Integration( $this->widget );

		$this->account_form_settings = $wordpress_integration->get_account_form_settings();
		$this->integrations_loader   = new Integrations_Loader( $this->settings_page );

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
			new BBPress_Integration( $this->widget ),
			new Contact_Form_7_Integration( $this->widget ),
			new Elementor_Pro_Integration( $this->widget, $this->account_form_settings ),
			new Everest_Forms_Integration( $this->widget ),
			new Fluent_Forms_Integration( $this->widget ),
			new Formidable_Forms_Integration( $this->widget ),
			new Gravity_Forms_Integration( $this->widget ),
			new JetPack_Integration( $this->widget ),
			new Ninja_Forms_Integration( $this->widget ),
			new Spectra_Integration( $this->widget ),
			new User_Registration_Integration( $this->widget, $this->account_form_settings ),
			new WPForms_Integration( $this->widget ),
			new WooCommerce_Integration( $this->widget, $this->account_form_settings ),
			new Simple_Membership_Integration( $this->widget, $this->account_form_settings ),
			new Beaver_Builder_Integration( $this->widget, $this->account_form_settings ),
			new Memberpress_Integration( $this->widget, $this->account_form_settings ),
		);
	}

	/**
	 * @return Settings_Tab[]
	 */
	protected function get_standalone_settings_tabs(): array {
		return array(
			new General_Settings_Tab(),
			new Statistics_Settings_Tab( $this->procaptcha_settings, $this->views_manager ),
			new Compatible_Plugins_Tab(),
		);
	}
}
