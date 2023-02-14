<?php
/**
 * Template Part: Comments
 *
 * Renders the comment form and threads.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 */
?>

<?php

// Don't show the comments if the password has not been entered
if ( post_password_required() ) return;

// Setup
$user = wp_get_current_user();
$comments_count = get_comments_number();
$logout_url = fictioneer_get_logout_url( get_permalink() );

?>

<div id="comments" class="fictioneer-comments" data-post-id="<?php echo get_the_ID(); ?>" data-logout-url="<?php echo esc_url( $logout_url ); ?>">

  <?php

    if ( get_option( 'fictioneer_enable_ajax_comments' ) ) {
      fictioneer_comments_ajax_skeleton( $comments_count ); // AJAX loading skeleton
    } else {
      // Query arguments
      $query_args = ['post_id' => get_the_ID()];

      if ( ! get_option( 'fictioneer_disable_comment_query' ) ) {
        $query_args['type'] = ['comment', 'private'];
        $query_args['order'] = get_option( 'comment_order' );
      } else {
        $query_args['type'] = ['comment'];
      }

      // Filter query arguments
      $query_args = apply_filters( 'fictioneer_filter_comments_query', $query_args, get_the_ID() );

      // Query comments
      $comments_query = new WP_Comment_Query( $query_args );
      $comments = $comments_query->comments;

      // Filter comments
      $comments = apply_filters( 'fictioneer_filter_comments', $comments, get_the_ID() );

      // Comments header
      fictioneer_comment_header( $comments_count );

      // Comment form
      if ( ! fictioneer_is_commenting_disabled() ) {
        if ( get_option( 'fictioneer_enable_ajax_comment_form' ) ) {
          fictioneer_comments_ajax_form_skeleton();
        } else {
          comment_form();
        }
      } else {
        echo '<div class="fictioneer-comments__disabled">' . __( 'Comments are disabled.', 'fictioneer' ) . '</div>';
      }

      // Count all comments regardless of status
      $count = count( $comments );

      // Comment list
      if (
        have_comments() ||
        ( $count > 0 && user_can( $user, 'moderate_comments' ) ) ||
        ( $count > 0 && ! empty( $_GET['commentcode'] ) )
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

  ?>
</div>
