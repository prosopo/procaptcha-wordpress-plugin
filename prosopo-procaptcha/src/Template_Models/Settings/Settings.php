<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Template_Models\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class Settings extends BaseTemplateModel {
	public string $css;
	public bool $is_just_saved;
	/**
	 * @var array<int,mixed>
	 */
	public array $tabs;
	public string $current_tab;
	public TemplateModelInterface $tab_content;
}
