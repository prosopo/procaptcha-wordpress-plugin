<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Bricks;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\arr;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

/**
 * Bricks form element.
 *
 * A Hidden field named (or valued) `prosopo_procaptcha` marks the target form as protected:
 * its input is replaced with the Procaptcha widget, which puts the token into the very same field,
 * so the token always reaches the server as a regular Bricks form field.
 */
final class Bricks_Form_Integration extends Widget_Integration_Base {
	const FORM_ELEMENT_NAME = 'form';
	/**
	 * The 'bricks/frontend/render_element' filter, which is the only way to reach the form markup,
	 * is available since Bricks 2.0.
	 */
	const MINIMAL_VERSION = '2.0';

	private Bricks_Form $bricks_form;
	private string $marker;

	public function __construct( Widget $widget ) {
		parent::__construct( $widget );

		$this->bricks_form = new Bricks_Form();
		$this->marker      = $widget->get_field_name();
	}

	public function set_hooks( Screen_Detector $screen_detector ): void {
		// Bricks is a theme, so its version constant isn't available yet on the 'plugins_loaded' hook.
		add_action( 'after_setup_theme', array( $this, 'set_bricks_hooks' ), 20 );
	}

	public function set_bricks_hooks(): void {
		if ( ! $this->is_supported_bricks_version() ) {
			return;
		}

		add_filter( 'bricks/frontend/render_element', array( $this, 'integrate_widget_into_form' ), 10, 2 );
		add_filter( 'bricks/form/validate', array( $this, 'verify_form_submission' ), 10, 2 );
	}

	/**
	 * @param mixed $element_html
	 * @param mixed $element
	 *
	 * @return mixed
	 */
	public function integrate_widget_into_form( $element_html, $element ) {
		if ( ! is_string( $element_html ) ||
			! is_object( $element ) ||
			self::FORM_ELEMENT_NAME !== string( $element, 'name' ) ) {
			return $element_html;
		}

		$field_name = $this->bricks_form->get_protected_field_name( arr( $element, 'settings' ), $this->marker );

		if ( '' === $field_name ) {
			return $element_html;
		}

		return $this->bricks_form->replace_input_in_form(
			$field_name,
			$this->get_widget_field( $field_name ),
			$element_html
		);
	}

	/**
	 * @param mixed $errors
	 * @param mixed $form
	 *
	 * @return mixed
	 */
	public function verify_form_submission( $errors, $form ) {
		$widget = $this->widget;

		if ( ! is_array( $errors ) ||
			! is_object( $form ) ||
			! method_exists( $form, 'get_settings' ) ||
			! method_exists( $form, 'get_fields' ) ) {
			return $errors;
		}

		$field_name = $this->bricks_form->get_protected_field_name( $form->get_settings(), $this->marker );

		if ( '' === $field_name ||
			! $widget->is_protection_enabled() ) {
			return $errors;
		}

		$token = $this->bricks_form->get_submitted_field( $form->get_fields(), $field_name );

		if ( $widget->is_verification_token_valid( $token ) ) {
			return $errors;
		}

		$errors[] = $widget->get_validation_error_message();

		return $errors;
	}

	protected function is_supported_bricks_version(): bool {
		return defined( 'BRICKS_VERSION' ) &&
			version_compare( (string) constant( 'BRICKS_VERSION' ), self::MINIMAL_VERSION, '>=' );
	}

	protected function get_widget_field( string $field_name ): string {
		return $this->widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES      => array(
					'style' => 'margin:0 0 10px;width:100%',
				),
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					'name' => $field_name,
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
				Widget_Settings::IS_RETURN_ONLY          => true,
			)
		);
	}
}
