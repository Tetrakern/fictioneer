<?php

// =============================================================================
// ADD ROUTE
// =============================================================================

if ( ! function_exists( 'fictioneer_add_epub_download_endpoint' ) ) {
  /**
   * Add route to ePUB script
   *
   * @since Fictioneer 4.0
   */

  function fictioneer_add_epub_download_endpoint() {
    add_rewrite_endpoint( FICTIONEER_EPUB_ENDPOINT, EP_ROOT );
  }
}
add_action( 'init', 'fictioneer_add_epub_download_endpoint', 10 );

// =============================================================================
// DOWNLOAD
// =============================================================================

if ( ! function_exists( 'fictioneer_download_epub' ) ) {
  /**
   * Start and count ePUB download
   *
   * @since Fictioneer 4.0
   *
   * @param string $file_name File name of the ePUB to download.
   */

  function fictioneer_download_epub( $file_name, $story_id = null ) {
    // Setup
    $path = wp_upload_dir()['basedir'] . '/epubs/' . $file_name;
    $download_url = wp_upload_dir()['baseurl'] . '/epubs/' . $file_name;
    $story_id = $story_id ? $story_id : get_query_var( FICTIONEER_EPUB_ENDPOINT, null );

    // Abort if...
    if ( is_null( $story_id ) ) return;

    // Validate story ID again
    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );

    // If path to file exists...
    if ( file_exists( $path ) && $story_id ) {
      // Count downloads per ePUB version
      if ( $story_id ) {
        // Download counter(s)
        $downloads = get_post_meta( $story_id, 'fictioneer_epub_downloads', true );
        $downloads = is_array( $downloads ) ? $downloads : [];

        // Generate version key based on timestamp
        $key = md5( get_post_meta( $story_id, 'fictioneer_epub_timestamp', true ) );

        // Is this version already tracked?
        if ( array_key_exists( $key, $downloads ) ) {
          $downloads[ $key ] += 1; // Increment counter
        } else {
          $downloads[ $key ] = 1; // Start new counter
        }

        // update counter(s)
        update_post_meta( $story_id, 'fictioneer_epub_downloads', $downloads );
      }

      // Start download
      wp_redirect( $download_url );
    }

    // Terminate script
    exit;
  }
}

// =============================================================================
// HELPERS
// =============================================================================

if ( ! function_exists( 'fictioneer_epub_return_and_exit' ) ) {
  /**
   * Abort, redirect home, and terminate script
   *
   * @since Fictioneer 4.0
   */

  function fictioneer_epub_return_and_exit() {
    wp_redirect( home_url() );
    exit();
  }
}

if ( ! function_exists( 'fictioneer_nav_point' ) ) {
  /**
   * Create an return navPoint node
   *
   * @since Fictioneer 5.0
   *
   * @param DOMDocument $doc   The document to add the node to.
   * @param int         $index Current numerical index.
   * @param string      $src   Reference to file.
   * @param string      $text  Nice name of the file.
   *
   * @return DOMElement The navPoint node.
   */

  function fictioneer_nav_point( $doc, $index, $src, $text ) {
    $nav_point = $doc->createElement( 'navPoint' );
    $nav_point->setAttribute( 'id', 'navpoint' . $index );
    $nav_point->setAttribute( 'playOrder', strval( $index ) );

    $nav_content = $doc->createElement( 'content' );
    $nav_content->setAttribute( 'src', $src );

    $nav_text = $doc->createElement( 'text' );
    $nav_text->nodeValue = $text;

    $nav_label = $doc->createElement( 'navLabel' );
    $nav_label->appendChild( $nav_text );
    $nav_point->appendChild( $nav_label );
    $nav_point->appendChild( $nav_content );

    return $nav_point;
  }
}

if ( ! function_exists( 'fictioneer_fix_html_entities' ) ) {
  /**
   * Replace invalid HTML entities with XML entities
   *
   * @since Fictioneer 5.0.6
   *
   * @param string $text The string with invalid HTML entities.
   *
   * @return string The string with XML entities.
   */

  function fictioneer_fix_html_entities( $text ) {
    $text = str_replace( '&ndash;', '&#8211;&#8203;', $text ); // Add zero-width space for line break control
    $text = str_replace( '&mdash;', '&#8212;&#8203;', $text ); // Add zero-width space for line break control
    $text = str_replace( '&nbsp;', '&#160;', $text );
    $text = str_replace( '&ldquo;', '&#8220;', $text );
    $text = str_replace( '&rdquo;', '&#8221;', $text );
    $text = str_replace( '&lsquo;', '&#8216;', $text );
    $text = str_replace( '&rsquo;', '&#8217;', $text );
    $text = str_replace( '&deg;', '&#176;', $text );
    $text = str_replace( '&reg;', '&#174;', $text );
    $text = str_replace( '&copy;', '&#169;', $text );
    $text = str_replace( '&times;', '&#215;', $text );
    $text = str_replace( '&tilde;', '&#732;', $text );
    $text = str_replace( '&circ;', '&#710;', $text );

    return $text;
  }
}

// =============================================================================
// PREPARE BUILD DIRECTORY AND BASE FILES
// =============================================================================

