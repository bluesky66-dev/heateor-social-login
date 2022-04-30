<?php

/**
 * The file that defines Shortcodes class
 *
 * A class definition that includes functions used for Shortcodes.
 *
 * @since      1.0.0
 *
 */

/**
 * Shortcodes class.
 *
 * This is used to define functions for Shortcodes.
 *
 * @since      1.0.0
 */
class Heateor_Social_Login_Shortcodes {

	/**
	 * Options saved in database.
	 *
	 * @since     1.1.5
	 */
	private $options;

	/**
	 * Member to assign object of heateor social login_Public Class.
	 *
	 * @since     1.1.5
	 */
	private $public_class_object;

	/**
	 * Assign plugin options to private member $options.
	 *
	 * @since     1.1.5
	 */
	public function __construct( $options, $public_class_object ) {

		$this->options = $options;
		$this->public_class_object = $public_class_object;

	}

	/** 
	 * Shortcode for Social Login.
	 *
	 * @since     1.1.5
	 */ 
	public function shortcode( $params ) {

		extract( shortcode_atts( array(
			'style' => '',
			'title' => '',
			'redirect_url' => '',
			'show_username' => 'OFF'
		), $params ) );
		if ( $show_username == 'ON' && is_user_logged_in() ) {
			global $user_ID;
			$userInfo = get_userdata( $user_ID );
			$html = "<div style='height:80px;width:180px'><div style='width:63px;float:left;'>";
			$html .= @get_avatar( $user_ID, 60, $default, $alt );
			$html .= "</div><div style='float:left; margin-left:10px'>";
			$html .= str_replace( '-', ' ', $userInfo->user_login );
			$html .= '<br/><a href="' . wp_logout_url(esc_url( home_url() ) ) . '">' . __( 'Log Out', 'heateor-social-login' ) . '</a></div></div>';
		} else {
			$html = '<div ';
			// style 
			if ( $style != "" ) {
				if ( strpos( $style, 'float' ) === false ) {
					$style = 'float: left;' . $style;
				}
				$html .= 'style="' . $style . '"';
			}
			$html .= '>';
			if ( ! is_user_logged_in() && $title != '' ) {
				$html .= '<div style="font-weight:bold" class="heateor_sl_social_login_title">' . ucfirst( $title ) . '</div>';
			}
			$admin_object = new Heateor_Social_Login_Admin( $this->options, HEATEOR_SOCIAL_LOGIN_VERSION );
			$html .= $admin_object->buttons( true );
			$html .= '</div><div style="clear:both"></div>';
			if ( $redirect_url ) {
				$html .= '<script type="text/javascript">heateorSlCustomRedirect = encodeURI( "' . $redirect_url . '" ); var heateorSlSteamAuthUrl = "";var heateorSlTwitterAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Twitter&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlFacebookAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Facebook&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlGithubAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Github&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlKakaoAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Kakao&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlSpotifyAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Spotify&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlDribbbleAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Dribbble&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlWordpressAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Wordpress&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlYahooAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Yahoo&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlGoogleAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Google&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlVkontakteAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Vkontakte&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlLinkedinAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Linkedin&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlInstagramAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Instagram&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlLineAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Line&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlLiveAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Live&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlTwitchAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Twitch&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlRedditAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Reddit&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlDisqusAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Disqus&heateor_sl_redirect_to=" + heateorSlCustomRedirect; var heateorSlDropboxAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Dropbox&heateor_sl_redirect_to=" + heateorSlCustomRedirect,heateorSlFoursquareAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Foursquare&heateor_sl_redirect_to=" + heateorSlCustomRedirect,heateorSlAmazonAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Amazon&heateor_sl_redirect_to=" + heateorSlCustomRedirect;  var heateorSlStackoverflowAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Stackoverflow&heateor_sl_redirect_to=" + heateorSlCustomRedirect,heateorSlDiscordAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Discord&heateor_sl_redirect_to=" + heateorSlCustomRedirect,heateorSlMailruAuthUrl = heateorSlSiteUrl + "?HeateorSlAuth=Mailru&heateor_sl_redirect_to=" + heateorSlCustomRedirect;</script>';
			}
		}
		return $html;
	
	}

}