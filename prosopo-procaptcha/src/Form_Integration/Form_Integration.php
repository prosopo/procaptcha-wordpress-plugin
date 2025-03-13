<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Form_Integration;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Form_Integration\Helper\Form_Integration_Helper;

interface Form_Integration {
	public static function set_form_helper( Form_Integration_Helper $form_helper ): void;

	public static function get_form_helper(): Form_Integration_Helper;
}
