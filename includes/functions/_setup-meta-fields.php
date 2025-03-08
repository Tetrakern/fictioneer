<?php

// Array: Example collection of Font Awesome icons
if ( ! defined( 'FICTIONEER_EXAMPLE_CHAPTER_ICONS' ) ) {
  define(
    'FICTIONEER_EXAMPLE_CHAPTER_ICONS',
    ['fa-solid fa-book', 'fa-solid fa-star', 'fa-solid fa-heart', 'fa-solid fa-bomb', 'fa-solid fa-wine-glass',
    'fa-solid fa-face-smile', 'fa-solid fa-shield', 'fa-solid fa-ghost', 'fa-solid fa-gear', 'fa-solid fa-droplet',
    'fa-solid fa-fire', 'fa-solid fa-radiation', 'fa-solid fa-lemon', 'fa-solid fa-globe', 'fa-solid fa-flask',
    'fa-solid fa-snowflake', 'fa-solid fa-cookie-bite', 'fa-solid fa-circle', 'fa-solid fa-square', 'fa-solid fa-moon',
    'fa-solid fa-brain', 'fa-solid fa-diamond', 'fa-solid fa-virus', 'fa-solid fa-horse-head', 'fa-solid fa-certificate',
    'fa-solid fa-scroll', 'fa-solid fa-spa', 'fa-solid fa-skull']
  );
}

// =============================================================================
// VALIDATORS
// =============================================================================

/**
 * Validates save action
 *
 * @since 5.24.1
 *
 * @param int    $post_id    ID of the updated post.
 * @param string $post_type  Type of the updated post.
 *
 * @return bool True if valid, false if not.
 */

function fictioneer_validate_save_action_user( $post_id, $post_type ) {
  // Capability substring
  $substring = $post_type === 'fcn_story' ? 'fcn_stories' : "{$post_type}s";

  // Check post permission
  if ( ! current_user_can( "edit_{$substring}", $post_id ) ) {
    return false;
  }

  // Check post ownership
  if (
    absint( get_post_field( 'post_author', $post_id ) ) !== get_current_user_id() &&
    ! current_user_can( "edit_others_{$substring}" )
  ) {
    return false;
  }

  // Check post status
  if (
    get_post_status( $post_id ) === 'publish' &&
    ! current_user_can( "edit_published_{$substring}", $post_id )
  ) {
    return false;
  }

  // Valid
  return true;
}

// =============================================================================
// SANITIZERS
// =============================================================================

/**
 * Sanitize editor content.
 *
 * Removes malicious HTML, magic quote slashes, shortcodes, and blocks.
 *
 * @since 5.7.4
 *
 * @param string $content  The content to be sanitized.
 *
 * @return string The sanitized content.
 */

function fictioneer_sanitize_editor( $content ) {
  $content = wp_kses_post( $content );
  $content = strip_shortcodes( $content );
  $content = preg_replace( '/<!--\s*wp:(.*?)-->(.*?)<!--\s*\/wp:\1\s*-->/s', '', $content );

  return $content;
}

// =============================================================================
// META FIELD HELPERS
// =============================================================================

