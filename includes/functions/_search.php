<?php

// =============================================================================
// OUTPUT KEYWORD SEARCH TAXONOMIES INPUT
// =============================================================================

if ( ! function_exists( 'fcn_keyword_search_taxonomies_input' ) ) {
  /**
   * Output keyword taxonomies input field for search form
   *
   * @since 5.0
   *
   * @param array  $taxonomies  Array of WP_Term objects.
   * @param string $query_var   Name of the submitted collection field.
   * @param string $and_var     Name of the submitted operator field.
   * @param string $singular    Singular display name of taxonomy.
   * @param string $plural      Plural display name of taxonomy.
   * @param array  $args        Optional arguments.
   */

  function fcn_keyword_search_taxonomies_input( $taxonomies, $query_var, $and_var, $singular, $plural, $args = [] ) {
    // Setup
    $and = wp_strip_all_tags( $_GET[ $and_var ] ?? 0, true );
    $query_list = wp_strip_all_tags( $_GET[ $query_var ] ?? '', true );
    $examples = array_rand( $taxonomies, min( 5, count( $taxonomies ) ) );
    $examples = is_array( $examples ) ? $examples : [$examples];

    // Start HTML ---> ?>
    <div class="keyword-input <?php if ( empty( $query_list ) ) echo '_empty'; ?>">
      <?php if ( ! ( $args['no_operator'] ?? 0 ) ) : ?>
        <label
          class="keyword-input__operator"
          role="checkbox"
          aria-checked="<?php if ( $and == 1 ) echo 'true'; ?>"
          title="<?php esc_attr_e( 'Operator. Either must match (OR) or all must match (AND).', 'fictioneer' ); ?>"
          tabindex="0"
        >
          <input type="checkbox" name="<?php echo $and_var; ?>" value="1" hidden <?php if ( $and == 1 ) echo 'checked'; ?> autocomplete="off">
          <span class="on"><?php _ex( 'AND', 'Advanced search operator.', 'fictioneer' ); ?></span>
          <span class="off"><?php _ex( 'OR', 'Advanced search operator.', 'fictioneer' ); ?></span>
        </label>
      <?php endif; ?>
      <input type="hidden" class="keyword-input__collection" name="<?php echo $query_var; ?>" value="<?php echo esc_attr( $query_list ); ?>" autocomplete="off">
      <label
        class="keyword-input__track <?php if ( ! ( $args['no_operator'] ?? 0 ) ) echo '_operator'; ?>"
        data-hint="<?php _e( 'Start typing for suggestions…', 'fictioneer' ); ?>"
      >
        <?php
          if ( ! empty( $query_list ) ) {
            $nodes = explode( ',', $query_list );
            $x_mark = fictioneer_get_icon( 'fa-xmark' );

            foreach ( $taxonomies as $term ) {
              if ( ! in_array( $term->term_id, $nodes ) ) {
                continue;
              }

              $name = $term->name;
              $value = rawurlencode( $term->term_id );

              echo "<span class='node' data-value='$value'><span>$name</span><span class='node-delete'>$x_mark</span></span>";
            }
          }
        ?>
        <div class="keyword-input__input-wrapper">
          <div class="keyword-input__tab-suggestion"></div>
          <input type="text" class="keyword-input__input" style="width: 16px;" autocomplete="off">
        </div>
      </label>
      <div class="keyword-input__hints"><?php
        foreach ( $examples as $key ) {
          $name = $taxonomies[ $key ]->name;
          $value = rawurlencode( $taxonomies[ $key ]->term_id );
          $description = esc_attr( $taxonomies[ $key ]->description );

          echo "<button type='button' value='$value' class='keyword-input__suggestion keyword-button _$singular' title='$description'>$name</button>";
        }
      ?></div>
      <div class="keyword-input__no-suggestions" hidden><?php
        printf(
          _x( 'No matching %s found.', 'Advanced search hint if nothing found.', 'fictioneer' ),
          $plural
        );
      ?></div>
      <div class="keyword-input__suggestion-list"><?php
        foreach ( $taxonomies as $term ) {
          $name = $term->name;
          $value = rawurlencode( $term->term_id );
          $description = esc_attr( $term->description );

          echo "<button type='button' value='$value' class='keyword-input__suggestion keyword-button _$singular' title='$description' hidden>$name</button>";
        }
      ?></div>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// OUTPUT KEYWORD SEARCH AUTHORS INPUT
// =============================================================================

if ( ! function_exists( 'fcn_keyword_search_authors_input' ) ) {
  /**
   * Output keyword authors input field for search form
   *
   * @since 5.0
   *
   * @param array  $authors    Array of WP_User objects.
   * @param string $query_var  Name of the submitted collection field.
   * @param string $singular   Singular display name of taxonomy.
   * @param string $plural     Plural display name of taxonomy.
   * @param array  $args       Optional arguments.
   */

  function fcn_keyword_search_authors_input( $authors, $query_var, $singular, $plural, $args = [] ) {
    // Setup
    $query_list = wp_strip_all_tags( $_GET[ $query_var ] ?? '', true );
    $examples = array_rand( $authors, min( 5, count( $authors ) ) );
    $examples = is_array( $examples ) ? $examples : [$examples];

    // Start HTML ---> ?>
    <div class="keyword-input  _empty">
      <input type="hidden" class="keyword-input__collection" name="<?php echo $query_var; ?>" value="<?php echo esc_attr( $query_list ); ?>" autocomplete="off">
      <label class="keyword-input__track" data-hint="<?php _e( 'Start typing for suggestions…', 'fictioneer' ); ?>">
        <?php
          if ( ! empty( $query_list ) ) {
            $nodes = explode( ',', $query_list );
            $x_mark = fictioneer_get_icon( 'fa-xmark' );

            foreach ( $authors as $author ) {
              if ( ! in_array( $author->ID, $nodes ) ) {
                continue;
              }

              $name = $author->display_name;
              $value = rawurlencode( $author->ID );

              echo "<span class='node' data-value='$value'><span>$name</span><span class='node-delete'>$x_mark</span></span>";
            }
          }
        ?>
        <div class="keyword-input__input-wrapper">
          <div class="keyword-input__tab-suggestion"></div>
          <input type="text" class="keyword-input__input" style="width: 16px;" autocomplete="off">
        </div>
      </label>
      <div class="keyword-input__hints"><?php
        foreach ( $examples as $key ) {
          $name = $authors[ $key ]->display_name;
          $value = rawurlencode( $authors[ $key ]->ID );

          echo "<button type='button' value='$value' class='keyword-input__suggestion keyword-button _$singular'>$name</button>";
        }
      ?></div>
      <div class="keyword-input__no-suggestions" hidden><?php
        printf(
          _x( 'No matching %s found.', 'Advanced search hint if nothing found.', 'fictioneer' ),
          $plural
        );
      ?></div>
      <div class="keyword-input__suggestion-list"><?php
        foreach ( $authors as $author ) {
          $name = $author->display_name;
          $value = rawurlencode( $author->ID );

          echo "<button type='button' value='$value' class='keyword-input__suggestion keyword-button _$singular' hidden>$name</button>";
        }
      ?></div>
    </div>
    <?php // <--- End HTML
  }
}

// =============================================================================
// FILTER SEARCH QUERY
// =============================================================================

/**
 * Remove unlisted chapters and stories from search results
 *
 * @since 5.2.4
 *
 * @param WP_Query $query  The query.
 */

function fictioneer_remove_unlisted_from_search( $query ) {
  // Only for search queries on the frontend...
  if ( ! is_admin() && $query->is_main_query() && $query->is_search ) {
    $query->set(
      'meta_query',
      array(
        'relation' => 'AND',
        array(
          'relation' => 'OR',
          array(
            'key' => 'fictioneer_chapter_hidden',
            'compare' => 'NOT EXISTS'
          ),
          array(
            'key' => 'fictioneer_chapter_hidden',
            'value' => '0'
          )
        ),
        array(
          'relation' => 'OR',
          array(
            'key' => 'fictioneer_story_hidden',
            'compare' => 'NOT EXISTS'
          ),
          array(
            'key' => 'fictioneer_story_hidden',
            'value' => '0'
          )
        )
      )
    );
  }
}
add_action( 'pre_get_posts' ,'fictioneer_remove_unlisted_from_search', 10 );

/**
 * Extend search query with custom input
 *
 * @since 5.0
 *
 * @param WP_Query $query  The query.
 */

function fictioneer_extend_search_query( $query ) {
  // Only for search queries on the frontend...
  if ( is_admin() || ! $query->is_main_query() || ! $query->is_search ) {
    return;
  }

  // Empty search if no params provided
  if ( empty( array_filter( $_GET ) ) ) {
    $query->set( 'post__in', [0] );
    return;
  }

  // Fix broken search if no term was entered
  if ( empty( $query->get( 's' ) ) ) {
    $query->set( 's', ' ' ); // Most posts should have at least one whitespace
  }

  // Setup
  $tax_array = [];
  $authors = [];

  $is_any_post = isset( $_GET['post_type'] ) && ( $_GET['post_type'] === 'any' ) ? 1 : 0;
  $authors_in = empty( $_GET['authors'] ) ? [] : array_map( 'absint', explode( ',', $_GET['authors'] ) );
  $authors_out = empty( $_GET['ex_authors'] ) ? [] : array_map( 'absint', explode( ',', $_GET['ex_authors'] ) );

  $genres = empty( $_GET['genres'] ) ? [] : array_map( 'absint', explode( ',', $_GET['genres'] ) );
  $fandoms = empty( $_GET['fandoms'] ) ? [] : array_map( 'absint', explode( ',', $_GET['fandoms'] ) );
  $characters = empty( $_GET['characters'] ) ? [] : array_map( 'absint', explode( ',', $_GET['characters'] ) );
  $warnings = empty( $_GET['warnings'] ) ? [] : array_map( 'absint', explode( ',', $_GET['warnings'] ) );
  $tags = empty( $_GET['tags'] ) ? [] : array_map( 'absint', explode( ',', $_GET['tags'] ) );

  $ex_genres = empty( $_GET['ex_genres'] ) ? [] : array_map( 'absint', explode( ',', $_GET['ex_genres'] ) );
  $ex_fandoms = empty( $_GET['ex_fandoms'] ) ? [] : array_map( 'absint', explode( ',', $_GET['ex_fandoms'] ) );
  $ex_characters = empty( $_GET['ex_characters'] ) ? [] : array_map( 'absint', explode( ',', $_GET['ex_characters'] ) );
  $ex_warnings = empty( $_GET['ex_warnings'] ) ? [] : array_map( 'absint', explode( ',', $_GET['ex_warnings'] ) );
  $ex_tags = empty( $_GET['ex_tags'] ) ? [] : array_map( 'absint', explode( ',', $_GET['ex_tags'] ) );

  // Exclude pages if necessary
  if ( $is_any_post || empty( $_GET['post_type'] ) ) {
    $query->set( 'post_type', ['post', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'] );
  }

  // Included terms
  $included_terms = array(
    [ $genres, 'fcn_genre', 'genres_and' ],
    [ $fandoms, 'fcn_fandom', 'fandoms_and' ],
    [ $characters, 'fcn_character', 'characters_and' ],
    [ $warnings, 'fcn_content_warning', 'warning_and' ],
    [ $tags, 'post_tag', 'tags_and' ]
  );

  foreach ( $included_terms as $triple ) {
    if ( ! empty( $triple[0] ) ) {
      $all_terms = get_tags( array( 'taxonomy' => $triple[1] ) );
      $all_term_ids = array_map(
        function( $item ) {
          return $item->term_id;
        },
        $all_terms
      );
      $valid_terms = [];
      $query_part = [];
      $and = $_GET[ $triple[2] ] ?? 0;

      // Filter out terms that do not exist
      foreach ( $triple[0] as $term_id ) {
        if ( in_array( $term_id, $all_term_ids ) ) {
          $valid_terms[] = $term_id;
        }
      }

      // Skip if no terms are valid
      if ( empty( $valid_terms ) ) {
        continue;
      }

      if ( $and === '1' && count( $valid_terms ) > 1 ) {
        $query_part['relation'] = 'AND';

        // Must be split up or child terms will not be included
        foreach ( $valid_terms as $term ) {
          // Skip if empty
          if ( empty( $term ) ) {
            continue;
          }

          // Add to query
          $query_part[] = array(
            'taxonomy' => $triple[1],
            'field' => 'term_id',
            'terms' => [ $term ],
            'operator' => 'IN'
          );
        }
      } else {
        // Match any in array of terms
        $query_part = array(
          'taxonomy' => $triple[1],
          'field' => 'term_id',
          'terms' => $valid_terms,
          'operator' => 'IN'
        );
      }

      $tax_array[] = $query_part;
    }
  }

  // Excluded terms
  $excluded_terms = array(
    [ $ex_genres, 'fcn_genre', 'ex_genres_and' ],
    [ $ex_fandoms, 'fcn_fandom', 'ex_fandoms_and' ],
    [ $ex_characters, 'fcn_character', 'ex_characters_and' ],
    [ $ex_warnings, 'fcn_content_warning', 'ex_warning_and' ],
    [ $ex_tags, 'post_tag', 'ex_tags_and' ]
  );

  foreach ( $excluded_terms as $triple ) {
    if ( ! empty( $triple[0] ) ) {
      $all_terms = get_tags( array( 'taxonomy' => $triple[1] ) );
      $all_term_ids = array_map(
        function( $item ) {
          return $item->term_id;
        },
        $all_terms
      );
      $valid_terms = [];
      $query_part = [];
      $and = $_GET[ $triple[2] ] ?? 0;

      // Filter out terms that do not exist
      foreach ( $triple[0] as $term_id ) {
        if ( in_array( $term_id, $all_term_ids ) ) {
          $valid_terms[] = $term_id;
        }
      }

      // Skip if no terms are valid
      if ( empty( $valid_terms ) ) {
        continue;
      }

      if ( $and == '1' && count( $valid_terms ) > 1 ) {
        $query_part['relation'] = 'OR';

        // Must be split up or child terms will not be included
        foreach ( $valid_terms as $term ) {
          // Skip if empty
          if ( empty( $term ) ) {
            continue;
          }

          // Add to query
          $query_part[] = array(
            'taxonomy' => $triple[1],
            'field' => 'term_id',
            'terms' => [ $term ],
            'operator' => 'NOT IN'
          );
        }
      } else {
        // Match any in array of terms
        $query_part = array(
          'taxonomy' => $triple[1],
          'field' => 'term_id',
          'terms' => $valid_terms,
          'operator' => 'NOT IN'
        );
      }

      $tax_array[] = $query_part;
    }
  }

  // Add relation parameter if more than one tax_array
  if ( count( $tax_array ) > 1 ) {
    $tax_array['relation'] = 'AND';
  }

  // Extend with tax_query
  if ( ! empty( $tax_array ) ) {
    $query->set( 'tax_query', $tax_array );
  }

  // Prepare author array (negate IDs)
  if ( ! empty( $authors_out ) ) {
    $authors_out = array_map(
      function ( $item ) {
        return "-$item";
      },
      $authors_out
    );
  }

  $authors = array_merge( $authors_in, $authors_out );

  // Extend with authors (if any)
  if ( ! empty( $authors ) ) {
    $query->set( 'author', implode( ',', $authors ) );
  }
}

if ( ! get_option( 'fictioneer_disable_theme_search' ) ) {
  add_action( 'pre_get_posts' ,'fictioneer_extend_search_query', 11 );
}

?>
