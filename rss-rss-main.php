<?php
/**
 * RSS2 Feed Template to display the latest content.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/feed-rss2.php
 */


// Query
$query_args = array(
  'fictioneer_query_name' => 'main_rss',
  'post_type' => ['post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation'],
  'post_status' => 'publish',
  'ignore_sticky_posts' => 1,
  'orderby' => 'date',
  'order' => 'DESC',
  'posts_per_page' => get_option( 'posts_per_rss' ) + 8, // Buffer in case of invalid results
  'update_post_meta_cache' => true,
  'update_post_term_cache' => true,
  'no_found_rows' => true // Improve performance
);

$query_args = apply_filters( 'fictioneer_filter_rss_main_query_args', $query_args );

$posts = get_posts( $query_args );

// Filter out hidden posts (faster than meta query)
$posts = array_filter( $posts, function ( $post ) {
  // Chapter hidden?
  if ( $post->post_type === 'fcn_chapter' ) {
    $chapter_hidden = get_post_meta( $post->ID, 'fictioneer_chapter_hidden', true );

    return empty( $chapter_hidden ) || $chapter_hidden === '0';
  }

  // Story hidden?
  if ( $post->post_type === 'fcn_story' ) {
    $story_hidden = get_post_meta( $post->ID, 'fictioneer_story_hidden', true );

    return empty( $story_hidden ) || $story_hidden === '0';
  }

  // Keep
  return true;
});

// Filter posts for whatever reason
$posts = apply_filters( 'fictioneer_filter_rss_main_posts', $posts, $query_args );

// Crop number of posts (remove buffer posts)
$posts = array_slice( $posts, 0, get_option( 'posts_per_rss' ) );

// Prime author cache
if ( function_exists( 'update_post_author_caches' ) ) {
  update_post_author_caches( $posts );
}

// Feed title
$title = sprintf( __( '%s Feed', 'fictioneer' ), get_bloginfo_rss( 'name' ) );

// Feed description
$description = sprintf( __( 'Recent updates on %s.', 'fictioneer' ), get_bloginfo_rss( 'name' ) );

// Updated time (any post)
$date = mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false );

// Cover image
$cover = get_theme_mod( 'og_image' );

// Set header
header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );

// Echo XML version
echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?>';

// Fire default action
do_action( 'rss_tag_pre', 'rss2' );

?>

<rss
  version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:wfw="http://wellformedweb.org/CommentAPI/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
  xmlns:webfeeds="http://webfeeds.org/rss/1.0"
  <?php do_action( 'rss2_ns' ); ?>
>

  <channel>

    <title><?php echo $title; ?></title>
    <description><![CDATA[<?php echo $description; ?>]]></description>
    <language><?php echo bloginfo_rss( 'language' ); ?></language>
    <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
    <link><?php bloginfo_rss( 'url' ); ?></link>
    <lastBuildDate><?php echo $date; ?></lastBuildDate>
    <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
    <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>

    <?php
      if ( $cover ) {
        $cover_src = wp_get_attachment_image_src( $cover, 'full' );

        if ( $cover_src ) {
          echo '<webfeeds:cover image="' . esc_url( $cover_src[0] ) . '" />';
        }
      }
    ?>

    <?php if ( has_site_icon() ) : ?>
      <webfeeds:icon><?php echo esc_url( get_site_icon_url() ); ?></webfeeds:icon>
    <?php endif; ?>

    <?php if ( get_option( 'site_icon', false ) ) : ?>
      <image>
        <url><?php echo esc_url( get_site_icon_url( 32 ) ); ?></url>
        <title><?php echo $title; ?></title>
        <link><?php bloginfo_rss( 'url' ); ?></link>
        <width>32</width>
        <height>32</height>
      </image>
    <?php endif; ?>

    <?php
      foreach ( $posts as $post ) {
        // Setup
        setup_postdata( $post );

        // Data
        $pub_date = mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false );
        $og_image = fictioneer_get_seo_image( $post->ID );

        // <-- Start HTML
        ?>
        <item>
          <title><?php the_title_rss(); ?></title>
          <link><?php the_permalink_rss(); ?></link>
          <pubDate><?php echo $pub_date; ?></pubDate>
          <dc:creator><?php the_author(); ?></dc:creator>
          <guid isPermaLink="false"><?php the_guid(); ?></guid>
          <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>

          <?php if ( $og_image ) : ?>
            <webfeeds:featuredImage
              url="<?php echo esc_url( $og_image['url'] ); ?>"
              width="<?php echo esc_attr( $og_image['width'] ); ?>"
              height="<?php echo esc_attr( $og_image['height'] ); ?>"
            />
          <?php endif; ?>

          <?php
            $post_id = get_the_ID();

            if ( $categories = wp_get_post_categories( $post_id ) ) {
              foreach ( $categories as $cat ) {
                echo '<category>' . get_category( $cat )->name . '</category>';
              }
            }

            if ( $fandoms = get_the_terms( $post_id, 'fcn_fandom' ) ) {
              foreach ( $fandoms as $fandom ) {
                echo '<category>' . $fandom->name . '</category>';
              }
            }

            if ( $genres = get_the_terms( $post_id, 'fcn_genre' ) ) {
              foreach ( $genres as $genre ) {
                echo '<category>' . $genre->name . '</category>';
              }
            }

            if ( $characters = get_the_terms( $post_id, 'fcn_character' ) ) {
              foreach ( $characters as $character ) {
                echo '<category>' . $character->name . '</category>';
              }
            }

            if ( $tags = get_the_tags( $post_id ) ) {
              foreach ( $tags as $tag ) {
                echo '<category>' . $tag->name . '</category>';
              }
            }

            rss_enclosure();

            do_action( 'rss2_item' );
          ?>
        </item>
        <?php // <-- End HTML
      }

      wp_reset_postdata();
    ?>

  </channel>

</rss>
