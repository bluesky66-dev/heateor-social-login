<?php defined( 'ABSPATH' ) or die( "Cheating........Uh!!" );

?>
<div>
	<h1>Heateor Social Login</h1>
		<?php
		echo sprintf( __( 'You can appreciate the effort put in this free plugin by rating it <a href="%s" target="_blank">here</a> and help us continue the development of this plugin by purchasing the premium add-ons and plugins <a href="%s" target="_blank">here</a>', 'heateor-social-login' ), 'https://wordpress.org/support/view/plugin-reviews/heateor-social-login', 'https://www.heateor.com/add-ons' );
		?>
	</div>
	<div class="metabox-holder">
	<form action="options.php" method="post">
		<?php settings_fields( 'heateor_sl_options' ); ?>
		<div class="menu_div" id="tabs">
			<h2 class="nav-tab-wrapper" style="height:34px">
				<ul>
					<li style="margin-left:9px"><a style="margin:0; line-height:auto !important; height:23px" class="nav-tab" href="#tabs-1"><?php _e( 'Basic Configuration', 'heateor-social-login' ) ?></a>
					</li>
					<li style="margin-left:9px"><a style="margin:0; line-height:auto !important; height:23px" class="nav-tab" href="#tabs-2"><?php _e( 'Advanced Configuration', 'heateor-social-login' ) ?></a>
					</li>
					<li style="margin-left:9px"><a style="margin:0; line-height:auto !important; height:23px" class="nav-tab" href="#tabs-3"><?php _e( 'GDPR', 'heateor-social-login' ) ?></a>
					</li>
					<li style="margin-left:9px"><a style="margin:0; height:23px" class="nav-tab" href="#tabs-4"><?php _e( 'Shortcode & Widget', 'heateor-social-login' ) ?></a>
					</li>
					<li style="margin-left:9px"><a style="margin:0; height:23px" class="nav-tab" href="#tabs-5"><?php _e( 'FAQ', 'heateor-social-login' ) ?></a>
					</li>
				</ul>
			</h2>

	<div class="menu_containt_div" id="tabs-1">
		<div class="clear"></div>
				<div class="heateor_sl_left_column">
					<div class="stuffbox">
					<h3 class="hndle"><label><?php _e( 'Basic Configuration', 'heateor-social-login' );?></label></h3>
					<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
					<tr>
					<td colspan="2"><a href="https://www.heateor.com/social-login-buttons" target="_blank" style="text-decoration:none"><input style="width: auto;padding: 10px 42px;" type="button" value="<?php _e( 'Customize Social Login Icons', 'heateor-social-login' ); ?> >>>" class="ss_demo"></a></td>
					</tr>			
						<tr>
							<th>
							
							<label for="heateor_sl_disable_reg"><?php _e( "Disable user registration via Social Login", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_disable_reg_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_disable_reg" name="heateor_sl[disable_reg]" onclick="if ( this.checked) {jQuery( '#heateor_sl_disable_reg_options' ) .css( 'display', 'table-row-group' )} else { jQuery( '#heateor_sl_disable_reg_options' ) .css( 'display', 'none' ) }" type="checkbox" <?php echo isset( $this->options['disable_reg'] ) ? 'checked="checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_disable_reg_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'After enabling this option, new users will not be able to login through social login. Only existing users will be able to social login. ', 'heateor-social-login' ) ?>
							</div>
							</td>
						</tr>
						
						<tbody id="heateor_sl_disable_reg_options" <?php echo ! isset( $this->options['disable_reg'] ) ? 'style = "display: none"' : '';?> >
						<tr>
							<th>
							
							<label for="heateor_sl_disable_reg_redirect"><?php _e( "Redirection url", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_disable_reg_redirect_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_disable_reg_redirect" name="heateor_sl[disable_reg_redirect]" type="text" value="<?php echo isset( $this->options['disable_reg_redirect'] ) ? $this->options['disable_reg_redirect'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_disable_reg_redirect_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'User will be redirected to this page after unsuccessful registration attempt via Social Login. You can specify the url of registration form or of a page showing message regarding disabled registration through Social Login. ', 'heateor-social-login' ); ?>
							</div>
							</td>
						</tr>
						</tbody>

						<tr>
							<th>
							<label for="heateor_sl_disable_sl_admin"><?php _e( "Disable Social Login for admin accounts", 'heateor-social-login' ); ?></label><img id="heateor_sl_disable_sl_admin_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_disable_sl_admin" name="heateor_sl[disable_sl_admin]" type="checkbox" <?php echo isset( $this->options['disable_sl_admin'] ) ? 'checked' : ''; ?> value="1" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_disable_sl_admin_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'After enabling this option, administrator users will not be able to login through social login. Other users will be able to login via social login.', 'heateor-social-login' ) ?>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label><?php _e( "Select Social Networks", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_providers_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_facebook" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'facebook', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="facebook" />
							<label for="heateor_sl_facebook"><?php _e( "Facebook", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_twitter" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'twitter', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="twitter" />
							<label for="heateor_sl_twitter"><?php _e( "Twitter", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_linkedin" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'linkedin', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="linkedin" />
							<label for="heateor_sl_linkedin"><?php _e( "LinkedIn", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_google" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'google', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="google" />
							<label for="heateor_sl_google"><?php _e( "Google", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_vkontakte" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'vkontakte', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="vkontakte" />
							<label for="heateor_sl_vkontakte"><?php _e( "Vkontakte", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_steam" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'steam', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="steam" />
							<label for="heateor_sl_steam"><?php _e( "Steam", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_line" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'line', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="line" />
							<label for="heateor_sl_line"><?php _e( "Line", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_stackoverflow" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'stackoverflow', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="stackoverflow" />
							<label for="heateor_sl_stackoverflow"><?php _e( "Stack Overflow", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_amazon" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'amazon', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="amazon" />
							<label for="heateor_sl_amazon"><?php _e( "Amazon", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_discord" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'discord', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="discord" />
							<label for="heateor_sl_discord"><?php _e( "Discord", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_instagram" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'instagram', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="instagram" />
							<label for="heateor_sl_instagram"><?php _e( "Instagram", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_dribbble" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'dribbble', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="dribbble" />
							<label for="heateor_sl_dribbble"><?php _e( "Dribbble", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_twitch" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'twitch', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="twitch" />
							<label for="heateor_sl_twitch"><?php _e( "Twitch", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_mailru" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'mailru', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="mailru" />
							<label for="heateor_sl_mailru"><?php _e( "Mail.ru", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_dropbox" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'dropbox', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="dropbox" />
							<label for="heateor_sl_dropbox"><?php _e( "Dropbox", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_reddit" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'reddit', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="reddit" />
							<label for="heateor_sl_reddit"><?php _e( "Reddit", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_disqus" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'disqus', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="disqus" />
							<label for="heateor_sl_disqus"><?php _e( "Disqus", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_foursquare" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'foursquare', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="foursquare" />
							<label for="heateor_sl_foursquare"><?php _e( "Foursquare", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_kakao" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'kakao', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="kakao" />
							<label for="heateor_sl_kakao"><?php _e( "Kakao", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_github" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'github', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="github" />
							<label for="heateor_sl_github"><?php _e( "Github", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_spotify" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'spotify', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="spotify" />
							<label for="heateor_sl_spotify"><?php _e( "Spotify", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_yahoo" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'yahoo', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="yahoo" />
							<label for="heateor_sl_yahoo"><?php _e( "Yahoo", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_wordpress" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'wordpress', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="wordpress" />
							<label for="heateor_sl_wordpress"><?php _e( "Wordpress", 'heateor-social-login' ); ?></label>
							</div>
							<div class="heateorSlHorizontalSharingProviderContainer">
							<input id="heateor_sl_live" name="heateor_sl[providers][]" type="checkbox" <?php echo isset( $this->options['providers'] ) && in_array( 'microsoft', $this->options['providers'] ) ? 'checked="checked"' : '';?> value="microsoft" />
							<label for="heateor_sl_live"><?php _e( "Windows Live", 'heateor-social-login' ); ?></label>
							</div>

							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_providers_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Select Social ID provider to enable in Social Login', 'heateor-social-login' ) ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							
							<label for="heateor_sl_fblogin_key"><?php _e( "Facebook App ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_slfb_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_fblogin_key" name="heateor_sl[fb_key]" type="text" value="<?php echo isset( $this->options['fb_key'] ) ? $this->options['fb_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_slfb_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Facebook Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Facebook App ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-facebook-app-id/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Site URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_fblogin_secret"><?php _e( "Facebook App Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_slfb_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_fblogin_secret" name="heateor_sl[fb_secret]" type="text" value="<?php echo isset( $this->options['fb_secret'] ) ? $this->options['fb_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_slfb_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Facebook Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Facebook App Secret', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-facebook-app-id/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Site URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<label for="heateor_sl_tw_login_key"><?php _e( "Twitter API Key", 'heateor-social-login' ); ?></label>
							<img id="heateor_sltw_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_tw_login_key" name="heateor_sl[twitter_key]" type="text" value="<?php echo isset( $this->options['twitter_key'] ) ? $this->options['twitter_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sltw_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Twitter Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Twitter API Key', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-twitter-api-key-and-secret/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Website URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Callback URLs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							
							<label for="heateor_sl_tw_login_secret"><?php _e( "Twitter API Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sltw_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_tw_login_secret" name="heateor_sl[twitter_secret]" type="text" value="<?php echo isset( $this->options['twitter_secret'] ) ? $this->options['twitter_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sltw_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Twitter Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Twitter API Secret', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-twitter-api-key-and-secret/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Website URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Callback URLs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<label for="heateor_sl_lilogin_key"><?php _e( "LinkedIn Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_slli_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_lilogin_key" name="heateor_sl[li_key]" type="text" value="<?php echo isset( $this->options['li_key'] ) ? $this->options['li_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_slli_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for LinkedIn Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get LinkedIn Client ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-linkedin-api-key/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Redirect URLs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/?HeateorSlAuth=Linkedin'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_li_login_secret"><?php _e( "LinkedIn Client Secret ", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_li_login_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_li_login_secret" name="heateor_sl[li_secret]" type="text" value="<?php echo isset( $this->options['li_secret'] ) ? $this->options['li_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_li_login_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for LinkedIn Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get LinkedIn Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-linkedin-api-key/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Redirect URLs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/?HeateorSlAuth=Linkedin'; ?></strong>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<label for="heateor_sl_gplogin_key"><?php _e( "Google Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_slgp_id_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_gplogin_key" name="heateor_sl[google_key]" type="text" value="<?php echo isset( $this->options['google_key'] ) ? $this->options['google_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_slgp_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Google Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Google Client ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-google-plus-client-id/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>AUTHORIZED REDIRECT URI</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							
							<label for="heateor_sl_gplogin_secret"><?php _e( "Google Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_slgp_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_gplogin_secret" name="heateor_sl[google_secret]" type="text" value="<?php echo isset( $this->options['google_secret'] ) ? $this->options['google_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_slgp_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Google Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Google Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-google-plus-client-id/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>AUTHORIZED REDIRECT URI</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<label for="heateor_sl_vklogin_key"><?php _e( "Vkontakte Application ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_slvk_id_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_vklogin_key" name="heateor_sl[vk_key]" type="text" value="<?php echo isset( $this->options['vk_key'] ) ? $this->options['vk_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_slvk_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Vkontakte Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Vkontakte Application ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-vkontakte-application-id/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Site address</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_vklogin_secure_key"><?php _e( "Vkontakte Secure key", 'heateor-social-login' ); ?></label>
							<img id="heateor_slvk_secure_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_vklogin_secure_key" name="heateor_sl[vk_secure_key]" type="text" value="<?php echo isset( $this->options['vk_secure_key'] ) ? $this->options['vk_secure_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_slvk_secure_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Vkontakte Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Vkontakte Application ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-vkontakte-application-id/' ) ?>
							<br/>
							<span style="color: #14ACDF"><?php _e( 'Paste following url in <strong>Site address</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							
							<label for="heateor_sl_steam_key"><?php _e( "Steam API Key", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_steam_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_steam_key" name="heateor_sl[steam_api_key]" type="text" value="<?php echo isset( $this->options['steam_api_key'] ) ? $this->options['steam_api_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_steam_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Steam Social Login to work. Get it at <a href="%s" target="_blank">this link</a>', 'heateor-social-login' ), 'https://steamcommunity.com/dev/apikey' ); ?><br/>
							<span style="color: #14ACDF"><?php _e( 'Save following <strong>domain</strong> to get the key', 'heateor-social-login' ); ?></span><br/>
							<strong style="color: #14ACDF"><?php echo esc_url( home_url() ); ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_line_id"><?php _e( "Line Channel ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_line_id_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_line_id" name="heateor_sl[line_channel_id]" type="text" value="<?php echo isset( $this->options['line_channel_id'] ) ? $this->options['line_channel_id'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_line_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Line Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get it', 'heateor-social-login' ), 'http://support.heateor.com/create-line-channel-for-line-login' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Line'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_line_secret"><?php _e( "Line Channel Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_line_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_line_secret" name="heateor_sl[line_channel_secret]" type="text" value="<?php echo isset( $this->options['line_channel_secret'] ) ? $this->options['line_channel_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_line_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Line Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get it', 'heateor-social-login' ), 'http://support.heateor.com/create-line-channel-for-line-login' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Line'; ?></strong>
							</div>
							</td>
						</tr>

						<!-- Twitch -->
						<tr>
							<th>
							<label for="heateor_sl_twitch_key"><?php _e( "Twitch Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_twitch_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_twitch_key" name="heateor_sl[twitch_client_id]" type="text" value="<?php echo $this->options['twitch_client_id'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_twitch_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Twitch Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Twitch Client ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-enable-twitch-login-at-wordpress-website/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Twitch'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_twitch_secret"><?php _e( "Twitch Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_twitch_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_twitch_secret" name="heateor_sl[twitch_client_secret]" type="text" value="<?php echo $this->options['twitch_client_secret'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_twitch_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Twitch Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Twitch Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/how-to-enable-twitch-login-at-wordpress-website/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Twitch'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of Twitch -->

						<!-- Stack Overflow -->
						<tr>
							<th>
							<label for="heateor_sl_stackoverflow_client_id"><?php _e( "Stack Overflow Client Id", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_stackoverflow_client_id_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_stackoverflow_client_id" name="heateor_sl[stackoverflow_client_id]" type="text" value="<?php echo $this->options['stackoverflow_client_id'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_stackoverflow_client_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Stack Overflow Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Stack Overflow Client Id', 'heateor-social-login' ), 'http://support.heateor.com/stackoverflow-client-id-stackoverflow-client-secret' ) ?>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_stackoverflow_client_secret"><?php _e( "Stack Overflow App Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_stackoverflow_client_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_stackoverflow_client_secret" name="heateor_sl[stackoverflow_client_secret]" type="text" value="<?php echo $this->options['stackoverflow_client_secret'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_stackoverflow_client_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Stack Overflow Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Stack Overflow Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/stackoverflow-client-id-stackoverflow-client-secret' ) ?>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_stackoverflow_key"><?php _e( "Stack Overflow Key", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_stackoverflow_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_stackoverflow_key" name="heateor_sl[stackoverflow_key]" type="text" value="<?php echo $this->options['stackoverflow_key'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_stackoverflow_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Stack Overflow Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Stack Overflow App Key', 'heateor-social-login' ), 'http://support.heateor.com/stackoverflow-client-id-stackoverflow-client-secret' ) ?>
							</div>
							</td>
						</tr>
						<!-- End of Stack Overflow -->

						<!-- Amazon -->
						<tr>
							<th>
							<label for="heateor_sl_amazon_key"><?php _e( "Amazon Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_amazon_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_amazon_key" name="heateor_sl[amazon_client_id]" type="text" value="<?php echo isset( $this->options['amazon_client_id'] ) ? $this->options['amazon_client_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_amazon_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Amazon Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Amazon Client ID', 'heateor-social-login' ), 'http://support.heateor.com/amazon-client-id-client-secret-amazon-developer-center' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Allowed Return URLs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Amazon'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_amazon_secret"><?php _e( "Amazon Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_amazon_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_amazon_secret" name="heateor_sl[amazon_client_secret]" type="text" value="<?php echo isset( $this->options['amazon_client_secret'] ) ? $this->options['amazon_client_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_amazon_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Amazon Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Amazon Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/amazon-client-id-client-secret-amazon-developer-center' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Allowed Return URLs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Amazon'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of Amazon -->
						
						<!-- Discord -->
						<tr>
							<th>
							<label for="heateor_sl_discord_key"><?php _e( "Discord Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_discord_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_discord_key" name="heateor_sl[discord_client_id]" type="text" value="<?php echo isset( $this->options['discord_client_id'] ) ? $this->options['discord_client_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_discord_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Discord Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Discord Client ID', 'heateor-social-login' ), 'http://support.heateor.com/discord-client-id-discord-client-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirects</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Discord'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_discord_secret"><?php _e( "Discord Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_discord_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_discord_secret" name="heateor_sl[discord_client_secret]" type="text" value="<?php echo isset( $this->options['discord_client_secret'] ) ? $this->options['discord_client_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_discord_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Discord Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Discord Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/discord-client-id-discord-client-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirects</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Discord'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of Discord -->

						<!-- Dropbox -->
						<tr>
							<th>
							<label for="heateor_sl_dropbox_key"><?php _e( "Dropbox App Key", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_dropbox_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_dropbox_key" name="heateor_sl[dropbox_app_key]" type="text" value="<?php echo $this->options['dropbox_app_key'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_dropbox_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Dropbox Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Dropbox App Key', 'heateor-social-login' ), 'http://support.heateor.com/get-dropbox-app-key-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Dropbox'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_dropbox_secret"><?php _e( "Dropbox App Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_dropbox_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_dropbox_secret" name="heateor_sl[dropbox_app_secret]" type="text" value="<?php echo $this->options['dropbox_app_secret'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_dropbox_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Dropbox Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Dropbox App Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-dropbox-app-key-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Dropbox'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of Dropbox -->

						<!-- Reddit -->
						<tr>
							<th>
							<label for="heateor_sl_reddit_key"><?php _e( "Reddit Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_reddit_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_reddit_key" name="heateor_sl[reddit_client_id]" type="text" value="<?php echo $this->options['reddit_client_id'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_reddit_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Reddit Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Reddit Client ID', 'heateor-social-login' ), 'http://support.heateor.com/get-reddit-client-id-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect Uri</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Reddit'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_reddit_secret"><?php _e( "Reddit Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_reddit_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_reddit_secret" name="heateor_sl[reddit_client_secret]" type="text" value="<?php echo $this->options['reddit_client_secret'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_reddit_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Reddit Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Reddit Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-reddit-client-id-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect Uri</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Reddit'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of Reddit -->

						<!-- Mail.ru -->
						<tr>
							<th>
							<label for="heateor_sl_mailru_key"><?php _e( "Mail.ru Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_mailru_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_mailru_key" name="heateor_sl[mailru_client_id]" type="text" value="<?php echo $this->options['mailru_client_id'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_mailru_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Mail.ru Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Mail.ru Client ID', 'heateor-social-login' ), 'http://support.heateor.com/get-mail-ru-client-id-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in the <strong>All redirect_uri</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Mailru'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_mailru_secret"><?php _e( "Mail.ru Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_mailru_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_mailru_secret" name="heateor_sl[mailru_client_secret]" type="text" value="<?php echo $this->options['mailru_client_secret'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_mailru_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Mail.ru Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Mail.ru Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-mail-ru-client-id-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in the <strong>All redirect_uri</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Mailru'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of mail.ru -->

						<!-- Disqus -->
						<tr>
							<th>
							<label for="heateor_sl_disqus_key"><?php _e( "Disqus Public Key", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_disqus_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_disqus_key" name="heateor_sl[disqus_public_key]" type="text" value="<?php echo $this->options['disqus_public_key'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_disqus_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Disqus Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Disqus Public Key', 'heateor-social-login' ), 'http://support.heateor.com/get-disqus-public-key-and-secret-key' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Disqus'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_disqus_secret"><?php _e( "Disqus Secret Key", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_disqus_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_disqus_secret" name="heateor_sl[disqus_secret_key]" type="text" value="<?php echo $this->options['disqus_secret_key'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_disqus_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Disqus Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Disqus Secret Key', 'heateor-social-login' ), 'http://support.heateor.com/get-disqus-public-key-and-secret-key' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Disqus'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of Disqus -->

						<!-- Foursquare -->
						<tr>
							<th>
							<label for="heateor_sl_foursquare_key"><?php _e( "Foursquare Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_foursquare_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_foursquare_key" name="heateor_sl[foursquare_client_id]" type="text" value="<?php echo $this->options['foursquare_client_id'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_foursquare_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Foursquare Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Foursquare Client ID', 'heateor-social-login' ), 'http://support.heateor.com/get-foursquare-client-id-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Foursquare'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_foursquare_secret"><?php _e( "Foursquare Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_foursquare_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_foursquare_secret" name="heateor_sl[foursquare_client_secret]" type="text" value="<?php echo $this->options['foursquare_client_secret'] ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_foursquare_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Foursquare Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Foursquare Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-foursquare-client-id-and-secret' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Foursquare'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End of Foursquare -->
					
						<!-- Kakao -->
						<tr>
							<th>
							<label for="heateor_sl_kakao_id"><?php _e( "Kakao Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_kakao_id_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_kakao_id" name="heateor_sl[kakao_channel_id]" type="text" value="<?php echo isset( $this->options['kakao_channel_id'] ) ? $this->options['kakao_channel_id'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_kakao_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Kakao Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get it', 'heateor-social-login' ), 'http://support.heateor.com/get-kakao-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Kakao'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_kakao_secret"><?php _e( "Kakao Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_kakao_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_kakao_secret" name="heateor_sl[kakao_channel_secret]" type="text" value="<?php echo isset( $this->options['kakao_channel_secret'] ) ? $this->options['kakao_channel_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_kakao_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Kakao Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get it', 'heateor-social-login' ), 'http://support.heateor.com/get-kakao-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Kakao'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End Kakao -->
						<!-- Github -->
						<tr>
							<th>
							<label for="heateor_sl_github_id"><?php _e( "Github Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_github_id_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_github_id" name="heateor_sl[github_channel_id]" type="text" value="<?php echo isset( $this->options['github_channel_id'] ) ? $this->options['github_channel_id'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_github_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Github Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get it', 'heateor-social-login' ), 'http://support.heateor.com/get-github-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Github'; ?></strong>
							</div>
							</td>
						</tr>
						<tr>
							<th>
							<label for="heateor_sl_github_secret"><?php _e( "Github Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_github_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_github_secret" name="heateor_sl[github_channel_secret]" type="text" value="<?php echo isset( $this->options['github_channel_secret'] ) ? $this->options['github_channel_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="heateor_sl_help_content" id="heateor_sl_github_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Github Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get it', 'heateor-social-login' ), 'http://support.heateor.com/get-github-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Github'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End Github -->
						<!-- Wordpress -->
						<tr>
							<th>
							<label for="heateor_sl_wordpress_key"><?php _e( "Wordpress Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_wordpress_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_wordpress_key" name="heateor_sl[wordpress_channel_id]" type="text" value="<?php echo isset( $this->options['wordpress_channel_id'] ) ? $this->options['wordpress_channel_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_wordpress_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Wordpress Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Wordpress Client ID', 'heateor-social-login' ), 'http://support.heateor.com/get-wordpress-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Wordpress'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_wordpress_secret"><?php _e( "Wordpress Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_wordpress_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_wordpress_secret" name="heateor_sl[wordpress_channel_secret]" type="text" value="<?php echo isset( $this->options['wordpress_channel_secret'] ) ? $this->options['wordpress_channel_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_wordpress_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Wordpress Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Wordpress Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-wordpress-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Wordpress'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End Wordpress-->
						<!-- Spotify -->
						<tr>
							<th>
							<label for="heateor_sl_spotify_key"><?php _e( "Spotify Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_spotify_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_spotify_key" name="heateor_sl[spotify_channel_id]" type="text" value="<?php echo isset( $this->options['spotify_channel_id'] ) ? $this->options['spotify_channel_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_spotify_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Spotify Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Spotify Client ID', 'heateor-social-login' ), 'http://support.heateor.com/get-spotify-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Spotify'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_spotify_secret"><?php _e( "Spotify Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_spotify_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_spotify_secret" name="heateor_sl[spotify_channel_secret]" type="text" value="<?php echo isset( $this->options['spotify_channel_secret'] ) ? $this->options['spotify_channel_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_spotify_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Spotify Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Spotify Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-spotify-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Spotify'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End Spotify -->
						<!-- Yahoo -->
						<tr>
							<th>
							<label for="heateor_sl_yahoo_key"><?php _e( "Yahoo Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_yahoo_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_yahoo_key" name="heateor_sl[yahoo_channel_id]" type="text" value="<?php echo isset( $this->options['yahoo_channel_id'] ) ? $this->options['yahoo_channel_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_yahoo_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Yahoo Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Yahoo Client ID', 'heateor-social-login' ), 'http://support.heateor.com/get-yahoo-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Yahoo'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_yahoo_secret"><?php _e( "Yahoo Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_yahoo_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_yahoo_secret" name="heateor_sl[yahoo_channel_secret]" type="text" value="<?php echo isset( $this->options['yahoo_channel_secret'] ) ? $this->options['yahoo_channel_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_yahoo_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Yahoo Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Yahoo Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-yahoo-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Yahoo'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End Yahoo -->
						<!-- Dribbble -->
						<tr>
							<th>
							<label for="heateor_sl_dribbble_key"><?php _e( "Dribbble Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_dribbble_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_dribbble_key" name="heateor_sl[dribbble_channel_id]" type="text" value="<?php echo isset( $this->options['dribbble_channel_id'] ) ? $this->options['dribbble_channel_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_dribbble_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Dribbble Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Dribbble Client ID', 'heateor-social-login' ), 'http://support.heateor.com/get-dribbble-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Dribbble'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_dribbble_secret"><?php _e( "Dribbble Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_dribbble_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_dribbble_secret" name="heateor_sl[dribbble_channel_secret]" type="text" value="<?php echo isset( $this->options['dribbble_channel_secret'] ) ? $this->options['dribbble_channel_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_dribbble_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Dribbble Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Dribbble Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/get-dribbble-client-id-client-secret/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Dribbble'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End Dribbble -->
						<!-- Instagram -->
						<tr>
							<th>
							<label for="heateor_sl_instagram_key"><?php _e( "Instagram Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_instagram_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_instagram_key" name="heateor_sl[instagram_channel_id]" type="text" value="<?php echo isset( $this->options['instagram_channel_id'] ) ? $this->options['instagram_channel_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_instagram_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Instagram Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Instagram Client ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-instagram-client-id/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Instagram'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_instagram_secret"><?php _e( "Instagram Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_instagram_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_instagram_secret" name="heateor_sl[instagram_channel_secret]" type="text" value="<?php echo isset( $this->options['instagram_channel_secret'] ) ? $this->options['instagram_channel_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_instagram_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Instagram Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Instagram Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-instagram-client-id/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Instagram'; ?></strong>
							</div>
							</td>
						</tr>
						<!-- End Instagram -->
						
						<tr>
							<th>
							<label for="heateor_sl_live_key"><?php _e( "Microsoft Client ID", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_live_key_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_live_key" name="heateor_sl[live_channel_id]" type="text" value="<?php echo isset( $this->options['live_channel_id'] ) ? $this->options['live_channel_id'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_live_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Windows Live Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Microsoft Client ID', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-windows-live-client-id-and-client-secret-key/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Redirect URIs</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Live'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
							<label for="heateor_sl_live_secret"><?php _e( "Microsoft Client Secret", 'heateor-social-login' ); ?></label>
							<img id="heateor_sl_live_secret_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
							</th>
							<td>
							<input id="heateor_sl_live_secret" name="heateor_sl[live_channel_secret]" type="text" value="<?php echo isset( $this->options['live_channel_secret'] ) ? $this->options['live_channel_secret'] : '' ?>" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_live_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf( __( 'Required for Windows Live Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Microsoft Client Secret', 'heateor-social-login' ), 'http://support.heateor.com/how-to-get-windows-live-client-id-and-client-secret-key/' ) ?>
							<br/>
							<span style="color:#14ACDF"><?php _e( 'Paste following url in <strong>Callback URL</strong> option mentioned at the link', 'heateor-social-login' ); ?></span>
							<br/>
							<strong style="color:#14ACDF"><?php echo esc_url( home_url() ) . '/HeateorSlAuth/Live'; ?></strong>
							</div>
							</td>
						</tr>

						<tr>
							<th>
								<label for="heateor_sl_footer_script"><?php _e( "Include Javascript in website footer", 'heateor-social-login' ); ?></label>
								<img id="heateor_sl_footer_script_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />	
							</th>
							<td>
							<input id="heateor_sl_footer_script" name="heateor_sl[footer_script]" type="checkbox" <?php echo isset( $this->options['footer_script'] ) ? 'checked="checked"' : '';?> value="1" />
							</td>
						</tr>
						<tr class="heateor_sl_help_content" id="heateor_sl_footer_script_help_cont">
							<td colspan="2">
							<div>
							<?php _e( 'Load Javascript file(s) of plugin in the footer of website (recommended)', 'heateor-social-login' ); ?>
							</div>
							</td>
						</tr>
					<tr>
						<th>
						
						<label for="heateor_sl_delete_options"><?php _e( "Delete all the options on plugin deletion", 'heateor-social-login' ); ?></label>
						<img id="heateor_sl_delete_options_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<input id="heateor_sl_delete_options" name="heateor_sl[delete_options]" type="checkbox" <?php echo isset( $this->options['delete_options'] ) ? 'checked="checked"' : '';?> value="1" />
						</td>
					</tr>
					
					<tr class="heateor_sl_help_content" id="heateor_sl_delete_options_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'If enabled, plugin options will get deleted when plugin is deleted/uninstalled and you will need to reconfigure the options when you install the plugin next time. ', 'heateor-social-login' ) ?>
						</div>
						</td>
					</tr>

					<tr>
						<th>
						
						<label for="heateor_sl_custom_css"><?php _e( "Custom CSS", 'heateor-social-login' ); ?></label>
						<img id="heateor_sl_custom_css_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
						</th>
						<td>
						<textarea rows="7" cols="63" id="heateor_sl_custom_css" name="heateor_sl[custom_css]"><?php echo isset( $this->options['custom_css'] ) ? $this->options['custom_css'] : '' ?></textarea>
						</td>
					</tr>
					
					<tr class="heateor_sl_help_content" id="heateor_sl_custom_css_help_cont">
						<td colspan="2">
						<div>
						<?php _e( 'You can specify any additional CSS rules ( without &lt;style&gt; tag)', 'heateor-social-login' ) ?>
						</div>
						</td>
					</tr>	
					
					</table>
							<input style="margin-left:8px" type="submit" name="save" class="button button-primary" value="<?php _e( "Save Changes", 'heateor-social-login' ); ?>" />				
							</div>
							</div>
							
						</div>
						<?php include 'heateor-social-login-about.php'; ?>
					</div>

					<div class="menu_containt_div" id="tabs-2">
						<div class="clear"></div>
						<div class="heateor_sl_left_column">
						<div class="stuffbox">
							<h3 class="hndle"><label><?php _e( 'Social Login Options', 'heateor-social-login' );?></label></h3>
							<div class="inside">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
								<tr>
									<th>
									
									<label for="heateor_sl_fblogin_title"><?php _e( "Title", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_title_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_fblogin_title" name="heateor_sl[title]" type="text" value="<?php echo isset( $this->options['title'] ) ? $this->options['title'] : '' ?>" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_title_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Text to display above the Social Login interface', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								
								<tr>
									<th>
									
									<label for="heateor_sl_same_tab"><?php _e( "Trigger social login in the same browser tab", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_same_tab_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_same_tab" name="heateor_sl[same_tab_login]" type="checkbox" <?php echo isset( $this->options['same_tab_login'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_same_tab_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Trigger social login in the same browser tab instead of a popup window', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<tr>
									<th>
									
									<label for="heateor_sl_align"><?php _e( "Center align icons", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_align_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_align" name="heateor_sl[center_align]" type="checkbox" <?php echo isset( $this->options['center_align'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_align_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Center align social login icons', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<tr>
									<th>
									
									<label for="heateor_sl_fblogin_enableAtLogin"><?php _e( "Enable at login page", 'heateor-social-login' ); ?></label>
									<img id="heateor_slpage_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_fblogin_enableAtLogin" name="heateor_sl[enableAtLogin]" type="checkbox" <?php echo isset( $this->options['enableAtLogin'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_slpage_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Social Login interface will get enabled at the login page of your website', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								
								<tr>
									<th>
									
									<label for="heateor_sl_fblogin_enableAtRegister"><?php _e( "Enable at register page", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_regpage_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_fblogin_enableAtRegister" name="heateor_sl[enableAtRegister]" type="checkbox" <?php echo isset( $this->options['enableAtRegister'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_regpage_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Social Login interface will get enabled at the registration page of your website', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								
								<tr>
									<th>
									
									<label for="heateor_sl_fblogin_enableAtComment"><?php _e( "Enable at comment form", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_cmntform_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_fblogin_enableAtComment" name="heateor_sl[enableAtComment]" type="checkbox" <?php echo isset( $this->options['enableAtComment'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_cmntform_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Social Login interface will get enabled at your Wordpress Comment form', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<?php
								/**
								 * Check if WooCommerce is active
								 **/
								if ( $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
								    ?>
								    <tr>
										<th>
										<img id="heateor_sl_wc_before_form_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										<label for="heateor_sl_wc_before_form"><?php _e( "Enable before WooCommerce Customer Login Form", 'heateor-social-login' ); ?></label>
										</th>
										<td>
										<input id="heateor_sl_wc_before_form" name="heateor_sl[enable_before_wc]" type="checkbox" <?php echo isset( $this->options['enable_before_wc'] ) ? 'checked="checked"' : '';?> value="1" />
										</td>
									</tr>
									
									<tr class="heateor_sl_help_content" id="heateor_sl_wc_before_form_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Social Login Interface will get enabled before the customer login form at WooCommerce My Account page', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>

									<tr>
										<th>
										<img id="heateor_sl_wc_after_form_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										<label for="heateor_sl_wc_after_form"><?php _e( "Enable at WooCommerce Customer Login Form", 'heateor-social-login' ); ?></label>
										</th>
										<td>
										<input id="heateor_sl_wc_after_form" name="heateor_sl[enable_after_wc]" type="checkbox" <?php echo isset( $this->options['enable_after_wc'] ) ? 'checked="checked"' : '';?> value="1" />
										</td>
									</tr>
									
									<tr class="heateor_sl_help_content" id="heateor_sl_wc_after_form_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Integrate Social Login Interface with the customer login form at WooCommerce My Account page', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>

									<tr>
										<th>
										<img id="heateor_sl_wc_register_form_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										<label for="heateor_sl_wc_register_form"><?php _e( "Enable at WooCommerce Customer Register Form", 'heateor-social-login' ); ?></label>
										</th>
										<td>
										<input id="heateor_sl_wc_register_form" name="heateor_sl[enable_register_wc]" type="checkbox" <?php echo isset( $this->options['enable_register_wc'] ) ? 'checked="checked"' : '';?> value="1" />
										</td>
									</tr>
									
									<tr class="heateor_sl_help_content" id="heateor_sl_wc_after_form_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Integrate Social Login Interface with the customer register form at WooCommerce My Account page', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>

									<tr>
										<th>
										<img id="heateor_sl_wc_checkout_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										<label for="heateor_sl_wc_checkout"><?php _e( "Enable at WooCommerce checkout page", 'heateor-social-login' ); ?></label>
										</th>
										<td>
										<input id="heateor_sl_wc_checkout" name="heateor_sl[enable_wc_checkout]" type="checkbox" <?php echo isset( $this->options['enable_wc_checkout'] ) ? 'checked="checked"' : '';?> value="1" />
										</td>
									</tr>
									
									<tr class="heateor_sl_help_content" id="heateor_sl_wc_checkout_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Social Login Interface will get enabled at WooCommerce checkout page', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>
								    <?php
								}
								if ( ! isset( $heateorSlFacebookOptions['force_fb_comment'] ) && isset( $this->options['enable'] ) ) {
									?>
									<tr>
										<th>
										<img id="heateor_sl_approve_comment_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										<label for="heateor_sl_approve_comment"><?php _e( "Auto-approve comments made by Social Login users", 'heateor-social-login' ); ?></label>
										</th>
										<td>
										<input id="heateor_sl_approve_comment" name="heateor_sl[autoApproveComment]" type="checkbox" <?php echo isset( $this->options['autoApproveComment'] ) ? 'checked="checked"' : '';?> value="1" />
										</td>
									</tr>
									
									<tr class="heateor_sl_help_content" id="heateor_sl_approve_comment_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'If this option is enabled, and WordPress comment is made by Social Login user, comment will get approved immediately without keeping in moderation. ', 'heateor-social-login' ) ?><br/>
										<strong><?php _e( 'Note: This is not related to Facebook comments', 'heateor-social-login' ) ?></strong>
										</div>
										</td>
									</tr>
									<?php
								}
								?>
								<tr>
									<th>
									
									<label for="heateor_sl_avatar"><?php _e( "Enable social avatar", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_avatar_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_avatar" onclick="if ( this.checked) {jQuery( '#heateor_sl_avatar_options' ) .css( 'display', 'table-row-group' )} else { jQuery( '#heateor_sl_avatar_options' ) .css( 'display', 'none' ) }" name="heateor_sl[avatar]" type="checkbox" <?php echo isset( $this->options['avatar'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_avatar_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Social profile pictures of the logged in user will be displayed as profile avatar', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								<tbody id="heateor_sl_avatar_options" <?php echo ! isset( $this->options['avatar'] ) ? 'style = "display: none"' : '';?> >
								<tr>
									<th>
									
									<label><?php _e( "Avatar quality", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_avatar_quality_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_average_avatar" name="heateor_sl[avatar_quality]" type="radio" <?php echo ! isset( $this->options['avatar_quality'] ) || $this->options['avatar_quality'] == 'average' ? 'checked="checked"' : '';?> value="average" /> <label for="heateor_sl_average_avatar"><?php _e( "Average", 'heateor-social-login' ); ?></label><br/>
									<input id="heateor_sl_better_avatar" name="heateor_sl[avatar_quality]" type="radio" <?php echo isset( $this->options['avatar_quality'] ) && $this->options['avatar_quality'] == 'better' ? 'checked="checked"' : '';?> value="better" /> <label for="heateor_sl_better_avatar"><?php _e( "Best", 'heateor-social-login' ); ?></label>
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_avatar_quality_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Choose avatar quality', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<?php if ( $this->is_bp_active ) { ?>
								<tr>
									<th>
									<img id="heateor_sl_avatar_options_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									<label for="heateor_sl_avatar_options"><?php _e( "Show option for users to update social avatar at BuddyPress profile page", 'heateor-social-login' ); ?></label>
									</th>
									<td>
									<input id="heateor_sl_avatar_options" name="heateor_sl[avatar_options]" type="checkbox" <?php echo isset( $this->options['avatar_options'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_avatar_options_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'If enabled, users would be able to update their social avatar from "Profile photo" section in BuddyPress profile at front-end', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								<?php } ?>

								</tbody>
								
								<tr>
									<th>
									
									<label for="heateor_sl_email_required"><?php _e( "Email required", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_emailreq_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									
									<td>
									<input onclick="heateorSlEmailPopupOptions(this)" id="heateor_sl_email_required" name="heateor_sl[email_required]" type="checkbox" <?php echo isset( $this->options['email_required'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_emailreq_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'If enabled and Social ID provider does not provide user\'s email address on login, user will be asked to provide his/her email address. Otherwise, a dummy email will be generated', 'heateor-social-login' ) ?>
									</div>
									<img src="<?php echo plugins_url( '../../images/snaps/sl_email_required.png', __FILE__ ); ?>" />
									</td>
								</tr>
								
								<tr>
									<th>
									
									<label for="heateor_sl_password_email"><?php _e( "Send post-registration email to user to set account password", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_postreg_email_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_password_email" name="heateor_sl[password_email]" type="checkbox" <?php echo isset( $this->options['password_email'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_postreg_email_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'If enabled, an email will be sent to user after registration through Social Login, regarding his/her login credentials (username-password to be able to login via traditional login form)', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<tr>
									<th>
									
									<label for="heateor_sl_postreg_admin_email"><?php _e( "Send new user registration notification email to admin", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_postreg_admin_email_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_postreg_admin_email" name="heateor_sl[new_user_admin_email]" type="checkbox" <?php echo isset( $this->options['new_user_admin_email'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_postreg_admin_email_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'If enabled, an email will be sent to admin after new user registers through Social Login, notifying admin about the new user registration', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<tr>
									<th>
									
									<label><?php _e( "Login redirection", 'heateor-social-login' ); ?></label>
									<img id="heateor_slredirect_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td id="heateor_sl_redirection_column">
									<input id="heateor_sl_redirection_same" name="heateor_sl[login_redirection]" type="radio" <?php echo ! isset( $this->options['login_redirection'] ) || $this->options['login_redirection'] == 'same' ? 'checked="checked"' : '';?> value="same" />
									<label for="heateor_sl_redirection_same"><?php _e( 'Same page where user logged in', 'heateor-social-login' ) ?></label><br/>
									<input id="heateor_sl_redirection_home" name="heateor_sl[login_redirection]" type="radio" <?php echo isset( $this->options['login_redirection'] ) && $this->options['login_redirection'] == 'homepage' ? 'checked="checked"' : '';?> value="homepage" />
									<label for="heateor_sl_redirection_home"><?php _e( 'Homepage', 'heateor-social-login' ) ?></label><br/>
									<input id="heateor_sl_redirection_account" name="heateor_sl[login_redirection]" type="radio" <?php echo isset( $this->options['login_redirection'] ) && $this->options['login_redirection'] == 'account' ? 'checked="checked"' : '';?> value="account" />
									<label for="heateor_sl_redirection_account"><?php _e( 'Account dashboard', 'heateor-social-login' ) ?></label><br/>
									<?php if ( $this->is_bp_active ) { ?>
										<input id="heateor_sl_redirection_bp" name="heateor_sl[login_redirection]" type="radio" <?php echo isset( $this->options['login_redirection'] ) && $this->options['login_redirection'] == 'bp_profile' ? 'checked="checked"' : '';?> value="bp_profile" />
										<label for="heateor_sl_redirection_bp"><?php _e( 'BuddyPress profile page', 'heateor-social-login' ) ?></label><br/>
									<?php } ?>
									<input id="heateor_sl_redirection_custom" name="heateor_sl[login_redirection]" type="radio" <?php echo isset( $this->options['login_redirection'] ) && $this->options['login_redirection'] == 'custom' ? 'checked="checked"' : '';?> value="custom" />
									<label for="heateor_sl_redirection_custom"><?php _e( 'Custom Url', 'heateor-social-login' ) ?></label><br/>
									<input id="heateor_sl_redirection_url" name="heateor_sl[login_redirection_url]" type="text" value="<?php echo isset( $this->options['login_redirection_url'] ) ? $this->options['login_redirection_url'] : '' ?>" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_slredirect_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'User will be redirected to the selected page after Social Login', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								
								<tr>
									<th>
								
									<label><?php _e( "Registration redirection", 'heateor-social-login' ); ?></label>
										<img id="heateor_sl_register_redirect_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td id="heateor_sl_register_redirection_column">
									<input id="heateor_sl_register_redirection_same" name="heateor_sl[register_redirection]" type="radio" <?php echo ! isset( $this->options['register_redirection'] ) || $this->options['register_redirection'] == 'same' ? 'checked="checked"' : '';?> value="same" />
									<label for="heateor_sl_register_redirection_same"><?php _e( 'Same page from where user registered', 'heateor-social-login' ) ?></label><br/>
									<input id="heateor_sl_register_redirection_home" name="heateor_sl[register_redirection]" type="radio" <?php echo isset( $this->options['register_redirection'] ) && $this->options['register_redirection'] == 'homepage' ? 'checked="checked"' : '';?> value="homepage" />
									<label for="heateor_sl_register_redirection_home"><?php _e( 'Homepage', 'heateor-social-login' ) ?></label><br/>
									<input id="heateor_sl_register_redirection_account" name="heateor_sl[register_redirection]" type="radio" <?php echo isset( $this->options['register_redirection'] ) && $this->options['register_redirection'] == 'account' ? 'checked="checked"' : '';?> value="account" />
									<label for="heateor_sl_register_redirection_account"><?php _e( 'Account dashboard', 'heateor-social-login' ) ?></label><br/>
									<?php if ( $this->is_bp_active ) { ?>
										<input id="heateor_sl_register_redirection_bp" name="heateor_sl[register_redirection]" type="radio" <?php echo isset( $this->options['register_redirection'] ) && $this->options['register_redirection'] == 'bp_profile' ? 'checked="checked"' : '';?> value="bp_profile" />
										<label for="heateor_sl_register_redirection_bp"><?php _e( 'BuddyPress profile page', 'heateor-social-login' ) ?></label><br/>
									<?php } ?>
									<input id="heateor_sl_register_redirection_custom" name="heateor_sl[register_redirection]" type="radio" <?php echo isset( $this->options['register_redirection'] ) && $this->options['register_redirection'] == 'custom' ? 'checked="checked"' : '';?> value="custom" />
									<label for="heateor_sl_register_redirection_custom"><?php _e( 'Custom Url', 'heateor-social-login' ) ?></label><br/>
									<input id="heateor_sl_register_redirection_url" name="heateor_sl[register_redirection_url]" type="text" value="<?php echo isset( $this->options['register_redirection_url'] ) ? $this->options['register_redirection_url'] : '' ?>" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_register_redirect_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'User will be redirected to the selected page after registration ( first Social Login ) through Social Login', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
							</table>
							</div>
						</div>

						<div class="stuffbox">
							<h3 class="hndle"><label><?php _e( 'Social Account Linking Options', 'heateor-social-login' );?></label></h3>
							<div class="inside">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
								<tr>
									<th>
									
									<label for="heateor_sl_scl_title"><?php _e( "Title", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_scl_title_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_scl_title" name="heateor_sl[scl_title]" type="text" value="<?php echo isset( $this->options['scl_title'] ) ? $this->options['scl_title'] : '' ?>" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_scl_title_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Text to display above the Social Account Linking interface', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<tr>
									<th>
									<label for="heateor_sl_linking"><?php _e( "Link social account to already existing account, if email address matches", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_linking_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_linking" name="heateor_sl[link_account]" type="checkbox" <?php echo isset( $this->options['link_account'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_linking_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'If email address of the user\'s Social Account matches with an already existing account at your website, that social account will be linked to existing account. User would be able to manage this from Social Account Linking interface at their profile page. ', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<?php if ( $this->is_bp_active ) { ?>
								<tr>
									<th>
									<img id="heateor_sl_bp_linking_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									<label for="heateor_sl_bp_linking"><?php _e( "Enable social account linking at BuddyPress profile page", 'heateor-social-login' ); ?></label>
									</th>
									<td>
									<input id="heateor_sl_bp_linking" name="heateor_sl[bp_linking]" type="checkbox" <?php echo isset( $this->options['bp_linking'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_bp_linking_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Enable this option to show social account linking interface at BuddyPress profile page', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								<?php } ?>
								
							</table>

							</div>
						</div>

							<div class="stuffbox" <?php echo ! isset( $this->options['email_required'] ) ? 'style="display: none"' : '' ?> id="heateor_sl_email_popup_options">
							<h3 class="hndle"><label><?php _e( 'Email popup options', 'heateor-social-login' );?></label></h3>
							<div class="inside">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
								<tr>
									<th>
									
									<label for="heateor_sl_email_required_text"><?php _e( "Text on 'Email required' popup", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_emailreq_text_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<textarea rows="4" cols="40" id="heateor_sl_email_required_text" name="heateor_sl[email_popup_text]"><?php echo isset( $this->options['email_popup_text'] ) ? $this->options['email_popup_text'] : '' ?></textarea>
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_emailreq_text_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'This text will be displayed on email required popup. Leave empty if not required. ', 'heateor-social-login' ) ?>
									</div>
									<img width="550" src="<?php echo plugins_url( '../../images/snaps/sl_email_popup_message.png', __FILE__ ); ?>" />
									</td>
								</tr>
								
								<tr>
									<th>
									
									<label for="heateor_sl_email_required_error"><?php _e( "Error message for 'Email required' popup", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_emailreq_error_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<textarea rows="4" cols="40" id="heateor_sl_email_required_error" name="heateor_sl[email_error_message]"><?php echo isset( $this->options['email_error_message'] ) ? $this->options['email_error_message'] : '' ?></textarea>
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_emailreq_error_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'This message will be displayed to user if it provides invalid or already registered email', 'heateor-social-login' ) ?>
									</div>
									<img width="550" src="<?php echo plugins_url( '../../images/snaps/sl_emailreq_message.png', __FILE__ ); ?>" />
									</td>
								</tr>
								<tr class="heateor_sl_help_content" id="heateor_sl_emailver_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'If enabled, email provided by the user will be verified by sending a confirmation link to that email. User would not be able to login without verifying his/her email', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>
								
							</table>
							
							</div>

							</div>
							<p class="submit">
					<input style="margin-left:8px" type="submit" name="save" class="button button-primary" value="<?php _e( "Save Changes", 'heateor-social-login' ); ?>" />
				</p>		
						</div>
						<?php include 'heateor-social-login-about.php'; ?>
					</div>
					<div class="menu_containt_div" id="tabs-3">
						<div class="clear"></div>
						<div class="heateor_sl_left_column">
						<div class="stuffbox">
							<h3><label><?php _e( 'GDPR', 'heateor-social-login' );?></label></h3>
							<div class="inside">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
								<tr>
									<th>
									
									<label for="heateor_sl_gdpr_enable"><?php _e( "Enable GDPR opt-in", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_gdpr_enable_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
									</th>
									<td>
									<input id="heateor_sl_gdpr_enable" name="heateor_sl[gdpr_enable]" type="checkbox" <?php echo isset( $this->options['gdpr_enable'] ) ? 'checked="checked"' : '';?> value="1" />
									</td>
								</tr>
								
								<tr class="heateor_sl_help_content" id="heateor_sl_gdpr_enable_help_cont">
									<td colspan="2">
									<div>
									<?php _e( 'Enable it to show GDPR opt-in for social login and social account linking', 'heateor-social-login' ) ?>
									</div>
									</td>
								</tr>

								<tbody id="heateor_sl_gdpr_options" <?php echo ! isset( $this->options['gdpr_enable'] ) ? 'style = "display: none"' : '';?> >
									<tr>
										<th>
										
										<label for="heateor_sl_gdpr_placement_above"><?php _e( "Placement of GDPR opt-in", 'heateor-social-login' ); ?></label>
										<img id="heateor_sl_gdpr_placement_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										</th>
										<td>
										<input id="heateor_sl_gdpr_placement_above" name="heateor_sl[gdpr_placement]" type="radio" <?php echo ! isset( $this->options['gdpr_placement'] ) || $this->options['gdpr_placement'] == 'above' ? 'checked="checked"' : '';?> value="above" />
										<label for="heateor_sl_gdpr_placement_above"><?php _e( 'Above Social Login icons', 'heateor-social-login' ) ?></label><br/>
										<input id="heateor_sl_gdpr_placement_below" name="heateor_sl[gdpr_placement]" type="radio" <?php echo $this->options['gdpr_placement'] == 'below' ? 'checked="checked"' : '';?> value="below" />
										<label for="heateor_sl_gdpr_placement_below"><?php _e( 'Below Social Login icons', 'heateor-social-login' ) ?></label>
										</td>
									</tr>
									<tr class="heateor_sl_help_content" id="heateor_sl_gdpr_placement_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Placement of GDPR opt-in above or below the social login icons', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>

									<tr>
										<th>
										
										<label for="heateor_sl_privacy_policy_optin_text"><?php _e( "Opt-in text", 'heateor-social-login' ); ?></label>
										<img id="heateor_sl_privacy_policy_optin_text_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										</th>
										<td>
										<textarea rows="7" cols="63" id="heateor_sl_privacy_policy_optin_text" name="heateor_sl[privacy_policy_optin_text]"><?php echo isset( $this->options['privacy_policy_optin_text'] ) ? $this->options['privacy_policy_optin_text'] : '' ?></textarea>
										</td>
									</tr>

									<tr class="heateor_sl_help_content" id="heateor_sl_privacy_policy_optin_text_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Text for the GDPR opt-in', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>
									<tr>
								<th>
									
									<label><?php _e( "Text to link to Terms-Conditions page", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_tc_placeholder_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
								</th>
								<td>
									<input id="heateor_sl_tc_placeholder" name="heateor_sl[tc_placeholder]" type="text" value="<?php echo $this->options['tc_placeholder'] ?>" />
								</td>
							</tr>

							<tr class="heateor_sl_help_content" id="heateor_sl_tc_placeholder_help_cont">
								<td colspan="2">
								<div>
								<?php _e( 'Word(s) in the opt-in text to be linked to terms-conditions page', 'heateor-social-login' ) ?>
								</div>
								</td>
							</tr>

							<tr>
								<th>
									
									<label><?php _e( "Terms-Conditions Url", 'heateor-social-login' ); ?></label>
									<img id="heateor_sl_tc_url_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
								</th>
								<td>
									<input id="heateor_sl_tc_url" name="heateor_sl[tc_url]" type="text" value="<?php echo $this->options['tc_url'] ?>" />
								</td>
							</tr>

							<tr class="heateor_sl_help_content" id="heateor_sl_tc_url_help_cont">
								<td colspan="2">
								<div>
								<?php _e( 'Url of the terms-conditions page of your website', 'heateor-social-login' ) ?>
								</div>
								</td>
							</tr>

									<tr>
										<th>
											
											<label><?php _e( "Text to link to Privacy Policy page", 'heateor-social-login' ); ?></label>
											<img id="heateor_sl_privacy_ppu_placeholder_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										</th>
										<td>
											<input id="heateor_sl_privacy_ppu_placeholder" name="heateor_sl[ppu_placeholder]" type="text" value="<?php echo $this->options['ppu_placeholder'] ?>" />
										</td>
									</tr>

									<tr class="heateor_sl_help_content" id="heateor_sl_privacy_ppu_placeholder_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Word(s) in the opt-in text to be linked to privacy policy page', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>

									<tr>
										<th>
											
											<label><?php _e( "Privacy Policy Url", 'heateor-social-login' ); ?></label>
											<img id="heateor_sl_privacy_policy_url_help" class="heateor_sl_help_bubble" src="<?php echo plugins_url( '../../images/info.png', __FILE__ ) ?>" />
										</th>
										<td>
											<input id="heateor_sl_privacy_policy_url" name="heateor_sl[privacy_policy_url]" type="text" value="<?php echo $this->options['privacy_policy_url'] ?>" />
										</td>
									</tr>

									<tr class="heateor_sl_help_content" id="heateor_sl_privacy_policy_url_help_cont">
										<td colspan="2">
										<div>
										<?php _e( 'Url of the privacy policy page of your website', 'heateor-social-login' ) ?>
										</div>
										</td>
									</tr>
								</tbody>
								
							</table>
							<p class="submit">
								<input style="margin-left:8px" type="submit" name="save" class="button button-primary" value="<?php _e( "Save Changes", 'heateor-social-login' ); ?>" />
							</p>
							</div>
						</div>
						</div>
						<?php include 'heateor-social-login-about.php'; ?>
					</div>

					<div class="menu_containt_div" id="tabs-4">
						<div class="clear"></div>
						<div class="heateor_sl_left_column">
						<div class="stuffbox">
							<h3><label><?php _e( 'Shortcode & Widget', 'heateor-social-login' );?></label></h3>
							<div class="inside" style="padding-left:7px">
								<p><a style="text-decoration:none" href="http://support.heateor.com/heateor-social-login-shortcode-widget" target="_blank"><?php _e( 'Social Login Shortcode & Widget', 'heateor-social-login' ) ?></a></p>
							</div>
						</div>
						</div>
						<?php include 'heateor-social-login-about.php'; ?>
					</div>
					<div class="menu_containt_div" id="tabs-5">
						<div class="clear"></div>
						<div class="heateor_sl_left_column">
						<div class="stuffbox">
							<h3><label><?php _e( 'FAQ', 'heateor-social-login' );?></label></h3>
							<div class="inside faq" style="padding-left:7px">
								<p><?php _e( '<strong>Note:</strong> Plugin will not work on local server. You should have an online website for the plugin to function properly. ', 'heateor-social-login' ); ?></p>
								<p>
								<a href="javascript:void(0 )"><?php _e( 'Why is social login not working?', 'heateor-social-login' ); ?></a>
								<div><?php _e( 'Make sure that App ID and Secret (Client ID and Secret ) keys you have saved, belong to the same app', 'heateor-social-login' ); ?></div>
								</p>
								<p><a href="https://wordpress.org/support/plugin/heateor-social-login/" target="_blank"><?php _e( 'More', 'heateor-social-login' ) ?></a>...</p>
							</div>
						</div>
						</div>
						<?php include 'heateor-social-login-about.php'; ?>
					</div>
				<div class="heateor_sl_clear"></div>
				<p>
					<?php
					echo sprintf( __( 'You can appreciate the effort put in this free plugin by rating it <a href="%s" target="_blank">here</a> and help us continue the development of this plugin by purchasing the premium add-ons and plugins <a href="%s" target="_blank">here</a>', 'heateor-social-login' ), 'https://wordpress.org/support/view/plugin-reviews/heateor-social-login', 'https://www.heateor.com/add-ons' );
					?>
				</p>
				</form>
		<div class="clear"></div>
		<div class="stuffbox">
			<h3><label><?php _e( "Instagram Shoutout", 'heateor-social-login' ); ?></label></h3>
			<div class="inside" style="padding-left:10px">
			<p><?php _e( 'If you can send ( to hello@heateor.com) how our plugin is helping your business, we can share it on Instagram. You can also send any relevant hashtags and people to mention in the Instagram post. ', 'heateor-social-login' ) ?></p>
			</div>
		</div>
	</div>
</div>