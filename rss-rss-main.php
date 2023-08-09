<?php
/**
 * RSS2 Feed Template to display the latest content.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/feed-rss2.php
 */

// Query posts
$posts = new WP_Query(
  array (
    'post_type' => ['post', 'fcn_story', 'fcn_chapter', 'fcn_recommendation'],
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => get_option( 'posts_per_rss' ),
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'relation' => 'OR',
        array(
          'key' => 'fictioneer_chapter_hidden',
          'value' => '0'
        ),
        array(
          'key' => 'fictioneer_chapter_hidden',
          'compare' => 'NOT EXISTS'
        )
      ),
      array(
        'relation' => 'OR',
        array(
          'key' => 'fictioneer_story_hidden',
          'value' => '0'
        ),
        array(
          'key' => 'fictioneer_story_hidden',
          'compare' => 'NOT EXISTS'
        )
      )
    ),
    'no_found_rows' => true
  )
);

// Prime author cache
if ( function_exists( 'update_post_author_caches' ) ) {
  update_post_author_caches( $posts->posts );
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
    <description><?php echo $description; ?></description>
    <language><?php echo bloginfo_rss( 'language' ); ?></language>
    <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
    <link><?php bloginfo_rss( 'url' ) ?></link>
    <lastBuildDate><?php echo $date; ?></lastBuildDate>
    <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
    <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>

    <?php if ( $cover ) : ?>
      <webfeeds:cover image="<?php echo wp_get_attachment_image_src( $cover, 'full' )[0]; ?>" />
    <?php endif; ?>

    <?php if ( has_site_icon() ) : ?>
      <webfeeds:icon><?php echo get_site_icon_url(); ?></webfeeds:icon>
    <?php endif; ?>

    <?php if ( get_option( 'site_icon', false ) ) : ?>
      <image>
        <url><?php echo get_site_icon_url( 32 ); ?></url>
        <title><?php echo $title; ?></title>
        <link><?php bloginfo_rss( 'url' ); ?></link>
        <width>32</width>
        <height>32</height>
      </image>
    <?php endif; ?>

    <?php
      while ( $posts->have_posts() ) {
        // Setup
        $posts->the_post();

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
          <description><![CDATA[<?php the_excerpt_rss() ?>]]></description>

          <?php if ( $og_image ) : ?>
            <webfeeds:featuredImage
              url="<?php echo $og_image['url']; ?>"
              width="<?php echo $og_image['width']; ?>"
              height="<?php echo $og_image['height']; ?>"
            />
          <?php endif; ?>

          <?php
            if ( $categories = wp_get_post_categories( get_the_ID() ) ) {
              foreach ( $categories as $cat ) {
                echo '<category>' . get_category( $cat )->name . '</category>';
              }
            }

            if ( $fandoms = get_the_terms( get_the_ID(), 'fcn_fandom' ) ) {
              foreach ( $fandoms as $fandom ) {
                echo '<category>' . $fandom->name . '</category>';
              }
            }

            if ( $genres = get_the_terms( get_the_ID(), 'fcn_genre' ) ) {
              foreach ( $genres as $genre ) {
                echo '<category>' . $genre->name . '</category>';
              }
            }

            if ( $characters = get_the_terms( get_the_ID(), 'fcn_character' ) ) {
              foreach ( $characters as $character ) {
                echo '<category>' . $character->name . '</category>';
              }
            }

            if ( $tags = get_the_tags( get_the_ID() ) ) {
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
