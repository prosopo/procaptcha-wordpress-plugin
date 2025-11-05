<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\Elementor_Pro;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Widget_Base;
use Io\Prosopo\Procaptcha\Integration\Widget\Widget_Integration_Base;
use Io\Prosopo\Procaptcha\Utils\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;

final class Elementor_Login_Widget_Integration extends Widget_Integration_Base {

	private string $widget_name = 'login';

	public function set_hooks( Screen_Detector $screen_detector ): void {
		add_action(
			sprintf( 'elementor/element/%s/section_fields_content/before_section_end', $this->widget_name ),
			array( $this, 'register_widget_setting' )
		);
		add_filter( 'elementor/widget/render_content', array( $this, 'inject_captcha_into_target_widget' ), 10, 2 );

		// No custom validation, since the widget submits the form to the native WordPress login page.
	}

	public function register_widget_setting( Controls_Stack $element ): void {
		$element->add_control(
			$this->widget->get_field_name(),
			array(
				'default' => false,
				'label'   => $this->widget->get_field_label(),
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
		$field_name = $this->widget->get_field_name();

		return 'yes' === $widget_base->get_settings( $field_name );
	}

	protected function inject_captcha_into_form( string $content ): string {
		$widget_field = $this->widget->print_form_field(
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
