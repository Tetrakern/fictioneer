<?php
/**
 * Partial: Site Settings Modal
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.1
 */
?>

<div id="site-settings-modal" class="site-settings modal">
  <label for="modal-site-settings-toggle" class="background-close"></label>
  <div class="modal__wrapper narrow-inputs">
    <label class="close" for="modal-site-settings-toggle" tabindex="0" aria-label="<?php esc_attr_e( 'Close modal', 'fictioneer' ); ?>">
      <?php fictioneer_icon( 'fa-xmark' ); ?>
    </label>
    <h4 class="modal__header drag-anchor"><?php echo fcntr( 'site_settings' ) ?></h4>
    <div class="modal__description modal__row site-settings__description site-settings__row">
      <p class="border-bottom"><?php _e( 'You can toggle selected features and styles per device/browser to boost performance. Some options may not be available.', 'fictioneer' ) ?></p>
    </div>
    <div class="modal__row _no-vertical-padding site-settings__row">
      <div class="site-settings__sticky-navigation">
        <input type="checkbox" id="site-setting-nav-sticky" checked>
        <label for="site-setting-nav-sticky" class="modal__setting-toggle"><?php _e( 'Scrolling (sticky) navigation bar', 'fictioneer' ) ?></label>
      </div>
      <div class="site-settings__background-textures">
        <input type="checkbox" id="site-setting-background-textures" checked>
        <label for="site-setting-background-textures" class="modal__setting-toggle"><?php _e( 'Background textures', 'fictioneer' ) ?></label>
      </div>
      <div class="site-settings__polygons">
        <input type="checkbox" id="site-setting-polygons" checked>
        <label for="site-setting-polygons" class="modal__setting-toggle"><?php _e( 'Polygons (irregular borders)', 'fictioneer' ) ?></label>
      </div>
      <div class="site-settings__progress-bar">
        <input type="checkbox" id="site-setting-chapter-progress-bar" checked>
        <label for="site-setting-chapter-progress-bar" class="modal__setting-toggle"><?php _e( 'Chapter progress bar', 'fictioneer' ) ?></label>
      </div>
      <div class="site-settings__covers">
        <input type="checkbox" id="site-setting-covers" checked>
        <label for="site-setting-covers" class="modal__setting-toggle"><?php _e( 'Cover images (chapters and stories)', 'fictioneer' ) ?></label>
      </div>
      <div class="site-settings__text-shadows">
        <input type="checkbox" id="site-setting-text-shadows">
        <label for="site-setting-text-shadows" class="modal__setting-toggle"><?php _e( 'Text shadows', 'fictioneer' ) ?></label>
      </div>
      <div class="site-settings__minimal">
        <input type="checkbox" id="site-setting-minimal">
        <label for="site-setting-minimal" class="modal__setting-toggle"><?php _e( 'Minimalist mode', 'fictioneer' ) ?></label>
      </div>
    </div>
    <div class="modal__row site-settings__row">
      <div class="site-settings__theme grouped-inputs border-top">
        <i class="fa-solid fa-weight-hanging reset font-weight-reset"></i>
        <div class="select-wrapper">
          <select name="font-weight" class="site-setting-font-weight">
            <option value="default" selected="selected"><?php _ex( 'Font Weight: Default', 'Site settings modal font weight selection.', 'fictioneer' ) ?></option>
            <option value="thinner"><?php _ex( 'Font Weight: Thinner', 'Site settings modal font weight selection.', 'fictioneer' ) ?></option>
            <option value="normal"><?php _ex( 'Font Weight: Normal', 'Site settings modal font weight selection.', 'fictioneer' ) ?></option>
          </select>
        </div>
      </div>
      <?php if ( is_child_theme() && FICTIONEER_THEME_SWITCH ) : ?>
        <div class="site-settings__theme grouped-inputs">
          <i class="fa-solid fa-masks-theater reset" id="site-setting-theme-reset"></i>
          <div class="select-wrapper">
            <select name="site-theme" class="site-setting-site-theme">
              <option value="default" selected="selected"><?php _ex( 'Theme:', 'Site settings modal theme selection: Current Theme.', 'fictioneer' ) ?> <?php echo FICTIONEER_SITE_NAME; ?></option>
              <option value="base"><?php _ex( 'Theme: Fictioneer Base', 'Site settings modal theme selection: Base Theme.', 'fictioneer' ) ?></option>
            </select>
          </div>
        </div>
      <?php endif; ?>
      <div class="site-settings__hue-rotate grouped-inputs">
        <i class="fa-solid fa-swatchbook reset" id="site-setting-hue-rotate-reset"></i>
        <input type="range" value="0" min="0" max="360" step="1" class="slider" id="site-setting-hue-rotate-range">
        <input type="number" value="0" min="0" max="360" id="site-setting-hue-rotate-text">
      </div>
      <div class="site-settings__darken grouped-inputs">
        <i class="fa-solid fa-adjust reset setting-darken-reset" style="transform: scale(-1);"></i>
        <input type="range" value="0" min="-1" max="1" step=".01" class="slider setting-darken-range">
        <input type="number" value="0" min="-100" max="100" class="setting-darken-text">
      </div>
      <div class="site-settings__saturation grouped-inputs">
        <i class="fa-solid fa-droplet reset setting-saturation-resets"></i>
        <input type="range" value="0" min="-1" max="1" step=".01" class="slider setting-saturation-range">
        <input type="number" value="0" min="-100" max="100" class="setting-saturation-text">
      </div>
    </div>
  </div>
</div>
