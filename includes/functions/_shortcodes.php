<?php

// =============================================================================
// SHORTCODE-BASED RELATIONSHIPS
// =============================================================================

/**
 * Register relationships for posts with certain shortcodes
 *
 * @since Fictioneer 5.0
 */

function fictioneer_update_shortcode_relationships( $post_id, $post ) {
  // Prevent multi-fire
  if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return;
  if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) return;

  // Setup
  $registry = fictioneer_get_relationship_registry();

  // Look for showcase shortcode
  if ( str_contains( $post->post_content, 'fictioneer_showcase' ) ) {
    $registry['always'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['always'][ $post_id ] );
  }

  // Look for post-related shortcode
  if ( str_contains( $post->post_content, 'fictioneer_latest_post' ) ) {
    $registry['ref_posts'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_posts'][ $post_id ] );
  }

  // Look for chapter-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_chapter_list' ) ||
    str_contains( $post->post_content, 'fictioneer_latest_chapter' ) ||
    str_contains( $post->post_content, 'fictioneer_chapter_cards' )
  ) {
    $registry['ref_chapters'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_chapters'][ $post_id ] );
  }

  // Look for story-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_latest_stor' ) ||
    str_contains( $post->post_content, 'fictioneer_story_cards' ) ||
    str_contains( $post->post_content, 'fictioneer_latest_update' ) ||
    str_contains( $post->post_content, 'fictioneer_update_cards' )
  ) {
    $registry['ref_stories'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_stories'][ $post_id ] );
  }

  // Look for recommendation-related shortcodes
  if (
    str_contains( $post->post_content, 'fictioneer_latest_recommendation' ) ||
    str_contains( $post->post_content, 'fictioneer_recommendation_cards' )
  ) {
    $registry['ref_recommendations'][ $post_id ] = 'shortcode';
  } else {
    unset( $registry['ref_recommendations'][ $post_id ] );
  }

  // Update database
  fictioneer_save_relationship_registry( $registry );
}
add_action( 'save_post', 'fictioneer_update_shortcode_relationships', 10, 2 );

// =============================================================================
// SHOWCASE SHORTCODE
// =============================================================================

