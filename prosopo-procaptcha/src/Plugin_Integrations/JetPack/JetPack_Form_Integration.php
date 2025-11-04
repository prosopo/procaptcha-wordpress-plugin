<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\JetPack;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Forms\ContactForm\Contact_Form;
use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration;
use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use WP_Error;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\object;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

final class JetPack_Form_Integration extends Widget_Integration {
	/**
	 * @var array<int,string>
	 */
	private array $validated_form_ids = array();

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'jetpack_contact_form_is_spam', array( $this, 'is_spam_submission' ) );
		add_filter( 'do_shortcode_tag', array( $this, 'run_validation_for_rendering_form' ), 10, 2 );
	}

	/**
	 * @param bool|WP_Error $current_spam_status
	 * @return bool|WP_Error
	 */
	public function is_spam_submission( $current_spam_status ) {
		$submitted_form = $this->get_submitted_form();
		$widget         = $this->widget;

		// adding an error to the form instance here will have no effect on the error displaying.

		// in addition, we can't save either form id or form hash at this stage,
		// as for some odd reason they'll have changed.

		// returning WP_Error will outright abort form processing.
		return $widget->is_protection_enabled() &&
		$submitted_form instanceof Contact_Form &&
		$this->is_form_submission_unverified( $submitted_form ) ?
			$widget->get_validation_error() :
			$current_spam_status;
	}

	public function run_validation_for_rendering_form( string $shortcode_content, string $shortcode_name ): string {
		if ( 'contact-field' === $shortcode_name ) {
			$this->run_validation_on_current_form();
		}

		return $shortcode_content;
	}

	protected function run_validation_on_current_form(): void {
		/**
		 * @var Contact_Form|null $rendering_form
		 */
		$rendering_form = Contact_Form::$current_form;

		if ( null === $rendering_form ) {
			// todo log. It means breaking changes in the JetPack codebase.
			return;
		}

		$this->run_validation_once_per_form( $rendering_form );
	}

	protected function get_submitted_form(): ?Contact_Form {
		$current_action      = Query_Arguments::get_non_action_string( 'action', 'post' );
		$submitted_form_hash = Query_Arguments::get_non_action_string( 'contact-form-hash', 'post' );

		return 'grunion-contact-form' === $current_action ?
			$this->get_form_by_hash( $submitted_form_hash ) :
			null;
	}

	protected function get_form_by_hash( string $form_hash ): ?Contact_Form {
		$submitted_form = object( Contact_Form::$forms, $form_hash );

		return $submitted_form instanceof Contact_Form ?
			$submitted_form :
			null;
	}

	protected function is_form_submission_unverified( Contact_Form $submitted_form ): bool {
		$widget = $this->widget;

		return $this->is_form_protected( $submitted_form ) &&
			! $widget->is_verification_token_valid();
	}

	protected function is_form_protected( Contact_Form $form ): bool {
		$widget       = $this->widget;
		$form_content = string( $form, 'content' );

		return false !== strpos( $form_content, '[' . $widget->get_field_name() );
	}

	protected function run_validation_once_per_form( Contact_Form $form ): void {
		$form_id = string( $form, 'attributes.id' );

		if ( in_array( $form_id, $this->validated_form_ids, true ) ) {
			return;
		}

		$this->validated_form_ids[] = $form_id;

		$this->validate_form( $form );
	}

	protected function validate_form( Contact_Form $form ): void {
		$widget = $this->widget;

		// we can detect only if any form was submitted, but can't confirm if it's exactly the current one.
		// For some reason, $form->attributes['id'] and ->hash of the submitted form and the current form always different,
		// even when they're the same.
		$is_form_submitted = $this->get_submitted_form() instanceof Contact_Form;

		if ( $widget->is_protection_enabled() &&
			$is_form_submitted &&
			$this->is_form_submission_unverified( $form ) ) {
			$form->errors = $widget->get_validation_error( $form->errors );
		}
	}
}
