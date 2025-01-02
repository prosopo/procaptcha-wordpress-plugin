<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Captcha\Widget_Arguments;
use Io\Prosopo\Procaptcha\Integration\Form\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Query_Arguments;

class BBPress_Forum extends Hookable_Form_Integration_Base {
	public function set_hooks( bool $is_admin_area ): void {
		add_action( 'add_meta_boxes', array( $this, 'add_settings_metabox' ) );
		add_action( 'save_post_forum', array( $this, 'update_option' ) );

		add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $this, 'maybe_print_captcha' ) );
		add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $this, 'maybe_print_captcha' ) );

		add_action( 'bbp_new_topic_pre_extras', array( $this, 'maybe_validate_captcha' ) );
		add_action( 'bbp_new_reply_pre_extras', array( $this, 'maybe_validate_captcha' ) );
	}

	public function update_option( int $post_id ): void {
		$query_arguments = self::get_form_helpers()->get_query_arguments();

		$value = $query_arguments->get_bool_for_non_action( $this->get_meta_key(), Query_Arguments::POST );

		update_post_meta( $post_id, $this->get_meta_key(), $value );
	}

	public function print_metabox(): void {
		$forum_id = (int) get_the_ID();

		$enabled_checked_attr = $this->is_enabled( $forum_id ) ?
			' checked' :
			'';

		echo '<label>';
		printf(
			'<input type="checkbox" name="%s" value="1" style="margin:0 3px 0 0;"%s>',
			esc_html( $this->get_meta_key() ),
			esc_html( $enabled_checked_attr )
		);
		echo '<span style="vertical-align: middle;">';
		esc_html_e( 'Enable form protection for this forum (new topic, reply)', 'prosopo-procaptcha' );
		echo '</span>';
		echo '</label>';
	}

	public function add_settings_metabox(): void {
		$captcha = self::get_form_helpers()->get_captcha();

		add_meta_box(
			$captcha->get_field_name() . '_bbpress_forum',
			$captcha->get_field_label(),
			array( $this, 'print_metabox' ),
			'forum'
		);
	}

	public function maybe_print_captcha(): void {
		$forum_id = $this->get_current_forum_id();

		if ( ! $this->is_enabled( $forum_id ) ) {
			return;
		}

		$captcha = self::get_form_helpers()->get_captcha();

		$captcha->print_form_field(
			array(
				Widget_Arguments::IS_DESIRED_ON_GUESTS => true,
			)
		);
	}

	public function maybe_validate_captcha(): void {
		$forum_id = $this->get_current_forum_id();

		$captcha = self::get_form_helpers()->get_captcha();

		if ( ! $this->is_enabled( $forum_id ) ||
			! $captcha->present() ||
			$captcha->human_made_request() ) {
			return;
		}

		if ( function_exists( 'bbp_add_error' ) ) {
			bbp_add_error( $captcha->get_field_name(), $captcha->get_validation_error_message() );
		}
	}

	protected function get_meta_key(): string {
		$captcha = self::get_form_helpers()->get_captcha();

		return $captcha->get_field_name() . '_bbpress_forum_protection';
	}

	protected function is_enabled( int $forum_id ): bool {
		return (bool) get_post_meta( $forum_id, $this->get_meta_key(), true );
	}

	protected function get_current_forum_id(): int {
		return function_exists( 'bbp_get_forum_id' ) ?
			bbp_get_forum_id() :
			0;
	}
}
