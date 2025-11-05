<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Settings\General\Tab;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\General\Upgrade_Tier\Upgrade_Tier_Banner;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\BaseTemplateModel;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class General_Settings extends BaseTemplateModel {
	public TemplateModelInterface $form;
	public string $widget_preview;
	public Upgrade_Tier_Banner $tier_upgrade_banner;
}
