<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

defined( 'ABSPATH' ) || exit;

class Collection {
	/**
	 * @var array<string|int, mixed>
	 */
	protected array $items;

	/**
	 * @param array<string|int, mixed> $data
	 */
	public function __construct( array $data ) {
		$this->items = $data;
	}

	// Cast to types.

	/**
	 * @return array<int|string,mixed>
	 */
	public function get_array( string $item_name ): array {
		return true === $this->exists( $item_name ) &&
				true === is_array( $this->items[ $item_name ] ) ?
			$this->items[ $item_name ] :
			array();
	}

	public function get_int( string $item_name ): int {
		return true === $this->exists( $item_name ) &&
				true === is_numeric( $this->items[ $item_name ] ) ?
			(int) $this->items[ $item_name ] :
			0;
	}

	public function get_bool( string $item_name ): bool {
		return true === $this->exists( $item_name ) &&
				// [1] and '1' are allowed values for [true] if we talk about boolean,
				// e.g. ACF uses [1] it for the 'multiple' attribute of the select field.
				true === in_array( $this->items[ $item_name ], array( true, 1, '1', 'on' ), true );
	}

	public function get_string( string $item_name ): string {
		return true === $this->exists( $item_name ) &&
				( true === is_string( $this->items[ $item_name ] ) || true === is_numeric( $this->items[ $item_name ] ) ) ?
			(string) $this->items[ $item_name ] :
			'';
	}

	// Items.

	public function exists( string $item_name ): bool {
		return true === key_exists( $item_name, $this->items );
	}

	/**
	 * @return array<int,int|string>
	 */
	public function keys(): array {
		return array_keys( $this->items );
	}

	/**
	 * @param mixed $item_value
	 */
	public function add( string $item_name, $item_value ): self {
		$this->items[ $item_name ] = $item_value;

		return $this;
	}

	public function remove( string $item_name ): self {
		if ( true === $this->exists( $item_name ) ) {
			unset( $this->items[ $item_name ] );
		}

		return $this;
	}

	/**
	 * @param self|array<string|int, mixed> $origin
	 */
	public function merge( $origin, bool $use_as_defaults = false ): self {
		$origin_array = false === is_array( $origin ) ?
			$origin->items :
			$origin;

		$this->items = false === $use_as_defaults ?
			array_merge( $this->items, $origin_array ) :
			array_merge( $origin_array, $this->items );

		return $this;
	}

	/**
	 * @param self|array<string|int, mixed> $origin
	 */
	public function merge_html_attrs( $origin, bool $use_as_defaults = false ): self {
		$origin_collection = false === is_array( $origin ) ?
			$origin :
			new self( $origin );

		foreach ( $origin_collection->keys() as $origin_key ) {
			if ( false === is_string( $origin_key ) ) {
				continue;
			}

			$is_present   = $this->exists( $origin_key );
			$origin_value = $origin_collection->get_string( $origin_key );

			if ( 'class' === $origin_key &&
			true === $is_present ) {
				$current_classes = explode( ' ', $this->get_string( $origin_key ) );
				$new_classes     = explode( ' ', $origin_value );

				$origin_value = array_unique( array_merge( $current_classes, $new_classes ) );
				$origin_value = join( ' ', $origin_value );

				$is_present = false;
			}

			if ( 'style' === $origin_key &&
			true === $is_present ) {
				$current_styles = explode( ';', $this->get_string( $origin_key ) );
				$new_styles     = explode( ';', $origin_value );

				$origin_value = array_unique( array_merge( $current_styles, $new_styles ) );
				$origin_value = join( ';', $origin_value );

				$is_present = false;
			}

			if ( true === $is_present &&
			true === $use_as_defaults ) {
				continue;
			}

			$this->add( $origin_key, $origin_value );
		}

		return $this;
	}

	public function empty(): bool {
		return array() === $this->items;
	}

	// General.

	public function get_sub_collection( string $item_name ): self {
		// Make an object, so future changes to it will be reflected in the current object as well.
		if ( false === $this->exists( $item_name ) ||
			false === ( $this->items[ $item_name ] instanceof self ) ) {
			$items                     = $this->get_array( $item_name );
			$this->items[ $item_name ] = new self( $items );
		}

		return $this->items[ $item_name ];
	}

	/**
	 * @return array<int|string,mixed>
	 */
	public function to_array(): array {
		$items = $this->items;

		foreach ( $items as $item_name => $item_value ) {
			if ( true === ( $item_value instanceof self ) ) {
				$items[ $item_name ] = $item_value->to_array();
			}
		}

		return $items;
	}

	public function __toString(): string {
		$attributes = array();

		foreach ( $this->items as $item_key => $item_value ) {
			$item_key   = (string) $item_key;
			$item_value = $this->get_string( $item_key );

			$attributes[] = sprintf( '%s="%s"', esc_html( $item_key ), esc_html( $item_value ) );
		}

		$attributes = join( ' ', $attributes );

		return $attributes;
	}
}
