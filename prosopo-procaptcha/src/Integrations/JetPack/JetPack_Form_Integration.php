<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\JetPack;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Forms\ContactForm\Contact_Form;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable\Hookable_Form_Integration_Base;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class JetPack_Form_Integration extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'do_shortcode_tag', array( $this, 'maybe_validate' ), 10, 2 );
	}

	public function maybe_validate( string $output, string $tag ): string {
		$widget = self::get_form_helper()->get_widget();

		if ( $widget->get_field_name() !== $tag ||
			! $widget->is_present() ||
			null === Contact_Form::$current_form ||
			! $this->is_form_submitted( Contact_Form::$current_form ) ||
			$widget->is_human_made_request() ) {
			return $output;
		}

		Contact_Form::$current_form->errors = $widget->add_validation_error( Contact_Form::$current_form->errors );

		return $output;
	}

	protected function is_form_submitted( Contact_Form $form ): bool {
		$form_id = string( $form, 'attributes.id' );

		$query_arguments = self::get_form_helper()->get_query_arguments();

		$current_action    = $query_arguments->get_string_for_non_action( 'action', 'post' );
		$current_form_id   = $query_arguments->get_string_for_non_action( 'contact-form-id', 'post' );
		$current_form_hash = $query_arguments->get_string_for_non_action( 'contact-form-hash', 'post' );

		return 'grunion-contact-form' === $current_action &&
				$form_id === $current_form_id &&
				hash_equals( $form->hash, wp_unslash( $current_form_hash ) );
	}
}
