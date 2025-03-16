<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Fluent_Forms;

defined( 'ABSPATH' ) || exit;

use FluentForm\App\Services\FormBuilder\BaseFieldManager;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Form_Integration;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Helper\Form_Integration_Helper_Container;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

class Fluent_Forms_Form_Integration extends BaseFieldManager implements Form_Integration {
	use Form_Integration_Helper_Container;

	public function __construct() {
		parent::__construct(
			self::get_form_helper()->get_widget()->get_field_name(),
			self::get_form_helper()->get_widget()->get_field_label(),
			array(
				'prosopo',
				'procaptcha',
				'captcha',
			),
			'advanced'
		);

		add_filter( "fluentform/validate_input_item_{$this->key}", array( $this, 'validate' ), 10, 5 );
	}

	/**
	 * @return array<string,mixed>
	 */
	public function getComponent(): array {
		return array(
			'attributes'     => array(
				'name' => $this->key,
				'type' => 'text',
			),
			'editor_options' => array(
				'icon_class' => 'ff-edit-recaptha',
				'template'   => 'inputText',
				'title'      => $this->title,
			),
			'element'        => $this->key,
			'index'          => 1,
			'settings'       => array(
				'label'            => $this->title,
				'validation_rules' => array(
					'required' => array(
						'message' => self::get_form_helper()->get_widget()->get_validation_error_message(),
						'value'   => true,
					),
				),
			),
		);
	}

	/**
	 * @return string[]
	 */
	public function getGeneralEditorElements(): array {
		return array(
			'validation_rules',
		);
	}

	/**
	 * @param mixed $element
	 * @param mixed $form
	 *
	 * @return void
	 */
	public function render( $element, $form ) {
		echo '<div class="ff-el-group">';
		self::get_form_helper()->get_widget()->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES      => array(
					'class' => 'ff-el-input--content',
				),
				Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
					'class'     => 'ff-el-form-control',
					'data-name' => $this->key,
					'name'      => $this->key,
				),
				Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
			)
		);
		echo '</div>';
	}

	/**
	 * @param string|string[] $error_message
	 * @param array<string,mixed> $field
	 * @param array<string,mixed> $form_data
	 * @param array<string,mixed> $fields
	 * @param object $form
	 *
	 * @return string|string[]
	 */
	public function validate( $error_message, array $field, $form_data, $fields, $form ) {
		$token = string( $form_data, $this->key );

		$widget = self::get_form_helper()->get_widget();

		if ( ! $widget->is_protection_enabled() ||
		$widget->is_verification_token_valid( $token ) ) {
			return $error_message;
		}

		return $widget->get_validation_error_message();
	}
}
