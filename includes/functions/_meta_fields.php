<?php

// =============================================================================
// META FIELD HELPERS
// =============================================================================

function fictioneer_get_metabox_checkbox( $post, $meta_key, $label, $args = [] ) {
  // Setup
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';

  ob_start();

  // Start HTML ---> ?>
  <label class="fictioneer-metabox-checkbox" for="<?php echo $meta_key; ?>" <?php echo $data_required; ?>>
    <div class="fictioneer-metabox-checkbox__checkbox">
      <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
      <input type="checkbox" id="<?php echo $meta_key; ?>" name="<?php echo $meta_key; ?>" value="1" autocomplete="off" <?php checked( get_post_meta( $post->ID, $meta_key, true ), 1 ); ?> <?php echo $required; ?>>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" focusable="false"><path d="M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"></path></svg>
    </div>
    <div class="fictioneer-metabox-checkbox__label"><?php echo $label; ?></div>
  </label>
  <?php // <--- End HTML

  return ob_get_clean();
}

function fictioneer_get_metabox_text( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-metabox-text" <?php echo $data_required; ?>>
    <?php if ( $label ) : ?>
      <label class="fictioneer-metabox-text__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
    <input type="text" id="<?php echo $meta_key; ?>" class="fictioneer-metabox-text__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>>
    <?php if ( $description ) : ?>
      <div class="fictioneer-metabox-text__description"><?php echo $description; ?></div>
    <?php endif; ?>
  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

function fictioneer_get_metabox_url( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-metabox-url" <?php echo $data_required; ?>>
    <?php if ( $label ) : ?>
      <label class="fictioneer-metabox-url__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
    <div class="fictioneer-metabox-url__wrapper">
      <span class="fictioneer-metabox-url__icon dashicons dashicons-admin-site"></span>
      <input type="url" id="<?php echo $meta_key; ?>" class="fictioneer-metabox-url__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>>
    </div>
    <?php if ( $description ) : ?>
      <div class="fictioneer-metabox-url__description"><?php echo $description; ?></div>
    <?php endif; ?>
  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

function fictioneer_get_metabox_array( $post, $meta_key, $args = [] ) {
  // Setup
  $array = get_post_meta( $post->ID, $meta_key, true );
  $array = is_array( $array ) ? $array : [];
  $list = esc_attr( implode( ', ', $array ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-metabox-text" <?php echo $data_required; ?>>
    <?php if ( $label ) : ?>
      <label class="fictioneer-metabox-text__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
    <input type="text" id="<?php echo $meta_key; ?>" class="fictioneer-metabox-text__input" name="<?php echo $meta_key; ?>" value="<?php echo $list; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>>
    <?php if ( $description ) : ?>
      <div class="fictioneer-metabox-text__description"><?php echo $description; ?></div>
    <?php endif; ?>
  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

function fictioneer_get_metabox_select( $post, $meta_key, $options, $args = [] ) {
  // Setup
  $selected = get_post_meta( $post->ID, $meta_key, true );
  $selected = empty( $selected ) ? array_keys( $options )[0] : $selected;
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-metabox-select" <?php echo $data_required; ?>>
    <?php if ( $label ) : ?>
      <label class="fictioneer-metabox-select__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
    <div class="fictioneer-metabox-select__wrapper">
      <select id="<?php echo $meta_key; ?>" class="fictioneer-metabox-select__select" name="<?php echo $meta_key; ?>" autocomplete="off" <?php echo $required; ?>><?php
        foreach ( $options as $value => $translation ) {
          $value = esc_attr( $value );
          $translation = esc_html( $translation );

          echo "<option value='{$value}' " . selected( $selected, $value, false ) . ">{$translation}</option>";
        }
      ?></select>
    </div>
    <?php if ( $description ) : ?>
      <div class="fictioneer-metabox-select__description"><?php echo $description; ?></div>
    <?php endif; ?>
  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

function fictioneer_get_metabox_textarea( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = get_post_meta( $post->ID, $meta_key, true );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-metabox-textarea" <?php echo $data_required; ?>>
    <?php if ( $label ) : ?>
      <label class="fictioneer-metabox-textarea__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
    <div class="fictioneer-metabox-textarea__wrapper">
      <textarea id="<?php echo $meta_key; ?>" class="fictioneer-metabox-textarea__textarea" name="<?php echo $meta_key; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>><?php echo $meta_value; ?></textarea>
    </div>
    <?php if ( $description ) : ?>
      <div class="fictioneer-metabox-textarea__description"><?php echo $description; ?></div>
    <?php endif; ?>
  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

function fictioneer_get_metabox_image( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = get_post_meta( $post->ID, $meta_key, true );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $upload = strval( $args['button'] ?? _x( 'Set image', 'Metabox image upload button.', 'fictioneer' ) );
  $replace = _x( 'Replace', 'Metabox image upload button.', 'fictioneer' );
  $remove = _x( 'Remove', 'Metabox image remove button.', 'fictioneer' );
  $image_id = get_post_meta( $post->ID, $meta_key, true );
  $image_url = wp_get_attachment_url( $image_id );
  $image_css = $image_url ? "style='background-image: url(\"{$image_url}\");'" : '';

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-metabox-image">
    <?php if ( $label ) : ?>
      <label class="fictioneer-metabox-image__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <input type="hidden" name="<?php echo $meta_key; ?>" class="fictioneer-metabox-image__id" value="<?php echo esc_attr( $meta_value ); ?>" autocomplete="off">
    <div class="fictioneer-metabox-image__wrapper">
      <div class="fictioneer-metabox-image__display" <?php echo $image_css; ?>></div>
      <button class="fictioneer-metabox-image__upload <?php echo $image_css ? 'hidden' : ''; ?>"><?php echo $upload; ?></button>
      <div class="fictioneer-metabox-image__edit-actions <?php echo $image_css ? '' : 'hidden'; ?>">
        <button class="fictioneer-metabox-image__replace"><?php echo $replace; ?></button>
        <button class="fictioneer-metabox-image__remove"><?php echo $remove; ?></button>
      </div>
    </div>
    <?php if ( $description ) : ?>
      <div class="fictioneer-metabox-image__description"><?php echo $description; ?></div>
    <?php endif; ?>
  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

// =============================================================================
// STORY META FIELDS
// =============================================================================

/**
 * Adds story metabox
 *
 * @since Fictioneer 5.7.4
 */

function fictioneer_add_story_meta_metabox() {
  add_meta_box(
    'fictioneer-story-meta',
    __( 'Story Meta', 'fictioneer' ),
    'fictioneer_render_story_metabox',
    ['fcn_story'],
    'side',
    'high'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_story_meta_metabox' );

/**
 * Append classes to the story metabox
 *
 * @since Fictioneer 5.7.4
 *
 * @param array $classes  An array of postbox classes.
 *
 * @return array The modified array of postbox classes.
 */

function fictioneer_append_story_metabox_classes( $classes ) {
  // Add class
  $classes[] = 'fictioneer-side-metabox';

  // Return with added class
  return $classes;
}
add_filter( 'postbox_classes_fcn_story_fictioneer-story-meta', 'fictioneer_append_story_metabox_classes' );

/**
 * Render story metabox
 *
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_story_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( 'fictioneer_metabox_nonce' );
  $output = [];

  // --- Add fields --------------------------------------------------------------

  $output['fictioneer_story_status'] = fictioneer_get_metabox_select(
    $post,
    'fictioneer_story_status',
    array(
      'Ongoing' => _x( 'Ongoing', 'Story status select option.', 'fictioneer' ),
      'Completed' => _x( 'Completed', 'Story status select option.', 'fictioneer' ),
      'Oneshot' => _x( 'Oneshot', 'Story status select option.', 'fictioneer' ),
      'Hiatus' => _x( 'Hiatus', 'Story status select option.', 'fictioneer' ),
      'Canceled' => _x( 'Canceled', 'Story status select option.', 'fictioneer' )
    ),
    array(
      'label' => _x( 'Status', 'Story status meta field label.', 'fictioneer' ),
      'description' => __( 'Current status of the story.', 'fictioneer' )
    ),
    array( 'required' => 1 )
  );

  $output['fictioneer_story_rating'] = fictioneer_get_metabox_select(
    $post,
    'fictioneer_story_rating',
    array(
      'Everyone' => _x( 'Everyone', 'Story age rating select option.', 'fictioneer' ),
      'Teen' => _x( 'Teen', 'Story age rating select option.', 'fictioneer' ),
      'Mature' => _x( 'Mature', 'Story age rating select option.', 'fictioneer' ),
      'Adult' => _x( 'Adult', 'Story age rating select option.', 'fictioneer' )
    ),
    array(
      'label' => _x( 'Age Rating', 'Story age rating meta field label.', 'fictioneer' ),
      'description' => __( 'Select an overall age rating.', 'fictioneer' )
    ),
    array( 'required' => 1 )
  );

  $output['fictioneer_story_co_authors'] = fictioneer_get_metabox_array(
    $post,
    'fictioneer_story_co_authors',
    array(
      'label' => _x( 'Co-Authors', 'Story co-authors meta field label.', 'fictioneer' ),
      'description' => __( 'Comma-separated list of author IDs.', 'fictioneer' )
    )
  );

  $output['fictioneer_story_copyright_notice'] = fictioneer_get_metabox_text(
    $post,
    'fictioneer_story_copyright_notice',
    array(
      'label' => _x( 'Copyright Notice', 'Story copyright notice meta field label.', 'fictioneer' ),
      'description' => __( 'Optional, leave empty to hide.', 'fictioneer' )
    )
  );

  $output['fictioneer_story_topwebfiction_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_story_topwebfiction_link',
    array(
      'label' => _x( 'Top Web Fiction Link', 'Story top web fiction link meta field label.', 'fictioneer' ),
      'description' => __( 'URL to Top Web Fiction page.', 'fictioneer' )
    )
  );

  if ( current_user_can( 'fcn_make_sticky', $post->ID ) && FICTIONEER_ENABLE_STICKY_CARDS ) {
    $output['fictioneer_story_sticky'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_story_sticky',
      __( 'Sticky in lists', 'fictioneer' )
    );
  }

  $output['fictioneer_story_hidden'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_story_hidden',
    __( 'Hide story in lists', 'fictioneer' )
  );

  $output['fictioneer_story_no_thumbnail'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_story_no_thumbnail',
    __( 'Hide thumbnail on story page', 'fictioneer' )
  );

  $output['fictioneer_story_no_tags'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_story_no_tags',
    __( 'Hide tags on story page', 'fictioneer' )
  );

  if ( ! get_option( 'fictioneer_hide_chapter_icons' ) ) {
    $output['fictioneer_story_hide_chapter_icons'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_story_hide_chapter_icons',
      __( 'Hide chapter icons', 'fictioneer' )
    );
  }

  if ( ! get_option( 'fictioneer_disable_chapter_collapsing' ) ) {
    $output['fictioneer_story_disable_collapse'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_story_disable_collapse',
      __( 'Disable collapsing of chapters', 'fictioneer' )
    );
  }

  if ( get_option( 'fictioneer_enable_chapter_groups' ) ) {
    $output['fictioneer_story_disable_groups'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_story_disable_groups',
      __( 'Disable chapter groups', 'fictioneer' )
    );
  }

  if ( get_option( 'fictioneer_enable_epubs' ) ) {
    $output['fictioneer_story_no_epub'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_story_no_epub',
      __( 'Disable ePUB download', 'fictioneer' )
    );
  }

  $output['fictioneer_disable_commenting'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_disable_commenting',
    __( 'Disable new comments', 'fictioneer' )
  );

  if ( current_user_can( 'fcn_custom_page_css', $post->ID ) ) {
    $output['fictioneer_story_css'] = fictioneer_get_metabox_textarea(
      $post,
      'fictioneer_story_css',
      array(
        'label' => __( 'Custom Story CSS', 'fictioneer' ),
        'description' => __( 'Applied to the story and all chapters.', 'fictioneer' ),
        'placeholder' => __( '.selector { ... }', 'fictioneer' )
      )
    );
  }

  $output['fictioneer_landscape_image'] = fictioneer_get_metabox_image(
    $post,
    'fictioneer_landscape_image',
    array(
      'label' => __( 'Landscape Cover Image', 'fictioneer' ),
      'description' => __( 'Used where the image is wider than high.', 'fictioneer' ),
      'button' => __( 'Set landscape image', 'fictioneer' ),
    )
  );

  // --- Filters -----------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_story_sidebar_meta_fields', $output, $post );

  // --- Render ------------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_metabox_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save story metabox data
 *
 * @since Fictioneer 4.0
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_story_metabox( $post_id ) {
  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_metabox_nonce'] ?? '' ), 'fictioneer_metabox_nonce' ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_story'
  ) {
    return;
  }

  // --- Permissions? ------------------------------------------------------------

  if (
    ! current_user_can( 'edit_fcn_stories', $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( 'edit_published_fcn_stories', $post_id ) )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $allowed_statuses = ['Ongoing', 'Completed', 'Oneshot', 'Hiatus', 'Canceled'];
  $status = fictioneer_sanitize_selection( $_POST['fictioneer_story_status'] ?? '', $allowed_statuses, $allowed_statuses[0] );

  $allowed_ratings = ['Everyone', 'Teen', 'Mature', 'Adult'];
  $rating = fictioneer_sanitize_selection( $_POST['fictioneer_story_rating'] ?? '', $allowed_ratings, $allowed_ratings[0] );

  $fields = array(
    'fictioneer_story_status' => $status,
    'fictioneer_story_rating' => $rating,
    'fictioneer_story_copyright_notice' => sanitize_text_field( $_POST['fictioneer_story_copyright_notice'] ?? '' ),
    'fictioneer_story_hidden' => fictioneer_sanitize_checkbox( $_POST['fictioneer_story_hidden'] ?? 0 ),
    'fictioneer_story_no_thumbnail' => fictioneer_sanitize_checkbox( $_POST['fictioneer_story_no_thumbnail'] ?? 0 ),
    'fictioneer_story_no_tags' => fictioneer_sanitize_checkbox( $_POST['fictioneer_story_no_tags'] ?? 0 ),
    'fictioneer_disable_commenting' => fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_commenting'] ?? 0 ),
    'fictioneer_landscape_image' => absint( $_POST['fictioneer_landscape_image'] ?? 0 )
  );

  $twf_url = sanitize_url( $_POST['fictioneer_story_topwebfiction_link'] ?? '' );
  $twf_url = filter_var( $twf_url, FILTER_VALIDATE_URL ) ? $twf_url : '';
  $twf_url = strpos( $twf_url, 'https://topwebfiction.com/') === 0 ? $twf_url : '';
  $fields['fictioneer_story_topwebfiction_link'] = $twf_url;

  $co_authors = fictioneer_explode_list( $_POST['fictioneer_story_co_authors'] ?? '' );
  $co_authors = array_map( 'absint', $co_authors );
  $fields['fictioneer_story_co_authors'] = array_unique( $co_authors );

  if ( current_user_can( 'fcn_make_sticky', $post_id ) && FICTIONEER_ENABLE_STICKY_CARDS ) {
    $fields['fictioneer_story_sticky'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_story_sticky'] ?? 0 );
  }

  if ( get_option( 'fictioneer_enable_epubs' ) ) {
    $fields['fictioneer_story_no_epub'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_story_no_epub'] ?? 0 );
  }

  if ( ! get_option( 'fictioneer_disable_chapter_collapsing' ) ) {
    $fields['fictioneer_story_disable_collapse'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_story_disable_collapse'] ?? 0 );
  }

  if ( ! get_option( 'fictioneer_hide_chapter_icons' ) ) {
    $fields['fictioneer_story_hide_chapter_icons'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_story_hide_chapter_icons'] ?? 0 );
  }

  if ( get_option( 'fictioneer_enable_chapter_groups' ) ) {
    $fields['fictioneer_story_disable_groups'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_story_disable_groups'] ?? 0 );
  }

  if ( current_user_can( 'fcn_custom_page_css', $post_id ) ) {
    $css = sanitize_textarea_field( $_POST['fictioneer_story_css'] ?? '' );
    $css = preg_match( '/<\/?\w+/', $css ) ? '' : $css;
    $fields['fictioneer_story_css'] = str_replace( '<', '', $css );
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_story_sidebar_meta_updates', $fields );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 );
  }
}
add_action( 'save_post', 'fictioneer_save_story_metabox' );




?>
