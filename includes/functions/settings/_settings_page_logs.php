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
            <div class="overflow-horizontal">
              <div class="textarea row">
                <textarea name="fictioneer_log" id="fictioneer_log" rows="24" class="code" readonly><?php echo esc_attr( get_option( 'fictioneer_log' ) ); ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php do_action( 'fictioneer_admin_settings_logs' ); ?>

    </div>
  </div>

</div>
