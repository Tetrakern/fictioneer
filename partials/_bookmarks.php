<?php
/**
 * Partial: Bookmarks
 *
 * Renders the HTML required for the script-generated bookmark cards.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 *
 * @internal $args['show_empty'] Whether to render a note when empty or nothing at all.
 * @internal $args['count']      Maximum number of bookmarks to render.
 */
?>

<?php

// Setup
$show_empty = isset( $args['show_empty'] ) && $args['show_empty'];
$count = isset( $args['count'] ) ? intval( $args['count'] ) : -1;

?>

<template class="bookmark-small-card-template">
  <li class="card _small bookmark-card" data-color>
    <div class="card__body polygon">
      <div class="bookmark-card__progress"></div>
      <div class="card__main _grid _small _relative-z1">
        <a href="" class="card__image cell-img bookmark-card__image" <?php echo fictioneer_get_lightbox_attribute(); ?>><img src="" class="no-auto-lightbox"></a>
        <h3 class="card__title _with-delete _small cell-title bookmark-card__title"><a href="" class="truncate _1-1"></a></h3>
        <div class="card__content bookmark-card__meta _small cell-meta">
          <i class="fa-solid fa-bookmark"></i>
          <span class="bookmark-card__percentage"></span>
          <i class="fa-solid fa-calendar-days"></i>
          <time></time>
        </div>
        <div class="card__content _small cell-desc bookmark-card__excerpt truncate _3-3"></div>
      </div>
      <button class="card__delete button-delete-bookmark" data-bookmark-id><i class="fa-solid fa-trash-can"></i></button>
    </div>
  </li>
</template>

<section class="small-card-block bookmarks-block <?php echo $show_empty ? '' : 'hidden' ?>" data-count="<?php echo $count; ?>">
  <?php if ( $show_empty ) : ?>
    <div class="bookmarks-block__no-bookmarks no-results"><?php echo fcntr( 'no_bookmarks' ); ?></div>
  <?php endif; ?>
  <ul class="two-columns"></ul>
</section>
