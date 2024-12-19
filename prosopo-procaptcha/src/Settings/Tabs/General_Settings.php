<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Tabs;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Interface;
use Io\Prosopo\Procaptcha\Settings\Settings_Tab;
use Io\Prosopo\Procaptcha\Template_Models\Settings\Settings_General_Tab_Model;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class General_Settings extends Settings_Tab {
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

	public function make_tab_component( ModelFactoryInterface $factory, Captcha_Interface $captcha ): TemplateModelInterface {
		return $factory->createModel(
			Settings_General_Tab_Model::class,
			function ( Settings_General_Tab_Model $settings_general_tab ) use ( $factory, $captcha ) {
				$settings_general_tab->form = parent::make_tab_component( $factory, $captcha );

				$settings_general_tab->preview = $captcha->print_form_field(
					array(
						Widget_Arguments::IS_RETURN_ONLY => true,
					)
				);
			}
		);
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
