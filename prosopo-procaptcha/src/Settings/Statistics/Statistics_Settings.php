<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Statistics;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

defined( 'ABSPATH' ) || exit;

class Statistics_Settings extends BaseTemplateModel {
	public bool $is_available;
}