if ( ! function_exists( 'fictioneer_prepare_build_directory' ) ) {
  /**
   * Prepare clean directory and base files for build process
   *
   * @since Fictioneer 5.0
   *
   * @param string $dir      The build template directory in the theme folder.
   * @param string $epub_dir The target ePUB directory in the uploads folder.
   * @param int    $story_id The story ID the ePUB is based on.
   */

  function fictioneer_prepare_build_directory( $dir, $epub_dir, $story_id ) {
    // Remove outdated build files from ePUB directory (if any)
    if ( file_exists( $epub_dir ) ) {
      $iterator = new RecursiveDirectoryIterator( $epub_dir, RecursiveDirectoryIterator::SKIP_DOTS );
      $files = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::CHILD_FIRST );

      foreach( $files as $file ) {
        if ( $file->isDir() ){
          rmdir( $file->getRealPath() );
        } else {
          unlink( $file->getRealPath() );
        }
      }

      rmdir( $epub_dir );
    }

    // Create clean folders for new build
    mkdir( $epub_dir, 0755, true);
    mkdir( $epub_dir . '/META-INF', 0755, true);
    mkdir( $epub_dir . '/OEBPS', 0755, true);
    mkdir( $epub_dir . '/OEBPS/Images', 0755, true);
    mkdir( $epub_dir . '/OEBPS/Text', 0755, true);
    mkdir( $epub_dir . '/OEBPS/Styles', 0755, true);

    // Copy mimetype from build template
    copy( $dir . '_build/templates/mimetype', $epub_dir . '/mimetype' );

    // Copy/Edit CSS file
    $css_path = $epub_dir . '/OEBPS/Styles/style.css';
    $css_edit = fictioneer_get_field( 'fictioneer_story_epub_custom_css', $story_id, false );

    copy( $dir . '_build/templates/style.css', $css_path );

    if ( ! empty( $css_edit ) ) {
      $css_file = fopen( $css_path, 'a' );
      fwrite( $css_file, $css_edit );
      fclose( $css_file );
    }

    // Copy fallback image
    copy( $dir . '_build/templates/image_fallback.jpg', $epub_dir . '/OEBPS/Images/image_fallback.jpg' );

    // Copy container XML
    copy(
      $dir . '_build/templates/container.xml',
      $epub_dir . '/META-INF/container.xml'
    );

    // Copy com.apple.ibooks.display-options.xml (that the world apparently needed)
    copy(
      $dir . '_build/templates/com.apple.ibooks.display-options.xml',
      $epub_dir . '/META-INF/com.apple.ibooks.display-options.xml'
    );
  }
}

// =============================================================================
// ADD COVER
// =============================================================================

if ( ! function_exists( 'fictioneer_add_epub_cover' ) ) {
  /**
   * Copy cover image and HTML file to the ePUB directory
   *
   * @since Fictioneer 5.0
   * @link https://www.php.net/manual/en/class.domelement.php
   *
   * @param string $dir      The build template directory in the theme folder.
   * @param string $epub_dir The target ePUB directory in the uploads folder.
   * @param int    $story_id The story ID the ePUB is based on.
   */

  function fictioneer_add_epub_cover( $dir, $epub_dir, $story_id ) {
    // Abort if the story has no cover image...
    if ( ! has_post_thumbnail( $story_id ) ) return;

    // Setup
    $path_parts = pathinfo( get_the_post_thumbnail_url( $story_id, 'full' ) );
    $path_extension = isset( $path_parts['extension'] ) ? $path_parts['extension'] : 'jpg';
    $extension = preg_replace( '/(?<=\.jpg|jpeg|png|gif|webp|svg|avif|apng|tiff).+/', '', '.' . $path_extension ); // Remove query params

    // Copy image
    copy(
      get_the_post_thumbnail_url( $story_id, 'full' ),
      $epub_dir . '/OEBPS/Images/cover' . $extension
    );

    // Build HTML from template
    $cov = new DOMDocument();
    $cov->load( $dir . '_build/templates/cover.html' );
    $cov->preserveWhiteSpace = false;
    $cov->formatOutput = true;
    $cover_img = $cov->getElementsByTagName( 'img' )->item( 0 );
    $cover_img->setAttribute( 'src', '../Images/cover' . $extension );
    $cover_file = fopen( $epub_dir . '/OEBPS/Text/cover.html', 'w' );

    // Write HTML to new file in ePUB directory
    fwrite( $cover_file, $cov->saveXML() );
    fclose( $cover_file );
  }
}

// =============================================================================
// ADD CHAPTERS
// =============================================================================

