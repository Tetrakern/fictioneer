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
 * @internal $args  Array of arguments passed to the template.
 */


if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
  global $fictioneer_render_start_time;
  $fictioneer_render_start_time = microtime( true );
}

// IDs
global $post;

$page_id = get_queried_object_id();
$post_id = $post ? ( isset( $post->ID ) ? $post->ID : null ) : null;
$story_id = null;

if ( $page_id != $post_id ) {
  $post_id = $page_id;
}

if ( is_archive() || is_search() || is_404() || FICTIONEER_MU_REGISTRATION ) {
  $post_id = null;
}

// Prevent indexing if required
if ( $args['no_index'] ?? 0 ) {
  add_filter( 'wp_robots', 'wp_robots_no_robots' );
}

// Setup
$story_id = null;
$post_type = $post_id ? $post->post_type : null;
$header_image_source = 'default';
$header_image_url = get_header_image();

// If this is a content page...
if ( $post_id ) {
  // Type?
  switch ( $post_type ) {
    case 'fcn_story':
      $story_id = $post_id;
      break;
    case 'fcn_chapter':
      $story_id = fictioneer_get_chapter_story_id( get_the_ID() );
      break;
  }

  // Custom header image?
  if ( get_post_meta( $post_id, 'fictioneer_custom_header_image', true ) ) {
    $header_image_id = get_post_meta( $post_id, 'fictioneer_custom_header_image', true );
    $header_image_url = wp_get_attachment_image_url( $header_image_id, 'full' );
    $header_image_source = 'post';
  } elseif ( ! empty( $story_id ) && get_post_meta( $story_id, 'fictioneer_custom_header_image', true ) ) {
    $header_image_id = get_post_meta( $story_id, 'fictioneer_custom_header_image', true );
    $header_image_url = wp_get_attachment_image_url( $header_image_id, 'full' );
    $header_image_source = 'story';
  }
}

// Filter header image
$header_image_preload = '';

if ( $header_image_url ) {
  $header_image_url = apply_filters( 'fictioneer_filter_header_image', $header_image_url, $post_id, $header_image_source );

  if ( $header_image_url && ! get_option( 'fictioneer_disable_header_image_preload' ) ) {
    $header_image_preload = apply_filters(
      'fictioneer_filter_header_image_preload',
      "<link rel='preload' href='{$header_image_url}' as='image' fetchPriority='high'>",
      $header_image_url,
      $post_id,
      $header_image_source
    );
  }
}

// Action arguments
$action_args = array(
  'post_id' => $post_id,
  'post_type' => $post_type,
  'story_id' => $story_id,
  'header_image_url' => $header_image_url,
  'header_image_source' => $header_image_source,
  'header_args' => $args ?? []
);

// Body attributes
$body_attributes = array(
  'data-post-id' => ( $post_id ?: -1 ),
  'data-controller' => 'fictioneer-last-click fictioneer-mobile-menu',
  'data-action' => 'click->fictioneer-last-click#removeAll keydown.esc->fictioneer-last-click#removeAll click->fictioneer#bodyClick'
);

if ( get_option( 'fictioneer_enable_bookmarks' ) ) {
  $body_attributes['data-controller'] .= ' fictioneer-bookmarks';
}

if ( get_option( 'fictioneer_enable_follows' ) ) {
  $body_attributes['data-controller'] .= ' fictioneer-follows';
}

if ( get_option( 'fictioneer_enable_reminders' ) ) {
  $body_attributes['data-controller'] .= ' fictioneer-reminders';
}

if ( get_option( 'fictioneer_enable_checkmarks' ) ) {
  $body_attributes['data-controller'] .= ' fictioneer-checkmarks';
}

if ( $post_type === 'fcn_chapter' ) {
  $body_attributes['data-controller'] .= ' fictioneer-chapter-formatting';
}

if ( get_option( 'fictioneer_enable_alerts' ) ) {
  $body_attributes['data-controller'] .= ' fictioneer-alerts';
}

if ( $story_id ) {
  $body_attributes['data-story-id'] = $story_id;
}

$body_attributes = apply_filters( 'fictioneer_filter_body_attributes', $body_attributes, $action_args );
unset( $body_attributes['class'] ); // Prevent mistakes

$body_attributes = array_map(
  function ( $key, $value ) { return $key . '="' . esc_attr( $value ) . '"'; },
  array_keys( $body_attributes ),
  $body_attributes
);

?>

<!doctype html>

<html <?php language_attributes(); ?> <?php fictioneer_root_attributes(); ?>>

  <head><?php echo $header_image_preload; wp_head(); ?></head>

  <body <?php body_class( 'site-bg scrolled-to-top' ); echo implode( ' ', $body_attributes ); ?>>
    <?php
      // WP Default action
      wp_body_open();

      // Includes mobile menu
      do_action( 'fictioneer_body', $action_args );
    ?>

    <div id="notifications" class="notifications"></div>

    <div id="site" class="site background-texture">

      <?php
        // Includes header background, navigation bar, and site header (with title, logo, etc.)
        do_action( 'fictioneer_site', $action_args );
      ?>
