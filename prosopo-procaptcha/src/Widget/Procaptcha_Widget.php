<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Widget;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Settings\Procaptcha_Settings;
use Io\Prosopo\Procaptcha\Utils\Query_Arguments;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use WP_Error;
use function Io\Prosopo\Procaptcha\html_attrs_collection;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;

class Procaptcha_Widget implements Widget {
	const API_URL                    = 'https://api.prosopo.io/siteverify';
	const FORM_FIELD_NAME            = 'procaptcha-response';
	const ALLOW_BYPASS_CONSTANT_NAME = 'PROSOPO_PROCAPTCHA_ALLOW_BYPASS';

	private Widget_Assets_Loader $widget_assets_manager;
	private ModelRendererInterface $renderer;
	private Procaptcha_Settings $procaptcha_settings;
	/**
	 * @var array<string,bool> token => result
	 * Used to avoid multiple HTTP requests when verification is called several times for the same token.
	 * (e.g. JetPack calls verification in several different hooks per same token).
	 */
	private array $token_verification_results;

	public function __construct(
		Widget_Assets_Loader $widget_assets_manager,
		ModelRendererInterface $renderer,
		Procaptcha_Settings $procaptcha_settings
	) {
		$this->widget_assets_manager = $widget_assets_manager;
		$this->renderer              = $renderer;
		$this->procaptcha_settings   = $procaptcha_settings;

		$this->token_verification_results = array();
	}

	public function get_validation_error_message(): string {
		$message = __( 'Please verify that you are human.', 'prosopo-procaptcha' );

		return apply_filters( 'prosopo/procaptcha/validation_error_message', $message );
	}

	/**
	 * @return array<string,string>
	 */
	public function get_themes(): array {
		return array(
			'dark'  => __( 'Dark', 'prosopo-procaptcha' ),
			'light' => __( 'Light', 'prosopo-procaptcha' ),
		);
	}

	/**
	 * @return array<string,string>
	 */
	public function get_types(): array {
		return array(
			'frictionless' => __( 'Frictionless', 'prosopo-procaptcha' ),
			'image'        => __( 'Image', 'prosopo-procaptcha' ),
			'pow'          => __( 'Pow', 'prosopo-procaptcha' ),
		);
	}

	// Do not use dash, it doesn't work with some (e.g. ContactForm7).
	public function get_field_name(): string {
		return 'prosopo_procaptcha';
	}

	public function get_field_label(): string {
		return __( 'Prosopo Procaptcha', 'prosopo-procaptcha' );
	}

	/**
	 * @param string|null $token Allows to define the token value for JS-based custom forms (like NinjaForms).
	 */
	public function is_verification_token_valid( ?string $token = null ): bool {
		$token = null === $token ?
			Query_Arguments::get_non_action_string( self::FORM_FIELD_NAME, Query_Arguments::POST ) :
			$token;

		// bail early if the token is empty.
		if ( '' === $token ) {
			return false;
		}

		$allow_bypass = defined( self::ALLOW_BYPASS_CONSTANT_NAME ) &&
			true === constant( self::ALLOW_BYPASS_CONSTANT_NAME );

		if ( $allow_bypass &&
			'bypass' === $token ) {
			return true;
		}

		if ( false === key_exists( $token, $this->token_verification_results ) ) {
			$this->token_verification_results[ $token ] = $this->verify_token( $token );
		}

		return $this->token_verification_results[ $token ];
	}

	public function get_validation_error( WP_Error $base_error = null ): WP_Error {
		$error_code    = 'procaptcha-failed';
		$error_data    = 400; // must be numeric, e.g. for the WP comment form, likely HTTP code.
		$error_message = $this->get_validation_error_message();

		if ( null === $base_error ) {
			$base_error = new WP_Error( $error_code, $error_message, $error_data );
		}

		// make sure we add the error only once if the method is called multiple times for the same error instance.
		if ( false === in_array( $error_code, $base_error->get_error_codes(), true ) ) {
			$base_error->add( $error_code, $error_message, $error_data );
		}

		return $base_error;
	}

	// Note: this function is available only after the 'set_current_user' hook.
	public function is_protection_enabled(): bool {
		$is_user_authorized = wp_get_current_user()->exists();

		$should_bypass_user = $is_user_authorized && $this->procaptcha_settings->should_bypass_authorized_user();
		$is_captcha_present = ! $should_bypass_user;

		return apply_filters( 'prosopo/procaptcha/is_captcha_present', $is_captcha_present );
	}

	public function is_available(): bool {
		$secret_key = $this->procaptcha_settings->get_secret_key();
		$site_key   = $this->procaptcha_settings->get_site_key();

		return strlen( $secret_key ) > 0 &&
			strlen( $site_key ) > 0;
	}

	public function load_plugin_integration_script( string $integration_name ): void {
		$this->widget_assets_manager->load_integration_script( $integration_name );
	}

	public function add_integration_css( string $css_code ): void {
		$this->widget_assets_manager->load_integration_css( $css_code );
	}

	public function print_form_field( array $settings = array() ): string {
		$desired_on_guests = bool( $settings, Widget_Settings::IS_DESIRED_ON_GUESTS );

		$is_field_stub = $desired_on_guests &&
			! $this->is_protection_enabled();

		if ( ! $is_field_stub ) {
			// automatically mark as in use.
			$this->widget_assets_manager->load_widget();
		}

		$form_field = $this->renderer->renderModel(
			Widget_Model::class,
			function ( Widget_Model $widget ) use ( $is_field_stub, $settings ) {
				$attributes         = arr( $settings, Widget_Settings::ELEMENT_ATTRIBUTES );
				$hidden_input_attrs = arr( $settings, Widget_Settings::HIDDEN_INPUT_ATTRIBUTES );

				$widget->attributes           = html_attrs_collection( $attributes );
				$widget->hidden_input_attrs   = html_attrs_collection( $hidden_input_attrs );
				$widget->is_stub              = $is_field_stub;
				$widget->no_client_validation = bool( $settings, Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION );
				$widget->is_error_visible     = bool( $settings, Widget_Settings::IS_ERROR_ACTIVE );
				$widget->error_message        = $this->get_validation_error_message();
			}
		);

		$return_only = bool( $settings, Widget_Settings::IS_RETURN_ONLY );

		if ( ! $return_only ) {
            // @phpcs:ignore WordPress.Security.EscapeOutput
			echo $form_field;
			$form_field = '';
		}

		return $form_field;
	}

	protected function verify_token( string $token ): bool {
		$secret_key = $this->procaptcha_settings->get_secret_key();

		$response = wp_remote_post(
			self::API_URL,
			array(
				'body'    => (string) wp_json_encode(
					array(
						'secret' => $secret_key,
						'token'  => $token,
					)
				),
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'method'  => 'POST',
				// limit waiting time to 20 seconds.
				'timeout' => 20,
			)
		);

		if ( is_wp_error( $response ) ||
			200 !== wp_remote_retrieve_response_code( $response ) ) {
			// something went wrong, maybe connection issue, but we still shouldn't allow the request.
			// todo log.
			return false;
		}

		$body = wp_remote_retrieve_body( $response );

		$body = json_decode( $body, true );

		$body = is_array( $body ) ?
			$body :
			array();

		return bool( $body, 'verified' );
	}
}
