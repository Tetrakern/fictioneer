<?php
/**
 * Partial: Formatting Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0
 */
?>

<div id="reader-settings" class="reader-settings modal">
  <label for="modal-formatting-toggle" class="background-close"></label>
  <div class="modal__wrapper narrow-inputs">
    <label class="close" for="modal-formatting-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>
    <h4 class="modal__header drag-anchor"><?php echo fcntr( 'formatting_modal' ); ?></h4>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-adjust reset setting-darken-reset" title="<?php esc_attr_e( 'Darken Background', 'fictioneer' ); ?>"></i>
      <input type="range" value="0" min="-1" max="1" step=".01" class="slider setting-darken-range">
      <input type="number" value="0" min="-100" max="100" class="setting-darken-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-droplet reset setting-saturation-resets" title="<?php esc_attr_e( 'Saturate Background', 'fictioneer' ); ?>"></i>
      <input type="range" value="0" min="-1" max="1" step=".01" class="slider setting-saturation-range">
      <input type="number" value="0" min="-100" max="100" class="setting-saturation-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-arrows-alt-h reset" title="<?php esc_attr_e( 'Site Width', 'fictioneer' ); ?>" id="reader-settings-site-width-reset"></i>
      <input type="range" value="960" min="640" max="1920" step="1" class="slider" id="reader-settings-site-width-range">
      <input type="number" value="960" min="640" max="1920" step="1" id="reader-settings-site-width-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-text-height reset" title="<?php esc_attr_e( 'Font Size (Percentage)', 'fictioneer' ); ?>" id="reader-settings-font-size-reset"></i>
      <input type="range" value="100" min="50" max="200" step="1" class="slider" id="reader-settings-font-size-range">
      <input type="number" value="100" min="50" max="200" step="1" id="reader-settings-font-size-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-text-width reset" title="<?php esc_attr_e( 'Letter Spacing', 'fictioneer' ); ?>" id="reader-settings-letter-spacing-reset"></i>
      <input type="range" value="0.0" min="-0.1" max="0.2" step=".001" class="slider" id="reader-settings-letter-spacing-range">
      <input type="number" value="0.0" min="-0.1" max="0.2" step=".001" id="reader-settings-letter-spacing-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <?php fictioneer_icon( 'line-height', 'reset', 'reader-settings-line-height-reset' ); ?>
      <input type="range" value="1.7" min="0.8" max="3.0" step=".1" class="slider" id="reader-settings-line-height-range">
      <input type="number" value="1.7" min="0.8" max="3.0" step=".1" id="reader-settings-line-height-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-paragraph reset" title="<?php esc_attr_e( 'Paragraph Spacing', 'fictioneer' ); ?>" id="reader-settings-paragraph-spacing-reset"></i>
      <input type="range" value="1.5" min="0" max="3.0" step=".05" class="slider" id="reader-settings-paragraph-spacing-range">
      <input type="number" value="1.5" min="0" max="3.0" step=".05" id="reader-settings-paragraph-spacing-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-pen-nib reset" title="<?php esc_attr_e( 'Font Saturation', 'fictioneer' ); ?>" id="reader-settings-font-saturation-reset"></i>
      <input type="range" value="0" min="-1" max="1" step=".01" class="slider" id="reader-settings-font-saturation-range">
      <input type="number" value="0" min="-1" max="1" step=".01" id="reader-settings-font-saturation-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-font reset" title="<?php esc_attr_e( 'Font', 'fictioneer' ); ?>" id="reader-settings-font-reset"></i>
      <div class="select-wrapper">
        <select id="reader-settings-font-select" name="fonts">
          <?php
            $fonts = [FICTIONEER_PRIMARY_FONT, 'System Font', 'Lato', 'Helvetica Neue', 'Georgia', 'Roboto Mono', 'Roboto Serif', 'Cormorant Garamond', 'Open Dyslexic'];
            $font_names = [FICTIONEER_PRIMARY_FONT_NAME, __( 'System Font', 'fictioneer' ), __( 'Lato', 'fictioneer' ), __( 'Helvetica Neue', 'fictioneer' ), __( 'Georgia', 'fictioneer' ), __( 'Roboto Mono', 'fictioneer' ), __( 'Roboto Serif', 'fictioneer' ), __( 'Cormorant Garamond', 'fictioneer' ), __( 'Open Dyslexic', 'fictioneer' )];
            $length = count( $fonts );

            for ( $i = 0; $i < $length; $i++ ) {
              echo '<option value="' . $i . '">' . $font_names[ $i ] . '</option>';
            }
          ?>
        </select>
      </div>
      <button type="button" id="reader-settings-prev-font" value="-1" class="button button--left font-stepper"><i class="fa-solid fa-caret-left"></i></button>
      <button type="button" id="reader-settings-next-font" value="1" class="button button--right font-stepper"><i class="fa-solid fa-caret-right"></i></button>
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-palette reset" title="<?php esc_attr_e( 'Color', 'fictioneer' ); ?>" id="reader-settings-font-color-reset"></i>
      <div class="select-wrapper">
        <select id="reader-settings-font-color-select" name="colors">
          <?php
            $colors = ['Tinted', 'Baseline', 'Low', 'Lower', 'Lowest', 'High', 'Higher', 'Highest', 'White', 'Gray', 'Black'];
            $color_names = [__( 'Tinted', 'fictioneer' ), __( 'Baseline', 'fictioneer' ), __( 'Low', 'fictioneer' ), __( 'Lower', 'fictioneer' ), __( 'Lowest', 'fictioneer' ), __( 'High', 'fictioneer' ), __( 'Higher', 'fictioneer' ), __( 'Highest', 'fictioneer' ), __( 'White', 'fictioneer' ), __( 'Gray', 'fictioneer' ), __( 'Black', 'fictioneer' )];
            $length = count( $colors );

            for ( $i = 0; $i < $length; $i++ ) {
              echo '<option value="' . $i . '">' . $color_names[ $i ] . '</option>';
            }
          ?>
        </select>
      </div>
      <button type="button" value="-1" id="reader-settings-prev-font-color" class="button button--left font-color-stepper"><i class="fa-solid fa-caret-left"></i></button>
      <button type="button" value="1" id="reader-settings-next-font-color" class="button button--right font-color-stepper"><i class="fa-solid fa-caret-right"></i></button>
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing">
      <i class="fa-solid fa-weight-hanging reset font-weight-reset"></i>
      <div class="select-wrapper">
        <select name="font-weight" class="site-setting-font-weight">
          <option value="default" selected="selected"><?php _ex( 'Default Font Weight', 'Formatting modal font weight selection.', 'fictioneer' ) ?></option>
          <option value="thinner"><?php _ex( 'Thinner Font Weight', 'Formatting modal font weight selection.', 'fictioneer' ) ?></option>
          <option value="normal"><?php _ex( 'Normal Font Weight', 'Formatting modal font weight selection.', 'fictioneer' ) ?></option>
        </select>
      </div>
    </div>

    <div class="modal__row reader-settings__row">
      <i class="fa-solid fa-check" title="<?php esc_attr_e( 'Toggles', 'fictioneer' ); ?>"></i>
      <div class="reader-settings__toggles">
        <label class="toggle" title="<?php esc_attr_e( 'Toggle text indent', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-label="<?php esc_attr_e( 'Toggle text indent', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-indent-toggle" hidden checked>
          <span><i class="fa-solid fa-indent"></i></span>
        </label>
        <label class="toggle" title="<?php esc_attr_e( 'Toggle text justify', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-label="<?php esc_attr_e( 'Toggle text justify', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-justify-toggle" hidden>
          <span><i class="fa-solid fa-align-justify"></i></span>
        </label>
        <label class="toggle" title="<?php esc_attr_e( 'Toggle light mode', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-label="<?php esc_attr_e( 'Toggle light mode', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-lightmode-toggle" class="toggle-light-mode" hidden>
          <span><i class="fa-solid fa-sun"></i></span>
        </label>
        <label class="toggle" title="<?php esc_attr_e( 'Toggle paragraph tools', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-label="<?php esc_attr_e( 'Toggle paragraph tools', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-paragraph-tools-toggle" hidden checked>
          <span><i class="fa-solid fa-marker"></i></span>
        </label>
        <label class="toggle" title="<?php esc_attr_e( 'Toggle chapter notes', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-label="<?php esc_attr_e( 'Toggle chapter notes', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-chapter-notes-toggle" hidden checked>
          <span><?php fictioneer_icon( 'note-filled' ); ?></span>
        </label>
        <label class="toggle" title="<?php esc_attr_e( 'Toggle comment section', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-label="<?php esc_attr_e( 'Toggle comment section', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-comments-toggle" hidden checked>
          <span><i class="fa-solid fa-comments"></i></span>
        </label>
        <label class="toggle" title="<?php esc_attr_e( 'Toggle sensitive content', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-label="<?php esc_attr_e( 'Toggle sensitive content', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-sensitive-content-toggle" hidden checked>
          <span><i class="fa-solid fa-exclamation"></i></span>
        </label>
      </div>
    </div>

  </div>
</div>