/**
 * Return HTML for hidden input to test whether magic quotes were applied.
 *
 * @since 5.28.0
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_magic_quote_test() {
  return '<input type="hidden" name="fictioneer_magic_quotes_test" value="' . "O'Reilly" . '">';
}

/**
 * Returns HTML for a checkbox meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param string  $label     The checkbox label.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type bool $required  Whether the field is required. Default false.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_checkbox( $post, $meta_key, $label, $args = [] ) {
  // Setup
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  ob_start();

  // Start HTML ---> ?>
  <label class="fictioneer-meta-checkbox" for="<?php echo $meta_key; ?>" <?php echo $data_required; ?>>

    <div class="fictioneer-meta-checkbox__checkbox">
      <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">
      <input type="checkbox" id="<?php echo $meta_key; ?>" name="<?php echo $meta_key; ?>" value="1" autocomplete="off" <?php checked( get_post_meta( $post->ID, $meta_key, true ), 1 ); ?> <?php echo $attributes; ?> <?php echo $required; ?>>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" focusable="false"><path d="M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"></path></svg>
    </div>

    <div class="fictioneer-meta-checkbox__label"><?php echo $label; ?></div>

  </label>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for a text input meta field
 *
 * @since 5.7.4
 * @since 5.15.0 - Added additional optional arguments.
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $placeholder  Placeholder text.
 *   @type string $type         Type of the input. Default 'text'.
 *   @type string $maxlength    Maxlength attribute.
 *   @type bool   $required     Whether the field is required. Default false.
 *   @type array  $attributes   Additional attributes.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_text( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $type = $args['type'] ?? 'text';
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $maxlength = isset( $args['maxlength'] ) ? 'maxlength="' . $args['maxlength'] . '"' : '';
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--text" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <input type="<?php echo $type; ?>" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $maxlength; ?> <?php echo $attributes; ?> <?php echo $required; ?>>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Return HTML for a input/select combo meta field.
 *
 * @since 5.27.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $placeholder  Placeholder text.
 *   @type string $type         Type of the input. Default 'text'.
 *   @type string $maxlength    Maxlength attribute.
 *   @type bool   $required     Whether the field is required. Default false.
 *   @type array  $attributes   Additional attributes.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_combo( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $type = $args['type'] ?? 'text';
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $maxlength = isset( $args['maxlength'] ) ? 'maxlength="' . $args['maxlength'] . '"' : '';
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--combo" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--combo">

      <select name="combo-select-<?php echo $meta_key ?>" class="fictioneer-meta-field__select fictioneer-meta-field__select--combo" data-meta-field-controller-target="combo">
        <option value="0"><?php _e( '— Select Group —', 'fictioneer' ); ?></option>
        <option value="-1"><?php _e( '— New Group —', 'fictioneer' ); ?></option>
      </select>

      <input type="<?php echo $type; ?>" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input fictioneer-meta-field__input--combo" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $maxlength; ?> <?php echo $attributes; ?> <?php echo $required; ?>>

    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Return HTML for a number input meta field.
 *
 * @since 5.15.0
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $placeholder  Placeholder text.
 *   @type string $maxlength    Maxlength attribute.
 *   @type bool   $required     Whether the field is required. Default false.
 *   @type array  $attributes   Additional attributes.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_number( $post, $meta_key, $args = [] ) {
  // Set type
  $args['type'] = 'number';

  // Call parent function
  return fictioneer_get_metabox_text( $post, $meta_key, $args );
}

/**
 * Returns HTML for a datetime-local input meta field
 *
 * @since 5.17.0
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type array  $attributes   Additional attributes.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_datetime( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  if ( $meta_value ) {
    $utc_datetime = new DateTime( $meta_value, new DateTimeZone( 'UTC' ) );
    $utc_datetime->setTimezone( wp_timezone() );

    $meta_value = $utc_datetime->format( 'Y-m-d\TH:i:s' );
  }

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--datetime">

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <input type="datetime-local" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" autocomplete="off" <?php echo $attributes; ?>>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for a URL meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $placeholder  Placeholder text.
 *   @type bool   $required     Whether the field is required. Default false.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_url( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--url" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <span class="fictioneer-meta-field__url-icon dashicons dashicons-admin-site"></span>
      <input type="url" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $attributes; ?> <?php echo $required; ?>>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for a comma-separated text meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $placeholder  Placeholder text.
 *   @type bool   $required     Whether the field is required. Default false.
 * }
 *
 * @return string The HTML markup for the field.
 */

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
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--array" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <input type="text" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $list; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $attributes; ?> <?php echo $required; ?>>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for a select meta field
 *
 * @since 5.7.4
 * @since 5.9.3 - Added 'query_var' argument.
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $options   Array of select options (ID => Name).
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type bool   $required     Whether the field is required. Default false.
 *   @type string $query_var    GET variable for current selection if empty.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_select( $post, $meta_key, $options, $args = [] ) {
  // Setup
  $selected = get_post_meta( $post->ID, $meta_key, true );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  // Current selection
  if ( empty( $selected ) ) {
    $query_var = sanitize_key( $_GET[ $args['query_var'] ?? '' ] ?? '' );

    if ( $query_var && array_key_exists( $query_var, $options ) ) {
      $selected = $query_var;
    } else {
      $selected = empty( $selected ) ? array_keys( $options )[0] : $selected;
    }
  }

  // Sort alphabetically
  if ( $args['asort'] ?? 0 ) {
    $none_item = isset( $options['0'] ) ? array( '0' => $options['0'] ) : [];
    unset( $options['0'] );
    asort( $options );
    $options = $none_item + $options;
  }

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--select" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <select id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__select" name="<?php echo $meta_key; ?>" autocomplete="off" <?php echo $attributes; ?> <?php echo $required; ?>><?php
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

/**
 * Returns HTML for a textarea meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $placeholder  Placeholder text.
 *   @type bool   $required     Whether the field is required. Default false.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_textarea( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = get_post_meta( $post->ID, $meta_key, true );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $input_classes = strval( $args['input_classes'] ?? '' );
  $attributes = implode( ' ', $args['attributes'] ?? [] );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--textarea" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <textarea id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__textarea <?php echo $input_classes; ?>" name="<?php echo $meta_key; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $attributes; ?> <?php echo $required; ?>><?php echo esc_textarea( $meta_value ); ?></textarea>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for a image meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $button       Caption on the button.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_image( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = get_post_meta( $post->ID, $meta_key, true );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $upload = strval( $args['button'] ?? _x( 'Set image', 'Metabox image upload button.', 'fictioneer' ) );
  $replace = _x( 'Replace', 'Metabox image upload button.', 'fictioneer' );
  $remove = _x( 'Remove', 'Metabox image remove button.', 'fictioneer' );
  $image_url = wp_get_attachment_url( $meta_value );
  $image_css = $image_url ? "style='background-image: url(\"{$image_url}\");'" : '';

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--image" data-target="fcn-meta-field-image">

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

/**
 * Returns HTML for an eBook meta field
 *
 * @since 5.8.0
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $button       Caption on the button.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_ebook( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = get_post_meta( $post->ID, $meta_key, true );
  $file_path = get_attached_file( $meta_value );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $button_upload = strval( $args['button'] ?? _x( 'Add eBook', 'Metabox ebook upload button.', 'fictioneer' ) );
  $button_replace = _x( 'Replace', 'Metabox replace button.', 'fictioneer' );
  $button_remove = _x( 'Remove', 'Metabox remove button.', 'fictioneer' );
  $title = null;
  $filename = null;
  $filesize = null;
  $url = null;
  $is_set = false;

  if ( file_exists( $file_path ) ) {
    $title = get_the_title( $meta_value );
    $filename = wp_basename( $file_path );
    $filesize = size_format( filesize( $file_path ) );
    $url = wp_get_attachment_url( $meta_value );
    $is_set = true;
  } else {
    $meta_value = null;
  }

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--ebook" data-target="fcn-meta-field-ebook">

    <?php if ( $label ) : ?>
      <div class="fictioneer-meta-field__label"><?php echo $label; ?></div>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="<?php echo esc_attr( $meta_value ); ?>" autocomplete="off" data-target="fcn-meta-field-ebook-id">

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--ebook">
      <div class="fictioneer-meta-field__ebook-display <?php echo $is_set ? '' : 'hidden'; ?>" data-target="fcn-meta-field-ebook-display">
        <div class="fictioneer-meta-field__ebook-thumbnail">
          <span class="dashicons dashicons-media-text"></span>
        </div>
        <div class="fictioneer-meta-field__ebook-data">
          <div class="fictioneer-meta-field__ebook-name" data-target="fcn-meta-field-ebook-name">
            <?php echo $title ?? ''; ?>
          </div>
          <div class="fictioneer-meta-field__ebook-filename">
            <strong><?php _ex( 'File:', 'Meta box file name label.', 'fictioneer' ); ?></strong>
            <a href="<?php echo $url ?? ''; ?>" rel="noreferrer noopener" data-target="fcn-meta-field-ebook-filename" target="_blank" download>
              <?php echo $filename ?? ''; ?>
            </a>
          </div>
          <div class="fictioneer-meta-field__ebook-size">
            <strong><?php _ex( 'Size:', 'Meta box file size label.', 'fictioneer' ); ?></strong>
            <span data-target="fcn-meta-field-ebook-size"><?php echo $filesize ?? ''; ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="fictioneer-meta-field__actions fictioneer-meta-field__actions--ebook">
      <button type="button" class="button <?php echo $is_set ? 'hidden' : ''; ?>" data-target="fcn-meta-field-ebook-upload">
        <?php echo $button_upload; ?>
      </button>
      <button type="button" class="button <?php echo $is_set ? '' : 'hidden'; ?>" data-target="fcn-meta-field-ebook-replace">
        <?php echo $button_replace; ?>
      </button>
      <button type="button" class="button <?php echo $is_set ? '' : 'hidden'; ?>" data-target="fcn-meta-field-ebook-remove">
        <?php echo $button_remove; ?>
      </button>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for a token array meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $names        Names for the values (instead of post titles).
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_tokens( $post, $meta_key, $options, $args = [] ) {
  // Setup
  $array = get_post_meta( $post->ID, $meta_key, true );
  $array = is_array( $array ) ? $array : [];
  $array = array_map( 'absint', $array ); // Array should already be safe anyway
  $titles = [];
  $list = esc_attr( implode( ', ', $array ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );

  // Query post titles if required
  if ( $array && ! ( $args['names'] ?? 0 ) ) {
    global $wpdb;

    $placeholders = implode( ',', array_fill( 0, count( $array ), '%d' ) );

    $sql =
      "SELECT p.ID, p.post_title
      FROM {$wpdb->posts} p
      WHERE p.ID IN ($placeholders)";

    $results = $wpdb->get_results( $wpdb->prepare( $sql, ...$array ), OBJECT_K );
    $titles = wp_list_pluck( $results, 'post_title', 'ID' );
  }

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
            if ( $args['names'] ?? 0 ) {
              $name = $args['names'][ $token ] ?? $token;
            } else {
              $name = ( $titles[ $token ] ?? 0 ) ? $titles[ $token ] : $token;
            }

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

/**
 * Returns HTML for an icon class meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 *   @type string $placeholder  Placeholder text.
 *   @type bool   $required     Whether the field is required. Default false.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_icons( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $meta_value = $meta_value ? $meta_value : FICTIONEER_DEFAULT_CHAPTER_ICON;
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $current_icon_class = fictioneer_get_icon_field( $meta_key, $post->ID );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--icons" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--icon">
      <i class="fictioneer-meta-field__fa-icon <?php echo $current_icon_class; ?>"></i>
      <input type="text" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input fictioneer-meta-field__input--icon" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

    <div class="fictioneer-meta-field__button-grid hidden">
      <?php
        foreach ( FICTIONEER_EXAMPLE_CHAPTER_ICONS as $icon ) {
          echo "<button type='button' class='fictioneer-meta-field__icon-button' data-value='{$icon}'><i class='{$icon}'></i></button>";
        }
      ?>
    </div>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for an editor meta field
 *
 * Note: As of WordPress 6.3.1, the TinyMCE editor does not properly load in
 * Firefox if the visual tab is preselected. It does work if you switch from
 * the text tab, therefore this is set as default for now.
 *
 * @since 5.7.4
 *
 * @param WP_Post $post      The post.
 * @param string  $meta_key  The meta key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_editor( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = get_post_meta( $post->ID, $meta_key, true );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';

  $settings = array(
    'wpautop' => true,
    'media_buttons' => false,
    'textarea_name' => $meta_key,
    'textarea_rows' => 10,
    'default_editor' => 'html',
    'tinymce' => array(
      'toolbar1' => 'bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link',
      'toolbar2' => '',
      'paste_as_text' => true
    ),
    'quicktags' => array(
      'buttons' => 'strong,em,link,block,img,ul,ol,li,close'
    )
  );

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--editor" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <div class="fictioneer-meta-field__label fictioneer-meta-field__label--editor" for="<?php echo $meta_key; ?>"><?php echo $label; ?></div>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--editor">
      <?php wp_editor( $meta_value, "{$meta_key}_editor", $settings ); ?>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Returns HTML for a relationship meta field
 *
 * @since 5.8.0
 *
 * @param WP_Post $post      The post.
 * @param array   $meta_key  The meta key.
 * @param array   $selected  Currently selected post relationships.
 * @param string  $callback  Function to build the selected items list.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string     $label        Label above the field.
 *   @type string     $description  Description below the field.
 *   @type string     $show_info    Whether to show the hover info field.
 *   @type bool|array $post_types   Arguments for the post type select. Set true
 *                                  to render a select field for all types.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_metabox_relationships( $post, $meta_key, $selected, $callback, $args = [] ) {
  // Setup
  $nonce = wp_create_nonce( "relationship_posts_{$meta_key}_{$post->ID}" );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );

  // Build and return HTML
  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--relationships" data-nonce="<?php echo $nonce; ?>" data-key="<?php echo $meta_key; ?>" data-post-id="<?php echo $post->ID; ?>" data-ajax-action="fictioneer_ajax_query_relationship_posts">

    <?php if ( $label ) : ?>
      <div class="fictioneer-meta-field__label"><?php echo $label; ?></div>
    <?php endif; ?>

    <div class="fictioneer-meta-field__top-actions">
      <button type="button" data-action="fcn-relationships-scroll" data-direction="-1"><span class="dashicons dashicons-arrow-up-alt"></span></button>
      <button type="button" data-action="fcn-relationships-scroll" data-direction="1"><span class="dashicons dashicons-arrow-down-alt"></span></button>
    </div>

    <input type="hidden" name="<?php echo $meta_key; ?>[]" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--relationships">
      <div class="fictioneer-meta-field__relationships">

        <div class="fictioneer-meta-field__relationships-filters">
          <div class="fictioneer-meta-field__relationships-search">
            <input type="text" placeholder="<?php _ex( 'Search…', 'Relationship meta field search placeholder.', 'fictioneer' ); ?>" maxlength="200" autocomplete="off" data-target="fcn-relationships-search">
          </div>
          <?php if ( isset( $args['post_types'] ) && ! empty( $args['post_types'] ) ) : ?>
            <div class="fictioneer-meta-field__relationships-selects">
              <?php
                fictioneer_relationship_post_type_select(
                  'fcn-relationships-post-type',
                  $args['post_types']
                );
              ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="fictioneer-meta-field__relationships-lists">
          <ol class="fictioneer-meta-field__relationships-source" data-target="fcn-relationships-source">
            <?php
              // Start HTML ---> ?>
              <li class="fictioneer-meta-field__relationships-source-observer disabled" data-target="fcn-relationships-observer" data-nonce="<?php echo $nonce; ?>" data-key="<?php echo $meta_key; ?>" data-post-id="<?php echo $post->ID; ?>" data-page="1" data-ajax-action="fictioneer_ajax_query_relationship_posts">
                <img src="<?php echo esc_url( admin_url() . 'images/spinner-2x.gif' ); ?>">
                <span><?php _ex( 'Loading', 'AJAX loading.', 'fictioneer' ); ?></span>
              </li>
              <?php // <--- End HTML
            ?>
          </ol>

          <ol class="fictioneer-meta-field__relationships-values" data-target="fcn-relationships-values">
            <?php
              if ( function_exists( $callback ) ) {
                call_user_func( $callback, $selected, $meta_key, $args );
              }
            ?>
          </ol>
        </div>

        <?php if ( $args['show_info'] ?? 0 ) : ?>
          <div class="fictioneer-meta-field__relationships-info" data-default="<?php esc_attr_e( 'Hover or tab chapter to see details.', 'fictioneer' ); ?>" data-target="fcn-relationships-info">
            <?php _e( 'Hover or tab chapter to see details.', 'fictioneer' ); ?>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <?php if ( $description ) : ?>
      <div class="fictioneer-meta-field__description"><?php echo $description; ?></div>
    <?php endif; ?>

    <template data-target="selected-item-template">
      <li class="fictioneer-meta-field__relationships-item fictioneer-meta-field__relationships-values-item" data-id>
        <input type="hidden" name="<?php echo $meta_key; ?>[]" value autocomplete="off">
        <span></span>
        <button type="button" data-action="remove"><span class="dashicons dashicons-no-alt"></span></button>
      </li>
    </template>

    <template data-target="spinner-template">
      <li class="fictioneer-meta-field__relationships-source-observer disabled" data-target="fcn-relationships-observer">
        <img src="<?php echo esc_url( admin_url() . 'images/spinner-2x.gif' ); ?>">
        <span><?php _ex( 'Loading', 'AJAX relationship field loading.', 'fictioneer' ); ?></span>
      </li>
    </template>

    <template data-target="loading-error-template">
      <li class="fictioneer-meta-field__relationships-item-error disabled">
        <span class="dashicons dashicons-warning"></span>
        <span class="error-message"></span>
      </li>
    </template>

  </div>
  <?php // <--- End HTML

  return ob_get_clean();
}

/**
 * Render HTML for relationship post type select
 *
 * @since 5.8.0
 *
 * @param string $name  The name for the data-target attribute.
 * @param array  $args {
 *   Optional. An array of additional arguments.
 *
 *   @type array $type_args  Additional type query arguments.
 *   @type array $excluded   Post types to exclude.
 * }
 */

function fictioneer_relationship_post_type_select( $name, $args = [] ) {
  // Setup
  $args = is_array( $args ) ? $args : [];
  $type_args = array_merge( array( 'public' => true ), $args['type_args'] ?? [] );
  $post_types = get_post_types( $type_args, 'objects' );

  // HTML
  echo '<div class="fictioneer-meta-field fictioneer-meta-field--select"><div class="fictioneer-meta-field__wrapper">';
  echo "<select data-target='{$name}' autocomplete='off'>";
  echo '<option value="" selected>' . __( 'Select post type', 'fictioneer' ) . '</option>';

  foreach ( $post_types as $post_type ) {
    // Excluded?
    if ( in_array( $post_type->name, $args['excluded'] ?? [] ) ) {
      continue;
    }

    $label = esc_html( $post_type->labels->singular_name );
    $value = esc_attr( $post_type->name );

    echo "<option value='{$value}'>{$label}</option>";
  }

  echo '</select></div></div>';
}

/**
 * AJAX: Delegate query and send posts for relationship fields
 *
 * @since 5.8.0
 */

function fictioneer_ajax_query_relationship_posts() {
  // Validate
  $action = sanitize_text_field( $_REQUEST['action'] ?? '' );
  $nonce = sanitize_text_field( $_REQUEST['nonce'] ?? '' );
  $meta_key = sanitize_text_field( $_REQUEST['key'] ?? '' );
  $post_id = absint( $_REQUEST['post_id'] ?? 0 );
  $user = wp_get_current_user();

  // Abort if...
  if ( empty( $action ) ) {
    wp_send_json_error( array( 'error' => 'Action parameter missing.' ) );
  }

  if ( empty( $nonce ) ) {
    wp_send_json_error( array( 'error' => 'Nonce parameter missing.' ) );
  }

  if ( empty( $meta_key ) ) {
    wp_send_json_error( array( 'error' => 'Key parameter missing.' ) );
  }

  if ( ! $user->exists() ) {
    wp_send_json_error( array( 'error' => 'Not logged in.' ) );
  }

  // Story chapter assignment?
  if ( check_ajax_referer( "relationship_posts_fictioneer_story_chapters_{$post_id}", 'nonce', false ) && $post_id > 0 ) {
    fictioneer_ajax_get_relationship_chapters( $post_id, $meta_key );
  }

  // Story page assignment?
  if ( check_ajax_referer( "relationship_posts_fictioneer_story_custom_pages_{$post_id}", 'nonce', false ) && $post_id > 0 ) {
    fictioneer_ajax_get_relationship_story_pages( $post_id, $meta_key );
  }

  // Collection item assignment?
  if ( check_ajax_referer( "relationship_posts_fictioneer_collection_items_{$post_id}", 'nonce', false ) && $post_id > 0 ) {
    fictioneer_ajax_get_relationship_collection( $post_id, $meta_key );
  }

  // Post featured item assignment?
  if ( check_ajax_referer( "relationship_posts_fictioneer_post_featured_{$post_id}", 'nonce', false ) && $post_id > 0 ) {
    fictioneer_ajax_get_relationship_featured( $post_id, $meta_key );
  }

  // Nothing worked...
  wp_send_json_error( array( 'error' => 'Invalid request.' ) );
}
add_action( 'wp_ajax_fictioneer_ajax_query_relationship_posts', 'fictioneer_ajax_query_relationship_posts' );

/**
 * Render HTML for selected story chapters
 *
 * @since 5.8.0
 * @since 5.26.0 - Use custom chapter objects.
 *
 * @param array  $selected  Currently selected chapters.
 * @param string $meta_key  The meta key.
 * @param array  $args      Optional. An array of additional arguments.
 */

function fictioneer_callback_relationship_chapters( $selected, $meta_key, $args = [] ) {
  // Build HTML
  foreach ( $selected as $chapter ) {
    $title = fictioneer_sanitize_safe_title(
      $chapter->post_title,
      get_date_from_gmt( $chapter->post_date_gmt, get_option( 'date_format' ) ),
      get_date_from_gmt( $chapter->post_date_gmt, get_option( 'time_format' ) )
    );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-values-item'];
    $label = fictioneer_get_post_status_label( $chapter->post_status );

    if ( $chapter->fictioneer_chapter_hidden ?? 0 ) {
      $title = "{$title} (" . _x( 'Unlisted', 'Chapter assignment flag.', 'fictioneer' ) . ")";
    }

    if ( $chapter->post_status !== 'publish' ) {
      $title = "{$title} ({$label})";
    }

    // Start HTML ---> ?>
    <li class="<?php echo implode( ' ', $classes ); ?>" data-id="<?php echo $chapter->ID; ?>" data-info="<?php echo esc_attr( esc_attr( fictioneer_get_relationship_chapter_details( $chapter ) ) ); ?>">
      <input type="hidden" name="<?php echo $meta_key; ?>[]" value="<?php echo $chapter->ID; ?>" autocomplete="off">
      <span><?php echo $title; ?></span>
      <button type="button" data-action="remove"><span class="dashicons dashicons-no-alt"></span></button>
    </li>
    <?php // <--- End HTML
  }
}

/**
 * AJAX: Query and send paginated chapters for story chapter assignments
 *
 * @since 5.8.0
 *
 * @param int    $post_id   The story post ID.
 * @param string $meta_key  The meta key.
 */

function fictioneer_ajax_get_relationship_chapters( $post_id, $meta_key ) {
  // Setup
  $post = get_post( $post_id );
  $user = wp_get_current_user();
  $page = absint( $_REQUEST['page'] ?? 1 );
  $search = sanitize_text_field( $_REQUEST['search'] ?? '' );

  // Validations
  if ( $post->post_type !== 'fcn_story' ) {
    wp_send_json_error( array( 'error' => 'Wrong post type.' ) );
  }

  if (
    ! current_user_can( 'edit_fcn_stories' ) ||
    (
      $user->ID != $post->post_author &&
      ! current_user_can( 'edit_others_fcn_stories' ) &&
      ! current_user_can( 'manage_options' )
    )
  ) {
    wp_send_json_error( array( 'error' => 'Insufficient permissions.' ) );
  }

  // Query
  $query_args = array(
    'post_type' => 'fcn_chapter',
    'post_status' => ['publish', 'private', 'future'],
    'orderby' => 'date',
    'order' => 'desc',
    'posts_per_page' => 10,
    'paged' => $page,
    's' => $search,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => false // Improve performance
  );

  if ( FICTIONEER_FILTER_STORY_CHAPTERS ) {
    $query_args['meta_key'] = 'fictioneer_chapter_story';
    $query_args['meta_value'] = $post_id;
  }

  $query = new WP_Query( $query_args );

  // Setup
  $nonce = wp_create_nonce( "relationship_posts_{$meta_key}_{$post_id}" );
  $output = [];

  // Build HTML for items
  foreach ( $query->posts as $chapter ) {
    // Chapter setup
    $title = fictioneer_get_safe_title( $chapter, 'admin-ajax-get-relationship-chapters' );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-source-item'];
    $label = fictioneer_get_post_status_label( $chapter->post_status );

    // Update title if necessary
    if ( get_post_meta( $chapter->ID, 'fictioneer_chapter_hidden', true ) ) {
      $title = "{$title} (" . _x( 'Unlisted', 'Chapter assignment flag.', 'fictioneer' ) . ")";
    }

    if ( $chapter->post_status !== 'publish' ) {
      $title = "{$title} ({$label})";
    }

    // Build and append item
    $item = "<li class='" . implode( ' ', $classes ) . "' data-action='add' data-id='{$chapter->ID}' ";
    $item .= "data-info='" . esc_attr( fictioneer_get_relationship_chapter_details( $chapter ) ) . "'><span>{$title}</span></li>";

    $output[] = $item;
  }

  // Build HTML for observer
  if ( $page < $query->max_num_pages ) {
    $page++;

    $observer = "<li class='fictioneer-meta-field__relationships-source-observer disabled' ";
    $observer .= "data-target='fcn-relationships-observer' data-nonce='{$nonce}' ";
    $observer .= "data-key='{$meta_key}' data-post-id='{$post_id}' data-page='{$page}' ";
    $observer .= 'data-ajax-action="fictioneer_ajax_query_relationship_posts">';
    $observer .= '<img src="' . esc_url( admin_url() . 'images/spinner-2x.gif' ) . '">';
    $observer .= '<span>' . _x( 'Loading', 'AJAX relationship posts loading.', 'fictioneer' ) . '</span></li>';

    $output[] = $observer;
  }

  // No results
  if ( empty( $output ) ) {
    $no_matches = "<li class='fictioneer-meta-field__relationships-no-matches disabled'><span>";
    $no_matches .= _x( 'No matches found.', 'AJAX relationship posts loading.', 'fictioneer' );
    $no_matches .= '</span></li>';

    $output[] = $no_matches;
  }

  // Response
  wp_send_json_success(
    array(
      'html' => implode( '', $output )
    )
  );
}

/**
 * Return HTML story chapter info
 *
 * @since 5.8.0
 *
 * @param WP_Post|object $chapter  The chapter post or similar object.
 *
 * @return string HTML for the chapter info.
 */

function fictioneer_get_relationship_chapter_details( $chapter ) {
  // Setup
  $text_icon = $chapter->fictioneer_chapter_text_icon ?? '';
  $icon = fictioneer_get_icon_field( 'fictioneer_chapter_icon', null, $chapter->fictioneer_chapter_icon ?? '' );
  $rating = $chapter->fictioneer_chapter_rating ?? '';
  $warning = $chapter->fictioneer_chapter_warning ?? '';
  $group = $chapter->fictioneer_chapter_group ?? '';
  $date_local = get_date_from_gmt( $chapter->post_date_gmt, get_option( 'date_format' ) );
  $time_local = get_date_from_gmt( $chapter->post_date_gmt, get_option( 'time_format' ) );
  $flags = [];
  $info = [];

  // Build
  $info[] = empty( $text_icon ) ? sprintf( '<i class="%s"></i>', $icon ) : "<strong>{$text_icon}</strong>";

  if ( $chapter->fictioneer_chapter_hidden ?? 0 ) {
    $flags[] = _x( 'Unlisted', 'Chapter assignment flag.', 'fictioneer' );
  }

  if ( $chapter->fictioneer_chapter_no_chapter ?? 0 ) {
    $flags[] = _x( 'No Chapter', 'Chapter assignment flag.', 'fictioneer' );
  }

  $info[] = sprintf(
    _x( '<strong>Status:</strong>&nbsp;%s', 'Chapter assignment info.', 'fictioneer' ),
    fictioneer_get_post_status_label( $chapter->post_status )
  );

  $info[] = sprintf(
    _x( '<strong>Date:</strong>&nbsp;%1$s at %2$s', 'Chapter assignment info.', 'fictioneer' ),
    $date_local,
    $time_local
  );

  if ( ! empty( $group ) ) {
    $info[] = sprintf(
      _x( '<strong>Group:</strong>&nbsp;%s', 'Chapter assignment info.', 'fictioneer' ),
      $group
    );
  }

  if ( ! empty( $rating ) ) {
    $info[] = sprintf(
      _x( '<strong>Age&nbsp;Rating:</strong>&nbsp;%s', 'Chapter assignment info.', 'fictioneer' ),
      $rating
    );
  }

  if ( ! empty( $flags ) ) {
    $info[] = sprintf(
      _x( '<strong>Flags:</strong>&nbsp;%s', 'Chapter assignment info.', 'fictioneer' ),
      implode( ', ', $flags )
    );
  }

  if ( ! empty( $warning ) ) {
    $info[] = sprintf(
      _x( '<strong>Warning:</strong>&nbsp;%s', 'Chapter assignment info.', 'fictioneer' ),
      $warning
    );
  }

  // Implode and return
  return implode( ' &bull; ', $info );
}

/**
 * Render HTML for selected story pages
 *
 * @since 5.8.0
 *
 * @param array  $selected  Currently selected pages.
 * @param string $meta_key  The meta key.
 * @param array  $args      Optional. An array of additional arguments.
 */

function fictioneer_callback_relationship_story_pages( $selected, $meta_key, $args = [] ) {
  // Build HTML
  foreach ( $selected as $page ) {
    $title = fictioneer_get_safe_title( $page, 'admin-callback-relationship-story-pages' );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-values-item'];

    // Start HTML ---> ?>
    <li class="<?php echo implode( ' ', $classes ); ?>" data-id="<?php echo $page->ID; ?>">
      <input type="hidden" name="<?php echo $meta_key; ?>[]" value="<?php echo $page->ID; ?>" autocomplete="off">
      <span><?php echo $title; ?></span>
      <button type="button" data-action="remove"><span class="dashicons dashicons-no-alt"></span></button>
    </li>
    <?php // <--- End HTML
  }
}

/**
 * AJAX: Query and send paginated pages for story custom pages
 *
 * @since 5.8.0
 *
 * @param int    $post_id   The story post ID.
 * @param string $meta_key  The meta key.
 */

function fictioneer_ajax_get_relationship_story_pages( $post_id, $meta_key ) {
  // Setup
  $post = get_post( $post_id );
  $user = wp_get_current_user();
  $page = absint( $_REQUEST['page'] ?? 1 );
  $search = sanitize_text_field( $_REQUEST['search'] ?? '' );

  // Validations
  if ( $post->post_type !== 'fcn_story' ) {
    wp_send_json_error( array( 'error' => 'Wrong post type.' ) );
  }

  if (
    ! current_user_can( 'edit_fcn_stories' ) ||
    (
      $user->ID != $post->post_author &&
      ! current_user_can( 'edit_others_fcn_stories' ) &&
      ! current_user_can( 'manage_options' )
    )
  ) {
    wp_send_json_error( array( 'error' => 'Insufficient permissions.' ) );
  }

  $forbidden = array_unique(
    array(
      get_option( 'fictioneer_user_profile_page', 0 ),
      get_option( 'fictioneer_bookmarks_page', 0 ),
      get_option( 'fictioneer_stories_page', 0 ),
      get_option( 'fictioneer_chapters_page', 0 ),
      get_option( 'fictioneer_recommendations_page', 0 ),
      get_option( 'fictioneer_collections_page', 0 ),
      get_option( 'fictioneer_bookshelf_page', 0 ),
      get_option( 'fictioneer_404_page', 0 ),
      get_option( 'page_on_front', 0 ),
      get_option( 'page_for_posts', 0 )
    )
  );

  // Query
  $query = new WP_Query(
    array(
      'post_type' => 'page',
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'desc',
      'author' => get_post_field( 'post_author', $post_id ),
      'post__not_in' => array_map( 'strval', $forbidden ),
      'posts_per_page' => 10,
      'paged' => $page,
      's' => $search,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false // Improve performance
    )
  );

  // Setup
  $nonce = wp_create_nonce( "relationship_posts_{$meta_key}_{$post_id}" );
  $output = [];

  // Build HTML for items
  foreach ( $query->posts as $item ) {
    // Chapter setup
    $title = fictioneer_get_safe_title( $item, 'admin-ajax-get-relationship-story-pages' );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-source-item'];

    // Build and append item
    $item = "<li class='" . implode( ' ', $classes ) . "' data-action='add' data-id='{$item->ID}'>";
    $item .= "<span>{$title}</span></li>";

    $output[] = $item;
  }

  // Build HTML for observer
  if ( $page < $query->max_num_pages ) {
    $page++;

    $observer = "<li class='fictioneer-meta-field__relationships-source-observer disabled' ";
    $observer .= "data-target='fcn-relationships-observer' data-nonce='{$nonce}' ";
    $observer .= "data-key='{$meta_key}' data-post-id='{$post_id}' data-page='{$page}' ";
    $observer .= 'data-ajax-action="fictioneer_ajax_query_relationship_posts">';
    $observer .= '<img src="' . esc_url( admin_url() . 'images/spinner-2x.gif' ) . '">';
    $observer .= '<span>' . _x( 'Loading', 'AJAX relationship posts loading.', 'fictioneer' ) . '</span></li>';

    $output[] = $observer;
  }

  // No results
  if ( empty( $output ) ) {
    $no_matches = "<li class='fictioneer-meta-field__relationships-no-matches disabled'><span>";
    $no_matches .= _x( 'No matches found.', 'AJAX relationship posts loading.', 'fictioneer' );
    $no_matches .= '</span></li>';

    $output[] = $no_matches;
  }

  // Response
  wp_send_json_success(
    array(
      'html' => implode( '', $output )
    )
  );
}

/**
 * Render HTML for selected collection items
 *
 * @since 5.8.0
 *
 * @param array  $selected  Currently selected items.
 * @param string $meta_key  The meta key.
 * @param array  $args      Optional. An array of additional arguments.
 */

function fictioneer_callback_relationship_collection( $selected, $meta_key, $args = [] ) {
  foreach ( $selected as $item ) {
    $title = fictioneer_get_safe_title( $item, 'admin-callback-relationship-collection' );
    $label = esc_html( fictioneer_get_post_type_label( $item->post_type ) );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-values-item'];

    // Start HTML ---> ?>
    <li class="<?php echo implode( ' ', $classes ); ?>" data-id="<?php echo $item->ID; ?>">
      <input type="hidden" name="<?php echo $meta_key; ?>[]" value="<?php echo $item->ID; ?>" autocomplete="off">
      <span>
        <strong class="fictioneer-meta-field__relationships-item-label"><?php echo $label; ?></strong>
        <?php echo $title; ?>
      </span>
      <button type="button" data-action="remove"><span class="dashicons dashicons-no-alt"></span></button>
    </li>
    <?php // <--- End HTML
  }
}

/**
 * AJAX: Query and send paginated items for collection
 *
 * @since 5.8.0
 *
 * @param int    $post_id   The story post ID.
 * @param string $meta_key  The meta key.
 */

function fictioneer_ajax_get_relationship_collection( $post_id, $meta_key ) {
  // Setup
  $post = get_post( $post_id );
  $user = wp_get_current_user();
  $page = absint( $_REQUEST['page'] ?? 1 );
  $search = sanitize_text_field( $_REQUEST['search'] ?? '' );
  $allowed_post_types = ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'];
  $post_type = sanitize_text_field( $_REQUEST['post_type'] ?? '' );
  $post_type = in_array( $post_type, $allowed_post_types ) ? $post_type : null;

  $forbidden = array_unique(
    array(
      get_option( 'fictioneer_user_profile_page', 0 ),
      get_option( 'fictioneer_bookmarks_page', 0 ),
      get_option( 'fictioneer_stories_page', 0 ),
      get_option( 'fictioneer_chapters_page', 0 ),
      get_option( 'fictioneer_recommendations_page', 0 ),
      get_option( 'fictioneer_collections_page', 0 ),
      get_option( 'fictioneer_bookshelf_page', 0 ),
      get_option( 'fictioneer_404_page', 0 ),
      get_option( 'page_on_front', 0 ),
      get_option( 'page_for_posts', 0 )
    )
  );

  // Validations
  if ( $post->post_type !== 'fcn_collection' ) {
    wp_send_json_error( array( 'error' => 'Wrong post type.' ) );
  }

  if (
    ! current_user_can( 'edit_fcn_collections' ) ||
    (
      $user->ID != $post->post_author &&
      ! current_user_can( 'edit_others_fcn_collections' ) &&
      ! current_user_can( 'manage_options' )
    )
  ) {
    wp_send_json_error( array( 'error' => 'Insufficient permissions.' ) );
  }

  // Query
  $query = new WP_Query(
    array(
      'post_type' => $post_type ?: $allowed_post_types,
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'desc',
      'post__not_in' => array_map( 'strval', $forbidden ),
      'posts_per_page' => 10,
      'paged' => $page,
      's' => $search,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false // Improve performance
    )
  );

  // Setup
  $nonce = wp_create_nonce( "relationship_posts_{$meta_key}_{$post_id}" );
  $output = [];

  // Build HTML for items
  foreach ( $query->posts as $item ) {
    // Chapter setup
    $title = fictioneer_get_safe_title( $item, 'admin-ajax-get-relationship-collection' );
    $label = esc_html( fictioneer_get_post_type_label( $item->post_type ) );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-source-item'];

    // Build and append item
    $item = "<li class='" . implode( ' ', $classes ) . "' data-action='add' data-id='{$item->ID}'>";
    $item .= "<span><strong class='fictioneer-meta-field__relationships-item-label'>{$label}</strong> {$title}</span></li>";

    $output[] = $item;
  }

  // Build HTML for observer
  if ( $page < $query->max_num_pages ) {
    $page++;

    $observer = "<li class='fictioneer-meta-field__relationships-source-observer disabled' ";
    $observer .= "data-target='fcn-relationships-observer' data-nonce='{$nonce}' ";
    $observer .= "data-key='{$meta_key}' data-post-id='{$post_id}' data-page='{$page}' ";
    $observer .= 'data-ajax-action="fictioneer_ajax_query_relationship_posts">';
    $observer .= '<img src="' . esc_url( admin_url() . 'images/spinner-2x.gif' ) . '">';
    $observer .= '<span>' . _x( 'Loading', 'AJAX relationship posts loading.', 'fictioneer' ) . '</span></li>';

    $output[] = $observer;
  }

  // No results
  if ( empty( $output ) ) {
    $no_matches = "<li class='fictioneer-meta-field__relationships-no-matches disabled'><span>";
    $no_matches .= _x( 'No matches found.', 'AJAX relationship posts loading.', 'fictioneer' );
    $no_matches .= '</span></li>';

    $output[] = $no_matches;
  }

  // Response
  wp_send_json_success(
    array(
      'html' => implode( '', $output )
    )
  );
}

/**
 * Render HTML for selected featured items
 *
 * @since 5.8.0
 *
 * @param array  $selected  Currently selected items.
 * @param string $meta_key  The meta key.
 * @param array  $args      Optional. An array of additional arguments.
 */

function fictioneer_callback_relationship_featured( $selected, $meta_key, $args = [] ) {
  foreach ( $selected as $item ) {
    $title = fictioneer_get_safe_title( $item, 'admin-callback-relationship-featured' );
    $label = esc_html( fictioneer_get_post_type_label( $item->post_type ) );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-values-item'];

    // Start HTML ---> ?>
    <li class="<?php echo implode( ' ', $classes ); ?>" data-id="<?php echo $item->ID; ?>">
      <input type="hidden" name="<?php echo $meta_key; ?>[]" value="<?php echo $item->ID; ?>" autocomplete="off">
      <span>
        <strong class="fictioneer-meta-field__relationships-item-label"><?php echo $label; ?></strong>
        <?php echo $title; ?>
      </span>
      <button type="button" data-action="remove"><span class="dashicons dashicons-no-alt"></span></button>
    </li>
    <?php // <--- End HTML
  }
}

/**
 * AJAX: Query and send paginated items for featured posts
 *
 * @since 5.8.0
 *
 * @param int    $post_id   The story post ID.
 * @param string $meta_key  The meta key.
 */

function fictioneer_ajax_get_relationship_featured( $post_id, $meta_key ) {
  // Setup
  $post = get_post( $post_id );
  $user = wp_get_current_user();
  $page = absint( $_REQUEST['page'] ?? 1 );
  $search = sanitize_text_field( $_REQUEST['search'] ?? '' );
  $allowed_post_types = ['post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'];
  $post_type = sanitize_text_field( $_REQUEST['post_type'] ?? '' );
  $post_type = in_array( $post_type, $allowed_post_types ) ? $post_type : null;

  // Validations
  if ( $post->post_type !== 'post' ) {
    wp_send_json_error( array( 'error' => 'Wrong post type.' ) );
  }

  if (
    ! current_user_can( 'edit_posts' ) ||
    (
      $user->ID != $post->post_author &&
      ! current_user_can( 'edit_others_posts' ) &&
      ! current_user_can( 'manage_options' )
    )
  ) {
    wp_send_json_error( array( 'error' => 'Insufficient permissions.' ) );
  }

  // Query
  $query = new WP_Query(
    array(
      'post_type' => $post_type ?: $allowed_post_types,
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'desc',
      'posts_per_page' => 10,
      'paged' => $page,
      's' => $search,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false // Improve performance
    )
  );

  // Setup
  $nonce = wp_create_nonce( "relationship_posts_{$meta_key}_{$post_id}" );
  $output = [];

  // Build HTML for items
  foreach ( $query->posts as $item ) {
    // Chapter setup
    $title = fictioneer_get_safe_title( $item, 'admin-ajax-get-relationship-featured' );
    $label = esc_html( fictioneer_get_post_type_label( $item->post_type ) );
    $classes = ['fictioneer-meta-field__relationships-item', 'fictioneer-meta-field__relationships-source-item'];

    // Build and append item
    $item = "<li class='" . implode( ' ', $classes ) . "' data-action='add' data-id='{$item->ID}'>";
    $item .= "<span><strong class='fictioneer-meta-field__relationships-item-label'>{$label}</strong> {$title}</span></li>";

    $output[] = $item;
  }

  // Build HTML for observer
  if ( $page < $query->max_num_pages ) {
    $page++;

    $observer = "<li class='fictioneer-meta-field__relationships-source-observer disabled' ";
    $observer .= "data-target='fcn-relationships-observer' data-nonce='{$nonce}' ";
    $observer .= "data-key='{$meta_key}' data-post-id='{$post_id}' data-page='{$page}' ";
    $observer .= 'data-ajax-action="fictioneer_ajax_query_relationship_posts">';
    $observer .= '<img src="' . esc_url( admin_url() . 'images/spinner-2x.gif' ) . '">';
    $observer .= '<span>' . _x( 'Loading', 'AJAX relationship posts loading.', 'fictioneer' ) . '</span></li>';

    $output[] = $observer;
  }

  // No results
  if ( empty( $output ) ) {
    $no_matches = "<li class='fictioneer-meta-field__relationships-no-matches disabled'><span>";
    $no_matches .= _x( 'No matches found.', 'AJAX relationship posts loading.', 'fictioneer' );
    $no_matches .= '</span></li>';

    $output[] = $no_matches;
  }

  // Response
  wp_send_json_success(
    array(
      'html' => implode( '', $output )
    )
  );
}

// =============================================================================
// METABOX CLASSES
// =============================================================================

/**
 * Append classes to the metabox
 *
 * @since 5.7.4
 *
 * @param array $classes  An array of postbox classes.
 *
 * @return array The modified array of postbox classes.
 */

function fictioneer_append_metabox_classes( $classes ) {
  global $post;

  // Add class
  $classes[] = 'fictioneer-side-metabox';

  if ( ! use_block_editor_for_post_type( $post->post_type ) ) {
    $classes[] = 'fictioneer-side-metabox--classic';
  }

  // Return with added class
  return $classes;
}
add_filter( 'postbox_classes_fcn_story_fictioneer-story-meta', 'fictioneer_append_metabox_classes' );
add_filter( 'postbox_classes_fcn_story_fictioneer-story-data', 'fictioneer_append_metabox_classes' );
add_filter( 'postbox_classes_fcn_story_fictioneer-story-epub', 'fictioneer_append_metabox_classes' );
add_filter( 'postbox_classes_fcn_chapter_fictioneer-chapter-meta', 'fictioneer_append_metabox_classes' );
add_filter( 'postbox_classes_fcn_chapter_fictioneer-chapter-data', 'fictioneer_append_metabox_classes' );
add_filter( 'postbox_classes_post_fictioneer-featured-content', 'fictioneer_append_metabox_classes' );
add_filter( 'postbox_classes_fcn_collection_fictioneer-collection-data', 'fictioneer_append_metabox_classes' );
add_filter( 'postbox_classes_fcn_recommendation_fictioneer-recommendation-data', 'fictioneer_append_metabox_classes' );

foreach ( ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'] as $type ) {
  add_filter( "postbox_classes_{$type}_fictioneer-extra", 'fictioneer_append_metabox_classes' );
}

foreach ( ['post', 'fcn_story', 'fcn_chapter'] as $type ) {
  add_filter( "postbox_classes_{$type}_fictioneer-support-links", 'fictioneer_append_metabox_classes' );
}

// =============================================================================
// STORY META FIELDS
// =============================================================================

/**
 * Adds story meta metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_story_meta_metabox() {
  add_meta_box(
    'fictioneer-story-meta',
    __( 'Story Meta', 'fictioneer' ),
    'fictioneer_render_story_meta_metabox',
    ['fcn_story'],
    'side',
    'default'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_story_meta_metabox' );

/**
 * Render story meta metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_story_meta_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "story_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $advanced = get_option( 'fictioneer_enable_advanced_meta_fields' );
  $output = [];

  // --- Add fields ------------------------------------------------------------

  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

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
      'Everyone' => fcntr( 'Everyone' ),
      'Teen' => fcntr( 'Teen' ),
      'Mature' => fcntr( 'Mature' ),
      'Adult' => fcntr( 'Adult' )
    ),
    array(
      'label' => _x( 'Age Rating', 'Story age rating meta field label.', 'fictioneer' ),
      'description' => __( 'Select an overall age rating.', 'fictioneer' ),
      'required' => 1
    )
  );

  if ( $advanced ) {
    $output['fictioneer_story_co_authors'] = fictioneer_get_metabox_array(
      $post,
      'fictioneer_story_co_authors',
      array(
        'label' => _x( 'Co-Authors', 'Story co-authors meta field label.', 'fictioneer' ),
        'description' => __( 'Comma-separated list of author IDs.', 'fictioneer' )
      )
    );
  }

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
      'description' => __( 'URL to Top Web Fiction page.', 'fictioneer' ),
      'placeholder' => 'https://...'
    )
  );

  $output['flags_heading'] = '<div class="fictioneer-meta-field-heading">' .
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
    __( 'Hide taxonomies on story page', 'fictioneer' )
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
        'placeholder' => __( '.selector { ... }', 'fictioneer' ),
        'input_classes' => 'fictioneer-meta-field__textarea--code'
      )
    );
  }

  if ( $advanced && current_user_can( 'manage_options' ) ) {
    $output['fictioneer_story_redirect_link'] = fictioneer_get_metabox_url(
      $post,
      'fictioneer_story_redirect_link',
      array(
        'label' => _x( 'Redirect Link (Beware!)', 'Story redirect link meta field label.', 'fictioneer' ),
        'description' => __( 'URL to redirect to when story is opened.', 'fictioneer' ),
        'placeholder' => 'https://...'
      )
    );
  }

  // --- Filters ---------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_story_meta', $output, $post );

  // --- Render ----------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_story_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Adds story data metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_story_data_metabox() {
  add_meta_box(
    'fictioneer-story-data',
    __( 'Story Data', 'fictioneer' ),
    'fictioneer_render_story_data_metabox',
    ['fcn_story'],
    'normal',
    'default'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_story_data_metabox' );

/**
 * Render story data metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_story_data_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "story_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields ------------------------------------------------------------

  // Magic quote test
  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  // Short description
  $output['fictioneer_story_short_description'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_story_short_description',
    array(
      'label' => _x( 'Short Description', 'Story short description meta field label.', 'fictioneer' ),
      'description' => __( 'The <em>first paragraph</em> is used on cards, so keep it nice and concise. The full short description is passed to the Storygraph API.', 'fictioneer' ),
      'required' => 1
    )
  );

  // Chapters
  $description = get_option( 'fictioneer_limit_chapter_stories_by_author' ) ?
    __( 'Select and order chapters assigned to the story (set in the chapter). The author (or co-author) of the chapter and the story must match.', 'fictioneer' ) :
    __( 'Select and order chapters assigned to the story (set in the chapter).', 'fictioneer' );

  $output['fictioneer_story_chapters'] = fictioneer_get_metabox_relationships(
    $post,
    'fictioneer_story_chapters',
    fictioneer_sql_get_story_chapter_relationship_data( $post->ID ),
    'fictioneer_callback_relationship_chapters',
    array(
      'label' => _x( 'Chapters', 'Story chapters meta field label.', 'fictioneer' ),
      'description' => $description,
      'show_info' => 1
    )
  );

  // Custom pages
  if ( current_user_can( 'fcn_story_pages', $post->ID ) && FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY > 0 ) {
    $page_ids = get_post_meta( $post->ID, 'fictioneer_story_custom_pages', true ) ?: [];
    $page_ids = is_array( $page_ids ) ? $page_ids : [];
    $pages = empty( $page_ids ) ? [] : get_posts(
      array(
        'post_type' => 'page',
        'post_status' => 'any',
        'post__in' => $page_ids ?: [0], // Must not be empty!
        'orderby' => 'post__in',
        'posts_per_page' => -1,
        'author' => get_post_field( 'post_author', $post->ID ),
        'update_post_meta_cache' => false, // Improve performance
        'update_post_term_cache' => false, // Improve performance
        'no_found_rows' => true // Improve performance
      )
    );

    $output['fictioneer_story_custom_pages'] = fictioneer_get_metabox_relationships(
      $post,
      'fictioneer_story_custom_pages',
      $pages,
      'fictioneer_callback_relationship_story_pages',
      array(
        'label' => _x( 'Custom Pages', 'Story pages meta field label.', 'fictioneer' ),
        'description' => sprintf(
          __( 'Add up to %s pages as tabs to stories. Pages must have a short name and the same author as the story; otherwise, they will not show up.', 'fictioneer' ),
          FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY
        )
      )
    );
  }

  // Global note
  $output['fictioneer_story_global_note'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_story_global_note',
    array(
      'label' => _x( 'Global Note', 'Story global note meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed in a box above all chapters; start with "[!password]" to hide in protected chapters. Limited HTML allowed.', 'fictioneer' )
    )
  );

  // Password note
  $output['fictioneer_story_password_note'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_story_password_note',
    array(
      'label' => _x( 'Password Note', 'Story password note meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed for password protected content; start with "[!global]" to show on all protected chapters without note. Limited HTML allowed.', 'fictioneer' )
    )
  );

  // --- Filters ---------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_story_data', $output, $post );

  // --- Render ----------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_story_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Adds story epub metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_story_epub_metabox() {
  add_meta_box(
    'fictioneer-story-epub',
    __( 'ePUB Setup', 'fictioneer' ),
    'fictioneer_render_story_epub_metabox',
    ['fcn_story'],
    'normal',
    'default'
  );
}

if ( get_option( 'fictioneer_enable_epubs' ) ) {
  add_action( 'add_meta_boxes', 'fictioneer_add_story_epub_metabox' );
}

/**
 * Render story epub metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_story_epub_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "story_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields ------------------------------------------------------------

  // Magic quote test
  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  // Custom upload
  if ( current_user_can( 'fcn_custom_epub_upload', $post->ID ) ) {
    $output['fictioneer_story_ebook_upload_one'] = fictioneer_get_metabox_ebook(
      $post,
      'fictioneer_story_ebook_upload_one',
      array(
        'label' => _x( 'Custom Upload', 'Story ebook upload meta field label.', 'fictioneer' ),
        'description' => __( 'If you do not want to rely on the ePUB converter, you can upload your own ebook. Allowed formats are <strong>epub</strong>, <strong>mobi</strong>, <strong>pdf</strong>, <strong>rtf</strong>, and <strong>txt</strong>.', 'fictioneer' )
      )
    );
  }

  // Preface
  $output['fictioneer_story_epub_preface'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_story_epub_preface',
    array(
      'label' => _x( 'ePUB Preface', 'Story ePUB preface meta field label.', 'fictioneer' ),
      'description' => __( 'Required for the download. Inserted as separate page between cover and table of contents. Disclaimers, copyright notes, tags, and other front matter goes here.', 'fictioneer' )
    )
  );

  // Afterword
  $output['fictioneer_story_epub_afterword'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_story_epub_afterword',
    array(
      'label' => _x( 'ePUB Afterword', 'Story ePUB afterword meta field label.', 'fictioneer' ),
      'description' => __( 'Inserted after the last chapter. Thanks and personal thoughts on the story go here, for example.', 'fictioneer' )
    )
  );

  // CSS
  if ( current_user_can( 'fcn_custom_epub_css', $post->ID ) ) {
    $output['fictioneer_story_epub_custom_css'] = fictioneer_get_metabox_textarea(
      $post,
      'fictioneer_story_epub_custom_css',
      array(
        'label' => _x( 'ePUB CSS', 'Story ePUB CSS meta field label.', 'fictioneer' ),
        'description' => __( 'Inject CSS into the ePUB to customize the style for your story. Dangerous.', 'fictioneer' ),
        'input_classes' => 'fictioneer-meta-field__textarea--code',
        'placeholder' => '.selector { ... }'
      )
    );
  }

  // --- Filters ---------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_story_epub', $output, $post );

  // --- Render ----------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_story_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save story metaboxes
 *
 * @since 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_story_metaboxes( $post_id ) {
  // --- Verify ----------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_story_nonce'] ?? '' ), "story_meta_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_story' ||
    ! fictioneer_validate_save_action_user( $post_id, 'fcn_story' )
  ) {
    return;
  }

  // --- Sanitize and add data -------------------------------------------------

  $post_author_id = get_post_field( 'post_author', $post_id );

  // Status
  if ( isset( $_POST['fictioneer_story_status'] ) ) {
    $allowed_statuses = ['Ongoing', 'Completed', 'Oneshot', 'Hiatus', 'Canceled'];
    $status = fictioneer_sanitize_selection( $_POST['fictioneer_story_status'], $allowed_statuses, $allowed_statuses[0] );
    $fields['fictioneer_story_status'] = $status;
  }

  // Rating
  if ( isset( $_POST['fictioneer_story_rating'] ) ) {
    $allowed_ratings = ['Everyone', 'Teen', 'Mature', 'Adult'];
    $rating = fictioneer_sanitize_selection( $_POST['fictioneer_story_rating'], $allowed_ratings, $allowed_ratings[0] );
    $fields['fictioneer_story_rating'] = $rating;
  }

  // Copyright notice
  if ( isset( $_POST['fictioneer_story_copyright_notice'] ) ) {
    $fields['fictioneer_story_copyright_notice'] = sanitize_text_field( $_POST['fictioneer_story_copyright_notice'] );
  }

  // Top Web Fiction
  if ( isset( $_POST['fictioneer_story_topwebfiction_link'] ) ) {
    $twf_url = fictioneer_sanitize_url( $_POST['fictioneer_story_topwebfiction_link'], 'https://topwebfiction.com/' );
    $fields['fictioneer_story_topwebfiction_link'] = $twf_url;
  }

  // Redirect
  if ( current_user_can( 'manage_options' ) && isset( $_POST['fictioneer_story_redirect_link'] ) ) {
    $redirect_url = fictioneer_sanitize_url( $_POST['fictioneer_story_redirect_link'] );
    $fields['fictioneer_story_redirect_link'] = $redirect_url;
  }

  // Co-Authors
  if ( isset( $_POST['fictioneer_story_co_authors'] ) && get_option( 'fictioneer_enable_advanced_meta_fields' ) ){
    $co_authors = fictioneer_explode_list( $_POST['fictioneer_story_co_authors'] );
    $co_authors = array_map( 'absint', $co_authors );
    $co_authors = array_filter( $co_authors, function( $user_id ) {
      return get_userdata( $user_id ) !== false;
    });
    $co_authors = array_unique( $co_authors );

    $fields['fictioneer_story_co_authors'] = array_map( 'strval', $co_authors );
  }

  // Sticky flag
  if (
    isset( $_POST['fictioneer_story_sticky'] ) &&
    current_user_can( 'fcn_make_sticky', $post_id ) &&
    FICTIONEER_ENABLE_STICKY_CARDS
  ) {
    $fields['fictioneer_story_sticky'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_story_sticky'] );
  }

  // Hidden flag
  if ( isset( $_POST['fictioneer_story_hidden'] ) ) {
    $fields['fictioneer_story_hidden'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_story_hidden'] );
  }

  // Thumbnail flag
  if ( isset( $_POST['fictioneer_story_no_thumbnail'] ) ) {
    $fields['fictioneer_story_no_thumbnail'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_story_no_thumbnail'] );
  }

  // Tags flag
  if ( isset( $_POST['fictioneer_story_no_tags'] ) ) {
    $fields['fictioneer_story_no_tags'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_story_no_tags'] );
  }

  // ePUBs flag
  if ( isset( $_POST['fictioneer_story_no_epub'] ) && get_option( 'fictioneer_enable_epubs' ) ) {
    $fields['fictioneer_story_no_epub'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_story_no_epub'] );
  }

  // Chapter collapsing flag
  if ( isset( $_POST['fictioneer_story_disable_collapse'] ) && ! get_option( 'fictioneer_disable_chapter_collapsing' ) ) {
    $fields['fictioneer_story_disable_collapse'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_story_disable_collapse'] );
  }

  // Hide icons flag
  if ( isset( $_POST['fictioneer_story_hide_chapter_icons'] ) && ! get_option( 'fictioneer_hide_chapter_icons' ) ) {
    $fields['fictioneer_story_hide_chapter_icons'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_story_hide_chapter_icons']);
  }

  // Chapter groups flag
  if ( isset( $_POST['fictioneer_story_disable_groups'] ) && get_option( 'fictioneer_enable_chapter_groups' ) ) {
    $fields['fictioneer_story_disable_groups'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_story_disable_groups'] );
  }

  // Custom story CSS
  if ( isset( $_POST['fictioneer_story_css'] ) && current_user_can( 'fcn_custom_page_css', $post_id ) ) {
    $fields['fictioneer_story_css'] = fictioneer_sanitize_css( $_POST['fictioneer_story_css'] );
  }

  // Short description (required)
  if ( isset( $_POST['fictioneer_story_short_description'] ) ) {
    $fields['fictioneer_story_short_description'] =
      fictioneer_sanitize_editor( $_POST['fictioneer_story_short_description'] );

    if ( empty( $fields['fictioneer_story_short_description'] ) ) {
      $fields['fictioneer_story_short_description'] = '';
    }
  }

  // Chapters
  if ( isset( $_POST['fictioneer_story_chapters'] ) && current_user_can( 'edit_fcn_stories', $post_id ) ) {
    $previous_chapter_ids = fictioneer_get_story_chapter_ids( $post_id );

    // Filter out non-valid chapter IDs
    $chapter_ids = fictioneer_sql_filter_valid_chapter_ids( $post_id, $_POST['fictioneer_story_chapters'] );

    if ( empty( $chapter_ids ) ) {
      $fields['fictioneer_story_chapters'] = []; // Ensure empty meta is removed
    } else {
      $chapter_ids = array_map( 'strval', $chapter_ids );
      $fields['fictioneer_story_chapters'] = $chapter_ids;
    }

    // Remember when chapters have been changed
    if ( $previous_chapter_ids !== $chapter_ids ) {
      update_post_meta( $post_id, 'fictioneer_chapters_modified', current_time( 'mysql', true ) );
    }

    // Remember when chapters have been added
    if ( fictioneer_sql_has_new_story_chapters( $post_id, $chapter_ids, $previous_chapter_ids ) ) {
      update_post_meta( $post_id, 'fictioneer_chapters_added', current_time( 'mysql', true ) );
    }

    // Log changes
    fictioneer_log_story_chapter_changes( $post_id, $chapter_ids, $previous_chapter_ids );
  }

  // Custom pages
  if (
    isset( $_POST['fictioneer_story_custom_pages'] ) &&
    current_user_can( 'fcn_story_pages', $post_id )
  ) {
    // Filter out non-valid page IDs
    $page_ids = fictioneer_sql_filter_valid_page_ids( $post_author_id, $_POST['fictioneer_story_custom_pages'] );

    if ( empty( $page_ids ) ) {
      $fields['fictioneer_story_custom_pages'] = []; // Ensure empty meta is removed
    } else {
      $fields['fictioneer_story_custom_pages'] = array_map( 'strval', $page_ids );
    }
  }

  // Global note
  if ( isset( $_POST['fictioneer_story_global_note'] ) ) {
    $fields['fictioneer_story_global_note'] =
      fictioneer_sanitize_editor( $_POST['fictioneer_story_global_note'] );
  }

  // Password note
  if ( isset( $_POST['fictioneer_story_password_note'] ) ) {
    $fields['fictioneer_story_password_note'] =
      fictioneer_sanitize_editor( $_POST['fictioneer_story_password_note'] );
  }

  // ePUBs...
  if ( get_option( 'fictioneer_enable_epubs' ) ) {
    // Custom upload
    if ( isset( $_POST['fictioneer_story_ebook_upload_one'] ) && current_user_can( 'fcn_custom_epub_upload', $post_id ) ) {
      $ebook_id = absint( $_POST['fictioneer_story_ebook_upload_one'] );

      if ( $ebook_id > 0 && wp_get_attachment_url( $ebook_id ) ) {
        $fields['fictioneer_story_ebook_upload_one'] = $ebook_id;
      } else {
        $fields['fictioneer_story_ebook_upload_one'] = 0;
      }
    }

    // ePUB preface
    if ( isset( $_POST['fictioneer_story_epub_preface'] ) ) {
      $fields['fictioneer_story_epub_preface'] = fictioneer_sanitize_editor( $_POST['fictioneer_story_epub_preface'] );
    }

    // ePUB afterword
    if ( isset( $_POST['fictioneer_story_epub_afterword'] ) ) {
      $fields['fictioneer_story_epub_afterword'] = fictioneer_sanitize_editor( $_POST['fictioneer_story_epub_afterword'] );
    }

    // Custom ePUB CSS
    if ( isset( $_POST['fictioneer_story_epub_custom_css'] ) && current_user_can( 'fcn_custom_epub_css', $post_id ) ) {
      $fields['fictioneer_story_epub_custom_css'] = fictioneer_sanitize_css( $_POST['fictioneer_story_epub_custom_css'] );
    }
  }

  // --- Filters ---------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_metabox_updates_story', $fields, $post_id );

  // --- Save ------------------------------------------------------------------

  fictioneer_bulk_update_post_meta( $post_id, $fields );
}
add_action( 'save_post', 'fictioneer_save_story_metaboxes' );

// =============================================================================
// CHAPTER META FIELDS
// =============================================================================

/**
 * Adds chapter meta metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_chapter_meta_metabox() {
  add_meta_box(
    'fictioneer-chapter-meta',
    __( 'Chapter Meta', 'fictioneer' ),
    'fictioneer_render_chapter_meta_metabox',
    ['fcn_chapter'],
    'side',
    'default'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_chapter_meta_metabox' );

/**
 * Render chapter meta metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_chapter_meta_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "chapter_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields ------------------------------------------------------------

  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  if ( ! get_option( 'fictioneer_hide_chapter_icons' ) ) {
    $output['fictioneer_chapter_icon'] = fictioneer_get_metabox_icons(
      $post,
      'fictioneer_chapter_icon',
      array(
        'label' => _x( 'Icon', 'Chapter icon meta field label.', 'fictioneer' ),
        'description' => sprintf(
          __( 'You can use all <em>free</em> <a href="%s" target="_blank">Font Awesome</a> icons.', 'fictioneer' ),
          'https://fontawesome.com/search'
        ),
        'placeholder' => FICTIONEER_DEFAULT_CHAPTER_ICON
      )
    );
  }

  if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) && ! get_option( 'fictioneer_hide_chapter_icons' ) ) {
    $output['fictioneer_chapter_text_icon'] = fictioneer_get_metabox_text(
      $post,
      'fictioneer_chapter_text_icon',
      array(
        'label' => _x( 'Text Icon', 'Chapter text icon meta field label.', 'fictioneer' ),
        'description' => __( 'Text string as icon; mind the limited space.', 'fictioneer' )
      )
    );
  }

  if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
    $output['fictioneer_chapter_short_title'] = fictioneer_get_metabox_text(
      $post,
      'fictioneer_chapter_short_title',
      array(
        'label' => _x( 'Short Title', 'Chapter short title meta field label.', 'fictioneer' ),
        'description' => __( 'Shorter title, such as "Arc 15, Ch. 17". Not used by default, intended for child themes.', 'fictioneer' )
      )
    );
  }

  if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
    $output['fictioneer_chapter_prefix'] = fictioneer_get_metabox_text(
      $post,
      'fictioneer_chapter_prefix',
      array(
        'label' => _x( 'Prefix', 'Chapter prefix meta field label.', 'fictioneer' ),
        'description' => __( 'Prefix, such as "Prologue" or "Act I ~ 01".', 'fictioneer' )
      )
    );
  }

  if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
    $output['fictioneer_chapter_co_authors'] = fictioneer_get_metabox_array(
      $post,
      'fictioneer_chapter_co_authors',
      array(
        'label' => _x( 'Co-Authors', 'Chapter co-authors meta field label.', 'fictioneer' ),
        'description' => __( 'Comma-separated list of author IDs.', 'fictioneer' )
      )
    );
  }

  $output['fictioneer_chapter_rating'] = fictioneer_get_metabox_select(
    $post,
    'fictioneer_chapter_rating',
    array(
      '0' => __( '— Unset —', 'fictioneer' ),
      'Everyone' => fcntr( 'Everyone' ),
      'Teen' => fcntr( 'Teen' ),
      'Mature' => fcntr( 'Mature' ),
      'Adult' => fcntr( 'Adult' )
    ),
    array(
      'label' => _x( 'Age Rating', 'Chapter age rating meta field label.', 'fictioneer' ),
      'description' => __( 'Optional age rating only for the chapter.', 'fictioneer' )
    )
  );

  $output['fictioneer_chapter_warning'] = fictioneer_get_metabox_text(
    $post,
    'fictioneer_chapter_warning',
    array(
      'label' => _x( 'Warning', 'Chapter warning meta field label.', 'fictioneer' ),
      'description' => __( 'Warning shown in chapter lists.', 'fictioneer' ),
      'maxlength' => 48
    )
  );

  $output['fictioneer_chapter_warning_notes'] = fictioneer_get_metabox_textarea(
    $post,
    'fictioneer_chapter_warning_notes',
    array(
      'label' => __( 'Warning Notes', 'fictioneer' ),
      'description' => __( 'Warning note shown above chapters.', 'fictioneer' )
    )
  );

  $output['flags_heading'] = '<div class="fictioneer-meta-field-heading">' .
    __( 'Flags', 'Metabox checkbox heading.', 'fictioneer' ) . '</div>';

  $output['fictioneer_chapter_hidden'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_chapter_hidden',
    __( 'Unlisted (but accessible with link)', 'fictioneer' )
  );

  $output['fictioneer_chapter_no_chapter'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_chapter_no_chapter',
    __( 'Do not count as chapter', 'fictioneer' )
  );

  $output['fictioneer_chapter_hide_title'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_chapter_hide_title',
    __( 'Hide title in chapter', 'fictioneer' )
  );

  $output['fictioneer_chapter_hide_support_links'] = fictioneer_get_metabox_checkbox(
    $post,
    'fictioneer_chapter_hide_support_links',
    __( 'Hide support links', 'fictioneer' )
  );

  if ( FICTIONEER_ENABLE_PARTIAL_CACHING && current_user_can( 'manage_options' ) ) {
    $output['fictioneer_chapter_disable_partial_caching'] = fictioneer_get_metabox_checkbox(
    $post,
      'fictioneer_chapter_disable_partial_caching',
      __( 'Disable partial caching of content', 'fictioneer' )
    );
  }

  // --- Filters ---------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_chapter_meta', $output, $post );

  // --- Render ----------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_chapter_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Adds chapter data metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_chapter_data_metabox() {
  add_meta_box(
    'fictioneer-chapter-data',
    __( 'Chapter Data', 'fictioneer' ),
    'fictioneer_render_chapter_data_metabox',
    ['fcn_chapter'],
    'normal',
    'high'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_chapter_data_metabox' );

/**
 * Render chapter data metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_chapter_data_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "chapter_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $post_author_id = get_post_field( 'post_author', $post->ID );
  $current_story_id = fictioneer_get_chapter_story_id( $post->ID );
  $output = [];

  // --- Add fields ------------------------------------------------------------

  // Magic quote test
  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  // Story
  $story_selection = fictioneer_sql_get_chapter_story_selection( $post_author_id, $current_story_id );
  $stories = $story_selection['stories'];

  $author_warning = $story_selection['other_author'] ? ' ' . __( '<strong>Warning:</strong> The selected story belongs to another author. If you change the selection, you cannot go back.', 'fictioneer' ) : '';

  $author_warning = $story_selection['co_author'] ? ' ' . __( '<strong>Note:</strong> You are a co-author of the selected story, but only you can edit this chapter.', 'fictioneer' ) : $author_warning;

  if ( get_option( 'fictioneer_enable_chapter_appending' ) ) {
    $description = __( 'Select the story this chapter belongs to. Published and scheduled chapters are automatically appended to the story.', 'fictioneer' );
  }

  $output['fictioneer_chapter_story'] = fictioneer_get_metabox_select(
    $post,
    'fictioneer_chapter_story',
    $stories,
    array(
      'label' => _x( 'Story', 'Chapter story meta field label.', 'fictioneer' ),
      'description' => $description . $author_warning,
      'attributes' => array(
        'data-action="select-story"',
        "data-target='chapter_groups_for_{$post->ID}'"
      ),
      'asort' => 1,
      'query_var' => 'story_id'
    )
  );

  // Story override
  if ( current_user_can( 'manage_options' ) || current_user_can( 'fcn_crosspost' ) ) {
    if ( get_option( 'fictioneer_enable_chapter_appending' ) ) {
      $override_description = __( 'Accepts a story ID to assign and append the chapter with override permission.', 'fictioneer' );
    } else {
      $override_description = __( 'Accepts a story ID to assign the chapter with override permission.', 'fictioneer' );
    }

    $output['fictioneer_chapter_story_override'] = fictioneer_get_metabox_text(
      $post,
      'fictioneer_chapter_story_override',
      array(
        'label' => _x( 'Story Override', 'Chapter story override meta field label.', 'fictioneer' ),
        'description' => $override_description
      )
    );
  }

  // Card/List title
  $output['fictioneer_chapter_list_title'] = fictioneer_get_metabox_text(
    $post,
    'fictioneer_chapter_list_title',
    array(
      'label' => _x( 'Card/List Title', 'Chapter card/list title meta field label.', 'fictioneer' ),
      'description' => __( 'Alternative title used in cards and lists. Useful for long titles that get truncated on small screens.', 'fictioneer' )
    )
  );

  // Group
  $output['fictioneer_chapter_group'] = fictioneer_get_metabox_combo(
    $post,
    'fictioneer_chapter_group',
    array(
      'label' => _x( 'Group', 'Chapter group meta field label.', 'fictioneer' ),
      'description' => __( 'Organize chapters into groups; mind the order and spelling (case-sensitive). Only rendered if there are at least two, unassigned chapters are grouped under "Unassigned".', 'fictioneer' ),
      'attributes' => array(
        "list='chapter_groups_for_{$post->ID}'"
      )
    )
  );

  // Foreword
  $output['fictioneer_chapter_foreword'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_chapter_foreword',
    array(
      'label' => _x( 'Foreword', 'Chapter foreword meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed in a box above the chapter; start with "[!show]" to show in protected chapters. Limited HTML allowed.', 'fictioneer' )
    )
  );

  // Afterword
  $output['fictioneer_chapter_afterword'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_chapter_afterword',
    array(
      'label' => _x( 'Afterword', 'Chapter afterword meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed in a box below the chapter; start with "[!show]" to show in protected chapters. Limited HTML allowed.', 'fictioneer' )
    )
  );

  // Password note
  $output['fictioneer_chapter_password_note'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_chapter_password_note',
    array(
      'label' => _x( 'Password Note', 'Chapter password note meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed for password protected content. Limited HTML allowed.', 'fictioneer' )
    )
  );

  // --- Filters ---------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_chapter_data', $output, $post );

  // --- Render ----------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_chapter_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save chapter metaboxes
 *
 * @since 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_chapter_metaboxes( $post_id ) {
  // --- Verify ----------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_chapter_nonce'] ?? '' ), "chapter_meta_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_chapter' ||
    ! fictioneer_validate_save_action_user( $post_id, 'fcn_chapter' )
  ) {
    return;
  }

  // --- Sanitize and add data -------------------------------------------------

  $post_author_id = get_post_field( 'post_author', $post_id );

  // Hidden flag
  if ( isset( $_POST['fictioneer_chapter_hidden'] ) ) {
    $fields['fictioneer_chapter_hidden'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_chapter_hidden'] );
  }

  // No chapter flag
  if ( isset( $_POST['fictioneer_chapter_no_chapter'] ) ) {
    $fields['fictioneer_chapter_no_chapter'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_chapter_no_chapter'] );
  }

  // Hide title flag
  if ( isset( $_POST['fictioneer_chapter_hide_title'] ) ) {
    $fields['fictioneer_chapter_hide_title'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_chapter_hide_title'] );
  }

  // Hide support links flag
  if ( isset( $_POST['fictioneer_chapter_hide_support_links'] ) ) {
    $fields['fictioneer_chapter_hide_support_links'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_chapter_hide_support_links'] );
  }

  // Disable partial caching flag
  if (
    FICTIONEER_ENABLE_PARTIAL_CACHING &&
    isset( $_POST['fictioneer_chapter_disable_partial_caching'] ) &&
    current_user_can( 'manage_options' )
  ) {
    $fields['fictioneer_chapter_disable_partial_caching'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_chapter_disable_partial_caching'] );
  }

  // Rating
  if ( isset( $_POST['fictioneer_chapter_rating'] ) ) {
    $allowed_ratings = ['Everyone', 'Teen', 'Mature', 'Adult'];
    $rating = fictioneer_sanitize_selection( $_POST['fictioneer_chapter_rating'], $allowed_ratings );
    $fields['fictioneer_chapter_rating'] = $rating;
  }

  // Icon
  if ( isset( $_POST['fictioneer_chapter_icon'] ) && ! get_option( 'fictioneer_hide_chapter_icons' ) ) {
    $icon = sanitize_text_field( $_POST['fictioneer_chapter_icon'] );
    $icon_object = json_decode( $icon ); // Legacy

    // Valid?
    if ( ! $icon_object && ( empty( $icon ) || strpos( $icon, 'fa-' ) !== 0 ) ) {
      $icon = '';
    }

    if ( $icon_object && ( ! property_exists( $icon_object, 'style' ) || ! property_exists( $icon_object, 'id' ) ) ) {
      $icon = '';
    }

    if ( $icon === FICTIONEER_DEFAULT_CHAPTER_ICON ) {
      $icon = ''; // Do not save default icon
    }

    $fields['fictioneer_chapter_icon'] = $icon;
  }

  // Text icon
  if (
    isset( $_POST['fictioneer_chapter_text_icon'] ) &&
    get_option( 'fictioneer_enable_advanced_meta_fields' ) &&
    ! get_option( 'fictioneer_hide_chapter_icons' )
  ) {
    $text_icon = sanitize_text_field( $_POST['fictioneer_chapter_text_icon'] );
    $fields['fictioneer_chapter_text_icon'] = mb_substr( $text_icon, 0, 10, 'UTF-8' ); // Icon codes, etc.
  }

  // Short title
  if ( isset( $_POST['fictioneer_chapter_short_title'] ) && get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
    $fields['fictioneer_chapter_short_title'] = sanitize_text_field( $_POST['fictioneer_chapter_short_title'] );
  }

  // Prefix
  if ( isset( $_POST['fictioneer_chapter_prefix'] ) && get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
    $fields['fictioneer_chapter_prefix'] = sanitize_text_field( $_POST['fictioneer_chapter_prefix'] );
  }

  // Co-authors
  if ( isset( $_POST['fictioneer_chapter_co_authors'] ) && get_option( 'fictioneer_enable_advanced_meta_fields' ) ){
    $co_authors = fictioneer_explode_list( $_POST['fictioneer_chapter_co_authors'] );
    $co_authors = array_map( 'absint', $co_authors );
    $co_authors = array_filter( $co_authors, function( $user_id ) {
      return get_userdata( $user_id ) !== false;
    });
    $co_authors = array_unique( $co_authors );

    $fields['fictioneer_chapter_co_authors'] = array_map( 'strval', $co_authors );
  }

  // Warning
  if ( isset( $_POST['fictioneer_chapter_warning'] ) ) {
    $fields['fictioneer_chapter_warning'] = sanitize_text_field( $_POST['fictioneer_chapter_warning'] );
    $fields['fictioneer_chapter_warning'] = fictioneer_truncate( $fields['fictioneer_chapter_warning'], 48 );
  }

  // Warning notes
  if ( isset( $_POST['fictioneer_chapter_warning_notes'] ) ) {
    $notes = sanitize_textarea_field( $_POST['fictioneer_chapter_warning_notes'] );
    $fields['fictioneer_chapter_warning_notes'] = $notes;
  }

  // Story
  if ( isset( $_POST['fictioneer_chapter_story'] ) && ! ( $_POST['fictioneer_chapter_story_override'] ?? 0 ) ) {
    $story_id = fictioneer_validate_id( $_POST['fictioneer_chapter_story'], 'fcn_story' );
    $current_story_id = absint( fictioneer_get_chapter_story_id( $post_id ) );

    if ( $current_story_id && $story_id !== $current_story_id ) {
      // Make sure the chapter is not in the list of the wrong story
      $other_story_chapters = fictioneer_get_story_chapter_ids( $current_story_id );
      $other_story_chapters = array_diff( $other_story_chapters, [ strval( $post_id ) ] );

      update_post_meta( $current_story_id, 'fictioneer_story_chapters', $other_story_chapters );
    }

    if ( $story_id ) {
      $story_author_id = get_post_field( 'post_author', $story_id );
      $invalid_story = false;

      if ( get_option( 'fictioneer_limit_chapter_stories_by_author' ) ) {
        $co_authored_stories = fictioneer_sql_get_co_authored_story_ids( $post_author_id );

        $invalid_story = $story_author_id != $post_author_id && ! in_array( $story_id, $co_authored_stories );
      }

      if ( $invalid_story && $current_story_id != $story_id ) {
        $story_id = 0;
      } else {
        if ( get_option( 'fictioneer_enable_chapter_appending' ) ) {
          fictioneer_append_chapter_to_story( $post_id, $story_id );
        }
      }
    }

    fictioneer_set_chapter_story_parent( $post_id, $story_id ?: 0 );
    $fields['fictioneer_chapter_story'] = strval( $story_id ?: 0 );
  }

  // Story override
  if ( current_user_can( 'manage_options' ) || current_user_can( 'fcn_crosspost' ) ) {
    if ( isset( $_POST['fictioneer_chapter_story_override'] ) ) {
      $story_id = fictioneer_validate_id( $_POST['fictioneer_chapter_story_override'], 'fcn_story' );
      $current_story_id = absint( fictioneer_get_chapter_story_id( $post_id ) );

      if ( $story_id ) {
        if ( $current_story_id && $story_id !== $current_story_id ) {
          $other_story_chapters = fictioneer_get_story_chapter_ids( $current_story_id );
          $other_story_chapters = array_diff( $other_story_chapters, [ strval( $post_id ) ] );

          update_post_meta( $current_story_id, 'fictioneer_story_chapters', $other_story_chapters );
        }

        if ( get_option( 'fictioneer_enable_chapter_appending' ) ) {
          fictioneer_append_chapter_to_story( $post_id, $story_id, true );
        }

        fictioneer_set_chapter_story_parent( $post_id, $story_id );
        $fields['fictioneer_chapter_story'] = strval( $story_id );
      }
    }
  }

  // Card/List title
  if ( isset( $_POST['fictioneer_chapter_list_title'] ) ) {
    $fields['fictioneer_chapter_list_title'] = sanitize_text_field( $_POST['fictioneer_chapter_list_title'] );
  }

  // Group
  if ( isset( $_POST['fictioneer_chapter_group'] ) ) {
    $fields['fictioneer_chapter_group'] = sanitize_text_field( $_POST['fictioneer_chapter_group'] );
  }

  // Foreword
  if ( isset( $_POST['fictioneer_chapter_foreword'] ) ) {
    $fields['fictioneer_chapter_foreword'] = fictioneer_sanitize_editor( $_POST['fictioneer_chapter_foreword'] );
  }

  // Afterword
  if ( isset( $_POST['fictioneer_chapter_afterword'] ) ) {
    $fields['fictioneer_chapter_afterword'] = fictioneer_sanitize_editor( $_POST['fictioneer_chapter_afterword'] );
  }

  // Password note
  if ( isset( $_POST['fictioneer_chapter_password_note'] ) ) {
    $fields['fictioneer_chapter_password_note'] = fictioneer_sanitize_editor( $_POST['fictioneer_chapter_password_note'] );
  }

  // --- Filters ---------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_metabox_updates_chapter', $fields, $post_id );

  // --- Save ------------------------------------------------------------------

  fictioneer_bulk_update_post_meta( $post_id, $fields );
}
add_action( 'save_post', 'fictioneer_save_chapter_metaboxes' );

// =============================================================================
// EXTRA META FIELDS
// =============================================================================

/**
 * Adds extra metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_extra_metabox() {
  add_meta_box(
    'fictioneer-extra',
    __( 'Extra Meta', 'fictioneer' ),
    'fictioneer_render_extra_metabox',
    ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'],
    'side',
    'default'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_extra_metabox' );

/**
 * Render extra metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_extra_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "advanced_meta_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];
  $author_id = $post->post_author ?: get_current_user_id();

  // --- Add fields ------------------------------------------------------------

  // Magic quote test
  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  // Landscape image
  $output['fictioneer_landscape_image'] = fictioneer_get_metabox_image(
    $post,
    'fictioneer_landscape_image',
    array(
      'label' => __( 'Landscape Image', 'fictioneer' ),
      'description' => __( 'Used where the image is wider than high.', 'fictioneer' ),
      'button' => __( 'Set landscape image', 'fictioneer' )
    )
  );

  // Custom page header
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

  // Custom page CSS
  if ( current_user_can( 'fcn_custom_page_css', $post->ID ) ) {
    $output['fictioneer_custom_css'] = fictioneer_get_metabox_textarea(
      $post,
      'fictioneer_custom_css',
      array(
        'label' => __( 'Custom Page CSS', 'fictioneer' ),
        'description' => __( 'Only applied to the page.', 'fictioneer' ),
        'placeholder' => __( '.selector { ... }', 'fictioneer' ),
        'input_classes' => 'fictioneer-meta-field__textarea--code'
      )
    );
  }

  if ( $post->post_type === 'page' ) {
    // Short name
    $output['fictioneer_short_name'] = fictioneer_get_metabox_text(
      $post,
      'fictioneer_short_name',
      array(
        'label' => _x( 'Short Name', 'Page short name meta field label.', 'fictioneer' ),
        'description' => __( 'Required for the tab view in stories.', 'fictioneer' )
      )
    );

    // Filter & Search ID
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

    // Story ID
    if ( current_user_can( 'manage_options' ) ) {
      $output['fictioneer_template_story_id'] = fictioneer_get_metabox_text(
        $post,
        'fictioneer_template_story_id',
        array(
          'label' => _x( 'Story Id', 'Page story ID meta field label.', 'fictioneer' ),
          'description' => __( 'Used by the "Story Page/Mirror" templates.', 'fictioneer' )
        )
      );
    }

    // Checkbox: Show story header
    if ( current_user_can( 'manage_options' ) ) {
      $output['fictioneer_template_show_story_header'] = fictioneer_get_metabox_checkbox(
        $post,
        'fictioneer_template_show_story_header',
        __( 'Show story header', 'fictioneer' )
      );
    }
  }

  // Story blogs
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

  // Patreon
  $can_patreon = current_user_can( 'fcn_assign_patreon_tiers' ) || current_user_can( 'manage_options' );

  if ( $can_patreon && get_option( 'fictioneer_enable_patreon_locks' ) ) {
    $patreon_client_id = get_option( 'fictioneer_patreon_client_id' );
    $patreon_client_secret = get_option( 'fictioneer_patreon_client_secret' );

    if ( get_option( 'fictioneer_enable_oauth' ) && $patreon_client_id && $patreon_client_secret ) {
      $patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
      $patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];

      if ( ! empty( $patreon_tiers ) ) {
        $tier_options = array( '0' => __( '— Tier —', 'fictioneer' ) );

        foreach ( $patreon_tiers as $tier ) {
          $tier_options[ $tier['id'] ] = sprintf(
            _x( '%s (%s)', 'Patreon tier meta field token (title and amount_cents).', 'fictioneer' ),
            $tier['title'],
            $tier['amount_cents']
          );
        }

        // Tiers
        $output['fictioneer_patreon_lock_tiers'] = fictioneer_get_metabox_tokens(
          $post,
          'fictioneer_patreon_lock_tiers',
          $tier_options,
          array(
            'label' => _x( 'Patreon Tiers', 'Patreon tiers meta field label.', 'fictioneer' ),
            'description' => __( 'Select which tiers ignore the password.', 'fictioneer' ),
            'names' => $tier_options
          )
        );

        // Threshold
        $output['fictioneer_patreon_lock_amount'] = fictioneer_get_metabox_number(
          $post,
          'fictioneer_patreon_lock_amount',
          array(
            'label' => _x( 'Patreon Amount Cents', 'Patreon amount cents meta field label.', 'fictioneer' ),
            'description' => __( 'Pledge threshold to ignore the password.', 'fictioneer' ),
            'attributes' => array(
              'min="0"'
            )
          )
        );
      }
    }
  }

  // Password expiration datetime
  if ( current_user_can( 'manage_options' ) || current_user_can( 'fcn_expire_passwords' ) ) {
    $output['fictioneer_post_password_expiration_date'] = fictioneer_get_metabox_datetime(
      $post,
      'fictioneer_post_password_expiration_date',
      array(
        'label' => _x( 'Expire Post Password', 'Password expiration meta field label.', 'fictioneer' ),
        'description' => __( 'Removes the password after the date.', 'fictioneer' ),
      )
    );
  }

  // Flags...
  $flag_count = 0;

  $output['flags_heading'] = '<div class="fictioneer-meta-field-heading">' .
    _x( 'Flags', 'Metabox checkbox heading.', 'fictioneer' ) . '</div>';

  // Checkbox: Patreon inheritance
  if ( $can_patreon && get_option( 'fictioneer_enable_patreon_locks' ) ) {
    $output['fictioneer_patreon_inheritance'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_patreon_inheritance',
      __( 'Chapters inherit Patreon settings', 'fictioneer' )
    );

    $flag_count++;
  }

  // Checkbox: Disable new comments
  if ( in_array( $post->post_type, ['post', 'page', 'fcn_story', 'fcn_chapter'] ) ) {
    $output['fictioneer_disable_commenting'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_disable_commenting',
      __( 'Disable new comments', 'fictioneer' )
    );

    $flag_count++;
  }

  // Checkbox: Disable new comments
  if ( current_user_can( 'manage_options' ) && get_theme_mod( 'sidebar_style', 'none' ) !== 'none' ) {
    $output['fictioneer_disable_sidebar'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_disable_sidebar',
      __( 'Disable sidebar', 'fictioneer' )
    );

    $flag_count++;
  }

  // Checkbox: Disable new comments
  if ( current_user_can( 'manage_options' ) ) {
    $output['fictioneer_disable_page_padding'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_disable_page_padding',
      __( 'Disable page padding', 'fictioneer' )
    );

    $flag_count++;
  }

  // Remove Flags heading if no flags are rendered
  if ( ! $flag_count ) {
    unset( $output['flags_heading'] );
  }

  // --- Filters ---------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_advanced', $output, $post );

  // --- Render ----------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_advanced_meta_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save extra metabox
 *
 * @since 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_extra_metabox( $post_id ) {
  $post_type = get_post_type( $post_id );

  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_advanced_meta_nonce'] ?? '' ), "advanced_meta_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    ! in_array( $post_type, ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_recommendation', 'fcn_collection'] ) ||
    ! fictioneer_validate_save_action_user( $post_id, $post_type )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $post_author_id = get_post_field( 'post_author', $post_id );
  $fields = [];

  // Landscape image
  if ( isset( $_POST['fictioneer_landscape_image'] ) ) {
    $fields['fictioneer_landscape_image'] = absint( $_POST['fictioneer_landscape_image'] );
  }

  // Custom page header
  if ( isset( $_POST['fictioneer_custom_header_image'] ) && current_user_can( 'fcn_custom_page_header', $post_id ) ) {
    $fields['fictioneer_custom_header_image'] = absint( $_POST['fictioneer_custom_header_image'] );
  }

  // Custom page CSS
  if ( isset( $_POST['fictioneer_custom_css'] ) && current_user_can( 'fcn_custom_page_css', $post_id ) ) {
    $fields['fictioneer_custom_css'] = fictioneer_sanitize_css( $_POST['fictioneer_custom_css'] );
  }

  // Short name
  if ( isset( $_POST['fictioneer_short_name'] ) && $post_type === 'page' ) {
    $fields['fictioneer_short_name'] = sanitize_text_field( $_POST['fictioneer_short_name'] );

    if ( empty( $fields['fictioneer_short_name'] ) ) {
      $fields['fictioneer_short_name'] = fictioneer_truncate( get_the_title( $post_id ), 24 );
    }
  }

  // Search & Filter ID
  if ( isset( $_POST['fictioneer_filter_and_search_id'] ) && $post_type === 'page' ) {
    if ( current_user_can( 'install_plugins' ) ) {
      $fields['fictioneer_filter_and_search_id'] = absint( $_POST['fictioneer_filter_and_search_id'] );
    }
  }

  // Story ID
  if ( isset( $_POST['fictioneer_template_story_id'] ) && $post_type === 'page' ) {
    $template_story_id = absint( $_POST['fictioneer_template_story_id'] );

    if ( current_user_can( 'manage_options' ) && fictioneer_validate_id( $template_story_id, 'fcn_story' ) ) {
      $fields['fictioneer_template_story_id'] = $template_story_id;
    } else {
      $fields['fictioneer_template_story_id'] = '';
    }
  }

  // Checkbox: Show story template header
  if (
    isset( $_POST['fictioneer_template_show_story_header'] ) &&
    $post_type === 'page' &&
    current_user_can( 'manage_options' )
  ) {
    $fields['fictioneer_template_show_story_header'] =
      fictioneer_sanitize_checkbox( $_POST['fictioneer_template_show_story_header'] );
  }

  // Story blogs
  if ( isset( $_POST['fictioneer_post_story_blogs'] ) && $post_type === 'post' ) {
    $story_blogs = fictioneer_explode_list( $_POST['fictioneer_post_story_blogs'] );
    $story_blogs = fictioneer_sql_filter_valid_blog_story_ids( $post_author_id, $story_blogs );

    $fields['fictioneer_post_story_blogs'] = array_map( 'strval', $story_blogs ); // Safer to match with LIKE in SQL
  }

  // Patreon
  $can_patreon = current_user_can( 'fcn_assign_patreon_tiers' ) || current_user_can( 'manage_options' );

  if ( $can_patreon && get_option( 'fictioneer_enable_patreon_locks' ) ) {
    $patreon_client_id = get_option( 'fictioneer_patreon_client_id' );
    $patreon_client_secret = get_option( 'fictioneer_patreon_client_secret' );
    $patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
    $patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];

    if ( get_option( 'fictioneer_enable_oauth' ) && $patreon_client_id && $patreon_client_secret && $patreon_tiers ) {
      // Tiers
      if ( isset( $_POST['fictioneer_patreon_lock_tiers'] ) ) {
        $selected_patreon_tiers = fictioneer_explode_list( $_POST['fictioneer_patreon_lock_tiers'] );

        if ( ! empty( $patreon_tiers ) ) {
          $selected_patreon_tiers = array_intersect( $selected_patreon_tiers, array_keys( $patreon_tiers ) );
          $selected_patreon_tiers = array_map( 'absint', $selected_patreon_tiers );
          $selected_patreon_tiers = array_unique( $selected_patreon_tiers );
          $selected_patreon_tiers = array_map( 'strval', $selected_patreon_tiers ); // Safer to match with LIKE in SQL
        }

        $fields['fictioneer_patreon_lock_tiers'] = $selected_patreon_tiers;
      }

      // Threshold
      if ( isset( $_POST['fictioneer_patreon_lock_amount'] ) ) {
        $fields['fictioneer_patreon_lock_amount'] = absint( $_POST['fictioneer_patreon_lock_amount'] );
      }

      // Inheritance
      if ( isset( $_POST['fictioneer_patreon_inheritance'] ) ) {
        $fields['fictioneer_patreon_inheritance'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_patreon_inheritance'] );
      }
    }
  }

  // Password expiration datetime
  $can_expire_passwords = current_user_can( 'manage_options' ) || current_user_can( 'fcn_expire_passwords' );

  if ( $can_expire_passwords && isset( $_POST['fictioneer_post_password_expiration_date'] ) ) {
    $expiration_date = $_POST['fictioneer_post_password_expiration_date'];

    if ( ! empty( $expiration_date ) ) {
      $local_datetime = new DateTime( $expiration_date, wp_timezone() );
      $local_datetime->setTimezone( new DateTimeZone( 'UTC' ) );

      $fields['fictioneer_post_password_expiration_date'] = $local_datetime->format( 'Y-m-d H:i:s' );
    } else {
      $fields['fictioneer_post_password_expiration_date'] = 0;
    }
  }

  // Checkbox: Disable new comments
  if (
    isset( $_POST['fictioneer_disable_commenting'] ) &&
    in_array( $post_type, ['post', 'page', 'fcn_story', 'fcn_chapter'] )
  ) {
    $fields['fictioneer_disable_commenting'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_commenting'] );
  }

  // Checkbox: Disable sidebar
  if ( isset( $_POST['fictioneer_disable_sidebar'] ) && current_user_can( 'manage_options' ) ) {
    $fields['fictioneer_disable_sidebar'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_sidebar'] );
  }

  // Checkbox: Disable padding
  if ( isset( $_POST['fictioneer_disable_page_padding'] ) && current_user_can( 'manage_options' ) ) {
    $fields['fictioneer_disable_page_padding'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_page_padding'] );
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_metabox_updates_advanced', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  fictioneer_bulk_update_post_meta( $post_id, $fields );
}
add_action( 'save_post', 'fictioneer_save_extra_metabox' );

// =============================================================================
// SUPPORT LINKS META FIELDS
// =============================================================================

/**
 * Adds support links metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_support_links_metabox() {
  add_meta_box(
    'fictioneer-support-links',
    __( 'Support Links', 'fictioneer' ),
    'fictioneer_render_support_links_metabox',
    ['post', 'fcn_story', 'fcn_chapter'],
    'side',
    'low'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_support_links_metabox' );

/**
 * Render support links metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_support_links_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "support_links_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  $output['fictioneer_patreon_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_patreon_link',
    array(
      'label' => _x( 'Patreon Link', 'Patreon link meta field label.', 'fictioneer' ),
      'placeholder' => 'https://...'
    )
  );

  $output['fictioneer_kofi_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_kofi_link',
    array(
      'label' => _x( 'Ko-fi Link', 'Ko-fi link meta field label.', 'fictioneer' ),
      'placeholder' => 'https://...'
    )
  );

  $output['fictioneer_subscribestar_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_subscribestar_link',
    array(
      'label' => _x( 'SubscribeStar Link', 'SubscribeStar link meta field label.', 'fictioneer' ),
      'placeholder' => 'https://...'
    )
  );

  $output['fictioneer_paypal_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_paypal_link',
    array(
      'label' => _x( 'Paypal Link', 'Paypal link meta field label.', 'fictioneer' ),
      'placeholder' => 'https://...'
    )
  );

  $output['fictioneer_donation_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_donation_link',
    array(
      'label' => _x( 'Donation Link', 'Donation link meta field label.', 'fictioneer' ),
      'placeholder' => 'https://...'
    )
  );

  // --- Filters -----------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_support_links', $output, $post );

  // --- Render ------------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_support_links_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save support links metabox
 *
 * @since 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_support_links_metabox( $post_id ) {
  $post_type = get_post_type( $post_id );

  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_support_links_nonce'] ?? '' ), "support_links_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    ! in_array( $post_type, ['post', 'fcn_story', 'fcn_chapter'] ) ||
    ! fictioneer_validate_save_action_user( $post_id, $post_type )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $fields = [];

  // Patreon link
  if ( isset( $_POST['fictioneer_patreon_link'] ) ) {
    $patreon = fictioneer_sanitize_url( $_POST['fictioneer_patreon_link'], null, '#^https://(www\.)?patreon\.#' );
    $fields['fictioneer_patreon_link'] = $patreon;
  }

  // Ko-fi link
  if ( isset( $_POST['fictioneer_kofi_link'] ) ) {
    $kofi = fictioneer_sanitize_url( $_POST['fictioneer_kofi_link'], null, '#^https://(www\.)?ko-fi\.#' );
    $fields['fictioneer_kofi_link'] = $kofi;
  }

  // SubscribeStar link
  if ( isset( $_POST['fictioneer_subscribestar_link'] ) ) {
    $subscribe_star = fictioneer_sanitize_url( $_POST['fictioneer_subscribestar_link'], null, '#^https://(www\.)?subscribestar\.#' );
    $fields['fictioneer_subscribestar_link'] = $subscribe_star;
  }

  // Paypal link
  if ( isset( $_POST['fictioneer_paypal_link'] ) ) {
    $paypal = fictioneer_sanitize_url( $_POST['fictioneer_paypal_link'], null, '#^https://(www\.)?paypal\.#' );
    $fields['fictioneer_paypal_link'] = $paypal;
  }

  // Donation link
  if ( isset( $_POST['fictioneer_donation_link'] ) ) {
    $donation = fictioneer_sanitize_url( $_POST['fictioneer_donation_link'], 'https://' );
    $fields['fictioneer_donation_link'] = $donation;
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_metabox_updates_support_links', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  fictioneer_bulk_update_post_meta( $post_id, $fields );
}
add_action( 'save_post', 'fictioneer_save_support_links_metabox' );

// =============================================================================
// BLOG POST META FIELDS
// =============================================================================

/**
 * Adds featured content side metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_featured_content_metabox() {
  add_meta_box(
    'fictioneer-featured-content',
    __( 'Featured Content', 'fictioneer' ),
    'fictioneer_render_featured_content_metabox',
    'post',
    'normal',
    'default'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_featured_content_metabox' );

/**
 * Render featured content metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_featured_content_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "post_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

  // Magic quote test
  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  // Featured items
  $item_ids = get_post_meta( $post->ID, 'fictioneer_post_featured', true ) ?: [];
  $item_ids = is_array( $item_ids ) ? $item_ids : [];
  $items = empty( $item_ids ) ? [] : get_posts(
    array(
      'post_type' => ['post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
      'post_status' => 'any',
      'post__in' => $item_ids ?: [0], // Must not be empty!
      'orderby' => 'post__in',
      'posts_per_page' => -1,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    )
  );

  $output['fictioneer_post_featured'] = fictioneer_get_metabox_relationships(
    $post,
    'fictioneer_post_featured',
    $items,
    'fictioneer_callback_relationship_featured',
    array(
      'description' => __( 'Select posts to be shown as cards below the content.', 'fictioneer' ),
      'post_types' => array(
        'excluded' => ['attachment', 'page']
      )
    )
  );

  // --- Filters -----------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_post_data', $output, $post );

  // --- Render ------------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_post_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save post metaboxes
 *
 * @since 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_post_metaboxes( $post_id ) {
  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_post_nonce'] ?? '' ), "post_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'post' ||
    ! fictioneer_validate_save_action_user( $post_id, 'post' )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $fields = [];

  // Featured posts
  if ( isset( $_POST['fictioneer_post_featured'] ) ) {
    $item_ids = fictioneer_sql_filter_valid_featured_ids( $_POST['fictioneer_post_featured'] );

    if ( empty( $item_ids ) ) {
      $fields['fictioneer_post_featured'] = []; // Ensure empty meta is removed
    } else {
      $fields['fictioneer_post_featured'] = array_map( 'strval', $item_ids );
    }
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_metabox_updates_post', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  fictioneer_bulk_update_post_meta( $post_id, $fields );

  // --- After update ------------------------------------------------------------

  // Update relationship registry
  if ( isset( $_POST['fictioneer_post_featured'] ) ) {
    fictioneer_update_post_relationship_registry( $post_id );
  }
}
add_action( 'save_post', 'fictioneer_save_post_metaboxes' );

/**
 * Update relationship registry for 'post' post types
 *
 * @since 5.0.0
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_update_post_relationship_registry( $post_id ) {
  // Setup
  $registry = fictioneer_get_relationship_registry();
  $featured = get_post_meta( $post_id, 'fictioneer_post_featured', true );

  // Update relationships
  $registry[ $post_id ] = [];

  if ( is_array( $featured ) && ! empty( $featured ) ) {
    foreach ( $featured as $featured_id ) {
      $registry[ $post_id ][ $featured_id ] = 'is_featured';

      if ( ! isset( $registry[ $featured_id ] ) ) {
        $registry[ $featured_id ] = [];
      }

      $registry[ $featured_id ][ $post_id ] = 'featured_by';
    }
  } else {
    $featured = [];
  }

  // Check for and remove outdated direct references
  foreach ( $registry as $key => $entry ) {
    // Skip if...
    if ( absint( $key ) < 1 || ! is_array( $entry ) || in_array( $key, $featured ) ) {
      continue;
    }

    // Unset if in array
    unset( $registry[ $key ][ $post_id ] );

    // Remove node if empty
    if ( empty( $registry[ $key ] ) ) {
      unset( $registry[ $key ] );
    }
  }

  // Update database
  fictioneer_save_relationship_registry( $registry );
}

// =============================================================================
// COLLECTION META FIELDS
// =============================================================================

/**
 * Adds collection data metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_collection_data_metabox() {
  add_meta_box(
    'fictioneer-collection-data',
    __( 'Collection Data', 'fictioneer' ),
    'fictioneer_render_collection_data_metabox',
    'fcn_collection',
    'normal',
    'default'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_collection_data_metabox' );

/**
 * Render collection data metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_collection_data_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "collection_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

  // Magic quote test
  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  // Card/List title
  $output['fictioneer_collection_list_title'] = fictioneer_get_metabox_text(
    $post,
    'fictioneer_collection_list_title',
    array(
      'label' => _x( 'Card/List Title', 'Collection card/list title meta field label.', 'fictioneer' ),
      'description' => __( 'Alternative title used in cards and lists. Useful for long titles that get truncated on small screens.', 'fictioneer' )
    )
  );

  // Short description
  $output['fictioneer_collection_description'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_collection_description',
    array(
      'label' => _x( 'Short Description', 'Collection short description meta field label.', 'fictioneer' ),
      'description' => __( 'The short description is used on cards, so best keep it under 100 words or 400 characters.', 'fictioneer' ),
      'required' => 1
    )
  );

  // Collection items
  $item_ids = get_post_meta( $post->ID, 'fictioneer_collection_items', true ) ?: [];
  $item_ids = is_array( $item_ids ) ? $item_ids : [];
  $items = empty( $item_ids ) ? [] : get_posts(
    array(
      'post_type' => ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
      'post_status' => 'any',
      'post__in' => $item_ids ?: [0], // Must not be empty!
      'orderby' => 'post__in',
      'posts_per_page' => -1,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    )
  );

  $output['fictioneer_collection_items'] = fictioneer_get_metabox_relationships(
    $post,
    'fictioneer_collection_items',
    $items,
    'fictioneer_callback_relationship_collection',
    array(
      'label' => _x( 'Collection Items', 'Collection Items meta field label.', 'fictioneer' ),
      'description' => __(
        'Select the posts you want to be featured. If you add a story, <strong>do not</strong> add its chapters separately.', 'fictioneer'
      ),
      'post_types' => array(
        'excluded' => ['attachment']
      )
    )
  );

  // --- Filters -----------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_collection_data', $output, $post );

  // --- Render ------------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_collection_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save collection metaboxes
 *
 * @since 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_collection_metaboxes( $post_id ) {
  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_collection_nonce'] ?? '' ), "collection_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_collection' ||
    ! fictioneer_validate_save_action_user( $post_id, 'fcn_collection' )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $fields = [];

  // Card/List title
  if ( isset( $_POST['fictioneer_collection_list_title'] ) ) {
    $fields['fictioneer_collection_list_title'] = sanitize_text_field( $_POST['fictioneer_collection_list_title'] );
  }

  // Short description
  if ( isset( $_POST['fictioneer_collection_description'] ) ) {
    $fields['fictioneer_collection_description'] = fictioneer_sanitize_editor( $_POST['fictioneer_collection_description'] );

    if ( empty( $fields['fictioneer_collection_description'] ) ) {
      $fields['fictioneer_collection_description'] = '';
    }
  }

  // Collection items
  if ( isset( $_POST['fictioneer_collection_items'] ) ) {
    $item_ids = fictioneer_sql_filter_valid_collection_ids( $_POST['fictioneer_collection_items'] );

    if ( empty( $item_ids ) ) {
      $fields['fictioneer_collection_items'] = []; // Ensure empty meta is removed
    } else {
      $fields['fictioneer_collection_items'] = array_map( 'strval', $item_ids );
    }
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_metabox_updates_collection', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  fictioneer_bulk_update_post_meta( $post_id, $fields );
}
add_action( 'save_post', 'fictioneer_save_collection_metaboxes' );

// =============================================================================
// RECOMMENDATION META FIELDS
// =============================================================================

/**
 * Adds recommendation data metabox
 *
 * @since 5.7.4
 */

function fictioneer_add_recommendation_data_metabox() {
  add_meta_box(
    'fictioneer-recommendation-data',
    __( 'Recommendation Data', 'fictioneer' ),
    'fictioneer_render_recommendation_data_metabox',
    'fcn_recommendation',
    'normal',
    'default'
  );
}
add_action( 'add_meta_boxes', 'fictioneer_add_recommendation_data_metabox' );

/**
 * Render recommendation data metabox
 *
 * @since 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_recommendation_data_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "recommendation_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

  // Magic quote test
  $output['fictioneer_magic_quote_test'] = fictioneer_get_magic_quote_test();

  // One sentence
  $output['fictioneer_recommendation_one_sentence'] = fictioneer_get_metabox_text(
    $post,
    'fictioneer_recommendation_one_sentence',
    array(
      'label' => _x( 'One Sentence', 'Recommendation one sentence meta field label.', 'fictioneer' ),
      'description' => __( 'Elevator pitch with 150 characters are less. For example: "Rebellious corporate heiress and her genius friend commit high-tech heists in a doomed city."', 'fictioneer' ),
      'required' => 1
    )
  );

  // Author
  $output['fictioneer_recommendation_author'] = fictioneer_get_metabox_text(
    $post,
    'fictioneer_recommendation_author',
    array(
      'label' => _x( 'Author', 'Recommendation author meta field label.', 'fictioneer' ),
      'description' => __( 'The name of the author. Separate multiple authors with commas.', 'fictioneer' ),
      'required' => 1
    )
  );

  // Primary URL
  $output['fictioneer_recommendation_primary_url'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_recommendation_primary_url',
    array(
      'label' => _x( 'Primary Link', 'Recommendation primary link meta field label.', 'fictioneer' ),
      'description' => __( "Primary link to the recommendation or author's website.", 'fictioneer' ),
      'required' => 1
    )
  );

  // URLs
  $output['fictioneer_recommendation_urls'] = fictioneer_get_metabox_textarea(
    $post,
    'fictioneer_recommendation_urls',
    array(
      'label' => _x( 'Additional Links', 'Recommendation additional links meta field label.', 'fictioneer' ),
      'description' => __( 'Links to the story, one link per line and mind the whitespaces. Format: "Link Name | https://www.address.abc" (without quotes).', 'fictioneer' )
    )
  );

  // Support
  $output['fictioneer_recommendation_support'] = fictioneer_get_metabox_textarea(
    $post,
    'fictioneer_recommendation_support',
    array(
      'label' => _x( 'Support', 'Recommendation support meta field label.', 'fictioneer' ),
      'description' => __( 'Link to the author\'s Patreon, Ko-Fi, etc. One link per line and mind the whitespaces. Format: "Link Name | https://www.address.abc" (without quotes).', 'fictioneer' )
    )
  );

  // --- Filters -----------------------------------------------------------------

  $output = apply_filters( 'fictioneer_filter_metabox_recommendation_data', $output, $post );

  // --- Render ------------------------------------------------------------------

  echo implode( '', $output );

  // Start HTML ---> ?>
  <input type="hidden" name="fictioneer_recommendation_nonce" value="<?php echo esc_attr( $nonce ); ?>" autocomplete="off">
  <?php // <--- End HTML
}

/**
 * Save recommendation metaboxes
 *
 * @since 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_recommendation_metaboxes( $post_id ) {
  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_recommendation_nonce'] ?? '' ), "recommendation_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_recommendation' ||
    ! fictioneer_validate_save_action_user( $post_id, 'fcn_recommendation' )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $fields = [];

  // One sentence
  if ( isset( $_POST['fictioneer_recommendation_one_sentence'] ) ) {
    $fields['fictioneer_recommendation_one_sentence'] = sanitize_text_field( $_POST['fictioneer_recommendation_one_sentence'] );

    if ( empty( $fields['fictioneer_recommendation_one_sentence'] ) ) {
      $excerpt = get_the_excerpt( $post_id ) ?: __( 'No description provided yet.', 'fictioneer' );

      $fields['fictioneer_recommendation_one_sentence'] = fictioneer_truncate( $excerpt, 256 );
    }
  }

  // Author
  if ( isset( $_POST['fictioneer_recommendation_author'] ) ) {
    $fields['fictioneer_recommendation_author'] = sanitize_text_field( $_POST['fictioneer_recommendation_author'] );

    if ( empty( $fields['fictioneer_recommendation_author'] ) ) {
      $fields['fictioneer_recommendation_author'] = _x( 'Undefined', 'Undefined recommendation author.', 'fictioneer' );
    }
  }

  // Primary URL
  if ( isset( $_POST['fictioneer_recommendation_primary_url'] ) ) {
    $url = fictioneer_sanitize_url( $_POST['fictioneer_recommendation_primary_url'] );
    $fields['fictioneer_recommendation_primary_url'] = $url;

    if ( empty( $fields['fictioneer_recommendation_primary_url'] ) ) {
      $fields['fictioneer_recommendation_primary_url'] = get_permalink( $post_id );
    }
  }

  // URLs
  if ( isset( $_POST['fictioneer_recommendation_urls'] ) ) {
    $notes = sanitize_textarea_field( $_POST['fictioneer_recommendation_urls'] );
    $fields['fictioneer_recommendation_urls'] = $notes;
  }

  // Support
  if ( isset( $_POST['fictioneer_recommendation_support'] ) ) {
    $notes = sanitize_textarea_field( $_POST['fictioneer_recommendation_support'] );
    $fields['fictioneer_recommendation_support'] = $notes;
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_metabox_updates_recommendation', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  fictioneer_bulk_update_post_meta( $post_id, $fields );
}
add_action( 'save_post', 'fictioneer_save_recommendation_metaboxes' );

// =============================================================================
// PATREON LIST VIEW AND BULK EDIT
// =============================================================================

/**
 * Hides Patreon columns in list table views by default.
 *
 * @since 5.17.0
 *
 * @param array     $hidden  Array of IDs of columns hidden by default.
 * @param WP_Screen $screen  WP_Screen object of the current screen.
 *
 * @return array Updated array of IDs.
 */

function fictioneer_default_hide_patreon_posts_columns( $hidden, $screen ) {
  if (
    in_array(
      $screen->post_type,
      ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation']
    )
  ) {
    $hidden[] = 'fictioneer_patreon_lock_tiers';
    $hidden[] = 'fictioneer_patreon_lock_amount';
  }

  return $hidden;
}
add_filter( 'default_hidden_columns', 'fictioneer_default_hide_patreon_posts_columns', 10, 2 );

/**
 * Adds Patreon columns to list table views.
 *
 * @since 5.17.0
 *
 * @param array $post_columns  An associative array of column headings.
 *
 * @return array Updated associative array of column headings.
 */

function fictioneer_add_posts_columns_patreon( $post_columns ) {
  $post_columns[ 'fictioneer_patreon_lock_tiers' ] =
    _x( 'Patreon Tiers', 'Patreon tiers list table column title.', 'fictioneer' );

  $post_columns[ 'fictioneer_patreon_lock_amount' ] =
    _x( 'Patreon Cents', 'Patreon amount cents list table column title.', 'fictioneer' );

  return $post_columns;
}

/**
 * Renders Patreon values in list table views.
 *
 * @since 5.17.0
 *
 * @param string $column_name  The name of the column to display.
 * @param int    $post_id      The current post ID.
 */

function fictioneer_manage_posts_column_patreon( $column_name, $post_id ) {
  // Setup
  $post = get_post( $post_id );
  $class = $post->post_password ? 'has-password' : 'no-password';

  // Output
  switch( $column_name ) {
    case 'fictioneer_patreon_lock_tiers':
      static $patreon_tiers = null;

      if ( ! $patreon_tiers ) {
        $patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
        $patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];
      }

      $post_tiers = get_post_meta( $post_id, 'fictioneer_patreon_lock_tiers', true ) ?: [];
      $post_tiers = is_array( $post_tiers ) ? $post_tiers : [];

      foreach ( $post_tiers as $key => $tier ) {
        $tier_data = $patreon_tiers[ $tier ] ?? 0;

        if ( $tier_data ) {
          $post_tiers[ $key ] = $tier_data['title'] === 'Free' ? fcntr( 'free_patreon_tier' ) : $tier_data['title'];
        }
      }

      echo $post_tiers ? '<span class="' . $class . '">' . implode( ', ', $post_tiers ) . '</span>' : '—';

      break;
    case 'fictioneer_patreon_lock_amount':
      $amount = get_post_meta( $post_id, 'fictioneer_patreon_lock_amount', true );

      echo $amount ? '<span class="' . $class . '">' . $amount . '</span>' : '—';

      break;
  }
}

