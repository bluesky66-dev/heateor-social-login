<?php

/**
 * Contains functions responsible for functionality at front-end of website
 *
 * @since      1.0.0
 *
 */

/**
 * This class defines all code necessary for functionality at front-end of website
 *
 * @since      1.0.0
 *
 */
class Heateor_Social_Login_Public {

	/**
	 * Options saved in the database.
	 *
	 * @since     1.1.5
	 */
	private $options;

	/**
	 * Flags
	 *
	 * @since     1.1.5
	 */
	private $vertical_home_count = 0, $vertical_excerpt_count = 0, $vertical_counter_homeCount = 0, $vertical_counter_excerptCount = 0;

	/**
	 * Current version of the plugin.
	 *
	 * @since     1.1.5
	 */
	public $version;

	/**
	 * Short urls calculated for current webpage.
	 *
	 * @since     1.1.5
	 */
	private $short_urls = array();

	/**
	 * Share Count Transient ID
	 *
	 * @since    1.7
	 */
	public $share_count_transient_id = '';

	/**
	 * Get saved options.
	 *
	 * @since     1.1.5
     * @param    array     $options    Plugin options saved in database
     * @param    string    $version    Current version of the plugin
	 */
	public function __construct( $options, $version ) {

		$this->options = $options;
		$this->version = $version;
		$this->social_login_options = get_option( 'heateor_sl_login' );

	}

	/**
	 * Check if a user is admin
	 *
	 * @since    1.1.9
	 */
	private function check_if_admin( $user_id ) {

		if ( isset( $this->options['disable_sl_admin'] ) ) {
			$user = get_userdata( $user_id );
			if ( ! empty( $user ) && is_array( $user->roles ) ) {
				if ( in_array( 'administrator', $user->roles ) ) {
					return true;
				}
			}
		}

		return false;

	}

