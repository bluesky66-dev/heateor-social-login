<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 */

/**
 * The core plugin class.
 *
 * This is used to define hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 */
class Heateor_Social_Login {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since     1.1.5
	 */
	protected $plugin_slug;

	/**
	 * Current version of the plugin.
	 *
	 * @since     1.1.5
	 */
	protected $version;

	/**
	 * Options saved in database.
	 *
	 * @since     1.1.5
	 */
	public $options;

	/**
	 * Member to assign object of Heateor_Social_Login_Public Class
	 *
	 * @since    1.1.5
	 */
	public $plugin_public;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since     1.1.5
	 */
	public function __construct( $version ) {

		$this->plugin_slug = 'heateor-social-login';
		$this->version = $version;
		$this->options = get_option( 'heateor_sl' );

		$this->load_dependencies();
		$this->set_locale();
		$this->call_admin_hooks();
		$this->call_public_hooks();
	
		$this->define_shortcodes();
		$this->define_widgets();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since     1.1.5
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining all functions for the functionality that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-heateor-social-login-admin.php';

		/**
		 * The class responsible for defining all functions for the functionality that occur at front-end of website.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-heateor-social-login-public.php';

		/**
		 * The class responsible for defining all functions for widgets.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-heateor-social-login-widgets.php';

		/**
		 * The class responsible for defining all shortcode functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-heateor-social-login-shortcodes.php';

	}

	/**
	 * Define the locale for this plugin for internationalization
	 *
	 * @since     1.1.5
	 */
	private function set_locale() {

		load_plugin_textdomain( 'heateor-social-login', false, plugin_dir_path( dirname( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin
	 *
	 * @since     1.1.5
	 */
	private function call_admin_hooks() {

		// create object of admin class to pass options
		$plugin_admin = new Heateor_Social_Login_Admin( $this->options, $this->version );
		add_action( 'admin_menu', array( $plugin_admin, 'admin_menu' ) );
		add_action( 'admin_notices', array( $plugin_admin, 'addon_update_notification' ) );
		add_action( 'admin_init', array( $plugin_admin, 'register_setting' ) );
		add_option( 'heateor_sl_version', HEATEOR_SOCIAL_LOGIN_VERSION );
		if ( isset( $this->options['enableAtLogin'] ) ) {
			add_action( 'login_form', array( $plugin_admin,'buttons' ) );
			add_action( 'bp_before_sidebar_login_form', array( $plugin_admin,'buttons' ) );
		}
		if ( isset( $this->options['enableAtRegister'] ) ) {
			add_action( 'register_form', array( $plugin_admin,'buttons' ) );
			add_action( 'after_signup_form', array( $plugin_admin,'buttons' ) );
			add_action( 'bp_before_account_details_fields', array( $plugin_admin,'buttons' ) ); 
		}
		if ( isset( $this->options['enableAtComment'] ) ) {
			global $user_ID;
			if ( get_option( 'comment_registration' ) && intval( $user_ID ) == 0 ) {
				add_action( 'comment_form_must_log_in_after', array( $plugin_admin, 'buttons' ) );
			} else {
				add_action( 'comment_form_top', array( $plugin_admin, 'buttons' ) );
			}
		}
		if ( isset( $this->options['enable_before_wc'] ) ) {
			add_action( 'woocommerce_before_customer_login_form', array( $plugin_admin, 'buttons' ) );
		}
		if ( isset( $this->options['enable_after_wc'] ) ) {
			add_action( 'woocommerce_login_form', array( $plugin_admin, 'buttons' ) );
		}
		if ( isset( $this->options['enable_register_wc'] ) ) {
			add_action( 'woocommerce_register_form', array( $plugin_admin, 'buttons' ) );
		}
		if ( isset( $this->options['enable_wc_checkout'] ) && $this->options['enable_wc_checkout'] == 1 ) {
			add_action( 'woocommerce_checkout_before_customer_details', array( $plugin_admin, 'buttons' ) );
		}
		add_action( 'plugins_loaded', array( $plugin_admin, 'update_options' ) );
		add_action( 'plugin_action_links_heateor-social-login/heateor-social-login.php', array( $plugin_admin, 'add_settings_link' ) );
		add_action( 'wp_ajax_heateor_sl_unlink', array( $plugin_admin, 'unlink' ) );

		// show options related to GDPR and the options to edit social avatar
		add_action( 'edit_user_profile', array( $plugin_admin, 'show_avatar_option' ) );
		add_action( 'show_user_profile', array( $plugin_admin, 'show_avatar_option' ) );

		// update options related to GDPR and social avatar in the user meta
		add_action( 'personal_options_update', array( $plugin_admin, 'save_avatar' ) );
		add_action( 'edit_user_profile_update', array( $plugin_admin, 'save_avatar' ) );

	}

	/**
	 * Register all of the hooks related to the front-end functionality of the plugin.
	 *
	 * @since     1.1.5
	 */
	private function call_public_hooks() {

		// create object of public class to pass options
		$plugin_public = new Heateor_Social_Login_Public( $this->options, $this->version );
		add_action( 'init', array( $plugin_public, 'init' ) );

		add_filter( 'get_avatar', array( $plugin_public, 'social_avatar' ), 100000, 6 );
		add_filter( 'bp_core_fetch_avatar', array( $plugin_public, 'buddypress_avatar' ), 10, 2 );
		add_filter( 'get_avatar_url', array( $plugin_public, 'social_avatar_url' ), 10, 3 );
		add_action( 'heateor_sl_before_registration', array( $plugin_public, 'disable_social_registration' ), 10, 1 );
		add_action( 'admin_notices', array( $plugin_public, 'user_profile_account_linking' ) );
		add_action( 'bp_setup_nav', array( $plugin_public, 'add_linking_tab' ), 100 );

	}

	/**
	 * Define widgets
	 *
	 * @since     1.1.5
	 */
	private function define_widgets() {

		add_action( 'widgets_init', function() { return register_widget( "HeateorSlLoginWidget" ); } ); 

	}

	/**
	 * Define shortcodes
	 *
	 * @since     1.1.5
	 */
	private function define_shortcodes() {

		$plugin_shortcodes = new Heateor_Social_Login_Shortcodes( $this->options, $this->plugin_public );
		add_shortcode( 'Heateor_Social_Login', array( $plugin_shortcodes, 'shortcode' ) );
	
	}

	/**
	 * Returns the plugin slug
	 *
	 * @since     1.1.5
	 * @return    string    The plugin slug.
	 */
	public function get_plugin_slug() {

		return $this->plugin_slug;
	
	}

	/**
	 * Retrieve the version number of the plugin
	 *
	 * @since     1.1.5
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;
	
	}

}
