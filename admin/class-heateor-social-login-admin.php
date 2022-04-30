<?php

/**
 * Contains functions responsible for functionality at admin side
 *
 * @since      1.0.0
 *
 */

/**
 * This class defines all code necessary for functionality at admin side
 *
 * @since      1.0.0
 *
 */
class Heateor_Social_Login_Admin {

	/**
	 * Options saved in database.
	 *
	 * @since     1.1.5
	 */
	public $options;

	/**
	 * Flag to check if BuddyPress is active
	 *
	 *
	 * @since     1.1.5
	 */
	public $is_bp_active;

	/**
	 * Social Login Options saved in database
	 *
	 * @since     1.1.5
	 */
	private $social_login_options;

	/**
	 * Current version of the plugin
	 *
	 * @since     1.1.5
	 */
	private $version;


	/**
	 * Member to assign object of Heateor_Social_Login_Public Class
	 *
	 * @since    1.1.5
	 */
	public $plugin_public;


	/**
	 * Get saved options
	 *
	 * @since     1.1.5
     * @param    array    $options    Plugin options saved in database
	 */
	public function __construct( $options, $version ) {

		$this->options = $options;
		$this->version = $version;
		$this->social_login_options = get_option( 'heateor_sl_login' );

	}

	/**
	 * Scripts to load at the configuration page
	 *
	 * @since     1.1.5
	 */
	public function admin_scripts() {

		?>
		<script>var heateorSlWebsiteUrl = '<?php echo esc_url( home_url() ) ?>', heateorSlHelpBubbleTitle = "<?php echo __( 'Click to toggle help', 'heateor-social-login' ) ?>";</script>
		<?php
		wp_enqueue_script( 'heateor_sl_script', plugins_url( 'js/heateor-social-login-admin.js', __FILE__ ), array( 'jquery', 'jquery-ui-tabs' ), HEATEOR_SOCIAL_LOGIN_VERSION );
	
	}

	/**
	 * Create menu for the configuration page in the admin area
	 *
	 * @since     1.1.5
	 */
	public function admin_menu() {

		$page = add_menu_page( 'Social Login by Heateor', 'Heateor Social Login', 'manage_options', 'heateor-social-login-options', array( $this, 'options_page' ), plugins_url( 'heateor-social-login/../../images/logo.ico', __FILE__ ) );
		add_action( 'admin_print_scripts-' . $page, array($this, 'admin_scripts' ) );
		add_action( 'admin_print_scripts-' . $page, array($this,'admin_style' ) );
		add_action( 'admin_print_scripts-' . $page, array($this,'fb_sdk_script' ) );
	
	}

	
	/**
	 * Style to load at the configuration page
	 *
	 * @since     1.1.5
	 */
	public function admin_style() {

		wp_enqueue_style( 'heateor-sl-admin-style', plugins_url( 'css/heateor-social-login-admin.css', __FILE__ ), false, HEATEOR_SOCIAL_LOGIN_VERSION );
	
	}

	/**
	 * Load fb_sdk_script at the configuration page
	 *
	 * @since     1.1.5
	 */
	public function fb_sdk_script() {

		wp_enqueue_script( 'fb_sdk_script', plugins_url( 'js/heateor-social-login-fb-sdk.js', __FILE__ ), false, HEATEOR_SOCIAL_LOGIN_VERSION );

	}

	/**
	 * Show notification related to the update of the addon
	 *
	 * @since     1.1.5
	 */
	public function addon_update_notification() {

		if ( current_user_can( 'manage_options' ) ) {
			if ( get_transient( 'heateor-sl-admin-notice-on-activation' ) ) {
				?>
				<div class="notice notice-success is-dismissible">
		            <p><strong><?php printf( __( 'Thanks for installing Heateor Social Login plugin', 'heateor-social-login' ), 'http://support.heateor.com/social-login-configuration' ); ?></strong></p>
		            <p>
						<a href="http://support.heateor.com/heateor-social-login-configuration" target="_blank" class="button button-primary"><?php _e( 'Configure the Plugin', 'heateor-social-login' ); ?></a>
					</p>
		        </div>
				<?php
			}

			if ( defined( 'HEATEOR_SOCIAL_LOGIN_BUTTONS_VERSION' ) && version_compare( '1.2.10', HEATEOR_SOCIAL_LOGIN_BUTTONS_VERSION ) > 0 ) {
				?>
				<div class="error notice">
					<h3>Social Login Buttons</h3>
					<p><?php _e( 'Update "Social Login Buttons" add-on to version 1.2.10 or above for compatibility with the current version of Heateor Social Login', 'heateor-social-login' ) ?></p>
				</div>
				<?php
			}
		}
	
	}

