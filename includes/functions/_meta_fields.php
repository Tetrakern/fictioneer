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
  <label class="fictioneer-meta-checkbox" for="<?php echo $meta_key; ?>" <?php echo $data_required; ?>>

    <div class="fictioneer-meta-checkbox__checkbox">
      <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
      <input type="checkbox" id="<?php echo $meta_key; ?>" name="<?php echo $meta_key; ?>" value="1" autocomplete="off" <?php checked( get_post_meta( $post->ID, $meta_key, true ), 1 ); ?> <?php echo $required; ?>>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" focusable="false"><path d="M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"></path></svg>
    </div>

    <div class="fictioneer-meta-checkbox__label"><?php echo $label; ?></div>

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
  <div class="fictioneer-meta-field fictioneer-meta-field--text" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <input type="text" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
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
  <div class="fictioneer-meta-field fictioneer-meta-field--url" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <span class="fictioneer-meta-field__url-icon dashicons dashicons-admin-site"></span>
      <input type="url" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
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
  <div class="fictioneer-meta-field fictioneer-meta-field--array" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <input type="text" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $list; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
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
  <div class="fictioneer-meta-field fictioneer-meta-field--select" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <select id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__select" name="<?php echo $meta_key; ?>" autocomplete="off" <?php echo $required; ?>><?php
        foreach ( $options as $value => $translation ) {
          $value = esc_attr( $value );
          $translation = esc_html( $translation );

          echo "<option value='{$value}' " . selected( $selected, $value, false ) . ">{$translation}</option>";
        }
      ?></select>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
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
  <div class="fictioneer-meta-field fictioneer-meta-field--textarea" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <textarea id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__textarea" name="<?php echo $meta_key; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>><?php echo $meta_value; ?></textarea>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
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
  <div class="fictioneer-meta-field fictioneer-meta-field--select" data-target="fcn-meta-field-image">

    <?php if ( $label ) : ?>
      <div class="fictioneer-meta-field__label"><?php echo $label; ?></div>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="<?php echo esc_attr( $meta_value ); ?>" autocomplete="off" data-target="fcn-meta-field-image-id">

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--image">
      <div class="fictioneer-meta-field__image-display" <?php echo $image_css; ?>></div>
      <button type="button" class="fictioneer-meta-field__image-upload <?php echo $image_css ? 'hidden' : ''; ?>"><?php echo $upload; ?></button>
      <div class="fictioneer-meta-field__image-actions <?php echo $image_css ? '' : 'hidden'; ?>">
        <button type="button" class="fictioneer-meta-field__image-replace"><?php echo $replace; ?></button>
        <button type="button" class="fictioneer-meta-field__image-remove"><?php echo $remove; ?></button>
      </div>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

