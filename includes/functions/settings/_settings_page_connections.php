<?php
/**
 * Partial: Connections Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<?php

// Setup
$action = $_GET['action'] ?? null;

?>

<div class="fictioneer-ui fictioneer-settings">

  <?php fictioneer_settings_header( 'connections' ); ?>

  <div class="fictioneer-settings__content">

    <div class="tab-content">
      <form method="post" action="options.php" class="form">
        <?php settings_fields( 'fictioneer-settings-connections-group' ); ?>
        <?php do_settings_sections( 'fictioneer-settings-connections-group' ); ?>

        <div class="columns-layout two-columns">

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Discord', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <div class="text-input row">
    								<label for="fictioneer_discord_client_id">
    									<input data-lpignore="true" name="fictioneer_discord_client_id" type="text" id="fictioneer_discord_client_id" value="<?php echo esc_attr( get_option( 'fictioneer_discord_client_id' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Discord Client ID', 'fictioneer' ) ?></span>
    								</label>
    							</div>

                  <div class="text-input row">
    								<label for="fictioneer_discord_client_secret">
    									<input data-lpignore="true" name="fictioneer_discord_client_secret" type="text" id="fictioneer_discord_client_secret" value="<?php echo esc_attr( get_option( 'fictioneer_discord_client_secret' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Discord Client Secret', 'fictioneer' ) ?></span>
    								</label>
    							</div>

                  <div class="text-input row">
    								<label for="fictioneer_discord_invite_link">
    									<input data-lpignore="true" name="fictioneer_discord_invite_link" type="url" id="fictioneer_discord_invite_link" value="<?php echo esc_attr( get_option( 'fictioneer_discord_invite_link' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Discord Invite Link', 'fictioneer' ) ?></span>
    								</label>
    							</div>

                  <div class="text-input row">
    								<label for="fictioneer_discord_channel_comments_webhook">
    									<input data-lpignore="true" name="fictioneer_discord_channel_comments_webhook" type="text" id="fictioneer_discord_channel_comments_webhook" value="<?php echo esc_attr( get_option( 'fictioneer_discord_channel_comments_webhook' ) ); ?>">
    									<span class="sub-label"><?php printf( __( 'Discord Comment Channel <a href="%1$s" target="_blank" rel="noreferrer noopener nofollow">%2$s</a>', 'fictioneer' ), 'https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks', 'Webhook' ) ?></span>
    								</label>
    							</div>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Twitch', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <div class="text-input row">
    								<label for="fictioneer_twitch_client_id">
    									<input data-lpignore="true" name="fictioneer_twitch_client_id" type="text" id="fictioneer_twitch_client_id" value="<?php echo esc_attr( get_option( 'fictioneer_twitch_client_id' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Twitch Client ID' ) ?></span>
    								</label>
    							</div>

                  <div class="text-input row">
    								<label for="fictioneer_twitch_client_secret">
    									<input data-lpignore="true" name="fictioneer_twitch_client_secret" type="text" id="fictioneer_twitch_client_secret" value="<?php echo esc_attr( get_option( 'fictioneer_twitch_client_secret' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Twitch Client Secret', 'fictioneer' ) ?></span>
    								</label>
    							</div>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Google', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <div class="text-input row">
    								<label for="fictioneer_google_client_id">
    									<input data-lpignore="true" name="fictioneer_google_client_id" type="text" id="fictioneer_google_client_id" value="<?php echo esc_attr( get_option( 'fictioneer_google_client_id' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Google Client ID', 'fictioneer' ) ?></span>
    								</label>
    							</div>

                  <div class="text-input row">
    								<label for="fictioneer_google_client_secret">
    									<input data-lpignore="true" name="fictioneer_google_client_secret" type="text" id="fictioneer_google_client_secret" value="<?php echo esc_attr( get_option( 'fictioneer_google_client_secret' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Google Client Secret', 'fictioneer' ) ?></span>
    								</label>
    							</div>

    						</div>
    					</div>
    				</div>
    			</div>

          <div class="card">
    				<div class="card-wrapper">
    					<h3 class="card-header"><?php _e( 'Patreon', 'fictioneer' ) ?></h3>
    					<div class="card-content">
    						<div class="overflow-horizontal">

                  <div class="text-input row">
    								<label for="fictioneer_patreon_client_id">
    									<input data-lpignore="true" name="fictioneer_patreon_client_id" type="text" id="fictioneer_patreon_client_id" value="<?php echo esc_attr( get_option( 'fictioneer_patreon_client_id' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Patreon Client ID', 'fictioneer' ) ?></span>
    								</label>
    							</div>

                  <div class="text-input row">
    								<label for="fictioneer_patreon_client_secret">
    									<input data-lpignore="true" name="fictioneer_patreon_client_secret" type="text" id="fictioneer_patreon_client_secret" value="<?php echo esc_attr( get_option( 'fictioneer_patreon_client_secret' ) ); ?>">
    									<span class="sub-label"><?php _e( 'Patreon Client Secret', 'fictioneer' ) ?></span>
    								</label>
    							</div>

    						</div>
    					</div>
    				</div>
    			</div>

					<?php do_action( 'fictioneer_admin_settings_connections' ); ?>

        </div>

        <div class="action-row">
          <?php submit_button(); ?>
        </div>

      </form>
    </div>
  </div>
</div>