/**
 * Adds Patreon tiers to bulk edit.
 *
 * @since 5.17.0
 *
 * @param string $column_name  Name of the column to edit.
 * @param string $post_type    The post type slug.
 */

function fictioneer_add_patreon_bulk_edit_tiers( $column_name, $post_type ) {
  // Check column and post type
  if (
    $column_name !== 'fictioneer_patreon_lock_tiers' ||
    ! in_array( $post_type, ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'] )
  ) {
    return;
  }

  // Setup
  $patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
  $patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];

  // Tiers?
  if ( ! $patreon_tiers ) {
    return;
  }

  // Prepare options
  $tier_options = [];

  foreach ( $patreon_tiers as $tier ) {
    $tier_options[ $tier['id'] ] = sprintf(
      _x( '%s (%s)', 'Patreon tier meta field token (title and amount_cents).', 'fictioneer' ),
      $tier['title'],
      $tier['amount_cents']
    );
  }

  // Nonce
  wp_nonce_field( 'fictioneer_bulk_edit_patreon', 'fictioneer_bulk_edit_patreon_nonce', false );

  // Start HTML ---> ?>
  <fieldset class="inline-edit-col-left">
    <div class="inline-edit-patreon-tiers-wrap">

      <span class="bulk-edit-title title"><?php
        _ex( 'Patreon Tiers (Override)', 'Patreon tiers bulk edit label.', 'fictioneer' );
      ?></span>

      <div class="bulk-edit-box">

        <input type="hidden" name="fictioneer_bulk_edit_patreon_lock_tiers" value="no_change">

        <label class="bulk-edit-checkbox-label-pair">
          <input type="checkbox" name="fictioneer_bulk_edit_patreon_lock_tiers" value="remove">
          <span><?php _e( 'Remove Tiers', 'fictioneer' ); ?></span>
        </label>

        <?php foreach ( $tier_options as $key => $tier ) : ?>

          <label class="bulk-edit-checkbox-label-pair">
            <input type="checkbox" name="fictioneer_bulk_edit_patreon_lock_tiers[]" value="<?php echo $key; ?>">
            <span><?php echo $tier; ?></span>
          </label>

        <?php endforeach; ?>

      </div>

    </div>
  </fieldset>
  <?php // <--- End HTML
}

