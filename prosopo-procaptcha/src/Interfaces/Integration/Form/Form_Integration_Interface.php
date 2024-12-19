<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Integration\Form;

defined( 'ABSPATH' ) || exit;

interface Form_Integration_Interface {
	public static function set_form_helper( Form_Helper_Interface $form_helper ): void;

	public static function get_form_helper(): Form_Helper_Interface;
}
