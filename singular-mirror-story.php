<?php
/**
 * Template Name: Story Mirror
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.17.0
 * @see partials/_story-header.php
 * @see partials/_story-footer.php
 */
?>

<?php

// Setup
$post_id = get_post_meta( get_the_ID(), 'fictioneer_template_story_id', true );
$post_id = fictioneer_validate_id( $post_id, 'fcn_story' );

if ( ! $post_id ) {
  wp_die( __( 'Invalid or missing story ID in page template.', 'fictioneer' ) );
}

$post = get_post( $post_id );

get_template_part( 'single-fcn_story', null, array( 'post_id' => $post_id ) );
