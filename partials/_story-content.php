<?php
/**
 * Partial: Story Content
 *
 * Rendered in the single-fcn_story.php template below the summary section
 * with description, tags, content warnings, and action row.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7
 * @see single-fcn_story.php
 *
 * @internal $args['story_data'] Story data from fictioneer_get_story_data().
 * @internal $args['story_id']   Current story and post ID.
 */
?>

<?php

// Setup
$story_id = $args['story_id'];
$story = $args['story_data'];
$custom_pages = fictioneer_get_field( 'fictioneer_story_custom_pages' );
$tab_pages = [];
$above_collapse = 5;

if ( $custom_pages ) {
  foreach ( $custom_pages as $page_id ) {
    $tab_pages[] = [$page_id, fictioneer_get_field( 'fictioneer_short_name', $page_id ), get_the_content( null, false, $page_id )];
  }
}

// Check for cached chapters output
$chapters_html = FICTIONEER_CHAPTER_LIST_TRANSIENTS ? get_transient( 'fictioneer_story_chapter_list_' . $story_id ) : null;

// Flags
$hide_icons = fictioneer_get_field( 'fictioneer_story_hide_chapter_icons' ) || get_option( 'fictioneer_hide_chapter_icons' );
$enable_groups = get_option( 'fictioneer_enable_chapter_groups' ) && ! fictioneer_get_field( 'fictioneer_story_disable_groups' );
$disable_collapse = fictioneer_get_field( 'fictioneer_story_disable_collapse' );

// Query blog posts (if any)
$category = implode( ', ', wp_get_post_categories( $args['story_id'] ) );

$blog_posts = new WP_Query(
  array (
    'nopaging' => false,
    'posts_per_page' => 10,
    'cat' => $category == 0 ? '99999999' : $category,
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false
  )
);

?>

<?php
  // ======================================================================================
  // CONTROL INPUTS
  // ======================================================================================
?>

<input type="radio" id="toggle-chapter" name="story_tab" hidden checked>

<?php
  if ( $blog_posts->have_posts() ) {
    echo '<input type="radio" id="toggle-blog" name="story_tab" hidden>';
  }

  if ( $custom_pages ) {
    $index = 0;

    foreach ( $tab_pages as $page ) {
      if ( empty( $page[1] ) ) continue;
      echo '<input type="radio" id="toggle-custom-' . $index . '" name="story_tab" hidden>';
      $index++;
    }
  }
?>

<input id="toggle-order" type="checkbox" hidden>
<input id="toggle-view" type="checkbox" hidden>

<?php
  // ======================================================================================
  // TAB SECTION
  // ======================================================================================
?>

<section id="tabs" class="story__tabs tabs-wrapper padding-left padding-right">

  <div class="tabs">
    <label id="chapter-tab" for="toggle-chapter" class="tabs__item" tabindex="0">
      <?php
        if ( $story['status'] === 'Oneshot' ) {
          _e( 'Oneshot', 'fictioneer' );
        } else {
          echo sprintf(
            __( '%1$s %2$s', 'fictioneer' ),
            $story['chapter_count'],
            _n( 'Chapter', 'Chapters', $story['chapter_count'], 'fictioneer' )
          );
        }
      ?>
    </label>

    <?php if ( $blog_posts->have_posts() ) : ?>
      <label id="blog-tab" for="toggle-blog" class="tabs__item" tabindex="0"><?php echo fcntr( 'story_blog' ); ?></label>
    <?php endif; ?>

    <?php
      if ( $custom_pages ) {
        $index = 0;

        foreach ( $tab_pages as $page ) {
          if ( empty( $page[1] ) ) continue;
          echo '<label tabindex="0" id="custom-tab-' . $index . '" for="toggle-custom-' . $index . '" class="tabs__item">' . $page[1] . '</label>';
          $index++;
          if ( $index > 3) break; // Only show 4 custom tabs
        }
      }
    ?>
  </div>

  <div class="story__chapter-list-toggles">
    <label class="list-button story__toggle _view" for="toggle-view" tabindex="0">
      <?php fictioneer_icon( 'grid-2x2', 'on' ); ?>
      <i class="fa-solid fa-list off"></i>
    </label>
    <label class="list-button story__toggle _order" for="toggle-order" tabindex="0">
      <i class="fa-solid fa-arrow-down-1-9 off"></i>
      <i class="fa-solid fa-arrow-down-9-1 on"></i>
    </label>
  </div>

</section>

<?php
  // ======================================================================================
  // CUSTOM PAGES SECTION
  // ======================================================================================
?>