if ( ! function_exists( 'fictioneer_add_epub_chapters' ) ) {
  /**
   * Process and add chapters to ePUB directory and lists
   *
   * @since Fictioneer 5.0
   *
   * @param string $dir      The build template directory in the theme folder.
   * @param string $epub_dir The target ePUB directory in the uploads folder.
   * @param array  $chapters Chapters in the story.
   *
   * @return array Collected items for the toc, ncx, opf, and image lists.
   */

  function fictioneer_add_epub_chapters( $dir, $epub_dir, $chapters ) {
    // Setup
    $toc_list = [];
    $ncx_list = [];
    $opf_list = [];
    $image_list = [];
    $index = 0;

    // Abort if...
    if ( empty( $chapters ) ) fictioneer_epub_return_and_exit();

    // Process chapters
    foreach ( $chapters as $post_id ) {
      $post = get_post( $post_id );

      // Skip if...
      if ( fictioneer_get_field( 'fictioneer_chapter_no_chapter' ) || get_post_status() !== 'publish' ) continue;

      // Setup
      $title = fictioneer_get_safe_title( $post_id );
      $content = apply_filters( 'the_content', $post->post_content );
      $is_hidden = fictioneer_get_field( 'fictioneer_chapter_hidden', $post_id );
      $processed = false;
      $index++;

      // Create empty chapter file from build template
      $doc = new DOMDocument();
      $doc->loadHTMLFile( $dir . '_build/templates/chapter.html' );
      $doc->preserveWhiteSpace = false;
      $doc->formatOutput = true;
      $doc->xmlStandalone = false;

      // Prepare nodes
      $finder = new DOMXpath( $doc );
      $head = $doc->getElementsByTagName( 'head' )->item( 0 );
      $frame = $finder->query( "*/div[contains(@class, 'content')]" )[0];
      $frame->setAttribute( 'id', 'chapter-' . $index );

      // Password?
      if ( ! empty( $post->post_password ) && ! $processed ) {
        $processed = true;
        $head->appendChild( $doc->createElement( 'title', _x( 'Password Protected Chapter', 'ePUB', 'fictioneer' ) ) );
        $frame->appendChild( $doc->createElement( 'h1', $title ) );
        $frame->appendChild(
          $doc->createElement( 'p', _x( 'This chapter requires a password and is only available on the website.', 'ePUB', 'fictioneer' ) )
        );
      }

      // Hidden?
      if ( $is_hidden && ! $processed ) {
        $processed = true;
        $head->appendChild( $doc->createElement( 'title', _x( 'Hidden Chapter', 'ePUB', 'fictioneer' ) ) );
        $frame->appendChild( $doc->createElement( 'h1', _x( 'Hidden Chapter', 'ePUB', 'fictioneer' ) ) );
        $frame->appendChild( $doc->createElement( 'p', _x( 'This chapter is hidden and only available on the website.', 'ePUB', 'fictioneer' ) ) );
      }

      // No content?
      if ( empty( $content ) && ! $processed ) {
        $processed = true;
        $head->appendChild( $doc->createElement( 'title', $title ) );
        $frame->appendChild( $doc->createElement( 'h1', $title ) );
        $frame->appendChild( $doc->createElement( 'p', _x( 'This chapter is empty.', 'ePUB', 'fictioneer' ) ) );
      }

      // Can chapter be processed?
      if ( ! $processed ) {
        $processed = true;

        // Add title to <head>
        $head->appendChild( $doc->createElement( 'title', $title ) );

        // Add title to frame if not hidden
        if ( ! fictioneer_get_field( 'fictioneer_chapter_hide_title' ) ) {
          $frame->appendChild( $doc->createElement( 'h1', $title ) );
        }

        // Start cleaning up content...
        $content = str_replace( '<body>', '<div class="content__inner">', $content );
        $content = str_replace( '</body>', '</div>', $content );
        $content = str_replace( 'data-type="URL"', '', $content );
        $content = preg_replace( ['(\s+)u', '(^\s|\s$)u'], [' ', ''], $content );
        $content = preg_replace( '/data-align="([^"]*)"/', '', $content );

        // Create temporary file to continue cleaning up content...
        $inner = new DOMDocument();
        $inner->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
        $inner->preserveWhiteSpace = false;
        $inner->formatOutput = true;
        $inner_finder = new DOMXpath( $inner );

        // Add entire content block to chapter file
        foreach ( $inner_finder->query( "*/div[contains(@class, 'content__inner')]" ) as $node ) {
          $frame->appendChild( $doc->importNode( $node, true ) );
        }

        // Remove sensitive-alternatives (only full chapters) from chapter file
        foreach ( $finder->query( "//*[contains(@class, 'sensitive-alternative')]" ) as $node ) {
          $node->parentNode->removeChild( $node );
        }

        // Remove aria-hidden attributes from spacers
        foreach ( $finder->query( "//*[contains(@class, 'wp-block-spacer')]" ) as $node ) {
          $node->removeAttribute( 'aria-hidden' );
        }

        // Remove data-paragraph-id attributes from paragraphs in chapter file
        foreach ( $doc->getElementsByTagName( 'p' ) as $node ) {
          $node->removeAttribute( 'data-paragraph-id' );
        }

        // Remove data-id attributes from links in chapter file
        foreach ( $doc->getElementsByTagName( 'a' ) as $node ) {
          $node->removeAttribute( 'data-id' );
        }

        // Remove empty link tags from chapter file
        foreach ( $finder->query( "//a[not(node())]", $frame ) as $node ) {
          $node->parentNode->removeChild( $node );
        }

        // Add tr-footer class to <tr> children of tfoot
        foreach ( $doc->getElementsByTagName( 'tfoot' ) as $node ) {
          foreach ( $node->childNodes as $child_node ) {
            $child_node->setAttribute( 'class', 'tr-footer' );
          }
        }

        // Copy images displayed in chapter to ePUB directory and remember them for later
        foreach ( $finder->query( "//*[contains(@class, 'wp-block-image')]" ) as $node ) {
          // Extract image from wrapper(s)
          $img = $node->childNodes->item( 0 );

          if ( $img->childNodes->item( 0 ) ) {
            $img = $img->childNodes->item( 0 );
          }

          // Get attributes
          $url = urldecode( $img->getAttribute( 'src' ) );
          $alt = urldecode( $img->getAttribute( 'alt' ) );

          // Abort if...
          if ( ! $url ) continue;

          // Prepare path (remove unwanted extensions, e.g. '.jpg?_i=AA')
          $path_parts = pathinfo( $url );
          $path_extension = isset( $path_parts['extension'] ) ? $path_parts['extension'] : 'jpg';
          $extension = preg_replace( '/(?<=\.jpg|jpeg|png|gif|webp|svg|avif|apng).+/', '', '.' . $path_extension );

          // Copy image to ePUb directory
          copy( $url, $epub_dir . '/OEBPS/Images/' . $path_parts['filename'] . $extension );

          // Create new image node
          $new_img = $doc->createElement( 'img' );
          $new_img->setAttribute( 'src', '../Images/' . $path_parts['basename'] );
          $new_img->setAttribute( 'alt', $alt );

          // Add image to list
          $image_list[] = [$path_parts['filename'] . $extension, substr( $extension, 1 )];

          // Replace wrapped image node with cleaned image node
          $node->parentNode->insertBefore( $new_img, $node->nextSibling );
          $node->parentNode->removeChild( $node );
        }

        // Remove all remaining WP blocks that are not pullquotes or tables
        foreach (
          $finder->query( "//figure[not(contains(@class, 'wp-block-pullquote') or contains(@class, 'wp-block-table'))]" ) as $node
        ) {
          $node->parentNode->removeChild( $node );
        }

        // Replace classes from figcaption elements
        foreach (
          $finder->query( "//figcaption" ) as $node
        ) {
          $node->setAttribute( 'class', 'figcaption' );
        }

        // Replace classes from cite elements
        foreach (
          $finder->query( "//cite" ) as $node
        ) {
          $node->setAttribute( 'class', 'cite' );
        }
      }

      // Add to OPF list
      array_push( $opf_list, $index );

      // Add to NCX list
      $nav_item_title = ( ! $is_hidden ) ? $title : _x( 'Unavailable', 'ePUB', 'fictioneer' );
      array_push( $ncx_list, array( $nav_item_title, $index ) );

      // Add to ToC list
      array_push( $toc_list, array( "$nav_item_title", "../Text/chapter-$index.html", $index ) );

      // Fix remaining elements not valid in ePUBs...
      $file_content = $doc->saveXML();
      $file_content = str_replace( '<figure', '<div', $file_content );
      $file_content = str_replace( '</figure>', '</div>', $file_content );
      $file_content = str_replace( '<cite', '<div', $file_content );
      $file_content = str_replace( '</cite>', '</div>', $file_content );
      $file_content = str_replace( '<figcaption', '<div', $file_content );
      $file_content = str_replace( '</figcaption>', '</div>', $file_content );
      $file_content = str_replace( '<u>', '<span class="underline">', $file_content );
      $file_content = str_replace( '</u>', '</span>', $file_content );
      $file_content = str_replace( '<s>', '<span class="strike">', $file_content );
      $file_content = str_replace( '</s>', '</span>', $file_content );
      $file_content = str_replace( '<tfoot>', '<tbody>', $file_content );
      $file_content = str_replace( '</tfoot>', '</tbody>', $file_content );

      // Fix invalid entities (because of course)
      $file_content = fictioneer_fix_html_entities( $file_content );

      // Save chapter file in ePUB directory
      $file_path = $epub_dir . "/OEBPS/Text/chapter-$index.html";
      $file = fopen( $file_path, 'w' );
      fwrite( $file, $file_content );
      fclose( $file );
    }

    // Terminate script if no chapter has been added
    if ( $index == 0 ) exit();

    // Return lists
    return array(
      'toc' => $toc_list,
      'ncx' => $ncx_list,
      'opf' => $opf_list,
      'images' => $image_list
    );
  }
}

