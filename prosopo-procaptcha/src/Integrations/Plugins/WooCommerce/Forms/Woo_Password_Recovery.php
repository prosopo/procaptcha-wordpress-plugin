<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Woo_Password_Recovery extends Widget_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'woocommerce_lostpassword_form',
			function () {
				$this->widget->print_form_field(
					array(
						Widget_Settings::ELEMENT_ATTRIBUTES => array(
							'style' => 'margin:0 0 10px',
						),
					)
				);
			}
		);

		// validation happens in the WordPress/Lost_Password_Form class.
	}
}