	/**
	 * Update options on updating the plugin
	 *
	 * @since     1.1.7
	 */
	public function update_options() {

		$current_version = get_option( 'heateor_sl_version' );

		if ( $current_version && $current_version != HEATEOR_SOCIAL_LOGIN_VERSION ) {
			if ( version_compare( "1.1.11", $current_version ) > 0 ) {
				$this->options['mailru_client_id'] = '';
				$this->options['mailru_client_secret'] = '';
				update_option( 'heateor_sl', $this->options );
			}
			if ( version_compare( "1.1.9", $current_version ) > 0 ) {
				$this->options['stackoverflow_client_id'] = '';
				$this->options['stackoverflow_client_secret'] = '';
				$this->options['stackoverflow_key'] = '';
				$this->options['amazon_client_id'] = '';
				$this->options['amazon_client_secret'] = '';
				$this->options['discord_client_id'] = '';
				$this->options['discord_client_secret'] = '';
				update_option( 'heateor_sl', $this->options );
			}
			if ( version_compare( "1.1.8", $current_version ) > 0 ) {
				$this->options['reddit_client_id'] = '';
				$this->options['reddit_client_secret'] = '';
				$this->options['disqus_public_key'] = '';
				$this->options['disqus_secret_key'] = '';
				$this->options['foursquare_client_id'] = '';
				$this->options['foursquare_client_secret'] = '';
				$this->options['dropbox_app_key'] = '';
				$this->options['dropbox_app_secret'] = '';
				update_option( 'heateor_sl', $this->options );
			}
			if ( version_compare( "1.1.7", $current_version ) > 0 ) {
				$this->options['twitch_client_id'] = '';
				$this->options['twitch_client_secret'] = '';
				update_option( 'heateor_sl', $this->options );
			}
			update_option( 'heateor_sl_version', HEATEOR_SOCIAL_LOGIN_VERSION );
		}

	}

	/**
	 * Show help links at "Plugins" page in admin area
	 *
	 * @since     1.1.7
	 */
	public function add_settings_link( $links ) {
		
		if ( is_array( $links ) ) {
		    $addons_link = '<br/><a href="https://www.heateor.com/add-ons" target="_blank">' . __( 'Add-Ons', 'heateor-social-login' ) . '</a>';
		    $support_link = '<a href="http://support.heateor.com" target="_blank">' . __( 'Support Documentation', 'heateor-social-login' ) . '</a>';
		    $settings_link = '<a href="admin.php?page=heateor-social-login-options">' . __( 'Settings', 'heateor-social-login' ) . '</a>';
		    // place it before other links
			array_unshift( $links, $settings_link );
			$links[] = $addons_link;
			$links[] = $support_link;
		}

		return $links;

	}

	/**  
	 * Validate options of the plugin
	 *
	 * @since     1.1.5
	 */
	public function validate_plugin_options( $options ) {

		foreach( $options as $k => $v ) {
			if ( is_string( $v ) ) {
				$options[$k] = trim( esc_attr( $v ) );
			}
		}
		return $options;
	
	}

