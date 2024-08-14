<?php

/**
 * PHP rand()
 *
 * @link https://www.php.net/manual/en/function.rand.php
 *
 * @param int $min  Optional minimum.
 * @param int $max  Optional maximum.
 *
 * @return int Random number.
 */

function rand( int $min = null, int $max = null ) {}

/**
 * Updates post author user caches for a list of post objects.
 *
 * @since WP 6.1.0
 *
 * @param WP_Post[] $posts Array of post objects.
 */

function update_post_author_caches( $posts ) {}

/**
 * PHP random_bytes()
 *
 * Generates a string containing uniformly selected
 * random bytes with the requested length.
 *
 * @link https://www.php.net/manual/en/function.random-bytes.php
 *
 * @param int $length  Length of the requested random string.
 *
 * @return string Random string with the requested length.
 */

function random_bytes( int $length ) {}

/**
 * Outputs an admin notice.
 *
 * @since 6.4.0
 *
 * @param string $message  The message to output.
 * @param array  $args {
 *   Optional. An array of arguments for the admin notice. Default empty array.
 *
 *   @type string   $type                Optional. The type of admin notice.
 *                                       For example, 'error', 'success', 'warning', 'info'.
 *                                       Default empty string.
 *   @type bool     $dismissible         Optional. Whether the admin notice is dismissible. Default false.
 *   @type string   $id                  Optional. The value of the admin notice's ID attribute. Default empty string.
 *   @type string[] $additional_classes  Optional. A string array of class names. Default empty array.
 *   @type string[] $attributes          Optional. Additional attributes for the notice div. Default empty array.
 *   @type bool     $paragraph_wrap      Optional. Whether to wrap the message in paragraph tags. Default true.
 * }
 */

function wp_admin_notice( string $message, array $args = array() ) {}

/**
 * Render Elementor template
 *
 * @param string $location  Template location.
 */

function elementor_theme_do_location( $location ) {}

/**
 * Replaces insecure HTTP URLs to the site in the given content, if configured to do so.
 *
 * @param string $content  Content to replace URLs in.
 *
 * @return string Filtered content.
 */

function wp_replace_insecure_home_url( $content ) {}

/**
 * Use the rocket_clean_domain() function when you want to purge
 * a complete domain from the cache, that is: clear the cache for
 * your entire website.
 *
 * @link https://docs.wp-rocket.me/article/92-rocketcleandomain
 */

function rocket_clean_domain() {}

/**
 * Clears the plugins cache used by get_plugins() and by default, the plugin updates cache.
 *
 * @param string $clear_update_cache  Whether to clear the plugin updates cache. Default true.
 */

function wp_cache_clean_cache( $clear_update_cache = true ) {}

/**
 * W3TC: Purges/Flushes everything
 */

function w3tc_flush_all( $extras = null ) {}
