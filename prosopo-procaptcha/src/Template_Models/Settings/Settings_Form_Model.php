<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Template_Models\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Collection;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

class Settings_Form_Model extends BaseTemplateModel {
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