/**
 * Adds Patreon amount_cents to bulk edit.
 *
 * @since 5.17.0
 *
 * @param string $column_name  Name of the column to edit.
 * @param string $post_type    The post type slug.
 */

function fictioneer_add_patreon_bulk_edit_amount( $column_name, $post_type ) {
  // Check column and post type
  if (
    $column_name !== 'fictioneer_patreon_lock_amount' ||
    ! in_array( $post_type, ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'] )
  ) {
    return;
  }

  // Nonce
  wp_nonce_field( 'fictioneer_bulk_edit_patreon', 'fictioneer_bulk_edit_patreon_nonce', false );

  // Start HTML ---> ?>
  <fieldset class="inline-edit-col-left">
    <div class="inline-edit-patreon-amount-cents-wrap">

      <span class="bulk-edit-title title"><?php
        _ex( 'Patreon Amount Cents (Override)', 'Patreon amount cents bulk edit label.', 'fictioneer' );
      ?></span>

      <div>
        <input type="number" name="fictioneer_bulk_edit_patreon_lock_amount" autocomplete="off" min="0">
      </div>

    </div>
  </fieldset>
  <?php // <--- End HTML
}

/**
 * Saves bulk edit Patreon meta.
 *
 * @since 5.27.2
 * @link https://developer.wordpress.org/reference/hooks/bulk_edit_posts/
 *
 * @param int[] $updated_post_ids  An array of updated post IDs.
 * @param int[] $shared_post_data  Associative array containing the post data.
 */

function fictioneer_save_patreon_bulk_edit( $updated_post_ids, $shared_post_data ) {
  // Validate
  if (
    ! wp_verify_nonce( $shared_post_data['fictioneer_bulk_edit_patreon_nonce'] ?? 0, 'fictioneer_bulk_edit_patreon' ) ||
    ( $shared_post_data['action2'] ?? 0 ) === 'trash' ||
    ! in_array(
      $shared_post_data['post_type'] ?? 0,
      ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation']
    ) ||
    ! ( current_user_can( 'manage_options' ) || current_user_can( 'fcn_assign_patreon_tiers' ) ) ||
    empty( $updated_post_ids )
  ) {
    return;
  }

  global $wpdb;

  // Setup
  $tiers = $shared_post_data['fictioneer_bulk_edit_patreon_lock_tiers'] ?? 'no_change';
  $amount = sanitize_text_field( $shared_post_data['fictioneer_bulk_edit_patreon_lock_amount'] ?? '' );
  $user_id = get_current_user_id();

  // Nothing to do?
  if ( $tiers === 'no_change' && $amount === '' ) {
    return;
  }

  // Prepare post author map
  $post_author_map = [];

  $posts_and_authors = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT ID, post_author
      FROM {$wpdb->posts}
      WHERE ID IN (" . implode( ',', array_fill( 0, count( $updated_post_ids ), '%d' ) ) . ")",
      $updated_post_ids
    ), ARRAY_A
  );

  foreach ( $posts_and_authors as $post ) {
    $post_author_map[ (int) $post['ID'] ] = (int) $post['post_author'];
  }

  // Update Patreon tiers
  if ( $tiers !== 'no_change' ) {
    foreach ( $updated_post_ids as $post_id ) {
      if ( ! current_user_can( 'manage_options' ) && $user_id !== $post_author_map[ (int) $post_id ] ) {
        continue;
      }

      if ( $tiers === 'remove' ) {
        fictioneer_update_post_meta( $post_id, 'fictioneer_patreon_lock_tiers', 0 );
      } elseif ( is_array( $tiers ) ) {
        $allowed_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
        $allowed_tiers = is_array( $allowed_tiers ) ? $allowed_tiers : [];

        if ( ! empty( $allowed_tiers ) ) {
          $tiers = array_intersect( $tiers, array_keys( $allowed_tiers ) );
          $tiers = array_map( 'strval', $tiers ); // Safer to match with LIKE in SQL
          $tiers = array_unique( $tiers );

          fictioneer_update_post_meta( $post_id, 'fictioneer_patreon_lock_tiers', $tiers );
        }
      }
    }
  }

  // Update Patreon amount cents
  if ( $amount !== '' ) {
    foreach ( $updated_post_ids as $post_id ) {
      if ( ! current_user_can( 'manage_options' ) && $user_id !== $post_author_map[ (int) $post_id ] ) {
        continue;
      }

      fictioneer_update_post_meta( $post_id, 'fictioneer_patreon_lock_amount', absint( $amount ) );
    }
  }
}

