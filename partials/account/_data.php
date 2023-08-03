<?php
/**
 * Partial: Account - Data
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 *
 * @internal $args['user']         Current user.
 * @internal $args['is_admin']     True if the user is an administrator.
 * @internal $args['is_author']    True if the user is an author (by capabilities).
 * @internal $args['is_editor']    True if the user is an editor.
 * @internal $args['is_moderator'] True if the user is a moderator (by capabilities).
 */
?>

<?php

// Setup
$current_user = $args['user'];
$bookmarks_link = fictioneer_get_assigned_page_link( 'fictioneer_bookmarks_page' );
$bookshelf_link = fictioneer_get_assigned_page_link( 'fictioneer_bookshelf_page' );
$checkmarks = fictioneer_load_checkmarks( $current_user );
$follows = fictioneer_load_follows( $current_user );
$reminders = fictioneer_load_reminders( $current_user );
$comments_count = get_comments( ['user_id' => $current_user->ID, 'count' => true] );
$last_comment = $comments_count > 0 ? get_comments( ['user_id' => $current_user->ID, 'number' => 1] )[0] : false;
$timezone = get_user_meta( $current_user->ID, 'fictioneer_user_timezone_string', true );
$timezone = empty( $timezone ) ? get_option( 'timezone_string' ) : $timezone;
$notification_validator = get_user_meta( $current_user->ID, 'fictioneer_comment_reply_validator', true );

if ( empty( $notification_validator ) ) {
  $comment_subscriptions_count = 0;
} else {
  $comment_subscriptions_count = get_comments(
    array(
      'user_id' => $current_user->ID,
      'meta_key' => 'fictioneer_send_notifications',
      'meta_value' => $notification_validator,
      'count' => true
    )
  );
}

// Remember data to pass on to hooks
$args['follows'] = $follows;
$args['reminders'] = $reminders;
$args['checkmarks'] = $checkmarks;
$args['timezone'] = $timezone;
$args['comments_count'] = $comments_count;

// Flags
$can_follows = get_option( 'fictioneer_enable_follows' );
$can_checkmarks = get_option( 'fictioneer_enable_checkmarks' );
$can_reminders = get_option( 'fictioneer_enable_reminders' );
$can_bookmarks = get_option( 'fictioneer_enable_bookmarks' );

?>

<div id="profile-data-translations" data-cleared-success="<?php esc_attr_e( 'Data has been cleared.', 'fictioneer' ); ?>" data-cleared-error="<?php esc_attr_e( 'Error. Data could not be cleared.', 'fictioneer' ); ?>" hidden></div>

<h3 class="profile__actions-headline" id="profile-actions"><?php _e( 'Data', 'fictioneer' ) ?></h3>

<p class="profile__description"><?php
  printf(
    __( 'The following cards represent data nodes stored in your account. Anything submitted by yourself, such as comments. You can clear these nodes by clicking on the trash icon, but be aware that this is irreversible. The timestamps are based on the server’s timezone (%s).', 'fictioneer' ),
    wp_timezone_string()
  );
?></p>

