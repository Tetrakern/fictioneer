<?php
/**
 * Partial: Recommendation Header
 *
 * Rendered in the single-fcn_recommendation.php template right after opening
 * the <article> block.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 * @see single-fcn_recommendation.php
 *
 * @internal $args['recommendation']     Recommendation post object.
 * @internal $args['recommendation_id']  Recommendation post ID.
 * @internal $args['title']              Safe recommendation title.
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$fandoms = get_the_terms( $args['recommendation_id'], 'fcn_fandom' );
$characters = get_the_terms( $args['recommendation_id'], 'fcn_character' );
$genres = get_the_terms( $args['recommendation_id'], 'fcn_genre' );

// Flags
$show_taxonomies = ! get_option( 'fictioneer_hide_taxonomies_on_pages' ) && ( $fandoms || $characters || $genres );

?>

<header class="recommendation__header">
  <?php if ( $show_taxonomies ) : ?>
    <div class="recommendation__taxonomies tag-group"><?php
      echo fictioneer_get_taxonomy_pills(
        array(
          'fandoms' => $fandoms,
          'genres' => $genres,
          'characters' => $characters
        ),
        'recommendation_header'
      );
    ?></div>
  <?php endif; ?>

  <?php if ( ! empty( $args['title'] ) ) : ?>
    <h1 class="recommendation__title"><?php echo $args['title']; ?></h1>
  <?php endif; ?>
</header>
