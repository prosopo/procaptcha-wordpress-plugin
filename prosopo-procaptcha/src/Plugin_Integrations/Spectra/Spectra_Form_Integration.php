<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Spectra;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use WP_Post;

final class Spectra_Form_Integration extends Hookable_Form_Integration_Base {
	private Spectra_Form $spectra_form;
	private string $stub_form_input_name;

	public function construct(): void {
		$this->spectra_form         = new Spectra_Form();
		$this->stub_form_input_name = self::get_widget()->get_field_name();
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter(
			'render_block_uagb/forms',
			array( $this, 'integrate_widget_field_into_form_content' )
		);

		// With the low priority to be process before the primary handler.
		add_action( 'wp_ajax_uagb_process_forms', array( $this, 'trigger_verification_for_protected_form_submission' ), -999 );
		add_action(
			'wp_ajax_nopriv_uagb_process_forms',
			array(
				$this,
				'trigger_verification_for_protected_form_submission',
			),
			-999
		);
	}

	public function integrate_widget_field_into_form_content( string $form_content ): string {
		if ( $this->spectra_form->is_input_in_form( $this->stub_form_input_name, $form_content ) ) {
			$form_content = $this->replace_stub_form_input_with_widget_field( $form_content );
		}

		return $form_content;
	}

	public function trigger_verification_for_protected_form_submission(): void {
		$widget = self::get_widget();

		if ( $widget->is_protection_enabled() &&
			$this->is_protected_form_submission() ) {
			$this->verify_form_submission();
		}
	}

	protected function replace_stub_form_input_with_widget_field( string $form_content ): string {
		$widget = self::get_widget();

		$widget_element = $widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES      => array(
					'style' => 'margin: 0 0 20px',
				),
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					'class' => 'uagb-forms-hidden-input',
					'id'    => 'hidden',
					'name'  => $this->stub_form_input_name,
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
				Widget_Settings::IS_RETURN_ONLY          => true,
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);

		return $this->spectra_form->replace_input_in_form(
			$this->stub_form_input_name,
			$widget_element,
			$form_content
		);
	}

	protected function is_protected_form_submission(): bool {
		$post_id  = Query_Arguments::get_non_action_int( 'post_id', Query_Arguments::POST );
		$block_id = Query_Arguments::get_non_action_string( 'block_id', Query_Arguments::POST );

		return get_post( $post_id ) instanceof WP_Post &&
			strlen( $block_id ) > 0 &&
			$this->spectra_form->is_hidden_input_in_form_block( $this->stub_form_input_name, $post_id, $block_id );
	}

	protected function verify_form_submission(): void {
		$widget = self::get_widget();

		$token_field_name  = $widget->get_field_name();
		$token_field_value = $this->spectra_form->get_submitted_form_field( $token_field_name );

		$is_submission_verified = is_string( $token_field_value ) &&
			$widget->is_verification_token_valid( $token_field_value );

		if ( $is_submission_verified ) {
			return;
		}

		// Spectra doesn't process error messages from the backend.
		wp_send_json_error( 400 );
	}
}
