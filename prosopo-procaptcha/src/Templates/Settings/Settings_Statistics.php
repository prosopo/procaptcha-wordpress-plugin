<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Templates\Settings;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

defined( 'ABSPATH' ) || exit;

class Settings_Statistics extends BaseTemplateModel {
	public bool $is_available;
}
