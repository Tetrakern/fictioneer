<?php
/**
 * Partial: Mobile Bookmark Template
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.4.5
 */
?>

<?php

// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<template id="mobile-bookmark-template" hidden>
  <li class="mobile-menu__bookmark">
    <button class="mobile-menu-bookmark-delete-button" data-bookmark-id><i class="fa-solid fa-trash-alt"></i></button>
    <i class="fa-solid fa-bookmark"></i>
    <a href="#"><span></span></a>
    <div class="mobile-menu__bookmark-progress"><div><div class="mobile-menu__bookmark-bg" style></div></div></div>
  </li>
</template>
