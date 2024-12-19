<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Views\Settings;

use Io\Prosopo\Procaptcha\Interfaces\View\View_Interface;
use Io\Prosopo\Procaptcha\View\View;

defined( 'ABSPATH' ) || exit;

class Settings_General_Tab extends View {
	public View_Interface $form;
	public string $preview;
}
