<?php
/**
 * Partial: Bookmark Template
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.4.5
 */
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
