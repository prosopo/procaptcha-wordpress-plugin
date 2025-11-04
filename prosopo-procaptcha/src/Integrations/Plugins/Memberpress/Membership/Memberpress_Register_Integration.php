<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Membership;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Integrations\Plugins\Memberpress\Membership\Memberpress_Membership;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Memberpress_Register_Integration extends Widget_Integration {
	private Memberpress_Membership $membership;

	public function __construct( Widget $widget ) {
		parent::__construct( $widget );

		$this->membership = new Memberpress_Membership( $this->widget->get_field_name() );
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			'mepr-product-registration-metabox',
			fn() => $this->membership->print_protection_setting( (int) get_the_ID() )
		);

		add_action(
			'mepr-membership-save-meta',
			fn()=>$this->membership->save_protection_setting( (int) get_the_ID() )
		);

		add_action(
			'mepr-checkout-before-submit',
			fn ( int $membership_id ) =>$this->print_widget_field( $membership_id )
		);

		add_filter(
			'mepr-validate-signup',
			/**
			 * @param string[] $errors
			 */
			function ( array $errors ): array {
				$membership_id = Query_Arguments::get_non_action_int(
					'mepr_product_id',
					Query_Arguments::POST
				);

				if ( $this->is_valid_submission( $membership_id ) ) {
					return $errors;
				}

				return array_merge(
					$errors,
					array( $this->widget->get_validation_error_message() )
				);
			}
		);
	}

	protected function print_widget_field( int $membership_id ): void {
		$widget = $this->widget;

		if ( $widget->is_protection_enabled() &&
			$this->membership->is_protected( $membership_id ) ) {
			$widget->print_form_field(
				array(
					Widget_Settings::ELEMENT_ATTRIBUTES => array(
						'style' => 'margin:0 0 10px',
					),
				)
			);
		}
	}

	protected function is_valid_submission( int $membership_id ): bool {
		$widget = $this->widget;

		if ( $widget->is_protection_enabled() &&
			$this->membership->is_protected( $membership_id ) ) {
			return $widget->is_verification_token_valid();
		}

		return true;
	}
}
