<?php
/**
 * Template Part: Comments
 *
 * Renders the comment form and threads.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 */


// Don't show the comments if the password has not been entered
if ( post_password_required() ) {
  return;
}

global $wp;

// Setup
$post_id = get_the_ID();
$post_status = get_post_status();
$user = wp_get_current_user();
$comments_count = get_comments_number();
$order = fictioneer_sanitize_query_var( $_GET['corder'] ?? 0, ['desc', 'asc'], get_option( 'comment_order' ) );
$logout_url = fictioneer_get_logout_url( get_permalink() );
$order_link = add_query_arg( 'corder', $order === 'desc' ? 'asc' : 'desc', home_url( $wp->request ) ) . '#comments';
$is_ajax_comments = get_option( 'fictioneer_enable_ajax_comments' );

// Abort if on scheduled post and not logged in, because there
// should be no scenario where a guest can comment here.
if ( $post_status === 'future' && ! is_user_logged_in() ) {
  return;
}

// Commenting on scheduled?
if (
  $post_status === 'future' &&
  get_post_type() === 'fcn_chapter' &&
  ! get_option( 'fictioneer_enable_scheduled_chapter_commenting' )
) {
  return;
}

// Edit template
if (
  get_option( 'fictioneer_enable_user_comment_editing' ) &&
  ! fictioneer_is_commenting_disabled()
) {
  get_template_part( 'partials/_template_comment_edit' );
}

?>

<div id="comments" class="fictioneer-comments scroll-margin-top" data-post-id="<?php echo $post_id; ?>" data-order="<?php echo $order; ?>" data-logout-url="<?php echo esc_url( $logout_url ); ?>" <?php echo $is_ajax_comments ? 'data-ajax-comments' : ''; ?>><?php
  if ( get_option( 'fictioneer_enable_ajax_comments' ) ) {
    fictioneer_comments_ajax_skeleton( $comments_count ); // AJAX loading skeleton
  } else {
    // Query arguments
    $query_args = array(
      'post_id' => $post_id,
      'order' => $order
    );

    if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
      $query_args['type'] = ['comment', 'private', 'user_deleted'];
    } else {
      $query_args['type'] = ['comment'];
    }

    // Filter query arguments
    $query_args = apply_filters( 'fictioneer_filter_comments_query', $query_args, $post_id );

    // Query comments
    $comments_query = new WP_Comment_Query( $query_args );
    $comments = $comments_query->comments;

    // Filter comments
    $comments = apply_filters( 'fictioneer_filter_comments', $comments, $post_id );

    // Comments header
    fictioneer_comment_header( $comments_count );

    // Comment form
    if ( ! fictioneer_is_commenting_disabled( $post_id ) ) {
      if ( get_option( 'fictioneer_enable_ajax_comment_form' ) ) {
        fictioneer_comments_ajax_form_skeleton();
      } else {
        if ( get_option( 'fictioneer_disable_comment_form' ) ) {
          comment_form();
        } else {
          fictioneer_comment_form();
        }
      }
    } else {
      echo '<div class="fictioneer-comments__disabled">' . __( 'Commenting is disabled.', 'fictioneer' ) . '</div>';
    }

    if ( $comments_count > 0 ) {
      // Start HTML ---> ?>
      <div id="sof" class="sort-order-filter _comments">
        <a class="list-button _order  <?php echo $order === 'desc' ? '_on' : '_off'; ?>" href="<?php echo $order_link; ?>" rel='nofollow' aria-label="<?php esc_attr_e( 'Toggle comments between ascending and descending order', 'fictioneer' ); ?>">
          <i class="fa-solid fa-arrow-up-short-wide _off"></i><i class="fa-solid fa-arrow-down-wide-short _on"></i>
        </a>
      </div>
      <?php // <--- End HTML
    }

    // Count all comments regardless of status
    $count = count( $comments );

    // Moderation menu template
    fictioneer_comment_moderation_template();

    // Comment list
    if (
      have_comments() ||
      ( $count > 0 && user_can( $user, 'moderate_comments' ) ) ||
      ( $count > 0 && ! empty( $_GET['commentcode'] ?? 0 ) )
    ) {
      // Start HTML ---> ?>
      <ol class="fictioneer-comments__list commentlist">
        <?php wp_list_comments( [], $comments ); ?>
      </ol>
      <?php // <--- End HTML

      // Pagination
      $pag_args = [];

      if ( ! get_option( 'fictioneer_disable_comment_pagination' ) ) {
        $pag_args['prev_text'] = fcntr( 'previous' );
        $pag_args['next_text'] = fcntr( 'next' );
      }

      the_comments_pagination( $pag_args );
    }
  }
?></div>
