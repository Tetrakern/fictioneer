<?php
/**
 * Template Part: Header
 *
 * Starts the document. The HTML root <html> is used to store default settings and
 * configuration classes since it is the only node available at the beginning.
 * This is necessary for the light/dark switch and site settings to work without
 * causing color flickering or layout shifts.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see wp_head()
 * @see wp_body_open()
 * @see fictioneer_output_head_meta()
 * @see fictioneer_output_head_critical_scripts()
 *
 * @internal $args Array of arguments passed to the template.
 */
?>

<!doctype html>

<html <?php language_attributes(); ?> <?php fictioneer_root_attributes(); ?>>

  <head>
    <?php
      // Includes charset, content type, viewport, fonts, etc...
      fictioneer_output_head_meta();

      // Prevent indexing if required
      if ( $args['no_index'] ?? 0 ) {
        add_filter( 'wp_robots', 'fictioneer_add_noindex_to_robots' );
      }

      // WordPress <head> hook
      wp_head();

      /*
       * Most post types and pages can add individual custom CSS, which is
       * included here. This can hypothetically be used to completely change
       * the style but requires in-depth knowledge of the theme. Validated
       * using the same method as in WP_Customize_Custom_CSS_Setting(). And
       * removes any "<" for good measure since CSS does not use it.
       */

      $custom_css = fictioneer_get_field( 'fictioneer_custom_css', get_the_ID() );

      if ( get_post_type( get_the_ID() ) == 'fcn_story' ) {
        $custom_css .= fictioneer_get_field( 'fictioneer_story_css', get_the_ID() );
      }

      if ( get_post_type( get_the_ID() ) == 'fcn_chapter' ) {
        $story_id = fictioneer_get_field( 'fictioneer_chapter_story', get_the_ID() );
        $custom_css .= fictioneer_get_field( 'fictioneer_story_css', $story_id );
      }

      if ( ! empty( $custom_css ) && ! preg_match( '#</?\w+#', $custom_css ) ) {
        echo '<style id="fictioneer-custom-styles">' . str_replace( '<', '', $custom_css ). '</style>';
      }

      // Includes critical path scripts that must be executed before the rest
      fictioneer_output_head_critical_scripts();
    ?>
  </head>

  <body <?php body_class( 'site-bg' ); ?>>
    <?php wp_body_open(); ?>

    <?php
      // Setup
      global $post;
      $post_id = $post ? $post->ID : null;
      $story_id = null;
      $header_image_url = get_header_image();

      // If this is a content page...
      if ( ! is_archive() && ! is_search() && ! is_404() && $post_id ) {
        // Type?
        switch ( $post->post_type ) {
          case 'fcn_story':
            $story_id = $post_id;
            break;
          case 'fcn_chapter':
            $story_id = fictioneer_get_field( 'fictioneer_chapter_story' );
            break;
        }

        // Custom header image?
        if ( fictioneer_get_field( 'fictioneer_custom_header_image', $post_id ) ) {
          $header_image_url = fictioneer_get_field( 'fictioneer_custom_header_image', $post_id );
          $header_image_url = wp_get_attachment_image_url( $header_image_url, 'full' );
        } elseif ( ! empty( $story_id ) && fictioneer_get_field( 'fictioneer_custom_header_image', $story_id ) ) {
          $header_image_url = fictioneer_get_field( 'fictioneer_custom_header_image', $story_id );
          $header_image_url = wp_get_attachment_image_url( $header_image_url, 'full' );
        }
      }

      // Filter header image
      if ( $header_image_url && $post_id ) {
        $header_image_url = apply_filters( 'fictioneer_filter_header_image', $header_image_url, $post_id );
      }

      // Action arguments
      $action_args = array(
        'post_id' => $post_id,
        'story_id' => $story_id,
        'header_image_url' => $header_image_url,
        'header_args' => $args
      );

      // Includes mobile menu and modals
      do_action( 'fictioneer_body', $action_args );

      // Inline storage tuples
      $inline_storage = array(
        'permalink' => ['data-permalink', esc_url( get_permalink() )],
        'homelink' => ['data-homelink', esc_url( home_url() )],
        'post_id' => ['data-post-id', $post_id],
        'story_id' => ['data-story-id', $story_id]
      );

      // Filter inline storage
      $inline_storage = apply_filters( 'fictioneer_filter_inline_storage', $inline_storage );

      // Prepare inline storage attributes
      $inline_storage = array_map(
        function ( $node ) {
          return $node[0] . '="' . $node[1] . '"';
        },
        $inline_storage
      );
    ?>

    <div id="inline-storage" <?php echo implode( ' ', $inline_storage ); ?> hidden></div>

    <div id="notifications" class="notifications"></div>

    <div id="site" class="site background-texture">

      <?php
        // Includes header background, navigation bar, and site header (with title, logo, etc.)
        do_action( 'fictioneer_site', $action_args );
      ?>
