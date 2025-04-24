<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integration\Form;

use Io\Prosopo\Procaptcha\Widget\Widget;

defined( 'ABSPATH' ) || exit;

/**
 * Trait, instead of the abstract class,
 * form integrations inherit field classes from plugins, and instances may be created dynamically.
 */
trait Widget_Container {
	private static Widget $widget;

	public static function set_widget( Widget $widget ): void {
		self::$widget = $widget;
	}

	public static function get_widget(): Widget {
		return self::$widget;
	}
}
