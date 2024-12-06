<?php
/**
 * Partial: Bookmark Template
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.4.5
 * @since 5.18.0 - Added 'seamless' and 'thumbnail' params.
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

// Setup
$card_image_style = get_theme_mod( 'card_image_style', 'default' );

// Extra classes
$card_classes = [];

if ( $card_image_style !== 'default' ) {
  $card_classes[] = '_' . $card_image_style;
}

if ( $args['seamless'] ?? 0 ) {
  $card_classes[] = '_seamless';
}

?>

<template class="bookmark-small-card-template">
  <li class="card _small bookmark-card _no-footer <?php echo implode( ' ', $card_classes ); ?>" data-color>
    <div class="card__body polygon">
      <div class="bookmark-card__progress"></div>
      <div class="card__main _grid _small _relative-z1">
        <?php if ( $args['thumbnail'] ?? 1 ) : ?>
          <a href="" class="card__image cell-img bookmark-card__image" <?php echo fictioneer_get_lightbox_attribute(); ?>><img src="" class="no-auto-lightbox"></a>
        <?php endif; ?>
        <h3 class="card__title _with-delete _small cell-title bookmark-card__title"><a href="" class="truncate _1-1"></a></h3>
        <div class="card__content bookmark-card__meta _small cell-meta">
          <i class="fa-solid fa-bookmark"></i>
          <span class="bookmark-card__percentage"></span>
          <i class="fa-solid fa-calendar-days"></i>
          <time></time>
        </div>
        <div class="card__content _small cell-desc bookmark-card__excerpt truncate _cq-2-3"></div>
      </div>
      <button class="card__delete button-delete-bookmark" data-action="click->fictioneer-bookmarks#remove"><i class="fa-solid fa-trash-can"></i></button>
    </div>
  </li>
</template>
