<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\WooCommerce\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Woo_Lost_Password_Form_Integration extends Widget_Integration {
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
