<?php

// =============================================================================
// REGISTER BLOCK STYLES
// =============================================================================

/**
 * Register new block styles.
 *
 * @since 5.32.0
 */

function fictioneer_register_block_styles() {
  register_block_style(
    'core/gallery',
    array(
      'name' => 'grid-gallery',
      'label' => _x( 'Grid', 'Gallery block style label.', 'fictioneer' )
    )
  );
}
add_action( 'init', 'fictioneer_register_block_styles' );

// =============================================================================
// ADD WRAPPER TO TERM LIST BLOCK SELECT
// =============================================================================

/**
 * Add wrapper to term list block select.
 *
 * @since 5.32.0
 *
 * @param string $block_content  The block content.
 * @param array  $block          The full block, including name and attributes.
 *
 * @return string The updated block content.
 */

function fictioneer_term_list_select_wrapper( $block_content, $block ) {
  // Guard clause for legacy or malformed blocks
  if ( empty( $block['blockName'] ) ) {
    return $block_content;
  }

  // Only for categories block (with select)
  if ( $block['blockName'] === 'core/categories' ) {
    $block_content = str_replace( '<select', '<div class="select-wrapper"><select', $block_content );
    $block_content = str_replace( '</select>', '</select></div>', $block_content );
  }

  // Continue filter
  return $block_content;
}
add_filter( 'render_block', 'fictioneer_term_list_select_wrapper', 10, 2 );

// =============================================================================
// ADD CONSENT WRAPPER TO EMBED BLOCKS
// =============================================================================

/**
 * Add consent wrapper to embed block.
 *
 * @since 5.32.0
 *
 * @param string $block_content  The block content.
 * @param array  $block          The full block, including name and attributes.
 *
 * @return string The updated block content.
 */

function fictioneer_embed_consent_wrapper( $block_content, $block ) {
  // Guard clause for legacy or malformed blocks
  if ( empty( $block['blockName'] ) ) {
    return $block_content;
  }

  // Check block
  if (
    $block['blockName'] !== 'core/embed' &&
    ! str_starts_with( $block['blockName'], 'core-embed/twitter' )
  ) {
    return $block_content;
  }

  // Check for iframes or twitter
  if (
    strpos( $block_content, '<iframe' ) === false &&
    strpos( $block_content, "twitter-timeline" ) === false &&
    strpos( $block_content, "twitter-tweet" ) === false
  ) {
    return $block_content;
  }

  // Prepare dom document
  libxml_use_internal_errors( true );
  $dom = new DOMDocument();
  $dom->loadHTML( '<?xml encoding="UTF-8">' . $block_content );
  libxml_clear_errors();
  $xpath = new DomXPath( $dom );

  // Handle iframes...
  $iframes = $dom->getElementsByTagName( 'iframe' );

  foreach ( $iframes as $iframe ) {
    $src = $iframe->getAttribute( 'src' );

    if ( ! $src ) {
      continue;
    }

    $title = htmlspecialchars( $iframe->getAttribute( 'title' ) );
    $title = ( ! $title || $title === '' ) ? 'embedded content' : $title;

    $iframe->removeAttribute( 'src' );

    $consent_element = $dom->createElement( 'button' );
    $consent_element->setAttribute( 'type', 'button' );
    $consent_element->setAttribute( 'class', 'iframe-consent' );
    $consent_element->setAttribute( 'data-src', $src );
    $consent_element->nodeValue = sprintf(
      __( 'Click to load %s with third-party consent.', 'fictioneer' ),
      $title
    );

    $embed_logo = $dom->createElement( 'div' );
    $embed_logo->setAttribute( 'class', 'embed-logo' );

    $iframe->parentNode->insertBefore( $consent_element );
    $iframe->parentNode->insertBefore( $embed_logo );
  }

  // Handle Twitter timelines
  $twitter_timelines = $xpath->query( "//a[contains(@class, 'twitter-timeline')]/following-sibling::*[1]" );

  foreach ( $twitter_timelines as $twitter ) {
    $src = $twitter->getAttribute( 'src' );

    if ( ! $src ) {
      continue;
    }

    $twitter->removeAttribute( 'src' );
    $twitter->previousSibling->nodeValue = '';

    $consent_element = $dom->createElement( 'div' );
    $consent_element->setAttribute( 'class', 'twitter-consent' );
    $consent_element->setAttribute( 'data-src', $src );
    $consent_element->nodeValue = sprintf(
      __( 'Click to load %s with third-party consent.', 'fictioneer' ),
      __( 'Twitter', 'fictioneer' )
    );

    $twitter->parentNode->insertBefore( $consent_element );
  }

  // Handle Twitter tweets
  $twitter_tweets = $xpath->query( "//blockquote[contains(@class, 'twitter-tweet')]/following-sibling::*[1]" );

  foreach ( $twitter_tweets as $twitter ) {
    $src = $twitter->getAttribute( 'src' );

    if ( ! $src ) {
      continue;
    }

    $twitter->removeAttribute( 'src' );

    $consent_element = $dom->createElement( 'div' );
    $consent_element->setAttribute( 'class', 'twitter-consent' );
    $consent_element->setAttribute( 'data-src', $src );
    $consent_element->nodeValue = sprintf(
      __( 'Click to load %s with third-party consent.', 'fictioneer' ),
      __( 'Twitter', 'fictioneer' )
    );

    $twitter->parentNode->insertBefore( $consent_element );
  }

  // Transform back to HTML and continue filter
  $body = $dom->getElementsByTagName( 'body' )->item( 0 );
  $html = $body ? $dom->saveHTML( $body ) : '';

  return preg_replace( '/<\/?body>/', '', $html );
}

if ( get_option( 'fictioneer_consent_wrappers' ) ) {
  add_filter( 'render_block', 'fictioneer_embed_consent_wrapper', 10, 2 );
}
