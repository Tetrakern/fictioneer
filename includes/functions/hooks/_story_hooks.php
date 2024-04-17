<?php

// =============================================================================
// STATISTICS FOR ALL STORIES
// =============================================================================

/**
 * Outputs the statistics section for all stories
 *
 * Renders a statistics block with the number of published stories as well as
 * word count, comments, and the estimated reading time for all stories. The
 * reading time divisor can be changed under Fictioneer > General (default: 200).
 *
 * @since 5.0.0
 * @see stories.php
 *
 * @param int      $args['current_page']  Current page number of pagination or 1.
 * @param int      $args['post_id']       The post ID.
 * @param WP_Query $args['stories']       Paginated query of all published stories.
 * @param string   $args['queried_type']  The queried post type ('fcn_story').
 */

function fictioneer_stories_statistics( $args ) {
  // Look for cached value (purged after each update, should never be stale)
  $statistics = get_transient( 'fictioneer_stories_statistics' );

  // Compute statistics if necessary
  if ( ! $statistics ) {
    $words = fictioneer_get_stories_total_word_count();

    $statistics = array(
      'stories' => array(
        'label' => __( 'Stories', 'fictioneer' ),
        'content' => number_format_i18n( wp_count_posts( 'fcn_story' )->publish )
      ),
      'words' => array(
        'label' => _x( 'Words', 'Word count caption in statistics.', 'fictioneer' ),
        'content' => fictioneer_shorten_number( $words )
      ),
      'comments' => array(
        'label' => __( 'Comments', 'fictioneer' ),
        'content' => number_format_i18n(
          get_comments(
            array(
              'post_type' => 'fcn_chapter',
              'status' => 1,
              'count' => true,
              'update_comment_meta_cache' => false
            )
          )
        )
      ),
      'reading' => array(
        'label' => __( 'Reading', 'fictioneer' ),
        'content' => fictioneer_get_reading_time_nodes( $words )
      ),
    );

    // Apply filter
    $statistics = apply_filters( 'fictioneer_filter_stories_statistics', $statistics, $args );

    // Cache for next time
    set_transient( 'fictioneer_stories_statistics', $statistics, 900 );
  }

  // Start HTML ---> ?>
  <div class="stories__statistics statistics spacing-top">
    <?php foreach ( $statistics as $stat ) : ?>
      <div class="statistics__inline-stat">
        <strong><?php echo $stat['label']; ?></strong>
        <span><?php echo $stat['content']; ?></span>
      </div>
    <?php endforeach; ?>
  </div>
  <?php // <--- End HTML
}
add_action( 'fictioneer_stories_after_content', 'fictioneer_stories_statistics', 10 );

// =============================================================================
// LIST OF ALL STORIES
// =============================================================================

/**
 * Outputs the paginated card list for all stories
 *
 * @since 5.0.0
 * @see stories.php
 *
 * @param int        $args['current_page']  Current page number of pagination or 1.
 * @param int        $args['post_id']       The post ID of the page.
 * @param WP_Query   $args['stories']       Paginated query of all published stories.
 * @param string     $args['queried_type']  The queried post type ('fcn_story').
 * @param array      $args['query_args']    The query arguments used.
 * @param string     $args['order']         Current order. Default 'desc'.
 * @param string     $args['orderby']       Current orderby. Default 'modified'.
 * @param int|string $args['ago']           Current date query argument part. Default 0.
 */

