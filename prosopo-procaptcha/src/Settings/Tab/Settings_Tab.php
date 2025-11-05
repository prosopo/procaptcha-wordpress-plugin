<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Tab;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Io\Prosopo\Procaptcha\Widget\Widget;

interface Settings_Tab {
	/**
	 * @return array<string,mixed>
	 */
	public function get_settings(): array;

	public function process_form(): void;

	public function get_tab_name(): string;

	public function get_tab_title(): string;

	public function make_tab_component( ModelFactoryInterface $factory, Widget $widget ): TemplateModelInterface;

	public function get_tab_script_asset(): string;

	public function get_style_asset(): string;

	/**
	 * @return array<string,mixed>
	 */
	public function get_tab_js_data(): array;

	public function clear_data(): void;
}
