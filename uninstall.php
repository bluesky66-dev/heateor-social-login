<?php
//if uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
$heateor_sl_options = get_option( 'heateor_sl' );
if ( isset( $heateor_sl_options['delete_options'] ) ) {
	global $wpdb;
	$heateor_sl_options = array(
		'heateor_sl',
		'heateor_sl_version',
		'widget_heateorsllogin'
	);
	// For Multisite
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		// For Multisite
		$heateor_sl_blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		$heateor_sl_original_blog_id = $wpdb->blogid;
		foreach ( $heateor_sl_blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			foreach ( $heateor_sl_options as $option ) {
				delete_site_option( $option );
			}
		}
		switch_to_blog( $heateor_sl_original_blog_id );
	} else {
		foreach ( $heateor_sl_options as $option ) {
			delete_option( $option );
		}
	}
}