<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Everest_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Plugin_Integration\Plugin_Integration_Base;

class Everest_Forms_Integration extends Plugin_Integration_Base implements Hookable {
	/**
	 * @param string[] $fields
	 *
	 * @return string[]
	 */
	public function register_field( array $fields ): array {
		return array_merge(
			$fields,
			array(
				Everest_Forms_Form_Integration::class,
			)
		);
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'everest_forms_fields', array( $this, 'register_field' ) );
	}

	public function get_vendor_classes(): array {
		return array(
			'EverestForms',
		);
	}

	protected function get_form_integrations(): array {
		return array(
			Everest_Forms_Form_Integration::class,
		);
	}
}