// =============================================================================
// OPF FILE
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_epub_opf' ) ) {
  /**
   * Generate and add OPF file to ePUB directory
   *
   * @since Fictioneer 5.0
   *
   * @param int   $story_id The story ID the ePUB is based on.
   * @param array $args     Collection of ePUB data.
   */

  function fictioneer_generate_epub_opf( $story_id, $args ) {
    // Description
    $description = wp_strip_all_tags( $args['fictioneer_story_short_description'], true );

    // Create temporary file from build template
    $opf = new DOMDocument();
    $opf->load( $args['dir'] . '_build/templates/content.opf' );
    $opf->preserveWhiteSpace = false;
    $opf->formatOutput = true;

    // Update attributes with values
    $opf->getElementsByTagName( 'identifier' )->item( 0 )->nodeValue = $args['permalink'];
    $opf->getElementsByTagName( 'rights' )->item( 0 )->nodeValue = sprintf(
      __( 'Original Content Copyright © %s. All rights reserved.', 'fictioneer' ),
      implode( ', ', $args['all_authors'] )
    );
    $opf->getElementsByTagName( 'title' )->item( 0 )->nodeValue = $args['title'];
    $opf->getElementsByTagName( 'description' )->item( 0 )->nodeValue = $description;
    $opf->getElementsByTagName( 'publisher' )->item( 0 )->nodeValue = $args['home_link'];
    $opf->getElementsByTagName( 'source' )->item( 0 )->nodeValue = $args['permalink'];
    $opf->getElementsByTagName( 'creator' )->item( 0 )->nodeValue = $args['author'];
    $opf->getElementsByTagName( 'date' )->item( 0 )->nodeValue = $args['epub_last_updated'];

    // Get list nodes
    $manifest = $opf->getElementsByTagName( 'manifest' )->item( 0 );
    $spine = $opf->getElementsByTagName( 'spine' )->item( 0 );
    $guide = $opf->getElementsByTagName( 'guide' )->item( 0 );

    // Cover node (if any)
    if( has_post_thumbnail( $story_id ) ) {
      $path_parts = pathinfo( get_the_post_thumbnail_url( $story_id, 'full' ) );
      $path_extension = isset( $path_parts['extension'] ) ? $path_parts['extension'] : 'jpg';
      $extension = preg_replace( '/(?<=\.jpg|jpeg|png|gif|webp|svg|avif|apng).+/', '', '.' . $path_extension );

      $cover_meta = $opf->createElement( 'meta' );
      $cover_meta->setAttribute( 'name', 'cover' );
      $cover_meta->setAttribute( 'content', 'cover' . $extension );
      $opf->getElementsByTagName( 'metadata' )->item( 0 )->appendChild( $cover_meta );

      $m_item = $opf->createElement( 'item' );
      $m_item->setAttribute( 'href', 'Images/cover' . $extension );
      $m_item->setAttribute( 'id', 'cover' . $extension );
      $m_item->setAttribute( 'media-type', 'image/' . substr( $extension, 1 ) );
      $m_item->setAttribute( 'fallback', 'fictioneer_image_fallbackjpg' );
      $manifest->appendChild( $m_item );

      $m_item = $opf->createElement( 'item' );
      $m_item->setAttribute( 'href', 'Text/cover.html' );
      $m_item->setAttribute( 'id', 'cover' );
      $m_item->setAttribute( 'media-type', 'application/xhtml+xml' );
      $manifest->appendChild( $m_item );

      $s_item = $opf->createElement( 'itemref' );
      $s_item->setAttribute( 'idref', 'cover' );
      $spine->appendChild( $s_item );

      $g_item = $opf->createElement( 'reference' );
      $g_item->setAttribute( 'href', 'Text/cover.html' );
      $g_item->setAttribute( 'title', 'Cover' );
      $g_item->setAttribute( 'type', 'cover' );
      $guide->appendChild( $g_item );
    }

    // Image nodes
    foreach ( $args['image_list'] as $img ) {
      $m_item = $opf->createElement( 'item' );
      $m_item->setAttribute( 'href', 'Images/' . $img[0] );
      $m_item->setAttribute( 'id', $img[0] );
      $m_item->setAttribute( 'media-type', 'image/' . $img[1] );
      $m_item->setAttribute( 'fallback', 'fictioneer_image_fallbackjpg' );
      $manifest->appendChild( $m_item );
    }

    // Front Matter node
    $s_item = $opf->createElement( 'itemref' );
    $s_item->setAttribute( 'idref', 'frontmatter' );
    $spine->appendChild( $s_item );

    // Table of Contents node
    $s_item = $opf->createElement( 'itemref' );
    $s_item->setAttribute( 'idref', 'toc' );
    $spine->appendChild( $s_item );

    // $opf_list is filled in the chapters loop
    foreach ( $args['opf_list'] as $index ) {
      $m_item = $opf->createElement( 'item' );
      $m_item->setAttribute( 'href', "Text/chapter-$index.html" );
      $m_item->setAttribute( 'id', "chapter-$index" );
      $m_item->setAttribute( 'media-type', 'application/xhtml+xml' );
      $manifest->appendChild( $m_item );

      $s_item = $opf->createElement( 'itemref' );
      $s_item->setAttribute( 'idref', "chapter-$index" );
      $spine->appendChild( $s_item );

      $g_item = $opf->createElement( 'reference' );
      $g_item->setAttribute( 'href', "Text/chapter-$index.html" );
      $g_item->setAttribute( 'type', 'text' );
      $guide->appendChild( $g_item );
    }

    // Afterword node
    if ( fictioneer_get_field( 'fictioneer_story_epub_afterword', $story_id, false ) ) {
      $m_item = $opf->createElement( 'item' );
      $m_item->setAttribute( 'href', 'Text/afterword.html' );
      $m_item->setAttribute( 'id', 'afterword' );
      $m_item->setAttribute( 'media-type', 'application/xhtml+xml' );
      $manifest->appendChild( $m_item );

      $s_item = $opf->createElement( 'itemref' );
      $s_item->setAttribute( 'idref', 'afterword' );
      $spine->appendChild( $s_item );

      $g_item = $opf->createElement( 'reference' );
      $g_item->setAttribute( 'href', 'Text/afterword.html' );
      $g_item->setAttribute( 'type', 'text' );
      $guide->appendChild( $g_item );
    }

    // Save in ePUB directory
    $opf_file = fopen( $args['epub_dir'] . '/OEBPS/content.opf', 'w' );
    fwrite( $opf_file, $opf->saveXML() );
    fclose( $opf_file );
  }
}

