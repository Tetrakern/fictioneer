<?php

/**
 * This is a discard pile for functions and snippets that are no longer in use
 * but may prove useful in the future (or were removed by mistake). Keeping them
 * in a searchable file is easier that sorting through my poorly written commits.
*/

// =============================================================================
// RENDER TIME
// =============================================================================

function fictioneer_measure_render_time() {
  global $render_start_time;
  $render_start_time = microtime( true );
}
// add_action( 'template_redirect', 'fictioneer_measure_render_time' );

function fictioneer_display_render_time() {
  global $render_start_time;
  echo '<!-- Render Time: ' . microtime( true ) - $render_start_time . ' seconds -->';
}
// add_action( 'shutdown', 'fictioneer_display_render_time' );


?>
