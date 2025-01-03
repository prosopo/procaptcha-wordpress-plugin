<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Interfaces\Integration\Form;

defined( 'ABSPATH' ) || exit;

interface Form_Integration {
	public static function set_form_helpers( Form_Integration_Helpers $form_helper ): void;

	public static function get_form_helpers(): Form_Integration_Helpers;
}