	/**
	 * Check if current page is AMP page
	 *
	 * @since     1.1.5
	 */
	public function is_amp_page() {

		if ( ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) || ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Render Social Login icons HTML
	 *
	 * @since     1.1.5
	 */
	public function buttons( $widget = false ) {

		if ( ! is_user_logged_in() && ! $this->is_amp_page() ) {
			$html='';
			$custom_interface = apply_filters( 'heateor_sl_interface_filter', '', $this->options, $widget );
			if ( $custom_interface !== '' ) {
				$html = $custom_interface;
			} elseif ( isset( $this->options['providers'] ) && is_array( $this->options['providers'] ) && count( $this->options['providers'] ) > 0 ) {
				$html = $this->notifications( $this->options );
				if ( ! $widget ) {
					$html .= '<div class="heateor_sl_outer_login_container">';
					if ( isset( $this->options['title'] ) && $this->options['title'] != '' ) {
						$html .= '<div class="heateor_sl_title">'. $this->options['title'] . '</div>';
					}
				}	
				$html .= '<div class="heateor_sl_login_container">';
				$gdpr_opt_in = '';
				if ( isset( $this->options['gdpr_enable'] ) ) {
					$ppu_link = '<a href="' . $this->options['privacy_policy_url'] . '" target="_blank">' . $this->options['ppu_placeholder'] . '</a>';
					$tc_link = '<a href="' . $this->options['tc_url'] . '" target="_blank">' . $this->options['tc_placeholder'] . '</a>';
					$gdpr_opt_in = '<div class="heateor_sl_optin_container"><label><input type="checkbox" class="heateor_sl_social_login_optin" value="1" />'. str_replace( array( $this->options['ppu_placeholder'], $this->options['tc_placeholder'] ), array( $ppu_link, $tc_link ), wp_strip_all_tags( $this->options['privacy_policy_optin_text'] ) ) . '</label></div>';
				}
				if ( isset( $this->options['gdpr_enable'] ) && $this->options['gdpr_placement'] == 'above' ) {
					$html .= $gdpr_opt_in;
				}
				$html .= '<ul class="heateor_sl_login_ul">';
				if ( isset( $this->options['providers'] ) && is_array( $this->options['providers'] ) && count( $this->options['providers'] ) > 0 ) {
					foreach ( $this->options['providers'] as $provider ) {
						$html.='<li><i ';
						if ( $provider =='google' ) {
								$html .= 'id="heateorSl'. ucfirst( $provider) . 'Button" ';
							}
						$html .= 'class="heateorSlLogin heateorSl'. ucfirst( $provider) . 'Background heateorSl'. ucfirst( $provider) . 'Login" ';
						$html .= 'alt="Login with ';
						$html .= ucfirst( $provider);
						$html .= '" title="Login with ';
						$html .= ucfirst( $provider);
						if (current_filter() == 'comment_form_top' || current_filter() == 'comment_form_must_log_in_after' ) {
							$html .= '" onclick="heateorSlCommentFormLogin = true;heateorSlInitiateLogin( this, \'' . $provider . '\' )" >';
						} else {
							$html .= '" onclick="heateorSlInitiateLogin( this, \'' . $provider . '\' )" >';
						}
						if ( $provider == 'facebook' ) {
							$html .= '<div class="heateorSlFacebookLogoContainer">';
						}
						$html .= '<ss style="display:block" class="heateorSlLoginSvg heateorSl'. ucfirst( $provider) . 'LoginSvg"></ss>';
						if ( $provider == 'facebook' ) {
							$html .= '</div>';
						}
						$html .='</i></li>';
					}
				}
				$html .= '</ul>';
				if ( isset( $this->options['gdpr_enable'] ) && $this->options['gdpr_placement'] == 'below' ) {
					$html .= '<div style="clear:both"></div>';
					$html .= $gdpr_opt_in;
				}
				$html .= '</div>';
				if ( ! $widget ) {
					$html .= '</div><div style="clear:both; margin-bottom: 6px"></div>';
				}
			}
			
			if ( ! $widget ) {
				echo $html;
			} else {
				return $html;
			}
		}
	
	}

	/**
	 * Render configuration page
	 *
	 * @since     1.1.5
	 */
	public function options_page() {

		// message on saving options
		echo $this->settings_saved_notification();
		echo $this->notifications( $this->options);
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/heateor-social-login-options-page.php';
	
	}

	/**
	 * Return error message HTML
	 *
	 * @since     1.1.5
	 */
	public function error_message( $error, $heading = false ) {

		$html = "";
		$html .= "<div class='heateor_sl_error'>";
		if ( $heading) {
			$html .= "<p style='color: black'><strong>Heateor Social Login: </strong></p>";
		}
		$html .= "<p style ='color:red; margin: 0'>" . $error . "</p></div>";
		return $html;
	
	}

	/**
	 * Show social login notifications
	 *
	 * @since     1.1.5
	 */
	public function notifications( $login_options ) {

		$error_html = '';
		if ( isset( $login_options['providers'] ) ) {
			if ( in_array( 'facebook', $login_options['providers'] ) && ( ! isset( $login_options['fb_key'] ) || $login_options['fb_key'] == '' || ! isset( $login_options['fb_secret'] ) || $login_options['fb_secret'] == '' ) ) {
				$error_html .= $this->error_message( __( 'Specify Facebook App ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Facebook Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'twitter', $login_options['providers'] ) && ( ! isset( $login_options['twitter_key'] ) || $login_options['twitter_key'] == '' || ! isset( $login_options['twitter_secret'] ) || $login_options['twitter_secret'] == '' ) ) {
				$error_html .= $this->error_message( __( 'Specify Twitter Consumer Key and Secret in <strong>Heateor Social Login</strong> section in admin panel for Twitter Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'linkedin', $login_options['providers'] ) && ( ! isset( $login_options['li_key'] ) || $login_options['li_key'] == '' || ! isset( $login_options['li_secret'] ) || $login_options['li_secret'] == '' ) ) {
				$error_html .= $this->error_message( __( 'Specify LinkedIn Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for LinkedIn Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'google', $login_options['providers'] ) && ( ! isset( $login_options['google_key'] ) || $login_options['google_key'] == '' || ! isset( $login_options['google_secret'] ) || $login_options['google_secret'] == '' ) ) {
				$error_html .= $this->error_message( __( 'Specify Google Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Google Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'vkontakte', $login_options['providers'] ) && ( ! isset( $login_options['vk_key'] ) || $login_options['vk_key'] == '' || ! isset( $login_options['vk_secure_key'] ) || $login_options['vk_secure_key'] == '' ) ) {
				$error_html .= $this->error_message( __( 'Specify Vkontakte Application ID and Secret Key in <strong>Heateor Social Login</strong> section in admin panel for Vkontakte Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'steam', $login_options['providers'] ) && ( ! isset( $login_options['steam_api_key'] ) || $login_options['steam_api_key'] == '' ) ) {
				$error_html .= $this->error_message( __( 'Specify Steam API Key in <strong>Heateor Social Login</strong> section in admin panel for Steam Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'line', $login_options['providers'] ) && ( ! isset( $login_options['line_channel_id'] ) || $login_options['line_channel_id'] == '' || ! isset( $login_options['line_channel_secret'] ) || $login_options['line_channel_secret'] == '' ) ) {
				$error_html .= $this->error_message( __( 'Specify Line Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Line Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'twitch', $login_options['providers'] ) && ( ! $login_options['twitch_client_id'] || ! $login_options['twitch_client_secret'] ) ) {
				$error_html .= $this->error_message( __( 'Specify Twitch Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Twitch Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'reddit', $login_options['providers'] ) && ( ! $login_options['reddit_client_id'] || ! $login_options['reddit_client_secret'] ) ) {
			    $error_html .= $this->error_message( __( 'Specify Reddit Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Reddit Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'foursquare', $login_options['providers'] ) && ( ! $login_options['foursquare_client_id'] || ! $login_options['foursquare_client_secret'] ) ) {
			    $error_html .= $this->error_message( __( 'Specify Foursquare Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Foursquare Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'disqus', $login_options['providers'] ) && ( ! $login_options['disqus_public_key'] || ! $login_options['disqus_secret_key'] ) ) {
			    $error_html .= $this->error_message( __( 'Specify Disqus Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Disqus Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'dropbox', $login_options['providers'] ) && ( ! $login_options['dropbox_app_key'] || ! $login_options['dropbox_app_secret'] ) ) {
			    $error_html .= $this->error_message( __( 'Specify Dropbox Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Dropbox Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'discord', $login_options['providers'] ) && ( ! $login_options['discord_client_id'] || ! $login_options['discord_client_secret'] ) ) {
			    $error_html .= $this->error_message( __( 'Specify Discord Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Discord Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'stackoverflow', $login_options['providers'] ) && ( ! $login_options['stackoverflow_client_id'] || ! $login_options['stackoverflow_client_secret'] ) ) {
			    $error_html .= $this->error_message( __( 'Specify Stack Overflow Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Stack Overflow Login to work', 'heateor-social-login' ) );
			}
			if ( in_array( 'amazon', $login_options['providers'] ) && ( ! $login_options['amazon_client_id'] || ! $login_options['amazon_client_secret'] ) ) {
			    $error_html .= $this->error_message( __( 'Specify Amazon Client ID and Secret in <strong>Heateor Social Login</strong> section in admin panel for Amazon Login to work', 'heateor-social-login' ) );
			}
		}
		return $error_html;
	
	}

	/**
	 * Display notification message when plugin options are saved
	 *
	 * @since     1.1.5
	 */
	public function settings_saved_notification() {

		if ( isset( $_GET['settings-updated'] ) && sanitize_text_field( $_GET['settings-updated'] ) == 'true' ) {
			return '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible below-h2"> 
	<p><strong>' . __( 'Settings saved', 'heateor-social-login' ) . '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice', 'heateor-social-login' ) . '</span></button></div>';
		}
	
	}

	/**
	 * Check if a plugin is active
	 *
	 * @since     1.1.5
	 */
	public function is_plugin_active( $plugin_file ) {

		return in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	
	}

	/**
	 * Link Social Account
	 *
	 * @since     1.1.5
	 */
	public function link_account( $social_id, $provider, $user_id ) {

		$linked_accounts = get_user_meta( $user_id, 'heateorsl_linked_accounts', true );
		if ( $linked_accounts ) {
			$linked_accounts = maybe_unserialize( $linked_accounts );
		} else {
			$linked_accounts = array();
		}
		$linked_accounts[$provider] = $social_id;
		update_user_meta( $user_id, 'heateorsl_linked_accounts', maybe_serialize( $linked_accounts ) );
	
	}


	/**
	 * register plugin setting
	 *
	 * @since     1.1.5
	 */
	function register_setting() {

		register_setting( 'heateor_sl_options', 'heateor_sl', array( $this, 'validate_plugin_options' ) );
	
	}

	/**
	 * Unlink the social account
	 *
	 * @since     1.1.6
	 */
	public function unlink() {

		if ( isset( $_POST['provider'] ) ) {
			global $user_ID;
			$linked_accounts = get_user_meta( $user_ID, 'heateorsl_linked_accounts', true );
			$primary_social_network = get_user_meta( $user_ID, 'heateorsl_provider', true );
			if ( $linked_accounts || $primary_social_network ) {
				$social_network_to_unlink = sanitize_text_field( $_POST['provider'] );
				$linked_accounts = maybe_unserialize( $linked_accounts );
				$currentSocialId = get_user_meta( $user_ID, 'heateorsl_current_id', true );
				if ( $primary_social_network == $social_network_to_unlink ) {
					if ( $currentSocialId == get_user_meta( $user_ID, 'heateorsl_social_id', true ) ) {
						delete_user_meta( $user_ID, 'heateorsl_current_id' );
					}
					delete_user_meta( $user_ID, 'heateorsl_social_id' );
					delete_user_meta( $user_ID, 'heateorsl_provider' );
					delete_user_meta( $user_ID, 'heateorsl_large_avatar' );
					delete_user_meta( $user_ID, 'heateorsl_avatar' );
				} else {
					if ( $currentSocialId == $linked_accounts[$social_network_to_unlink] ) {
						delete_user_meta( $user_ID, 'heateorsl_current_id' );
					}
					unset( $linked_accounts[$social_network_to_unlink] );
					update_user_meta( $user_ID, 'heateorsl_linked_accounts', maybe_serialize( $linked_accounts ) );
				}
				die( json_encode( array( 'status' => 1, 'message' => '' ) ) );
			}
		}
		die;

	}

	/**
	 * Save social avatar options from profile page
	 *
	 * @since     1.1.10
	 */
	public function save_avatar( $user_id ) {

	 	if ( ! current_user_can( 'edit_user', $user_id ) ) {
	 		return false;
	 	}
	 	if ( isset( $_POST['heateor_sl_gdpr_consent'] ) && $_POST['heateor_sl_gdpr_consent'] == 'no' ) {
			global $wpdb;
			// delete user's social avatar saved in the website locally
			$avatar_path = ABSPATH .'wp-content/uploads/heateor/'. get_user_meta( $user_id, 'heateorsl_social_id', true ) .'.jpeg';
			$large_avatar_path = ABSPATH .'wp-content/uploads/heateor/'. get_user_meta( $user_id, 'heateorsl_social_id', true ) .'_large.jpeg';
			if ( file_exists( $avatar_path ) ) {
				unlink( $avatar_path );
			}
			if ( file_exists( $large_avatar_path ) ) {
				unlink( $large_avatar_path );
			}
			// delete personal data from the user meta 
			$wpdb->query( $wpdb->prepare( 'DELETE FROM '. $wpdb->prefix .'usermeta WHERE user_id = %d and meta_key LIKE "heateorsl%"', $user_id ) );
	 	}
	 	if ( ( ! isset( $_POST['heateor_sl_gdpr_consent'] ) || $_POST['heateor_sl_gdpr_consent'] == 'yes' ) && isset( $_POST['heateor_sl_small_avatar'] ) ) {
	 		update_user_meta( $user_id, 'heateorsl_avatar', esc_url( trim( $_POST['heateor_sl_small_avatar'] ) ) );
	 	}
	 	if ( ( ! isset( $_POST['heateor_sl_gdpr_consent'] ) || $_POST['heateor_sl_gdpr_consent'] == 'yes' ) && isset( $_POST['heateor_sl_large_avatar'] ) ) {
	 		update_user_meta( $user_id, 'heateorsl_large_avatar', esc_url( trim( $_POST['heateor_sl_large_avatar'] ) ) );
	 	}
		if ( isset( $_POST['heateor_sl_dontupdate_avatar'] ) ) {
			update_user_meta( $user_id, 'heateorsl_dontupdate_avatar', intval( $_POST['heateor_sl_dontupdate_avatar'] ) );
		}
		if ( isset( $_POST['heateor_sl_gdpr_consent'] ) ) {
		    update_user_meta( $user_id, 'heateorsl_gdpr_consent', $_POST['heateor_sl_gdpr_consent'] == 'yes' ? 'yes' : 'no' );
	    }
		
	}

	/**
	 * Show Social Avatar options at profile page
	 *
	 * @since     1.1.10
	 */
	public function show_avatar_option( $user ) {

		global $user_ID;
		if ( isset( $this->options['gdpr_enable'] ) ) {
			$gdpr_consent = get_user_meta( $user->ID, 'heateorsl_gdpr_consent', true );
			?>
			<h3><?php _e( 'GDPR', 'heateor-social-login' ) ?></h3>
			<table class="form-table">
				<tr>
		            <th><label for="heateor_sl_gdpr_consent"><?php _e( 'I agree to my personal data being stored and used as per Privacy Policy and Terms and Conditions', 'heateor-social-login' ) ?></label></th>
		        	<td><input id="heateor_sl_gdpr_consent" style="margin-right:5px" type="radio" name="heateor_sl_gdpr_consent" value="yes" <?php echo ! $gdpr_consent || $gdpr_consent == 'yes' ? 'checked' : '' ?> /></td>
		        </tr>
		        <tr>
		            <th><label for="heateor_sl_revoke_gdpr_consent"><?php _e( 'I revoke my consent to store and use my personal data. Kindly delete my personal data saved in this website.', 'heateor-social-login' ) ?></label></th>
		        	<td><input id="heateor_sl_revoke_gdpr_consent" style="margin-right:5px" type="radio" name="heateor_sl_gdpr_consent" value="no" <?php echo $gdpr_consent == 'no' ? 'checked' : '' ?> /></td>
		        </tr>
		    </table>
			<?php
		}
		if ( isset( $this->options['avatar'] ) ) {
			$dont_update_avatar = get_user_meta( $user_ID, 'heateorsl_dontupdate_avatar', true );
			?>
			<h3><?php _e( 'Social Avatar', 'heateor-social-login' ) ?></h3>
			<table class="form-table">
		        <tr>
		            <th><label for="heateor_sl_small_avatar"><?php _e( 'Small Avatar Url', 'heateor-social-login' ) ?></label></th>
		            <td><input id="heateor_sl_small_avatar" type="text" name="heateor_sl_small_avatar" value="<?php echo esc_attr(get_user_meta( $user->ID, 'heateorsl_avatar', true ) ); ?>" class="regular-text" /></td>
		        </tr>
		        <tr>
		            <th><label for="heateor_sl_large_avatar"><?php _e( 'Large Avatar Url', 'heateor-social-login' ) ?></label></th>
		            <td><input id="heateor_sl_large_avatar" type="text" name="heateor_sl_large_avatar" value="<?php echo esc_attr(get_user_meta( $user->ID, 'heateorsl_large_avatar', true ) ); ?>" class="regular-text" /></td>
		        </tr>
		        <tr>
		            <th><label for="heateor_sl_dontupdate_avatar_1"><?php _e( 'Do not fetch and update social avatar from my profile, next time I Social Login', 'heateor-social-login' ) ?></label></th>
		            <td><input id="heateor_sl_dontupdate_avatar_1" style="margin-right:5px" type="radio" name="heateor_sl_dontupdate_avatar" value="1" <?php echo $dont_update_avatar ? 'checked' : '' ?> /></td>
		        </tr>
		        <tr>
		            <th><label for="heateor_sl_dontupdate_avatar_0"><?php _e( 'Update social avatar, next time I Social Login', 'heateor-social-login' ) ?></label></th>
		            <td><input id="heateor_sl_dontupdate_avatar_0" style="margin-right:5px" type="radio" name="heateor_sl_dontupdate_avatar" value="0" <?php echo ! $dont_update_avatar ? 'checked' : '' ?> /></td>
		        </tr>
		    </table>
			<?php
		}
	}
	
}