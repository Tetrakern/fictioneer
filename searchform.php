<?php
/**
 * Search form
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 5.0.0
 * @since 5.11.0 - Added 'fictioneer_search_form_filters' action hook.
 *
 * @internal $args['simple']          Optional. Hide advanced options.
 * @internal $args['placeholder']     Optional. Change search placeholder.
 * @internal $args['preselect_type']  Optional. Default post type to query.
 * @internal $args['cache']           Whether to account for active caching.
 */
?>

<?php

// Setup
$no_params = empty( array_filter( $_GET ) );
$show_advanced = ! get_option( 'fictioneer_disable_theme_search' ) && ! ( $args['simple'] ?? 0 );
$placeholder = $args['placeholder'] ?? _x( 'Search keywords or phrase', 'Advanced search placeholder.', 'fictioneer' );
$post_type = sanitize_text_field( $_GET['post_type'] ?? $args['preselect_type'] ?? 'any' );

// Advanced setup
if ( $show_advanced ) {
  $sentence = sanitize_text_field( $_GET['sentence'] ?? 0 );
  $order = sanitize_text_field( $_GET['order'] ?? 'desc' );
  $orderby = sanitize_text_field( $_GET['orderby'] ?? 'modified' );

  $story_status = array_intersect(
    [ $_GET['story_status'] ?? 0 ],
    ['Completed', 'Ongoing', 'Oneshot', 'Hiatus', 'Canceled']
  );
  $story_status = reset( $story_status ) ?: 0;

  $all_authors = get_users(
    array(
      'has_published_posts' => ['fcn_story', 'fcn_chapter', 'fcn_recommendation', 'post']
    )
  );

  $skip_author_keywords = count( $all_authors ) > FICTIONEER_AUTHOR_KEYWORD_SEARCH_LIMIT;

  $queried_authors_in = sanitize_text_field( $_GET['authors'] ?? 0 );
  $queried_authors_out = sanitize_text_field( $_GET['ex_authors'] ?? 0 );
  $author_name = sanitize_text_field( $_GET['author_name'] ?? 0 ); // Simple text field

  $all_tags = get_tags();
  $all_genres = get_tags( array( 'taxonomy' => 'fcn_genre' ) );
  $all_fandoms = get_tags( array( 'taxonomy' => 'fcn_fandom' ) );
  $all_characters = get_tags( array( 'taxonomy' => 'fcn_character' ) );
  $all_warnings = get_tags( array( 'taxonomy' => 'fcn_content_warning' ) );

  $queried_genres = sanitize_text_field( $_GET['genres'] ?? 0 );
  $queried_fandoms = sanitize_text_field( $_GET['fandoms'] ?? 0 );
  $queried_characters = sanitize_text_field( $_GET['characters'] ?? 0 );
  $queried_warnings = sanitize_text_field( $_GET['warnings'] ?? 0 );
  $queried_tags = sanitize_text_field( $_GET['tags'] ?? 0 );

  $queried_ex_genres = sanitize_text_field( $_GET['ex_genres'] ?? 0 );
  $queried_ex_fandoms = sanitize_text_field( $_GET['ex_fandoms'] ?? 0 );
  $queried_ex_characters = sanitize_text_field( $_GET['ex_characters'] ?? 0 );
  $queried_ex_warnings = sanitize_text_field( $_GET['ex_warnings'] ?? 0 );
  $queried_ex_tags = sanitize_text_field( $_GET['ex_tags'] ?? 0 );

  $is_advanced_search = $post_type != 'any' || $sentence != '0' || $order != 'desc' || $orderby != 'modified' || $queried_tags || $queried_genres || $queried_fandoms || $queried_characters || $queried_warnings || $queried_ex_tags || $queried_ex_genres || $queried_ex_fandoms || $queried_ex_characters || $queried_ex_warnings || $queried_authors_in || $queried_authors_out || $author_name || $story_status;

  // Prime author cache
  if ( function_exists( 'update_post_author_caches' ) ) {
    update_post_author_caches( $all_authors );
  }

  // Prepare data JSONs
  $all_terms = array_merge( $all_tags, $all_genres, $all_fandoms, $all_characters, $all_warnings );
  $allow_list = [];

  foreach ( $all_terms as $term ) {
    $allow_list[ rawurlencode( strtolower( $term->name ) ) ] = $term->term_id;
  }

  if ( ! $skip_author_keywords ) {
    foreach ( $all_authors as $author ) {
      $allow_list[ rawurlencode( strtolower( $author->display_name ) ) ] = $author->ID;
    }
  }
}

