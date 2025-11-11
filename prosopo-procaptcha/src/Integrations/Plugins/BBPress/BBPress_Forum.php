<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\BBPress;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Query_Arguments;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class BBPress_Forum extends Widget_Integration_Base {
	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action( 'add_meta_boxes', array( $this, 'add_settings_metabox' ) );
		add_action( 'save_post_forum', array( $this, 'update_option' ) );

		add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $this, 'maybe_print_captcha' ) );
		add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $this, 'maybe_print_captcha' ) );

		add_action( 'bbp_new_topic_pre_extras', array( $this, 'maybe_validate_captcha' ) );
		add_action( 'bbp_new_reply_pre_extras', array( $this, 'maybe_validate_captcha' ) );
	}

	public function update_option( int $post_id ): void {
		$value = Query_Arguments::get_non_action_bool( $this->get_meta_key(), Query_Arguments::POST );

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
		add_meta_box(
			$this->widget->get_field_name() . '_bbpress_forum',
			$this->widget->get_field_label(),
			array( $this, 'print_metabox' ),
			'forum'
		);
	}

	public function maybe_print_captcha(): void {
		$forum_id = $this->get_current_forum_id();

		if ( ! $this->is_enabled( $forum_id ) ) {
			return;
		}

		$this->widget->print_form_field(
			array(
				Widget_Settings::IS_DESIRED_ON_GUESTS => true,
			)
		);
	}

	public function maybe_validate_captcha(): void {
		$forum_id = $this->get_current_forum_id();

		if ( ! $this->is_enabled( $forum_id ) ||
			! $this->widget->is_protection_enabled() ||
			$this->widget->is_verification_token_valid() ) {
			return;
		}

		if ( function_exists( 'bbp_add_error' ) ) {
			bbp_add_error( $this->widget->get_field_name(), $this->widget->get_validation_error_message() );
		}
	}

	protected function get_meta_key(): string {
		return $this->widget->get_field_name() . '_bbpress_forum_protection';
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