function fictioneer_get_metabox_tokens( $post, $meta_key, $options, $args = [] ) {
  // Setup
  $array = get_post_meta( $post->ID, $meta_key, true );
  $array = is_array( $array ) ? $array : [];
  $list = esc_attr( implode( ', ', $array ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--tokens" data-target="fcn-meta-field-tokens">

    <?php if ( $label ) : ?>
      <div class="fictioneer-meta-field__label"><?php echo $label; ?></div>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <input type="hidden" value="<?php echo esc_attr( json_encode( $options ) ); ?>" autocomplete="off" data-target="fcn-meta-field-tokens-options">

    <input type="hidden" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $list; ?>" autocomplete="off" data-target="fcn-meta-field-tokens-values">

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--tokens">

      <div class="fictioneer-meta-field__token-track" data-target="fcn-meta-field-tokens-track">
        <?php
          foreach ( $array as $token ) {
            $name = get_the_title( $token ) ?: __( 'n/a', 'fictioneer' );

            echo "<span class='fictioneer-meta-field__token' data-id='{$token}'><span class='fictioneer-meta-field__token-name'>{$name}</span><button type='button' class='fictioneer-meta-field__token-button' data-id='{$token}'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24' aria-hidden='true' focusable='false'><path d='M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z'></path></svg></button></span>";
          }
        ?>
      </div>

      <select class="fictioneer-meta-field__select" autocomplete="off" data-target="fcn-meta-field-tokens-add"><?php
        foreach ( $options as $value => $translation ) {
          echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $translation ) . '</option>';
        }
      ?></select>

    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

// =============================================================================
// METABOX CLASSES
// =============================================================================

/**
 * Append classes to the metabox
 *
 * @since Fictioneer 5.7.4
 *
 * @param array $classes  An array of postbox classes.
 *
 * @return array The modified array of postbox classes.
 */

function fictioneer_append_metabox_classes( $classes ) {
  // Add class
  $classes[] = 'fictioneer-side-metabox';

  // Return with added class
  return $classes;
}
add_filter( 'postbox_classes_fcn_story_fictioneer-story-meta', 'fictioneer_append_metabox_classes' );

foreach ( ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'] as $type ) {
  add_filter( "postbox_classes_{$type}_fictioneer-advanced", 'fictioneer_append_metabox_classes' );
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
      'description' => __( 'Current status of the story.', 'fictioneer' ),
      'required' => 1
    )
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
      'description' => __( 'Select an overall age rating.', 'fictioneer' ),
      'required' => 1
    )
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

  $output['additional_settings_heading'] = '<div class="fictioneer-meta-field-heading">' .
    __( 'Flags', 'Metabox checkbox heading.', 'fictioneer' ) . '</div>';

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

  // --- Filters -----------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_story_meta_fields', $output, $post );

  // --- Render ------------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_metabox_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save story metabox data
 *
 * @since Fictioneer 5.7.4
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
    'fictioneer_story_no_tags' => fictioneer_sanitize_checkbox( $_POST['fictioneer_story_no_tags'] ?? 0 )
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

  $fields = apply_filters( 'fictioneer_filter_story_meta_updates', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_story_metabox' );

// =============================================================================
// ADVANCED META FIELDS
// =============================================================================

/**
 * Adds advanced metabox
 *
 * @since Fictioneer 5.7.4
 */

function fictioneer_add_advanced_metabox() {
  add_meta_box(
    'fictioneer-advanced',
    __( 'Advanced', 'fictioneer' ),
    'fictioneer_render_advanced_metabox',
    ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
    'side',
    'high'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_advanced_metabox' );

/**
 * Render advanced metabox
 *
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_advanced_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( 'fictioneer_metabox_nonce' );
  $output = [];
  $author_id = $post->post_author ?: get_current_user_id();

  // --- Add fields --------------------------------------------------------------

  // Landscape Image
  $output['fictioneer_landscape_image'] = fictioneer_get_metabox_image(
    $post,
    'fictioneer_landscape_image',
    array(
      'label' => __( 'Landscape Image', 'fictioneer' ),
      'description' => __( 'Used where the image is wider than high.', 'fictioneer' ),
      'button' => __( 'Set landscape image', 'fictioneer' )
    )
  );

  if ( current_user_can( 'fcn_custom_page_header', $post->ID ) ) {
    $output['fictioneer_custom_header_image'] = fictioneer_get_metabox_image(
      $post,
      'fictioneer_custom_header_image',
      array(
        'label' => __( 'Header Image', 'fictioneer' ),
        'description' => __( 'Replaces the default header image.', 'fictioneer' ),
        'button' => __( 'Set header image', 'fictioneer' ),
      )
    );
  }

  if ( current_user_can( 'fcn_custom_page_css', $post->ID ) ) {
    $output['fictioneer_custom_css'] = fictioneer_get_metabox_textarea(
      $post,
      'fictioneer_custom_css',
      array(
        'label' => __( 'Custom Page CSS', 'fictioneer' ),
        'description' => __( 'Only applied to the page.', 'fictioneer' ),
        'placeholder' => __( '.selector { ... }', 'fictioneer' )
      )
    );
  }

  if ( $post->post_type === 'page' ) {
    $output['fictioneer_short_name'] = fictioneer_get_metabox_text(
      $post,
      'fictioneer_short_name',
      array(
        'label' => _x( 'Short Name', 'Page short name meta field label.', 'fictioneer' ),
        'description' => __( 'Required for the tab view in stories.', 'fictioneer' )
      )
    );

    if ( current_user_can( 'install_plugins' ) ) {
      $output['fictioneer_filter_and_search_id'] = fictioneer_get_metabox_text(
        $post,
        'fictioneer_filter_and_search_id',
        array(
          'label' => _x( 'Filter & Search ID', 'Page filter ans search meta field label.', 'fictioneer' ),
          'description' => __( 'ID for a filter and/or search plugins.', 'fictioneer' )
        )
      );
    }
  }

  // Story Blogs
  if ( $post->post_type === 'post' ) {
    $user_stories = get_posts(
      array(
        'post_type' => 'fcn_story',
        'post_status' => ['publish', 'private'],
        'author' => $author_id,
        'posts_per_page' => -1,
        'update_post_meta_cache' => false, // Improve performance
        'update_post_term_cache' => false, // Improve performance
        'no_found_rows' => true // Improve performance
      )
    );

    if ( ! empty( $user_stories ) ) {
      $blog_options = array( '0' => __( '— Story —', 'fictioneer' ) );

      foreach ( $user_stories as $story ) {
        $blog_options[ $story->ID ] = $story->post_title;
      }

      $output['fictioneer_post_story_blogs'] = fictioneer_get_metabox_tokens(
        $post,
        'fictioneer_post_story_blogs',
        $blog_options,
        array(
          'label' => _x( 'Story Blogs', 'Story blogs meta field label.', 'fictioneer' ),
          'description' => __( 'Select where the post should appear.', 'fictioneer' )
        )
      );
    }
  }

  // Checkbox: Disable new comments
  if ( in_array( $post->post_type, ['post', 'page', 'fcn_story', 'fcn_chapter'] ) ) {
    $output['additional_settings_heading'] = '<div class="fictioneer-meta-field-heading">' .
      _x( 'Flags', 'Metabox checkbox heading.', 'fictioneer' ) . '</div>';

    $output['fictioneer_disable_commenting'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_disable_commenting',
      __( 'Disable new comments', 'fictioneer' )
    );
  }

  // --- Filters -----------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_advanced_meta_fields', $output, $post );

  // --- Render ------------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_metabox_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save advanced metabox data
 *
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_advanced_metabox( $post_id ) {
  $post_type = get_post_type( $post_id );
  $post_author = get_post_field( 'post_author', $post_id );

  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_metabox_nonce'] ?? '' ), 'fictioneer_metabox_nonce' ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    ! in_array( $post_type, ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'] )
  ) {
    return;
  }

  // --- Permissions? ------------------------------------------------------------

  $permission_list = array(
    'post' => ['edit_posts', 'edit_published_posts'],
    'page' => ['edit_pages', 'edit_published_pages'],
    'fcn_story' => ['edit_fcn_stories', 'edit_published_fcn_stories'],
    'fcn_chapter' => ['edit_fcn_chapters', 'edit_published_fcn_chapters'],
    'fcn_collections' => ['edit_fcn_collections', 'edit_published_fcn_collections'],
    'fcn_recommendation' => ['edit_fcn_recommendations', 'edit_published_fcn_recommendations']
  );

  if (
    ! current_user_can( $permission_list[ $post_type ][0], $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( $permission_list[ $post_type ][1], $post_id ) )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  // Landscape Image
  $fields = array(
    'fictioneer_landscape_image' => absint( $_POST['fictioneer_landscape_image'] ?? 0 )
  );

  // Custom Page Header
  if ( current_user_can( 'fcn_custom_page_header', $post_id ) ) {
    $fields['fictioneer_custom_header_image'] = absint( $_POST['fictioneer_custom_header_image'] ?? 0 );
  }

  // Custom Page CSS
  if ( current_user_can( 'fcn_custom_page_css', $post_id ) ) {
    $css = sanitize_textarea_field( $_POST['fictioneer_custom_css'] ?? '' );
    $css = preg_match( '/<\/?\w+/', $css ) ? '' : $css;
    $fields['fictioneer_custom_css'] = str_replace( '<', '', $css );
  }

  // Short Name
  if ( $post_type === 'page' ) {
    $fields['fictioneer_short_name'] = sanitize_text_field( $_POST['fictioneer_short_name'] ?? '' );
  }

  // Search & Filter ID
  if ( $post_type === 'page' ) {
    if ( current_user_can( 'install_plugins' ) ) {
      $fields['fictioneer_filter_and_search_id'] = absint( $_POST['fictioneer_filter_and_search_id'] ?? 0 );
    }
  }

  // Story Blogs
  if ( $post_type === 'post' ) {
    $story_blogs = fictioneer_explode_list( $_POST['fictioneer_post_story_blogs'] ?? '' );

    if ( ! empty( $story_blogs ) ) {
      $story_blogs = array_map( 'absint', $story_blogs );
      $story_blogs = array_map( 'strval', $story_blogs ); // Safer to match with LIKE in SQL
      $story_blogs = array_unique( $story_blogs );

      // Ensure the stories belong to the post author
      $blog_story_query = new WP_Query(
        array(
          'author' => $post_author,
          'post_type' => 'fcn_story',
          'post_status' => ['publish', 'private'],
          'post__in' => $story_blogs,
          'fields' => 'ids',
          'posts_per_page'=> -1,
          'update_post_meta_cache' => false, // Improve performance
          'update_post_term_cache' => false, // Improve performance
          'no_found_rows' => true // Improve performance
        )
      );

      $story_blogs = $blog_story_query->posts;
    }

    $fields['fictioneer_post_story_blogs'] = $story_blogs;
  }

  // Checkbox: Disable new comments
  if ( in_array( $post_type, ['post', 'page', 'fcn_story', 'fcn_chapter'] ) ) {
    $fields['fictioneer_disable_commenting'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_commenting'] ?? 0 );
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_advanced_meta_updates', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_advanced_metabox' );

// =============================================================================
// SUPPORT LINKS META FIELDS
// =============================================================================




?>
