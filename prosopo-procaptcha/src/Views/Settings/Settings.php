<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Views\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Collection;
use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;
use Io\Prosopo\Procaptcha\View\View;

class Settings extends View {
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
	public View_Interface $tab_content;
}
