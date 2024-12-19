<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Views\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Collection;
use Io\Prosopo\Procaptcha\View\View;

class Settings_Form extends View {
	public string $nonce;
	public string $tab_name;
	/**
	 * @var Collection[]
	 */
	public array $inputs;
	/**
	 * @var Collection[]
	 */
	public array $checkboxes;
	public string $inputs_title;
	public string $checkboxes_title;
}
