<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\General;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class General_Settings extends BaseTemplateModel {
	public TemplateModelInterface $form;
	public string $preview;
}
