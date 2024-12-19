<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Template_Models\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Collection;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class Settings extends BaseTemplateModel {
	public string $css;
	public string $js_file;
	/**
	 * @var array<string,mixed>
	 */
	public array $js_data;
	public bool $is_just_saved;
	/**
	 * @var Collection[]
	 */
	public array $tabs;
	public string $current_tab;
	public TemplateModelInterface $tab_content;
}
