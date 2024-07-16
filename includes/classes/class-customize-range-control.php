<?php

class Fictioneer_Customize_Range_Control extends \WP_Customize_Control {
  public $type = 'fictioneer-range';

  /**
   * Renders the control's HTML
   *
   * @version 5.21.1
   */

  public function render_content() {
    // Start HTML ---> ?>
    <span class="customize-control-title"><?php echo $this->label; ?></span>

    <?php if ( ! empty( $this->description ) ) : ?>
      <span class="description customize-control-description"><?php echo $this->description; ?></span>
    <?php endif; ?>

    <div class="customize-control-content fictioneer-customize-range">

      <input class="fictioneer-customize-range__range" type="range"
        id="<?php echo esc_attr( $this->id ); ?>"
        value="<?php echo esc_attr( $this->value() ); ?>"
        min="<?php echo esc_attr( $this->input_attrs['min'] ?? 0 ); ?>"
        max="<?php echo esc_attr( $this->input_attrs['max'] ?? 100 ); ?>"
        step="<?php echo esc_attr( $this->input_attrs['step'] ?? 1 ); ?>"
        <?php $this->link(); ?>
      >

      <input
        class="fictioneer-customize-range__number" type="number"
        value="<?php echo esc_attr( $this->value() ); ?>"
        min="<?php echo esc_attr( $this->input_attrs['min'] ?? 0 ); ?>"
        max="<?php echo esc_attr( $this->input_attrs['max'] ?? 100 ); ?>"
        step="<?php echo esc_attr( $this->input_attrs['step'] ?? 1 ); ?>"
        <?php $this->link(); ?>
      >

    </div>
    <?php // <--- End HTML
  }
}
