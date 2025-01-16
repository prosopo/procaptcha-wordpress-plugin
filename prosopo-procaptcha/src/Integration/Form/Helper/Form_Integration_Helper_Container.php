<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Trait, instead of the abstract class,
 * form integrations inherit field classes from plugins, and instances may be created dynamically.
 */
trait Form_Integration_Helper_Container {
	private static Form_Integration_Helper $form_helper;

	public static function set_form_helper( Form_Integration_Helper $form_helper ): void {
		self::$form_helper = $form_helper;
	}

	public static function get_form_helper(): Form_Integration_Helper {
		return self::$form_helper;
	}
}
