<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\WordPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

// Used as independent part of other integrations, e.g. JetPack Gutenberg Forms.
final class WP_Shortcode_Integration extends Widget_Integration {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_shortcode(
			$this->widget->get_field_name(),
			fn ()=>$this->widget->print_form_field(
				array(
					Widget_Settings::IS_DESIRED_ON_GUESTS => true,
					Widget_Settings::IS_RETURN_ONLY       => true,
				)
			)
		);
	}
}
