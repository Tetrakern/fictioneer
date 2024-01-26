<?php
/**
 * Partial: Story Header
 *
 * Rendered in the single-fcn_story.php template right after opening
 * the <article> block.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 * @see single-fcn_story.php
 *
 * @internal $args['story_data']  Story data from fictioneer_get_story_data().
 * @internal $args['story_id']    Current story and post ID.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$story = $args['story_data'];
$story_id = $args['story_id'];
$thumbnail_shown = has_post_thumbnail() && ! get_post_meta( $story_id, 'fictioneer_story_no_thumbnail', true );
$tax_shown = ! get_option( 'fictioneer_hide_taxonomies_on_pages' ) &&
  $story['has_taxonomies'] &&
  ! get_post_meta( $story_id, 'fictioneer_story_no_tags', true );

// Story header classes
$header_classes = [];

if ( ! $tax_shown ) {
  $header_classes[] = '_no-tax';
}

if ( ! $thumbnail_shown ) {
  $header_classes[] = '_no-thumbnail';
  $header_classes[] = 'padding-top';
}

?>

<header class="story__header padding-left padding-right <?php echo implode( ' ', $header_classes ); ?>">

  <?php if ( $thumbnail_shown ) echo fictioneer_get_story_page_cover( $story ); ?>

  <?php if ( $tax_shown ) : ?>
    <div class="story__taxonomies tag-group"><?php
      echo fictioneer_get_taxonomy_pills(
        array(
          'fandoms' => $story['fandoms'],
          'genres' => $story['genres'],
          'characters' => $story['characters']
        ),
        'story_header'
      );
    ?></div>
    <div class="story__taxonomies-space"></div>
  <?php endif; ?>

  <div class="story__identity"><?php
    $identity = [];
    $identity['title'] = '<h1 class="story__identity-title">' . $story['title'] . '</h1>';
    $identity['meta'] = '<div class="story__identity-meta">' . sprintf(
        _x( 'by %s', 'Story page: by {Author(s)}', 'fictioneer' ),
        fictioneer_get_story_author_nodes( $story_id )
      ) . '</div>';
    $identity = apply_filters( 'fictioneer_filter_story_identity', $identity, $story_id, $story );

    echo implode( '', $identity );
  ?></div>

</header>