function fictioneer_stories_list( $args ) {
  // Start HTML ---> ?>
  <section class="stories__list spacing-top container-inline-size">
    <ul id="list-of-stories" class="scroll-margin-top card-list">

      <?php if ( $args['stories']->have_posts() ) : ?>

        <?php
          // Card arguments
          $card_args = array(
            'cache' => fictioneer_caching_active( 'card_args' ) && ! fictioneer_private_caching_active(),
            'order' => $args['order'] ?? 'desc',
            'orderby' => $args['orderby'] ?? 'modified',
            'ago' => $args['ago'] ?? 0
          );

          // Filter card arguments
          $card_args = apply_filters( 'fictioneer_filter_stories_card_args', $card_args, $args );

          while ( $args['stories']->have_posts() ) {
            $args['stories']->the_post();

            if ( get_post_meta( get_the_ID(), 'fictioneer_story_hidden', true ) ) {
              get_template_part( 'partials/_card-hidden', null, $card_args );
            } else {
              get_template_part( 'partials/_card-story', null, $card_args );
            }
          }

          // Actions at end of results
          do_action( 'fictioneer_stories_end_of_results', $args );
        ?>

      <?php else : ?>

        <?php do_action( 'fictioneer_stories_no_results', $args ); ?>

        <li class="no-results">
          <span><?php _e( 'No stories found.', 'fictioneer' ); ?></span>
        </li>

      <?php endif; wp_reset_postdata(); ?>

      <?php
        $pag_args = array(
          'current' => max( 1, get_query_var( 'paged' ) ),
          'total' => $args['stories']->max_num_pages,
          'prev_text' => fcntr( 'previous' ),
          'next_text' => fcntr( 'next' ),
          'add_fragment' => '#list-of-stories'
        );
      ?>

      <?php if ( $args['stories']->max_num_pages > 1 ) : ?>
        <li class="pagination"><?php echo fictioneer_paginate_links( $pag_args ); ?></li>
      <?php endif; ?>

    </ul>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_stories_after_content', 'fictioneer_stories_list', 30 );

// =============================================================================
// STORY COPYRIGHT NOTICE
// =============================================================================

/**
 * Outputs the HTML for the story page copyright notice
 *
 * @since 5.0.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_copyright_notice( $args ) {
  // Setup
  $copyright_notice = get_post_meta( $args['story_id'], 'fictioneer_story_copyright_notice', true );

  // Abort conditions...
  if ( empty( $copyright_notice ) ) {
    return;
  }

  // Start HTML ---> ?>
  <section class="story__copyright-notice padding-left padding-right">
    <i class="fa-regular fa-copyright"></i> <?php echo $copyright_notice; ?>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_copyright_notice', 10 );

// =============================================================================
// STORY TAGS & WARNINGS
// =============================================================================

/**
 * Outputs the HTML for the story page tags and warnings
 *
 * @since 5.0.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_tags_and_warnings( $args ) {
  // Abort conditions...
  $tags_shown = $args['story_data']['tags'] &&
    ! get_option( 'fictioneer_hide_tags_on_pages' ) &&
    ! get_post_meta( $args['story_id'], 'fictioneer_story_no_tags', true );
  $warnings_shown = $args['story_data']['warnings'] && ! get_option( 'fictioneer_hide_content_warnings_on_pages' );

  if ( ! $tags_shown && ! $warnings_shown ) {
    return;
  }

  // Setup
  $tag_args = [];

  if ( $tags_shown ) {
    $tag_args['tags'] = $args['story_data']['tags'];
  }

  if ( $warnings_shown ) {
    $tag_args['warnings'] = $args['story_data']['warnings'];
  }

  // Start HTML ---> ?>
  <section class="story__tags-and-warnings tag-group padding-left padding-right"><?php
    echo fictioneer_get_taxonomy_pills( $tag_args, 'story_after_content', '_secondary' );
  ?></section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_tags_and_warnings', 20 );

// =============================================================================
// STORY ACTIONS ROW
// =============================================================================

/**
 * Outputs the HTML for the story page actions row
 *
 * @since 5.0.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_actions( $args ) {
  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  // Start HTML ---> ?>
  <section class="story__after-summary padding-left padding-right">
    <?php echo fictioneer_get_media_buttons(); ?>
    <div class="story__actions"><?php echo fictioneer_get_story_buttons( $args ); ?></div>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_actions', 30 );

// =============================================================================
// STORY TABS
// =============================================================================

/**
 * Outputs the HTML for the story tabs
 *
 * @since 5.9.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_tabs( $args ) {
  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  // Setup
  $story_id = $args['story_id'];
  $story = $args['story_data'];
  $custom_pages = get_post_meta( $story_id, 'fictioneer_story_custom_pages', true );
  $blog_posts = fictioneer_get_story_blog_posts( $story_id );
  $tab_pages = [];

  if ( is_array( $custom_pages ) ) {
    foreach ( $custom_pages as $page_id ) {
      if ( get_post_status( $page_id ) === 'publish' ) {
        $tab_pages[] = [$page_id, get_post_meta( $page_id, 'fictioneer_short_name', true ), get_the_content( null, false, $page_id )];
      }
    }
  }

  // Start HTML ---> ?>
  <section id="tabs-<?php echo $story_id; ?>" class="story__tabs tabs-wrapper padding-left padding-right" data-current="chapters" data-order="asc" data-view="list">

    <div class="tabs">
      <button class="tabs__item _current" data-target="chapters" tabindex="0"><?php
        if ( $story['status'] === 'Oneshot' ) {
          _e( 'Oneshot', 'fictioneer' );
        } else {
          printf(
            _x( '%1$s %2$s', 'Story chapter tab with count.', 'fictioneer' ),
            $story['chapter_count'],
            _n( 'Chapter', 'Chapters', $story['chapter_count'], 'fictioneer' )
          );
        }
      ?></button>

      <?php if ( $blog_posts->have_posts() ) : ?>
        <button class="tabs__item" data-target="blog" tabindex="0"><?php echo fcntr( 'story_blog' ); ?></button>
      <?php endif; ?>

      <?php
        if ( $custom_pages ) {
          $index = 1;

          foreach ( $tab_pages as $page ) {
            if ( empty( $page[1] ) ) {
              continue;
            }

            echo "<button class='tabs__item' data-target='tab-page-{$index}' tabindex='0'>{$page[1]}</button>";

            $index++;

            if ( $index > FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY ) {
              break; // Only show 4 custom tabs
            }
          }
        }
      ?>
    </div>

    <?php if ( $story['status'] !== 'Oneshot' || $story['chapter_count'] > 1 ) : ?>
      <div class="story__chapter-list-toggles">
        <button data-click-action="toggle-chapter-view" class="list-button story__toggle _view" data-view="list" tabindex="0" aria-label="<?php esc_attr_e( 'Toggle between list and grid view', 'fictioneer' ); ?>">
          <?php fictioneer_icon( 'grid-2x2', 'on' ); ?>
          <i class="fa-solid fa-list off"></i>
        </button>
        <button data-click-action="toggle-chapter-order" class="list-button story__toggle _order" data-order="asc" tabindex="0" aria-label="<?php esc_attr_e( 'Toggle between ascending and descending order', 'fictioneer' ); ?>">
          <i class="fa-solid fa-arrow-down-1-9 off"></i>
          <i class="fa-solid fa-arrow-down-9-1 on"></i>
        </button>
      </div>
    <?php endif; ?>

  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_tabs', 40 );

// =============================================================================
// STORY SCHEDULED CHAPTER
// =============================================================================

/**
 * Outputs the HTML for the scheduled chapter
 *
 * @since 5.9.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_scheduled_chapter( $args ) {
  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  // Setup
  $scheduled_chapters = get_posts(
    array(
      'post_type' => 'fcn_chapter',
      'post_status' => 'future',
      'posts_per_page' => 1,
      'orderby' => 'date',
      'order' => 'ASC',
      'update_post_term_cache' => false, // Improve performance
      'update_post_meta_cache' => false, // Improve performance
      'no_found_rows' => true, // Improve performance
      'meta_query' => array(
        array(
          'key' => 'fictioneer_chapter_story',
          'value' => $args['story_id'],
        ),
      )
    )
  );

  // Abort if no chapters are scheduled
  if ( empty( $scheduled_chapters ) ) {
    return;
  }

  // Start HTML ---> ?>
  <section class="story__tab-target story__scheduled-chapter _current" data-finder="chapters">
    <i class="fa-solid fa-calendar-days"></i>
    <span><?php
      printf(
        _x( 'Next Chapter: %1$s, %2$s', 'Scheduled chapter note with date and time.', 'fictioneer' ),
        get_the_date( '', $scheduled_chapters[0] ),
        get_the_time( '', $scheduled_chapters[0] )
      );
    ?></span>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_scheduled_chapter', 41 );

// =============================================================================
// STORY PAGES
// =============================================================================

/**
 * Outputs the HTML for the story custom pages
 *
 * @since 5.9.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_pages( $args ) {
  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  // Setup
  $story_id = $args['story_id'];
  $custom_pages = get_post_meta( $story_id, 'fictioneer_story_custom_pages', true );
  $tab_pages = [];

  if ( is_array( $custom_pages ) ) {
    foreach ( $custom_pages as $page_id ) {
      if ( get_post_status( $page_id ) === 'publish' ) {
        $tab_pages[] = [$page_id, get_post_meta( $page_id, 'fictioneer_short_name', true ), get_the_content( null, false, $page_id )];
      }
    }
  }

  // Output
  if ( $custom_pages ) {
    $index = 1;

    foreach ( $tab_pages as $page ) {
      if ( empty( $page[1] ) ) {
        continue;
      }

      // Start HTML ---> ?>
      <section class="story__tab-target padding-left padding-right content-section background-texture" data-finder="tab-page-<?php echo $index; ?>">
        <div class="story__custom-page"><?php echo apply_filters( 'the_content', $page[2] ); ?></div>
      </section>
      <?php // <--- End HTML

      $index++;

      if ( $index > FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY ) {
        break; // Only show 4 custom tabs
      }
    }
  }
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_pages', 42 );

// =============================================================================
// STORY CHAPTERS
// =============================================================================

/**
 * Outputs the HTML for the story chapters
 *
 * @since 5.9.0
 *
 * @param array $args {
 *   Array of arguments.
 *
 *   @type array $story_data  Pre-compiled array of story data.
 *   @type int   $story_id    ID of the story.
 * }
 */

function fictioneer_story_chapters( $args ) {
  global $post;

  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  $cache_plugin_active = fictioneer_caching_active( 'story_chapter_list' );

  // Check for cached chapters output
  if ( FICTIONEER_CHAPTER_LIST_TRANSIENTS && ! $cache_plugin_active && ! is_customize_preview() ) {
    $transient_cache = get_transient( 'fictioneer_story_chapter_list_html' . $args['story_id'] );

    if ( $transient_cache ) {
      echo $transient_cache;
      return;
    }
  }

  // Setup
  $story_id = $args['story_id'];
  $story = $args['story_data'];
  $hide_icons = get_post_meta( $story_id, 'fictioneer_story_hide_chapter_icons', true ) ||
  get_option( 'fictioneer_hide_chapter_icons' );
  $enable_groups = get_option( 'fictioneer_enable_chapter_groups' ) &&
    ! get_post_meta( $story_id, 'fictioneer_story_disable_groups', true );
  $disable_folding = get_post_meta( $story_id, 'fictioneer_story_disable_collapse', true );
  $collapse_groups = get_option( 'fictioneer_collapse_groups_by_default' );

  // Capture output
  ob_start();

  // Start HTML ---> ?>
  <section class="story__tab-target _current story__chapters" data-finder="chapters" data-order="asc" data-view="list">
    <?php
      $chapters = fictioneer_get_story_chapter_posts( $story_id );
      $chapter_groups = [];
      $group_classes = [];

      if ( $hide_icons ) {
        $group_classes[] = '_no-icons';
      }

      // Loop chapters and prepare groups
      if ( ! empty( $chapters ) ) {
        // Prepare chapter groups
        foreach ( $chapters as $post ) {
          // Setup
          setup_postdata( $post );
          $chapter_id = get_the_ID();

          // Skip not visible chapters
          if ( get_post_meta( $chapter_id, 'fictioneer_chapter_hidden', true ) ) {
            continue;
          }

          // Data
          $group = get_post_meta( $chapter_id, 'fictioneer_chapter_group', true );
          $group = empty( $group ) ? fcntr( 'unassigned_group' ) : $group;
          $group = $enable_groups ? $group : 'all_chapters';
          $group_key = sanitize_title( $group );

          if ( ! array_key_exists( $group_key, $chapter_groups ) ) {
            $chapter_groups[ $group_key ] = array(
              'group' => $group,
              'data' => []
            );
          }

          $chapter_groups[ $group_key ]['data'][] = array(
            'id' => $chapter_id,
            'status' => $post->post_status,
            'link' => get_permalink(),
            'timestamp' => get_the_time( 'c' ),
            'password' => ! empty( $post->post_password ),
            'list_date' => get_the_date( '', $post ),
            'grid_date' => get_the_time( get_option( 'fictioneer_subitem_date_format', "M j, 'y" ) ?: "M j, 'y" ),
            'icon' => fictioneer_get_icon_field( 'fictioneer_chapter_icon', $chapter_id ),
            'text_icon' => get_post_meta( $chapter_id, 'fictioneer_chapter_text_icon', true ),
            'prefix' => get_post_meta( $chapter_id, 'fictioneer_chapter_prefix', true ),
            'title' => fictioneer_get_safe_title( $chapter_id, 'story-chapter-list' ),
            'list_title' => get_post_meta( $chapter_id, 'fictioneer_chapter_list_title', true ),
            'words' => fictioneer_get_word_count( $chapter_id ),
            'warning' => get_post_meta( $chapter_id, 'fictioneer_chapter_warning', true )
          );
        }

        // Reset postdata
        wp_reset_postdata();
      }

      // Pre-collapse?
      if ( $collapse_groups && count( $chapter_groups ) > 1 ) {
        $group_classes[] = '_closed';
      }

      // Build HTML
      if ( ! empty( $chapter_groups ) ) {
        $group_index = 0;
        $has_groups = count( $chapter_groups ) > 1 && get_option( 'fictioneer_enable_chapter_groups' );

        foreach ( $chapter_groups as $group ) {
          $index = 0;
          $group_chapter_count = count( $group['data'] );
          $reverse_order = 99999;
          $chapter_folding = ! $disable_folding && ! get_option( 'fictioneer_disable_chapter_collapsing' );
          $chapter_folding = $chapter_folding && count( $group['data'] ) >= FICTIONEER_CHAPTER_FOLDING_THRESHOLD * 2 + 3;
          $aria_label = __( 'Toggle chapter group: %s', 'fictioneer' );
          $group_index++;

          // Start HTML ---> ?>
          <div class="chapter-group <?php echo implode( ' ', $group_classes ); ?>" data-folded="true">

            <?php if ( $has_groups ) : ?>
              <button
                class="chapter-group__name"
                aria-label="<?php echo esc_attr( sprintf( $aria_label, $group['group'] ) ); ?>"
                tabindex="0"
              >
                <i class="fa-solid fa-chevron-down chapter-group__heading-icon"></i>
                <span><?php echo $group['group']; ?></span>
              </button>
            <?php endif; ?>

            <ol class="chapter-group__list">
              <?php foreach ( $group['data'] as $chapter ) : ?>
                <?php
                  $index++;
                  $extra_classes = "_{$chapter['status']} ";

                  // Must account for extra toggle row and start at 1
                  $is_folded = $chapter_folding && $index > FICTIONEER_CHAPTER_FOLDING_THRESHOLD &&
                    $index < ( $group_chapter_count + 2 - FICTIONEER_CHAPTER_FOLDING_THRESHOLD );

                  if ( $is_folded ) {
                    $extra_classes .= ' _foldable';
                  }

                  if ( $chapter['password'] ) {
                    $extra_classes .= ' _password';
                  }
                ?>

                <?php if ( $chapter_folding && $index == FICTIONEER_CHAPTER_FOLDING_THRESHOLD + 1 ) : ?>
                  <li class="chapter-group__list-item _folding-toggle" style="order: <?php echo $reverse_order - $index; ?>">
                    <button class="chapter-group__folding-toggle" tabindex="0">
                      <?php
                        printf(
                          __( 'Show %s more', 'fictioneer' ),
                          $group_chapter_count - FICTIONEER_CHAPTER_FOLDING_THRESHOLD * 2
                        );
                      ?>
                    </button>
                  </li>
                  <?php $index++; ?>
                <?php endif; ?>

                <li
                  class="chapter-group__list-item <?php echo $extra_classes; ?>"
                  style="order: <?php echo $reverse_order - $index; ?>"
                >

                  <?php if ( ! empty( $chapter['text_icon'] ) && ! $hide_icons ) : ?>
                    <span class="chapter-group__list-item-icon _text text-icon"><?php echo $chapter['text_icon']; ?></span>
                  <?php elseif ( ! $hide_icons ) : ?>
                    <i class="<?php echo empty( $chapter['icon'] ) ? 'fa-solid fa-book' : $chapter['icon']; ?> chapter-group__list-item-icon"></i>
                  <?php endif; ?>

                  <a
                    href="<?php echo $chapter['link']; ?>"
                    class="chapter-group__list-item-link truncate _1-1 <?php echo $chapter['password'] ? '_password' : ''; ?>"
                  ><?php

                    // Non-published chapter prefixes
                    if ( in_array( $chapter['status'], ['future', 'trash', 'private'] ) ) {
                      echo '<span class="chapter-group__list-item-status">' . fcntr( "{$chapter['status']}_prefix" ) . '</span> ';
                    }

                    if ( ! empty( $chapter['prefix'] ) ) {
                      // Mind space between prefix and title
                      echo apply_filters( 'fictioneer_filter_list_chapter_prefix', $chapter['prefix'] ) . ' ';
                    }

                    if ( ! empty( $chapter['list_title'] ) && $chapter['title'] !== $chapter['list_title'] ) {
                      printf(
                        ' <span class="chapter-group__list-item-title list-view">%s</span><span class="grid-view">%s</span>',
                        $chapter['title'],
                        wp_strip_all_tags( $chapter['list_title'] )
                      );
                    } else {
                      echo $chapter['title'];
                    }

                  ?></a>

                  <?php if ( $chapter['password'] ) : ?>
                    <i class="fa-solid fa-lock icon-password grid-view"></i>
                  <?php endif; ?>

                  <?php echo fictioneer_get_list_chapter_meta_row( $chapter, array( 'grid' => true ) ); ?>

                  <?php if ( get_option( 'fictioneer_enable_checkmarks' ) ) : ?>
                    <button
                      class="checkmark chapter-group__list-item-checkmark"
                      data-type="chapter"
                      data-story-id="<?php echo $story_id; ?>"
                      data-id="<?php echo $chapter['id']; ?>"
                      role="checkbox"
                      aria-checked="false"
                      aria-label="<?php printf( esc_attr__( 'Chapter checkmark for %s.', 'fictioneer' ), $chapter['title'] ); ?>"
                    ><i class="fa-solid fa-check"></i></button>
                  <?php endif; ?>

                </li>
              <?php endforeach; ?>
            </ol>

          </div>
          <?php // <--- End HTML
        }
      } elseif ( $story['status'] !== 'Oneshot' ) {
        // Start HTML ---> ?>
        <div class="chapter-group <?php echo implode( ' ', $group_classes ); ?>">
          <ol class="chapter-group__list">
            <li class="chapter-group__list-item _empty">
              <span><?php _e( 'No chapters published yet.', 'fictioneer' ); ?></span>
            </li>
          </ol>
        </div>
        <?php // <--- End HTML
      }
    ?>
  </section>
  <?php // <--- End HTML

  // Store output
  $chapters_html = ob_get_clean();

  // Compress output
  $chapters_html = fictioneer_minify_html( $chapters_html );

  // Flush buffered output
  echo $chapters_html;

  // Cache for next time (24 hours)
  if ( FICTIONEER_CHAPTER_LIST_TRANSIENTS && ! $cache_plugin_active && ! is_customize_preview() ) {
    set_transient( 'fictioneer_story_chapter_list_html' . $story_id, $chapters_html, 86400 );
  }
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_chapters', 43 );

// =============================================================================
// STORY BLOG
// =============================================================================

/**
 * Outputs the HTML for the story blog
 *
 * @since 5.9.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_blog( $args ) {
  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  // Setup
  $story_id = $args['story_id'];
  $blog_posts = fictioneer_get_story_blog_posts( $story_id );

  // Start HTML ---> ?>
  <section class="story__blog story__tab-target" data-finder="blog">
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
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_blog', 44 );

// =============================================================================
// STORY COMMENTS
// =============================================================================

/**
 * Outputs the HTML for the story page comments
 *
 * @since 5.0.0
 * @since 5.14.0 - Merged partial into function.
 *
 * @param array       $args['story_data']  Collection of story data.
 * @param int         $args['story_id']    The story post ID.
 * @param string|null $args['classes']     Optional. Additional CSS classes, separated by whitespace.
 * @param bool|null   $args['header']      Optional. Whether to show the heading with count. Default true.
 * @param string|null $attr['style']       Optional. Inline style applied to wrapper element.
 * @param bool|null   $args['shortcode']   Optional. Whether the render context is a shortcode. Default false.
 */

function fictioneer_story_comments( $args ) {
  // Setup
  $story = $args['story_data'];
  $shortcode = filter_var( $args['shortcode'] ?? 0, FILTER_VALIDATE_BOOLEAN );
  $header = filter_var( $args['header'] ?? 1, FILTER_VALIDATE_BOOLEAN );
  $classes = $args['classes'] ?? '';
  $style = $args['style'] ?? '';

  // Abort conditions...
  if ( post_password_required() || $story['comment_count'] < 1 ) {
    return;
  }

  // Add extra classes
  if ( ! $shortcode ) {
    $classes .= ' padding-left padding-right padding-bottom';
  }

  // Start HTML ---> ?>
  <section class="comment-section fictioneer-comments <?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $style ); ?>">
    <?php if ( $header ) : ?>
      <h2 class="fictioneer-comments__title"><?php
        printf(
          _n(
            '<span>%s</span> <span>Comment</span>',
            '<span>%s</span> <span>Comments</span>',
            $story['comment_count'],
            'fictioneer'
          ),
          $story['comment_count']
        );
      ?></h2>
    <?php endif; ?>
    <?php do_action( 'fictioneer_story_before_comments_list', $args ); ?>
    <div class="fictioneer-comments__list">
      <ul>
        <li class="load-more-list-item">
          <button class="load-more-comments-button" data-story-id="<?php echo $args['story_id']; ?>"><?php
            $load_n = $story['comment_count'] < get_option( 'comments_per_page' ) ?
              $story['comment_count'] : get_option( 'comments_per_page' );

            printf(
              _n(
                'Load latest comment (may contain spoilers)',
                'Load latest %s comments (may contain spoilers)',
                $load_n,
                'fictioneer'
              ),
              $load_n
            );
          ?></button>
        </li>
        <div class="comments-loading-placeholder hidden"><i class="fa-solid fa-spinner spinner"></i></div>
      </ul>
    </div>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_article', 'fictioneer_story_comments', 10 );

?>