if (
  get_option( 'fictioneer_enable_patreon_locks' ) &&
  ( current_user_can( 'manage_options' ) || current_user_can( 'fcn_assign_patreon_tiers' ) )
) {
  foreach ( ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'] as $type ) {
    add_filter( "manage_{$type}_posts_columns", 'fictioneer_add_posts_columns_patreon' );
    add_action( "manage_{$type}_posts_custom_column", 'fictioneer_manage_posts_column_patreon', 10, 2 );
  }

  add_action( 'bulk_edit_custom_box',  'fictioneer_add_patreon_bulk_edit_tiers', 10, 2 );
  add_action( 'bulk_edit_custom_box',  'fictioneer_add_patreon_bulk_edit_amount', 10, 2 );
  add_action( 'bulk_edit_posts', 'fictioneer_save_patreon_bulk_edit', 10, 2 );
}

// =============================================================================
// CHAPTER LIST VIEW AND BULK EDIT
// =============================================================================

/**
 * Saves bulk edit chapter meta.
 *
 * @since 5.27.2
 * @link https://developer.wordpress.org/reference/hooks/bulk_edit_posts/
 *
 * @param int[] $updated_post_ids  An array of updated post IDs.
 * @param int[] $shared_post_data  Associative array containing the post data.
 */

function fictioneer_save_chapter_bulk_edit( $updated_post_ids, $shared_post_data ) {
  // Validate
  if (
    ! wp_verify_nonce( $shared_post_data['fictioneer_bulk_edit_chapters_nonce'] ?? 0, 'fictioneer_bulk_edit_chapters' ) ||
    ( $shared_post_data['action2'] ?? 0 ) === 'trash' ||
    ( $shared_post_data['screen'] ?? 0 ) !== 'edit-fcn_chapter' ||
    ( $shared_post_data['post_type'] ?? 0 ) !== 'fcn_chapter' ||
    empty( $updated_post_ids )
  ) {
    return;
  }

  global $wpdb;

  // Setup
  $story_id = sanitize_text_field( $shared_post_data['bulk_edit_fictioneer_chapter_story_id'] ?? '' );
  $icon = sanitize_text_field( $shared_post_data['bulk_edit_fictioneer_chapter_icon'] ?? '' );
  $text_icon = sanitize_text_field( $shared_post_data['bulk_edit_fictioneer_chapter_text_icon'] ?? '' );
  $prefix = sanitize_text_field( $shared_post_data['bulk_edit_fictioneer_chapter_prefix'] ?? '' );
  $group = sanitize_text_field( $shared_post_data['bulk_edit_fictioneer_chapter_group'] ?? '' );
  $user_id = get_current_user_id();
  $user_is_editor = current_user_can( 'edit_others_fcn_chapters' ) || current_user_can( 'manage_options' );
  $update_fields = [];

  // Nothing to do?
  if ( $story_id === '' && $icon === '' && $text_icon === '' && $prefix === '' && $group === '' ) {
    return;
  }

  // Prepare post author map
  $post_author_map = [];

  $posts_and_authors = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT ID, post_author
      FROM {$wpdb->posts}
      WHERE ID IN (" . implode( ',', array_fill( 0, count( $updated_post_ids ), '%d' ) ) . ")",
      $updated_post_ids
    ), ARRAY_A
  );

  foreach ( $posts_and_authors as $post ) {
    $post_author_map[ (int) $post['ID'] ] = (int) $post['post_author'];
  }

  // Icon
  if ( $icon ) {
    foreach ( $updated_post_ids as $post_id ) {
      if ( ! $user_is_editor && $user_id !== $post_author_map[ (int) $post_id ] ) {
        continue;
      }

      if ( strpos( $icon, 'fa-' ) === 0 && $icon !== FICTIONEER_DEFAULT_CHAPTER_ICON ) {
        $update_fields['fictioneer_chapter_icon'] = $icon;
      } elseif ( $icon === '_remove' ) {
        $update_fields['fictioneer_chapter_icon'] = 0;
      }
    }
  }

  // Text icon
  if ( $text_icon && get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
    foreach ( $updated_post_ids as $post_id ) {
      if ( ! $user_is_editor && $user_id !== $post_author_map[ (int) $post_id ] ) {
        continue;
      }

      if ( $text_icon === '_remove' ) {
        $update_fields['fictioneer_chapter_text_icon'] = 0;
      } else {
        $update_fields['fictioneer_chapter_text_icon'] = mb_substr( $text_icon, 0, 10, 'UTF-8' );
      }
    }
  }

  // Prefix
  if ( $prefix && get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
    foreach ( $updated_post_ids as $post_id ) {
      if ( ! $user_is_editor && $user_id !== $post_author_map[ (int) $post_id ] ) {
        continue;
      }

      if ( $prefix === '_remove' ) {
        $update_fields['fictioneer_chapter_prefix'] = 0;
      } else {
        $update_fields['fictioneer_chapter_prefix'] = $prefix;
      }
    }
  }

  // Chapter group
  if ( $group ) {
    foreach ( $updated_post_ids as $post_id ) {
      if ( ! $user_is_editor && $user_id !== $post_author_map[ (int) $post_id ] ) {
        continue;
      }

      if ( $group === '_remove' ) {
        $update_fields['fictioneer_chapter_group'] = 0;
      } else {
        $update_fields['fictioneer_chapter_group'] = $group;
      }
    }
  }

  // Perform meta updates
  if ( ! empty( $update_fields ) ) {
    if ( count( $update_fields ) < 2 ) {
      foreach ( $updated_post_ids as $post_id ) {
        reset( $update_fields );

        fictioneer_update_post_meta( $post_id, key( $update_fields ), current( $update_fields ) );
      }
    } else {
      foreach ( $updated_post_ids as $post_id ) {
        fictioneer_bulk_update_post_meta( $post_id, $update_fields );
      }
    }
  }

  // Update story ID
  if ( $story_id !== '' ) {
    $story_id = intval( $story_id );
    $story_author_id = (int) get_post_field( 'post_author', $story_id );

    if ( $user_is_editor || $story_author_id === $user_id ) {
      if ( $story_id < 1 || fictioneer_validate_id( $story_id, 'fcn_story' ) ) {
        fictioneer_bulk_edit_chapter_story( $updated_post_ids, $story_id, $post_author_map );
      }
    }
  }

  // Clear story meta caches (if not already done)
  if ( $story_id === '' ) {
    $updated_stories = [];

    foreach ( $updated_post_ids as $chapter_id ) {
      $current_story_id = intval( fictioneer_get_chapter_story_id( $chapter_id ) );

      if ( ! $current_story_id ) {
        continue;
      }

      if ( ! in_array( $current_story_id, $updated_stories ) ) {
        wp_update_post( array( 'ID' => $current_story_id, 'post_type' => 'fcn_story' ) ); // Trigger hooks

        $updated_stories[] = $current_story_id;
      }
    }
  }
}
add_action( 'bulk_edit_posts', 'fictioneer_save_chapter_bulk_edit', 10, 2 );

