<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Template_Models\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

class Settings_Form extends BaseTemplateModel {
	public string $nonce;
	public string $tab_name;
	/**
	 * @var array<int,mixed>
	 */
	public array $inputs;
	/**
	 * @var array<int,mixed>
	 */
	public array $checkboxes;
	public string $inputs_title;
	public string $checkboxes_title;
}
