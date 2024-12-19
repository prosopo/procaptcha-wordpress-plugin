<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Collection;
use Io\Prosopo\Procaptcha\Interfaces\Captcha\Captcha_Interface;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;

interface Settings_Tab_Interface {
	public function get_settings(): Collection;

	public function process_form( Query_Arguments $query_arguments ): void;

	public function get_tab_name(): string;

	public function get_tab_title(): string;

	public function make_tab_component( ModelFactoryInterface $factory, Captcha_Interface $captcha ): TemplateModelInterface;

	public function get_tab_js_file(): string;

	public function get_tab_css_file(): string;

	/**
	 * @return array<string,mixed>
	 */
	public function get_tab_js_data( Settings_Storage_Interface $settings_storage ): array;

	public function clear_data(): void;
}
