<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Spectra;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration;
use Io\Prosopo\Procaptcha\Query_Arguments;

class Spectra_Form_Block_Field extends Hookable_Form_Integration {
	public function set_hooks( bool $is_admin_area ): void {
		add_filter(
			'render_block_uagb/forms',
			array( $this, 'inject_field_when_target_input_is_present' )
		);

		// With the low priority to be process before the primary handler.
		add_action( 'wp_ajax_uagb_process_forms', array( $this, 'process_submission_when_block_has_target_input' ), -999 );
		add_action(
			'wp_ajax_nopriv_uagb_process_forms',
			array(
				$this,
				'process_submission_when_block_has_target_input',
			),
			-999
		);
	}

	public function inject_field_when_target_input_is_present( string $content ): string {
		$captcha    = self::get_form_helper()->get_captcha();
		$input_name = $captcha->get_field_name();

		if ( false === $this->is_input_present( $input_name, $content ) ) {
			return $content;
		}

		$captcha_element = $captcha->print_form_field(
			array(
				Widget_Arguments::ELEMENT_ATTRIBUTES      => array(
					'style' => 'margin: 0 0 20px',
				),
				Widget_Arguments::HIDDEN_INPUT_ATTRIBUTES => array(
					'class' => 'uagb-forms-hidden-input',
					'id'    => 'hidden',
					'name'  => $input_name,
				),
				Widget_Arguments::IS_DESIRED_ON_GUESTS    => true,
				Widget_Arguments::IS_RETURN_ONLY          => true,
				Widget_Arguments::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);

		return $this->replace_hidden_input(
			$input_name,
			$captcha_element,
			$content
		);
	}

	public function process_submission_when_block_has_target_input(): void {
		$form_helper     = self::get_form_helper();
		$query_arguments = $form_helper->get_query_arguments();
		$captcha         = $form_helper->get_captcha();

		$post_id  = $query_arguments->get_int_for_non_action( 'post_id', Query_Arguments::POST );
		$block_id = $query_arguments->get_string_for_non_action( 'block_id', Query_Arguments::POST );

		if ( false === $captcha->is_present() ||
			false === $this->is_valid_post_id( $post_id ) ||
		'' === $block_id ) {
			return;
		}

		$field_name = $captcha->get_field_name();

		if ( false === $this->form_block_has_hidden_field( $post_id, $block_id, $field_name ) ) {
			return;
		}

		$form_data   = $query_arguments->get_string_for_non_action( 'form_data', Query_Arguments::POST );
		$token_value = $this->get_value_from_json_string( $field_name, $form_data );
		$token       = true === is_string( $token_value ) ?
			$token_value :
			'';

		if ( true === $captcha->is_human_made_request( $token ) ) {
			return;
		}

		// Spectra doesn't process error messages from the backend.
		wp_send_json_error( 400 );
	}

	/**
	 * @return mixed
	 */
	protected function get_value_from_json_string( string $field_name, string $json_string ) {
		$form_data = json_decode( $json_string, true );
		return true === is_array( $form_data ) &&
				true === key_exists( $field_name, $form_data ) ?
			$form_data[ $field_name ] :
			'';
	}

	protected function form_block_has_hidden_field( int $post_id, string $block_id, string $field_name ): bool {
		$post_content = get_post_field( 'post_content', $post_id );

		$post_blocks = parse_blocks( $post_content );

		$form_block = $this->find_block( 'uagb/forms', 'block_id', $block_id, $post_blocks );

		$hidden_input_block = $this->find_inner_block(
			$form_block,
			'uagb/forms-hidden',
			'hidden_field_name',
			$field_name
		);

		return array() !== $hidden_input_block;
	}

	/**
	 * @param array<string,mixed> $parent_block
	 *
	 * @return array<string,mixed>
	 */
	protected function find_inner_block( array $parent_block, string $block_name, string $attr_name, string $attr_value ): array {
		$form_inner_blocks = true === key_exists( 'innerBlocks', $parent_block ) &&
								true === is_array( $parent_block['innerBlocks'] ) ?
			$parent_block['innerBlocks'] :
			array();

		return $this->find_block(
			$block_name,
			$attr_name,
			$attr_value,
			$form_inner_blocks
		);
	}

	/**
	 * @param array<int|string,array<string,mixed>> $blocks
	 *
	 * @return array<string,mixed>
	 */
	protected function find_block( string $block_name, string $attr_name, string $attr_value, array $blocks ): array {
		$target_blocks = array_filter(
			$blocks,
			function ( $block ) use ( $block_name, $attr_name, $attr_value ) {
				return true === isset( $block['blockName'], $block['attrs'] ) &&
						true === is_array( $block['attrs'] ) &&
						$block_name === $block['blockName'] &&
						$attr_value === $block['attrs'][ $attr_name ];
			}
		);

		$target_block = array_pop( $target_blocks );

		return true === is_array( $target_block ) ?
			$target_block :
			array();
	}

	protected function is_valid_post_id( int $post_id ): bool {
		return null !== get_post( $post_id );
	}

	protected function is_input_present( string $input_name, string $content ): bool {
		return false !== strpos( $content, sprintf( 'name="%s"', $input_name ) );
	}

	protected function replace_hidden_input( string $input_name, string $replacement, string $subject ): string {
		$regex = sprintf(
			'/(<input\b[^>]*\btype=["\']hidden["\'][^>]*\bname=["\']%s["\'][^>]*>)/i',
			preg_quote( $input_name, '/' )
		);

		return (string) preg_replace( $regex, $replacement, $subject );
	}
}