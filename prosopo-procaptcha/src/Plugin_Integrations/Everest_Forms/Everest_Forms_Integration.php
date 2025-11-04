<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Everest_Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\About_Integration;
use Io\Prosopo\Procaptcha\Integration\Plugin\Plugin_Integration_Base;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;

final class Everest_Forms_Integration extends Plugin_Integration_Base {
	public function get_about(): About_Integration {
		$about = new About_Integration();

		$about->name     = 'Everest Forms';
		$about->docs_url = self::get_docs_url( 'everest-forms' );

		return $about;
	}

	public function is_active(): bool {
		return class_exists( 'EverestForms' );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		parent::set_hooks( $screen_detector );

		add_filter(
			'everest_forms_fields',
			/**
			 * @param string[] $fields
			 *
			 * @return string[]
			 */
			fn( array $fields )=>array_merge(
				$fields,
				array(
					Everest_Forms_Form_Integration::class,
				)
			)
		);
	}

	protected function get_external_integrations(): array {
		return array(
			Everest_Forms_Form_Integration::class,
		);
	}
}
