<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\General\Tab;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Procaptcha_Plugin;
use Io\Prosopo\Procaptcha\Settings\Procaptcha_Settings;
use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab_Base;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

final class General_Settings_Tab extends Settings_Tab_Base implements Procaptcha_Settings {
	const SITE_KEY                  = 'site_key';
	const SECRET_KEY                = 'secret_key';
	const THEME                     = 'theme';
	const WIDGET_LANGUAGE           = 'widget_language';
	const IS_ENABLED_FOR_AUTHORIZED = 'is_enabled_for_authorized';
	const VISITOR_IP_MATCHING       = 'visitor_ip_matching';

	const LANGUAGE_SITE    = 'site';
	const LANGUAGE_VISITOR = 'visitor';

	public function get_tab_title(): string {
		return __( 'General', 'prosopo-procaptcha' );
	}

	public function get_tab_name(): string {
		return 'general';
	}

	public function make_tab_component( ModelFactoryInterface $factory, Widget $widget ): TemplateModelInterface {
		return $factory->createModel(
			General_Settings::class,
			function ( General_Settings $general_settings_tab ) use ( $factory, $widget ) {
				$general_settings_tab->form     = parent::make_tab_component( $factory, $widget );
				$general_settings_tab->has_keys = '' !== $this->get_site_key() && '' !== $this->get_secret_key();

				$general_settings_tab->widget_preview = $widget->print_form_field(
					array(
						Widget_Settings::IS_RETURN_ONLY => true,
					)
				);
			}
		);
	}

	public function get_tab_script_asset(): string {
		return 'settings/general/general-settings.min.js';
	}

	public function get_tab_js_data(): array {
		return array(
			'accountApiEndpoint' => Procaptcha_Plugin::ACCOUNT_API_ENDPOINT_URL,
			'secretKey'          => $this->get_secret_key(),
			'siteKey'            => $this->get_site_key(),
		);
	}

	public function get_site_key(): string {
		return string( $this->get_settings(), self::SITE_KEY );
	}

	public function get_secret_key(): string {
		return string( $this->get_settings(), self::SECRET_KEY );
	}

	public function get_theme(): string {
		return string( $this->get_settings(), self::THEME );
	}

	public function get_language(): string {
		if ( self::LANGUAGE_VISITOR === string( $this->get_settings(), self::WIDGET_LANGUAGE ) ) {
			return '';
		}

		// The widget takes BCP 47 tags (pt-BR), WordPress locales use an underscore (pt_BR).
		return str_replace( '_', '-', determine_locale() );
	}

	public function should_bypass_authorized_user(): bool {
		$is_enabled_for_authorized = bool( $this->get_settings(), self::IS_ENABLED_FOR_AUTHORIZED );

		return ! $is_enabled_for_authorized;
	}

	public function get_visitor_ip_source(): string {
		$source = string( $this->get_settings(), self::VISITOR_IP_MATCHING );

		$sources = array(
			self::VISITOR_IP_CLOUDFLARE,
			self::VISITOR_IP_X_FORWARDED_FOR,
			self::VISITOR_IP_REMOTE_ADDR,
		);

		return in_array( $source, $sources, true ) ?
			$source :
			'';
	}

	protected function get_option_name(): string {
		// For back compatibility.
		return self::OPTION_BASE;
	}

	protected function get_string_settings(): array {
		return array(
			self::SECRET_KEY          => __( 'Your Secret Key:', 'prosopo-procaptcha' ),
			self::SITE_KEY            => __( 'Your Site Key:', 'prosopo-procaptcha' ),
			self::THEME               => __( 'Theme:', 'prosopo-procaptcha' ),
			self::VISITOR_IP_MATCHING => __( 'Visitor IP matching:', 'prosopo-procaptcha' ),
			self::WIDGET_LANGUAGE     => __( 'Language:', 'prosopo-procaptcha' ),
		);
	}

	protected function get_bool_settings(): array {
		return array(
			self::IS_ENABLED_FOR_AUTHORIZED => __( 'Require from authorized users:', 'prosopo-procaptcha' ),
		);
	}

	protected function get_default_values(): array {
		return array(
			self::THEME               => 'light',
			// Off, as most sites are behind a proxy such as Cloudflare: matching the wrong IP rejects real visitors.
			self::VISITOR_IP_MATCHING => self::VISITOR_IP_OFF,
			self::WIDGET_LANGUAGE     => self::LANGUAGE_SITE,
		);
	}

	protected function get_select_inputs(): array {
		return array(
			self::THEME               => array(
				'dark'  => __( 'Dark', 'prosopo-procaptcha' ),
				'light' => __( 'Light', 'prosopo-procaptcha' ),
			),
			// phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys.IncorrectKeyOrder -- Off first, as it's the default.
			self::VISITOR_IP_MATCHING => array(
				self::VISITOR_IP_OFF             => __( 'Off', 'prosopo-procaptcha' ),
				self::VISITOR_IP_CLOUDFLARE      => __( 'On - site is behind Cloudflare', 'prosopo-procaptcha' ),
				self::VISITOR_IP_REMOTE_ADDR     => __( 'On - visitors connect directly (REMOTE_ADDR)', 'prosopo-procaptcha' ),
				self::VISITOR_IP_X_FORWARDED_FOR => __( 'On - site is behind another proxy (X-Forwarded-For)', 'prosopo-procaptcha' ),
			),
			self::WIDGET_LANGUAGE     => array(
				self::LANGUAGE_SITE    => __( 'Same as the site', 'prosopo-procaptcha' ),
				self::LANGUAGE_VISITOR => __( "Visitor's browser language", 'prosopo-procaptcha' ),
			),
		);
	}

	protected function get_input_descriptions(): array {
		return array(
			self::VISITOR_IP_MATCHING => __( 'Only has an effect when IP validation is turned on for your site key in the Prosopo portal (Professional plan and above). Pick where your server sees the visitor\'s real IP: the wrong source makes IP validation reject real visitors.', 'prosopo-procaptcha' ),
		);
	}

	protected function get_password_inputs(): array {
		return array( self::SECRET_KEY );
	}

	protected function get_checkboxes_title(): string {
		return __( 'Behavior', 'prosopo-procaptcha' );
	}
}
