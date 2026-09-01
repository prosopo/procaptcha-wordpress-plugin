<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Blocksy\Forms;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

/**
 * Blocksy Companion newsletter subscribe form (both the block and the shortcode).
 *
 * The form is submitted with the whole FormData, so the widget's own input reaches the server as is.
 */
final class Blocksy_Newsletter extends Widget_Integration_Base {
	const AJAX_ACTION   = 'blc_newsletter_subscribe_process_ajax_subscribe';
	const BLOCK_NAME    = 'blocksy/newsletter';
	const MESSAGE_BLOCK = '<div class="ct-newsletter-subscribe-message">';
	const SHORTCODE     = 'blocksy_newsletter_subscribe';

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_filter( 'render_block', array( $this, 'integrate_widget_into_block' ), 10, 2 );
		add_filter( 'do_shortcode_tag', array( $this, 'integrate_widget_into_shortcode' ), 10, 2 );

		// With the low priority to be processed before the primary handler.
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'verify_form_submission' ), -999 );
		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, array( $this, 'verify_form_submission' ), -999 );
	}

	/**
	 * @param mixed $block_content
	 * @param mixed $block
	 *
	 * @return mixed
	 */
	public function integrate_widget_into_block( $block_content, $block ) {
		if ( ! is_string( $block_content ) ||
			self::BLOCK_NAME !== string( $block, 'blockName' ) ) {
			return $block_content;
		}

		return $this->add_widget_field( $block_content );
	}

	/**
	 * @param mixed $shortcode_content
	 *
	 * @return mixed
	 */
	public function integrate_widget_into_shortcode( $shortcode_content, string $shortcode_name ) {
		if ( ! is_string( $shortcode_content ) ||
			self::SHORTCODE !== $shortcode_name ) {
			return $shortcode_content;
		}

		return $this->add_widget_field( $shortcode_content );
	}

	public function verify_form_submission(): void {
		$widget = $this->widget;

		if ( ! $widget->is_protection_enabled() ||
			$widget->is_verification_token_valid() ) {
			return;
		}

		// Blocksy prints the 'message' key of the response, regardless of its success status.
		wp_send_json_error(
			array(
				'message' => $widget->get_validation_error_message(),
				'result'  => 'no',
			)
		);
	}

	protected function add_widget_field( string $form_content ): string {
		if ( false === strpos( $form_content, self::MESSAGE_BLOCK ) ) {
			return $form_content;
		}

		$widget_field = $this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin:10px 0 0',
				),
				Widget_Settings::IS_RETURN_ONLY     => true,
			)
		);

		return str_replace(
			self::MESSAGE_BLOCK,
			$widget_field . "\n" . self::MESSAGE_BLOCK,
			$form_content
		);
	}
}