<?php
  if ( $custom_pages ) {
    $index = 0;

    foreach ( $tab_pages as $page ) {
      if ( empty( $page[1] ) ) continue;

      ?>
      <section id="tab-page-<?php echo $index; ?>" class="story__custom-page padding-left padding-right padding-top padding-bottom content-section background-texture">
        <div class="story__custom-page-wrapper"><?php echo apply_filters( 'the_content', $page[2] ); ?></div>
      </section>
      <?php

      $index++;
      if ( $index > 3) break; // Only show 4 custom tabs
    }
  }
?>

<?php
  // ======================================================================================
  // CHAPTERS SECTION
  // ======================================================================================
?>

<?php if ( ! empty( $chapters_html ) ) : echo $chapters_html; ?>

<?php else : ob_start(); ?>

  <section id="chapters" class="story__chapters">
    <?php
      $chapters = fictioneer_get_field( 'fictioneer_story_chapters', $story_id );
      $chapter_groups = [];

      // Loop and prepare groups
      if ( ! empty( $chapters ) ) {
        // Query chapters
        $chapter_query = new WP_Query(
          array(
            'post_type' => 'fcn_chapter',
            'post_status' => 'publish',
            'post__in' => $chapters,
            'ignore_sticky_posts' => true,
            'orderby' => 'post__in', // Preserve order from meta box
            'posts_per_page' => -1, // Get all chapters (this can be hundreds)
            'no_found_rows' => false, // Improve performance
            'update_post_term_cache' => false // Improve performance
          )
        );

        if ( $chapter_query->have_posts() ) {
          while( $chapter_query->have_posts() ) {
            // Setup
            $chapter_query->the_post();
            $chapter_id = get_the_ID();

            // Skip not visible chapters
            if ( fictioneer_get_field( 'fictioneer_chapter_hidden' ) ) continue;

            // Data
            $group = fictioneer_get_field( 'fictioneer_chapter_group' );
            $group = empty( $group ) ? fcntr( 'unassigned_group' ) : $group;
            $group = $enable_groups ? $group : 'all_chapters';
            $group_key = sanitize_title( $group );
            $warning_color = fictioneer_get_field( 'fictioneer_chapter_warning_color' );
            $warning_color = empty( $warning_color ) ? '' : 'color: ' . $warning_color . ';';

            if ( ! array_key_exists( $group_key, $chapter_groups ) ) {
              $chapter_groups[sanitize_title( $group )] = array(
                'group' => $group,
                'data' => []
              );
            }

            $chapter_groups[sanitize_title( $group )]['data'][] = array(
              'id' => get_the_ID(),
              'link' => get_permalink(),
              'timestamp' => get_the_time( 'c' ),
              'password' => ! empty( $post->post_password ),
              'list_date' => get_the_date( '', $post ),
              'grid_date' => get_the_time( get_option( 'fictioneer_subitem_date_format', 'M j, y' ) ),
              'icon' => fictioneer_get_icon_field( 'fictioneer_chapter_icon' ),
              'text_icon' => fictioneer_get_field( 'fictioneer_chapter_text_icon' ),
              'prefix' => fictioneer_get_field( 'fictioneer_chapter_prefix' ),
              'title' => fictioneer_get_safe_title( $chapter_id ),
              'list_title' => fictioneer_get_field( 'fictioneer_chapter_list_title' ),
              'words' => get_post_meta( $chapter_id, '_word_count', true ),
              'warning' => fictioneer_get_field( 'fictioneer_chapter_warning' ),
              'warning_color' => $warning_color
            );
          }
        }

        // Reset postdata
        wp_reset_postdata();
      }

      // Build HTML
      if ( ! empty( $chapter_groups ) ) {
        $group_index = 0;
        $has_groups = count( $chapter_groups ) > 1 && get_option( 'fictioneer_enable_chapter_groups' );

        foreach ( $chapter_groups as $group ) {
          $index = 0;
          $reverse_order = 999999;
          $is_collapsed = ! $disable_collapse && ! get_option( 'fictioneer_disable_chapter_collapsing' );
          $is_collapsed = $is_collapsed && count( $group['data'] ) >= $above_collapse * 2 + 3;
          $group_index++;

          // Start HTML ---> ?>
          <div class="chapter-group<?php echo $hide_icons ? ' _no-icons' : ''; ?>">

            <?php if ( $has_groups ) : ?>
              <input id="group-toggle-<?php echo $group_index; ?>" class="chapter-group__toggle" type="checkbox" hidden>
              <label class="chapter-group__label" for="group-toggle-<?php echo $group_index; ?>" tabindex="0" role="button" aria-label="<?php esc_attr_e( 'Toggle chapter group collapse', 'fictioneer' ); ?>">
                <i class="fa-solid fa-chevron-down chapter-group__heading-icon"></i>
                <span><?php echo $group['group']; ?></span>
              </label>
            <?php endif; ?>

            <ol class="chapter-group__list">
              <?php foreach ( $group['data'] as $chapter ) : ?>
                <?php $index++; ?>

                <?php if ( $is_collapsed && $index == $above_collapse + 1 ) : ?>
                  <input id="chapters-toggle-<?php echo $group_index; ?>" type="checkbox" autocomplete="off" hidden>
                  <li class="chapter-group__list-item _collapse" style="order: <?php echo $reverse_order - $index; ?>">
                    <label for="chapters-toggle-<?php echo $group_index; ?>" tabindex="0">
                      <span><?php printf( __( 'Show %s more', 'fictioneer' ), count( $group['data'] ) - $above_collapse * 2 ); ?></span>
                    </label>
                  </li>
                  <?php $index++; ?>
                <?php endif; ?>

                <li class="chapter-group__list-item" style="order: <?php echo $reverse_order - $index; ?>">

                  <?php if ( ! empty( $chapter['text_icon'] ) && ! $hide_icons ) : ?>
                    <span class="chapter-group__list-item-icon _text text-icon"><?php echo $chapter['text_icon']; ?></span>
                  <?php elseif ( ! $hide_icons ) : ?>
                    <i class="<?php echo empty( $chapter['icon'] ) ? 'fa-solid fa-book' : $chapter['icon']; ?> chapter-group__list-item-icon"></i>
                  <?php endif; ?>

                  <a href="<?php echo $chapter['link']; ?>" class="chapter-group__list-item-link truncate _1-1 <?php echo $chapter['password'] ? '_password' : ''; ?>">
                    <?php if ( ! empty( $chapter['prefix'] ) ): ?>
                      <span class="chapter-group__list-item-prefix list-view"><?php echo $chapter['prefix']; ?></span>
                    <?php endif; ?>
                    <span class="chapter-group__list-item-title list-view"><?php echo $chapter['title']; ?></span>
                    <span class="grid-view"><?php
                      if ( ! empty( $chapter['list_title'] ) ) {
                        echo wp_strip_all_tags( $chapter['list_title'] );
                      } else {
                        if ( $chapter['prefix'] ) echo '<span>' . $chapter['prefix'] . '</span> ';
                        echo '<span>' . $chapter['title']. '</span>';
                      }
                    ?></span>
                  </a>

                  <?php if ( $chapter['password'] ) : ?>
                    <i class="fa-solid fa-lock icon-password grid-view"></i>
                  <?php endif; ?>

                  <?php echo fictioneer_get_list_chapter_meta_row( $chapter, ['grid' => true] ); ?>

                  <?php if ( get_option( 'fictioneer_enable_checkmarks' ) ) : ?>
                    <div class="chapter-group__list-item-right">
                      <button
                        class="checkmark chapter-group__list-item-checkmark"
                        data-type="chapter"
                        data-story-id="<?php echo $story_id; ?>"
                        data-id="<?php echo $chapter['id']; ?>"
                        role="checkbox"
                        aria-checked="false"
                        aria-label="<?php printf( esc_attr__( 'Chapter checkmark for %s.', 'fictioneer' ), $chapter['title'] ); ?>"
                      ><i class="fa-solid fa-check"></i></button>
                    </div>
                  <?php endif; ?>

                </li>
              <?php endforeach; ?>
            </ol>

          </div>
          <?php // <--- End HTML
        }
      } else {
        // Start HTML ---> ?>
        <div class="chapter-group<?php echo $hide_icons ? ' _no-icons' : ''; ?>">
          <ol class="chapter-group__list">
            <li class="chapter-group__list-item _empty">
              <span><?php _e( 'No chapters published yet.', 'fictioneer' ) ?></span>
            </li>
          </ol>
        </div>
        <?php // <--- End HTML
      }
    ?>
  </section>

<?php
  // Store output
  $chapters_html = ob_get_contents();

  // Flush buffered output
  ob_end_flush();

  // Cache for next time (24 hours)
  if ( FICTIONEER_CHAPTER_LIST_TRANSIENTS ) {
    set_transient( 'fictioneer_story_chapter_list_' . $story_id, $chapters_html, 86400 );
  }

  endif;
?>

<?php
  // ======================================================================================
  // BLOG SECTION
  // ======================================================================================
?>

<section class="story__blog">
  <ol class="story__blog-list">
    <?php
      if ( $blog_posts->have_posts() ) {
        while ( $blog_posts->have_posts() ) {
          $blog_posts->the_post();
          // Start HTML ---> ?>
          <li class="story__blog-list-item">
            <div class="story__blog-list-item-wrapper">
              <span class="story__blog-title">
                <a class="story__blog-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>:
              </span>
              <span class="story__blog-content"><?php echo fictioneer_get_limited_excerpt( 160 ); ?></span>
            </div>
          </li>
          <?php // <--- End HTML
        }
      }
      wp_reset_postdata();
    ?>
  </ol>
</section>