/**
 * Shortcode to display showcase
 *
 * @since 5.0
 *
 * @param string      $attr['for']      What the showcase is for. Allowed are chapters,
 *                                      collections, recommendations, and stories.
 * @param int|null    $attr['count']    Optional. Maximum number of items. Default 9.
 * @param string|null $attr['author']   Optional. Limit items to a specific author.
 * @param string|null $attr['order']    Optional. Order direction. Default 'DESC'.
 * @param string|null $attr['orderby']  Optional. Order argument. Default 'date'.
 * @param array|null  $attr['post_ids'] Optional. Limit items to specific post IDs.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_showcase( $attr ) {
  // Abort if...
  if ( empty( $attr['for'] ) ) return '';

  // Setup
  $count = max( 1, intval( $attr['count'] ?? 8 ) );
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'DESC';
  $orderby = $attr['orderby'] ?? 'date';
  $no_cap = $attr['no_cap'] ?? false;
  $post_ids = [];

  if ( ! empty( $attr['posts'] ) ) {
    $post_ids = str_replace( ' ', '', $attr['posts'] );
    $post_ids = explode( ',', $post_ids );
    $post_ids = is_array( $post_ids ) ? $post_ids : [];
  }

  // Prepare arguments
  $args = array(
    'count' => $count,
    'author' => $author,
    'orderby' => $orderby,
    'order' => $order,
    'post_ids' => $post_ids,
    'no_cap' => $no_cap == 'true' || $no_cap == '1'
  );

  switch ( $attr['for'] ) {
    case 'collections':
      $args['type'] = 'fcn_collection';
      break;
    case 'chapters':
      $args['type'] = 'fcn_chapter';
      break;
    case 'stories':
      $args['type'] = 'fcn_story';
      break;
    case 'recommendations':
      $args['type'] = 'fcn_recommendation';
      break;
  }

  // Abort if...
  if ( ! isset( $args['type'] ) ) return '';

  // Buffer
  ob_start();
  get_template_part( 'partials/_showcase', null, $args );

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_showcase', 'fictioneer_shortcode_showcase' );

// =============================================================================
// LATEST CHAPTERS SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest chapters
 *
 * @since 3.0
 *
 * @param int|null    $attr['count']    Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']   Optional. Limit items to a specific author.
 * @param string|null $attr['orderby']  Optional. Order argument. Default 'date'.
 * @param string|null $attr['type']     Optional. Choose between 'default', 'simple', and 'compact'.
 * @param string|null $attr['spoiler']  Optional. Whether to show spoiler content.
 * @param string|null $attr['source']   Optional. Whether to show author and story.
 * @param array|null  $attr['chapters'] Optional. Limit items to specific post IDs.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_chapters( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $orderby = $attr['orderby'] ?? 'date';
  $spoiler = $attr['spoiler'] ?? false;
  $source = $attr['source'] ?? 'true';
  $post_ids = [];

  if ( ! empty( $attr['chapters'] ) ) {
    $post_ids = str_replace( ' ', '', $attr['chapters'] );
    $post_ids = explode( ',', $post_ids );
    $post_ids = is_array( $post_ids ) ? $post_ids : [];
    $count = count( $post_ids );
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part(
        'partials/_latest-chapters-compact',
        null,
        array(
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'orderby' => $orderby,
          'spoiler' => $spoiler == 'true',
          'source' => $source == 'true',
          'post_ids' => $post_ids
        )
      );
      break;
    default:
      get_template_part(
        'partials/_latest-chapters',
        null,
        array(
          'simple' => $type == 'simple',
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'orderby' => $orderby,
          'spoiler' => $spoiler == 'true',
          'source' => $source == 'true',
          'post_ids' => $post_ids
        )
      );
  }

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_latest_chapters', 'fictioneer_shortcode_latest_chapters' );
add_shortcode( 'fictioneer_latest_chapter', 'fictioneer_shortcode_latest_chapters' ); // Alias
add_shortcode( 'fictioneer_chapter_cards', 'fictioneer_shortcode_latest_chapters' ); // Alias

// =============================================================================
// LATEST STORIES SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest stories
 *
 * @since 3.0
 *
 * @param int|null    $attr['count']   Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']  Optional. Limit items to a specific author.
 * @param string|null $attr['type']    Optional. Choose between 'default' and 'compact'.
 * @param string|null $attr['orderby'] Optional. Order argument. Default 'date'.
 * @param array|null  $attr['stories'] Optional. Limit items to specific post IDs.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_stories( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $orderby = $attr['orderby'] ?? 'date';
  $post_ids = [];

  if ( ! empty( $attr['stories'] ) ) {
    $post_ids = str_replace( ' ', '', $attr['stories'] );
    $post_ids = explode( ',', $post_ids );
    $post_ids = is_array( $post_ids ) ? $post_ids : [];
    $count = count( $post_ids );
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part(
        'partials/_latest-stories-compact',
        null,
        array(
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'orderby' => $orderby,
          'post_ids' => $post_ids
        )
      );
      break;
    default:
      get_template_part(
        'partials/_latest-stories',
        null,
        array(
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'orderby' => $orderby,
          'post_ids' => $post_ids
        )
      );
  }

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_latest_stories', 'fictioneer_shortcode_latest_stories' );
add_shortcode( 'fictioneer_latest_story', 'fictioneer_shortcode_latest_stories' ); // Alias
add_shortcode( 'fictioneer_story_cards', 'fictioneer_shortcode_latest_stories' ); // Alias

// =============================================================================
// LATEST UPDATES SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest story updates
 *
 * @since 4.3
 *
 * @param int|null    $attr['count']   Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']  Optional. Limit items to a specific author.
 * @param string|null $attr['type']    Optional. Choose between 'default', 'simple', and 'compact'.
 * @param array|null  $attr['stories'] Optional. Limit items to specific post IDs.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_story_updates( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $post_ids = [];

  if ( ! empty( $attr['stories'] ) ) {
    $post_ids = str_replace( ' ', '', $attr['stories'] );
    $post_ids = explode( ',', $post_ids );
    $post_ids = is_array( $post_ids ) ? $post_ids : [];
    $count = count( $post_ids );
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part(
        'partials/_latest-updates-compact',
        null,
        array(
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'post_ids' => $post_ids
        )
      );
      break;
    default:
      get_template_part(
        'partials/_latest-updates',
        null,
        array(
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'simple' => $type == 'simple',
          'post_ids' => $post_ids
        )
      );
  }

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_latest_updates', 'fictioneer_shortcode_latest_story_updates' );
add_shortcode( 'fictioneer_latest_update', 'fictioneer_shortcode_latest_story_updates' ); // Alias
add_shortcode( 'fictioneer_update_cards', 'fictioneer_shortcode_latest_story_updates' ); // Alias

// =============================================================================
// LATEST RECOMMENDATIONS SHORTCODE
// =============================================================================

/**
 * Shortcode to show latest recommendations
 *
 * @since 4.0
 *
 * @param int|null    $attr['count']           Optional. Maximum number of items. Default 4.
 * @param string|null $attr['author']          Optional. Limit items to a specific author.
 * @param string|null $attr['type']            Optional. Choose between 'default' and 'compact'.
 * @param array|null  $attr['recommendations'] Optional. Limit items to specific post IDs.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_recommendations( $attr ) {
  // Setup
  $count = max( 1, intval( $attr['count'] ?? 4 ) );
  $type = $attr['type'] ?? 'default';
  $author = $attr['author'] ?? false;
  $order = $attr['order'] ?? 'desc';
  $orderby = $attr['orderby'] ?? 'date';
  $post_ids = [];

  if ( ! empty( $attr['recommendations'] ) ) {
    $post_ids = str_replace( ' ', '', $attr['recommendations'] );
    $post_ids = explode( ',', $post_ids );
    $post_ids = is_array( $post_ids ) ? $post_ids : [];
    $count = count( $post_ids );
  }

  // Buffer
  ob_start();

  switch ( $type ) {
    case 'compact':
      get_template_part(
        'partials/_latest-recommendations-compact',
        null,
        array(
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'orderby' => $orderby,
          'post_ids' => $post_ids
        )
      );
      break;
    default:
      get_template_part(
        'partials/_latest-recommendations',
        null,
        array(
          'count' => $count,
          'author' => $author,
          'order' => $order,
          'orderby' => $orderby,
          'post_ids' => $post_ids
        )
      );
  }

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_latest_recommendations', 'fictioneer_shortcode_latest_recommendations' );
add_shortcode( 'fictioneer_latest_recommendation', 'fictioneer_shortcode_latest_recommendations' ); // Alias
add_shortcode( 'fictioneer_recommendation_cards', 'fictioneer_shortcode_latest_recommendations' ); // Alias

// =============================================================================
// LATEST POST SHORTCODE
// =============================================================================

/**
 * Shortcode to show the latest post
 *
 * @since 4.0
 *
 * @param int|null    $attr['count']  Optional. Maximum number of items. Default 1.
 * @param string|null $attr['author'] Optional. Limit items to a specific author.
 * @param array|null  $attr['posts']  Optional. Limit items to specific post IDs.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_latest_posts( $attr ) {
  // Setup
  $author = $attr['author'] ?? false;
  $count = max( 1, intval( $attr['count'] ?? 1 ) );
  $post_ids = [];

  if ( ! empty( $attr['posts'] ) ) {
    $post_ids = str_replace( ' ', '', $attr['posts'] );
    $post_ids = explode( ',', $post_ids );
    $post_ids = is_array( $post_ids ) ? $post_ids : [];
    $count = count( $post_ids );
  }

  // Buffer
  ob_start();

  get_template_part(
    'partials/_latest-posts',
    null,
    array(
      'count' => $count,
      'author' => $author,
      'post_ids' => $post_ids
    )
  );

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_latest_posts', 'fictioneer_shortcode_latest_posts' );
add_shortcode( 'fictioneer_latest_post', 'fictioneer_shortcode_latest_posts' ); // Alias

// =============================================================================
// BOOKMARKS SHORTCODE
// =============================================================================

/**
 * Shortcode to show bookmarks
 *
 * @since 4.0
 *
 * @param int|null     $attr['count']      Optional. Maximum number of items. Default -1 (all).
 * @param boolean|null $attr['show_empty'] Optional. Whether to show the "no bookmarks" message. Default false.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_bookmarks( $attr ) {
  // Setup
  $count = max( -1, intval( $attr['count'] ?? -1 ) );
  $show_empty = $attr['show_empty'] ?? false;

  // Buffer
  ob_start();

  get_template_part( 'partials/_bookmarks', null, array(
    'count' => $count,
    'show_empty' => $show_empty
  ));

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_bookmarks', 'fictioneer_shortcode_bookmarks' );

// =============================================================================
// COOKIES SHORTCODE
// =============================================================================

/**
 * Shortcode to show cookie consent actions
 *
 * Renders buttons to handle your consent and stored cookies.
 *
 * @since 4.7
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_cookie_buttons( $attr ) {
  ob_start();
  get_template_part( 'partials/_cookie-buttons' );
  return ob_get_clean();
}
add_shortcode( 'fictioneer_cookie_buttons', 'fictioneer_shortcode_cookie_buttons' );

// =============================================================================
// CHAPTER LIST SHORTCODE
// =============================================================================

/**
 * Shortcode to show chapter list outside of story pages
 *
 * @since 5.0
 * @see fictioneer_validate_id()
 * @see fictioneer_get_story_data()
 *
 * @param int         $attr['story']    Either/Or. The ID of the story the chapters belong to.
 * @param array|null  $attr['chapters'] Either/Or. IDs of chapters to display.
 * @param int|null    $attr['count']    Optional. Maximum number of items. Default -1 (all).
 * @param int|null    $attr['offset']   Optional. Skip a number of posts.
 * @param string|null $attr['group']    Optional. Only show chapters of the group.
 * @param string|null $attr['heading']  Optional. Show <h5> heading above list.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_chapter_list( $attr ) {
  // Build empty case

  ob_start();
  // Start HTML ---> ?>
  <div class="chapter-group">
    <?php if ( ! empty( $attr['heading'] ) ) : ?>
      <h5 class="chapter-group__heading truncate truncate--1-1"><?php echo $attr['heading']; ?></h5>
    <?php endif; ?>
    <ol class="chapter-group__list">
      <li class="chapter-group__list-item _empty">
        <span><?php _e( 'No chapters published yet.', 'fictioneer' ) ?></span>
      </li>
    </ol>
  </div>
  <?php // <--- End HTML
  $empty = ob_get_clean();

  // Abort if...
  if ( empty( $attr['story'] ) && empty( $attr['chapters'] ) ) return $empty;

  // Setup
  $count = max( -1, intval( $attr['count'] ?? -1 ) );
  $offset = max( 0, intval( $attr['offset'] ?? 0 ) );
  $group = empty( $attr['group'] ) ? false : trim( $attr['group'] );
  $heading = empty( $attr['heading'] ) ? false : $attr['heading'];
  $story_id = fictioneer_validate_id( $attr['story'] ?? -1, 'fcn_story' );
  $hide_icons = get_option( 'fictioneer_hide_chapter_icons' );
  $can_checkmarks = get_option( 'fictioneer_enable_checkmarks' );
  $chapter_ids = [];
  $chapters = [];

  // Extract chapter IDs (if any)
  if ( ! empty( $attr['chapters'] ) ) {
    $chapter_ids = str_replace( ' ', '', $attr['chapters'] );
    $chapter_ids = explode( ',', $chapter_ids );
    $chapter_ids = is_array( $chapter_ids ) ? $chapter_ids : [];
  }

  // Get chapters...
  if ( $story_id && empty( $chapter_ids ) ) {
    // ... via story
    $hide_icons = $hide_icons || fictioneer_get_field( 'fictioneer_story_hide_chapter_icons', $story_id );
    $story_data = fictioneer_get_story_data( $story_id );
    $chapters = $story_data['chapter_ids'];
  } elseif ( ! empty( $chapter_ids ) ) {
    // ... via chapter IDs
    $chapters = $chapter_ids;
  }

  // Apply offset
  if ( ! $group ) $chapters = array_slice( $chapters, $offset );

  // Apply count
  if ( ! $group ) $chapters = $count > 0 ? array_slice( $chapters, 0, $count ) : $chapters;

  // Last content check
  if ( empty( $chapters ) ) return $empty;

  // Buffer
  ob_start();

  // Start HTML ---> ?>
  <div class="chapter-group <?php echo $hide_icons ? ' _no-icons' : ''; ?>">
    <?php if ( $heading ) : ?>
      <?php $discriminator = md5( $heading . microtime() ); ?>
      <input id="group-toggle-<?php echo $discriminator; ?>" class="chapter-group__toggle" type="checkbox" hidden>
      <label class="chapter-group__label" for="group-toggle-<?php echo $discriminator; ?>" tabindex="0" role="button" aria-label="<?php esc_attr_e( 'Toggle chapter group collapse', 'fictioneer' ); ?>">
        <i class="fa-solid fa-chevron-down chapter-group__heading-icon"></i>
        <span><?php echo $heading; ?></span>
      </label>
    <?php endif; ?>
    <ol class="chapter-group__list">
      <?php
        $render_count = 0;

        foreach ( $chapters as $post_id ) {
          // Prepare
          $post = get_post( $post_id );
          if ( ! $post ) continue;
          setup_postdata( $post );
          $chapter_story_id = fictioneer_get_field( 'fictioneer_chapter_story', $post_id );

          // Check group (if any)
          if ( $group && $group != trim( fictioneer_get_field( 'fictioneer_chapter_group', $post_id ) ) ) continue;

          // Count renderings
          $render_count++;

          // Apply offset if limited to group
          if ( $group && $offset > 0 && $render_count <= $offset ) continue;

          // Apply count if limited to group
          if ( $group && $count > 0 && $render_count > $count ) break;

          // Data
          $warning = fictioneer_get_field( 'fictioneer_chapter_warning', $post_id );
          $warning_color = fictioneer_get_field( 'fictioneer_chapter_warning_color', $post_id );
          $warning_color = $warning_color ? 'color: ' . $warning_color . ';' : '';
          $icon = fictioneer_get_icon_field( 'fictioneer_chapter_icon', $post_id );
          $text_icon = fictioneer_get_field( 'fictioneer_chapter_text_icon', $post_id );
          $prefix = fictioneer_get_field( 'fictioneer_chapter_prefix', $post_id );
          $words = get_post_meta( $post_id, '_word_count', true );
          $title = fictioneer_get_safe_title( $post_id );

          ?>
          <li class="chapter-group__list-item" data-post-id="<?php echo $post_id; ?>">
            <?php if ( ! empty( $text_icon ) && ! $hide_icons ) : ?>
              <span class="chapter-group__list-item-icon _text text-icon"><?php echo $text_icon; ?></span>
            <?php elseif ( ! $hide_icons ) : ?>
              <i class="<?php echo empty( $icon ) ? 'fa-solid fa-book' : $icon; ?> chapter-group__list-item-icon"></i>
            <?php endif; ?>

            <a href="<?php the_permalink( $post_id ); ?>" class="chapter-group__list-item-link truncate truncate--1-1">
              <?php if ( ! empty( $prefix ) ): ?>
                <span class="chapter-group__list-item-prefix"><?php echo $prefix; ?></span>
              <?php endif; ?>
              <span class="chapter-group__list-item-title"><?php echo $title; ?></span>
            </a>

            <div class="chapter-group__list-item-subrow truncate truncate--1-1 dot-separator">
              <?php if ( ! empty( $warning ) ) : ?>
                <span class="chapter-group__list-item-warning" style="<?php echo $warning_color; ?>"><?php
                  printf( __( '<b>Warning:</b> <span>%s</span>', 'fictioneer' ), $warning );
                ?></span>
              <?php endif; ?>

              <?php if ( $post->post_password ) : ?>
                <span class="chapter-group__list-item-password"><?php echo fcntr( 'password' ) ?></span>
              <?php endif; ?>

              <time datetime="<?php the_time( 'c' ); ?>" class="chapter-group__list-item-date"><?php echo
                get_the_date( '', $post );
              ?></time>

              <span class="chapter-group__list-item-words"><?php
                printf( _x( '%s<span> Words</span>', 'Words in chapter list', 'fictioneer' ), number_format_i18n( $words ) );
              ?></span>
            </div>

            <?php if ( $can_checkmarks && ! empty( $chapter_story_id ) && get_post_status( $chapter_story_id ) === 'publish' ) : ?>
              <div class="chapter-group__list-item-right">
                <button
                  class="checkmark chapter-group__list-item-checkmark"
                  data-type="chapter"
                  data-story-id="<?php echo $chapter_story_id; ?>"
                  data-id="<?php echo $post_id; ?>"
                  role="checkbox"
                  aria-checked="false"
                  aria-label="<?php printf( esc_attr__( 'Chapter checkmark for %s.', 'fictioneer' ), $title ); ?>"
                ><i class="fa-solid fa-check"></i></button>
              </div>
            <?php endif; ?>
          </li>
          <?php
        }

        // Restore postdata
        wp_reset_postdata();
      ?>
    </ol>
  </div>
  <?php // <--- End HTML

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_chapter_list', 'fictioneer_shortcode_chapter_list' );

// =============================================================================
// CONTACT FORM SHORTCODE
// =============================================================================

/**
 * Shortcode to show a contact form
 *
 * @since 5.0
 *
 * @param string|null  $attr['title']          Optional. Title of the form.
 * @param string|null  $attr['submit']         Optional. Submit button caption.
 * @param string|null  $attr['privacy_policy'] Optional. Must accept privacy policy.
 * @param boolean|null $attr['required']       Optional. Make all fields required
 * @param string|null  $attr['email']          Optional. Email field.
 * @param string|null  $attr['name']           Optional. Name field.
 * @param string|null  $attr["text_{$i}"]      Optional. Up to 6 extra text field(s).
 * @param string|null  $attr["check_{$i}"]     Optional. Up to 6 extra checkbox field(s).
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_contact_form( $attr ) {
  // Setup
  $title = $attr['title'] ?? _x( 'Nameless Form', 'Contact form.', 'fictioneer' );
  $submit = $attr['submit'] ?? __( 'Submit', 'fictioneer' );
  $privacy_policy = filter_var( $attr['privacy_policy'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $required = isset( $attr['required'] ) ? 'required' : '';
  $email = $attr['email'] ?? '';
  $name = $attr['name'] ?? '';
  $fields = [];

  // HTML snippets
  if ( ! empty( $email ) ) {
    $fields[] = "<input style='opacity: 0;' type='email' name='email' maxlength='100' placeholder='$email' $required>";
  }

  if ( ! empty( $name ) ) {
    $fields[] = "<input style='opacity: 0;' type='text' name='name' maxlength='100' placeholder='$name' $required>";
  }

  // Custom text fields
  for ( $i = 1; $i <= 6; $i++ ) {
    $field = $attr["text_$i"] ?? '';

    if ( ! empty( $field ) ) {
      $fields[] = "<input style='opacity: 0;' type='text' name='text_$i' maxlength='100' placeholder='$field' $required><input type='hidden' name='text_label_$i' value='$field'>";
    }
  }

  // Custom checkboxes
  for ( $i = 1; $i <= 6; $i++ ) {
    $field = $attr["check_$i"] ?? '';

    if ( ! empty( $field ) ) {
      $fields[] = "<label class='checkbox-label'><input class='_no-stretch' style='opacity: 0;' type='checkbox' value='1' name='check_$i' autocomplete='off' $required><span>$field</span></label><input type='hidden' name='check_label_$i' value='$field'>";
    }
  }

  // Privacy policy checkbox
  $privacy_policy_link = get_option( 'wp_page_for_privacy_policy' ) ? esc_url( get_privacy_policy_url() ) : false;

  if ( $privacy_policy && $privacy_policy_link ) {
    $fields[] = "<label class='checkbox-label'><input class='_no-stretch' style='opacity: 0;' type='checkbox' value='1' name='privacy_policy' autocomplete='off' required><span>" . sprintf( fcntr( 'accept_privacy_policy' ), $privacy_policy_link ) . "</span></label><input type='hidden' name='require_privacy_policy' value='1'>";
  }

  // Apply filters
  $fields = apply_filters( 'fictioneer_filter_contact_form_fields', $fields, get_the_ID() );

  // Buffer
  ob_start();

  /*
   * The inline "opacity: 0" style is a reverse bot trap. They are made visible again with CSS,
   * but dumb bots might think this is a trap for them and ignore the fields. Not a reliable
   * spam protection, but this is security in depth.
   *
   * The "phone" and "terms" inputs are honeypots.
   */

  // Start HTML ---> ?>
  <form class="contact-form">
    <div class="contact-form__message">
      <textarea class="contact-form__textarea adaptive-textarea" style="opacity: 0;" name="message" maxlength="65525" placeholder="<?php _e( 'Please enter your message.', 'fictioneer' ); ?>" required></textarea>
    </div>
    <?php if ( ! empty( $fields ) ) : ?>
      <div class="contact-form__fields">
        <?php foreach( $fields as $field ) : ?>
          <div class="contact-form__field"><?php echo $field; ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <input type="checkbox" name="terms" value="1" autocomplete="off" tabindex="-1">
    <input type="tel" name="phone" autocomplete="off" tabindex="-1">
    <input type="hidden" name="title" value="<?php echo $title; ?>">
    <div class="contact-form__actions">
      <button class="contact-form__submit button" type="button" data-enabled="<?php echo esc_attr( $submit ); ?>" data-disabled="<?php esc_attr_e( 'Sending…', 'fictioneer' ); ?>" data-done="<?php esc_attr_e( 'Message sent!', 'fictioneer' ); ?>"><?php echo $submit; ?></button>
    </div>
  </form>
  <?php // <--- End HTML

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_contact_form', 'fictioneer_shortcode_contact_form' );

// =============================================================================
// SEARCH SHORTCODE
// =============================================================================

/**
 * Shortcode to show the latest post
 *
 * @since 5.0
 *
 * @param boolean|null $attr['simple']      Optional. Hide the advanced options.
 * @param string|null  $attr['placeholder'] Optional. Placeholder text.
 *
 * @return string The rendered shortcode HTML.
 */

function fictioneer_shortcode_search( $attr ) {
  // Setup
  $args = ['cache' => true];
  $simple = isset( $attr['simple'] ) ? $attr['simple'] == 'true' || $attr['simple'] == '1' : false;
  $placeholder = $attr['placeholder'] ?? false;

  // Prepare arguments
  if ( $simple ) $args['simple'] = $simple;
  if ( $placeholder ) $args['placeholder'] = $placeholder;

  // Buffer
  ob_start();

  get_search_form( $args );

  // Return buffer
  return ob_get_clean();
}
add_shortcode( 'fictioneer_search', 'fictioneer_shortcode_search' );

?>