	/**
	 * Trigger authentication via social login
	 *
	 * @since     1.1.5
	 */
	public function connect() {
		
		if ( isset( $_POST['heateor_sl_email_submit'] ) ) {
			$unique_id = sanitize_text_field( trim( $_POST['heateor_sl_unique_id'] ) );
			$profile_data = maybe_unserialize( get_user_meta( $unique_id, 'heateor_sl_temp_data', true ) );
			$email = sanitize_email( trim( $_POST['heateor_sl_email'] ) );

			$error_msg = '';
		 	if ( ! is_email( $email ) ) {
		 		$error_msg = __( 'Email address is not valid', 'heateor-social-login' );
		 	}
		 	if ( email_exists( $email ) ) {
		 		$error_msg = __( "This email already exists. Please specify another email", 'heateor-social-login' );
		 	}
		 	if ( $error_msg != '' ) {
			 	?>
	            <div id="heateor_sl_popup_bg"></div>
			 	<div id="heateor_sl_sharing_more_providers"><button id="heateor_sl_sharing_popup_close" onclick="jQuery(this).parent().prev().remove();jQuery(this).parent().remove();" class="close-button separated"><img src="<?php echo plugins_url( '../images/close.png', __FILE__ ) ?>" /></button><div id="heateor_sl_sharing_more_content"><div class="filter"></div><div class="all-services">
		 		<form action="<?php echo esc_url( home_url() ) . '/index.php'; ?>" method="post">
					<div><?php echo esc_html( $this->options['email_popup_text'] ); ?></div>
					<div style="color:red"><?php echo $error_msg; ?></div>
					<input name="heateor_sl_email" id="heateor_sl_email" value="<?php echo $email; ?>" placeholder="<?php _e( 'Email', 'heateor-social-login' ); ?>" type="text"/>
					<input name="heateor_sl_unique_id" value="<?php echo $unique_id; ?>" type="hidden"/>
					<input class="btn btn-default" type="submit" name="heateor_sl_email_submit" value="<?php _e( 'Submit', 'heateor-social-login' ) ?>"/>
				</form>
			 	</div></div>
			 	</div>
			 	<?php
			 	return;
			}
			$profile_data['email'] = $email;
			$user_id = $this->create_user( $profile_data );
			if ( $user_id ) {
				$error = $this->login_user( $user_id, $profile_data, $profile_data['id'], false ); 
				if ( isset( $error ) && $error === 0 ) {
					$response = array( 'status' => false, 'message' => 'inactive', 'url' => wp_login_url() . '?loggedout=true&hum=1' );
				} elseif ( isset( $this->options['register_redirection'] ) && $this->options['register_redirection'] == 'bp_profile' ) {
					$response = array( 'status' => true, 'message' => 'register', 'url' => bp_core_get_user_domain( $user_id ) );
				} else {
					$response = array( 'status' => true, 'message' => 'register' );
				}
			
			}

			$this->php_session_start_resume();
			$redirect_url = esc_url( home_url() );
			if ( isset( $_SESSION['heateor_sl_' . $profile_data['provider'] . '_redirect'] ) ) {
				$redirect_url = isset( $_SESSION['heateor_sl_' . $profile_data['provider'] . '_redirect'] ) && $_SESSION['heateor_sl_' . $profile_data['provider'] . '_redirect'] ? esc_url( trim( $_SESSION['heateor_sl_' . $profile_data['provider'] . '_redirect'] ) ) : esc_url( home_url() );
			} elseif( isset( $profile_data['state'] ) && $profile_data['state'] && ( $redirect_url = get_user_meta( $profile_data['state'], 'heateor_sl_redirect_to', true ) ) !== false ) {
				$redirect_url = $redirect_url;
				delete_user_meta( $profile_data['state'], 'heateor_sl_redirect_to' );
			}

			// unset PHP sessions
			$this->unset_php_session( 'heateor_sl_' . $profile_data['provider'] . '_redirect' );
			$this->unset_php_session( 'heateor_sl_' . $profile_data['provider'] . '_state' );
			$this->unset_php_session( 'heateor_sl_redirect_to' );
			
			// close social login popup and redirect user
			if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
				$redirect_to =$this->get_login_redirection_url( $redirect_url, true );
			} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
				$redirect_to = $redirect_url . ( strpos( $redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
			} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
				$redirect_to = $redirect_url . ( strpos( $redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
			} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
				$redirect_to = $response['url'];
			} else {
				$redirect_to = $this->get_login_redirection_url( $redirect_url );
			}
			$this->close_login_popup( $redirect_to );
		}
		
		// Steam auth
		if ( isset( $_GET['HeateorSlSteamAuth'] ) && trim( $_GET['HeateorSlSteamAuth'] ) != '' && isset( $this->options['steam_api_key'] ) && $this->options['steam_api_key'] != '' ) {
			global $heateor_sl_steam_login;
			try {
				$heateor_sl_steam_id = $heateor_sl_steam_login->validate();
			}
			catch ( Exception $e ) {
				die( $e->getMessage() );
			}
			$result = wp_remote_get( "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . $this->options['steam_api_key'] . "&steamids=" . $heateor_sl_steam_id . "/?xml=1", array(
				 'timeout' => 15 
			) );
			if ( ! is_wp_error( $result ) && isset( $result['response']['code'] ) && 200 === $result['response']['code'] ) {
				$data = json_decode( wp_remote_retrieve_body( $result ) );
				if ( $data && isset( $data->response ) && isset( $data->response->players ) && is_array( $data->response->players ) ) {
					$steam_profile_data = $data->response->players;
					if ( isset( $steam_profile_data[0] ) && isset( $steam_profile_data[0]->steamid ) ) {
						$steam_redirect = $this->validate_url( $_GET['HeateorSlSteamAuth'] ) !== false ? esc_url( trim( $_GET['HeateorSlSteamAuth'] ) ) : '';
						$profile_data   = $this->sanitize_profile_data( $steam_profile_data[0], 'steam' );
						if ( strpos( $steam_redirect, 'heateorMSEnabled' ) !== false ) {
							$profile_data['mc_subscribe'] = 1;
						}
						$response = $this->user_auth( $profile_data, 'steam', $steam_redirect );
						if ( $response == 'show form' ) {
							return;
						}
						if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
							$redirect_to = $this->get_login_redirection_url( $steam_redirect, true );
						} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
							$redirect_to = $steam_redirect . ( strpos( $steam_redirect, '?' ) !== false ? '&' : '?' ) . 'linked=1';
						} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
							$redirect_to = $steam_redirect . ( strpos( $steam_redirect, '?' ) !== false ? '&' : '?' ) . 'linked=0';
						} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
							$redirect_to = $response['url'];
						} else {
							$redirect_to = $this->get_login_redirection_url( $steam_redirect );
						}
						$this->close_login_popup( $redirect_to );
					}
				}
			}
			die;
		}

		// spotify
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Spotify' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'spotify', $this->options['providers'] ) && isset( $this->options['spotify_channel_id'] ) && $this->options['spotify_channel_id'] != '' && isset( $this->options['spotify_channel_secret'] ) && $this->options['spotify_channel_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) ) {
					$spotify_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $spotify_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://accounts.spotify.com/authorize?client_id=" . $this->options['spotify_channel_id'] . "&scope=user-read-private%20user-read-email&response_type=code&state=" . $spotify_login_state . "&redirect_uri=" . home_url() . "/HeateorSlAuth/Spotify" );
				die;
			}
			
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			'code',
			'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Spotify' ) {
			$spotify_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $spotify_redirect_url = get_user_meta( $spotify_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				'grant_type' => 'authorization_code',
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Spotify",
				'client_id' => $this->options['spotify_channel_id'],
				'client_secret' => $this->options['spotify_channel_secret'] 
			);
			$response = wp_remote_post( "https://accounts.spotify.com/api/token", array(
				'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body = json_decode( wp_remote_retrieve_body( $response ) );
				if ( isset( $body->access_token ) ) {
					$authorization = "Bearer " . $body->access_token;
					$response      = wp_remote_get( 'https://api.spotify.com/v1/me', array(
						 'timeout' => 15,
						'headers' => array(
							 'Accept' => 'application/json',
							'Authorization' => $authorization 
						) 
					) );
					if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
						$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
						if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
							$profile_data          = $this->sanitize_profile_data( $profile_data, 'spotify' );
							$profile_data['state'] = $spotify_login_state;
							$response = $this->user_auth( $profile_data, 'spotify', $spotify_redirect_url );
							if ( $response == 'show form' ) {
								return;
							}
							delete_user_meta( $spotify_login_state, 'heateor_sl_redirect_to', true );
							if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
								$redirect_to = $this->get_login_redirection_url( $live_redirect_url, true );
							} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
								$redirect_to = $spotify_redirect_url . ( strpos( $spotify_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
							} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
								$redirect_to = $spotify_redirect_url . ( strpos( $spotify_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
							} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
								$redirect_to = $response['url'];
							} else {
								$redirect_to = $this->get_login_redirection_url( $spotify_redirect_url );
							}
							$this->close_login_popup( $redirect_to );
						}
					}
				}
			}
		}

		// Wordpress
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Wordpress' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'wordpress', $this->options['providers'] ) && isset( $this->options['wordpress_channel_id'] ) && $this->options['wordpress_channel_id'] != '' && isset( $this->options['wordpress_channel_secret'] ) && $this->options['wordpress_channel_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) ) {
					$wordpress_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $wordpress_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://public-api.wordpress.com/oauth2/authorize?client_id=" . $this->options['wordpress_channel_id'] . "&scope=auth&state=" . $wordpress_login_state . "&response_type=code&redirect_uri=" . home_url() . "/HeateorSlAuth/Wordpress" );
				die;
			}
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			'code',
			'state' 
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Wordpress' ) {
			$wordpress_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $wordpress_redirect_url = get_user_meta( $wordpress_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				 'grant_type' => 'authorization_code',
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Wordpress",
				'client_id' => $this->options['wordpress_channel_id'],
				'client_secret' => $this->options['wordpress_channel_secret'] 
			);
			$response = wp_remote_post( "https://public-api.wordpress.com/oauth2/token", array(
				 'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body          = json_decode( wp_remote_retrieve_body( $response ) );
				$authorization = "Bearer " . $body->access_token;
				$response      = wp_remote_get( "https://public-api.wordpress.com/rest/v1/me/", array(
					'timeout' => 15,
					'headers'  => array(
						'Accept'       => 'application/json',
						'Authorization' => $authorization 
					) 
				) );
				if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( is_object( $profile_data ) && isset( $profile_data->ID ) ) {
						$profile_data          = $this->sanitize_profile_data( $profile_data, 'wordpress' );
						$profile_data['state'] = $wordpress_login_state;
						$response = $this->user_auth( $profile_data, 'wordpress', $wordpress_redirect_url );
						if ( $response == 'show form' ) {
							return;
						}
						delete_user_meta( $wordpress_login_state, 'heateor_sl_redirect_to', true );
						if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
							$redirect_to = $this->get_login_redirection_url( $wordpress_redirect_url, true );
						} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
							$redirect_to = $wordpress_redirect_url . ( strpos( $wordpress_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
						} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
							$redirect_to = $wordpress_redirect_url . ( strpos( $wordpress_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
						} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
							$redirect_to = $response['url'];
						} else {
							$redirect_to = $this->get_login_redirection_url( $wordpress_redirect_url );
						}
						$this->close_login_popup( $redirect_to );
					}
				}
			}
		}

		// kakao
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Kakao' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'kakao', $this->options['providers'] ) && isset( $this->options['kakao_channel_id'] ) && $this->options['kakao_channel_id'] != '' && isset( $this->options['kakao_channel_secret'] ) && $this->options['kakao_channel_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) ) {
					$kakao_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $kakao_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://kauth.kakao.com/oauth/authorize?client_id=" . $this->options['kakao_channel_id'] . "&state=" . $kakao_login_state . "&response_type=code&redirect_uri=" . home_url() . "/HeateorSlAuth/Kakao" );
				die;
			}
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			'code',
			'state' 
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Kakao' ) {
			$kakao_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $kakao_redirect_url = get_user_meta( $kakao_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				 'grant_type' => 'authorization_code',
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Kakao",
				'client_id' => $this->options['kakao_channel_id'],
				'client_secret' => $this->options['kakao_channel_secret'] 
			);
			$response = wp_remote_post( "https://kauth.kakao.com/oauth/token", array(
				 'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body = json_decode( wp_remote_retrieve_body( $response ) );
				if ( isset( $body->access_token ) ) {
					$authorization = "Bearer " . $body->access_token;
					$response = wp_remote_get( 'https://kapi.kakao.com/v2/user/me', array(
						 'timeout' => 15,
						'headers' => array(
							 'Accept' => 'application/json',
							'Authorization' => $authorization 
						) 
					) );
					if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
						$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
						if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
							$profile_data          = $this->sanitize_profile_data( $profile_data, 'kakao' );
							$profile_data['state'] = $kakao_login_state;
							$response = $this->user_auth( $profile_data, 'kakao', $kakao_redirect_url );
							if ( $response == 'show form' ) {
								return;
							}
							delete_user_meta( $kakao_login_state, 'heateor_sl_redirect_to', true );
							if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
								$redirect_to = $this->get_login_redirection_url( $live_redirect_url, true );
							} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
								$redirect_to = $kakao_redirect_url . ( strpos( $kakao_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
							} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
								$redirect_to = $kakao_redirect_url . ( strpos( $kakao_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
							} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
								$redirect_to = $response['url'];
							} else {
								$redirect_to = $this->get_login_redirection_url( $kakao_redirect_url );
							}
							$this->close_login_popup( $redirect_to );
						}
					}
				}
			}
		}

		// yahoo
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Yahoo' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'yahoo', $this->options['providers'] ) && isset( $this->options['yahoo_channel_id'] ) && $this->options['yahoo_channel_id'] != '' && isset( $this->options['yahoo_channel_secret'] ) && $this->options['yahoo_channel_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) ) {
					$yahoo_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $yahoo_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://api.login.yahoo.com/oauth2/request_auth?client_id=" . $this->options['yahoo_channel_id'] . "&response_type=code&state=" . $yahoo_login_state . "&language=en-us&redirect_uri=" . home_url() . "/HeateorSlAuth/Yahoo" );
				die;
			}
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			'code',
			'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Yahoo' ) {
			$yahoo_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $yahoo_redirect_url = get_user_meta( $yahoo_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				 'grant_type' => 'authorization_code',
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Yahoo",
				'client_id' => $this->options['yahoo_channel_id'],
				'client_secret' => $this->options['yahoo_channel_secret'] 
			);
			$response = wp_remote_post( "https://api.login.yahoo.com/oauth2/get_token", array(
				 'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body          = json_decode( wp_remote_retrieve_body( $response ) );
				$authorization = "Bearer " . $body->access_token;
				$response      = wp_remote_get( "https://api.login.yahoo.com/openid/v1/userinfo", array(
					 'timeout' => 15,
					'headers' => array(
						 'Accept' => 'application/json',
						'Authorization' => $authorization 
					) 
				) );
				if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( is_object( $profile_data ) && isset( $profile_data->sub ) ) {
						$profile_data          = $this->sanitize_profile_data( $profile_data, 'yahoo' );
						$profile_data['state'] = $yahoo_login_state;
						$response = $this->user_auth( $profile_data, 'yahoo', $yahoo_redirect_url );
						if ( $response == 'show form' ) {
							return;
						}
						delete_user_meta( $yahoo_login_state, 'heateor_sl_redirect_to', true );
						if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
							$redirect_to = $this->get_login_redirection_url( $yahoo_redirect_url, true );
						} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
							$redirect_to = $yahoo_redirect_url . ( strpos( $yahoo_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
						} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
							$redirect_to = $yahoo_redirect_url . ( strpos( $yahoo_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
						} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
							$redirect_to = $response['url'];
						} else {
							$redirect_to = $this->get_login_redirection_url( $yahoo_redirect_url );
						}
						$this->close_login_popup( $redirect_to );
					}
				}
			}
		}

		// discord
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Discord' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'discord', $this->options['providers'] ) && $this->options['discord_client_id'] && $this->options['discord_client_secret'] ) {
				if ( ! isset( $_GET['code'] ) ) {
					$discord_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $discord_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://discord.com/oauth2/authorize/request_auth?client_id=" . $this->options['discord_client_id'] . "&response_type=code&state=" . $discord_login_state . "&scope=identify%20email&redirect_uri=" . home_url() . "/HeateorSlAuth/Discord" );
				die;
			}
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array( 
			'code',
			'scope',
			'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Discord' ) {
			$discord_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $discord_redirect_url = get_user_meta( $discord_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				'grant_type' => 'authorization_code',
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Discord",
				'client_id' => $this->options['discord_client_id'],
				'client_secret' => $this->options['discord_client_secret'],
				'scope' => 'identify%20email' 
			);
			$response = wp_remote_post( "https://discord.com/api/oauth2/token", array(
				'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body          = json_decode( wp_remote_retrieve_body( $response ) );
				$authorization = "Bearer " . $body->access_token;
				$response      = wp_remote_get( "https://discordapp.com/api/users/@me", array(
					'timeout' => 15,
					'headers' => array(
						'Accept' => 'application/json',
						'Authorization' => $authorization 
					)
				) );

				if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( is_object( $profile_data ) && isset( $profile_data->id ) && isset( $profile_data->verified ) && $profile_data->verified == 1 ) {
						$profile_data          = $this->sanitize_profile_data( $profile_data, 'discord' );
						$profile_data['state'] = $discord_login_state;
						$response = $this->user_auth( $profile_data, 'discord', $discord_redirect_url );
						if ( $response == 'show form' ) {
							return;
						}
						delete_user_meta( $discord_login_state, 'heateor_sl_redirect_to', true );
						if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
							$redirect_to = $this->get_login_redirection_url( $discord_redirect_url, true );
						} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
							$redirect_to = $discord_redirect_url . ( strpos( $discord_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
						} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
							$redirect_to = $discord_redirect_url . ( strpos( $discord_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
						} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
							$redirect_to = $response['url'];
						} else {
							$redirect_to = $this->get_login_redirection_url( $discord_redirect_url );
						}
						$this->close_login_popup( $redirect_to );
					}
				}
			}
		}

		// amazon
		if ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Amazon' ) {
			if ( isset( $this->options['providers'] ) && in_array( 'amazon', $this->options['providers'] ) && $this->options['amazon_client_id'] && $this->options['amazon_client_secret'] ) {
				if ( ! isset( $_GET['code'] ) ) {
					$amazon_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $amazon_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://www.amazon.com/ap/oa?client_id=" . $this->options['amazon_client_id'] . "&response_type=code&state=" . $amazon_login_state . "&scope=profile&redirect_uri=" . urlencode( home_url() . "/HeateorSlAuth/Amazon" ) );
				die;
			}
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			 'code',
			'scope',
			'state' 
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Amazon' ) {
			$amazon_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $amazon_redirect_url = get_user_meta( $amazon_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				 'grant_type' => 'authorization_code',
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Amazon",
				'client_id' => $this->options['amazon_client_id'],
				'client_secret' => $this->options['amazon_client_secret'] 
			);
			$response = wp_remote_post( "https://api.amazon.com/auth/o2/token", array(
				 'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body          = json_decode( wp_remote_retrieve_body( $response ) );
				$authorization = "Bearer " . $body->access_token;
				$response      = wp_remote_get( "https://api.amazon.com/user/profile", array(
					'timeout' => 15,
					'headers' => array(
						'Accept' => 'application/json',
						'Authorization' => $authorization 
					) 
				) );

				if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( is_object( $profile_data ) && isset( $profile_data->user_id ) ) {
						$profile_data          = $this->sanitize_profile_data( $profile_data, 'amazon' );
						$profile_data['state'] = $amazon_login_state;
						$response 			   = $this->user_auth( $profile_data, 'amazon', $amazon_redirect_url );
						if ( $response == 'show form' ) {
							return;
						}
						delete_user_meta( $amazon_login_state, 'heateor_sl_redirect_to', true );
						if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
							$redirect_to = $this->get_login_redirection_url( $amazon_redirect_url, true );
						} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
							$redirect_to = $amazon_redirect_url . ( strpos( $amazon_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
						} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
							$redirect_to = $amazon_redirect_url . ( strpos( $amazon_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
						} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
							$redirect_to = $response['url'];
						} else {
							$redirect_to = $this->get_login_redirection_url( $amazon_redirect_url );
						}
						$this->close_login_popup( $redirect_to );
					}
				}
			}
		}

		// Stack Overflow
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Stackoverflow' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'stackoverflow', $this->options['providers'] ) && $this->options['stackoverflow_client_id'] && $this->options['stackoverflow_client_secret'] ) {
				if ( ! isset( $_GET['code'] ) ) {
					$stackoverflow_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $stackoverflow_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://stackexchange.com/oauth?client_id=" . $this->options['stackoverflow_client_id'] . "&scope=private_info&response_type=code&state=" . $stackoverflow_login_state . "&redirect_uri=" . home_url() . "/HeateorSlAuth/Stackoverflow" );
		        die;
		    }
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		    'code',
		    'scope',
		    'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Stackoverflow' ) {
			$stackoverflow_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $stackoverflow_redirect_url = get_user_meta( $stackoverflow_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
		    $post_data = array(
		        'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Stackoverflow",
		        'client_id' => $this->options['stackoverflow_client_id'],
		        'client_secret' => $this->options['stackoverflow_client_secret']
		    );
		    $response = wp_remote_post( "https://stackexchange.com/oauth/access_token", array(
		        'method' => 'POST',
		        'timeout' => 15,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->options['stackoverflow_client_id'] . ':' . $this->options['stackoverflow_client_secret'] ) 
				),
		        'body' => http_build_query( $post_data ) 
		    ) );
		    
		    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		        $body     				   = wp_remote_retrieve_body( $response );
		        $json_response 		       = explode( '&', $body );
		        $access_token_parts        = explode( '=', $json_response[0] );
		        $response 				   = wp_remote_get( "https://api.stackexchange.com/2.2/me?site=stackoverflow&access_token=" . $access_token_parts[1] . "&key=" . $this->options['stackoverflow_key'], array(
		            'timeout' => 15,
		            'headers' => array( 'content-type' =>'application/json' )
		        ) ); 

		        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
		            if ( is_object( $profile_data ) && isset( $profile_data->items[0]->account_id ) ) {
		                $profile_data 		   = $this->sanitize_profile_data( $profile_data->items[0], 'stackoverflow' );
		                $profile_data['state'] = $stackoverflow_login_state;
		                $response = $this->user_auth( $profile_data, 'stackoverflow', $stackoverflow_redirect_url );
		                if ( $response == 'show form' ) {
		                    return;
		                }
		                delete_user_meta( $stackoverflow_login_state, 'heateor_sl_redirect_to', true );
		                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
		                    $redirect_to = $this->get_login_redirection_url( $stackoverflow_redirect_url, true );
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
		                    $redirect_to = $stackoverflow_redirect_url . ( strpos( $stackoverflow_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
		                    $redirect_to = $stackoverflow_redirect_url . ( strpos( $stackoverflow_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
		                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
		                    $redirect_to = $response['url'];
		                } else {
		                    $redirect_to = $this->get_login_redirection_url( $stackoverflow_redirect_url );
		                }
		                $this->close_login_popup( $redirect_to );
		            }
		        }
		    }
		}

		// Dribbble
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Dribbble' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'dribbble', $this->options['providers'] ) && isset( $this->options['dribbble_channel_id'] ) && $this->options['dribbble_channel_id'] != '' && isset( $this->options['dribbble_channel_secret'] ) && $this->options['dribbble_channel_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) ) {
					$dribbble_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $dribbble_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://dribbble.com/oauth/authorize?client_id=" . $this->options['dribbble_channel_id'] . "&scope=public&state=" . $dribbble_login_state . "&redirect_uri=" . urlencode( home_url() . "/HeateorSlAuth/Dribbble" ) );
				die;
			}
			
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			'code',
			'scope',
			'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Dribbble' ) {
			$dribbble_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $dribbble_redirect_url = get_user_meta( $dribbble_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				 'grant_type' => 'authorization_code',
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Dribbble",
				'client_id' => $this->options['dribbble_channel_id'],
				'client_secret' => $this->options['dribbble_channel_secret'] 
			);
			$response = wp_remote_post( "https://dribbble.com/oauth/token", array(
				 'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body          = json_decode( wp_remote_retrieve_body( $response ) );
				$authorization = "Bearer " . $body->access_token;
				$response      = wp_remote_get( "https://api.dribbble.com/v2/user?access_token", array(
					 'timeout' => 15,
					'headers' => array(
						 'Accept' => 'application/json',
						'Authorization' => $authorization 
					) 
				) );
				if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
						$profile_data      	   = $this->sanitize_profile_data( $profile_data, 'dribbble' );
						$profile_data['state'] = $dribbble_login_state;
						$response = $this->user_auth( $profile_data, 'dribbble', $dribbble_redirect_url );
						if ( $response == 'show form' ) {
							return;
						}
						delete_user_meta( $dribbble_login_state, 'heateor_sl_redirect_to', true );
						if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
							$redirect_to = $this->get_login_redirection_url( $dribbble_redirect_url, true );
						} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
							$redirect_to = $dribbble_redirect_url . ( strpos( $dribbble_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
						} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
							$redirect_to = $dribbble_redirect_url . ( strpos( $dribbble_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
						} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
							$redirect_to = $response['url'];
						} else {
							$redirect_to = $this->get_login_redirection_url( $dribbble_redirect_url );
						}
						$this->close_login_popup( $redirect_to );
					}
				}
			}
		}

		// Instagram 
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Instagram' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'instagram', $this->options['providers'] ) && isset( $this->options['instagram_channel_id'] ) && $this->options['instagram_channel_id'] != '' && isset( $this->options['instagram_channel_secret'] ) && $this->options['instagram_channel_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) ) {
					$instagram_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $instagram_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://api.instagram.com/oauth/authorize?client_id=" . $this->options['instagram_channel_id'] . "&scope=user_profile,user_media&response_type=code&state=" . $instagram_login_state . "&language=en-us&redirect_uri=" . urlencode( home_url() . "/HeateorSlAuth/Instagram" ) );
				die;
			}
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			'code',
			'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Instagram' ) {
			$instagram_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $instagram_redirect_url = get_user_meta( $instagram_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				'client_id' => $this->options['instagram_channel_id'],
				'client_secret' => $this->options['instagram_channel_secret'],
				'grant_type' => 'authorization_code',
				'redirect_uri' => home_url() . '/HeateorSlAuth/Instagram',
				'code' => $_GET['code'] 
			);
			$response = wp_remote_post( "https://api.instagram.com/oauth/access_token", array(
				'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded' 
				),
				'body' => http_build_query( $post_data ) 
			) );
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body = json_decode( wp_remote_retrieve_body( $response ) );
				if ( isset( $body->access_token ) ) {
					$authorization = "Bearer " . $body->access_token;
					$response      = wp_remote_get( 'https://graph.instagram.com/' . $body->user_id . '?fields=account_type,id,username&access_token=' . $body->access_token, array(
						 'timeout' => 15 
					) );
					if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
						$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
						if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
							$profile_data           = $this->sanitize_profile_data( $profile_data, 'instagram' );
							$profile_data['state']  = $instagram_login_state;
							$response = $this->user_auth( $profile_data, 'instagram', $instagram_redirect_url );
							if ( $response == 'show form' ) {
								return;
							}
							delete_user_meta( $instagram_login_state, 'heateor_sl_redirect_to', true );
							if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
								$redirect_to = $this->get_login_redirection_url( $instagram_redirect_url, true );
							} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
								$redirect_to = $instagram_redirect_url . ( strpos( $instagram_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
							} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
								$redirect_to = $instagram_redirect_url . ( strpos( $instagram_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
							} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
								$redirect_to = $response['url'];
							} else {
								$redirect_to = $this->get_login_redirection_url( $instagram_redirect_url );
							}
							$this->close_login_popup( $redirect_to );
						}
					}
				}
			}
		}

		// Github
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Github' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'github', $this->options['providers'] ) && isset( $this->options['github_channel_id'] ) && $this->options['github_channel_id'] != '' && isset( $this->options['github_channel_secret'] ) && $this->options['github_channel_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) ) {
					$github_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $github_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
				wp_redirect( "https://github.com/login/oauth/authorize?client_id=" . $this->options['github_channel_id'] . "&scope=read:user&state=" . $github_login_state . "&response_type=code&redirect_uri=" . home_url() . "/HeateorSlAuth/Github" );
				die;
			}
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
			'code',
			'state' 
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Github' ) {
			$github_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $github_redirect_url = get_user_meta( $github_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
			$post_data = array(
				'code' => esc_attr( trim( $_GET['code'] ) ),
				'redirect_uri' => home_url() . "/HeateorSlAuth/Github",
				'client_id' => $this->options['github_channel_id'],
				'client_secret' => $this->options['github_channel_secret'] 
			);
			$response = wp_remote_post( "https://github.com/login/oauth/access_token", array(
				 'method' => 'POST',
				'timeout' => 15,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->options['github_channel_id'] . ':' . $this->options['github_channel_secret'] ) 
				),
				'body' => $post_data 
			) );
			
			if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$body             = wp_remote_retrieve_body( $response );
				$bodyParts        = explode( '&', $body );
				$access_token_parts = explode( '=', $bodyParts[0] );
				if ( count( $access_token_parts ) == 2 ) {
					$authorization = "token " . $access_token_parts[1];
					$response      = wp_remote_get( 'https://api.github.com/user', array(
						 'timeout' => 15,
						'headers' => array(
							 'Accept' => 'application/json',
							'Authorization' => $authorization 
						) 
					) );
					if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
						$profile_data = json_decode( wp_remote_retrieve_body( $response ) );
						if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
							$emails_response = wp_remote_get( 'https://api.github.com/user/emails', array(
								 'timeout' => 15,
								'headers' => array(
									 'Accept' => 'application/json',
									'Authorization' => $authorization 
								) 
							) );
							if ( ! is_wp_error( $emails_response ) && isset( $emails_response['response']['code'] ) && 200 === $emails_response['response']['code'] ) {
								$emails = json_decode( wp_remote_retrieve_body( $emails_response ) );
								if ( is_array( $emails ) ) {
									foreach ( $emails as $email ) {
										if ( isset( $email->primary ) && isset( $email->verified ) && $email->primary == 1 && $email->verified == 1 && ! empty( $email->email ) ) {
											$profile_data          = (array) $profile_data;
											$profile_data['email'] = $email->email;
											$profile_data          = (object) $profile_data;
											break;
										}
									}
								}
								$profile_data          = $this->sanitize_profile_data( $profile_data, 'github' );
								$profile_data['state'] = $github_login_state;
								$response = $this->user_auth( $profile_data, 'github', $github_redirect_url );
								if ( $response == 'show form' ) {
									return;
								}
								delete_user_meta( $github_login_state, 'heateor_sl_redirect_to', true );
								if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
									$redirect_to = $this->get_login_redirection_url( $github_redirect_url, true );
								} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
									$redirect_to = $github_redirect_url . ( strpos( $github_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
								} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
									$redirect_to = $github_redirect_url . ( strpos( $github_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
								} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
									$redirect_to = $response['url'];
								} else {
									$redirect_to = $this->get_login_redirection_url( $github_redirect_url );
								}
								$this->close_login_popup( $redirect_to );
							}
						}
					}
				}
			}
		}

		// Line login
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Line' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'line', $this->options['providers'] ) && isset( $this->options['line_channel_id'] ) && $this->options['line_channel_id'] != '' && isset( $this->options['line_channel_secret'] ) && $this->options['line_channel_secret'] != '' ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) .'library/Line/line.php';
				$heateor_sl_line = new linelogin( array(
				    'client_id' => $this->options['line_channel_id'],
				    'client_secret' => $this->options['line_channel_secret'],
				    'redirect_uri' => home_url() . '/HeateorSlAuth/Line'
				) );

				$link = $heateor_sl_line->getLink( 7 );
				$this->php_session_start_resume();
				// save referrer url in state
				$_SESSION['heateor_sl_redirect_to'] = isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url();
				wp_redirect( $link );
				die;
			}
			
		}
		if ( isset( $_GET['code'] ) && isset( $_GET['state'] ) && remove_query_arg( array( 'code', 'state' ), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Line' ) {
			$this->php_session_start_resume();
			require_once plugin_dir_path( dirname( __FILE__ ) ) .'library/Line/line.php';
			$heateor_sl_line = new linelogin( array(
			    'client_id' => $this->options['line_channel_id'],
			    'client_secret' => $this->options['line_channel_secret'],
			    'redirect_uri' => home_url() . '/HeateorSlAuth/Line'
			) );
			$response = $heateor_sl_line->token( $_GET['code'], $_GET['state'] );
			
			if ( isset( $response ) ) {
				$profile_data = $heateor_sl_line->profile( $response->access_token );
				// creating user profile
				if ( is_object( $profile_data ) && isset( $profile_data->userId ) ) {
					$profile_data = $this->sanitize_profile_data( $profile_data, 'line' );
					$line_redirect_url = isset( $_SESSION['heateor_sl_redirect_to'] ) && $_SESSION['heateor_sl_redirect_to'] ? esc_url( trim( $_SESSION['heateor_sl_redirect_to'] ) ) : home_url();
					$response = $this->user_auth( $profile_data, 'line', $line_redirect_url );
					if ( $response == 'show form' ) {
						return;
					}
					$this->unset_php_session( 'heateor_sl_redirect_to' );
					if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
						$redirect_to = $this->get_login_redirection_url( $line_redirect_url, true );
					} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
						$redirect_to = $line_redirect_url . ( strpos( $line_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
					} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
						$redirect_to = $line_redirect_url . ( strpos( $line_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
					} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
						$redirect_to = $response['url'];
					} else {
						$redirect_to = $this->get_login_redirection_url( $line_redirect_url );
					}
					$this->close_login_popup( $redirect_to );
				}				
			}
		}	

		// Facebook
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Facebook' ) ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'facebook', $this->options['providers'] ) && isset( $this->options['fb_key'] ) && $this->options['fb_key'] != '' && isset( $this->options['fb_secret'] ) && $this->options['fb_secret'] != '' ) {
		    	if ( ! isset( $_GET['code'] ) ) {
					$facebook_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $facebook_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
			        wp_redirect( "https://www.facebook.com/v10.0/dialog/oauth?scope=email&client_id=" . $this->options['fb_key'] . "&state=" . $facebook_login_state . "&redirect_uri=" . home_url() . "/?HeateorSlAuth=Facebook" );
			        die;
		    	} elseif ( isset( $_GET['code'] ) && isset( $_GET['state'] ) && get_user_meta( esc_attr( trim( $_GET['state'] ) ), 'heateor_sl_redirect_to', true ) !== false ) {
		    		$facebook_login_state  = esc_attr( trim( $_GET['state'] ) );
					if ( ( $facebook_redirect_url = get_user_meta( $facebook_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
				    	return;
				    }
				    $post_data = array(
				        'code' => esc_attr( trim( $_GET['code'] ) ),
				        'redirect_uri' => home_url() . "/?HeateorSlAuth=Facebook",
				        'client_id' => $this->options['fb_key'],
				        'client_secret' => $this->options['fb_secret'] 
				    );
				    $response = wp_remote_post( "https://graph.facebook.com/v10.0/oauth/access_token", array(
				        'method' => 'POST',
				        'timeout' => 15,
				        'redirection' => 5,
				        'httpversion' => '1.0',
				        'sslverify' => false,
				        'headers' => array(
				             'Content-Type' => 'application/x-www-form-urlencoded' 
				        ),
				        'body' => http_build_query( $post_data ) 
				    ) );
				    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				        $body     = json_decode( wp_remote_retrieve_body( $response ) );
					    if ( ! empty( $body->access_token ) ) {
					        $response = wp_remote_get( "https://graph.facebook.com/me?fields=id,name,about,link,email,first_name,last_name,picture.width(60).height(60).as(picture_small),picture.width(320).height(320).as(picture_large)&access_token=" . $body->access_token, array(
					             'timeout' => 15 
					        ) );
					        
					        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
					            if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
					                $profile_data     = $this->sanitize_profile_data( $profile_data, 'facebook' );
					                $profile_data['state'] = $facebook_login_state;
					                $response = $this->user_auth( $profile_data, 'facebook', $facebook_redirect_url );
					                if ( $response == 'show form' ) {
					                    return;
					                }
					                delete_user_meta( $facebook_login_state, 'heateor_sl_redirect_to', true );
					                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
					                    $redirect_to = $this->get_login_redirection_url( $facebook_redirect_url, true );
					                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
					                    $redirect_to = $facebook_redirect_url . ( strpos( $facebook_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
					                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
					                    $redirect_to = $facebook_redirect_url . ( strpos( $facebook_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
					                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
					                    $redirect_to = $response['url'];
					                } else {
					                    $redirect_to = $this->get_login_redirection_url( $facebook_redirect_url );
					                }
					                $this->close_login_popup( $redirect_to );
					            }
					        }
					    }
			    	}
			    }
		    }
		}

		// Twitch
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Twitch' ) ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'twitch', $this->options['providers'] ) && isset( $this->options['twitch_client_id'] ) && $this->options['twitch_client_id'] != '' && isset( $this->options['twitch_client_secret'] ) && $this->options['twitch_client_secret'] != '' ) {
		    	if ( ! isset( $_GET['code'] ) ) {
					$twitch_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $twitch_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://id.twitch.tv/oauth2/authorize?client_id=" . $this->options['twitch_client_id'] . "&scope=user:read:email&response_type=code&state=" . $twitch_login_state . "&redirect_uri=" . home_url() . "/HeateorSlAuth/Twitch" );
		        die;
		    }
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		    'code',
		    'scope',
		    'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Twitch' ) { 
		    $twitch_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $twitch_redirect_url = get_user_meta( $twitch_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
		    $post_data = array(
		        'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Twitch",
		        'client_id' => $this->options['twitch_client_id'],
		        'client_secret' => $this->options['twitch_client_secret']
		    );
		    $response = wp_remote_post( "https://id.twitch.tv/oauth2/token", array(
		        'method' => 'POST',
		        'timeout' => 15,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'headers' => array(
		            'Content-Type' => 'application/x-www-form-urlencoded'
		        ),
		        'body' => http_build_query( $post_data ) 
		    ) );
		    
		    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		        $body     = json_decode( wp_remote_retrieve_body( $response ) );
		        $response = wp_remote_get( "https://api.twitch.tv/helix/users", array(
		             'timeout' => 15,
		             'headers' => array( 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $body->access_token, 'Client-ID' => $this->options['twitch_client_id'] )
		        ) );
		        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );

		            if ( is_object( $profile_data ) && isset( $profile_data->data ) && is_array( $profile_data->data ) && isset( $profile_data->data[0] ) && isset( $profile_data->data[0]->id ) ) {
		                $profile_data          = $this->sanitize_profile_data( $profile_data->data[0], 'twitch' );
		                $profile_data['state'] = $twitch_login_state;
		                $response = $this->user_auth( $profile_data, 'twitch', $twitch_redirect_url );
		                if ( $response == 'show form' ) {
		                    return;
		                }
		                delete_user_meta( $twitch_login_state, 'heateor_sl_redirect_to', true );
		                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
		                    $redirect_to = $this->get_login_redirection_url( $twitch_redirect_url, true );
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
		                    $redirect_to = $twitch_redirect_url . ( strpos( $twitch_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
		                    $redirect_to = $twitch_redirect_url . ( strpos( $twitch_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
		                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
		                    $redirect_to = $response['url'];
		                } else {
		                    $redirect_to = $this->get_login_redirection_url( $twitch_redirect_url );
		                }
		                $this->close_login_popup( $redirect_to );
		            }
		        }
		    }
		}

		// mail.ru
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Mailru' ) ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'mailru', $this->options['providers'] ) && $this->options['mailru_client_id'] && $this->options['mailru_client_secret'] ) {
		    	if ( ! $this->is_curl_loaded() ) {
		    		_e( 'Enable CURL at your website server to use Mail.ru Social Login.', 'heateor-social-login' );
		    		die;
		    	}
		    	if ( ! isset( $_GET['code'] ) ) {
					$mailru_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $mailru_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        // save referrer url in state
		        $_SESSION['heateor_sl_mailru_redirect'] = isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url();
		        wp_redirect( "https://oauth.mail.ru/login?client_id=" . $this->options['mailru_client_id'] . "&scope=userinfo&state=" . $mailru_login_state . "&response_type=code&redirect_uri=" . home_url() . "/HeateorSlAuth/Mailru" );
		        die;
		    }
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		     'code' , 'scope' , 'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Mailru' ) { 
		    $post_data = array(
		        'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Mailru"
		    );

		    $service_url = 'https://oauth.mail.ru/token';
			$curl = curl_init( $service_url );
			curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt( $curl, CURLOPT_USERPWD, $this->options['mailru_client_id'] . ":" . $this->options['mailru_client_secret'] );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
			    'Content-Type:application/x-www-form-urlencoded',
			    'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36'
			) );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			$curl_response = curl_exec( $curl );
			$response      = json_decode( $curl_response );
			curl_close( $curl );
			if ( isset( $response->access_token ) ) {
			    $service_url = 'https://oauth.mail.ru/userinfo?access_token=' . $response->access_token;
			    $curl        = curl_init( $service_url );
			    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			    curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
			        'Content-Type:application/x-www-form-urlencoded',
			        'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36',
			        'Authorization' => "Bearer " . $response->access_token 
			    ) );
			    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			    $curl_response = curl_exec( $curl );
			    $profile_data  = json_decode( $curl_response );
			    curl_close( $curl );
	       
	            if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
	                $profile_data 			= $this->sanitize_profile_data( $profile_data, 'mailru' );
	                $mailru_login_state    	= esc_attr( trim( $_GET['state'] ) );
					$mailru_redirect_url   	= get_user_meta( $mailru_login_state, 'heateor_sl_redirect_to', true );
	                $response 				= $this->user_auth( $profile_data, 'mailru', $mailru_redirect_url );
	                if ( $response == 'show form' ) {
	                    return;
	                }
	                $this->unset_php_session( 'heateor_sl_mailru_redirect' );
	                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
	                    $redirect_to = $this->get_login_redirection_url( $mailru_redirect_url, true );
	                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
	                    $redirect_to = $mailru_redirect_url . ( strpos( $mailru_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
	                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
	                    $redirect_to = $mailru_redirect_url . ( strpos( $mailru_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
	                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
	                    $redirect_to = $response['url'];
	                } else {
	                    $redirect_to = $this->get_login_redirection_url( $mailru_redirect_url );
	                }
	                $this->close_login_popup( $redirect_to );
	            }
		    }
		}

		// reddit
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Reddit' ) ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'reddit', $this->options['providers'] ) && isset( $this->options['reddit_client_id'] ) && $this->options['reddit_client_id'] != '' && isset( $this->options['reddit_client_secret'] ) && $this->options['reddit_client_secret'] != '' ) {
		    	if ( ! isset( $_GET['code'] ) ) {
					$reddit_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $reddit_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://ssl.reddit.com/api/v1/authorize?client_id=" . $this->options['reddit_client_id'] . "&scope=identity&state=" . $reddit_login_state . "&duration=temporary&response_type=code&redirect_uri=" . home_url() . "/HeateorSlAuth/Reddit" );
		        die;
		    }
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		    'code',
		    'state',
		    'scope'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Reddit' ) {
			$reddit_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $reddit_redirect_url = get_user_meta( $reddit_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
		    $post_data = array(
		        'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Reddit",
		        'client_id' => $this->options['reddit_client_id'],
		        'client_secret' => $this->options['reddit_client_secret']
		    );
		    $response = wp_remote_post( "https://www.reddit.com/api/v1/access_token", array(
		        'timeout' => 15,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->options['reddit_client_id'] . ':' . $this->options['reddit_client_secret'] ) 
				),
		        'body' => http_build_query( $post_data ) 
		    ) );
		    
		    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		        $body     = json_decode( wp_remote_retrieve_body( $response ) );
		        $response = wp_remote_get( "https://oauth.reddit.com/api/v1/me", array(
					'timeout' => 15,
					'headers' => array( 'Authorization' => "Bearer " . $body->access_token )
		        ) );
		        
		        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
		            if ( is_object( $profile_data ) && isset( $profile_data->id ) && isset( $profile_data->verified ) && $profile_data->verified == 1 ) {
		                $profile_data 		   = $this->sanitize_profile_data( $profile_data, 'reddit' );
		                $profile_data['state'] = $reddit_login_state;
		                $response = $this->user_auth( $profile_data, 'reddit', $reddit_redirect_url );
		                if ( $response == 'show form' ) {
		                    return;
		                }
		                delete_user_meta( esc_attr( trim( $_GET['state'] ) ), 'heateor_sl_redirect_to', true );
		                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
		                    $redirect_to = $this->get_login_redirection_url( $reddit_redirect_url, true );
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
		                    $redirect_to = $reddit_redirect_url . ( strpos( $reddit_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
		                    $redirect_to = $reddit_redirect_url . ( strpos( $reddit_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
		                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
		                    $redirect_to = $response['url'];
		                } else {
		                    $redirect_to = $this->get_login_redirection_url( $reddit_redirect_url );
		                }
		                $this->close_login_popup( $redirect_to );
		            }
		        }
		    }
		}

		// Disqus
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Disqus' ) ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'disqus', $this->options['providers'] ) && isset( $this->options['disqus_public_key'] ) && $this->options['disqus_public_key'] != '' && isset( $this->options['disqus_secret_key'] ) && $this->options['disqus_secret_key'] != '' ) {
		    	if ( ! isset( $_GET['code'] ) ) {
					$disqus_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $disqus_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://disqus.com/api/oauth/2.0/authorize/?client_id=" . $this->options['disqus_public_key'] . "&scope=read,email&response_type=code&state=" . $disqus_login_state . "&redirect_uri=" . home_url() . "/HeateorSlAuth/Disqus" );
		        die;
		    }
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		    'code',
		    'scope',
		    'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Disqus' ) { 
		    $disqus_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $disqus_redirect_url = get_user_meta( $disqus_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
		    $post_data = array(
		        'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Disqus",
		        'client_id' => $this->options['disqus_public_key'],
		        'client_secret' => $this->options['disqus_secret_key']
		    );
		    $response = wp_remote_post( "https://disqus.com/api/oauth/2.0/access_token/", array(
		        'timeout' => 15,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->options['disqus_public_key'] . ':' . $this->options['disqus_secret_key'] ) 
				),
		        'body' => http_build_query( $post_data )
		    ) );
		    
		    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		        $body     = json_decode( wp_remote_retrieve_body( $response ) );
		        $response = wp_remote_get( "https://disqus.com/api/3.0/users/details.json?api_key=" . $this->options['disqus_public_key'], array(
					'timeout' => 15,
					'headers' => array( 'Authorization' => "Bearer " . $body->access_token )
		        ) );
		        
		        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
		            if ( is_object( $profile_data ) && isset( $profile_data->response->id ) ) {
		                $profile_data 		   = $this->sanitize_profile_data( $profile_data, 'disqus' );
		                $profile_data['state'] = $disqus_login_state;
		                $response = $this->user_auth( $profile_data, 'disqus', $disqus_redirect_url );
		                if ( $response == 'show form' ) {
		                    return;
		                }
		                delete_user_meta( $disqus_login_state, 'heateor_sl_redirect_to', true );
		                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
		                    $redirect_to = $this->get_login_redirection_url( $disqus_redirect_url, true );
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
		                    $redirect_to = $disqus_redirect_url . ( strpos( $disqus_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
		                    $redirect_to = $disqus_redirect_url . ( strpos( $disqus_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
		                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
		                    $redirect_to = $response['url'];
		                } else {
		                    $redirect_to = $this->get_login_redirection_url( $disqus_redirect_url );
		                }
		                $this->close_login_popup( $redirect_to );
		            }
		        }
		    }
		}

		// foursquare
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Foursquare' ) ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'foursquare', $this->options['providers'] ) && isset( $this->options['foursquare_client_id'] ) && $this->options['foursquare_client_id'] != '' && isset( $this->options['foursquare_client_secret'] ) && $this->options['foursquare_client_secret'] != '' ) {
		    	if ( ! isset( $_GET['code'] ) ) {
					$foursquare_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $foursquare_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://foursquare.com/oauth2/authenticate/?client_id=" . $this->options['foursquare_client_id'] . "&response_type=code&state=" . $foursquare_login_state . "&redirect_uri=" . home_url() . "/HeateorSlAuth/Foursquare" );
		        die;
		    }
		    
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		    'code',
		    'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Foursquare' ) {
			$foursquare_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $foursquare_redirect_url = get_user_meta( $foursquare_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
		    $post_data = array(
		        'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Foursquare",
		        'client_id' => $this->options['foursquare_client_id'],
		        'client_secret' => $this->options['foursquare_client_secret']
		    );
		    $response = wp_remote_post( "https://foursquare.com/oauth2/access_token", array(
		        'timeout' => 15,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->options['foursquare_client_id'] . ':' . $this->options['foursquare_client_secret'] ) 
				),
		        'body' => http_build_query( $post_data ) 
		    ) );
		    
		    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		        $body     = json_decode( wp_remote_retrieve_body( $response ) );
		        $response = wp_remote_get( "https://api.foursquare.com/v2/users/self?oauth_token=" . $body->access_token . "&v=" . rand() , array(
		             'timeout' => 15,
		             'headers' => array( 'Authorization' => "Bearer " . $body->access_token )
		        ) );    
		        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
		            if ( is_object( $profile_data ) && isset( $profile_data->response->user->id ) ) {
		                $profile_data 		   = $this->sanitize_profile_data( $profile_data, 'foursquare' );
		                $profile_data['state'] = $foursquare_login_state;
		                $response = $this->user_auth( $profile_data, 'foursquare', $foursquare_redirect_url );
		                if ( $response == 'show form' ) {
		                    return;
		                }
		                delete_user_meta( $foursqare_login_state, 'heateor_sl_redirect_to', true );
		                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
		                    $redirect_to = $this->get_login_redirection_url( $foursquare_redirect_url, true );
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
		                    $redirect_to = $foursquare_redirect_url . ( strpos( $foursquare_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
		                    $redirect_to = $foursquare_redirect_url . ( strpos( $foursquare_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
		                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
		                    $redirect_to = $response['url'];
		                } else {
		                    $redirect_to = $this->get_login_redirection_url( $foursquare_redirect_url );
		                }
		                $this->close_login_popup( $redirect_to );
		            }
		        }
		    }
		}

		// Dropbox
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Dropbox' ) ) {
			if ( isset( $this->options['providers'] ) && in_array( 'dropbox', $this->options['providers'] ) && isset( $this->options['dropbox_app_key'] ) && $this->options['dropbox_app_key'] != '' && isset( $this->options['dropbox_app_secret'] ) && $this->options['dropbox_app_secret'] != '' ) {
		    	if ( ! isset( $_GET['code'] ) ) {
					$dropbox_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $dropbox_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://www.dropbox.com/1/oauth2/authorize?client_id=" . $this->options['dropbox_app_key'] . "&scope=account_info.read&state=" . $dropbox_login_state . "&response_type=code&redirect_uri=" . home_url() . "/HeateorSlAuth/Dropbox" );
		        die;
		    }    
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		    'code',
		    'scope',
		    'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Dropbox' ) { 
		    $dropbox_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $dropbox_redirect_url = get_user_meta( $dropbox_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
		    $post_data = array(
		        'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Dropbox"
		    );
		    $response = wp_remote_post( "https://api.dropbox.com/1/oauth2/token", array(
		        'method' => 'POST',
		        'timeout' => 15,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'headers' => array(
					 'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->options['dropbox_app_key'] . ':' . $this->options['dropbox_app_secret'] ) 
				),
		        'body' => http_build_query( $post_data ) 
		    ) );
		    
		    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		        $body     = json_decode( wp_remote_retrieve_body( $response ) );
		        $response = wp_remote_post( "https://api.dropbox.com/2/users/get_current_account", array(
		             'timeout' => 15,
		             'headers' => array( 'content-type' =>'application/json','Authorization' => "Bearer " . $body->access_token ),
		             'body' => "null"
		        ) ); 
		        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
		            if ( is_object( $profile_data ) && isset( $profile_data->account_id ) ) {
		                $profile_data 		   = $this->sanitize_profile_data( $profile_data, 'dropbox' );
		                $profile_data['state'] = $dropbox_login_state;
		                $response = $this->user_auth( $profile_data, 'dropbox', $dropbox_redirect_url );
		                if ( $response == 'show form' ) {
		                    return;
		                }
		                delete_user_meta( $dropbox_login_state, 'heateor_sl_redirect_to', true );
		                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
		                    $redirect_to = $this->get_login_redirection_url( $dropbox_redirect_url, true );
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
		                    $redirect_to = $dropbox_redirect_url . ( strpos( $dropbox_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
		                    $redirect_to = $dropbox_redirect_url . ( strpos( $dropbox_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
		                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
		                    $redirect_to = $response['url'];
		                } else {
		                    $redirect_to = $this->get_login_redirection_url( $dropbox_redirect_url );
		                }
		                $this->close_login_popup( $redirect_to );
		            }
		        }
		    }
		}

		// send request to twitter
		if ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Twitter' && ! isset( $_REQUEST['oauth_token'] ) ) {	
			if ( isset( $this->options['twitter_key'] ) && $this->options['twitter_key'] != '' && isset( $this->options['twitter_secret'] ) && $this->options['twitter_secret'] != '' ) {
				if ( ! function_exists( 'curl_init' ) ) {
					?>
					<div style="width: 500px; margin: 0 auto">
					<?php _e( 'cURL is not enabled at your website server. Please contact your website server administrator to enable it. ', 'heateor-social-login' ) ?>
					</div>
					<?php
					die;
				}
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Config.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Response.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/SignatureMethod.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/HmacSha1.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Consumer.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Util.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Request.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/TwitterOAuthException.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Token.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Util/JsonDecoder.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/TwitterOAuth.php';
				/* Build TwitterOAuth object with client credentials. */
				$connection = new Abraham\TwitterOAuth\TwitterOAuth( $this->options['twitter_key'], $this->options['twitter_secret'] );
				$request_token = $connection->oauth( 'oauth/request_token', ['oauth_callback' => esc_url( home_url() )] );
				/* Get temporary credentials. */
				if ( $connection->getLastHttpCode() == 200 ) {
					// generate unique ID
					$unique_id = mt_rand();
					// save oauth token and secret in db temporarily
					update_user_meta( $unique_id, 'heateorsl_twitter_oauthtoken', $request_token['oauth_token'] );
					update_user_meta( $unique_id, 'heateorsl_twitter_oauthtokensecret', $request_token['oauth_token_secret'] );
					if ( isset( $_GET['heateorMSEnabled'] ) ) {
						update_user_meta( $unique_id, 'heateorsl_mc_subscribe', '1' );
					}
					if ( isset( $_GET['heateor_sl_redirect_to'] ) && $this->validate_url( $_GET['heateor_sl_redirect_to'] ) !== false ) {
						update_user_meta( $unique_id, 'heateorsl_twitter_redirect', esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) );
					}
					wp_redirect( $connection->url( 'oauth/authorize', ['oauth_token' => $request_token['oauth_token']] ) );
					die;
				} else {
					?>
					<div style="width: 500px; margin: 0 auto">
						<ol>
						<li><?php echo sprintf( __( 'Enter exactly the following url in <strong>Website</strong> option in your Twitter app ( see step 3 %s )', 'heateor-social-login' ), '<a target="_blank" href="http://support.heateor.com/how-to-get-twitter-api-key-and-secret/">here</a>' ) ?><br/>
						<?php echo esc_url( home_url() ) ?>
						</li>
						<li><?php echo sprintf( __( 'Enter exactly the following url in <strong>Callback URLs</strong> option in your Twitter app ( see step 3 %s )', 'heateor-social-login' ), '<a target="_blank" href="http://support.heateor.com/how-to-get-twitter-api-key-and-secret/">here</a>' ) ?><br/>
						<?php echo esc_url( home_url() ); ?>
						</li>
						<li><?php _e( 'Make sure cURL is enabled at your website server. You may need to contact the server administrator of your website to verify this', 'heateor-social-login' ) ?></li>
						</ol>
					</div>
					<?php
					die;
				}
			}
		}

		// twitter authentication
		if ( isset( $_REQUEST['oauth_token'] ) && isset( $_REQUEST['oauth_verifier'] ) ) {
			global $wpdb;
			$unique_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'heateorsl_twitter_oauthtoken' and meta_value = %s", sanitize_text_field( $_REQUEST['oauth_token'] ) ) );
			$oauth_token_secret = get_user_meta( $unique_id, 'heateorsl_twitter_oauthtokensecret', true );
			// twitter redirect url
			$twitter_redirect_url = get_user_meta( $unique_id, 'heateorsl_twitter_redirect', true );
			if (empty( $unique_id ) || $oauth_token_secret == '' ) {
				// invalid request
				wp_redirect(esc_url( home_url() ) );
				die;
			}
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Config.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Response.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/SignatureMethod.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/HmacSha1.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Consumer.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Util.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Request.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/TwitterOAuthException.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Token.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/Util/JsonDecoder.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/Twitter/src/TwitterOAuth.php';
			$connection = new Abraham\TwitterOAuth\TwitterOAuth( $this->options['twitter_key'], $this->options['twitter_secret'], $_REQUEST['oauth_token'], $oauth_token_secret );
			/* Request access tokens from twitter */
			$access_token = $connection->oauth( "oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']] );
			/* Create a TwitterOauth object with consumer/user tokens. */
			$connection = new Abraham\TwitterOAuth\TwitterOAuth( $this->options['twitter_key'], $this->options['twitter_secret'], $access_token['oauth_token'], $access_token['oauth_token_secret'] );
			$content = $connection->get( 'account/verify_credentials', array( 'include_email' => 'true' ) );
			// delete temporary data
			delete_user_meta( $unique_id, 'heateorsl_twitter_oauthtokensecret' );
			delete_user_meta( $unique_id, 'heateorsl_twitter_oauthtoken' );
			delete_user_meta( $unique_id, 'heateorsl_twitter_redirect' );
			if ( is_object( $content ) && isset( $content->id ) ) {
				$profile_data = $this->sanitize_profile_data( $content, 'twitter' );
				if ( get_user_meta( $unique_id, 'heateorsl_mc_subscribe', true ) != '' ) {
					$profile_data['mc_subscribe'] = 1;
				}
				delete_user_meta( $unique_id, 'heateorsl_mc_subscribe' );
				$response = $this->user_auth( $profile_data, 'twitter', $twitter_redirect_url );
				if ( $response == 'show form' ) {
					return;
				}
				if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
					$redirect_to = $this->get_login_redirection_url( $twitter_redirect_url, true );
				} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
					$redirect_to = $twitter_redirect_url . ( strpos( $twitter_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
				} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
					$redirect_to = $twitter_redirect_url . ( strpos( $twitter_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
				} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
					$redirect_to = $response['url'];
				} else {
					$redirect_to = $this->get_login_redirection_url( $twitter_redirect_url );
				}
				$this->close_login_popup( $redirect_to );
			}
		}

		// linkedin
		if ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Linkedin' ) {
			if ( isset( $this->options['li_key'] ) && $this->options['li_key'] != '' && isset( $this->options['li_secret'] ) && $this->options['li_secret'] != '' ) {
				if ( ! isset( $_GET['code'] ) && ! isset( $_GET['state'] ) ) {
					$linkedin_auth_state = mt_rand();
	                update_user_meta( $linkedin_auth_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
	                if ( isset( $_GET['heateorMSEnabled'] ) ) {
	                	update_user_meta( $linkedin_auth_state, 'heateor_sl_linkedin_mc_sub', 1 );
	                }
				    $linkedin_scope = 'r_liteprofile,r_emailaddress';
				    wp_redirect( 'https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=' . $this->options['li_key'] . '&redirect_uri=' . urlencode( home_url() . '/?HeateorSlAuth=Linkedin' ) . '&state='. $linkedin_auth_state . '&scope=' . $linkedin_scope );
				    die;
				}
				if ( isset( $_GET['code'] ) && isset( $_GET['state'] ) ) {
					$linkedin_login_state  		  = esc_attr( trim( $_GET['state'] ) );
					if ( ( $linkedin_redirect_url = get_user_meta( $linkedin_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
				    	return;
				    }
				    $url = 'https://www.linkedin.com/oauth/v2/accessToken';
					$data_access_token = array(
						'grant_type' => 'authorization_code',
						'code' => esc_attr( trim( $_GET['code'] ) ),
						'redirect_uri' => home_url() . '/?HeateorSlAuth=Linkedin',
						'client_id' => $this->options['li_key'],
						'client_secret' => $this->options['li_secret']
					);
					$response = wp_remote_post( $url, array(
							'method' => 'POST',
							'timeout' => 15,
							'redirection' => 5,
							'httpversion' => '1.0',
							'sslverify' => false,
							'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
							'body' => http_build_query( $data_access_token )
					    )
					);
					if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
						$body = json_decode( wp_remote_retrieve_body( $response ) );
						if ( is_object( $body ) && isset( $body->access_token ) ) {
							// fetch profile data
							$first_Last_name = wp_remote_get( 'https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams ) )', array(
									'method' => 'GET',
									'timeout' => 15,
									'headers' => array( 'Authorization' => "Bearer " . $body->access_token ),
							    )
							);
							$email = wp_remote_get( 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~) )', array(
									'method' => 'GET',
									'timeout' => 15,
									'headers' => array( 'Authorization' => "Bearer " . $body->access_token ),
							    )
							);
							if ( ! is_wp_error( $first_Last_name ) && isset( $first_Last_name['response']['code'] ) && 200 === $first_Last_name['response']['code'] && ! is_wp_error( $email ) && isset( $email['response']['code'] ) && 200 === $email['response']['code'] ) {
								$first_Last_name_body = json_decode( wp_remote_retrieve_body( $first_Last_name ) );
								$email_body = json_decode( wp_remote_retrieve_body( $email ) );
								if ( is_object( $first_Last_name_body ) && isset( $first_Last_name_body->id ) && $first_Last_name_body->id && is_object( $email_body ) && isset( $email_body->elements ) ) {
									$first_Last_name_body = json_decode(json_encode( $first_Last_name_body ), true );
									$email_body = json_decode(json_encode( $email_body ), true );
									$first_name = isset( $first_Last_name_body['firstName'] ) && isset( $first_Last_name_body['firstName']['localized'] ) && isset( $first_Last_name_body['firstName']['preferredLocale'] ) && isset( $first_Last_name_body['firstName']['preferredLocale']['language'] ) && isset( $first_Last_name_body['firstName']['preferredLocale']['country'] ) ? $first_Last_name_body['firstName']['localized'][$first_Last_name_body['firstName']['preferredLocale']['language'] . '_' . $first_Last_name_body['firstName']['preferredLocale']['country']] : '';
									$last_name = isset( $first_Last_name_body['lastName'] ) && isset( $first_Last_name_body['lastName']['localized'] ) && isset( $first_Last_name_body['lastName']['preferredLocale'] ) && isset( $first_Last_name_body['lastName']['preferredLocale']['language'] ) && isset( $first_Last_name_body['lastName']['preferredLocale']['country'] ) ? $first_Last_name_body['lastName']['localized'][$first_Last_name_body['lastName']['preferredLocale']['language'] . '_' . $first_Last_name_body['lastName']['preferredLocale']['country']] : '';
									$small_avatar = isset( $first_Last_name_body['profilePicture'] ) && isset( $first_Last_name_body['profilePicture']['displayImage~'] ) && isset( $first_Last_name_body['profilePicture']['displayImage~']['elements'] ) && is_array( $first_Last_name_body['profilePicture']['displayImage~']['elements'] ) && isset( $first_Last_name_body['profilePicture']['displayImage~']['elements'][0]['identifiers'] ) && is_array( $first_Last_name_body['profilePicture']['displayImage~']['elements'][0]['identifiers'][0] ) && isset( $first_Last_name_body['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] ) ? $first_Last_name_body['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] : '';
									$large_avatar = isset( $first_Last_name_body['profilePicture'] ) && isset( $first_Last_name_body['profilePicture']['displayImage~'] ) && isset( $first_Last_name_body['profilePicture']['displayImage~']['elements'] ) && is_array( $first_Last_name_body['profilePicture']['displayImage~']['elements'] ) && isset( $first_Last_name_body['profilePicture']['displayImage~']['elements'][3]['identifiers'] ) && is_array( $first_Last_name_body['profilePicture']['displayImage~']['elements'][3]['identifiers'][0] ) && isset( $first_Last_name_body['profilePicture']['displayImage~']['elements'][3]['identifiers'][0]['identifier'] ) ? $first_Last_name_body['profilePicture']['displayImage~']['elements'][3]['identifiers'][0]['identifier'] : '';
			                     	$email_address = isset( $email_body['elements'] ) && is_array( $email_body['elements'] ) && isset( $email_body['elements'][0]['handle~'] ) && isset( $email_body['elements'][0]['handle~']['emailAddress'] ) ? $email_body['elements'][0]['handle~']['emailAddress'] : '';
			                     	$user = array(
			                     		'firstName' => $first_name,
			                     		'lastName' => $last_name,
			                     		'email' => $email_address,
			                     		'id' => $first_Last_name_body['id'],
			                     		'smallAvatar' => $small_avatar,
			                     		'largeAvatar' => $large_avatar
			                     	);
			                     	
									$profile_data = $this->sanitize_profile_data( $user, 'linkedin' );
									if ( get_user_meta( $linkedin_login_state, 'heateor_sl_linkedin_mc_sub', true ) ) {
										$profile_data['mc_subscribe'] = 1;
										delete_user_meta( $linkedin_login_state, 'heateor_sl_linkedin_mc_sub' );
									}
									$profile_data['state'] = $linkedin_login_state;
									$response = $this->user_auth( $profile_data, 'linkedin', $linkedin_redirect_url );
									if ( $response == 'show form' ) {
										return;
									}
									delete_user_meta(esc_attr( trim( $_GET['state'] ) ), 'heateor_sl_redirect_to' );
									if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
										$redirect_to = $this->get_login_redirection_url( $linkedin_redirect_url, true );
									} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
										$redirect_to = $linkedin_redirect_url . ( strpos( $linkedin_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
									} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
										$redirect_to = $linkedin_redirect_url . ( strpos( $linkedin_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
									} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
										$redirect_to = $response['url'];
									} else {
										$redirect_to = $this->get_login_redirection_url( $linkedin_redirect_url );
									}
									$this->close_login_popup( $redirect_to );
								}
							}
						}
					}
				}
			}
		}

		// Google
		if ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Google' ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'google', $this->options['providers'] ) && isset( $this->options['google_key'] ) && $this->options['google_key'] != '' && isset( $this->options['google_secret'] ) && $this->options['google_secret'] != '' ) {
		        if ( ! isset( $_GET['code'] ) ) {
					$google_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $google_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://accounts.google.com/o/oauth2/auth?client_id=" . $this->options['google_key'] . "&prompt=select_account&scope=https://www.googleapis.com/auth/userinfo.email%20https://www.googleapis.com/auth/userinfo.profile&state=" . $google_login_state . "&response_type=code&redirect_uri=" . home_url() );
		        die;
		    }
		}
		if ( isset( $_GET['code'] ) && isset( $_GET['state'] ) ) {
	    	$google_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $google_redirect_url = get_user_meta( $google_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
	        $post_data = array(
	             'grant_type' => 'authorization_code',
	            'code' => esc_attr( trim( $_GET['code'] ) ),
	            'redirect_uri' => home_url(),
	            'client_id' => $this->options['google_key'],
	            'client_secret' => $this->options['google_secret'] 
	        );
	        $response  = wp_remote_post( "https://accounts.google.com/o/oauth2/token", array(
	             'method' => 'POST',
	            'timeout' => 15,
	            'redirection' => 5,
	            'httpversion' => '1.0',
	            'sslverify' => false,
	            'headers' => array(
	                 'Content-Type' => 'application/x-www-form-urlencoded' 
	            ),
	            'body' => http_build_query( $post_data ) 
	        ) );
	        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
	            $body = json_decode( wp_remote_retrieve_body( $response ) );
	            if ( isset( $body->access_token ) ) {
	                $authorization = "Bearer " . $body->access_token;
	                $response      = wp_remote_get( 'https://www.googleapis.com/oauth2/v3/userinfo', array(
	                    'timeout' => 15,
	                    'headers' => array(
	                         'Accept' => 'application/json',
	                        'Authorization' => $authorization 
	                    ) 
	                ) );
	                if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
	                    $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
	                    if ( is_object( $profile_data ) && isset( $profile_data->sub ) ) {
	                        $profile_data          = $this->sanitize_profile_data( $profile_data, 'google' );
	                        $profile_data['state'] = $google_login_state;
	                        $response 			   = $this->user_auth( $profile_data, 'google', $google_redirect_url );
	                        if ( $response == 'show form' ) {
	                            return;
	                        }
	                        delete_user_meta( $google_login_state, 'heateor_sl_redirect_to' );
	                        if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
	                            $redirect_to = $this->get_login_redirection_url( $google_redirect_url, true );
	                        } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
	                            $redirect_to = $google_redirect_url . ( strpos( $google_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
	                        } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
	                            $redirect_to = $google_redirect_url . ( strpos( $google_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
	                        } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
	                            $redirect_to = $response['url'];
	                        } else {
	                            $redirect_to = $this->get_login_redirection_url( $google_redirect_url );
	                        }
	                        $this->close_login_popup( $redirect_to );
	                    }
	                }
	            }
	        }
		}

		// windows live
		if ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Live' ) ) {
		    if ( isset( $this->options['providers'] ) && in_array( 'microsoft', $this->options['providers'] ) && isset( $this->options['live_channel_id'] ) && $this->options['live_channel_id'] != '' && isset( $this->options['live_channel_secret'] ) && $this->options['live_channel_secret'] != '' ) {
		    	if ( ! isset( $_GET['code'] ) ) {
					$live_login_state = mt_rand();
					// save referrer url in state
					update_user_meta( $live_login_state, 'heateor_sl_redirect_to', isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url() );
				}
		        wp_redirect( "https://login.live.com/oauth20_authorize.srf?client_id=" . $this->options['live_channel_id'] . "&scope=wl.emails,wl.basic&response_type=code&state=" . $live_login_state . "&redirect_uri=" . home_url() . "/HeateorSlAuth/Live" );
		        die;
		    }
		}
		if ( isset( $_GET['code'] ) && remove_query_arg( array(
		    'code',
		    'state'
		), html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) == home_url() . '/HeateorSlAuth/Live' ) {
			$live_login_state  = esc_attr( trim( $_GET['state'] ) );
			if ( ( $live_redirect_url = get_user_meta( $live_login_state, 'heateor_sl_redirect_to', true ) ) === false ) {
		    	return;
		    }
		    $post_data = array(
		         'grant_type' => 'authorization_code',
		        'code' => esc_attr( trim( $_GET['code'] ) ),
		        'redirect_uri' => home_url() . "/HeateorSlAuth/Live",
		        'client_id' => $this->options['live_channel_id'],
		        'client_secret' => $this->options['live_channel_secret'] 
		    );
		    $response = wp_remote_post( "https://login.live.com/oauth20_token.srf", array(
		         'method' => 'POST',
		        'timeout' => 15,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'headers' => array(
		             'Content-Type' => 'application/x-www-form-urlencoded' 
		        ),
		        'body' => http_build_query( $post_data ) 
		    ) );

		    if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		        $body     = json_decode( wp_remote_retrieve_body( $response ) );
		        $response = wp_remote_get( "https://apis.live.net/v5.0/me?access_token=" . $body->access_token, array(
		             'timeout' => 15 
		        ) );
		        if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
		            $profile_data = json_decode( wp_remote_retrieve_body( $response ) );
		            if ( is_object( $profile_data ) && isset( $profile_data->id ) ) {
		                $profile_data          = $this->sanitize_profile_data( $profile_data, 'microsoft' );
		                $profile_data['state'] = $live_login_state;
		                $response = $this->user_auth( $profile_data, 'microsoft', $live_redirect_url );
		                if ( $response == 'show form' ) {
		                    return;
		                }
		                delete_user_meta( $live_login_state, 'heateor_sl_redirect_to' );
		                if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
		                    $redirect_to = $this->get_login_redirection_url( $live_redirect_url, true );
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
		                    $redirect_to = $live_redirect_url . ( strpos( $live_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
		                } elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
		                    $redirect_to = $live_redirect_url . ( strpos( $live_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
		                } elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
		                    $redirect_to = $response['url'];
		                } else {
		                    $redirect_to = $this->get_login_redirection_url( $live_redirect_url );
		                }
		                $this->close_login_popup( $redirect_to );
		            }
		        }
		    }
		}

		// Vkontakte
		if ( ( ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Vkontakte' ) || ( isset( $_GET['code'] ) && ! isset( $_GET['HeateorSlAuth'] ) ) ) && ( isset( $this->options['providers'] ) && in_array( 'vkontakte', $this->options['providers'] ) && isset( $this->options['vk_key'] ) && $this->options['vk_key'] != '' && isset( $this->options['vk_secure_key'] ) && $this->options['vk_secure_key'] != '' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) .'library/Vkontakte/Vkontakte.php';
			$heateor_sl_vkontakte = new Vkontakte( array(
			    'client_id' => $this->options['vk_key'],
			    'client_secret' => $this->options['vk_secure_key'],
			    'redirect_uri' => esc_url( home_url() )
			) );
			$heateor_sl_vkontakte->setScope( array( 'email' ) );
			if ( isset( $_GET['HeateorSlAuth'] ) && sanitize_text_field( $_GET['HeateorSlAuth'] ) == 'Vkontakte' ) {
				$this->php_session_start_resume();
				$_SESSION['heateor_sl_vkontakte_redirect'] = isset( $_GET['heateor_sl_redirect_to'] ) ? esc_url( trim( $_GET['heateor_sl_redirect_to'] ) ) : home_url();
				wp_redirect( $heateor_sl_vkontakte->getLoginUrl() );
				die;
			}
			if ( isset( $_GET['code'] ) && ! isset( $_GET['HeateorSlAuth'] ) ) {
				if ( isset( $heateor_sl_vkontakte ) ) {
					$heateor_sl_vkontakte->authenticate( $_GET['code'] );
					$user_id = $heateor_sl_vkontakte->getUserId();
					$email = $heateor_sl_vkontakte->getUserEmail();
					if ( $user_id ) {
						$users = $heateor_sl_vkontakte->api( 'users.get', array(
						    'user_id' => $user_id,
						    'fields' => array( 'first_name', 'last_name', 'nickname', 'screen_name', 'photo_rec', 'photo_big' )
						) );
						if ( isset( $users[0] ) && isset( $users[0]["id"] ) && $users[0]["id"] ) {
							$profile_data = $this->sanitize_profile_data( $users[0], 'vkontakte' );
							$profile_data['email'] = '';
							if ( $email ) {
								$profile_data['email'] = sanitize_email( $email );
							}
							if ( isset( $_GET['heateorMSEnabled'] ) ) {
								$profile_data['mc_subscribe'] = 1;
							}
							$this->php_session_start_resume();
							$vkontakte_redirect_url = isset( $_SESSION['heateor_sl_vkontakte_redirect'] )  && $_SESSION['heateor_sl_vkontakte_redirect'] ? esc_url( trim( $_SESSION['heateor_sl_vkontakte_redirect'] ) ) : home_url();
							$response = $this->user_auth( $profile_data, 'vkontakte', $vkontakte_redirect_url );
							if ( $response == 'show form' ) {
								return;
							}
			                $this->unset_php_session( 'heateor_sl_vkontakte_redirect' );
							if ( is_array( $response ) && isset( $response['message'] ) && $response['message'] == 'register' && ( ! isset( $response['url'] ) || $response['url'] == '' ) ) {
								$redirect_to = $this->get_login_redirection_url( $vkontakte_redirect_url, true );
							} elseif ( isset( $response['message'] ) && $response['message'] == 'linked' ) {
								$redirect_to = $vkontakte_redirect_url . ( strpos( $vkontakte_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=1';
							} elseif ( isset( $response['message'] ) && $response['message'] == 'not linked' ) {
								$redirect_to = $vkontakte_redirect_url . ( strpos( $vkontakte_redirect_url, '?' ) !== false ? '&' : '?' ) . 'linked=0';
							} elseif ( isset( $response['url'] ) && $response['url'] != '' ) {
								$redirect_to = $response['url'];
							} else {
								$redirect_to = $this->get_login_redirection_url( $vkontakte_redirect_url );
							}
							$this->close_login_popup( $redirect_to );
						}
					}
				}
			}
		}
	}

	/**
	 * Check if cURL is enabled
	 *
	 * @since     1.1.11
	 */
	private function is_curl_loaded() {

	    return extension_loaded( 'curl' );
	
	}

	/**
	 * Initialize plugin
	 *
	 * @since     1.1.5
	 */
	public function init() {

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles' ));
		add_action( 'login_enqueue_scripts', array( $this,'frontend_styles' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ));
		add_action( 'login_enqueue_scripts', array( $this, 'frontend_scripts' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'load_event' ));
		add_action( 'login_enqueue_scripts', array( $this, 'load_event' ));
		add_action( 'parse_request', array( $this, 'connect' ));	
	
	}

	/**
	 * Load css at the frontend
	 *
	 * @since     1.1.5
	 */
	public function frontend_styles() {

		wp_enqueue_style( 'heateor_sl_frontend_css', plugins_url( 'css/heateor-social-login-public.css', __FILE__ ), false, HEATEOR_SOCIAL_LOGIN_VERSION );
		echo '<style type="text/css">';
		if ( isset( $this->options['center_align'] ) ) {
			echo 'div.heateor_sl_social_login_title,div.heateor_sl_login_container{text-align:center}ul.heateor_sl_login_ul{width:100%;text-align:center;}div.heateor_sl_login_container ul.heateor_sl_login_ul li{float:none!important;display:inline-block;}';
		}
		echo $this->options['custom_css'] . '</style>';
	
	}


	/**
	 * Javascript window load event
	 *
	 * @since     1.1.5
	 */
	public function load_event() {

		?>
		<script type="text/javascript">function heateorSlLoadEvent(e ) {var t=window.onload;if ( typeof window.onload!="function" ) {window.onload=e} else {window.onload=function() {t();e()}}}</script>
		<?php
	
	}


	/**
	 * Check if Social Login from particular provider is enabled
	 *
	 * @since     1.1.5
	 */
	public function social_login_provider_enabled( $provider ) {
		
		if ( isset( $this->options['providers'] ) && in_array( $provider, $this->options['providers'] ) ) {
			return true;
		} else {
			return false;
		}
	
	}

	/**
	 * Show icons for social account linking
	 *
	 * @since     1.1.5
	 */
	public function user_profile_account_linking() {

		global $pagenow;
		if ( $pagenow == 'profile.php' ) {
			echo $this->account_linking();
		}
	
	}

	/**
	 * Add tab for Social Account Linking
	 *
	 * @since     1.1.5
	 */
	public function add_linking_tab() {

		if ( bp_is_my_profile() ) {
			if ( isset( $this->options['bp_linking'] ) ) {
				global $bp, $user_ID;
				if ( $user_ID ) {
					bp_core_new_subnav_item( array(
							'name' => __( 'Social Account Linking', 'heateor-social-login' ),
							'slug' => 'account-linking',
							'parent_url' => trailingslashit( bp_loggedin_user_domain() . 'profile' ),
							'parent_slug' => 'profile',
							'screen_function' => 'bp_linking',
							'position' => 50
						)
					);
				}
			}
		}

	}

	/**
	 * Show social account linking icons for BuddyPress
	 *
	 * @since     1.1.5
	 */
	public function bp_account_linking() {

		echo $this->account_linking();
	
	}

	/**
	 * Show social account linking when 'Social Account Linking' tab is clicked
	 *
	 * @since     1.1.5
	 */
	public function bp_linking() {
		add_action( 'bp_template_content', array( $this, 'bp_account_linking' ));
		bp_core_load_template(apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render Social Account Linking icons
	 *
	 * @since     1.1.5
	 */
	public function account_linking() {

		global $user_ID;
		if ( is_user_logged_in() && ! $this->check_if_admin( $user_ID ) ) {
			$website_url = esc_url( home_url() );
			$user_verified = false;
		    $notification = '';
			$email_popup = false;
			$html = '<script type="text/javascript">var heateorSlSameTabLogin = ' . ( isset( $this->options["same_tab_login"] ) ? 1 : 0 ) . ', heateorSlDefaultLang = "' . get_locale() . '", heateorSlCloseIconPath = "' . plugins_url( '../images/close.png', __FILE__ ) . '";var heateorSlSiteUrl = "' . strtok( $website_url,"?" ) . '", heateorSlVerified = ' . intval( $user_verified ) . ', heateorSlEmailPopup = ' . intval( $email_popup ) . ';</script>';
			wp_enqueue_style( 'heateor_sl_frontend-css', plugins_url( 'css/heateor-social-login-public.css', __FILE__ ), false, HEATEOR_SOCIAL_LOGIN_VERSION );
			$twitterRedirect = urlencode( $this->get_valid_url( $this->get_http() . $_SERVER["HTTP_HOST"] . html_entity_decode( esc_url( remove_query_arg( array( 'linked' ) ) ) ) ) );
			$currentPageUrl = urldecode( $twitterRedirect );
			$html .= '<script>function heateorSlLoadEvent( e ) {var t=window.onload;if ( typeof window.onload!="function" ) {window.onload=e} else {window.onload=function() {t();e()}}} var heateorSlCloseIconPath = "' . plugins_url( 'images/close.png', __FILE__ ) . '";</script>';
			$website_url = esc_url( home_url() );
			$html .= '<script>var heateorSlLinkingRedirection = "' . ( $this->get_http() . $_SERVER["HTTP_HOST"] . html_entity_decode( esc_url( remove_query_arg( array( 'linked' ) ) ) ) ) . '"; var heateorSlSiteUrl = "' . $website_url . '", heateorSlVerified = 0, heateorSlAjaxUrl = "' . admin_url() . 'admin-ajax.php", heateorSlPopupTitle = "", heateorSlEmailPopup = 0, heateorSlEmailAjaxUrl = "' . admin_url() . 'admin-ajax.php", heateorSlEmailPopupTitle = "", heateorSlEmailPopupErrorMsg = "", heateorSlEmailPopupUniqueId = "", heateorSlEmailPopupVerifyMessage = "", heateorSlCurrentPageUrl = "' . $twitterRedirect . '";</script>';
			// scripts used for common Social Login functionality
			$loadingImagePath = plugins_url( '../images/ajax_loader.gif', __FILE__ );
			$heateorSlAjaxUrl = get_admin_url() . 'admin-ajax.php';
			$redirectionUrl = $this->get_login_redirection_url();
			$regRedirectionUrl = $this->get_login_redirection_url( '', true );
			global $heateorSlSteamLogin;
			$html .= '<style type="text/css">#ss_openid{border:1px solid gray;display:inline;font-family:"Trebuchet MS";font-size:12px;width:98%;padding:.35em .325em .75em;margin-bottom:20px}#ss_openid form{margin-top:25px;margin-left:0;padding:0;background:transparent;-webkit-box-shadow:none;box-shadow:none}#ss_openid input{font-family:"Trebuchet MS";font-size:12px;width:100px;float:left}#ss_openid input[type=submit]{background:#767676;padding:.75em 2em;border:0;-webkit-border-radius:2px;border-radius:2px;-webkit-box-shadow:none;box-shadow:none;color:#fff;cursor:pointer;display:inline-block;font-weight:800;line-height:1;text-shadow:none;-webkit-transition:background .2s;transition:background .2s}#ss_openid legend{color:#FF6200;float:left;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;display:table;max-width:100%;padding:0;white-space:normal}#ss_openid input.openid_login{background-color:#fff;background-position:0 50%;color:#000;width:220px;margin-right:10px;height:30px;margin-bottom:5px;background:#fff;background-image:-webkit-linear-gradient(rgba(255,255,255,0 ),rgba(255,255,255,0 ) );border:1px solid #bbb;-webkit-border-radius:3px;border-radius:3px;display:block;padding:.7em;line-height:1.5}#ss_openid a{color:silver}#ss_openid a:hover{color:#5e5e5e}</style>';
			$html .= '<script>var heateorSlLoadingImgPath = "' . $loadingImagePath . '", heateorSlAjaxUrl = "' . $heateorSlAjaxUrl . '", heateorSlRedirectionUrl = "' . $redirectionUrl . '", heateorSlRegRedirectionUrl = "' . $regRedirectionUrl . '", heateorSlSteamAuthUrl = "' . ( $heateorSlSteamLogin ? $heateorSlSteamLogin->url( esc_url( home_url() ) . '?HeateorSlSteamAuth=' . $twitterRedirect ) : '' ) . '", heateorMSEnabled = 0; var heateorSlTwitterAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Twitter&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlFacebookAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Facebook&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlGithubAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Github&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlKakaoAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Kakao&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlSpotifyAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Spotify&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDribbbleAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Dribbble&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlWordpressAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Wordpress&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlYahooAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Yahoo&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlGoogleAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Google&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlVkontakteAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Vkontakte&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlLinkedinAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Linkedin&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlInstagramAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Instagram&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlLineAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Line&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlLiveAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Live&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlTwitchAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Twitch&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlRedditAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Reddit&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDisqusAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Disqus&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlFoursquareAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Foursquare&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDropboxAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Dropbox&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlAmazonAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Amazon&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl;  var heateorSlStackoverflowAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Stackoverflow&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDiscordAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Discord&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlMailruAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Mailru&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl;</script>';
			$user_verified = false;
			$ajax_url = 'admin-ajax.php';
			$notification = '';
			wp_enqueue_script( 'heateor_sl_common', plugins_url( 'js/heateor-social-login-public.js', __FILE__ ), array( 'jquery' ), HEATEOR_SOCIAL_LOGIN_VERSION );
			
			// linking functions
			wp_enqueue_script( 'heateor_sl_linking_script', plugins_url( '../admin/js/heateor-social-login-linking.js', __FILE__ ), array( 'jquery' ), HEATEOR_SOCIAL_LOGIN_VERSION );
			$html .= '<style type="text/css">table.heateorSlTable td{padding:10px;}div.heateor_sl_optin_container a{color:blue}div.heateor_sl_optin_container label{font-size:11px;font-weight:normal}input.heateor_sl_social_login_optin{vertical-align:middle}</style>';

			$html .= '<div class="metabox-holder columns-2 heateor-social-login-linking-container" id="post-body">
	            <div class="stuffbox" style="width:60%; padding-bottom:10px">
	                <div class="inside" style="padding:0">
	                    <table class="form-table editcomment heateorSlTable">
	                        <tbody>';
	                        if ( isset( $_GET['linked'] ) ) {
	                        	if ( intval( $_GET['linked'] ) == 1 ) {
		                        	$html .= '<tr>
		                        		<td colspan="2" style="color: green">' . __( 'Account linked successfully', 'heateor-social-login' ) . '</td>
		                        	</tr>';
	                        	} elseif ( intval( $_GET['linked'] ) == 0 ) {
		                        	$html .= '<tr>
		                        		<td colspan="2" style="color: red">' . __( 'Account already exists or linked', 'heateor-social-login' ) . '</td>
		                        	</tr>';
	                        	}
	                        }
	                        $icons_container = '<div class="heateor_sl_login_container">';
	                        if ( isset( $this->options['gdpr_enable'] ) ) {
								$gdpr_opt_in = '<div class="heateor_sl_optin_container"><label><input type="checkbox" class="heateor_sl_social_login_optin" value="1" />' . str_replace( array( $this->options['ppu_placeholder'], $this->options['tc_placeholder'] ), array( '<a href="' . $this->options['privacy_policy_url'] . '" target="_blank">' . $this->options['ppu_placeholder'] . '</a>', '<a href="' . $this->options['tc_url'] . '" target="_blank">' . $this->options['tc_placeholder'] . '</a>' ), wp_strip_all_tags( $this->options['privacy_policy_optin_text'] ) ) . '</label></div>';
							}
							if ( isset( $this->options['gdpr_enable'] ) && $this->options['gdpr_placement'] == 'above' ) {
								$icons_container .= $gdpr_opt_in;
							}
	                        $icons_container .= '<ul class="heateor_sl_login_ul">';
							$existing_providers = array();
							$primary_social_network = get_user_meta( $user_ID, 'heateorsl_provider', true );
							$existing_providers[] = $primary_social_network;
							$linked_accounts = get_user_meta( $user_ID, 'heateorsl_linked_accounts', true );
							if ( $linked_accounts ) {
								$linked_accounts = maybe_unserialize( $linked_accounts );
								$linked_providers = array_keys( $linked_accounts );
								$existing_providers = array_merge( $existing_providers, $linked_providers );
							}
							
							if ( isset( $this->options['providers'] ) ) {
								$existing_providers = array_diff( $this->options['providers'], $existing_providers );
	                        }
							if (count( $existing_providers ) > 0 ) {
	                        $html .= '<tr>
	                            <td colspan="2"><strong>' . $this->options['scl_title'] . '</strong><br/>';
								foreach( $existing_providers as $provider ) {
									$icons_container .= '<li><i ';
									// id
									if ( $provider == 'google' ) {
										$icons_container .= 'id="heateorSl' . ucfirst( $provider ) . 'Button" ';
									}
									// class
									$icons_container .= 'class="heateorSlLogin heateorSl' . ucfirst( $provider ) . 'Background heateorSl' . ucfirst( $provider ) . 'Login" ';
									$icons_container .= 'alt="Login with ';
									$icons_container .= ucfirst( $provider );
									$icons_container .= '" title="Login with ';
									if ( $provider == 'live' ) {
										$icons_container .= 'Windows Live';
									} else {
										$icons_container .= ucfirst( $provider );
									}
									if (current_filter() == 'comment_form_top' ) {
										$icons_container .= '" onclick="heateorSlCommentFormLogin = true; heateorSlInitiateLogin( this, \'' . $provider . '\' )" >';
									} else {
										$icons_container .= '" onclick="heateorSlInitiateLogin( this, \'' . $provider . '\' )" >';
									}
									if ( $provider == 'facebook' ) {
										$icons_container .= '<div class="heateorSlFacebookLogoContainer">';
									}
									$icons_container .= '<div class="heateorSlLoginSvg heateorSl' . ucfirst( $provider ) . 'LoginSvg"></div>';
									if ( $provider == 'facebook' ) {
										$icons_container .= '</div>';
									}
									$icons_container .= '</i></li>';
								}
								$icons_container .= '</ul>';
								if ( isset( $this->options['gdpr_enable'] ) && $this->options['gdpr_placement'] == 'below' ) {
									$icons_container .= '<div style="clear:both"></div>';
									$icons_container .= $gdpr_opt_in;
								}
								$icons_container .= '</div>';
								$html .= $icons_container;
		                        $html .= '</td>
		                        </tr>';
		                    }
	                        $html .= '<tr>
	                            <td colspan="2">';
	                            	if ( is_array( $linked_accounts ) || $primary_social_network) {
	                            		$html .= '<table>
	                            		<tbody>';
	                            		$primarySocialId = get_user_meta( $user_ID, 'heateorsl_social_id', true );
	                            		if ( $primary_social_network && $primarySocialId ) {
	                            			$current = get_user_meta( $user_ID, 'heateorsl_current_id', true ) == get_user_meta( $user_ID, 'heateorsl_social_id', true );
		                            		$html .= '<tr>
		                            		<td style="padding: 0">' . ( $current ? '<strong>' . __( 'Currently', 'heateor-social-login' ) . ' </strong>' : '' ) . __( 'Connected with', 'heateor-social-login' ) . ' <strong>' . ucfirst( $primary_social_network) . '</strong></td><td><input type="button" onclick="heateorSlUnlink( this, \'' . $primary_social_network . '\' )" value="' . __( 'Remove', 'heateor-social-login' ) . '" /></td></tr>';
	                            		}
	                            		if ( is_array( $linked_accounts ) && count( $linked_accounts ) > 0 ) {
	                            			foreach( $linked_accounts as $key => $value ) {
		                            			$current = get_user_meta( $user_ID, 'heateorsl_current_id', true ) == $value;
		                            			$html .= '<tr>
		                            			<td style="padding: 0">' . ( $current ? '<strong>' . __( 'Currently', 'heateor-social-login' ) . ' </strong>' : '' ) . __( 'Connected with', 'heateor-social-login' ) . ' <strong>' . ucfirst( $key ) . '</strong></td><td><input type="button" onclick="heateorSlUnlink( this, \'' . $key . '\' )" value="' . __( 'Remove', 'heateor-social-login' ) . '" /></td></tr>';
		                            		}
	                            		}
	                            		$html .= '</tbody>
	                            		</table>';
	                            	}
	                            $html .= '</td>
	                        </tr>
	                    	</tbody>
	                    </table>
	                </div>
	            </div>
	        </div>';
	        return $html;
		}
		return '';
	
	}

	/**
	 * Load javascript at frontend
	 */
	public function frontend_scripts() {
		
		$in_footer = isset( $this->options['footer_script'] ) ? true : false;
		$fb_key = isset( $this->options["fb_key"] ) && $this->options["fb_key"] != "" ? $this->options["fb_key"] : "";
		$website_url = esc_url( home_url() );
		$user_verified = false;
		$notification = '';
		$email_popup = false;	
		$ajax_url = 'admin-ajax.php';
		
		// Instagram scripts
		if ( $this->social_login_provider_enabled( 'instagram' ) ) {
			?>
			<script> var heateorSlInstaId = '<?php echo ( isset( $this->options["instagram_channel_id"] ) && $this->options["instagram_channel_id"] != "" ) ? $this->options["instagram_channel_id"] : 0 ?>'; var heateorSlCurrentPageUrl = '<?php echo urlencode( $this->get_valid_url( html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) ); ?>';</script>
			<?php
		}
		?>
		<script type="text/javascript">var heateorSlDefaultLang = '<?php echo get_locale(); ?>', heateorSlCloseIconPath = '<?php echo plugins_url( 'images/close.png', __FILE__ ) ?>';
		 var heateorSlSiteUrl = '<?php echo strtok( $website_url, "?" ); ?>', heateorSlVerified = <?php echo intval( $user_verified ) ?>, heateorSlEmailPopup = <?php echo intval( $email_popup ); ?>;
		</script>
		<?php
		// scripts used for common Social Login functionality
		if ( ! is_user_logged_in() ) {
			$loadingImagePath = plugins_url( '../images/ajax_loader.gif', __FILE__ );
			$heateorSlAjaxUrl = get_admin_url() . 'admin-ajax.php';
			$redirection_url = $this->get_login_redirection_url();
			$regRedirectionUrl = $this->get_login_redirection_url( '', true );
			?>
			<script> var heateorSlLoadingImgPath = '<?php echo $loadingImagePath ?>'; var heateorSlAjaxUrl = '<?php echo $heateorSlAjaxUrl ?>'; var heateorSlRedirectionUrl = '<?php echo $redirection_url ?>'; var heateorSlRegRedirectionUrl = '<?php echo $regRedirectionUrl ?>'; </script>
			<?php
			$notification = '';
			if ( isset( $_GET['heateorSlVerified'] ) || isset( $_GET['heateorSlUnverified'] ) ) {
				$user_verified = true;
				$ajax_url = esc_url( add_query_arg( 
					array(
						'height' => 60,
						'width' => 300,
						'action' => 'heateor_sl_notify',
						'message' => urlencode( isset( $_GET['heateorSlUnverified'] ) ? __( 'Please verify your email address to login. ', 'heateor-social-login' ) : __( 'Your email has been verified. Now you can login to your account', 'heateor-social-login' ) )
					), 
					'admin-ajax.php'
				) );
				$notification = __( 'Notification', 'heateor-social-login' );
			}
			
			$email_ajax_url = 'admin-ajax.php';
			$email_popup_title = '';
			$email_popup_error_message = '';
			$email_popup_uniqueid = '';
			$email_popup_verify_message = '';
			if ( isset( $_GET['heateorSlEmail'] ) && isset( $_GET['par'] ) && trim( $_GET['par'] ) != '' ) {
				$email_popup = true;
				$email_ajax_url = esc_url( add_query_arg( 
					array(
						'height' => isset( $this->options['popup_height'] ) && $this->options['popup_height'] != '' ? esc_attr( $this->options['popup_height'] ) : 210,
						'width' => 300,
						'action' => 'heateor_sl_ask_email'
					), 
					'admin-ajax.php'
				) );
				$email_popup_title = __( 'Email required', 'heateor-social-login' );
				$email_popup_error_message = isset( $this->options["email_error_message"] ) ? $this->options["email_error_message"] : "";
				$email_popup_uniqueid = isset( $_GET['par'] ) ? sanitize_text_field( $_GET['par'] ) : '';
				$email_popup_verify_message = __( 'Please check your email inbox to complete the registration. ', 'heateor-social-login' );
			}
			global $heateor_sl_steam_login;
			$referrer_url = urlencode( $this->get_valid_url( html_entity_decode( esc_url( $this->get_http() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ) );
			$current_page_url = urldecode( $referrer_url );
			$heateor_sl_lj_auth_url = remove_query_arg( 'action', $this->get_valid_url( $current_page_url ) );
			?>
			<script> var heateorSlFBKey = '<?php echo $fb_key ?>', heateorSlSameTabLogin = '<?php echo isset( $this->options["same_tab_login"] ) ? 1 : 0; ?>', heateorSlVerified = <?php echo intval( $user_verified) ?>; var heateorSlAjaxUrl = '<?php echo html_entity_decode(admin_url() . $ajax_url ) ?>'; var heateorSlPopupTitle = '<?php echo $notification; ?>'; var heateorSlEmailPopup = <?php echo intval( $email_popup); ?>; var heateorSlEmailAjaxUrl = '<?php echo html_entity_decode(admin_url() . $email_ajax_url ); ?>'; var heateorSlEmailPopupTitle = '<?php echo $email_popup_title; ?>'; var heateorSlEmailPopupErrorMsg = '<?php echo htmlspecialchars( $email_popup_error_message, ENT_QUOTES ); ?>'; var heateorSlEmailPopupUniqueId = '<?php echo $email_popup_uniqueid; ?>'; var heateorSlEmailPopupVerifyMessage = '<?php echo $email_popup_verify_message; ?>'; var heateorSlSteamAuthUrl = "<?php echo $heateor_sl_steam_login ? $heateor_sl_steam_login->url( esc_url( home_url() ) . '?HeateorSlSteamAuth=' . $referrer_url ) : ''; ?>"; var heateorSlCurrentPageUrl = '<?php echo $referrer_url ?>'; <?php echo isset( $this->options['disable_reg'] ) && isset( $this->options['disable_reg_redirect'] ) && $this->options['disable_reg_redirect'] != '' ? 'var heateorSlDisableRegRedirect = "' . html_entity_decode(esc_url( $this->options['disable_reg_redirect'] ) ) . '";' : ''; ?> var heateorMSEnabled = 0; var heateorSlTwitterAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Twitter&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlFacebookAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Facebook&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlGoogleAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Google&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlLineAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Line&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlLiveAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Live&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlVkontakteAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Vkontakte&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlLinkedinAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Linkedin&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlSpotifyAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Spotify&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlKakaoAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Kakao&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlGithubAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Github&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlWordpressAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Wordpress&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlYahooAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Yahoo&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlInstagramAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Instagram&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDribbbleAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Dribbble&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlTwitchAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Twitch&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlRedditAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Reddit&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDisqusAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Disqus&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlFoursquareAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Foursquare&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDropboxAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Dropbox&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlAmazonAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Amazon&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlStackoverflowAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Stackoverflow&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlDiscordAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Discord&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl; var heateorSlMailruAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Mailru&heateor_sl_redirect_to=" + heateorSlCurrentPageUrl;</script>
			<?php
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
		}
		wp_enqueue_script( 'heateor-social-login-public', plugins_url( 'js/heateor-social-login-public.js', __FILE__ ), array( 'jquery' ), HEATEOR_SOCIAL_LOGIN_VERSION, $in_footer);
	
	}

	/**
	 * Close Social Login popup
	 */
	public function close_login_popup( $redirection_url ) {

		if ( isset( $this->options['same_tab_login'] ) ) {
			wp_redirect( $redirection_url );
			die;
			return;
		}
		?>
		<script>
		if ( window.opener) {
			window.opener.location.href="<?php echo trim( $redirection_url ); ?>";
			window.close();
		} else {
			window.location.href="<?php echo trim( $redirection_url ); ?>";
		}
		</script>
		<?php
		die;
	
	}

	/**
	 * Return valid redirection url
	 */
	public function get_valid_url( $url ) {

		$decoded_url = urldecode( $url );
		if ( html_entity_decode( esc_url( remove_query_arg( array( 'ss_message', 'heateorSlVerified', 'heateorSlUnverified', 'wp_lang', 'loggedout' ), $decoded_url ) ) ) == wp_login_url() || $decoded_url == home_url() . '/wp-login.php?action=register' || $decoded_url == home_url() . '/wp-login.php?loggedout=true' ) { 
			$url = esc_url( home_url() ) . '/';
		} elseif ( isset( $_GET['redirect_to'] ) ) {
			$redirect_to = esc_url( $_GET['redirect_to'] );
			if ( urldecode( $redirect_to ) == admin_url() ) {
				$url = esc_url( home_url() ) . '/';
			} elseif ( $this->validate_url( urldecode( $redirect_to ) ) && ( strpos( urldecode( $redirect_to ), 'http://' ) !== false || strpos( urldecode( $redirect_to ), 'https://' ) !== false ) ) {
				$url = $redirect_to;
			} else {
				$url = esc_url( home_url() ) . '/';
			}
		}
		return $url;
	}

	/**
	 * Get http/https protocol at the website
	 */
	public function get_http() {

		if ( isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) {
			return "https://";
		} else {
			return "http://";
		}
	
	}


	/**
	 * Return webpage url to redirect after login
	 */
	public function get_login_redirection_url( $referrer_url = '', $register = false ) {

		global $user_ID;
		if ( $register) {
			$option = 'register';
		} else {
			$option = 'login';
		}
		$redirection_url = esc_url( home_url() );
		if ( isset( $this->options[$option. '_redirection'] ) ) {
			if ( $this->options[$option. '_redirection'] == 'same' ) {
				$http = $this->get_http();
				if ( $referrer_url != '' ) {
					$url = $referrer_url;
				} else {
					$url = html_entity_decode(esc_url( $http. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
				}
				$redirection_url = $this->get_valid_url( $url );
			} elseif ( $this->options[$option. '_redirection'] == 'homepage' ) {
				$redirection_url = esc_url( home_url() );
			} elseif ( $this->options[$option. '_redirection'] == 'account' ) {
				$redirection_url = admin_url();
			} elseif ( $this->options[$option. '_redirection'] == 'custom' && $this->options[$option. '_redirection_url'] != '' ) {
				$redirection_url = esc_url( $this->options[$option. '_redirection_url'] );
			} elseif ( $this->options[$option. '_redirection'] == 'bp_profile' && $user_ID != 0 ) {
				$redirection_url = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $user_ID ) : admin_url();
			}
		}
		$redirection_url = apply_filters( 'heateor_sl_login_redirection_url_filter', $redirection_url, $this->options, $user_ID, $referrer_url, $register);

		return $redirection_url;
	
	}

	/**
	 * Create username
	 *
	 * @since     1.1.5
	 */
	public function create_username( $profile_data ) {

		$user_name = "";
		$first_name = "";
		$last_name = "";
		
		if ( ! empty( $profile_data['username'] ) ) {
			$user_name = $profile_data['username'];
		}
		if ( ! empty( $profile_data['first_name'] ) && ! empty( $profile_data['last_name'] ) ) {
			$user_name = ! $user_name ? $profile_data['first_name'] . ' ' . $profile_data['last_name'] : $user_name;
			$first_name = $profile_data['first_name'];
			$last_name = $profile_data['last_name'];
		} elseif ( ! empty( $profile_data['name'] ) ) {
			$user_name = ! $user_name ? $profile_data['name'] : $user_name;
			$name_parts = explode( ' ', $profile_data['name'] );
			if (count( $name_parts ) > 1 ) {
				$first_name = $name_parts[0];
				$last_name = $name_parts[1];
			} else {
				$first_name = $profile_data['name'];
			}
		} elseif ( ! empty( $profile_data['username'] ) ) {
			$first_name = $profile_data['username'];
		} elseif ( isset( $profile_data['email'] ) && $profile_data['email'] != '' ) {
			$user_name = explode( '@', $profile_data['email'] );
			if ( ! $user_name ) {
				$user_name = $user_name[0];
			}
			$first_name = str_replace( "_", " ", $user_name[0] );
		} else {
			$user_name = ! $user_name ? $profile_data['id'] : $user_name;
			$first_name = $profile_data['id'];
		}
		return $user_name . "|tc|" . $first_name . "|tc|" . $last_name;
	
	}
	
	/**
	 * Reate user in Wordpress database
	 *
	 * @since     1.1.5
	 */
	public function create_user( $profile_data, $verification = false ) {

		// create username, firstname and lastname
		$user_name_first_name_Last_name = explode( '|tc|', $this->create_username( $profile_data ) );
		$user_name = $user_name_first_name_Last_name[0];
		$first_name = $user_name_first_name_Last_name[1];
		$last_name = $user_name_first_name_Last_name[2];
		
		// make username unique
		$nameexists = true;
		$index = 1;
		$user_name = str_replace( ' ', '-', $user_name );
		//cyrillic username
		$user_name = sanitize_user( $user_name, true );
		if ( $user_name=='-' ) {
			$emailParts=explode( '@', $profile_data['email'] );
			$user_name = $emailParts[0];

		}

		$user_name = $user_name;

		while ( $nameexists== True ) {
			if (username_exists( $user_name )!= 0 ) {
				$index++;
				$user_name = $user_name. $index;
			} else {
				$nameexists =false;
			}
		}
		$user_name = $user_name;
		$password = wp_generate_password();

		$user_data = array(
			'user_login' => $user_name,
			'user_pass' => $password,
			'user_nicename' => sanitize_user( $first_name, true ),
			'user_email' => $profile_data['email'],
			'display_name' => $first_name,
			'nickname' => $first_name,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'description' => isset( $profile_data['bio'] ) && $profile_data['bio'] != '' ? $profile_data['bio'] : '',
			'user_url' => $profile_data['provider'] != 'facebook' && isset( $profile_data['link'] ) && $profile_data['link'] != '' ? $profile_data['link'] : '',
			'role' => get_option( 'default_role' )
		);
		
		if ( $this->is_plugin_active( 'theme-my-login/theme-my-login.php' ) ) {
			$tml_options = get_option( 'theme_my_login' );
			$tml_login_type = isset( $tml_options['login_type'] ) ? $tml_options['login_type'] : '';
			if ( $tml_login_type == 'email' ) {
				$user_data = array(
					'user_login' => $profile_data['email'],
					'user_pass' => $password,
					'user_nicename' => $profile_data['email'],
					'user_email' => $profile_data['email'],
					'display_name' => $profile_data['email'],
					'nickname' => $profile_data['email'],
					'first_name' => $first_name,
					'last_name' => $last_name,
					'description' => isset( $profile_data['bio'] ) && $profile_data['bio'] != '' ? $profile_data['bio'] : '',
					'user_url' => $profile_data['provider'] != 'facebook' && isset( $profile_data['link'] ) && $profile_data['link'] != '' ? $profile_data['link'] : '',
					'role' => get_option( 'default_role' )
				);
			}
		}
		
		$user_id = wp_insert_user( $user_data );
		if ( ! is_wp_error( $user_id ) ) {

			if ( isset( $profile_data['id'] ) && $profile_data['id'] != '' ) {
				update_user_meta( $user_id, 'heateorsl_social_id', $profile_data['id'] );
			}
			if ( isset( $profile_data['avatar'] ) && $profile_data['avatar'] != '' ) {
				update_user_meta( $user_id, 'heateorsl_avatar', $profile_data['avatar'] );
			}
			if ( isset( $profile_data['large_avatar'] ) && $profile_data['large_avatar'] != '' ) {
				update_user_meta( $user_id, 'heateorsl_large_avatar', $profile_data['large_avatar'] );
			}
			if ( ! empty( $profile_data['provider'] ) ) {
				update_user_meta( $user_id, 'heateorsl_provider', $profile_data['provider'] );
			}

			// send notification email
			$this->new_user_notification( $user_id );
			
			do_action( 'heateor_sl_user_successfully_created', $user_id, $user_data, $profile_data );
			return $user_id;
		}
		return false;
	
	}

	/**
	 * Send new user notification email
	 *
	 * @since     1.1.5
	 */
	public function new_user_notification( $user_id ) {

		
		$notification_type = '';

		if ( isset( $this->options['password_email'] ) ) {
			$notification_type = 'both';
		}
		elseif ( isset( $this->options['new_user_admin_email'] ) ) {
			$notification_type = 'admin';
		}
		if ( $notification_type ) {
			wp_new_user_notification( $user_id, null, $notification_type );
		}
	
	}

	// Replace default avatar with social avatar
	/**
	 * Check if a plugin is active
	 */
	public function is_plugin_active( $plugin_file ) {

		return in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	
	}

	/**
	 * Replace default avatar with social avatar
	 *
	 * @since     1.1.5
	 */
	public	function social_avatar( $avatar, $avuser, $size, $default, $alt = '', $args = null ) {
		if ( isset( $this->options['avatar'] ) ) {
			if ( $this->options['avatar_quality'] == 'better' ) {
				$avatar_type = 'heateorsl_large_avatar';
			} else {
				$avatar_type = 'heateorsl_avatar';
			}
			$user_id = 0;
			if ( is_numeric( $avuser ) ) {
				if ( $avuser > 0 ) {
					$user_id = $avuser;
				}
			} elseif ( is_object( $avuser ) ) {
				if ( property_exists( $avuser, 'user_id' ) AND is_numeric( $avuser->user_id ) ) {
					$user_id = $avuser->user_id;
				}
			} elseif ( is_email( $avuser ) ) {
				$user = get_user_by( 'email', $avuser );
				$user_id = isset( $user->ID ) ? $user->ID : 0;
			}
			if ( $avatar_type == 'heateorsl_large_avatar' && get_user_meta( $user_id, $avatar_type, true ) == '' ) {
				$avatar_type = 'heateorsl_avatar';
			}
			if ( ! empty( $user_id ) && ( $user_avatar = get_user_meta( $user_id, $avatar_type, true ) ) !== false && strlen( trim( $user_avatar) ) > 0 ) {
				$class = [''];
				if ( $args['class'] ) {
					if ( is_array( $args['class'] ) ) {
						$class = array_merge( $class, $args['class'] );
					} else {
						$class[] = $args['class'];
					}
				}
				return '<img alt="' . esc_attr( $alt ) . '" src="' . $user_avatar . '" class="avatar avatar-' . $size . ' '.esc_attr( implode( ' ', $class ) ).'"/>';
			}
		}
		return $avatar;
	
	}


	/**
	 * Return URL of social avater
	 *
	 * @since     1.1.5 
	 */
	public function social_avatar_url( $url, $id_or_email, $args ) {

		if ( isset( $this->options['enable'] ) && isset( $this->options['avatar'] ) ) {
			if ( isset( $this->options['avatar_quality'] ) && $this->options['avatar_quality'] == 'better' ) {
				$avatar_type = 'heateorsl_large_avatar';
			} else {
				$avatar_type = 'heateorsl_avatar';
			}
			$user_id = 0;
			if ( is_numeric( $id_or_email ) ) {
				$user = get_userdata( $id_or_email );
				if ( $id_or_email > 0 ) {
					$user_id = $id_or_email;
				}
			} elseif ( is_object( $id_or_email ) ) {
				if (property_exists( $id_or_email, 'user_id' ) AND is_numeric( $id_or_email->user_id ) ) {
					$user_id = $id_or_email->user_id;
				}
			} elseif ( is_email( $id_or_email ) ) {
				$user = get_user_by( 'email', $id_or_email );
				$user_id = isset( $user->ID ) ? $user->ID : 0;
			}

			if ( $avatar_type == 'heateorsl_large_avatar' && get_user_meta( $user_id, $avatar_type, true ) == '' ) {
				$avatar_type = 'heateorsl_avatar';
			}
			if ( ! empty( $user_id ) && ( $user_avatar = get_user_meta( $user_id, $avatar_type, true ) ) !== false && strlen( trim( $user_avatar) ) > 0 ) {
				return $user_avatar;
			}
		}
		return $url;
	
	}

	/**
	 * Get url of the image after saving it locally 
	 *
	 * @since     1.1.8
	 */
	private function save_social_avatar( $url = NULL, $name = NULL ) {
	    
	    $url = stripslashes( $url );
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		    return false;
		}
		if ( empty( $name ) ) {
		    $name = basename( $url );
		}
		$dir = wp_upload_dir();
		try {
		    $image = wp_remote_get( $url, array(
		         'timeout' => 15 
		    ) );
		    if ( ! is_wp_error( $image ) && isset( $image['response']['code'] ) && 200 === $image['response']['code'] ) {
		        $image_content    = wp_remote_retrieve_body( $image );
		        $image_type       = isset( $image['headers'] ) && isset( $image['headers']['content-type'] ) ? $image['headers']['content-type'] : '';
		        $image_type_parts = array();
		        $extension        = '';
		        if ( $image_type ) {
		            $image_type_parts = explode( '/', $image_type );
		            $extension        = $image_type_parts[1];
		        }
		        if ( ! is_string( $image_content ) || empty( $image_content ) ) {
		            return false;
		        }
		        if ( ! is_dir( $dir['basedir'] . '/heateor' ) ) {
		            wp_mkdir_p( $dir['basedir'] . '/heateor' );
		        }
		        $save = file_put_contents( $dir['basedir'] . '/heateor/' . $name . '.' . $extension, $image_content );
		        if ( !$save ) {
		            return false;
		        }
		        return $dir['baseurl'] . '/heateor/' . $name . '.' . $extension;
		    }
		} catch ( Exception $e ) {
		    return false;
		}

	}

	/**
	 * Login user to Wordpress
	 *
	 * @since     1.1.5
	 */
	public function login_user( $user_id, $profile_data = array(), $social_id = '', $update = false ) {

		$user = get_user_by( 'id', $user_id );
		
		if ( $update && ! get_user_meta( $user_id, 'heateorsl_dontupdate_avatar', true ) ) {
			if ( isset( $profile_data['avatar'] ) && $profile_data['avatar'] != '' ) {
				if ( $profile_data['provider'] == 'facebook' ) {
					$local_avatar_url = $this->save_social_avatar( $profile_data['avatar'], $profile_data['id'] );
					if ( $local_avatar_url ) {
						update_user_meta( $user_id, 'heateorsl_avatar', $local_avatar_url );
					}
				} else {
					update_user_meta( $user_id, 'heateorsl_avatar', $profile_data['avatar'] );
				}
			}
			if ( isset( $profile_data['large_avatar'] ) && $profile_data['large_avatar'] != '' ) {
				if ( $profile_data['provider'] == 'facebook' ) {
					$local_avatar_url = $this->save_social_avatar( $profile_data['large_avatar'], $profile_data['id'] . '_large' );
					if ( $local_avatar_url ) {
						update_user_meta( $user_id, 'heateorsl_large_avatar', $local_avatar_url );
					}
				} else {
					update_user_meta( $user_id, 'heateorsl_large_avatar', $profile_data['large_avatar'] );
				}
			}
		}
		if ( $social_id != '' ) {
			update_user_meta( $user_id, 'heateorsl_current_id', $social_id );
		}
		if ( isset( $this->options['gdpr_enable'] ) ) {
			update_user_meta( $user_id, 'heateorsl_gdpr_consent', 'yes' );
		}
		do_action( 'heateor_sl_login_user', $user_id, $profile_data, $social_id, $update );
		
	    clean_user_cache( $user->ID );
	    wp_clear_auth_cookie();
	    wp_set_current_user( $user_id, $user->user_login );
	    wp_set_auth_cookie( $user_id, true );
	    update_user_caches( $user );
	    
		do_action( 'wp_login', $user->user_login, $user );
	
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
	 * User authentication after Social Login
	 *
	 * @since     1.1.5
	 */
	public function user_auth( $profile_data, $provider = 'facebook', $referrer_url = '' ) {

		global $user_ID;
		// authenticate user
		// check if Social ID exists in database
		
		if ( $profile_data['id'] == '' ) {
			return array( 'status' => false, 'message' => '' );
		}
		$old_instagram_users = array();
		$old_instagram_user = ( $profile_data['provider'] == 'instagram' && ! empty( $profile_data['ig_id'] ) );
		if ( $old_instagram_user ) {
			$old_instagram_users = get_users( 'meta_key=heateorsl_social_id&meta_value=' . $profile_data['ig_id'] );
			$existing_user = $old_instagram_users;
		}
		if ( ( $old_instagram_user && count( $old_instagram_users) == 0 ) || ! $old_instagram_user ) {
			$existing_users = get_users( 'meta_key=heateorsl_social_id&meta_value=' . $profile_data['id'] );
			$existing_user = $existing_users;
		}
		// login redirection url
		$loginUrl = '';
		if ( isset( $this->options['login_redirection'] ) && $this->options['login_redirection'] == 'bp_profile' ) {
			$loginUrl = 'bp';
		}

		if (count( $existing_user ) > 0 ) {
			// user exists in the database
			if ( isset( $existing_user[0]->ID ) ) {
				if (count( $old_instagram_users) > 0) {
					update_user_meta( $existing_user[0]->ID, 'heateorsl_social_id', $profile_data['id'] );
				}
				// check if account needs verification
				if ( get_user_meta( $existing_user[0]->ID, 'heateorsl_key', true ) != '' ) {
					if ( ! in_array( $profile_data['provider'], array( 'twitter', 'instagram', 'steam' ) ) ) {
						if ( is_user_logged_in() ) {
							wp_delete_user( $existing_user[0]->ID );
							$this->link_account( $social_id, $provider, $user_ID );
							return array( 'status' => true, 'message' => 'linked' );
						} else {
							return array( 'status' => false, 'message' => 'unverified' );
						}
					}
					if ( is_user_logged_in() ) {
						wp_delete_user( $existing_user[0]->ID );
						$this->link_account( $profile_data['id'], $profile_data['provider'], $user_ID );
						$this->close_login_popup(admin_url() . '/profile.php' );	//** may be BP profile/custom profile page/wp profile page
					} else {
						$this->close_login_popup(esc_url( home_url() ) . '?HeateorSlUnverified=1' );
					}
				}
				if ( is_user_logged_in() ) {
					return array( 'status' => false, 'message' => 'not linked' );
				} else {
					// return if social login is disabled for admin accounts
					if ( $this->check_if_admin( $existing_user[0]->ID ) ) {
						return array( 'status' => false, 'message' => '' );
					}
					// hook to update profile data
					do_action( 'heateor_sl_hook_update_profile_data', $existing_user[0]->ID, $profile_data );
					$error = $this->login_user( $existing_user[0]->ID, $profile_data, $profile_data['id'], true );
					if ( isset( $error ) && $error === 0 ) {
						return array( 'status' => false, 'message' => 'inactive', 'url' => wp_login_url() . '?loggedout=true&hum=1' );
					} elseif ( get_user_meta( $existing_user[0]->ID, 'heateorsl_social_registration', true ) ) {
						// if logging in first time after email verification
						delete_user_meta( $existing_user[0]->ID, 'heateorsl_social_registration' );
						if ( isset( $this->options['register_redirection'] ) && $this->options['register_redirection'] == 'bp_profile' ) {
							return array( 'status' => true, 'message' => 'register', 'url' => bp_core_get_user_domain( $existing_user[0]->ID ) );
						} else {
							return array( 'status' => true, 'message' => 'register' );
						}
					}
					return array( 'status' => true, 'message' => '', 'url' => ( $loginUrl == 'bp' ? bp_core_get_user_domain( $existing_user[0]->ID ) : '' ) );
				}
			}
		} else {
			// check if id in linked accounts
			global $wpdb;
			$existing_instagram_user_id = '';

			if ( $old_instagram_user ) {
				$existing_instagram_user_id = $wpdb->get_var( 'SELECT user_id FROM ' . $wpdb->prefix . 'usermeta WHERE meta_key = "heateorsl_linked_accounts" and meta_value LIKE "%'. $profile_data['ig_id'] . '%"' );
				$existing_user_id = $existing_instagram_user_id;
			}
			if (( $old_instagram_user && ! $existing_instagram_user_id ) || ! $old_instagram_user ) {
				$existing_social_user_id = $wpdb->get_var( 'SELECT user_id FROM ' . $wpdb->prefix . 'usermeta WHERE meta_key = "heateorsl_linked_accounts" and meta_value LIKE "%'. $profile_data['id'] . '%"' );
				$existing_user_id = $existing_social_user_id;
			}
			if ( $existing_user_id ) {
				if ( $existing_instagram_user_id ) {
					$linked_accounts = get_user_meta( $existing_user_id, 'heateorsl_linked_accounts', true);
					$linked_accounts = maybe_unserialize( $linked_accounts);
					$linked_accounts['instagram'] = $profile_data['id'];
					update_user_meta( $existing_user_id, 'heateorsl_linked_accounts', maybe_serialize( $linked_accounts) );
				}
				if ( is_user_logged_in() ) {
					return array( 'status' => false, 'message' => 'not linked' );
				} else {
					$error = $this->login_user( $existing_user_id, $profile_data, $profile_data['id'], true);
					if (isset( $error) && $error === 0) {
						return array( 'status' => false, 'message' => 'inactive', 'url' => wp_login_url() . '?loggedout=true&hum=1' );
					}
					return array( 'status' => true, 'message' => '', 'url' => ( $loginUrl == 'bp' ? bp_core_get_user_domain( $existing_user_id ) : '' ) );
				}
			}

			// linking
			if ( is_user_logged_in() ) {
				global $user_ID;
				$provider_exists = $wpdb->get_var( 'SELECT user_id FROM ' . $wpdb->prefix . 'usermeta WHERE user_id='. $user_ID . ' and meta_key = "heateorsl_linked_accounts" and meta_value LIKE "%'. $profile_data['provider'] . '%"' );
				if ( $provider_exists ) {
					return array( 'status' => false, 'message' => 'provider exists' );
				} else {
					$this->link_account( $profile_data['id'], $profile_data['provider'], $user_ID );
					return array( 'status' => true, 'message' => 'linked' );
				}
			}
			
			if ( ! isset( $profile_data['email'] ) || $profile_data['email'] == '' ) {
				// if email is blank
				if ( ! isset( $this->options['email_required'] ) || $this->options['email_required'] != 1 ) {
					// generate dummy email
					$profile_data['email'] = $profile_data['id'] . '@'. $provider. '.com';
				} else {
					// save temporary data
					if ( $referrer_url != '' ) {
						$profile_data['twitter_redirect'] = $referrer_url;
					}
					
					$serialized_profile_data = maybe_serialize( $profile_data );
					$unique_id = mt_rand();
					update_user_meta( $unique_id, 'heateor_sl_temp_data', $serialized_profile_data );
					?>
					<div id="heateor_sl_popup_bg"></div>
			 		<div id="heateor_sl_sharing_more_providers"><button id="heateor_sl_sharing_popup_close" onclick="jQuery(this) .parent() .prev() .remove();jQuery(this) .parent() .remove();" class="close-button separated"><img src="<?php echo plugins_url( '../images/close.png', __FILE__ ) ?>" /></button><div id="heateor_sl_sharing_more_content"><div class="filter"></div><div class="all-services">
					<form action="<?php echo esc_url( home_url() ) . '/index.php'; ?>" method="post">
				        <div><?php echo esc_html( $this->options['email_popup_text'] ); ?></div>
				          <input type="text" name="heateor_sl_email" id="heateor_sl_email" placeholder="<?php _e( 'Email', 'heateor-social-login' ); ?>" class="form-control validate">
				          <input name="heateor_sl_unique_id" value="<?php echo $unique_id; ?>" type="hidden"/>
				        <input class="btn btn-default" type="submit" name="heateor_sl_email_submit" value="<?php _e( 'Submit', 'heateor-social-login' ) ?>"/>
					</form>
					</div></div></div>
					<?php
					return 'show form';
				}
			}

			// check if email exists in database
			if ( isset( $profile_data['email'] ) && $user_id = email_exists( $profile_data['email'] ) ) {
				// return if user is admin and social login is disabled for admin accounts
				if ( $this->check_if_admin( $user_id ) ) {
					return array( 'status' => false, 'message' => '' );
				}
				// email exists in WP DB
				$error = $this->login_user( $user_id, $profile_data, isset( $this->options['link_account'] ) ? $profile_data['id'] : '', true );
				if ( isset( $error ) && $error === 0 ) {
					return array( 'status' => false, 'message' => 'inactive', 'url' => wp_login_url() . '?loggedout=true&hum=1' );
				}
				if ( isset( $this->options['link_account'] ) ) {
					if ( get_user_meta( $user_id, 'heateorsl_social_id', true ) == '' ) {
						update_user_meta( $user_id, 'heateorsl_social_id', $profile_data['id'] );
						if ( get_user_meta( $user_id, 'heateorsl_provider', true ) == '' ) {
							update_user_meta( $user_id, 'heateorsl_provider', $profile_data['provider'] );
						}
					} else {
						$this->link_account( $profile_data['id'], $profile_data['provider'], $user_id );
					}
				}
				return array( 'status' => true, 'message' => '', 'url' => ( $loginUrl == 'bp' ? bp_core_get_user_domain( $user_id ) : '' ) );
			}
		}
		$custom_redirection = apply_filters( 'heateor_sl_before_user_registration', '', $profile_data );
		if ( $custom_redirection ) {
			return $custom_redirection;
		}
		do_action( 'heateor_sl_before_registration', $profile_data );
		
		// register user
		$user_id = $this->create_user( $profile_data );
		if ( $user_id ) {
			$error = $this->login_user( $user_id, $profile_data, $profile_data['id'], false ); 
			if ( isset( $error ) && $error === 0 ) {
				return array( 'status' => false, 'message' => 'inactive', 'url' => wp_login_url() . '?loggedout=true&hum=1' );
			} elseif ( isset( $this->options['register_redirection'] ) && $this->options['register_redirection'] == 'bp_profile' ) {
				return array( 'status' => true, 'message' => 'register', 'url' => bp_core_get_user_domain( $user_id ) );
			} else {
				return array( 'status' => true, 'message' => 'register' );
			}
		}
		return array( 'status' => false, 'message' => '' );
	
	}


	/**
	 * Enable social avatar in Buddypress
	 *
	 * @since     1.1.5
	 */
	public function buddypress_avatar( $text, $args ) {
		
		if ( isset( $this->options['enable'] ) && isset( $this->options['avatar'] ) ) {
			if ( is_array( $args ) ) {
				if ( ! empty( $args['object'] ) && strtolower( $args['object'] ) == 'user' ) {
					if ( ! empty( $args['item_id'] ) && is_numeric( $args['item_id'] ) ) {
						if ( ( $userData = get_userdata( $args['item_id'] ) ) !== false ) {
							if ( isset( $this->options['avatar_quality'] ) && $this->options['avatar_quality'] == 'better' ) {
								$avatar_type = 'heateorsl_large_avatar';
							} else {
								$avatar_type = 'heateorsl_avatar';
							}
							if ( $avatar_type == 'heateorsl_large_avatar' && get_user_meta( $args['item_id'], $avatar_type, true ) == '' ) {
								$avatar_type = 'heateorsl_avatar';
							}
							$avatar = '';
							if ( ( $user_avatar = get_user_meta( $args['item_id'], $avatar_type, true ) ) !== false && strlen( trim( $user_avatar) ) > 0 ) {
								$avatar = $user_avatar;
							}
							if ( $avatar != "" ) {
									$img_alt = ( ! empty( $args['alt'] ) ? 'alt="'.esc_attr( $args['alt'] ) . '" ' : '' );
									$img_alt = sprintf( $img_alt, htmlspecialchars( $userData->user_login ) );
									$img_class = ( 'class="'.( ! empty ( $args['class'] ) ? ( $args['class'] . ' ' ) : '' ) . 'avatar-social-login" ' );
									$img_width = ( ! empty ( $args['width'] ) ? 'width="'. $args['width'] . '" ' : 'width="50"' );
									$img_height = ( ! empty ( $args['height'] ) ? 'height="'. $args['height'] . '" ' : 'height="50"' );
									$text = preg_replace( '#<img[^>]+>#i', '<img src="'. $avatar. '" '. $img_alt. $img_class. $img_height. $img_width. ' style="float:left; margin-right:10px" />', $text );
							}
						}
					}
				}
			}
		}
		return $text;
	
	}

	/**
	 * Check if url is in valid format
	 *
	 * @since     1.1.5
	 */
	public function validate_url( $url ) {

		return filter_var( trim( $url ), FILTER_VALIDATE_URL );
	
	} 

	/**
	 * Sanitize profile data
	 *
	 * @since     1.1.5
	 */
	public function sanitize_profile_data( $profile_data, $provider) {

		$temp = array();
		if ( $provider == 'facebook' ) {
		    $temp['id'] = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		 	$temp['email'] = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
			$temp['name'] = isset( $profile_data->name ) ? $profile_data->name : '';
			$temp['username'] = '';
			$temp['first_name'] = isset( $profile_data->first_name ) ? $profile_data->first_name : '';
			$temp['last_name'] = isset( $profile_data->last_name ) ? $profile_data->last_name : '';
			$temp['bio'] = '';
			$temp['link'] = '';
			$temp['avatar'] = isset( $profile_data->picture_small ) && isset( $profile_data->picture_small->data ) && isset( $profile_data->picture_small->data->url ) && $this->validate_url( $profile_data->picture_small->data->url ) ? trim( $profile_data->picture_small->data->url ) : '';
			$temp['large_avatar'] = isset( $profile_data->picture_large ) && isset( $profile_data->picture_large->data ) && isset( $profile_data->picture_large->data->url ) && $this->validate_url( $profile_data->picture_large->data->url ) ? trim( $profile_data->picture_large->data->url ) : '';
		} elseif ( $provider == 'twitter' ) {
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['email']        = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
		    $temp['name']         = isset( $profile_data->name ) ? $profile_data->name : '';
		    $temp['username']     = isset( $profile_data->screen_name ) ? $profile_data->screen_name : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['bio']          = isset( $profile_data->description ) ? sanitize_text_field( $profile_data->description ) : '';
		    $temp['link']         = $temp['username'] != '' ? 'https://twitter.com/' . sanitize_user( $temp['username'] ) : '';
		    $temp['avatar']       = isset( $profile_data->profile_image_url_https ) && $this->validate_url( $profile_data->profile_image_url_https ) !== false ? trim( $profile_data->profile_image_url_https ) : '';
		    $temp['large_avatar'] = $temp['avatar'] != '' ? str_replace( '_normal', '', $temp['avatar'] ) : '';
		} elseif ( $provider == 'steam' ) {
		    $temp['id']           = isset( $profile_data->steamid ) ? sanitize_text_field( $profile_data->steamid ) : '';
		    $temp['email']        = '';
		    $temp['name']         = isset( $profile_data->realname ) ? $profile_data->realname : '';
		    $temp['username']     = isset( $profile_data->personaname ) ? $profile_data->personaname : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['bio']          = '';
		    $temp['link']         = isset( $profile_data->profileurl ) ? $profile_data->profileurl : '';
		    $temp['avatar']       = isset( $profile_data->avatarmedium ) && $this->validate_url( $profile_data->avatarmedium ) !== false ? $profile_data->avatarmedium : '';
		    $temp['large_avatar'] = isset( $profile_data->avatarfull ) && $this->validate_url( $profile_data->avatarfull ) !== false ? $profile_data->avatarfull : '';
		} elseif ( $provider == 'linkedin' ) {
		    $temp['id']           = isset( $profile_data['id'] ) ? sanitize_text_field( $profile_data['id'] ) : '';
		    $temp['email']        = isset( $profile_data['email'] ) ? sanitize_email( $profile_data['email'] ) : '';
		    $temp['name']         = '';
		    $temp['username']     = '';
		    $temp['first_name']   = isset( $profile_data['firstName'] ) ? $profile_data['firstName'] : '';
		    $temp['last_name']    = isset( $profile_data['lastName'] ) ? $profile_data['lastName'] : '';
		    $temp['bio']          = '';
		    $temp['link']         = '';
		    $temp['avatar']       = isset( $profile_data['smallAvatar'] ) && $this->validate_url( $profile_data['smallAvatar'] ) !== false ? trim( $profile_data['smallAvatar'] ) : '';
		    $temp['large_avatar'] = isset( $profile_data['largeAvatar'] ) && $this->validate_url( $profile_data['largeAvatar'] ) !== false ? trim( $profile_data['largeAvatar'] ) : '';
		} elseif ( $provider == 'google' ) {
		    $temp['id']           = isset( $profile_data->sub ) ? sanitize_text_field( $profile_data->sub ) : '';
		    $temp['email']        = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
		    $temp['name']         = isset( $profile_data->name ) ? $profile_data->name : '';
		    $temp['username']     = '';
		    $temp['first_name']   = isset( $profile_data->givenName ) ? $profile_data->givenName : '';
		    $temp['last_name']    = isset( $profile_data->familyName ) ? $profile_data->familyName : '';
		    $temp['bio']          = '';
		    $temp['link']         = isset( $profile_data->link ) ? $profile_data->link : '';
		    $temp['large_avatar'] = isset( $profile_data->picture ) && $this->validate_url( $profile_data->picture ) !== false ? trim( $profile_data->picture ) : '';
		    $temp['avatar']       = $temp['large_avatar'] != '' ? $temp['large_avatar'] . '?sz=50' : '';
		} elseif ( $provider == 'vkontakte' ) {
		    $temp['id']           = isset( $profile_data['id'] ) ? sanitize_text_field( $profile_data['id'] ) : '';
		    $temp['email']        = '';
		    $temp['name']         = '';
		    $temp['username']     = isset( $profile_data['screen_name'] ) ? $profile_data['screen_name'] : '';
		    $temp['first_name']   = isset( $profile_data['first_name'] ) ? $profile_data['first_name'] : '';
		    $temp['last_name']    = isset( $profile_data['last_name'] ) ? $profile_data['last_name'] : '';
		    $temp['bio']          = '';
		    $temp['link']         = $temp['id'] != '' ? 'https://vk.com/id' . $temp['id'] : '';
		    $temp['avatar']       = isset( $profile_data['photo_rec'] ) && $this->validate_url( $profile_data['photo_rec'] ) !== false ? trim( $profile_data['photo_rec'] ) : '';
		    $temp['large_avatar'] = isset( $profile_data['photo_big'] ) && $this->validate_url( $profile_data['photo_big'] ) !== false ? trim( $profile_data['photo_big'] ) : '';
		} elseif ( $provider == 'line' && isset( $profile_data->displayName ) && $profile_data->displayName ) {
			$temp['email'] 		  = '';
			$temp['bio'] 		  = '';
			$temp['username']     = $profile_data->displayName;
			$temp['link']         = '';
			$temp['avatar'] 	  =  isset( $profile_data->pictureUrl ) && $this->validate_url( $profile_data->pictureUrl ) !== false ? trim( $profile_data->pictureUrl ) : '';
			$temp['name'] 		  = $profile_data->displayName;
			$temp['first_name']   = $profile_data->displayName;
			$temp['last_name']    = '';
			$temp['id'] 		  = isset( $profile_data->userId ) ? sanitize_text_field( $profile_data->userId ) : '';
			$temp['large_avatar'] = '';
		} elseif ( $provider == 'mailru' ) {
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['email']        = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
		    $temp['name']         = isset( $profile_data->name ) ? $profile_data->name : '';
		    $temp['username']     =  '';
		    $temp['first_name']   = isset( $profile_data->first_name ) ? $profile_data->first_name : '';
		    $temp['last_name']    = isset( $profile_data->last_name ) ? $profile_data->last_name : '';
		    $temp['bio']          =  '';
		    $temp['link']         =  '';
		    $temp['avatar']       =  isset( $profile_data->image ) && $this->validate_url( $profile_data->image ) ? trim( $profile_data->image ) : '';
		    $temp['large_avatar'] =  '';
		} elseif ( $provider == 'microsoft' ) {
		    $temp['email']        = isset( $profile_data->emails->account ) ? sanitize_email( $profile_data->emails->account ) : '';
		    $temp['bio']          = '';
		    $temp['username']     = '';
		    $temp['link']         = '';
		    $temp['avatar']       = '';
		    $temp['name']         = isset( $profile_data->name ) ? sanitize_text_field( $profile_data->name ) : '';
		    $temp['first_name']   = isset( $profile_data->first_name ) ? sanitize_text_field( $profile_data->first_name ) : '';
		    $temp['last_name']    = isset( $profile_data->last_name ) ? sanitize_text_field( $profile_data->last_name ) : '';
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'spotify' ) {
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['email']        = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
		    $temp['name']         = '';
		    $temp['username']     = isset( $profile_data->login ) ? sanitize_text_field( $profile_data->login ) : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['bio']          = isset( $profile_data->bio ) ? sanitize_text_field( $profile_data->bio ) : '';
		    $temp['link']         = isset( $profile_data->html_url ) && $this->validate_url( $profile_data->html_url ) !== false ? trim( $profile_data->html_url ) : '';
		    $temp['avatar']       = isset( $profile_data->avatar_url ) && $this->validate_url( $profile_data->avatar_url ) !== false ? trim( $profile_data->avatar_url ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'kakao' ) {
		    $temp['email'] = '';
		    if ( isset( $profile_data->kakao_account ) && is_object( $profile_data->kakao_account ) && $profile_data->kakao_account->has_email == '1' && $profile_data->kakao_account->is_email_valid == '1' && $profile_data->kakao_account->is_email_verified == '1' && isset( $profile_data->kakao_account->email ) && $profile_data->kakao_account->email ) {
		        $temp['email'] = sanitize_email( $profile_data->kakao_account->email );
		    }
		    $temp['bio']          = '';
		    $temp['username']     = isset( $profile_data->properties ) && isset( $profile_data->properties->nickname ) && $profile_data->properties->nickname ? sanitize_text_field( $profile_data->properties->nickname ) : '';
		    $temp['link']         = '';
		    $temp['avatar']       = isset( $profile_data->properties ) && isset( $profile_data->properties->thumbnail_image ) && $profile_data->properties->thumbnail_image && $this->validate_url( $profile_data->properties->thumbnail_image ) !== false ? trim( $profile_data->properties->thumbnail_image ) : '';
		    $temp['name']         = '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['large_avatar'] = isset( $profile_data->properties ) && isset( $profile_data->properties->profile_image ) && $profile_data->properties->profile_image && $this->validate_url( $profile_data->properties->profile_image ) !== false ? trim( $profile_data->properties->profile_image ) : '';
		} elseif ( $provider == 'github' ) {
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['email']        = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
		    $temp['name']         = '';
		    $temp['username']     = isset( $profile_data->login ) ? sanitize_text_field( $profile_data->login ) : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['bio']          = isset( $profile_data->bio ) ? sanitize_text_field( $profile_data->bio ) : '';
		    $temp['link']         = isset( $profile_data->html_url ) && $this->validate_url( $profile_data->html_url ) !== false ? trim( $profile_data->html_url ) : '';
		    $temp['avatar']       = isset( $profile_data->avatar_url ) && $this->validate_url( $profile_data->avatar_url ) !== false ? trim( $profile_data->avatar_url ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'dribbble' ) {
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['email']        = '';
		    $temp['name']         = isset( $profile_data->name ) ? sanitize_text_field( $profile_data->name ) : '';
		    $temp['username']     = isset( $profile_data->login ) ? sanitize_text_field( $profile_data->login ) : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['bio']          = isset( $profile_data->bio ) ? sanitize_text_field( $profile_data->bio ) : '';
		    $temp['link']         = isset( $profile_data->html_url ) && $this->validate_url( $profile_data->html_url ) !== false ? trim( $profile_data->html_url ) : '';
		    $temp['avatar']       = isset( $profile_data->avatar_url ) && $this->validate_url( $profile_data->avatar_url ) !== false ? trim( $profile_data->avatar_url ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'wordpress' ) {
		    if ( isset( $profile_data->email_verified ) && $profile_data->email_verified == 1 && ! empty( $profile_data->email ) ) {
		        $temp['email'] = sanitize_email( $profile_data->email );
		    } else {
		        $temp['email'] = '';
		    }
		    $temp['bio']          = '';
		    $temp['username']     = isset( $profile_data->username ) ? sanitize_text_field( $profile_data->username ) : '';
		    $temp['link']         = isset( $profile_data->primary_blog_url ) && $this->validate_url( $profile_data->primary_blog_url ) !== false ? trim( $profile_data->primary_blog_url ) : '';
		    $temp['avatar']       = isset( $profile_data->avatar_URL ) && $this->validate_url( $profile_data->avatar_URL ) !== false ? trim( $profile_data->avatar_URL ) : '';
		    $temp['name']         = isset( $profile_data->display_name ) ? sanitize_text_field( $profile_data->display_name ) : '';
		    $temp['first_name']   = isset( $profile_data->display_name ) ? sanitize_text_field( $profile_data->display_name ) : '';
		    $temp['last_name']    = '';
		    $temp['id']           = isset( $profile_data->ID ) ? sanitize_text_field( $profile_data->ID ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'yahoo' ) {
		    if ( isset( $profile_data->email_verified ) && $profile_data->email_verified == 1 && ! empty( $profile_data->email ) ) {
		        $temp['email'] = sanitize_email( $profile_data->email );
		    } else {
		        $temp['email'] = '';
		    }
		    $temp['bio']          = '';
		    $temp['username']     = isset( $profile_data->nickname ) ? sanitize_text_field( $profile_data->nickname ) : '';
		    $temp['link']         = '';
		    $temp['name']         = isset( $profile_data->name ) ? sanitize_text_field( $profile_data->name ) : '';
		    $temp['first_name']   = isset( $profile_data->given_name ) ? sanitize_text_field( $profile_data->given_name ) : '';
		    $temp['last_name']    = isset( $profile_data->family_name ) ? sanitize_text_field( $profile_data->family_name ) : '';
		    $temp['id']           = isset( $profile_data->sub ) ? sanitize_text_field( $profile_data->sub ) : '';
		    $temp['large_avatar'] = isset( $profile_data->profile_images->image192 ) && $this->validate_url( $profile_data->profile_images->image192 ) !== false ? trim( $profile_data->profile_images->image192 ) : '';
		    $temp['avatar']       = isset( $profile_data->profile_images->image64 ) && $this->validate_url( $profile_data->profile_images->image64 ) !== false ? trim( $profile_data->profile_images->image64 ) : '';
		} elseif ( $provider == 'instagram' ) {
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['email']        = '';
		    $temp['name']         = '';
		    $temp['username']     = isset( $profile_data->username ) ? $profile_data->username : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['bio']          = '';
		    $temp['link']         = '';
		    $temp['avatar']       = '';
		    $temp['large_avatar'] = '';
		    $temp['ig_id']        = isset( $profile_data->ig_id ) ? sanitize_text_field( $profile_data->ig_id ) : '';
		} elseif ( $provider == 'twitch' ) {
		    $temp['email']        = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
		    $temp['bio']          = '';
		    $temp['username']     = isset( $profile_data->login ) ? sanitize_text_field( $profile_data->login ) : '';
		    $temp['link']         = $temp['username'] ? 'https://www.twitch.tv/' . $temp['username'] : '';
		    $temp['avatar']       = isset( $profile_data->profile_image_url ) && $this->validate_url( $profile_data->profile_image_url ) ? trim( $profile_data->profile_image_url ) : '';
		    $temp['name']         = isset( $profile_data->display_name ) ? sanitize_text_field( $profile_data->display_name ) : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['large_avatar'] = $temp['avatar'];
		} elseif ( $provider == 'reddit' ) {
		    $temp['email'] 		  = '';
		    $temp['bio']          = '';
		    $temp['username']     = '';
		    $temp['link']         = '';
		    $temp['avatar']       = isset( $profile_data->icon_img ) && $this->validate_url( $profile_data->icon_img ) !== false ? trim( $profile_data->icon_img ) : '';
		    $temp['name']         = isset( $profile_data->name ) ? sanitize_text_field( $profile_data->name ) : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'disqus' ) {
		    $temp['email']        = isset( $profile_data->response->email ) ? sanitize_email( $profile_data->response->email ) : '';
		    $temp['bio']          = '';
		    $temp['username']     = '';
		    $temp['link']         = isset( $profile_data->response->profileUrl ) && $this->validate_url( $profile_data->response->profileUrl ) !== false ? trim( $profile_data->response->profileUrl ) : '';
		    $temp['avatar']       = isset( $profile_data->small->permalink ) && $this->validate_url( $profile_data->small->permalink ) !== false ? trim( $profile_data->small->permalink ) : '';
		    $temp['name']         =  isset( $profile_data->response->name ) ? sanitize_text_field( $profile_data->response->name ) : '';
		    $temp['first_name']   =  '';
		    $temp['last_name']    =  '';
		    $temp['id']           = isset( $profile_data->response->id ) ? sanitize_text_field( $profile_data->response->id ) : '';
		    $temp['large_avatar'] = isset( $profile_data->large->permalink ) && $this->validate_url( $profile_data->large->permalink ) !== false ? trim( $profile_data->large->permalink ) : '';
		} elseif ( $provider == 'foursquare' ) {
			$temp['email'] = '';
		    if ( isset( $profile_data->response->user->contact->verifiedPhone ) && $profile_data->response->user->contact->verifiedPhone == true  ) {
		        $temp['email'] = sanitize_email( $profile_data->response->user->contact->email );
		    } 
		    $temp['bio']          = '';
		    $temp['username']     = '';
		    $temp['link']         = isset( $profile_data->user->canonicalUrl ) && $this->validate_url( $profile_data->user->canonicalUrl ) !== false ? trim( $profile_data->user->canonicalUrl ) : '';
		    $temp['avatar']       = isset( $profile_data->response->user ) && isset( $profile_data->response->user->photo ) && isset( $profile_data->response->user->photo->prefix ) && isset( $profile_data->response->user->photo->suffix ) ? sanitize_text_field( $profile_data->response->user->photo->prefix ) ."64x64" . sanitize_text_field( $profile_data->response->user->photo->suffix ) : '';
		    $temp['name']         = '';
		    $temp['first_name']   = isset( $profile_data->response->user->firstName ) ? sanitize_text_field( $profile_data->response->user->firstName ) : '';
		    $temp['last_name']    = isset( $profile_data->response->user->lastName ) ? sanitize_text_field( $profile_data->response->user->lastName ) : '';
		    $temp['id']           = isset( $profile_data->response->user->id ) ? sanitize_text_field( $profile_data->response->user->id ) : '';
		    $temp['large_avatar'] = isset( $profile_data->response->user ) && isset( $profile_data->response->user->photo ) && isset( $profile_data->response->user->photo->prefix ) && isset( $profile_data->response->user->photo->suffix ) ? sanitize_text_field( $profile_data->response->user->photo->prefix ) . "190x190" . sanitize_text_field( $profile_data->response->user->photo->suffix ) : '';
		} elseif ( $provider == 'dropbox' ) {
		    $temp['email'] = '';
		    if ( isset( $profile_data->email_verified ) && $profile_data->email_verified == 1 && ! empty( $profile_data->email ) ) {
		        $temp['email'] = sanitize_email( $profile_data->email );
		    }
		    $temp['bio']          = '';
		    $temp['username']     = isset( $profile_data->name->username ) ? sanitize_text_field( $profile_data->name->username ) : '';
		    $temp['link']         = '';
		    $temp['avatar']       = '';
		    $temp['name']         = isset( $profile_data->name->display_name ) ? sanitize_text_field( $profile_data->name->display_name ) : '';
		    $temp['first_name']   = isset( $profile_data->name->given_name ) ? sanitize_text_field( $profile_data->name->given_name ) : '';
		    $temp['last_name']    = isset( $profile_data->name->surname ) ? sanitize_text_field( $profile_data->name->surname ) : '';
		    $temp['id']           = isset( $profile_data->account_id ) ? sanitize_text_field( $profile_data->account_id ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'amazon' ) {
		    $temp['email']        = isset( $profile_data->email ) ? sanitize_email( $profile_data->email ) : '';
		    $temp['bio']          = '';
		    $temp['username']     = '';
		    $temp['link']         = '';
		    $temp['avatar']       = '';
		    $temp['name']         = isset( $profile_data->name ) ? $profile_data->name : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['id']           = isset( $profile_data->user_id ) ? sanitize_text_field( $profile_data->user_id ) : '';
		    $temp['large_avatar'] =  '';
		} elseif ( $provider == 'stackoverflow' ) {
		    $temp['email'] 		  = '';
		    $temp['bio']          = '';
		    $temp['username']     = '';
		    $temp['link']         = isset( $profile_data->link ) && $this->validate_url( $profile_data->link ) ? trim( $profile_data->link ) : '';
		    $temp['avatar']       = isset( $profile_data->profile_image ) && $this->validate_url( $profile_data->profile_image ) ? trim( $profile_data->profile_image ) : '';
		    $temp['name']         = isset( $profile_data->display_name ) ? $profile_data->display_name : '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['id']           = isset( $profile_data->account_id ) ? sanitize_text_field( $profile_data->account_id ) : '';
		    $temp['large_avatar'] = '';
		} elseif ( $provider == 'discord' ) {
		    $temp['email']        = '';
		    if ( ! empty( $profile_data->email ) ) {
		        $temp['email']    = sanitize_email( $profile_data->email );
		    }
		    $temp['bio']          = '';
		    $temp['username']     = isset( $profile_data->username ) ? $profile_data->username : '';
		    $temp['link']         = '';
		    $temp['avatar']       = '';
		    $temp['name']         = '';
		    $temp['first_name']   = '';
		    $temp['last_name']    = '';
		    $temp['id']           = isset( $profile_data->id ) ? sanitize_text_field( $profile_data->id ) : '';
		    $temp['large_avatar'] = '';
		}
		if ( $provider != 'steam' ) {
			$temp['avatar'] = str_replace( 'http://', '//', $temp['avatar'] );
			$temp['large_avatar'] = str_replace( 'http://', '//', $temp['large_avatar'] );
		}
		$temp = apply_filters( 'heateor_sl_hook_format_profile_data', $temp, $profile_data, $provider );
		$temp['name'] = isset( $temp['name'][0] ) && ctype_upper( $temp['name'][0] ) ? ucfirst( sanitize_user( $temp['name'], true ) ) : sanitize_user( $temp['name'], true );
		$temp['username'] = isset( $temp['username'][0] ) && ctype_upper( $temp['username'][0] ) ? ucfirst( sanitize_user( $temp['username'], true ) ) : sanitize_user( $temp['username'], true );
		$temp['first_name'] = isset( $temp['first_name'][0] ) && ctype_upper( $temp['first_name'][0] ) ? ucfirst( sanitize_user( $temp['first_name'], true ) ) : sanitize_user( $temp['first_name'], true );
		$temp['last_name'] = isset( $temp['last_name'][0] ) && ctype_upper( $temp['last_name'][0] ) ? ucfirst( sanitize_user( $temp['last_name'], true ) ) : sanitize_user( $temp['last_name'], true );
		$temp['provider'] = $provider;
		return $temp;
	}

	/**
	 * Start/Resume PHP Session
	 *
	 * @since     1.1.8
	 */
	private function php_session_start_resume() {

		if ( function_exists( 'session_start' ) ) {
			if ( session_status() == PHP_SESSION_NONE ) {
				session_start();
			}
		}

	}

	/**
	 * Unset PHP Session
	 *
	 * @since     1.1.8
	 */
	private function unset_php_session( $session_index ) {

		if ( isset( $_SESSION[$session_index] ) ) {
			unset( $_SESSION[$session_index] );
		}

	}

	/**
	 * Prevent Social Login if registration is disabled
	 *
	 * @since     1.1.5
	 */
	public function disable_social_registration( $profile_data ) {

		if ( isset( $this->options['disable_reg'] ) ) {
			$redirection_url = home_url();
			if ( isset( $this->options['disable_reg_redirect'] ) && $this->options['disable_reg_redirect'] != '' ) {
				$redirection_url = $this->options['disable_reg_redirect'];
			}
			$this->close_login_popup( $redirection_url );
		}
	
	}

}

	