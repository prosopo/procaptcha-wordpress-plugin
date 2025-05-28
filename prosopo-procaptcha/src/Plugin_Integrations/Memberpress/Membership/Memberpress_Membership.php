<?php

declare(strict_types=1);

namespace Io\Prosopo\Procaptcha\Plugin_Integrations\Memberpress\Membership;

defined( 'ABSPATH' ) || exit;

use Io\Prosopo\Procaptcha\Query_Arguments;
use function Io\Prosopo\Procaptcha\Vendors\WPLake\Typed\boolExtended;

final class Memberpress_Membership {
	private string $field_name;

	public function __construct( string $field_name ) {
		$this->field_name = $field_name;
	}

	public function print_protection_setting( int $membership_id ): void {
		$checked_attribute = $this->is_protected( $membership_id ) ?
			' checked' :
			'';

		echo '<div style="margin:20px 0 0;">';
		printf(
			'<input type="checkbox" name="%s" id="%1$s" %s/>',
			esc_attr( $this->field_name ),
			esc_attr( $checked_attribute )
		);
		printf(
			'<label for="%s">%s</label>',
			esc_attr( $this->field_name ),
			esc_html__( 'Enable Procaptcha form protection', 'prosopo-procaptcha' )
		);
		echo '</div>';
	}

	public function save_protection_setting( int $membership_id ): void {
		$value = Query_Arguments::get_non_action_bool( $this->field_name, Query_Arguments::POST );

		update_post_meta( $membership_id, $this->field_name, $value );
	}

	public function is_protected( int $membership_id ): bool {
		$enabled_meta_value = get_post_meta( $membership_id, $this->field_name, true );

		return boolExtended( $enabled_meta_value );
	}
}
