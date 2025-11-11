<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Everest_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Utils\Hookable;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;

final class Everest_Field_Integration implements Hookable {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter(
			'everest_forms_fields',
			/**
			 * @param string[] $fields
			 *
			 * @return string[]
			 */
			fn( array $fields ): array => array_merge(
				$fields,
				array(
					Everest_Field::class,
				)
			)
		);
	}
}
