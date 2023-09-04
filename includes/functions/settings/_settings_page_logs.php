<?php
/**
 * Partial: Logs Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 */
?>

<div class="fictioneer-settings">

	<?php fictioneer_settings_header( 'logs' ); ?>

	<div class="fictioneer-settings__content">

    <div class="fictioneer-card">
      <div class="fictioneer-card__wrapper">
        <h3 class="fictioneer-card__header"><?php _e( 'Fictioneer Log', 'fictioneer' ) ?></h3>
        <div class="fictioneer-card__content">
          <div class="fictioneer-card__row">
            <?php echo fictioneer_get_log(); ?>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>
