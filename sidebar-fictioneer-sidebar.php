<?php
/**
 * Sidebar
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.20.0
 */


// No direct access!
defined( 'ABSPATH' ) OR exit;

?>

<div id="fictioneer-sidebar" class="fictioneer-sidebar">
	<div class="fictioneer-sidebar__wrapper">
		<?php dynamic_sidebar( 'fictioneer-sidebar' ); ?>
	</div>
</div>
