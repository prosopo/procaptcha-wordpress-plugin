<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\Active_Integrations;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Module\About_Module_Integration;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;

final class Active_Integrations_List extends BaseTemplateModel {
	/**
	 * @var About_Module_Integration[]
	 */
	public array $active_integrations;
}
