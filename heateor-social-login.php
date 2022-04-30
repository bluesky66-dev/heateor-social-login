<?php
/**
 * Plugin bootstrap file
 *
 * @heateor-social-login
 * Plugin Name:       Heateor Social Login
 * Plugin URI:        https://www.heateor.com
 * Description:       Allow website visitors to login with their accounts on Facebook, Twitter, Linkedin, Google, Vkontakte, Steam, Instagram, Line, Microsoft, WordPress, Yahoo, Dribbble, Spotify, Kakao, Twitch, Github, Disqus, Reddit, Dropbox, Foursquare, Discord, Stack Overflow, Amazon and Mail.ru easiest possible way
 * Version:           10000
 * Author:            Team Heateor
 * Author URI:        https://www.heateor.com
 * Text Domain:       heateor-social-login
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die( "Cheating........Uh!!" );

// If this file is called directly, halt
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'HEATEOR_SOCIAL_LOGIN_VERSION', '1.1.16' );
define( 'HEATEOR_SL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Default options when plugin is installed
 *
 * @since     1.1.5
 */
function heateor_sl_save_default_options() {

	// login options
	add_option( 'heateor_sl', array(
		'footer_script' => '1',
		'delete_options' => '1',
		'same_tab_login' => '1',
		'custom_css' => '',
		'title' => __( 'Login with your Social ID', 'heateor-social-login' ),
		'email_error_message' => __( 'Email you entered is already registered or invalid', 'heateor-social-login' ),
		'avatar' => 1,
		'email_required' => 1,
		'password_email' => 1,
		'new_user_admin_email' => 1,
		'email_popup_text' => __( 'Please enter a valid email address. You might be required to verify it', 'heateor-social-login' ),
		'enableAtLogin' => 1,
		'enableAtRegister' => 1,
		'enableAtComment' => 1,
		'scl_title' => __( 'Link your social account to login to your account at this website', 'heateor-social-login' ),
		'link_account' => 1,
		'gdpr_placement' => 'above',
		'privacy_policy_url' => '',
		'privacy_policy_optin_text' => 'I have read and agree to Terms and Conditions of website and agree to my personal data being stored and used as per Privacy Policy',
		'ppu_placeholder' => 'Privacy Policy',
		'tc_placeholder' => 'Terms and Conditions',
		'tc_url' => '',
		'avatar_quality' => 'average',
		'twitch_client_id' => '',
		'twitch_client_secret' => '',
		'reddit_client_id' => '',
		'reddit_client_secret' => '',
		'disqus_public_key' => '',
		'disqus_secret_key' => '',
		'foursquare_client_id' => '',
		'foursquare_client_secret' => '',
		'dropbox_app_key' => '',
		'dropbox_app_secret' => '',
		'discord_client_id' => '',
		'discord_client_secret' => '',
		'amazon_client_id' => '',
		'amazon_client_secret' => '',
		'stackoverflow_client_id' => '',
		'stackoverflow_client_secret' => '',
		'stackoverflow_key' => '',
		'mailru_client_secret' => '',
		'mailru_client_id' => ''
	) );
	add_option( 'heateor_sl_version', HEATEOR_SOCIAL_LOGIN_VERSION );

}

/**
 * Plugin activation function
 *
 * @since     1.1.5
 */
function heateor_sl_activate_plugin( $network_wide ) {

	global $wpdb;
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		// check if it is network activation if so run the activation function for each id
		if ( $network_wide ) {
			$old_blog =  $wpdb->blogid;
			// Get all blog ids
			$blog_ids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			foreach( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				heateor_sl_save_default_options();
			}
			switch_to_blog( $old_blog );
			return;
		}
	}
	heateor_sl_save_default_options();
	set_transient( 'heateor-sl-admin-notice-on-activation', true, 5 );

}
register_activation_hook( __FILE__, 'heateor_sl_activate_plugin' );

/**
 * Save default options for the new subsite created
 *
 * @since     1.1.5
 */
function new_subsite_default_options( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

    if ( is_plugin_active_for_network( 'heateor-social-login/heateor-social-login.php' ) ) { 
        switch_to_blog( $blog_id );
        save_default_options();
        restore_current_blog();
    }

}
add_action( 'wpmu_new_blog', 'new_subsite_default_options', 10, 6 );

/**
 * Main class of the plugin
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-heateor-social-login.php';

/**
 * Begins execution of the plugin
 *
 * @since     1.1.5
 */
function run() {

	$heateor_sl = new Heateor_Social_Login( HEATEOR_SOCIAL_LOGIN_VERSION );

}
run();

register_activation_hook( __FILE__, 'activate_plugin' );