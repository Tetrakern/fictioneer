<?php
/**
 * Partial: Formatting Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.0.0
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="formatting-modal" class="reader-settings modal" data-fictioneer-target="modal" data-action="click->fictioneer#backgroundCloseModals keydown.esc@document->fictioneer#closeModals" data-nosnippet hidden>
  <div class="modal__wrapper narrow-inputs">

    <button class="close" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>" data-action="click->fictioneer#closeModals"><?php fictioneer_icon( 'fa-xmark' ); ?></button>

    <div class="modal__header drag-anchor"><?php echo fcntr( 'formatting_modal' ); ?></div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _darken">
      <i class="fa-solid fa-adjust reset setting-darken-reset" title="<?php esc_attr_e( 'Darken Background', 'fictioneer' ); ?>"></i>
      <input type="range" value="0" min="-1" max="1" step=".01" class="setting-darken-range" id="reader-settings-darken-range">
      <input type="number" value="0" min="-100" max="100" class="setting-darken-text" id="reader-settings-darken-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _saturation">
      <i class="fa-solid fa-droplet reset setting-saturation-reset" title="<?php esc_attr_e( 'Saturate Background', 'fictioneer' ); ?>"></i>
      <input type="range" value="0" min="-1" max="1" step=".01" class="setting-saturation-range" id="reader-settings-saturation-range">
      <input type="number" value="0" min="-100" max="100" class="setting-saturation-text" id="reader-settings-saturation-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing hide-below-640 _width">
      <i class="fa-solid fa-arrows-alt-h reset" title="<?php esc_attr_e( 'Content Width', 'fictioneer' ); ?>" id="reader-settings-site-width-reset" data-action="click->fictioneer-chapter-formatting#siteWidth" data-fictioneer-chapter-formatting-type-param="reset" data-fictioneer-chapter-formatting-target="siteWidthReset"></i>
      <input type="range" value="960" min="640" max="1920" step="1" id="reader-settings-site-width-range" data-action="input->fictioneer-chapter-formatting#siteWidth" data-fictioneer-chapter-formatting-type-param="range" data-fictioneer-chapter-formatting-target="siteWidthRange">
      <input type="number" value="960" min="640" max="1920" step="1" id="reader-settings-site-width-text" data-action="input->fictioneer-chapter-formatting#siteWidth" data-fictioneer-chapter-formatting-type-param="text" data-fictioneer-chapter-formatting-target="siteWidthText">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _font-size">
      <i class="fa-solid fa-text-height reset" title="<?php esc_attr_e( 'Font Size (Percentage)', 'fictioneer' ); ?>" id="reader-settings-font-size-reset" data-action="click->fictioneer-chapter-formatting#fontSize" data-fictioneer-chapter-formatting-type-param="reset" data-fictioneer-chapter-formatting-target="fontSizeReset"></i>
      <input type="range" value="100" min="50" max="200" step="1" id="reader-settings-font-size-range" data-action="input->fictioneer-chapter-formatting#fontSize" data-fictioneer-chapter-formatting-type-param="range" data-fictioneer-chapter-formatting-target="fontSizeRange">
      <input type="number" value="100" min="50" max="200" step="1" id="reader-settings-font-size-text"  data-action="input->fictioneer-chapter-formatting#fontSize" data-fictioneer-chapter-formatting-type-param="text" data-fictioneer-chapter-formatting-target="fontSizeText">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _letter-spacing">
      <i class="fa-solid fa-text-width reset" title="<?php esc_attr_e( 'Letter Spacing', 'fictioneer' ); ?>" id="reader-settings-letter-spacing-reset"></i>
      <input type="range" value="0.0" min="-0.1" max="0.2" step=".001" id="reader-settings-letter-spacing-range">
      <input type="number" value="0.0" min="-0.1" max="0.2" step=".001" id="reader-settings-letter-spacing-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _line-height">
      <?php fictioneer_icon( 'line-height', 'reset', 'reader-settings-line-height-reset' ); ?>
      <input type="range" value="1.7" min="0.8" max="3.0" step=".1" id="reader-settings-line-height-range">
      <input type="number" value="1.7" min="0.8" max="3.0" step=".1" id="reader-settings-line-height-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _paragraph-spacing">
      <i class="fa-solid fa-paragraph reset" title="<?php esc_attr_e( 'Paragraph Spacing', 'fictioneer' ); ?>" id="reader-settings-paragraph-spacing-reset"></i>
      <input type="range" value="1.5" min="0" max="3.0" step=".05" id="reader-settings-paragraph-spacing-range">
      <input type="number" value="1.5" min="0" max="3.0" step=".05" id="reader-settings-paragraph-spacing-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _font-saturation">
      <i class="fa-solid fa-pen-nib reset" title="<?php esc_attr_e( 'Font Saturation', 'fictioneer' ); ?>" id="reader-settings-font-saturation-reset"></i>
      <input type="range" value="0" min="-1" max="1" step=".01" id="reader-settings-font-saturation-range">
      <input type="number" value="0" min="-1" max="1" step=".01" id="reader-settings-font-saturation-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _font-lightness">
      <i class="fa-solid fa-bolt-lightning reset setting-font-lightness-reset" title="<?php esc_attr_e( 'Font Lightness', 'fictioneer' ); ?>" id="reader-settings-font-lightness-reset"></i>
      <input type="range" value="0" min="-1" max="1" step=".01" class="setting-font-lightness-range" id="reader-settings-font-lightness-range">
      <input type="number" value="0" min="-1" max="1" step=".01" class="setting-font-lightness-text" id="reader-settings-font-lightness-text">
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _fonts">
      <i class="fa-solid fa-font reset" title="<?php esc_attr_e( 'Font', 'fictioneer' ); ?>" id="reader-settings-font-reset"></i>
      <div class="select-wrapper">
        <select id="reader-settings-font-select" name="fonts">
          <?php
            $fonts = fictioneer_get_fonts();
            $length = count( $fonts );

            for ( $i = 0; $i < $length; $i++ ) {
              echo '<option value="' . $i . '">' . $fonts[ $i ]['name'] . '</option>';
            }
          ?>
        </select>
      </div>
      <button type="button" id="reader-settings-prev-font" value="-1" class="button button--left font-stepper"><i class="fa-solid fa-caret-left"></i></button>
      <button type="button" id="reader-settings-next-font" value="1" class="button button--right font-stepper"><i class="fa-solid fa-caret-right"></i></button>
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _colors">
      <i class="fa-solid fa-palette reset" title="<?php esc_attr_e( 'Color', 'fictioneer' ); ?>" id="reader-settings-font-color-reset"></i>
      <div class="select-wrapper">
        <select id="reader-settings-font-color-select" name="colors">
          <?php
            $colors = fictioneer_get_font_colors();
            $length = count( $colors );

            for ( $i = 0; $i < $length; $i++ ) {
              echo '<option value="' . $i . '">' . $colors[ $i ]['name'] . '</option>';
            }
          ?>
        </select>
      </div>
      <button type="button" value="-1" id="reader-settings-prev-font-color" class="button button--left font-color-stepper"><i class="fa-solid fa-caret-left"></i></button>
      <button type="button" value="1" id="reader-settings-next-font-color" class="button button--right font-color-stepper"><i class="fa-solid fa-caret-right"></i></button>
    </div>

    <div class="modal__row reader-settings__row _vertical-shrink-spacing _font-weight">
      <i class="fa-solid fa-weight-hanging reset font-weight-reset"></i>
      <div class="select-wrapper">
        <select name="font-weight" class="site-setting-font-weight">
          <option value="default" selected="selected"><?php _ex( 'Default Font Weight', 'Formatting modal font weight selection.', 'fictioneer' ); ?></option>
          <option value="thinner"><?php _ex( 'Thinner Font Weight', 'Formatting modal font weight selection.', 'fictioneer' ); ?></option>
          <option value="normal"><?php _ex( 'Normal Font Weight', 'Formatting modal font weight selection.', 'fictioneer' ); ?></option>
        </select>
      </div>
    </div>

    <div class="modal__row reader-settings__row">
      <i class="fa-solid fa-check" title="<?php esc_attr_e( 'Toggles', 'fictioneer' ); ?>"></i>
      <div class="reader-settings__toggles">
        <label class="reader-settings__toggle toggle" title="<?php esc_attr_e( 'Toggle text indent', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-checked="true" aria-label="<?php esc_attr_e( 'Toggle text indent', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-indent-toggle" hidden checked>
          <i class="fa-solid fa-indent reader-settings__toggle-icon"></i>
        </label>
        <label class="reader-settings__toggle toggle" title="<?php esc_attr_e( 'Toggle text justify', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-checked="false" aria-label="<?php esc_attr_e( 'Toggle text justify', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-justify-toggle" hidden>
          <i class="fa-solid fa-align-justify reader-settings__toggle-icon"></i>
        </label>
        <button
          class="reader-settings__toggle toggle toggle-light-mode"
          title="<?php esc_attr_e( 'Toggle Dark/Light Mode', 'fictioneer' ); ?>"
          role="checkbox"
          aria-checked="false"
          aria-label="<?php esc_attr_e( 'Toggle between dark mode and light mode', 'fictioneer' ); ?>"
        ><i class="fa-solid fa-sun reader-settings__toggle-icon"></i></button>
        <label class="reader-settings__toggle toggle" title="<?php esc_attr_e( 'Toggle paragraph tools', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-checked="true" aria-label="<?php esc_attr_e( 'Toggle paragraph tools', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-paragraph-tools-toggle" hidden checked>
          <i class="fa-solid fa-marker reader-settings__toggle-icon"></i>
        </label>
        <label class="reader-settings__toggle toggle" title="<?php esc_attr_e( 'Toggle chapter notes', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-checked="true" aria-label="<?php esc_attr_e( 'Toggle chapter notes', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-chapter-notes-toggle" hidden checked>
          <span class="reader-settings__toggle-icon"><?php fictioneer_icon( 'note-filled' ); ?></span>
        </label>
        <label class="reader-settings__toggle toggle" title="<?php esc_attr_e( 'Toggle comment section', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-checked="true" aria-label="<?php esc_attr_e( 'Toggle comment section', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-comments-toggle" hidden checked>
          <i class="fa-solid fa-comments reader-settings__toggle-icon"></i>
        </label>
        <label class="reader-settings__toggle toggle" title="<?php esc_attr_e( 'Toggle sensitive content', 'fictioneer' ); ?>" tabindex="0" role="checkbox" aria-checked="true" aria-label="<?php esc_attr_e( 'Toggle sensitive content', 'fictioneer' ); ?>">
          <input type="checkbox" id="reader-settings-sensitive-content-toggle" hidden checked>
          <i class="fa-solid fa-exclamation reader-settings__toggle-icon"></i>
        </label>
      </div>
    </div>

  </div>
</div>
