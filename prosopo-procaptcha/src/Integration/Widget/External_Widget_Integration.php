<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Widget;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Widget\Widget;

interface External_Widget_Integration {
	public static function set_widget( Widget $widget ): void;
}
