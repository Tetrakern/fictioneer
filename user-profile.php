<?php
/**
 * Template Name: User Profile
 *
 * Ths template renders a limited user profile form on the frontend to keep
 * subscribers out of the admin panel and make it look pretty. Requires an empty
 * page with the template assigned in the theme's settings.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.5.0
 */


// Only for logged-in users...
if ( ! is_user_logged_in() && ! get_option( 'fictioneer_enable_public_cache_compatibility' ) ) {
  wp_safe_redirect( home_url() );
  exit;
}

// If public caching is active, redirect to dashboard profile
if (
  ! get_option( 'fictioneer_enable_private_cache_compatibility' ) &&
  get_option( 'fictioneer_enable_public_cache_compatibility' )
) {
  wp_safe_redirect( get_edit_profile_url( 0 ) );
  exit;
}

// Redundant safeguard
if ( ! is_user_logged_in() ) {
  wp_safe_redirect( home_url() );
  exit;
}

// Try to prevent caching of the page
if ( ! defined( 'DONOTCACHEPAGE' ) ) {
  define( 'DONOTCACHEPAGE', true );
}

do_action( 'litespeed_control_set_nocache', 'nocache due to user-specific page.' );

// Setup
$post_id = get_the_ID();
$current_user = wp_get_current_user();
$title = trim( get_the_title() );
$title = empty( $title ) ? __( 'User Profile', 'fictioneer' ) : $title;
$this_breadcrumb = [ $title, get_the_permalink() ];

// Flags
$is_admin = fictioneer_is_admin( $current_user->ID );
$is_author = fictioneer_is_author( $current_user->ID );
$is_editor = fictioneer_is_editor( $current_user->ID );
$is_moderator = fictioneer_is_moderator( $current_user->ID );

// Arguments for hooks and templates/etc. and includes
$hook_args = array(
  'user' => $current_user,
  'is_admin' => $is_admin,
  'is_author' => $is_author,
  'is_editor' => $is_editor,
  'is_moderator' => $is_moderator
);

// Header
get_header( null, array( 'type' => 'user-profile', 'no_index' => 1 ) );

?>

<main id="main" class="main singular profile">

  <?php do_action( 'fictioneer_main', 'user-profile' ); ?>

  <div class="main__wrapper">

    <?php do_action( 'fictioneer_main_wrapper' ); ?>

    <article id="singular-<?php echo $post_id; ?>" class="singular__article">

      <section class="singular__content content-section profile__content">
        <?php do_action( 'fictioneer_account_content', $hook_args ); ?>
      </section>

    </article>

  </div>

</main>

<?php
  // Footer arguments
  $footer_args = array(
    'post_type' => 'page',
    'post_id' => $post_id,
    'breadcrumbs' => array(
      [fcntr( 'frontpage' ), get_home_url()]
    )
  );

  // Add current breadcrumb
  $footer_args['breadcrumbs'][] = $this_breadcrumb;

  // Get footer with breadcrumbs
  get_footer( null, $footer_args );
?>
