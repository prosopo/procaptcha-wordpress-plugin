<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Jet_Form_Builder;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration;
use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration_Trait;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use JFB_Modules\Captcha\Abstract_Captcha\Base_Captcha;
use JFB_Modules\Captcha\Abstract_Captcha\Captcha_Settings_From_Options;
use JFB_Modules\Captcha\Module;
use JFB_Modules\Security\Exceptions\Spam_Exception;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

/**
 * Procaptcha provider for the JetFormBuilder captcha system.
 *
 * JetFormBuilder builds the form editor's `Captcha Provider` dropdown from the providers
 * that implement Captcha_Settings_From_Options, so implementing it (even with empty options,
 * since the keys are defined in the Procaptcha plugin settings) is what makes the provider selectable.
 */
final class Jet_Captcha extends Base_Captcha implements Captcha_Settings_From_Options, External_Widget_Integration {
	use External_Widget_Integration_Trait;

	public function get_id(): string {
		return self::get_widget()->get_field_name();
	}

	public function get_title(): string {
		return self::get_widget()->get_field_label();
	}

	/**
	 * @param array<string,mixed> $request
	 *
	 * @throws Spam_Exception When the submitted token is missing or invalid.
	 */
	public function verify( array $request ): void {
		$widget = self::get_widget();

		if ( ! $widget->is_protection_enabled() ) {
			return;
		}

		$token = string( $request, self::FIELD );

		if ( $widget->is_verification_token_valid( $token ) ) {
			return;
		}

		// The message is a JetFormBuilder status slug: it maps it to the form's own 'Captcha failed' message.
		// @phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new Spam_Exception( Module::SPAM_EXCEPTION );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function on_load_options(): array {
		// The keys are defined in the Procaptcha plugin settings, so there is nothing to load.
		return array();
	}

	/**
	 * @param array<string,mixed> $post_request
	 *
	 * @return array<string,mixed>
	 */
	public function on_save_options( array $post_request ): array {
		// The keys are defined in the Procaptcha plugin settings, so there is nothing to save.
		return array();
	}

	protected function render(): string {
		return self::get_widget()->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES      => array(
					'class' => 'jet-form-builder-row',
				),
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					// JetFormBuilder syncs inputs marked with 'data-jfb-sync' into its own form data store.
					'class'         => self::FIELD_CLASS,
					'data-jfb-sync' => '',
					'name'          => self::FIELD,
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
				Widget_Settings::IS_RETURN_ONLY          => true,
				Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
			)
		);
	}
}
