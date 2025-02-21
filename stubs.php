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
 * PHP random_int()
 *
 * Generates a uniformly selected integer between the given minimum and maximum.
 *
 * @link https://www.php.net/manual/en/function.random-int.php
 *
 * @param int $min  Minimum.
 * @param int $max  Maximum.
 *
 * @return int A cryptographically secure, uniformly selected integer from the
 *             closed interval [min, max]. Both min and max are possible return values.
 */

function random_int( int $min, int $max ) {}

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
 * WordPress: Clears the plugins cache used by get_plugins() and by default, the plugin updates cache.
 *
 * @param string $clear_update_cache  Whether to clear the plugin updates cache. Default true.
 */

function wp_cache_clean_cache( $clear_update_cache = true ) {}

/**
 * Replaces insecure HTTP URLs to the site in the given content, if configured to do so.
 *
 * @param string $content  Content to replace URLs in.
 *
 * @return string Filtered content.
 */

function wp_replace_insecure_home_url( $content ) {}

/**
 * WP Super Cache: Clear post cache
 *
 * @param int $post_id  The post ID.
 */

function wp_cache_post_change( $post_id ) {}

/**
 * WP Rocket: Use the rocket_clean_domain() function when you want to purge
 * a complete domain from the cache, that is: clear the cache for
 * your entire website.
 *
 * @link https://docs.wp-rocket.me/article/92-rocketcleandomain
 */

function rocket_clean_domain() {}

/**
 * WP Rocket: Clear post cache
 *
 * @link https://docs.wp-rocket.me/article/93-rocketcleanpost
 *
 * @param int $post_id  The post ID.
 */

function rocket_clean_post( $post_id ) {}

/**
 * W3TC: Purges/Flushes everything
 */

function w3tc_flush_all( $extras = null ) {}

/**
 * W3TC: Clear post cache
 *
 * @param int $post_id  The post ID.
 */

function w3tc_flush_post( $post_id ) {}

/**
 * WP-Optimize: Returns instance
 *
 * @link https://getwpo.com
 *
 * @return WP_Optimize Instance of self.
 */

function WP_Optimize() {}

/**
 * WP-Optimize cache class
 */

class WPO_Page_Cache {
  /**
   * Deletes the cache for a single post
   *
   * @param int $post_id  The post ID.
   */

  public static function delete_single_post_cache( $post_id ) {}
}

/**
 * WPFC: Clear all cache
 */

function wpfc_clear_all_cache() {}

/**
 * WPFC: Clear post cache
 *
 * @param int $post_id  The post ID.
 */

function wpfc_clear_post_cache_by_id( $post_id ) {}

/**
 * Jetpack class
 */

class Jetpack {
  /**
   * Probably checks whether a module is active
   *
   * @param string $module  Probably the module name. This is just a stub.
   */

  public static function is_module_active( $module ) {}
}

class DOMNode {
  /**
   * Get an attribute of the DOM element.
   *
   * Note: This function for DOMNode is undocumented
   * in the official PHP documentation.
   *
   * @param string $name  The name of the attribute.
   *
   * @return string|null The attribute value or null if not found.
   */

  public function getAttribute( string $name ) : ?string {
    return '';
  }

  /**
   * Check if the element has a specific attribute.
   *
   * Note: This function for DOMNode is undocumented
   * in the official PHP documentation.
   *
   * @param string $name  The name of the attribute.
   *
   * @return bool True if the attribute exists, false otherwise.
   */

  public function hasAttribute( string $name ) : bool {
    return false;
  }

  /**
   * Set an attribute for the DOM element.
   *
   * Note: This function for DOMNode is undocumented
   * in the official PHP documentation.
   *
   * @param string $name   The name of the attribute.
   * @param string $value  The value to set for the attribute.
   *
   * @return void
   */

  public function setAttribute(string $name, string $value): void {}

  /**
   * Remove an attribute from the DOM element.
   *
   * @param string $name  The name of the attribute.
   *
   * @return void
   */

  public function removeAttribute( string $name ) : void {}
}
