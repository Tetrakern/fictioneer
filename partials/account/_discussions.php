<?php
/**
 * Partial: Account - Discussions
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 *
 * @internal $args['user']          Current user.
 * @internal $args['is_admin']      True if the user is an administrator.
 * @internal $args['is_author']     True if the user is an author (by capabilities).
 * @internal $args['is_editor']     True if the user is an editor.
 * @internal $args['is_moderator']  True if the user is a moderator (by capabilities).
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$current_user = $args['user'];
$avatar = get_avatar( $current_user->ID, 32 );
$badge = fictioneer_get_comment_badge( $current_user, null );

?>

<h3 class="profile__discussions-headline"><?php _e( 'Discussions', 'fictioneer' ) ?></h3>

<p class="profile__description"><?php _e( 'This is how your comments will look. You <b>avatar is pulled from the last account you logged in with</b> — which can fail if something is wrong with the avatar. This happens, for example, if you have changed your Discord avatar since you last logged in. Logging out and in again should resolve this issue.', 'fictioneer' ) ?></p>

<p class="profile__description">
  <?php
    printf(
      __( 'Alternatively, you can register your email address on the <a href="%s">Gravatar</a> service. Your avatar will then be matched with your email address; note the <b>[Always use gravatar]</b> flag under Account Settings in this case.', 'fictioneer' ),
      'https://gravatar.com/'
    )
  ?>
</p>

<ul class="profile__admin-notes">

  <?php if ( $current_user->badge_override ) : ?>
    <li>
      <i class="fa-solid fa-ribbon"></i>
      <span>
        <?php
          printf(
            __( 'Custom badge "<b>%s</b>" assigned.', 'fictioneer' ),
            $current_user->badge_override
          )
        ?>
      </span>
    </li>
  <?php endif; ?>

  <?php if ( $current_user->fictioneer_admin_disable_avatar ) : ?>
    <li>
      <i class="fa-solid fa-bolt"></i>
      <span><?php _e( 'Avatar capability disabled.', 'fictioneer' ) ?></span>
    </li>
  <?php endif; ?>

  <?php if ( $current_user->fictioneer_admin_disable_reporting ) : ?>
    <li>
      <i class="fa-solid fa-bolt"></i>
      <span><?php _e( 'Reporting capability disabled.', 'fictioneer' ) ?></span>
    </li>
  <?php endif; ?>

  <?php if ( $current_user->fictioneer_admin_disable_commenting ) : ?>
    <li>
      <i class="fa-solid fa-bolt"></i>
      <span><?php _e( 'Commenting capability disabled.', 'fictioneer' ) ?></span>
    </li>
  <?php endif; ?>

  <?php if ( $current_user->fictioneer_admin_disable_editing ) : ?>
    <li>
      <i class="fa-solid fa-bolt"></i>
      <span><?php _e( 'Comment editing capability disabled.', 'fictioneer' ) ?></span>
    </li>
  <?php endif; ?>

  <?php if ( $current_user->fictioneer_admin_always_moderate_comments ) : ?>
    <li>
      <i class="fa-solid fa-bolt"></i>
      <span><?php _e( 'Comments are moderated.', 'fictioneer' ) ?></span>
    </li>
  <?php endif; ?>

</ul>

<div class="comment">

  <div class="comment__header">

    <?php if ( $avatar ) echo $avatar; ?>

    <div class="comment__meta">
      <div class="comment__author">
        <span><?php echo $current_user->display_name; ?></span>
        <?php if ( $badge ) echo $badge; ?>
      </div>
      <div class="comment__date-and-link"><?php
        echo date_format(
          date_create(),
          sprintf(
            _x( '%1$s \a\t %2$s', 'Comment time format string.', 'fictioneer' ),
            get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y",
            get_option( 'time_format' )
          )
        );
      ?></div>
    </div>

  </div>

  <div class="comment__body clearfix">
    <p><?php _e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin mollis eu lectus eu pellentesque. Fusce ornare erat tellus, nec aliquet lacus sodales ut. Duis auctor vulputate dolor nec bibendum. Maecenas dapibus nibh at quam dictum porta at eu felis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Phasellus consequat, justo vulputate rutrum aliquam, magna sem malesuada turpis, et convallis est metus eget.', 'fictioneer' ) ?></p>
  </div>

</div>
