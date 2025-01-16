<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Everest_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Plugin\Procaptcha_Plugin_Integration;

class Everest_Forms_Integration extends Procaptcha_Plugin_Integration implements Hookable {
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

	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'everest_forms_fields', array( $this, 'register_field' ) );
	}

	public function get_target_plugin_classes(): array {
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
