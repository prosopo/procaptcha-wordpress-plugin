<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\General\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

class Settings_Form extends BaseTemplateModel {
	public string $nonce;
	public string $tab_name;
	/**
	 * Columns of cards, each card holding a title, description, inputs and checkboxes.
	 *
	 * @var array<int,array<int,array<string,mixed>>>
	 */
	public array $columns;
}