?>

<form
  role="search"
  method="get"
  class="search-form <?php if ( ! $show_advanced ) echo '_simple'; ?>"
  action="<?php echo esc_url( home_url( '/' ) ); ?>"
  data-advanced="<?php echo ( is_search() && $show_advanced && $no_params ) ? 'true' : 'false'; ?>"
>

  <?php if ( $show_advanced ) : ?>
    <div class="allow-list" hidden><?php echo json_encode( $allow_list ); ?></div>
  <?php else : ?>
    <input type="hidden" name="post_type" value="<?php echo $post_type; ?>">
  <?php endif; ?>

  <div class="search-form__bar">

    <input
      type="search"
      class="search-form__string"
      placeholder="<?php echo esc_attr( $placeholder ); ?>"
      value="<?php echo trim( get_search_query() ); ?>"
      name="s"
      spellcheck="false"
      autocomplete="off"
      autocorrect="off"
      data-default=""
    >

    <div class="search-form__bar-actions">
      <?php if ( $show_advanced ) : ?>
        <button
          type="button"
          class="search-form__advanced-toggle"
          tabindex="0"
        ><?php _ex( 'Advanced', 'Advanced search toggle.', 'fictioneer' ); ?></button>
      <?php endif; ?>
      <button type="submit" class="search-form__submit" aria-label="<?php echo esc_attr__( 'Submit search request', 'fictioneer' ); ?>"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>

  </div>

  <?php if ( $show_advanced ) : ?>

    <div class="search-form__current">
      <?php if ( $is_advanced_search && ! ( $args['cache'] ?? 0 ) ) : ?>

        <button
          type="button"
          class="reset"
          data-reset="<?php echo esc_attr_x( 'Search form reset.', 'Advanced search reset message.', 'fictioneer' ); ?>"
        ><?php _ex( 'Reset', 'Advanced search reset button.', 'fictioneer' ); ?></button>

        <span class="search-form__current-type"><?php
          $translations = array(
            'any' => _x( 'Any', 'Advanced search option.', 'fictioneer' ),
            'fcn_chapter' => _x( 'Chapters', 'Advanced search option.', 'fictioneer' ),
            'fcn_story' => _x( 'Stories', 'Advanced search option.', 'fictioneer' ),
            'fcn_recommendation' => _x( 'Recommendations', 'Advanced search option.', 'fictioneer' ),
            'fcn_collection' => _x( 'Collections', 'Advanced search option.', 'fictioneer' ),
            'post' => _x( 'Posts', 'Advanced search option.', 'fictioneer' )
          );

          printf(
            _x( '<b>Type:</b> <span>%s</span>', 'Advanced search summary.', 'fictioneer' ),
            $translations[ $post_type ] ?? __( 'Invalid Value', 'fictioneer' )
          );
        ?></span>

        <span class="search-form__current-match"><?php
          $translations = array(
            '0' => _x( 'Keywords', 'Advanced search option.', 'fictioneer' ),
            '1' => _x( 'Phrase', 'Advanced search option.', 'fictioneer' )
          );

          printf(
            _x( '<b>Match:</b> <span>%s</span>', 'Advanced search summary.', 'fictioneer' ),
            $translations[ $sentence ] ?? __( 'Invalid Value', 'fictioneer' )
          );
        ?></span>

        <span class="search-form__current-sort"><?php
          $translations = array(
            'relevance' => _x( 'Relevance', 'Advanced search option.', 'fictioneer' ),
            'date' => _x( 'Published', 'Advanced search option.', 'fictioneer' ),
            'modified' => _x( 'Updated', 'Advanced search option.', 'fictioneer' ),
            'title' => _x( 'Title', 'Advanced search option.', 'fictioneer' )
          );

          printf(
            _x( '<b>Sort:</b> <span>%s</span>', 'Advanced search summary.', 'fictioneer' ),
            $translations[ $orderby ] ?? __( 'Invalid Value', 'fictioneer' )
          );
        ?></span>

        <span class="search-form__current-order"><?php
          $translations = array(
            'desc' => _x( 'Descending', 'Advanced search option.', 'fictioneer' ),
            'asc' => _x( 'Ascending', 'Advanced search option.', 'fictioneer' )
          );

          printf(
            _x( '<b>Order:</b> <span>%s</span>', 'Advanced search summary.', 'fictioneer' ),
            $translations[ $order ] ?? __( 'Invalid Value', 'fictioneer' )
          );
        ?></span>

        <span class="search-form__current-status"><?php
          printf(
            _x( '<b>Status:</b> <span>%s</span>', 'Advanced search summary.', 'fictioneer' ),
            $story_status ? fcntr( $story_status ) : _x( 'Any', 'Advanced search option.', 'fictioneer' )
          );
        ?></span>

        <?php
          // [query string, type, class suffix, summary heading]
          $queried_terms = array(
            [$queried_genres, 'fcn_genre', 'genres', 'genres_and', _x( '%sGenres:', 'Advanced search summary.', 'fictioneer' ),],
            [$queried_fandoms, 'fcn_fandom', 'fandoms', 'fandoms_and', _x( '%sFandoms:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_characters, 'fcn_character', 'characters', 'characters_and', _x( '%sCharacters:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_warnings, 'fcn_content_warning', 'warnings', 'warnings_and', _x( '%sWarnings:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_tags, 'post_tag', 'tags', 'tags_and', _x( '%sTags:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_authors_in, 'author', 'authors', 'authors_and', _x( '%sAuthors:', 'Advanced search summary.', 'fictioneer' )],
            [$author_name, 'author_name', 'author', 'author_and', _x( '%sAuthor:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_ex_genres, 'fcn_genre', 'ex-genres', 'ex_genres_and', _x( '%sExcluded Genres:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_ex_fandoms, 'fcn_fandom', 'ex-fandoms', 'ex_fandoms_and', _x( '%sExcluded Fandoms:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_ex_characters, 'fcn_character', 'ex-characters', 'ex_characters_and', _x( '%sExcluded Characters:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_ex_warnings, 'fcn_content_warning', 'ex-warnings', 'ex_warnings_and', _x( '%sExcluded Warnings:', 'Advanced search summary.', 'fictioneer' )],
            [$queried_ex_tags, 'post_tag', 'ex-tags', 'ex_tags_and', _x( '%sExcluded Tags:', 'Excluded Advanced search summary.', 'fictioneer' )],
            [$queried_authors_out, 'author', 'authors', 'ex_authors_and', _x( '%sExcluded Authors:', 'Advanced search summary.', 'fictioneer' )],
          );

          foreach ( $queried_terms as $quad ) {
            // Skip if nothing has been queried
            if ( empty( $quad[0] ) ) {
              continue;
            }

            // Open wrapper
            echo "<span class='search-form__current-{$quad[2]}'>";

            // AND/OR?
            $and = ( $_GET[ $quad[3] ] ?? 0 ) === '1' ?
              _x( '[&] ', 'Advanced search summary AND operation note.', 'fictioneer' ) : '';

            // Heading (needs whitespace left and right)
            printf( ' <b>' . $quad[4] . '</b> ', $and );

            // Item names from IDs
            $item_ids = explode( ',', $quad[0] );
            $names = [];

            // Author or taxonomy?
            switch ( $quad[1] ) {
              case 'author':
                foreach ( $item_ids as $author_id ) {
                  $author = get_user_by( 'ID', $author_id );

                  if ( $author ) {
                    $names[] = $author->display_name;
                  }
                }
                break;
              case 'author_name':
                $names[] = $author_name;
                break;
              default:
                foreach ( $item_ids as $term_id ) {
                  $term = get_term_by( 'term_id', $term_id, $quad[1] );

                  if ( $term ) {
                    $names[] = $term->name;
                  }
                }
            }

            echo implode( ', ', $names );

            // Close wrapper
            echo '</span>';
          }
        ?>

      <?php endif; ?>
    </div>

    <div class="search-form__advanced">

      <div class="search-form__select-group">

        <div class="search-form__select-wrapper select-wrapper">
          <div class="search-form__select-title"><?php _ex( 'Type', 'Advanced search heading.', 'fictioneer' ); ?></div>
          <select name="post_type" class="search-form__select" autocomplete="off" data-default="any">
            <option value="any" <?php echo $post_type == 'any' ? 'selected' : ''; ?>><?php _ex( 'Any', 'Advanced search option.', 'fictioneer' ); ?></option>
            <option value="fcn_chapter" <?php echo $post_type == 'fcn_chapter' ? 'selected' : ''; ?>><?php _ex( 'Chapters', 'Advanced search option.', 'fictioneer' ); ?></option>
            <option value="fcn_story" <?php echo $post_type == 'fcn_story' ? 'selected' : ''; ?>><?php _ex( 'Stories', 'Advanced search option.', 'fictioneer' ); ?></option>
            <?php if ( post_type_exists( 'fcn_recommendation' ) ) : ?>
              <option value="fcn_recommendation" <?php echo $post_type == 'fcn_recommendation' ? 'selected' : ''; ?>><?php _ex( 'Recommendations', 'Advanced search option.', 'fictioneer' ); ?></option>
            <?php endif; ?>
            <?php if ( post_type_exists( 'fcn_collection' ) ) : ?>
              <option value="fcn_collection" <?php echo $post_type == 'fcn_collection' ? 'selected' : ''; ?>><?php _ex( 'Collections', 'Advanced search option.', 'fictioneer' ); ?></option>
            <?php endif; ?>
            <option value="post" <?php echo $post_type == 'post' ? 'selected' : ''; ?>><?php _ex( 'Posts', 'Advanced search option.', 'fictioneer' ); ?></option>
          </select>
        </div>

        <div class="search-form__select-wrapper select-wrapper">
          <div class="search-form__select-title"><?php _ex( 'Match', 'Advanced search heading.', 'fictioneer' ); ?></div>
          <select name="sentence" class="search-form__select" autocomplete="off" data-default="0">
            <option value="0" <?php echo $sentence == '0' ? 'selected' : ''; ?>><?php _ex( 'Keywords', 'Advanced search option.', 'fictioneer' ); ?></option>
            <option value="1" <?php echo $sentence == '1' ? 'selected' : ''; ?>><?php _ex( 'Phrase', 'Advanced search option.', 'fictioneer' ); ?></option>
          </select>
        </div>

        <div class="search-form__select-wrapper select-wrapper">
          <div class="search-form__select-title"><?php _ex( 'Sort', 'Advanced search heading.', 'fictioneer' ); ?></div>
          <select name="orderby" class="search-form__select" autocomplete="off" data-default="modified">
            <option value="relevance" <?php echo $orderby == 'relevance' ? 'selected' : ''; ?>><?php _ex( 'Relevance', 'Advanced search option.', 'fictioneer' ); ?></option>
            <option value="date" <?php echo $orderby == 'date' ? 'selected' : ''; ?>><?php _ex( 'Published', 'Advanced search option.', 'fictioneer' ); ?></option>
            <option value="modified" <?php echo $orderby == 'modified' ? 'selected' : ''; ?>><?php _ex( 'Updated', 'Advanced search option.', 'fictioneer' ); ?></option>
            <option value="title" <?php echo $orderby == 'title' ? 'selected' : ''; ?>><?php _ex( 'Title', 'Advanced search option.', 'fictioneer' ); ?></option>
          </select>
        </div>

        <div class="search-form__select-wrapper select-wrapper">
          <div class="search-form__select-title"><?php _ex( 'Order', 'Advanced search heading.', 'fictioneer' ); ?></div>
          <select name="order" class="search-form__select" autocomplete="off" data-default="desc">
            <option value="desc" <?php echo $order == 'desc' ? 'selected' : ''; ?>><?php _ex( 'Descending', 'Advanced search option.', 'fictioneer' ); ?></option>
            <option value="asc" <?php echo $order == 'asc' ? 'selected' : ''; ?>><?php _ex( 'Ascending', 'Advanced search option.', 'fictioneer' ); ?></option>
          </select>
        </div>

        <?php do_action( 'fictioneer_search_form_filters', $args ); ?>
      </div>

      <?php if ( count( $allow_list ) > 1 ) : ?><hr><?php endif; ?>

      <?php if ( ! empty( $all_genres ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Genres', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_genres, 'genres', 'genres_and', 'genre', 'genres' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_fandoms ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Fandoms', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_fandoms, 'fandoms', 'fandoms_and', 'fandom', 'fandoms' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_characters ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Characters', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_characters, 'characters', 'characters_and', 'character', 'characters' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_tags ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Tags', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_tags, 'tags', 'tags_and', 'tag', 'tags' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_warnings ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Warnings', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_warnings, 'warnings', 'warnings_and', 'warning', 'warnings' ); ?>
      <?php endif; ?>

      <?php if ( count( $all_authors ) > 1 ) : ?>
        <?php if ( $skip_author_keywords ) : ?>
          <h6 class="search-form__option-headline"><?php _ex( 'Author', 'Advanced search heading.', 'fictioneer' ); ?></h6>
          <input type="text" class="search-form__text-input" name="author_name" value="<?php echo esc_attr( $author_name ); ?>" placeholder="<?php echo esc_attr_x( 'Search for an author', 'Advanced search placeholder.', 'fictioneer' ); ?>">
        <?php else : ?>
          <h6 class="search-form__option-headline"><?php _ex( 'Authors', 'Advanced search heading.', 'fictioneer' ); ?></h6>
          <?php fcn_keyword_search_authors_input( $all_authors, 'authors', 'author', 'authors' ); ?>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ( count( $allow_list ) > 1 ) : ?><hr><?php endif; ?>

      <?php if ( ! empty( $all_genres ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Exclude Genres', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_genres, 'ex_genres', 'ex_genres_and', 'genre', 'genres' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_fandoms ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Exclude Fandoms', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_fandoms, 'ex_fandoms', 'ex_fandoms_and', 'fandom', 'fandoms' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_characters ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Exclude Characters', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_characters, 'ex_characters', 'ex_characters_and', 'character', 'characters' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_tags ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Exclude Tags', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_tags, 'ex_tags', 'ex_tags_and', 'tag', 'tags' ); ?>
      <?php endif; ?>

      <?php if ( ! empty( $all_warnings ) ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Exclude Warnings', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_taxonomies_input( $all_warnings, 'ex_warnings', 'ex_warnings_and', 'warning', 'warnings' ); ?>
      <?php endif; ?>

      <?php if ( count( $all_authors ) > 1 && ! $skip_author_keywords ) : ?>
        <h6 class="search-form__option-headline"><?php _ex( 'Exclude Authors', 'Advanced search heading.', 'fictioneer' ); ?></h6>
        <?php fcn_keyword_search_authors_input( $all_authors, 'ex_authors', 'author', 'authors' ); ?>
      <?php endif; ?>

      <div class="search-form__advanced-actions">
        <div class="search-form__advanced-actions-left">
          <button type="button" class="search-form__advanced-reset reset button _secondary" data-reset="<?php echo esc_attr_x( 'Search form reset.', 'Advanced search reset message.', 'fictioneer' ); ?>"><?php _ex( 'Reset', 'Advanced search reset button.', 'fictioneer' ); ?></button>
        </div>
        <div class="search-form__advanced-actions-right">
          <button type="submit" class="search-form__advanced-submit submit button"><?php _ex( 'Search', 'Advanced search submit.', 'fictioneer' ); ?></button>
        </div>
      </div>

    </div>

  <?php endif; ?>

</form>
