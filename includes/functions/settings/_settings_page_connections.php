<?php
/**
 * Partial: Connections Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 */

// Setup
$patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
$patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'connections' ); ?>

  <div class="fictioneer-settings__content">
    <form method="post" action="options.php" class="fictioneer-form">
      <?php settings_fields( 'fictioneer-settings-connections-group' ); ?>
      <?php do_settings_sections( 'fictioneer-settings-connections-group' ); ?>

      <div class="fictioneer-columns fictioneer-columns--two-columns">

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Discord', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_discord_client_id',
                    __( 'Discord Client ID', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_discord_client_secret',
                    __( 'Discord Client Secret', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_discord_invite_link',
                    __( 'Discord invite link', 'fictioneer' )
                  );
                ?>
              </div>

              <hr>

              <div class="fictioneer-card__row">
                <p><?php printf( __( 'You can set up <a href="%s" target="_blank" rel="noreferrer noopener nofollow">channel webhooks</a> to send notifications to Discord whenever a comment, story, or chapter is first <em>published</em>. This is useful for moderation (instead of emails) and to keep people posted. These are embeds, which can be disabled in Discord.', 'fictioneer' ), 'https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_discord_channel_comments_webhook',
                    __( 'Discord comment channel webhook &bull; Shows excerpts of (private) comments!', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_discord_channel_stories_webhook',
                    __( 'Discord story channel webhook', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_discord_channel_chapters_webhook',
                    __( 'Discord chapter channel webhook', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Twitch', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_twitch_client_id',
                    __( 'Twitch Client ID', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_twitch_client_secret',
                    __( 'Twitch Client Secret', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Google', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_google_client_id',
                    __( 'Google Client ID', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_google_client_secret',
                    __( 'Google Client Secret', 'fictioneer' )
                  );
                ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Patreon', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_client_id',
                    __( 'Patreon Client ID', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_client_secret',
                    __( 'Patreon Client Secret', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_campaign_link',
                    __( 'Patreon campaign link', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_unlock_message',
                    __( 'Patreon gate message override (below link, leave empty for default)', 'fictioneer' )
                  );
                ?>
              </div>

              <hr>

              <div class="fictioneer-card__row">
                <?php _e( 'You can pull your tiers into the theme and assign them to posts, allowing eligible users to ignore passwords. Pledge thresholds work too. You still need to set a password, changes on Patreon are <b>not</b> automatically pulled, and this only works for one campaign.', 'fictioneer' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_array_input(
                    'fictioneer_patreon_global_lock_tiers',
                    __( 'Global tiers to unlock posts (comma-separated list of tier IDs)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_global_lock_amount',
                    __( 'Global pledge threshold in cents to unlock posts (leave empty to disable)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_global_lock_lifetime_amount',
                    __( 'Global lifetime pledge threshold in cents to unlock posts (leave empty to disable)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_text_input(
                    'fictioneer_patreon_global_lock_unlock_amount',
                    __( 'Global pledge threshold in cents to gate regular post unlocks (leave empty to disable)', 'fictioneer' )
                  );
                ?>
              </div>

              <div class="fictioneer-card__row">
                <?php
                  fictioneer_settings_label_checkbox(
                    'fictioneer_hide_password_form_with_patreon',
                    __( 'Hide password form on Patreon-gated posts', 'fictioneer' ),
                  );
                ?>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--buttons">
                <a class="button button--secondary" href="<?php echo fictioneer_admin_action( 'fictioneer_connection_get_patreon_tiers' ); ?>"><?php _e( 'Pull Tiers', 'fictioneer' ); ?></a>
                <a class="button button--secondary" href="<?php echo fictioneer_admin_action( 'fictioneer_connection_delete_patreon_tiers' ); ?>"><?php _e( 'Delete Tiers', 'fictioneer' ); ?></a>
              </div>

              <?php if ( $patreon_tiers ) : ?>
                <div class="fictioneer-card__row">
                  <table class="fictioneer-card__table">
                    <thead>
                      <tr>
                        <th><?php _ex( 'Tier', 'Patreon connection tier table.', 'fictioneer' ); ?></th>
                        <th><?php _ex( 'ID', 'Patreon connection tier table.', 'fictioneer' ); ?></th>
                        <th><?php _ex( 'Amount Cents', 'Patreon connection tier table.', 'fictioneer' ); ?></th>
                        <th><?php _ex( 'Published', 'Patreon connection tier table.', 'fictioneer' ); ?></th>
                      </tr>
                    </thead>
                    <tbody><?php
                      foreach ( $patreon_tiers as $tier ) {
                        $title = $tier['title'] === 'Free' ? fcntr( 'free_patreon_tier' ) : $tier['title'];

                        echo '<tr>';
                        echo "<td>{$title}</td>";
                        echo "<td>{$tier['id']}</td>";
                        echo "<td>{$tier['amount_cents']}</td>";
                        echo '<td>' . ( $tier['published'] ? __( 'Yes', 'fictioneer' ) : __( 'No', 'fictioneer' ) ) . '</td>';
                        echo '</tr>';
                      }
                    ?></tbody>
                  </table>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>

        <?php do_action( 'fictioneer_admin_settings_connections' ); ?>

      </div>

      <div class="fictioneer-actions"><?php submit_button(); ?></div>

    </form>
  </div>
</div>
