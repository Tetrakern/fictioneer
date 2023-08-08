<?php
/**
 * RSS2 Feed Template to display chapters in a story
 *
 * Compiles an RSS feed for a given story ID with chapters up to the maximum
 * defined under Settings > Reading > "Syndication feeds show the most recent"
 * (posts_per_rss). The feed will not work if a password is required unless
 * the viewer has entered the password.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/feed-rss2.php
 */

// Get ID from parameter
$story_id = fictioneer_validate_id( $_GET[ 'story_id' ] ?? 0, 'fcn_story' );
$is_hidden = fictioneer_get_field( 'fictioneer_story_hidden', $story_id ?: 0 ) ?: 0;

// Abort if not a valid story ID or password protected
if ( ! $story_id || $is_hidden || post_password_required( $story_id ) ) {
  wp_redirect( home_url() );
  exit();
}

// Get story data
$story = fictioneer_get_story_data( $story_id, false ); // Does not refresh comment count!
$chapters = fictioneer_get_field( 'fictioneer_story_chapters', $story_id );

// Feed title
$title = sprintf(
  _x( '%1$s - %2$s Chapters', 'Story Feed: [Site] - [Story] Chapters.', 'fictioneer' ),
  get_bloginfo_rss( 'name' ),
  get_the_title( $story_id )
);

// Feed description
$description = sprintf(
  _x( 'Recent chapters of %s.', 'Story Feed: Recent chapters of [Story].', 'fictioneer' ),
  get_the_title( $story_id )
);

// Updated time (story)
$date = mysql2date( 'D, d M Y H:i:s +0000', get_post_modified_time( 'Y-m-d H:i:s', true, $story_id ), false );

// Cover image
$cover = fictioneer_get_seo_image( $story_id );

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
    <link><?php echo esc_url( get_permalink( $story_id ) ); ?></link>
    <lastBuildDate><?php echo $date; ?></lastBuildDate>
    <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
    <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>

    <?php if ( $cover && is_array( $cover ) ) : ?>
      <webfeeds:cover image="<?php echo $cover['url']; ?>" />
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
      if ( $story['fandoms'] ) {
        foreach ( $story['fandoms'] as $fandom ) {
          echo '<category>' . $fandom->name . '</category>';
        }
      }

      if ( $story['genres'] ) {
        foreach ( $story['genres'] as $genre ) {
          echo '<category>' . $genre->name . '</category>';
        }
      }

      if ( $story['characters'] ) {
        foreach ( $story['characters'] as $character ) {
          echo '<category>' . $character->name . '</category>';
        }
      }

      if ( $story['tags'] ) {
        foreach ( $story['tags'] as $tag ) {
          echo '<category>' . $tag->name . '</category>';
        }
      }
    ?>

    <?php
      if ( $chapters ) {
        // Reverse to get latest first
        $chapters = array_reverse( $chapters );

        // Limit items
        $terminator = get_option( 'posts_per_rss' );

        // Query
        $query_args = array(
          'post_type' => 'fcn_chapter',
          'post_status' => 'publish',
          'post__in' => $chapters,
          'orderby' => 'post__in',
          'posts_per_page' => get_option( 'posts_per_rss' ) + 4, // Buffer for hidden items
          'no_found_rows' => true // Improve performance
        );

        $chapter_query = new WP_Query( $query_args );

        // Loop over chapters
        foreach ( $chapter_query->posts as $post ) {
          // Setup
          setup_postdata( $post );

          // Terminate?
          if ( $terminator < 1 ) break;

          // Skip invisible chapters
          if ( fictioneer_get_field( 'fictioneer_chapter_hidden' ) ) {
            continue;
          }

          // Decrement terminator
          $terminator--;

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
              if ( $chapter_fandoms = get_the_terms( get_the_ID(), 'fcn_fandom' ) ) {
                foreach ( $chapter_fandoms as $fandom ) {
                  echo '<category>' . $fandom->name . '</category>';
                }
              }

              if ( $chapter_genres = get_the_terms( get_the_ID(), 'fcn_genre' ) ) {
                foreach ( $chapter_genres as $genre ) {
                  echo '<category>' . $genre->name . '</category>';
                }
              }

              if ( $chapter_characters = get_the_terms( get_the_ID(), 'fcn_character' ) ) {
                foreach ( $chapter_characters as $character ) {
                  echo '<category>' . $character->name . '</category>';
                }
              }

              if ( $chapter_tags = get_the_tags( get_the_ID() ) ) {
                foreach ( $chapter_tags as $tag ) {
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
      }
    ?>

  </channel>

</rss>
