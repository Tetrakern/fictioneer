<?php

/**
 * Sanitizes editor content
 *
 * Removes malicious HTML, shortcodes, and blocks.
 *
 * @since 5.7.4
 *
 * @param string $content  The content to be sanitized
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

/**
 * Returns HTML for a text meta field
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

function fictioneer_get_metabox_text( $post, $meta_key, $args = [] ) {
  // Setup
  $meta_value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $list = ( $args['list'] ?? 0 ) ? 'list="' . $args['list'] . '"' : '';
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
      <input type="text" id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__input" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $list; ?> <?php echo $required; ?>>
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

/**
 * Returns HTML for a select meta field
 *
 * @since 5.7.4
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
 * }
 *
 * @return string The HTML markup for the field.
 */

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

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--textarea" <?php echo $data_required; ?>>

    <?php if ( $label ) : ?>
      <label class="fictioneer-meta-field__label" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
    <?php endif; ?>

    <input type="hidden" name="<?php echo $meta_key; ?>" value="0" autocomplete="off">

    <div class="fictioneer-meta-field__wrapper">
      <textarea id="<?php echo $meta_key; ?>" class="fictioneer-meta-field__textarea <?php echo $input_classes; ?>" name="<?php echo $meta_key; ?>" placeholder="<?php echo $placeholder; ?>" autocomplete="off" <?php echo $required; ?>><?php echo $meta_value; ?></textarea>
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
 * }
 *
 * @return string The HTML markup for the field.
 */

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
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );
  $placeholder = strval( $args['placeholder'] ?? '' );
  $required = ( $args['required'] ?? 0 ) ? 'required' : '';
  $data_required = $required ? 'data-required="true"' : '';
  $current_icon_class = fictioneer_get_icon_field( $meta_key, $post->ID );

  $icons = ['fa-solid fa-book', 'fa-solid fa-star', 'fa-solid fa-heart', 'fa-solid fa-bomb', 'fa-solid fa-wine-glass',
    'fa-solid fa-face-smile', 'fa-solid fa-shield', 'fa-solid fa-ghost', 'fa-solid fa-gear', 'fa-solid fa-droplet',
    'fa-solid fa-fire', 'fa-solid fa-radiation', 'fa-solid fa-lemon', 'fa-solid fa-globe', 'fa-solid fa-flask',
    'fa-solid fa-snowflake', 'fa-solid fa-cookie', 'fa-solid fa-circle', 'fa-solid fa-square', 'fa-solid fa-moon',
    'fa-solid fa-brain', 'fa-solid fa-diamond', 'fa-solid fa-virus', 'fa-solid fa-horse-head', 'fa-solid fa-certificate',
    'fa-solid fa-scroll', 'fa-solid fa-spa', 'fa-solid fa-skull'];

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
        foreach( $icons as $icon ) {
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
      <label class="fictioneer-meta-field__label fictioneer-meta-field__label--editor" for="<?php echo $meta_key; ?>"><?php echo $label; ?></label>
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
 * Returns HTML for an ACF meta field
 *
 * @since 5.7.4
 *
 * @param WP_Post $post       The post.
 * @param string  $field_key  The ACF field key.
 * @param array   $args {
 *   Optional. An array of additional arguments.
 *
 *   @type string $label        Label above the field.
 *   @type string $description  Description below the field.
 * }
 *
 * @return string The HTML markup for the field.
 */

function fictioneer_get_acf_field( $post, $field_key, $args = [] ) {
  // Setup
  $acf_field = get_field_object( $field_key, $post->ID );
  $acf_field['label'] = '';
  $acf_field['instructions'] = '';
  $label = strval( $args['label'] ?? '' );
  $description = strval( $args['description'] ?? '' );

  // Abort if field object not found
  if ( ! $acf_field ) {
    return;
  }

  ob_start();

  // Start HTML ---> ?>
  <div class="fictioneer-meta-field fictioneer-meta-field--acf <?php echo $field_key; ?>">

    <?php if ( $label ) : ?>
      <div class="fictioneer-meta-field__label fictioneer-meta-field__label--acf"><?php echo $label; ?></div>
    <?php endif; ?>

    <div class="fictioneer-meta-field__wrapper fictioneer-meta-field__wrapper--acf">
      <?php acf_render_field_wrap( $acf_field ); ?>
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
  add_filter( "postbox_classes_{$type}_fictioneer-advanced", 'fictioneer_append_metabox_classes' );
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
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_story_meta_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "story_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields ------------------------------------------------------------

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

  if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
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
      'description' => __( 'URL to Top Web Fiction page.', 'fictioneer' )
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
        'placeholder' => __( '.selector { ... }', 'fictioneer' ),
        'input_classes' => 'fictioneer-meta-field__textarea--code'
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
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_story_data_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "story_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields ------------------------------------------------------------

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
  $output['fictioneer_story_chapters'] = fictioneer_get_acf_field(
    $post,
    'field_5d6304e4330fd',
    array(
      'label' => _x( 'Chapters', 'Story chapters meta field label.', 'fictioneer' ),
      'description' => __( 'Select and order chapters assigned to the story (set in the chapter).', 'fictioneer' )
    )
  );

  // Custom pages
  if ( current_user_can( 'fcn_story_pages', $post->ID ) ) {
    $output['fictioneer_story_custom_pages'] = fictioneer_get_acf_field(
      $post,
      'field_5d6e33e253add',
      array(
        'label' => _x( 'Custom Pages', 'Story custom pages meta field label.', 'fictioneer' ),
        'description' => __( 'Add up to six pages as tabs to stories. Pages must have a short name or will not be shown.', 'fictioneer' )
      )
    );
  }

  // Password note
  $output['fictioneer_story_password_note'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_story_password_note',
    array(
      'label' => _x( 'Password Note', 'Story password note meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed for password protected content. Limited HTML allowed.', 'fictioneer' )
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
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_story_epub_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "story_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields ------------------------------------------------------------

  // Upload
  $output['fictioneer_story_ebook_upload_one'] = fictioneer_get_acf_field(
    $post,
    'field_62eb89244fb11',
    array(
      'label' => _x( 'Custom Upload', 'Story ebook upload meta field label.', 'fictioneer' ),
      'description' => __( 'If you do not want to rely on the ePUB converter, you can upload your own ebook. Allowed formats are <strong>epub</strong>, <strong>mobi</strong>, <strong>ibooks</strong>, <strong>azw</strong>, <strong>azw3</strong>, <strong>kf8</strong>, <strong>kfx</strong>, <strong>pdf</strong>, <strong>iba</strong>, <strong>rtf</strong>, and <strong>txt</strong>.', 'fictioneer' )
    )
  );

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
        'input_classes' => 'fictioneer-meta-field__textarea--code'
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
 * Note: Must use the general 'save_post' hook to fire after ACF.
 *
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_story_metaboxes( $post_id ) {
  // --- Verify ----------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_story_nonce'] ?? '' ), "story_meta_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_story'
  ) {
    return;
  }

  // --- Permissions? ----------------------------------------------------------

  if (
    ! current_user_can( 'edit_fcn_stories', $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( 'edit_published_fcn_stories', $post_id ) )
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
    $twf_url = sanitize_url( $_POST['fictioneer_story_topwebfiction_link'] );
    $twf_url = filter_var( $twf_url, FILTER_VALIDATE_URL ) ? $twf_url : '';
    $twf_url = strpos( $twf_url, 'https://topwebfiction.com/') === 0 ? $twf_url : '';
    $fields['fictioneer_story_topwebfiction_link'] = $twf_url;
  }

  // Co-Authors
  if ( isset( $_POST['fictioneer_story_co_authors'] ) && get_option( 'fictioneer_enable_advanced_meta_fields' ) ){
    $co_authors = fictioneer_explode_list( $_POST['fictioneer_story_co_authors'] );
    $co_authors = array_map( 'absint', $co_authors );
    $co_authors = array_filter( $co_authors, function( $user_id ) {
      return get_userdata( $user_id ) !== false;
    });
    $fields['fictioneer_story_co_authors'] = array_unique( $co_authors );
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
      $fields['fictioneer_story_short_description'] = __( 'No description provided yet.', 'fictioneer' );
    }
  }

  // Chapters (already saved by ACF; restrict by story)
  $chapters = get_post_meta( $post_id, 'fictioneer_story_chapters', true );

  $chapters_query = new WP_Query(
    array(
      'post_type' => 'fcn_chapter',
      'post__in' => fictioneer_rescue_array_zero( $chapters ),
      'orderby' => 'post__in',
      'fields' => 'ids',
      'posts_per_page' => -1,
      'meta_key' => 'fictioneer_chapter_story',
      'meta_value' => $post_id,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    )
  );

  $fields['fictioneer_story_chapters'] = array_map( 'strval', $chapters_query->posts ); // This has query advantages
  $fields['_fictioneer_story_chapters'] = 0; // Remove unnecessary ACF control field

  // Custom pages (already saved by ACF; restrict by post author)
  $pages = get_post_meta( $post_id, 'fictioneer_story_custom_pages', true );

  if ( current_user_can( 'fcn_story_pages', $post_id ) ) {
    $pages_query = new WP_Query(
      array(
        'post_type' => 'page',
        'post__in' => fictioneer_rescue_array_zero( $pages ),
        'author' => $post_author_id, // Only allow author's pages
        'fields' => 'ids',
        'posts_per_page' => -1,
        'update_post_meta_cache' => false, // Improve performance
        'update_post_term_cache' => false, // Improve performance
        'no_found_rows' => true // Improve performance
      )
    );

    $fields['fictioneer_story_custom_pages'] = array_map( 'strval', $pages_query->posts ); // This has query advantages
  } elseif ( isset( $pages ) && empty( $pages ) ) {
    $fields['fictioneer_story_custom_pages'] = []; // Ensure empty ACF meta is removed
  }

  $fields['_fictioneer_story_custom_pages'] = 0; // Remove unnecessary ACF control field

  // Password note
  if ( isset( $_POST['fictioneer_story_password_note'] ) ) {
    $fields['fictioneer_story_password_note'] =
      fictioneer_sanitize_editor( $_POST['fictioneer_story_password_note'] );
  }

  // ePUBs...
  $custom_ebook = get_post_meta( $post_id, 'fictioneer_story_ebook_upload_one', true );

  if ( isset( $custom_ebook ) && empty( $custom_ebook ) ) {
    $fields['fictioneer_story_ebook_upload_one'] = 0; // Ensure empty ACF meta is removed
  }

  $fields['_fictioneer_story_ebook_upload_one'] = 0; // Remove unnecessary ACF control field

  if ( get_option( 'fictioneer_enable_epubs' ) ) {
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

  $fields = apply_filters( 'fictioneer_filter_field_updates_story', $fields, $post_id );

  // --- Save ------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_story_metaboxes' );

// =============================================================================
// CHAPTER META FIELDS
// =============================================================================

/**
 * Adds chapter meta metabox
 *
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_chapter_meta_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "chapter_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields ------------------------------------------------------------

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
        'placeholder' => 'fa-solid fa-book'
      )
    );
  }

  if ( get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
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
        'description' => __( 'Shorter title, such as "Arc 15, Ch. 17".', 'fictioneer' )
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
      'Everyone' => _x( 'Everyone', 'Chapter age rating select option.', 'fictioneer' ),
      'Teen' => _x( 'Teen', 'Chapter age rating select option.', 'fictioneer' ),
      'Mature' => _x( 'Mature', 'Chapter age rating select option.', 'fictioneer' ),
      'Adult' => _x( 'Adult', 'Chapter age rating select option.', 'fictioneer' )
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
      'description' => __( 'Warning shown in chapter lists.', 'fictioneer' )
    )
  );

  $output['fictioneer_chapter_warning_notes'] = fictioneer_get_metabox_textarea(
    $post,
    'fictioneer_chapter_warning_notes',
    array(
      'label' => __( 'Warning Notes', 'fictioneer' ),
      'description' => __( 'Warning note shown in chapters.', 'fictioneer' )
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
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_chapter_data_metabox( $post ) {
  // --- Setup -----------------------------------------------------------------

  global $wpdb;

  $nonce = wp_create_nonce( "chapter_meta_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $post_author_id = get_post_field( 'post_author', $post->ID );
  $current_story_id = get_post_meta( $post->ID, 'fictioneer_chapter_story', true );
  $output = [];

  // --- Add fields ------------------------------------------------------------

  // Story
  $stories = array( '0' => _x( '— Unassigned —', 'Chapter story select option.', 'fictioneer' ) );
  $description = __( 'Select the story this chapter belongs to; assign and order in the story.', 'fictioneer' );
  $author_warning = '';

  $author_stories = new WP_Query(
    array(
      'author' => $post_author_id,
      'post_type' => 'fcn_story',
      'post_status' => ['publish', 'private'],
      'posts_per_page'=> -1,
      'update_post_meta_cache' => false, // Improve performance
      'update_post_term_cache' => false, // Improve performance
      'no_found_rows' => true // Improve performance
    )
  );

  if ( $author_stories->have_posts() ) {
    foreach( $author_stories->posts as $story ) {
      if ( $story->post_status !== 'publish' ) {
        $status_object = get_post_status_object( $story->post_status );
        $status_label = $status_object ? $status_object->label : $story->post_status;

        $stories[ $story->ID ] = sprintf(
          _x( '%s (%s)', 'Chapter story meta field option with status label.', 'fictioneer' ),
          fictioneer_get_safe_title( $story->ID ),
          $status_label
        );
      } else {
        $stories[ $story->ID ] = fictioneer_get_safe_title( $story->ID );
      }
    }
  }

  if ( $current_story_id ) {
    // Check unmatched story assignment...
    if ( ! array_key_exists( $current_story_id, $stories ) ) {
      $other_author_id = get_post_field( 'post_author', $current_story_id );
      $suffix = [];

      // Other author
      if ( $other_author_id != $post_author_id ) {
        $suffix['author'] = get_the_author_meta( 'display_name', $other_author_id );
        $author_warning = __( '<strong>Warning:</strong> The selected story belongs to another author. If you change the selection, you cannot go back.', 'fictioneer' );
      }

      // Other status
      if ( get_post_status( $current_story_id ) === 'publish' ) {
        $status_object = get_post_status_object( $story->post_status );
        $status_label = $status_object ? $status_object->label : $story->post_status;

        $suffix['status'] = $status_label;
      }

      // Prepare suffix
      if ( ! empty( $suffix ) ) {
        $suffix = ' (' . implode( ' | ', $suffix ) . ')';
      }

      $stories[ $current_story_id ] = sprintf(
        _x( '%s %s', 'Chapter story meta field mismatched option with author and/or status label.', 'fictioneer' ),
        fictioneer_get_safe_title( $current_story_id ),
        $suffix
      );
    }
  }

  if ( get_option( 'fictioneer_enable_chapter_appending' ) ) {
    $description = __( 'Select the story this chapter belongs to; new <em>published</em> chapters are automatically appended to the story.', 'fictioneer' );
  }

  $output['fictioneer_chapter_story'] = fictioneer_get_metabox_select(
    $post,
    'fictioneer_chapter_story',
    $stories,
    array(
      'label' => _x( 'Story', 'Chapter story meta field label.', 'fictioneer' ),
      'description' => $description . ' ' . $author_warning
    )
  );

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
  $current_story = fictioneer_get_story_data( $current_story_id, false );
  $groups = [];

  if ( $current_story && ! empty( $current_story['chapter_ids'] ) ) {
    $post_ids_format = implode( ', ', array_fill( 0, count( $current_story['chapter_ids'] ), '%d' ) );

    $sql = $wpdb->prepare(
      "SELECT post_id, meta_value
      FROM $wpdb->postmeta
      WHERE meta_key = 'fictioneer_chapter_group'
      AND meta_value != ''
      AND post_id IN ($post_ids_format)",
      $current_story['chapter_ids']
    );

    $results = $wpdb->get_results( $sql );

    foreach ( $results as $result ) {
      if ( $result->meta_value ) {
        $groups[] = $result->meta_value;
      }
    }

    $groups = array_unique( $groups );
  }

  $output['fictioneer_chapter_group'] = fictioneer_get_metabox_text(
    $post,
    'fictioneer_chapter_group',
    array(
      'label' => _x( 'Group', 'Chapter group meta field label.', 'fictioneer' ),
      'description' => __( 'Organize chapters into groups; mind the order and spelling (case-sensitive). Only rendered if there are at least two, unassigned chapters are grouped under "Unassigned".', 'fictioneer' ),
      'list' => "chapter_groups_for_{$post->ID}"
    )
  );

  if ( ! empty( $groups ) ) {
    $datalist = "<datalist id='chapter_groups_for_{$post->ID}'>";

    foreach ( $groups as $group ) {
      $datalist .= "<option value='$group'></option>";
    }

    $datalist .= '</datalist>';

    $output['fictioneer_chapter_group_datalist'] = $datalist;
  }

  // Foreword
  $output['fictioneer_chapter_foreword'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_chapter_foreword',
    array(
      'label' => _x( 'Foreword', 'Chapter foreword meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed in a box above the chapter. Limited HTML allowed.', 'fictioneer' )
    )
  );

  // Afterword
  $output['fictioneer_chapter_afterword'] = fictioneer_get_metabox_editor(
    $post,
    'fictioneer_chapter_afterword',
    array(
      'label' => _x( 'Afterword', 'Chapter afterword meta field label.', 'fictioneer' ),
      'description' => __( 'Displayed in a box below the chapter. Limited HTML allowed.', 'fictioneer' )
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
 * Note: Must use the general 'save_post' hook to fire after ACF.
 *
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_chapter_metaboxes( $post_id ) {
  // --- Verify ----------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_chapter_nonce'] ?? '' ), "chapter_meta_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_chapter'
  ) {
    return;
  }

  // --- Permissions? ----------------------------------------------------------

  if (
    ! current_user_can( 'edit_fcn_chapters', $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( 'edit_published_fcn_chapters', $post_id ) )
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
      $icon = 'fa-solid fa-book';
    }

    if ( $icon_object && ( ! property_exists( $icon_object, 'style' ) || ! property_exists( $icon_object, 'id' ) ) ) {
      $icon = 'fa-solid fa-book';
    }

    $fields['fictioneer_chapter_icon'] = $icon;
  }

  // Text icon
  if ( isset( $_POST['fictioneer_chapter_text_icon'] ) && get_option( 'fictioneer_enable_advanced_meta_fields' ) ) {
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
    $fields['fictioneer_chapter_co_authors'] = array_unique( $co_authors );
  }

  // Warning
  if ( isset( $_POST['fictioneer_chapter_warning'] ) ) {
    $fields['fictioneer_chapter_warning'] = sanitize_text_field( $_POST['fictioneer_chapter_warning'] );
  }

  // Warning notes
  if ( isset( $_POST['fictioneer_chapter_warning_notes'] ) ) {
    $notes = sanitize_textarea_field( $_POST['fictioneer_chapter_warning_notes'] );
    $fields['fictioneer_chapter_warning_notes'] = $notes;
  }

  // Story
  if ( isset( $_POST['fictioneer_chapter_story'] ) ) {
    $story_id = absint( $_POST['fictioneer_chapter_story'] );
    $current_story_id = get_post_meta( $post_id, 'fictioneer_chapter_story', true );
    $current_story_id = absint( $current_story_id );

    if ( $story_id ) {
      $story_author_id = get_post_field( 'post_author', $story_id );

      if ( $story_author_id != $post_author_id && $current_story_id != $story_id ) {
        $story_id = 0;
      } else {
        fictioneer_append_chapter_to_story( $post_id, $story_id );
      }
    }

    $fields['fictioneer_chapter_story'] = strval( $story_id );
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

  $fields = apply_filters( 'fictioneer_filter_field_updates_chapter', $fields, $post_id );

  // --- Save ------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_chapter_metaboxes' );

/**
 * Append new chapters to story list
 *
 * @since Fictioneer 5.4.9
 * @since Fictioneer 5.7.4 - Updated
 *
 * @param int $post_id   The chapter post ID.
 * @param int $story_id  The story post ID.
 */

function fictioneer_append_chapter_to_story( $post_id, $story_id ) {
  if ( ! get_option( 'fictioneer_enable_chapter_appending' ) ) {
    return;
  }

  // Abort if older than 5 seconds
  $publishing_time = get_post_time( 'U', true, $post_id, true );
  $current_time = current_time( 'timestamp', true );

  if ( $current_time - $publishing_time > 5 ) {
    return;
  }

  // Setup
  $story = get_post( $story_id );

  // Abort if story not found
  if ( ! $story || ! $story_id ) {
    return;
  }

  // Setup, continued
  $chapter_author_id = get_post_field( 'post_author', $post_id );
  $story_author_id = get_post_field( 'post_author', $story_id );

  // Abort if the author IDs do not match
  if ( $chapter_author_id != $story_author_id ) {
    return;
  }

  // Get current story chapters
  $story_chapters = get_post_meta( $story_id, 'fictioneer_story_chapters', true );

  // Prepare chapter array if null (or broken)
  if ( ! is_array( $story_chapters ) ) {
    $story_chapters = [];
  }

  // Append chapter (if not already included) and save to database
  if ( ! in_array( $post_id, $story_chapters ) ) {
    $story_chapters[] = $post_id;

    // Append chapter to field
    update_post_meta( $story_id, 'fictioneer_story_chapters', array_unique( $story_chapters ) );

    // Remember when chapter list has been last updated
    update_post_meta( $story_id, 'fictioneer_chapters_modified', current_time( 'mysql' ) );
    update_post_meta( $story_id, 'fictioneer_chapters_added', current_time( 'mysql' ) );

    // Clear story data cache to ensure it gets refreshed
    delete_post_meta( $story_id, 'fictioneer_story_data_collection' );
  } else {
    // Nothing to do
    return;
  }

  // Update story post to fire associated actions
  wp_update_post( array( 'ID' => $story_id ) );
}

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
    'default'
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
  // --- Setup -----------------------------------------------------------------

  $nonce = wp_create_nonce( "advanced_meta_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];
  $author_id = $post->post_author ?: get_current_user_id();

  // --- Add fields ------------------------------------------------------------

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
        'placeholder' => __( '.selector { ... }', 'fictioneer' ),
        'input_classes' => 'fictioneer-meta-field__textarea--code'
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
    $output['flags_heading'] = '<div class="fictioneer-meta-field-heading">' .
      _x( 'Flags', 'Metabox checkbox heading.', 'fictioneer' ) . '</div>';

    $output['fictioneer_disable_commenting'] = fictioneer_get_metabox_checkbox(
      $post,
      'fictioneer_disable_commenting',
      __( 'Disable new comments', 'fictioneer' )
    );
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
 * Save advanced metabox
 *
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_advanced_metabox( $post_id ) {
  $post_type = get_post_type( $post_id );

  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_advanced_meta_nonce'] ?? '' ), "advanced_meta_{$post_id}" ) ||
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

  $post_author_id = get_post_field( 'post_author', $post_id );
  $fields = [];

  // Landscape Image
  if ( isset( $_POST['fictioneer_landscape_image'] ) ) {
    $fields['fictioneer_landscape_image'] = absint( $_POST['fictioneer_landscape_image'] );
  }

  // Custom Page Header
  if ( isset( $_POST['fictioneer_custom_header_image'] ) && current_user_can( 'fcn_custom_page_header', $post_id ) ) {
    $fields['fictioneer_custom_header_image'] = absint( $_POST['fictioneer_custom_header_image'] );
  }

  // Custom Page CSS
  if ( isset( $_POST['fictioneer_custom_css'] ) && current_user_can( 'fcn_custom_page_css', $post_id ) ) {
    $fields['fictioneer_custom_css'] = fictioneer_sanitize_css( $_POST['fictioneer_custom_css'] );
  }

  // Short Name
  if ( isset( $_POST['fictioneer_short_name'] ) && $post_type === 'page' ) {
    $fields['fictioneer_short_name'] = sanitize_text_field( $_POST['fictioneer_short_name'] );

    if ( empty( $fields['fictioneer_short_name'] ) ) {
      $fields['fictioneer_short_name'] = mb_strimwidth( get_the_title( $post_id ), 0, 16, '…' );
    }
  }

  // Search & Filter ID
  if ( isset( $_POST['fictioneer_filter_and_search_id'] ) && $post_type === 'page' ) {
    if ( current_user_can( 'install_plugins' ) ) {
      $fields['fictioneer_filter_and_search_id'] = absint( $_POST['fictioneer_filter_and_search_id'] );
    }
  }

  // Story Blogs
  if ( isset( $_POST['fictioneer_post_story_blogs'] ) && $post_type === 'post' ) {
    $story_blogs = fictioneer_explode_list( $_POST['fictioneer_post_story_blogs'] );

    if ( ! empty( $story_blogs ) ) {
      $story_blogs = array_map( 'absint', $story_blogs );
      $story_blogs = array_unique( $story_blogs );

      // Ensure the stories belong to the post author
      $blog_story_query = new WP_Query(
        array(
          'author' => $post_author_id,
          'post_type' => 'fcn_story',
          'post_status' => ['publish', 'private'],
          'post__in' => fictioneer_rescue_array_zero( $story_blogs ),
          'fields' => 'ids',
          'posts_per_page'=> -1,
          'update_post_meta_cache' => false, // Improve performance
          'update_post_term_cache' => false, // Improve performance
          'no_found_rows' => true // Improve performance
        )
      );

      $story_blogs = $blog_story_query->posts;
      $story_blogs = array_map( 'strval', $story_blogs ); // Safer to match with LIKE in SQL
    }

    $fields['fictioneer_post_story_blogs'] = $story_blogs;
  }

  // Checkbox: Disable new comments
  if (
    isset( $_POST['fictioneer_disable_commenting'] ) &&
    in_array( $post_type, ['post', 'page', 'fcn_story', 'fcn_chapter'] )
  ) {
    $fields['fictioneer_disable_commenting'] = fictioneer_sanitize_checkbox( $_POST['fictioneer_disable_commenting'] );
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_field_updates_advanced', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_advanced_metabox' );

// =============================================================================
// SUPPORT LINKS META FIELDS
// =============================================================================

/**
 * Adds support links metabox
 *
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_support_links_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "support_links_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

  $output['fictioneer_patreon_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_patreon_link',
    array(
      'label' => _x( 'Patreon Link', 'Patreon link meta field label.', 'fictioneer' )
    )
  );

  $output['fictioneer_kofi_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_kofi_link',
    array(
      'label' => _x( 'Ko-fi Link', 'Ko-fi link meta field label.', 'fictioneer' )
    )
  );

  $output['fictioneer_subscribestar_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_subscribestar_link',
    array(
      'label' => _x( 'SubscribeStar Link', 'SubscribeStar link meta field label.', 'fictioneer' )
    )
  );

  $output['fictioneer_paypal_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_paypal_link',
    array(
      'label' => _x( 'Paypal Link', 'Paypal link meta field label.', 'fictioneer' )
    )
  );

  $output['fictioneer_donation_link'] = fictioneer_get_metabox_url(
    $post,
    'fictioneer_donation_link',
    array(
      'label' => _x( 'Donation Link', 'Donation link meta field label.', 'fictioneer' )
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
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_support_links_metabox( $post_id ) {
  $post_type = get_post_type( $post_id );

  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_support_links_nonce'] ?? '' ), "support_links_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    ! in_array( $post_type, ['post', 'fcn_story', 'fcn_chapter'] )
  ) {
    return;
  }

  // --- Permissions? ------------------------------------------------------------

  $permission_list = array(
    'post' => ['edit_posts', 'edit_published_posts'],
    'fcn_story' => ['edit_fcn_stories', 'edit_published_fcn_stories'],
    'fcn_chapter' => ['edit_fcn_chapters', 'edit_published_fcn_chapters']
  );

  if (
    ! current_user_can( $permission_list[ $post_type ][0], $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( $permission_list[ $post_type ][1], $post_id ) )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $fields = [];

  // Patreon link
  if ( isset( $_POST['fictioneer_patreon_link'] ) ) {
    $patreon = sanitize_url( $_POST['fictioneer_patreon_link'] );
    $patreon = filter_var( $patreon, FILTER_VALIDATE_URL ) ? $patreon : '';
    $patreon = preg_match( '#^https://(www\.)?patreon#', $patreon ) ? $patreon : '';

    $fields['fictioneer_patreon_link'] = $patreon;
  }

  // Ko-fi link
  if ( isset( $_POST['fictioneer_kofi_link'] ) ) {
    $kofi = sanitize_url( $_POST['fictioneer_kofi_link'] );
    $kofi = filter_var( $kofi, FILTER_VALIDATE_URL ) ? $kofi : '';
    $kofi = preg_match( '#^https://(www\.)?ko-fi#', $kofi ) ? $kofi : '';

    $fields['fictioneer_kofi_link'] = $kofi;
  }

  // SubscribeStar link
  if ( isset( $_POST['fictioneer_subscribestar_link'] ) ) {
    $subscribe_star = sanitize_url( $_POST['fictioneer_subscribestar_link'] );
    $subscribe_star = filter_var( $subscribe_star, FILTER_VALIDATE_URL ) ? $subscribe_star : '';
    $subscribe_star = preg_match( '#^https://(www\.)?subscribestar#', $subscribe_star ) ? $subscribe_star : '';

    $fields['fictioneer_subscribestar_link'] = $subscribe_star;
  }

  // Paypal link
  if ( isset( $_POST['fictioneer_paypal_link'] ) ) {
    $paypal = sanitize_url( $_POST['fictioneer_paypal_link'] );
    $paypal = filter_var( $paypal, FILTER_VALIDATE_URL ) ? $paypal : '';
    $paypal = preg_match( '#^https://(www\.)?paypal#', $paypal ) ? $paypal : '';

    $fields['fictioneer_paypal_link'] = $paypal;
  }

  // Donation link
  if ( isset( $_POST['fictioneer_donation_link'] ) ) {
    $donation = sanitize_url( $_POST['fictioneer_donation_link'] );
    $donation = filter_var( $donation, FILTER_VALIDATE_URL ) ? $donation : '';
    $donation = strpos( $donation, 'https://' ) === 0 ? $donation : '';

    $fields['fictioneer_donation_link'] = $donation;
  }

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_field_updates_support_links', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_support_links_metabox' );

// =============================================================================
// BLOG POST META FIELDS
// =============================================================================

/**
 * Adds featured content side metabox
 *
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_featured_content_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "post_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

  $output['fictioneer_post_featured'] = fictioneer_get_acf_field(
    $post,
    'field_5ed4382ba70b2',
    array(
      'description' => __( 'Select content to be shown as cards below the post.', 'fictioneer' )
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
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_post_metaboxes( $post_id ) {
  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_post_nonce'] ?? '' ), "post_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'post'
  ) {
    return;
  }

  // --- Permissions? ------------------------------------------------------------

  if (
    ! current_user_can( 'edit_posts', $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( 'edit_published_posts', $post_id ) )
  ) {
    return;
  }

  // --- Sanitize and add data ---------------------------------------------------

  $fields = [];

  // Featured
  $featured = get_post_meta( $post_id, 'fictioneer_post_featured', true );

  if ( empty( $featured ) ) {
    $fields['fictioneer_post_featured'] = [];
  }

  $fields['_fictioneer_post_featured'] = 0; // Remove unnecessary ACF control field

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_field_updates_post', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_post_metaboxes' );

// =============================================================================
// COLLECTION META FIELDS
// =============================================================================

/**
 * Adds collection data metabox
 *
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_collection_data_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "collection_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

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
  $output['fictioneer_collection_items'] = fictioneer_get_acf_field(
    $post,
    'field_619fc7732b611',
    array(
      'label' => _x( 'Collection Items', 'Collection items meta field label.', 'fictioneer' ),
      'description' => __( 'Select the posts you want to be featured. If you add a story, <strong>do not</strong> add its chapters separately.', 'fictioneer' )
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
 * Note: Must use the general 'save_post' hook to fire after ACF.
 *
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_collection_metaboxes( $post_id ) {
  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_collection_nonce'] ?? '' ), "collection_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_collection'
  ) {
    return;
  }

  // --- Permissions? ------------------------------------------------------------

  if (
    ! current_user_can( 'edit_fcn_collections', $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( 'edit_published_fcn_collections', $post_id ) )
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
      $fields['fictioneer_collection_description'] = __( 'No description provided yet.', 'fictioneer' );
    }
  }

  // Collection items
  $items = get_post_meta( $post_id, 'fictioneer_collection_items', true );
  $items = is_array( $items ) ? $items : [];

  if ( isset( $items ) && empty( $items ) ) {
    $fields['fictioneer_collection_items'] = []; // Ensure empty ACF meta is removed
  }

  $forbidden = array(
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
  );

  $forbidden = array_map( 'strval', $forbidden );

  $fields['fictioneer_collection_items'] = array_diff( $items, $forbidden );
  $fields['_fictioneer_collection_items'] = 0; // Remove unnecessary ACF control field

  // --- Filters -----------------------------------------------------------------

  $fields = apply_filters( 'fictioneer_filter_field_updates_collection', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_collection_metaboxes' );

// =============================================================================
// RECOMMENDATION META FIELDS
// =============================================================================

/**
 * Adds recommendation data metabox
 *
 * @since Fictioneer 5.7.4
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
 * @since Fictioneer 5.7.4
 *
 * @param WP_Post $post  The current post object.
 */

function fictioneer_render_recommendation_data_metabox( $post ) {
  // --- Setup -------------------------------------------------------------------

  $nonce = wp_create_nonce( "recommendation_data_{$post->ID}" ); // Accounts for manual wp_update_post() calls!
  $output = [];

  // --- Add fields --------------------------------------------------------------

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
 * @since Fictioneer 5.7.4
 *
 * @param int $post_id  The post ID.
 */

function fictioneer_save_recommendation_metaboxes( $post_id ) {
  // --- Verify ------------------------------------------------------------------

  if (
    ! wp_verify_nonce( ( $_POST['fictioneer_recommendation_nonce'] ?? '' ), "recommendation_data_{$post_id}" ) ||
    fictioneer_multi_save_guard( $post_id ) ||
    get_post_type( $post_id ) !== 'fcn_recommendation'
  ) {
    return;
  }

  // --- Permissions? ------------------------------------------------------------

  if (
    ! current_user_can( 'edit_fcn_recommendations', $post_id ) ||
    ( get_post_status( $post_id ) === 'publish' && ! current_user_can( 'edit_published_fcn_recommendations', $post_id ) )
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

      $fields['fictioneer_recommendation_one_sentence'] = mb_strimwidth( $excerpt, 0, 150, '…' );
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
    $url = sanitize_url( $_POST['fictioneer_recommendation_primary_url'] );
    $url = filter_var( $url, FILTER_VALIDATE_URL ) ? $url : '';

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

  $fields = apply_filters( 'fictioneer_filter_field_updates_recommendation', $fields, $post_id );

  // --- Save --------------------------------------------------------------------

  foreach ( $fields as $key => $value ) {
    fictioneer_update_post_meta( $post_id, $key, $value ?? 0 ); // Add, update, or delete (if falsy)
  }
}
add_action( 'save_post', 'fictioneer_save_recommendation_metaboxes' );

?>
