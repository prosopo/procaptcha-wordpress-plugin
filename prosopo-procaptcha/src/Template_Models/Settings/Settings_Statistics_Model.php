<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Template_Models\Settings;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

defined( 'ABSPATH' ) || exit;

class Settings_Statistics_Model extends BaseTemplateModel {
	public bool $is_available;
}
