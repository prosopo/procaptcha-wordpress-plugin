<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Bricks;

defined( 'ABSPATH' ) || exit;

use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

/**
 * Knowledge about the Bricks form element structure.
 *
 * Both the rendering and the validation resolve the protected field from the very same settings shape,
 * so they either both recognize the form, or both leave it untouched.
 */
final class Bricks_Form {
	const FIELD_NAME_PREFIX = 'form-field-';
	const HIDDEN_FIELD_TYPE = 'hidden';

	/**
	 * Returns the 'name' attribute of the marker hidden field, or an empty string when the form isn't protected.
	 *
	 * @param mixed $element_settings
	 */
	public function get_protected_field_name( $element_settings, string $marker ): string {
		if ( ! is_array( $element_settings ) ||
			! is_array( $element_settings['fields'] ?? null ) ) {
			return '';
		}

		foreach ( $element_settings['fields'] as $field ) {
			if ( ! $this->is_marker_field( $field, $marker ) ) {
				continue;
			}

			$field_id = string( $field, 'id' );

			if ( '' === $field_id ) {
				continue;
			}

			return self::FIELD_NAME_PREFIX . $field_id;
		}

		return '';
	}

	public function replace_input_in_form( string $input_name, string $replacement, string $form ): string {
		$regex = sprintf(
			'/(<input\b[^>]*\bname=["\']%s["\'][^>]*>)/i',
			preg_quote( $input_name, '/' )
		);

		return (string) preg_replace( $regex, $replacement, $form, 1 );
	}

	/**
	 * @param mixed $submitted_fields
	 */
	public function get_submitted_field( $submitted_fields, string $field_name ): string {
		return is_array( $submitted_fields ) ?
			string( $submitted_fields, $field_name ) :
			'';
	}

	/**
	 * @param mixed $field
	 */
	protected function is_marker_field( $field, string $marker ): bool {
		if ( ! is_array( $field ) ||
			self::HIDDEN_FIELD_TYPE !== string( $field, 'type' ) ) {
			return false;
		}

		// The marker can be set either as the field label or as its value, whichever is available in the builder.
		return string( $field, 'label' ) === $marker ||
			string( $field, 'value' ) === $marker;
	}
}
