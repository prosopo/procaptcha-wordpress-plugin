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
	const TYPE                      = 'type';
	const IS_ENABLED_FOR_AUTHORIZED = 'is_enabled_for_authorized';

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
				$general_settings_tab->form = parent::make_tab_component( $factory, $widget );

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

	public function get_type(): string {
		return string( $this->get_settings(), self::TYPE );
	}

	public function should_bypass_authorized_user(): bool {
		$is_enabled_for_authorized = bool( $this->get_settings(), self::IS_ENABLED_FOR_AUTHORIZED );

		return ! $is_enabled_for_authorized;
	}

	protected function get_option_name(): string {
		// For back compatibility.
		return self::OPTION_BASE;
	}

	protected function get_string_settings(): array {
		return array(
			self::SECRET_KEY => __( 'Your Secret Key:', 'prosopo-procaptcha' ),
			self::SITE_KEY   => __( 'Your Site Key:', 'prosopo-procaptcha' ),
			self::THEME      => __( 'Theme:', 'prosopo-procaptcha' ),
			self::TYPE       => __( 'Type:', 'prosopo-procaptcha' ),
		);
	}

	protected function get_bool_settings(): array {
		return array(
			self::IS_ENABLED_FOR_AUTHORIZED => __( 'Require from authorized users:', 'prosopo-procaptcha' ),
		);
	}

	protected function get_default_values(): array {
		return array(
			self::THEME => 'light',
			self::TYPE  => 'frictionless',
		);
	}

	protected function get_select_inputs(): array {
		return array(
			self::THEME => array(
				'dark'  => __( 'Dark', 'prosopo-procaptcha' ),
				'light' => __( 'Light', 'prosopo-procaptcha' ),
			),
			self::TYPE  => array(
				'frictionless' => __( 'Frictionless', 'prosopo-procaptcha' ),
				'image'        => __( 'Image', 'prosopo-procaptcha' ),
				'pow'          => __( 'Pow', 'prosopo-procaptcha' ),
			),
		);
	}

	protected function get_password_inputs(): array {
		return array( self::SECRET_KEY );
	}

	protected function get_checkboxes_title(): string {
		return __( 'Behavior', 'prosopo-procaptcha' );
	}
}
