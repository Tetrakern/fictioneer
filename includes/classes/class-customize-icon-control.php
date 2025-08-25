<?php

class Fictioneer_Customize_Icon_HTML_Control extends WP_Customize_Control {
  public $type = 'fictioneer_icon_html';

  /**
   * Renders the control's HTML
   *
   * @version 5.32.0
   */

  public function render_content() {
    // Start HTML ---> ?>
    <label class="fictioneer-icon-html-control">
      <span class="customize-control-title">
        <?php echo esc_html( $this->label ); ?>
        (<span class="fictioneer-icon-preview" aria-hidden="true"><?php echo $this->value(); ?></span>)
      </span>
      <textarea
        <?php $this->input_attrs(); ?>
        <?php $this->link(); ?>
        class="large-text code customizer-icon-html"
        rows="3"
        placeholder="<?php echo esc_attr( $this->input_attrs['placeholder'] ?? '' ); ?>"
      ><?php echo esc_textarea( $this->value() ); ?></textarea>
    </label>
    <?php // <--- End HTML
  }
}