/**
 * Bulk edit chapter stories.
 *
 * @since 5.27.2
 *
 * @param int[] $chapter_ids  Array of post IDs.
 * @param int   $story_id     ID of the story or 0 to unset.
 * @param int[] $author_map   Array of post ID => author ID.
 */

function fictioneer_bulk_edit_chapter_story( $chapter_ids, $story_id, $author_map ) {
  // Setup
  $user_id = get_current_user_id();
  $user_is_editor = current_user_can( 'edit_others_fcn_chapters' ) || current_user_can( 'manage_options' );
  $updated_stories = [];
  $allowed_chapter_ids = $user_is_editor
    ? $chapter_ids
    : array_filter( $chapter_ids, fn( $post_id ) => $user_id === $author_map[ (int) $post_id ] );

  // Unset old story
  foreach ( $allowed_chapter_ids as $chapter_id ) {
    $current_story_id = intval( fictioneer_get_chapter_story_id( $chapter_id ) );

    if ( ! $current_story_id || $current_story_id === $story_id ) {
      continue;
    }

    $other_story_chapters = fictioneer_get_story_chapter_ids( $current_story_id );
    $other_story_chapters = array_diff( $other_story_chapters, [ strval( $chapter_id ) ] );

    update_post_meta( $current_story_id, 'fictioneer_story_chapters', $other_story_chapters );

    if ( $story_id < 1 ) {
      delete_post_meta( $chapter_id, 'fictioneer_chapter_story' );
      fictioneer_set_chapter_story_parent( $chapter_id, 0 );
    }

    if ( ! in_array( $current_story_id, $updated_stories ) ) {
      update_post_meta( $current_story_id, 'fictioneer_chapters_modified', current_time( 'mysql', 1 ) );
      wp_update_post( array( 'ID' => $current_story_id, 'post_type' => 'fcn_story' ) ); // Trigger hooks

      $updated_stories[] = $current_story_id;
    }
  }

  // Assign new story
  if ( $story_id > 0 ) {
    $previous_story_chapter_ids = fictioneer_get_story_chapter_ids( $story_id );
    $new_story_chapter_ids = $previous_story_chapter_ids;

    foreach ( $allowed_chapter_ids as $chapter_id ) {
      if ( ! in_array( $chapter_id, $previous_story_chapter_ids ) ) {
        update_post_meta( $chapter_id, 'fictioneer_chapter_story', $story_id );
        fictioneer_set_chapter_story_parent( $chapter_id, $story_id );

        if ( get_option( 'fictioneer_enable_chapter_appending' ) ) {
          $new_story_chapter_ids[] = $chapter_id;
        }
      }
    }

    $new_story_chapter_ids = array_unique( $new_story_chapter_ids );

    if ( $previous_story_chapter_ids !== $new_story_chapter_ids ) {
      update_post_meta( $story_id, 'fictioneer_story_chapters', array_map( 'strval', $new_story_chapter_ids ) );
      update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql', 1 ) );
      update_post_meta( $story_id, 'fictioneer_chapters_added', current_time( 'mysql', 1 ) );
      wp_update_post( array( 'ID' => $story_id, 'post_type' => 'fcn_story' ) ); // Trigger hooks
    }
  }
}

