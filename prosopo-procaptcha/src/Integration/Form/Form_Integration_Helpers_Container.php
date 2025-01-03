<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integration\Form;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Interfaces\Integration\Form\Form_Integration_Helpers;

/**
 * Trait, instead of the abstract class,
 * form integrations inherit field classes from plugins, and instances may be created dynamically.
 */
trait Form_Integration_Helpers_Container {
	private static Form_Integration_Helpers $form_helper;

	public static function set_form_helpers( Form_Integration_Helpers $form_helper ): void {
		self::$form_helper = $form_helper;
	}

	public static function get_form_helpers(): Form_Integration_Helpers {
		return self::$form_helper;
	}
}
