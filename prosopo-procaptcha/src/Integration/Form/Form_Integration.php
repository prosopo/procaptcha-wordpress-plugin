<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Helper_Interface;

/**
 * Trait, instead of the abstract class,
 * form integrations inherit field classes from plugins, and instances may be created dynamically.
 */
trait Form_Integration {
	private static Form_Helper_Interface $form_helper;

	public static function set_form_helper( Form_Helper_Interface $form_helper ): void {
		self::$form_helper = $form_helper;
	}

	public static function get_form_helper(): Form_Helper_Interface {
		return self::$form_helper;
	}
}
