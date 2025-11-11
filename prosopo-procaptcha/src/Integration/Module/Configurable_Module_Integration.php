<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Module;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\Tab\Settings_Tab;

interface Configurable_Module_Integration extends Module_Integration {
	public function get_settings_tab(): Settings_Tab;
}