// =============================================================================
// NCX
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_epub_ncx' ) ) {
  /**
   * Generate and add NCX file to ePUB directory
   *
   * @since Fictioneer 5.0
   * @link https://www.php.net/manual/en/class.domelement.php
   *
   * @param int   $story_id The story ID the ePUB is based on.
   * @param array $args     Collection of ePUB data.
   */

   function fictioneer_generate_epub_ncx( $story_id, $args ) {
    // Create work document from build template
    $ncx = new DOMDocument();
    $ncx->load( $args['dir'] . '_build/templates/toc.ncx' );
    $ncx->preserveWhiteSpace = false;
    $ncx->formatOutput = true;

    // Get node and set attributes
    $navmap = $ncx->getElementsByTagName( 'navMap' )->item( 0 );
    $node = $ncx->createElement( 'meta' );
    $node->setAttribute( 'content', get_permalink( $story_id ) );
    $node->setAttribute( 'name', 'dtb:uid' );
    $ncx->getElementsByTagName( 'head' )->item( 0 )->appendChild( $node );
    $ncx->getElementsByTagName( 'docTitle' )->item( 0 )->getElementsByTagName( 'text' )->item( 0 )->nodeValue = $args['title'];

    // Start index
    $index = 1;

    // Add cover node (if any)
    if ( has_post_thumbnail( $story_id ) ) {
      $navmap->appendChild( fictioneer_nav_point( $ncx, $index, 'Text/cover.html', __( 'Cover', 'fictioneer' ) ) );
      $index++;
    }

    // Add front matter node
    $navmap->appendChild( fictioneer_nav_point( $ncx, $index, 'Text/frontmatter.html', __( 'Front Matter', 'fictioneer' ) ) );
    $index++;

    // Add table of contents node
    $navmap->appendChild( fictioneer_nav_point( $ncx, $index, 'Text/toc.html', __( 'Table of Contents', 'fictioneer' ) ) );
    $index++;

    // Add each item in list
    foreach ( $args['ncx_list'] as $item ) {
      $navmap->appendChild( fictioneer_nav_point( $ncx, $index, 'Text/chapter-' . $item[1] . '.html', $item[1] . '. ' . $item[0] ) );
      $index++;
    }

    // Add afterword node
    if ( fictioneer_get_field( 'fictioneer_story_epub_afterword', $story_id, false ) ) {
      $navmap->appendChild( fictioneer_nav_point( $ncx, $index, 'Text/afterword.html', __( 'Afterword', 'fictioneer' ) ) );
      $index++;
    }

    // Save NCX to ePUB directory
    $ncx_file = fopen( $args['epub_dir'] . '/OEBPS/toc.ncx', 'w' );
    fwrite( $ncx_file, $ncx->saveXML() );
    fclose( $ncx_file );
  }
}

