<?php
/**
 * Partial: Chapter Header
 *
 * Rendered in the single-fcn_chapter.php template below the top actions
 * and after the fictioneer_chapter_before_header hook.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 * @see single-fcn_chapter.php
 *
 * @internal $args['story_post']       Optional. Post object of the story.
 * @internal $args['story_data']       Optional. Story data from fictioneer_get_story_data().
 * @internal $args['chapter_id']       The chapter ID.
 * @internal $args['chapter_title']    Safe chapter title.
 * @internal $args['chapter_password'] Chapter password or empty string.
 */
?>

<header class="chapter__headline layout-links">
  <?php if ( $args['story_post'] && ! empty( $args['story_data']['title'] ) ) : ?>
    <a href="<?php echo get_the_permalink( $args['story_post']->ID ); ?>"><?php echo $args['story_data']['title']; ?></a>
  <?php endif; ?>
  <?php if ( ! fictioneer_get_field( 'fictioneer_chapter_hide_title' ) ) : ?>
    <h1 class="chapter__title<?php if ( ! empty( $args['chapter_password'] ) ) echo ' password'; ?>"><?php echo $args['chapter_title']; ?></h1>
    <em class="chapter__author"><?php
      printf(
        _x( 'by %s', 'Chapter page: by {Author(s)}', 'fictioneer' ),
        fictioneer_get_chapter_author_nodes( $args['chapter_id'] )
      );
    ?></em>
  <?php endif; ?>
</header>
