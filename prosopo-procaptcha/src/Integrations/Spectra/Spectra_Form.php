<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Spectra;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Query_Arguments;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

final class Spectra_Form {
	public function is_input_in_form( string $input_name, string $form ): bool {
		$input          = sprintf( 'name="%s"', $input_name );
		$input_position = strpos( $form, $input );

		return is_int( $input_position );
	}

	public function is_hidden_input_in_form_block(
		string $hidden_input_name,
		int $post_id,
		string $block_id
	): bool {
		$post_content = get_post_field( 'post_content', $post_id );

		$post_blocks = parse_blocks( $post_content );

		$form_block = $this->find_block_with_attribute(
			'uagb/forms',
			'block_id',
			$block_id,
			$post_blocks
		);

		$inner_form_blocks = arr( $form_block, 'innerBlocks' );

		$hidden_input_block = $this->find_block_with_attribute(
			'uagb/forms-hidden',
			'hidden_field_name',
			$hidden_input_name,
			$inner_form_blocks
		);

		return is_array( $hidden_input_block );
	}

	public function replace_input_in_form( string $input_name, string $replacement, string $form ): string {
		$regex = sprintf(
			'/(<input\b[^>]*\bname=["\']%s["\'][^>]*>)/i',
			preg_quote( $input_name, '/' )
		);

		return (string) preg_replace( $regex, $replacement, $form );
	}

	/**
	 * @return mixed
	 */
	public function get_submitted_form_field( Query_Arguments $query_arguments, string $field_name ) {
		$form_data = $query_arguments->get_string_for_non_action( 'form_data', Query_Arguments::POST );

		return $this->extract_field_from_json_string( $field_name, $form_data );
	}

	/**
	 * @param array<int|string,mixed> $blocks
	 *
	 * @return mixed|null
	 */
	protected function find_block_with_attribute(
		string $block_name,
		string $attr_name,
		string $attr_value,
		array $blocks
	) {
		foreach ( $blocks as $item_block ) {
			$item_block_name      = string( $item_block, 'blockName' );
			$item_attribute_value = string( $item_block, array( 'attrs', $attr_name ) );

			if ( $block_name === $item_block_name &&
				$attr_value === $item_attribute_value ) {
				return $item_block;
			}
		}

		return null;
	}

	/**
	 * @return mixed
	 */
	protected function extract_field_from_json_string( string $field_name, string $json_string ) {
		$form_data = json_decode( $json_string, true );
		return is_array( $form_data ) &&
		key_exists( $field_name, $form_data ) ?
			$form_data[ $field_name ] :
			'';
	}
}
