<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha\View;

use Io\Prosopo\Procaptcha\Interfaces\View\Object_Property_Manager_Interface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class Object_Property_Manager implements Object_Property_Manager_Interface {
	const DEFAULT_VALUES = array(
		'array'  => array(),
		'bool'   => false,
		'float'  => 0.0,
		'int'    => 0,
		'object' => null,
		'string' => '',
	);

	/**
	 * @var array<string,mixed> $default_values type => default_value
	 */
	private array $default_values;

	/**
	 * @param array<string,mixed> $default_values type => default_value
	 */
	public function __construct( array $default_values = self::DEFAULT_VALUES ) {
		$this->default_values = $default_values;
	}

	public function set_default_values( object $instance ): void {
		$reflection_class       = $this->get_reflection_class( $instance );
		$public_typed_variables = $this->get_public_typed_variables( $reflection_class );

		array_map(
			function ( ReflectionProperty $reflection_property ) use ( $instance ) {
				if ( true === $reflection_property->isInitialized( $instance ) ) {
					return;
				}

				$this->set_default_value_when_type_is_supported( $instance, $reflection_property );
			},
			$public_typed_variables
		);
	}

	public function get_variables( object $instance ): array {
		$reflection_class = $this->get_reflection_class( $instance );

		$public_typed_variables = $this->get_public_typed_variables( $reflection_class );
		$variable_values        = $this->get_property_values( $instance, $public_typed_variables );

		$method_names     = $this->get_public_method_names( $reflection_class );
		$method_callbacks = $this->make_method_callbacks( $instance, $method_names );

		/**
		 * @var array<string,mixed>
		 */
		return array_merge( $variable_values, $method_callbacks );
	}

	/**
	 * @param ReflectionClass<object> $reflection_class
	 *
	 * @return ReflectionProperty[]
	 */
	protected function get_public_typed_variables( ReflectionClass $reflection_class ): array {
		$public_properties = $reflection_class->getProperties( ReflectionProperty::IS_PUBLIC );

		return $this->get_typed_properties( $public_properties );
	}

	/**
	 * @param ReflectionClass<object> $reflection_class
	 *
	 * @return string[]
	 */
	protected function get_public_method_names( ReflectionClass $reflection_class ): array {
		$public_methods = $reflection_class->getMethods( ReflectionMethod::IS_PUBLIC );

		return array_diff(
			$this->get_method_names_for_given_methods( $public_methods ),
			array( '__construct' )
		);
	}

	/**
	 * @return ReflectionClass<object>
	 */
	protected function get_reflection_class( object $instance ): ReflectionClass {
		return new ReflectionClass( $instance );
	}

	/**
	 * @param ReflectionProperty[] $reflection_properties
	 *
	 * @return ReflectionProperty[]
	 */
	protected function get_typed_properties( array $reflection_properties ): array {
		return array_filter(
			$reflection_properties,
			function ( ReflectionProperty $property ): bool {
				return null !== $property->getType();
			}
		);
	}

	/**
	 * @param ReflectionMethod[] $reflection_methods
	 *
	 * @return string[]
	 */
	protected function get_method_names_for_given_methods( array $reflection_methods ): array {
		return array_map(
			function ( ReflectionMethod $method ) {
				return $method->getName();
			},
			$reflection_methods
		);
	}

	/**
	 * @param ReflectionProperty[] $reflection_properties
	 *
	 * @return array<string,mixed> variableName => variableValue
	 */
	protected function get_property_values( object $instance, array $reflection_properties ): array {
		return array_reduce(
			$reflection_properties,
			function ( array $variable_values, ReflectionProperty $reflection_property ) use ( $instance ) {
				$variable_values[ $reflection_property->getName() ] = $reflection_property->getValue( $instance );

				return $variable_values;
			},
			array()
		);
	}

	/**
	 * @param string[] $method_names
	 *
	 * @return array<string,callable> methodName => method
	 */
	protected function make_method_callbacks( object $instance, array $method_names ): array {
		return array_reduce(
			$method_names,
			function ( array $method_callbacks, string $method_name ) use ( $instance ) {
				$method_callbacks[ $method_name ] = array( $instance, $method_name );

				return $method_callbacks;
			},
			array()
		);
	}

	protected function set_default_value_when_type_is_supported( object $instance, ReflectionProperty $reflection_property ): void {
		$type = $reflection_property->getType();

		$type_name = null !== $type ?
			// @phpstan-ignore-next-line
			$type->getName() :
			'';

		if ( false === key_exists( $type_name, $this->default_values ) ) {
			return;
		}

		$reflection_property->setValue( $instance, $this->default_values[ $type_name ] );
	}
}
