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
 * @since 5.0
 * @see stories.php
 *
 * @param int      $args['current_page']  Current page number of pagination or 1.
 * @param int      $args['post_id']       The post ID.
 * @param WP_Query $args['stories']       Paginated query of all published stories.
 * @param string   $args['queried_type']  The queried post type ('fcn_story').
 */

function fictioneer_stories_statistics( $args ) {
  // Setup
  $words = fictioneer_get_stories_total_word_count();
  $statistics = array(
    'stories' => array(
      'label' => __( 'Stories', 'fictioneer' ),
      'content' => number_format_i18n( wp_count_posts( 'fcn_story' )->publish )
    ),
    'words' => array(
      'label' => __( 'Words', 'fictioneer' ),
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
 * @since 5.0
 * @see stories.php
 *
 * @param int        $args['current_page']  Current page number of pagination or 1.
 * @param int        $args['post_id']       The post ID.
 * @param WP_Query   $args['stories']       Paginated query of all published stories.
 * @param string     $args['queried_type']  The queried post type ('fcn_story').
 * @param array      $args['query_args']    The query arguments used.
 * @param string     $args['order']         Current order. Default 'desc'.
 * @param string     $args['orderby']       Current orderby. Default 'modified'.
 * @param int|string $args['ago']           Current date query argument part. Default 0.
 */

function fictioneer_stories_list( $args ) {
  // Start HTML ---> ?>
  <section class="stories__list spacing-top">
    <ul id="list-of-stories" class="scroll-margin-top card-list">

      <?php if ( $args['stories']->have_posts() ) : ?>

        <?php
          // Card arguments
          $card_args = array(
            'cache' => fictioneer_caching_active() && ! fictioneer_private_caching_active(),
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
 * @since Fictioneer 5.0
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
 * @since Fictioneer 5.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_tags_and_warnings( $args ) {
  // Abort conditions...
  $tags_shown = $args['story_data']['tags'] &&
    ! get_option( 'fictioneer_hide_tags_on_pages' ) &&
    ! get_post_meta( get_the_ID(), 'fictioneer_story_no_tags', true );
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
    echo fictioneer_get_taxonomy_pills( $tag_args, '_secondary' );
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
 * @since Fictioneer 5.0
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
    <?php get_template_part( 'partials/_share-buttons' ); ?>
    <div class="story__actions"><?php echo fictioneer_get_story_buttons( $args ); ?></div>
  </section>
  <?php // <--- End HTML
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_actions', 30 );

// =============================================================================
// STORY CHAPTERS
// =============================================================================

/**
 * Outputs the HTML for the story page actions row
 *
 * @since Fictioneer 5.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_content( $args ) {
  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  // Render partial
  get_template_part( 'partials/_story-content', null, $args );
}
add_action( 'fictioneer_story_after_content', 'fictioneer_story_content', 40 );

// =============================================================================
// STORY COMMENTS
// =============================================================================

/**
 * Outputs the HTML for the story page comments
 *
 * @since Fictioneer 5.0
 *
 * @param array $args['story_data']  Collection of story data.
 * @param int   $args['story_id']    The story post ID.
 */

function fictioneer_story_comment( $args ) {
  // Abort conditions...
  if ( post_password_required() ) {
    return;
  }

  // Render partial
  get_template_part( 'partials/_story-comments', null, $args );
}
add_action( 'fictioneer_story_after_article', 'fictioneer_story_comment', 10 );

?>
