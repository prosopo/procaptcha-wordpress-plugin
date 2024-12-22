<?php

declare( strict_types=1 );

namespace Io\Prosopo\Procaptcha;

use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\string;

defined( 'ABSPATH' ) || exit;

class Html_Attributes_Collection {
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

	/**
	 * @param self|array<string|int, mixed> $origin
	 */
	public function merge( $origin, bool $use_as_defaults = false ): self {
		$origin_collection = is_array( $origin ) ?
			new self( $origin ) :
			$origin;

		foreach ( $origin_collection->keys() as $origin_key ) {
			if ( ! is_string( $origin_key ) ) {
				continue;
			}

			$present      = key_exists( $origin_key, $this->items );
			$origin_value = string( $origin_collection->get_items(), $origin_key );

			if ( 'class' === $origin_key &&
			$present ) {
				$current_classes = explode( ' ', string( $this->items, $origin_key ) );
				$new_classes     = explode( ' ', $origin_value );

				$origin_value = array_unique( array_merge( $current_classes, $new_classes ) );
				$origin_value = join( ' ', $origin_value );

				$present = false;
			}

			if ( 'style' === $origin_key &&
			$present ) {
				$current_styles = explode( ';', string( $this->items, $origin_key ) );
				$new_styles     = explode( ';', $origin_value );

				$origin_value = array_unique( array_merge( $current_styles, $new_styles ) );
				$origin_value = join( ';', $origin_value );

				$present = false;
			}

			if ( $present &&
			$use_as_defaults ) {
				continue;
			}

			$this->add( $origin_key, $origin_value );
		}

		return $this;
	}

	public function empty(): bool {
		return 0 === count( $this->items );
	}

	/**
	 * @return array<string|int,mixed>
	 */
	public function get_items(): array {
		return $this->items;
	}

	/**
	 * @return array<int,int|string>
	 */
	protected function keys(): array {
		return array_keys( $this->items );
	}

	/**
	 * @param mixed $item_value
	 */
	protected function add( string $item_name, $item_value ): self {
		$this->items[ $item_name ] = $item_value;

		return $this;
	}

	protected function remove( string $item_name ): self {
		if ( key_exists( $item_name, $this->items ) ) {
			unset( $this->items[ $item_name ] );
		}

		return $this;
	}

	public function __toString(): string {
		$attributes = array();

		foreach ( $this->items as $item_key => $item_value ) {
			$item_key   = (string) $item_key;
			$item_value = string( $this->items, $item_key );

			$attributes[] = sprintf( '%s="%s"', esc_html( $item_key ), esc_html( $item_value ) );
		}

		$attributes = join( ' ', $attributes );

		return $attributes;
	}
}
