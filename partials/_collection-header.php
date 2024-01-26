<?php
/**
 * Partial: Collection Header
 *
 * Rendered in the single-fcn_collection.php template right after opening
 * the <article> block.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 * @see single-fcn_collection.php
 *
 * @internal $args['collection']      Collection post object.
 * @internal $args['collection_id']   The collection post ID.
 * @internal $args['title']           Safe collection title.
 * @internal $args['current_page']    Number of the current page or 1.
 * @internal $args['max_pages']       Total number of pages or 1.
 * @internal $args['featured_list']   IDs of featured items in the collection.
 * @internal $args['featured_query']  Paginated query of featured items.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$fandoms = get_the_terms( $args['collection_id'], 'fcn_fandom' );
$characters = get_the_terms( $args['collection_id'], 'fcn_character' );
$genres = get_the_terms( $args['collection_id'], 'fcn_genre' );

// Flags
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_pages' ) && ( $fandoms || $characters || $genres );

?>

<header class="collection__header">
  <?php if ( $show_taxonomies ) : ?>
    <div class="collection__taxonomies tag-group"><?php
      echo fictioneer_get_taxonomy_pills(
        array(
          'fandoms' => $fandoms,
          'genres' => $genres,
          'characters' => $characters
        ),
        'collection_header'
      );
    ?></div>
  <?php endif; ?>
  <?php if ( ! empty( $args['title'] ) ) : ?>
    <h1 class="collection__title"><?php echo $args['title']; ?></h1>
  <?php endif; ?>
</header>