// =============================================================================
// TABLE OF CONTENTS
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_epub_toc' ) ) {
  /**
   * Generate and add table of content file to ePUB directory
   *
   * @since Fictioneer 5.0
   *
   * @param array $args     Collection of ePUB data.
   */

  function fictioneer_generate_epub_toc( $args ) {
    // Create work document from build template
    $toc = new DOMDocument();
    $toc->loadHTMLFile( $args['dir'] . '_build/templates/toc.html' );
    $toc->preserveWhiteSpace = false;
    $toc->formatOutput = true;
    $toc->xmlStandalone = false;

    // Get list node
    $table_of_contents = $toc->getElementsByTagName( 'ul' )->item( 0 );

    // Add each item in list
    foreach ( $args['toc_list'] as $item ) {
      $toc_item = $toc->createElement( 'li' );
      $toc_link = $toc->createElement( 'a' );
      $toc_title = $toc->createElement( 'span', $item[0] );
      $toc_num = $toc->createElement( 'span', $item[2] . '.' );
      $toc_num->setAttribute( 'class', 'num' );
      $toc_link->setAttribute( 'href', $item[1] );
      $toc_link->appendChild( $toc_num );
      $toc_link->appendChild( $toc_title );
      $toc_item->appendChild( $toc_link );
      $table_of_contents->appendChild( $toc_item );
    }

    // Save to ePUB directory
    $toc_file = fopen( $args['epub_dir'] . '/OEBPS/Text/toc.html', 'w' );
    fwrite( $toc_file, $toc->saveXML() );
    fclose( $toc_file );
  }
}

// =============================================================================
// FRONT MATTER
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_epub_front_matter' ) ) {
  /**
   * Generate and add front matter file to ePUB directory
   *
   * @since Fictioneer 5.0
   *
   * @param int   $story_id The story ID the ePUB is based on.
   * @param array $args     Collection of ePUB data.
   */

  function fictioneer_generate_epub_front_matter( $story_id, $args ) {
    // Setup
    $preface = fictioneer_get_content_field( 'fictioneer_story_epub_preface', $story_id );
    $preface = fictioneer_fix_html_entities( $preface );
    $short_description = fictioneer_get_content_field( 'fictioneer_story_short_description', $story_id );
    $short_description = fictioneer_fix_html_entities( $short_description );

    // Create work document from build template
    $ftm = new DOMDocument();
    $ftm->loadHTMLFile( $args['dir'] . '_build/templates/frontmatter.html' );
    $ftm->preserveWhiteSpace = false;
    $ftm->formatOutput = true;
    $ftm->xmlStandalone = false;

    // Get main container and add title
    $ftm_frame = $ftm->getElementsByTagName( 'div' )->item( 0 ); // Only one at the start
    $ftm_frame->appendChild( $ftm->createElement( 'h1', $args['title'] ) );

    // Add preface (if any)
    if ( ! empty( $preface ) ) {
      // Clean-up...
      $preface = mb_convert_encoding( $preface, 'HTML-ENTITIES', 'UTF-8' );
      $preface = str_replace( '&amp;', '&', $preface );
      $preface = preg_replace( '/(\r|\n){2,}/', '</p><p>', $preface );

      if ( substr( $preface, 0, 1 ) !== '<' ) {
        $preface = '<p>' . str_replace( '<p></p>', '</p>', $preface );
      }

      $preface = str_replace( '<p>&nbsp;</p>', '', $preface );
      $preface = '<div class="preface">' . $preface . '</div>';

      // Create temporary work document
      $temp = new DOMDocument();
      $temp->loadHTML( mb_convert_encoding( $preface, 'HTML-ENTITIES', 'UTF-8' ) );
      $temp->preserveWhiteSpace = false;
      $temp->formatOutput = true;
      $temp_finder = new DOMXpath( $temp );

      // Extract content and append to main container
      foreach ( $temp_finder->query( "*/div[@class='preface']" )[0]->childNodes as $node ) {
        $ftm_frame->appendChild( $ftm->importNode( $node, true ) );
      }
    }

    if ( ! empty( $short_description ) ) {
      // Add heading
      $ftm_frame->appendChild( $ftm->createElement( 'h2', __( 'Description', 'fictioneer' ) ) );

      // Clean-up...
      $short_description = mb_convert_encoding( $short_description, 'HTML-ENTITIES', 'UTF-8' );
      $short_description = str_replace( '&amp;', '&', $short_description );
      $short_description = preg_replace( '/(\r|\n){2,}/', '</p><p>', $short_description );

      if ( substr( $short_description, 0, 1 ) !== '<' ) {
        $short_description = '<p>' . str_replace( '<p></p>', '</p>', $short_description );
      }

      $short_description = str_replace( '<p>&nbsp;</p>', '', $short_description );
      $short_description = '<div class="description"><p>' . $short_description . '</p></div>';

      // Create temporary work document
      $temp = new DOMDocument();
      $temp->loadHTML( mb_convert_encoding( $short_description, 'HTML-ENTITIES', 'UTF-8' ) );
      $temp->preserveWhiteSpace = false;
      $temp->formatOutput = true;
      $temp_finder = new DOMXpath( $temp );

      // Extract content and append to main container
      foreach ( $temp_finder->query( "*/div[@class='description']" )[0]->childNodes as $node ) {
        $ftm_frame->appendChild( $ftm->importNode( $node, true ) );
      }
    }

    // Save to ePUB directory
    $ftm_file = fopen( $args['epub_dir'] . '/OEBPS/Text/frontmatter.html', 'w' );
    fwrite( $ftm_file, $ftm->saveXML() );
    fclose( $ftm_file );
  }
}

