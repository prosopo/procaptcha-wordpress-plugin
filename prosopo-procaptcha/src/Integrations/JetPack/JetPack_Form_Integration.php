<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\JetPack;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Forms\ContactForm\Contact_Form;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable\Hookable_Form_Integration_Base;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\object;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class JetPack_Form_Integration extends Hookable_Form_Integration_Base {
	protected array $validated_form_ids = array();

	public function set_hooks( bool $is_admin_area ): void {
		// fixme add_filter( 'jetpack_contact_form_is_spam', array( $this, 'validate_submitted_form' ) );
		// fixme add_filter( 'do_shortcode_tag', array( $this, 'trigger_validation_for_rendering_form' ), 10, 2 );
	}

	/**
	 * @template ValidationResponse WP_Error|bool
	 *
	 * @param ValidationResponse $validation_response
	 * @return ValidationResponse
	 */
	public function validate_submitted_form( $validation_response ) {
		$submitted_form = $this->get_submitted_form();

		if ( null === $submitted_form ) {
			// todo log. It would mean breaking changes in the JetPack codebase.
			return $validation_response;
		}

		// adding an error to the form instance here will have no effect on the error displaying.

		// in addition, we can't save either form id or form hash at this stage,
		// as for some odd reason they'll have changed.

		$widget = self::get_form_helper()->get_widget();

		return $this->is_invalid_form_submission( $submitted_form ) ?
			$widget->add_validation_error() :
			$validation_response;
	}

	public function trigger_validation_for_rendering_form( string $shortcode_output, string $shortcode_name ): string {
		if ( 'contact-field' === $shortcode_name ) {
			$current_form = Contact_Form::$current_form;

			// todo log if form is missing.

			if ( $current_form instanceof Contact_Form ) {
				$form_id = string( $current_form->attributes, 'id' );

				if ( ! in_array( $form_id, $this->validated_form_ids, true ) ) {
					$this->validated_form_ids[] = $form_id;
					$this->validate_form( $current_form );
				}
			}
		}

		return $shortcode_output;
	}

	protected function get_submitted_form(): ?Contact_Form {
		$query_arguments = self::get_form_helper()->get_query_arguments();
		$current_action  = $query_arguments->get_string_for_non_action( 'action', 'post' );

		if ( 'grunion-contact-form' !== $current_action ) {
			return null;
		}

		$submitted_form_hash = $query_arguments->get_string_for_non_action( 'contact-form-hash', 'post' );

		$submitted_form = object( Contact_Form::$forms, $submitted_form_hash );

		return $submitted_form instanceof Contact_Form ?
			$submitted_form :
			null;
	}

	protected function is_invalid_form_submission( Contact_Form $submitted_form ): bool {
		$widget = self::get_form_helper()->get_widget();

		return $this->is_protected_form( $submitted_form ) &&
			! $widget->is_human_made_request();
	}

	protected function is_protected_form( Contact_Form $form ): bool {
		$widget       = self::get_form_helper()->get_widget();
		$form_content = string( $form, 'content' );

		return false !== strpos( $form_content, '[' . $widget->get_field_name() );
	}

	protected function validate_form( Contact_Form $form ): void {
		// we can detect only if any form was submitted, but can't confirm if it's exactly the current one.
		// For some reason, $form->attributes['id'] and ->hash of the submitted form and the current form always different,
		// even when they're the same.
		$is_submitted_form = $this->get_submitted_form() instanceof Contact_Form;

		if ( $is_submitted_form &&
		$this->is_invalid_form_submission( $form ) ) {
			$widget       = self::get_form_helper()->get_widget();
			$form->errors = $widget->add_validation_error( $form->errors );
		}
	}
}