/**
 * Adds chapter meta fields to bulk edit.
 *
 * @since 5.24.1
 *
 * @param string $column_name  Name of the column to edit.
 * @param string $post_type    The post type slug.
 */

function fictioneer_add_bulk_edit_chapter_meta( $column_name, $post_type ) {
  static $added = false;

  // Abort if...
  if ( $post_type !== 'fcn_chapter' || $added ) {
    return;
  }

  $added = true;

  // Nonce
  wp_nonce_field( 'fictioneer_bulk_edit_chapters', 'fictioneer_bulk_edit_chapters_nonce', false );

  // Start HTML ---> ?>
  <?php if ( current_user_can( 'edit_others_fcn_chapters' ) || current_user_can( 'manage_options' ) ) : ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-chapter-story-wrap">
        <label class="inline-edit-chapter-story">
          <span class="title"><?php _ex( 'Story', 'Chapter story meta field label.', 'fictioneer' ); ?></span>
          <input type="text" name="bulk_edit_fictioneer_chapter_story_id" autocomplete="off" autocorrect="off" placeholder="<?php _e( 'Post ID (0 to remove)', 'fictioneer' ); ?>">
        </label>
        </div>
    </fieldset>
  <?php endif; ?>

  <p class="bulk-edit-help"><?php _e( 'Use <code>_remove</code> to remove current values.', 'fictioneer' ); ?></p>

  <?php if ( ! get_option( 'fictioneer_hide_chapter_icons' ) ) : ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-chapter-icon-wrap">
        <label class="inline-edit-chapter-icon">
          <span class="title"><?php _ex( 'Icon', 'Chapter icon meta field label.', 'fictioneer' ); ?></span>
          <input type="text" name="bulk_edit_fictioneer_chapter_icon" autocomplete="off" autocorrect="off">
        </label>
      </div>
    </fieldset>
    <?php if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) ) : ?>
      <fieldset class="inline-edit-col-right">
        <div class="inline-edit-chapter-text-icon-wrap">
          <label class="inline-edit-chapter-text-icon">
            <span class="title"><?php _ex( 'Text Icon', 'Chapter text icon meta field label.', 'fictioneer' ); ?></span>
            <input type="text" name="bulk_edit_fictioneer_chapter_text_icon" autocomplete="off" autocorrect="off">
          </label>
        </div>
      </fieldset>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) ) : ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-chapter-prefix-wrap">
        <label class="inline-edit-chapter-prefix">
          <span class="title"><?php _ex( 'Prefix', 'Chapter prefix meta field label.', 'fictioneer' ); ?></span>
          <input type="text" name="bulk_edit_fictioneer_chapter_prefix" autocomplete="off" autocorrect="off">
        </label>
      </div>
    </fieldset>
  <?php endif; ?>

  <fieldset class="inline-edit-col-right">
    <div class="inline-edit-chapter-group-wrap">
      <label class="inline-edit-chapter-group">
        <span class="title"><?php _ex( 'Group', 'Chapter group meta field label.', 'fictioneer' ); ?></span>
        <input type="text" name="bulk_edit_fictioneer_chapter_group" autocomplete="off" autocorrect="off">
      </label>
    </div>
  </fieldset>
  <?php // <--- End HTML
}
add_action( 'bulk_edit_custom_box', 'fictioneer_add_bulk_edit_chapter_meta', 10, 2 );