// =============================================================================
// AFTERWORD
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_epub_afterword' ) ) {
  /**
   * Generate and add afterword file to ePUB directory
   *
   * @since Fictioneer 5.0
   *
   * @param int   $story_id The story ID the ePUB is based on.
   * @param array $args     Collection of ePUB data.
   */

  function fictioneer_generate_epub_afterword( $story_id, $args ) {
    // Setup
    $afterword = fictioneer_get_content_field( 'fictioneer_story_epub_afterword', $story_id );
    $afterword = fictioneer_fix_html_entities( $afterword );

    if ( ! empty( $afterword ) ) {
      // Create work document from build template
      $atw = new DOMDocument();
      $atw->loadHTMLFile( $args['dir'] . '_build/templates/afterword.html' );
      $atw->preserveWhiteSpace = false;
      $atw->formatOutput = true;
      $atw->xmlStandalone = false;

      // Get main container
      $atw_frame = $atw->getElementsByTagName( 'div' )->item( 0 ); // Only one at the start

      // Clean-up...
      $afterword = mb_convert_encoding( $afterword, 'HTML-ENTITIES', 'UTF-8' );
      $afterword = str_replace( '&amp;', '&', $afterword );
      $afterword = preg_replace( '/(\r|\n){2,}/', '</p><p>', $afterword );

      if ( substr( $afterword, 0, 1 ) !== '<' ) {
        $afterword = '<p>' . str_replace( '<p></p>', '</p>', $afterword );
      }

      $afterword = str_replace( '<p>&nbsp;</p>', '', $afterword );
      $afterword = '<div class="afterword">' . $afterword . '</div>';

      // Create temporary work document
      $temp = new DOMDocument();
      $temp->loadHTML( mb_convert_encoding( $afterword, 'HTML-ENTITIES', 'UTF-8' ) );
      $temp->preserveWhiteSpace = false;
      $temp->formatOutput = true;
      $temp_finder = new DOMXpath( $temp );

      // Extract content and append to main container
      foreach ( $temp_finder->query( "*/div[@class='afterword']" )[0]->childNodes as $node ) {
        $atw_frame->appendChild( $atw->importNode( $node, true ) );
      }

      // Save to ePUB directory
      $atw_file = fopen( $args['epub_dir'] . '/OEBPS/Text/afterword.html', 'w' );
      fwrite( $atw_file, $atw->saveXML() );
      fclose( $atw_file );
    }
  }
}

// =============================================================================
// GENERATE EPUB
// =============================================================================

