<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integration\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Widget\Widget;

interface Form_Integration {
	public static function set_widget( Widget $widget ): void;

	public static function get_widget(): Widget;
}