// =============================================================================
// PASSWORD EXPIRATION NOTE IN POST TABLE
// =============================================================================

/**
 * Renders expiration date in post list table password note
 *
 * @since 5.25.0
 *
 * @param string[] $post_states  An array of post display states.
 * @param WP_Post  $post         The current post object.
 *
 * @return string[] Filtered post display states used in the posts list table.
 */

function fictioneer_add_post_table_pw_expiration( $post_states, $post ) {
  if ( isset( $post_states['protected'] ) ) {
    $expiration_date = get_post_meta( $post->ID, 'fictioneer_post_password_expiration_date', true );

    if ( $expiration_date ) {
      $timestamp = strtotime( $expiration_date );

      $post_states['protected'] = sprintf(
        _x( '%1$s (until %2$s)', 'Expiration suffix for protected note in post list tables.', 'fictioneer' ),
        $post_states['protected'],
        sprintf(
          __( '%1$s at %2$s' ), // WP post table default
          date_i18n( __( 'Y/m/d' ), $timestamp ), // WP post table default
          date_i18n( __( 'g:i a' ), $timestamp ) // WP post table default
        )
      );
    }
  }

  return $post_states;
}
add_filter( 'display_post_states', 'fictioneer_add_post_table_pw_expiration', 10, 2 );

// =============================================================================
// STORY IN CHAPTER LIST TABLE
// =============================================================================

/**
 * Hide chapter story column in chapter list table view by default
 *
 * @since 5.25.0
 * @link https://developer.wordpress.org/reference/hooks/default_hidden_columns/
 *
 * @param string[]  $hidden  Array of IDs of columns hidden by default.
 * @param WP_Screen $screen  WP_Screen object of the current screen.
 *
 * @return string[] Updated array of IDs.
 */

function fictioneer_default_hide_chapter_story_column( $hidden, $screen ) {
  if ( $screen->post_type === 'fcn_chapter' ) {
    $hidden[] = 'fictioneer_chapter_story';
  }

  return $hidden;
}
add_filter( 'default_hidden_columns', 'fictioneer_default_hide_chapter_story_column', 10, 2 );

/**
 * Add chapter story column to chapter list table view
 *
 * @since 5.25.0
 * @link https://developer.wordpress.org/reference/hooks/manage_post_type_posts_columns/
 *
 * @param string[] $post_columns  An associative array of column headings.
 *
 * @return string[] Updated associative array of column headings.
 */

function fictioneer_add_posts_column_chapter_story( $post_columns ) {
  return $post_columns = array_merge(
    array_slice( $post_columns, 0, 2, true ),
    array(
      'fictioneer_chapter_story' => _x( 'Story', 'Chapter story list table column title.', 'fictioneer' )
    ),
    array_slice( $post_columns, 2, null, true )
  );
}
add_filter( 'manage_fcn_chapter_posts_columns', 'fictioneer_add_posts_column_chapter_story' );

/**
 * Output chapter story value in chapter list table view
 *
 * @since 5.25.0
 * @link https://developer.wordpress.org/reference/hooks/manage_post-post_type_posts_custom_column/
 *
 * @param string $column_name  The name of the column to display.
 * @param int    $post_id      The current post ID.
 */

function fictioneer_manage_posts_column_chapter_story( $column_name, $post_id ) {
  if (
    $column_name === 'fictioneer_chapter_story' &&
    ( $story_id = fictioneer_get_chapter_story_id( $post_id ) )
  ) {
    static $titles = [];

    if ( ! isset( $titles[ $story_id ] ) ) {
      $titles[ $story_id ] = '<a href="edit.php?post_type=fcn_chapter&filter_story=' .
        absint( $story_id ) . '">' . fictioneer_truncate( fictioneer_get_safe_title( $story_id ), 64 ) . '</a>';
    }

    echo $titles[ $story_id ];
  }
}
add_action( 'manage_fcn_chapter_posts_custom_column', 'fictioneer_manage_posts_column_chapter_story', 10, 2 );
