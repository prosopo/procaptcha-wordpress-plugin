<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Templates\Settings;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class Settings_General_Tab extends BaseTemplateModel {
	public TemplateModelInterface $form;
	public string $preview;
}
