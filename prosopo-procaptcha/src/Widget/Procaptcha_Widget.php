<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Widget;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Query_Arguments;
use Io\Prosopo\Procaptcha\Settings\Storage\Procaptcha_Settings_Storage;
use Io\Prosopo\Procaptcha\Settings\Tabs\General_Procaptcha_Settings;
use Io\Prosopo\Procaptcha\Template_Models\Widget_Model;
use Io\Prosopo\Procaptcha\Vendors\Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Io\Prosopo\Procaptcha\Widget\Assets\Widget_Frontend_Assets;
use WP_Error;
use function Io\Prosopo\Procaptcha\html_attrs_collection;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\bool;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Procaptcha_Widget implements Widget {


	const API_URL                    = 'https://api.prosopo.io/siteverify';
	const FORM_FIELD_NAME            = 'procaptcha-response';
	const ALLOW_BYPASS_CONSTANT_NAME = 'PROSOPO_PROCAPTCHA_ALLOW_BYPASS';

	private Procaptcha_Settings_Storage $settings_storage;
	private Widget_Frontend_Assets $widget_assets_manager;
	private Query_Arguments $query_arguments;
	private ModelRendererInterface $renderer;
	/**
	 * @var array<string,bool> token => result
	 * Used to avoid multiple HTTP requests when verification is called several times for the same token.
	 * (e.g. JetPack calls verification in several different hooks per same token).
	 */
	private array $token_verification_results;

	public function __construct(
		Procaptcha_Settings_Storage $settings_storage,
		Widget_Frontend_Assets $widget_assets_manager,
		Query_Arguments $query_arguments,
		ModelRendererInterface $renderer
	) {
		$this->settings_storage           = $settings_storage;
		$this->widget_assets_manager      = $widget_assets_manager;
		$this->query_arguments            = $query_arguments;
		$this->renderer                   = $renderer;
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
	public function is_human_made_request( ?string $token = null ): bool {
		$token = null === $token ?
			$this->query_arguments->get_string_for_non_action( self::FORM_FIELD_NAME, Query_Arguments::POST ) :
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
	public function is_present(): bool {
		$user_authorized = wp_get_current_user()->exists();

		$general_settings       = $this->settings_storage->get( General_Procaptcha_Settings::class )->get_settings();
		$enabled_for_authorized = bool( $general_settings, General_Procaptcha_Settings::IS_ENABLED_FOR_AUTHORIZED );

		$present = ! $user_authorized ||
			$enabled_for_authorized;

		return apply_filters( 'prosopo/procaptcha/is_captcha_present', $present );
	}

	public function is_available(): bool {
		$general_settings = $this->settings_storage->get( General_Procaptcha_Settings::class )->get_settings();

		return '' !== string( $general_settings, General_Procaptcha_Settings::SECRET_KEY ) &&
			'' !== string( $general_settings, General_Procaptcha_Settings::SITE_KEY );
	}

	public function add_integration_js( string $integration_name ): void {
		$this->widget_assets_manager->add_integration_js( $integration_name );
	}

	public function add_integration_css( string $css_code ): void {
		$this->widget_assets_manager->add_integration_css( $css_code );
	}

	public function print_form_field( array $settings = array() ): string {
		$desired_on_guests = bool( $settings, Widget_Settings::IS_DESIRED_ON_GUESTS );

		$is_field_stub = $desired_on_guests &&
			! $this->is_present();

		if ( ! $is_field_stub ) {
			// automatically mark as in use.
			$this->widget_assets_manager->add_widget();
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
		$general_settings = $this->settings_storage->get( General_Procaptcha_Settings::class )->get_settings();
		$secret_key       = string( $general_settings, General_Procaptcha_Settings::SECRET_KEY );

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
