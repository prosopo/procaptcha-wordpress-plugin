<?php

define( 'PROSOPO_PROCAPTCHA_ALLOW_BYPASS', true );

/**
 * Reserved Prosopo test site keys, see https://docs.prosopo.io/en/basics/test-keys/
 *
 * They're constant across all the Prosopo environments, require no registration,
 * and their requests aren't billed, so the test suite doesn't depend on any personal account.
 *
 * The always-pass key is used, as the always-fail one emits no token at all,
 * while the suite needs to submit both valid and invalid tokens.
 * The secret is ignored for the reserved keys, but it must be non-empty
 * for the plugin to consider itself configured.
 */
define( 'PROSOPO_PROCAPTCHA_TEST_SITE_KEY', '5EARALUe4HXQwKo5KanZSGGKqJV4VTaytpezFwv8ZHbZewmh' );
define( 'PROSOPO_PROCAPTCHA_TEST_SECRET_KEY', 'test-secret-key-is-ignored-for-reserved-site-keys' );

add_filter(
	'option_prosopo-procaptcha__settings',
	function ( $settings ) {
		$settings = is_array( $settings ) ? $settings : array();

		$settings['site_key']   = PROSOPO_PROCAPTCHA_TEST_SITE_KEY;
		$settings['secret_key'] = PROSOPO_PROCAPTCHA_TEST_SECRET_KEY;

		return $settings;
	},
	999
);

add_filter( 'comment_flood_filter', '__return_false', 999 );
add_filter( 'pre_wp_mail', '__return_true' );
add_filter( 'bbp_bypass_check_for_flood', '__return_true' );
add_filter( 'wpforms_process_time_limit_check_bypass', '__return_true' );
add_filter( 'admin_email_check_interval', '__return_zero' );
add_filter(
	'swpm_get_current_page_url_filter',
	function ( string $url ): string {
		$current_host = wp_parse_url( home_url(), PHP_URL_HOST );

		return str_replace( '/localhost/', '/' . $current_host . '/', $url );
	}
);

add_action(
	'fluentform/before_submission_confirmation',
	function ( $insertId, $formData, $form ) {
		\FluentForm\App\Models\Submission::remove( array( $insertId ), $form->id );
	},
	10,
	3
);
