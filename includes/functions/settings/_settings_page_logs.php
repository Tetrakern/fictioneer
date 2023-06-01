<?php
/**
 * Partial: Logs Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0
 */
?>

<?php

// Setup
$action = $_GET['action'] ?? null;

?>

<div class="fictioneer-ui fictioneer-settings">

	<?php fictioneer_settings_header( 'logs' ); ?>

	<div class="fictioneer-settings__content">
    <div class="tab-content">

      <div class="card">
        <div class="card-wrapper">
          <h3 class="card-header"><?php _e( 'Fictioneer Log', 'fictioneer' ) ?></h3>
          <div class="card-content">
            <?php echo fictioneer_get_log(); ?>
          </div>
        </div>
      </div>

      <?php do_action( 'fictioneer_admin_settings_logs' ); ?>

    </div>
  </div>

</div>
