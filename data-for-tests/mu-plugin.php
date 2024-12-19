<?php

define( 'PROSOPO_PROCAPTCHA_ALLOW_BYPASS', true );

add_filter( 'comment_flood_filter', '__return_false', 999 );
add_filter( 'pre_wp_mail', '__return_true' );
add_filter( 'bbp_bypass_check_for_flood', '__return_true' );
add_filter( 'wpforms_process_time_limit_check_bypass', '__return_true' );
add_filter( 'jetpack_gutenberg', '__return_true' );

add_action(
	'fluentform/before_submission_confirmation',
	function ( $insertId, $formData, $form ) {
		\FluentForm\App\Models\Submission::remove( array( $insertId ) );
	},
	10,
	3
);