<div class="profile__data profile__segment">
  <ul class="two-columns">

    <?php if ( $comments_count > 0 ) : ?>
      <li class="card _small">
        <div class="card__body polygon">
          <div class="card__header _small">
            <h3 class="card__title _with-delete _small"><?php _e( 'Comments', 'fictioneer' ); ?></h3>
          </div>
          <div class="card__main _small">
            <div class="card__content _small">
              <?php
                printf(
                  __( 'You have written <b>%1$s %2$s</b>. Last comment written on %3$s.', 'fictioneer' ),
                  $comments_count,
                  _n( 'comment', 'comments', $comments_count, 'fictioneer' ),
                  wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), date_format( date_create( $last_comment->comment_date ), 'U' ) )
                );
              ?>
            </div>
          </div>
          <button class="card__delete button-clear-comments" data-nonce="<?php echo wp_create_nonce( 'fictioneer_clear_comments' ); ?>" data-warning="<?php esc_attr_e( 'Are you sure? Comments will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
        </div>
      </li>
    <?php endif; ?>

    <?php if ( $comment_subscriptions_count > 0 ) : ?>
      <li class="card _small">
        <div class="card__body polygon">
          <div class="card__header _small">
            <h3 class="card__title _with-delete _small"><?php _e( 'Comment Subscriptions', 'fictioneer' ); ?></h3>
          </div>
          <div class="card__main _small">
            <div class="card__content _small">
              <?php
                printf(
                  __( 'You are currently subscribed to <b>%1$s %2$s</b>. You will get an email notification for each direct reply.', 'fictioneer' ),
                  $comment_subscriptions_count,
                  _n( 'comment', 'comments', $comment_subscriptions_count, 'fictioneer' )
                );
              ?>
            </div>
          </div>
          <button class="card__delete button-clear-comment-subscriptions" data-nonce="<?php echo wp_create_nonce( 'fictioneer_clear_comment_subscriptions' ); ?>" data-warning="<?php esc_attr_e( 'Are you sure? Comment subscriptions will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
        </div>
      </li>
    <?php endif; ?>

    <?php if ( $can_follows ) : ?>
      <?php $follows_count = count( $follows['data'] ); ?>
      <li class="card _small">
        <div class="card__body polygon">
          <div class="card__header _small">
            <h3 class="card__title _with-delete _small"><?php
              if ( $bookshelf_link ) {
                ?><a href="<?php echo add_query_arg( 'tab', 'follows', $bookshelf_link ) . '#tabs'; ?>"><?php echo fcntr( 'follows' ); ?></a><?php
              } else {
                echo fcntr( 'follows' );
              }
            ?></h3>
          </div>
          <div class="card__main _small">
            <div class="card__content _small">
              <?php
                printf(
                  __( 'You are currently following <b>%1$s %2$s</b>. Last modified on %3$s.', 'fictioneer' ),
                  $follows_count,
                  _n( 'story', 'stories', $follows_count, 'fictioneer' ),
                  wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $follows['updated'] / 1000 )
                );
              ?>
            </div>
          </div>
          <?php if ( $follows_count > 0 ) : ?>
            <button class="card__delete button-clear-follows" data-nonce="<?php echo wp_create_nonce( 'fictioneer_clear_follows' ); ?>" data-warning="<?php esc_attr_e( 'Are you sure? Follows will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
          <?php endif; ?>
        </div>
      </li>
    <?php endif; ?>

    <?php if ( $can_reminders ) : ?>
      <?php $reminders_count = count( $reminders['data'] ); ?>
      <li class="card _small">
        <div class="card__body polygon">
          <div class="card__header _small">
            <h3 class="card__title _with-delete _small"><?php
              if ( $bookshelf_link ) {
                ?><a href="<?php echo add_query_arg( 'tab', 'reminders', $bookshelf_link ) . '#tabs'; ?>"><?php echo fcntr( 'reminders' ); ?></a><?php
              } else {
                echo fcntr( 'reminders' );
              }
            ?></h3>
          </div>
          <div class="card__main _small">
            <div class="card__content _small">
              <?php
                printf(
                  __( 'You have <b>%1$s %2$s</b> marked to be read later. Last modified on %3$s.', 'fictioneer' ),
                  $reminders_count,
                  _n( 'story', 'stories', $reminders_count, 'fictioneer' ),
                  wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $reminders['updated'] / 1000 )
                );
              ?>
            </div>
          </div>
          <?php if ( $reminders_count > 0 ) : ?>
            <button class="card__delete button-clear-reminders" data-nonce="<?php echo wp_create_nonce( 'fictioneer_clear_reminders' ); ?>" data-warning="<?php esc_attr_e( 'Are you sure? Reminders will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
          <?php endif; ?>
        </div>
      </li>
    <?php endif; ?>

    <?php if ( $can_checkmarks ) : ?>
      <?php
        $stories_count = count( fictioneer_get_finished_checkmarks( $checkmarks ) );
        $chapters_count = fictioneer_count_chapter_checkmarks( $checkmarks );
      ?>
      <li class="card _small">
        <div class="card__body polygon">
          <div class="card__header _small">
            <h3 class="card__title _with-delete _small"><?php
              if ( $bookshelf_link ) {
                ?><a href="<?php echo add_query_arg( 'tab', 'finished', $bookshelf_link ) . '#tabs'; ?>"><?php _e( 'Checkmarks', 'fictioneer' ); ?></a><?php
              } else {
                _e( 'Checkmarks', 'fictioneer' );
              }
            ?></h3>
          </div>
          <div class="card__main _small">
            <div class="card__content _small">
              <?php
                printf(
                  __( 'You have marked <b>%1$s %2$s</b> as finished and <b>%3$s %4$s</b> as read. Last modified on %5$s.', 'fictioneer' ),
                  $stories_count,
                  _n( 'story', 'stories', $stories_count, 'fictioneer' ),
                  $chapters_count,
                  _n( 'chapter', 'chapters', $chapters_count, 'fictioneer' ),
                  wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $checkmarks['updated'] / 1000 )
                );
              ?>
            </div>
          </div>
          <?php if ( $stories_count > 0 || $chapters_count > 0 ) : ?>
            <button class="card__delete button-clear-checkmarks" data-nonce="<?php echo wp_create_nonce( 'fictioneer_clear_checkmarks' ); ?>" data-warning="<?php esc_attr_e( 'Are you sure? Checkmarks will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
          <?php endif; ?>
        </div>
      </li>
    <?php endif; ?>

    <?php if ( $can_bookmarks ) : ?>
      <li class="card _small">
        <div class="card__body polygon">
          <div class="card__header _small">
            <h3 class="card__title _with-delete _small"><?php
              if ( $bookmarks_link ) {
                ?><a href="<?php echo $bookmarks_link; ?>"><?php echo fcntr( 'bookmarks' ); ?></a><?php
              } else {
                echo fcntr( 'bookmarks' );
              }
            ?></h3>
          </div>
          <div class="card__main _small">
            <div class="card__content _small profile-bookmarks-stats">
              <?php _e( 'You have currently <b>%s bookmark(s)</b> set. Bookmarks are only processed in your browser.', 'fictioneer' ); ?>
            </div>
          </div>
          <button class="card__delete button-clear-bookmarks" data-warning="<?php esc_attr_e( 'Are you sure? Bookmarks will be irrevocably deleted. Enter %s to confirm.', 'fictioneer' ); ?>"><i class="fa-solid fa-trash-can"></i></button>
        </div>
      </li>
    <?php endif; ?>

    <?php do_action( 'fictioneer_account_data_nodes', $current_user, $args ); ?>

  </ul>
</div>
