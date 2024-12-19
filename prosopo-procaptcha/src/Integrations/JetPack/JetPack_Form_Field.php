<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\JetPack;

use Automattic\Jetpack\Forms\ContactForm\Contact_Form;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration;
use function Io\Prosopo\Procaptcha\make_collection;

defined( 'ABSPATH' ) || exit;

class JetPack_Form_Field extends Hookable_Form_Integration {
	public function set_hooks( bool $is_admin_area ): void {
		add_filter( 'do_shortcode_tag', array( $this, 'maybe_validate' ), 10, 2 );
	}

	public function maybe_validate( string $output, string $tag ): string {
		$captcha = self::get_form_helper()->get_captcha();

		if ( $captcha->get_field_name() !== $tag ||
			false === $captcha->is_present() ||
			null === Contact_Form::$current_form ||
			false === $this->is_form_submitted( Contact_Form::$current_form ) ||
			true === $captcha->is_human_made_request() ) {
			return $output;
		}

		Contact_Form::$current_form->errors = $captcha->add_validation_error( Contact_Form::$current_form->errors );

		return $output;
	}

	protected function is_form_submitted( Contact_Form $form ): bool {
		// @phpstan-ignore-next-line
		$form_id = make_collection( $form->attributes )
			->get_string( 'id' );

		$query_arguments = self::get_form_helper()->get_query_arguments();

		$current_action    = $query_arguments->get_string_for_non_action( 'action', 'post' );
		$current_form_id   = $query_arguments->get_string_for_non_action( 'contact-form-id', 'post' );
		$current_form_hash = $query_arguments->get_string_for_non_action( 'contact-form-hash', 'post' );

		return 'grunion-contact-form' === $current_action &&
				$form_id === $current_form_id &&
				hash_equals( $form->hash, wp_unslash( $current_form_hash ) );
	}
}
