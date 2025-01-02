<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Spectra;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Definition\Settings\Settings_Storage;
use Io\Prosopo\Procaptcha\Integration\Plugin\Captcha_Plugin_Integration;

class Spectra extends Captcha_Plugin_Integration {
	public function get_target_plugin_classes(): array {
		return array( 'UAGB_Block' );
	}

	public function get_form_integrations( Settings_Storage $settings_storage ): array {
		return array(
			Spectra_Form_Block_FieldBase::class,
		);
	}
}
