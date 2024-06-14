<?php
/**
 * Template Part: Footer
 *
 * Closes the site container and includes the breadcrumbs, footer menu, cookie
 * consent banner, TTS interface, application wide icons, and HTML footer.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see fictioneer_get_breadcrumbs()
 * @see fictioneer_get_footer_copyright_note()
 * @see partials/_tts-interface.php
 * @see partials/_consent-banner.php
 * @see wp_footer()
 *
 * @internal $args['post_id']      Optional. Current post ID.
 * @internal $args['post_type']    Optional. Current post type.
 * @internal $args['breadcrumbs']  Array of breadcrumb tuples with label (0) and link (1).
 */


// Setup
$page_id = get_queried_object_id();
$extra_classes = [];

// Catch calls without arguments
$args['post_id'] = $args['post_id'] ?? $page_id;
$args['post_type'] = $args['post_type'] ?? 'none';
$args['breadcrumbs'] = $args['breadcrumbs'] ?? [];

// Fix wrong post ID
if ( $page_id != $args['post_id'] ) {
  $args['post_id'] = $page_id;
}

// Null post ID for generated pages
if ( is_archive() || is_search() || is_404() ) {
  $args['post_id'] = null;
}

// Extra classes
if ( get_theme_mod( 'footer_style' ) === 'isolated' ) {
  $extra_classes[] = '_footer-isolated';
}

// Hook after #main closes
do_action( 'fictioneer_after_main', $args );

// Render footer
if ( ! ( $args['blank'] ?? 0 ) ) {
  // Elementor?
  if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
    // Start HTML ---> ?>
    <footer class="footer layout-links <?php echo implode( ' ', $extra_classes ); ?>">
      <div class="footer__wrapper"><?php do_action( 'fictioneer_site_footer', $args ); ?></div>
    </footer>
    <?php // <--- End HTML
  }
}

?>

    </div> <!-- #site -->

    <?php
      // Render TTS interface if required
      if (
        ! empty( $args['post_id'] ) &&
        $args['post_type'] == 'fcn_chapter' &&
        ! post_password_required()
      ) {
        fictioneer_get_cached_partial( 'partials/_tts-interface' );
      }

      // Render cookie banner HTML if required
      if ( get_option( 'fictioneer_cookie_banner' ) ) {
        fictioneer_get_cached_partial( 'partials/_consent-banner' );
      }
    ?>

    <input id="general-fictioneer-nonce" name="fictioneer_nonce" type="hidden" value="<?php echo wp_create_nonce( 'fictioneer_nonce' ); ?>">

    <?php
      // Lightbox container
      if ( get_option( 'fictioneer_enable_lightbox' ) ) {
        echo '<div id="fictioneer-lightbox" class="lightbox"><button type="button" class="lightbox__close" aria-label="' . esc_attr__( 'Close lightbox', 'fictioneer' ) . '">' . fictioneer_get_icon( 'fa-xmark' ) . '</button><i class="fa-solid fa-spinner fa-spin loader" style="--fa-animation-duration: .8s;"></i><div class="lightbox__content"></div></div>';
      }

      // Fictioneer footer hook
      do_action( 'fictioneer_footer', $args );

      // WordPress footer hook (includes modals)
      wp_footer();
    ?>
  </body>
</html>

<?php

if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
  global $fictioneer_render_start_time;
  echo '<!-- Render Time: ' . number_format( microtime( true ) - $fictioneer_render_start_time, 4 ) . ' seconds. -->';
}

?>
