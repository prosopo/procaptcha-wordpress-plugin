<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Widget_Base;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Helper\Form_Integration_Helper_Container;
use Io\Prosopo\Procaptcha\Plugin_Integration\Form\Hookable\Hookable_Form_Integration_Base;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

class Elementor_Login_Widget_Integration extends Hookable_Form_Integration_Base {
	use Form_Integration_Helper_Container;

	private string $widget_name = 'login';

	public function set_hooks( bool $is_admin_area ): void {
		add_action(
			sprintf( 'elementor/element/%s/section_fields_content/before_section_end', $this->widget_name ),
			array( $this, 'register_widget_setting' )
		);
		add_filter( 'elementor/widget/render_content', array( $this, 'inject_captcha_into_target_widget' ), 10, 2 );

		// No custom validation, since the widget submits the form to the native WordPress login page.
	}

	public function register_widget_setting( Controls_Stack $element ): void {
		$widget = self::get_form_helper()->get_widget();

		$element->add_control(
			$widget->get_field_name(),
			array(
				'default' => false,
				'label'   => $widget->get_field_label(),
				'type'    => Controls_Manager::SWITCHER,
			)
		);
	}

	public function inject_captcha_into_target_widget( string $content, Widget_Base $widget ): string {
		if ( ! $this->is_target_widget( $widget ) ||
		! $this->is_active_widget( $widget ) ) {
			return $content;
		}

		return $this->inject_captcha_into_form( $content );
	}

	protected function is_target_widget( Widget_Base $widget ): bool {
		return $this->widget_name === $widget->get_name();
	}

	protected function is_active_widget( Widget_Base $widget_base ): bool {
		$widget     = self::get_form_helper()->get_widget();
		$field_name = $widget->get_field_name();

		return 'yes' === $widget_base->get_settings( $field_name );
	}

	protected function inject_captcha_into_form( string $content ): string {
		$widget = self::get_form_helper()->get_widget();

		$widget_field = $widget->print_form_field(
			array(
				Widget_Settings::ELEMENT_ATTRIBUTES => array(
					'style' => 'margin: 0 0 10px;width:100%;',
				),
				Widget_Settings::IS_RETURN_ONLY     => true,
			)
		);

		$button_wrapper = '/(<div class="elementor-field-group.+<button type="submit")/s';

		return (string) preg_replace( $button_wrapper, $widget_field . "\n$1", $content );
	}
}
