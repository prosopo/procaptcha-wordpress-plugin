<?php

// Global namespace: as User Registration plugin will be calling this class by its name.

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\Integrations\Plugins\User_Registration;
defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Hookable;
use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration;
use Io\Prosopo\Procaptcha\Integration\Widget\External_Widget_Integration_Trait;
use Io\Prosopo\Procaptcha\Screen_Detector\Screen_Detector;
use Io\Prosopo\Procaptcha\Widget\Widget_Settings;
use UR_Form_Field;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

// Class name must match the UR_Form_Field_{field_type} format.
final class UR_Form_Field_Prosopo_Procaptcha extends UR_Form_Field implements External_Widget_Integration, Hookable {
    use External_Widget_Integration_Trait;

    const NAME_PREFIX = 'user_registration_';

    public function __construct() {
        $widget = self::get_widget();

        // @phpstan-ignore-next-line
        $this->id                       = self::NAME_PREFIX . $widget->get_field_name();
        $this->form_id                  = 1;
        $this->registered_fields_config = array(
            'icon'  => 'ur-icon ur-icon-input-checkbox',
            // bypassing, as translations aren't available yet.
            'label' => '',
        );

        $this->field_defaults = array(
            'default_field_name' => $widget->get_field_name(),
            // bypassing, as translations aren't available yet.
            'default_label'      => '',
            // Mark as required, otherwise the form can be submitted without the field, and the validation callback won't be called.
            'default_required'   => true,
        );
    }

    public function set_field_label(): void {
        $widget      = self::get_widget();
        $field_label = $widget->get_field_label();

        $this->registered_fields_config['label'] = $field_label;
        $this->field_defaults['default_label']   = $field_label;
    }

    /**
     * @return string
     */
    public function get_registered_admin_fields() {
        return sprintf(
                '<li id="%s_list" class="ur-registered-item draggable" data-field-id="%1$s">',
                $this->id,
            ) .
            sprintf( '<span class="%s"></span>', $this->registered_fields_config['icon'] ) .
            sprintf( '%s</li>', $this->registered_fields_config['label'] );
    }

    /**
     * Validation for form field.
     *
     * @param object $single_form_field
     * @param object $form_data
     * @param string $filter_hook
     * @param int $form_id
     *
     * @return void
     */
    public function validation( $single_form_field, $form_data, $filter_hook, $form_id ) {
        $widget = self::get_widget();

        $token = string( $form_data, 'value' );

        if ( ! $widget->is_protection_enabled() ||
            $widget->is_verification_token_valid( $token ) ) {
            return;
        }

        add_filter(
            $filter_hook,
            function () use ( $widget ): string {
                return $widget->get_validation_error_message();
            }
        );
    }

    /**
     * @param array<string,mixed> $args
     * @param mixed $value
     */
    public function render_field( string $field, string $key, array $args, $value ): string {
        $widget = self::get_widget();

        return $widget->print_form_field(
            array(
                Widget_Settings::HIDDEN_INPUT_ATTRIBUTES => array(
                    'class'    => 'ur-frontend-field',
                    'data-id'  => $widget->get_field_name(),
                    'name'     => $widget->get_field_name(),
                    // It doesn't affect the browser, as the field is hidden, but it's picked up by the inner form validation.
                    'required' => '',
                ),
                Widget_Settings::IS_DESIRED_ON_GUESTS    => true,
                Widget_Settings::IS_RETURN_ONLY          => true,
                Widget_Settings::IS_WITHOUT_CLIENT_VALIDATION => true,
            )
        );
    }

    public function set_hooks( Screen_Detector $screen_detector ): void {
        add_filter(
            sprintf( 'user_registration_form_field_%s', self::get_widget()->get_field_name() ),
            array( $this, 'render_field' ),
            10,
            4
        );

        // set field label as soon translations are ready.
        add_action( 'init', array( $this, 'set_field_label' ) );
    }
}