if ( ! function_exists( 'fictioneer_generate_epub' ) ) {
  /**
   * Generate ePUB and save it to the uploads directory
   *
   * @since Fictioneer 4.0
   */

  function fictioneer_generate_epub() {
    // Get story ID from parameter
    $story_id = get_query_var( FICTIONEER_EPUB_ENDPOINT, null );

    // Abort if this is not an /download-epub/ URL...
    if ( is_null( $story_id ) ) return;

    // Abort if no story ID provided or ePUB download is disabled...
    if (
      empty( $story_id ) ||
      ! get_option( 'fictioneer_enable_epubs' ) ||
      fictioneer_get_field( 'fictioneer_story_no_epub', $story_id )
    ) {
      fictioneer_epub_return_and_exit();
    }

    // Validate story ID
    $story_id = fictioneer_validate_id( $story_id, 'fcn_story' );

    // Abort if not a valid story ID or password protected...
    if ( ! $story_id || post_password_required( $story_id ) ) {
      fictioneer_epub_return_and_exit();
    }

    // Setup
    $story = get_post( $story_id );
    $story_title = fictioneer_get_safe_title( $story_id );
    $dir = get_template_directory() . '/epubs/';
    $folder = sanitize_file_name( $story->post_name ); // This is the slug!
    $chapters = fictioneer_get_field( 'fictioneer_story_chapters', $story_id ); // ACF formatted output
    $author = get_the_author_meta( 'display_name', $story->post_author );
    $co_authors = fictioneer_get_field( 'fictioneer_story_co_authors', $story_id ) ?? [];
    $all_authors = [];
    $home_link = home_url();
    $short_description = mb_convert_encoding( fictioneer_get_content_field( 'fictioneer_story_short_description', $story_id, false ), 'HTML-ENTITIES', 'UTF-8' );
    $short_description = fictioneer_fix_html_entities( $short_description );
    $last_modified = get_the_modified_date( 'Y-m-d H:i:s', $story_id ); // Story
    $last_updated = get_post_meta( $story_id, 'fictioneer_epub_timestamp', true ); // ePUB
    $toc_list = [];
    $ncx_list = [];
    $opf_list = [];
    $image_list = [];

    // Build list of authors
    if ( ! empty( $co_authors ) ) {
      foreach ( $co_authors as $co_author_id ) {
        $co_author_name = get_the_author_meta( 'display_name', intval( $co_author_id ) );
        if ( ! empty( $co_author_name ) && $co_author_name != $author ) $all_authors[] = $co_author_name;
      }
    }

    $co_authors = $all_authors; // Names of co-authors
    array_unshift( $all_authors, $author ); // Prepend main author

    // Abort if post is not a story, the folder an empty string or contains the build templates...
    if ( get_post_type( $story_id ) !== 'fcn_story' || empty( $folder ) || $folder == '_build' ) {
      fictioneer_epub_return_and_exit();
    }

    // Uploads directory path
    wp_mkdir_p( trailingslashit( wp_upload_dir()['basedir'] ) . 'epubs' );
    $uploads_dir = wp_upload_dir()['basedir'] . '/epubs/';
    $epub_dir = $uploads_dir . $folder;

    // Abort if the build templates are missing or the story has no chapters...
    if ( ! file_exists( $dir . '_build/templates' ) || empty( $chapters ) ) {
      fictioneer_epub_return_and_exit();
    }

    // Download last generated ePUB if still up-to-date
    if ( $last_updated === $last_modified && file_exists( $uploads_dir . "$folder.epub" ) ) {
      fictioneer_download_epub( "$folder.epub", $story_id );
    }

    // Prepare build clean directory
    fictioneer_prepare_build_directory( $dir, $epub_dir, $story_id );

    // Copy cover image (if any)
    fictioneer_add_epub_cover( $dir, $epub_dir, $story_id );

    // Add chapters and merge returned lists
    $lists = fictioneer_add_epub_chapters( $dir, $epub_dir, $chapters );
    $toc_list = array_merge( $toc_list, $lists['toc'] );
    $ncx_list = array_merge( $ncx_list, $lists['ncx'] );
    $opf_list = array_merge( $opf_list, $lists['opf'] );
    $image_list = array_merge( $image_list, $lists['images'] );

    // Prepare shared arguments
    $epub_args = array(
      'home_link' => $home_link,
      'story_id' => $story_id,
      'title' => fictioneer_fix_html_entities( $story_title ),
      'permalink' => get_permalink( $story_id ),
      'author' => $author,
      'co_authors' => $co_authors,
      'all_authors' => $all_authors,
      'fictioneer_story_short_description' => $short_description,
      'story_last_modified' => $last_modified,
      'epub_last_updated' => wp_date( 'c', strtotime( $last_updated ) ),
      'dir' => $dir,
      'uploads_dir' => $uploads_dir,
      'epub_dir' => $epub_dir,
      'toc_list' => $toc_list,
      'ncx_list' => $ncx_list,
      'opf_list' => $opf_list,
      'image_list' => $image_list,
    );

    // Add OPF file
    fictioneer_generate_epub_opf( $story_id, $epub_args );

    // Add NCX file
    fictioneer_generate_epub_ncx( $story_id, $epub_args );

    // Add table of contents
    fictioneer_generate_epub_toc( $epub_args );

    // Add front matter
    fictioneer_generate_epub_front_matter( $story_id, $epub_args );

    // Add afterword
    fictioneer_generate_epub_afterword( $story_id, $epub_args );

    // Zip as epub and save to uploads directory
    $zip = new ZipArchive;
    $zip->open( $uploads_dir . "$folder.epub", ZipArchive::CREATE | ZipArchive::OVERWRITE );
    $directory = $uploads_dir . $folder;
    $mimetype_path = $uploads_dir . $folder . '/mimetype';
    $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory ), RecursiveIteratorIterator::LEAVES_ONLY );

    $zip->addFile( $mimetype_path, substr( $mimetype_path, strlen( $directory ) + 1) );

    foreach ( $files as $name => $file ) {
      if ( $name == 'mimetype' ) continue;

      if ( ! $file->isDir() ) {
        $filePath = $file->getRealPath();
        $relativePath = substr( $filePath, strlen( $directory ) + 1);

        $zip->addFile( $filePath, $relativePath );
      }
    }

    $zip->close();

    // Clean up build data...
    if ( file_exists( $uploads_dir . $folder ) ) {
      $iterator = new RecursiveDirectoryIterator( $uploads_dir . $folder, RecursiveDirectoryIterator::SKIP_DOTS );
      $files = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::CHILD_FIRST );

      foreach( $files as $file ) {
        if ( $file->isDir() ){
          rmdir( $file->getRealPath() );
        } else {
          unlink( $file->getRealPath() );
        }
      }
      rmdir( $uploads_dir . $folder );
    }

    // Remember date
    update_post_meta( $story_id, 'fictioneer_epub_timestamp', $last_modified );

    // Download
    fictioneer_download_epub( "$folder.epub", $story_id );
  }
}
add_action( 'template_redirect', 'fictioneer_generate_epub', 10 );

?>
