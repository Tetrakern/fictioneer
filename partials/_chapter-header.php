<?php
/**
 * Partial: Chapter Header
 *
 * Rendered in the single-fcn_chapter.php template below the top actions
 * and after the fictioneer_chapter_before_header hook.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 * @see single-fcn_chapter.php
 *
 * @internal $args['story_post']        Optional. Post object of the story.
 * @internal $args['story_data']        Optional. Story data from fictioneer_get_story_data().
 * @internal $args['chapter_id']        The chapter ID.
 * @internal $args['chapter_title']     Safe chapter title.
 * @internal $args['chapter_password']  Chapter password or empty string.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

$story_visible = $args['story_post'] &&
  ! empty( $args['story_data']['title'] ) &&
  in_array( get_post_status( $args['story_post']->ID ), ['publish', 'private'] );

?>

<header class="chapter__headline layout-links"><?php
  $password_class = empty( $args['chapter_password'] ) ? '' : ' _password';
  $identity = [];

  if ( $story_visible ) {
    $identity['link'] = '<a href="' . get_permalink( $args['story_post']->ID ) . '">' . $args['story_data']['title'] . '</a>';
  }

  if ( ! get_post_meta( $post->ID, 'fictioneer_chapter_hide_title', true ) ) {
    $identity['title'] = '<h1 class="chapter__title' . $password_class . '">' . $args['chapter_title'] . '</h1>';
    $identity['meta'] = '<em class="chapter__author">' . sprintf(
        _x( 'by %s', 'Chapter page: by {Author(s)}', 'fictioneer' ),
        fictioneer_get_chapter_author_nodes( $args['chapter_id'] )
      ) . '</em>';
  }

  $identity = apply_filters( 'fictioneer_filter_chapter_identity', $identity, $args );

  echo implode( '', $identity );
?></header>
