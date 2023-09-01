<?php
/**
 * Partial: Plugin Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.7.1
 */
?>

<div class="fictioneer-ui fictioneer-settings">

	<?php fictioneer_settings_header( 'plugins' ); ?>

	<div class="fictioneer-settings__content">
    <div class="tab-content">

      <div class="single-column-layout">
        <?php do_action( 'fictioneer_admin_settings_plugins' ); ?>
      </div>

    </div>
  </div>

</div>
