<?php

// =============================================================================
// SETUP
// =============================================================================

define( 'FICTIONEER_OPTIONS', array(
  'booleans' => array(
    'fictioneer_enable_maintenance_mode' => array(
      'name' => 'fictioneer_enable_maintenance_mode',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_dark_mode_as_default' => array(
      'name' => 'fictioneer_dark_mode_as_default',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_authors' => array(
      'name' => 'fictioneer_show_authors',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_chapter_icons' => array(
      'name' => 'fictioneer_hide_chapter_icons',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_story_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_story_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_chapter_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_chapter_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_recommendation_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_recommendation_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_collection_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_collection_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_tags_on_story_cards' => array(
      'name' => 'fictioneer_show_tags_on_story_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_tags_on_chapter_cards' => array(
      'name' => 'fictioneer_show_tags_on_chapter_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_tags_on_recommendation_cards' => array(
      'name' => 'fictioneer_show_tags_on_recommendation_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_tags_on_collection_cards' => array(
      'name' => 'fictioneer_show_tags_on_collection_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_pages' => array(
      'name' => 'fictioneer_hide_taxonomies_on_pages',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_tags_on_pages' => array(
      'name' => 'fictioneer_hide_tags_on_pages',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_content_warnings_on_pages' => array(
      'name' => 'fictioneer_hide_content_warnings_on_pages',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_theme_rss' => array(
      'name' => 'fictioneer_enable_theme_rss',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_oauth' => array(
      'name' => 'fictioneer_enable_oauth',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_lightbox' => array(
      'name' => 'fictioneer_enable_lightbox',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_bookmarks' => array(
      'name' => 'fictioneer_enable_bookmarks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_follows' => array(
      'name' => 'fictioneer_enable_follows',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_checkmarks' => array(
      'name' => 'fictioneer_enable_checkmarks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_reminders' => array(
      'name' => 'fictioneer_enable_reminders',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_suggestions' => array(
      'name' => 'fictioneer_enable_suggestions',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_tts' => array(
      'name' => 'fictioneer_enable_tts',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_seo' => array(
      'name' => 'fictioneer_enable_seo',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_sitemap' => array(
      'name' => 'fictioneer_enable_sitemap',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_epubs' => array(
      'name' => 'fictioneer_enable_epubs',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_require_js_to_comment' => array(
      'name' => 'fictioneer_require_js_to_comment',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_ajax_comment_submit' => array(
      'name' => 'fictioneer_enable_ajax_comment_submit',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_comment_link_limit' => array(
      'name' => 'fictioneer_enable_comment_link_limit',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_comment_toolbar' => array(
      'name' => 'fictioneer_enable_comment_toolbar',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_comment_bbcodes' => array(
      'name' => 'fictioneer_disable_comment_bbcodes',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_ajax_comment_moderation' => array(
      'name' => 'fictioneer_enable_ajax_comment_moderation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_custom_badges' => array(
      'name' => 'fictioneer_enable_custom_badges',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_patreon_badges' => array(
      'name' => 'fictioneer_enable_patreon_badges',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_private_commenting' => array(
      'name' => 'fictioneer_enable_private_commenting',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_comment_notifications' => array(
      'name' => 'fictioneer_enable_comment_notifications',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_comment_reporting' => array(
      'name' => 'fictioneer_enable_comment_reporting',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_heartbeat' => array(
      'name' => 'fictioneer_disable_heartbeat',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_remove_head_clutter' => array(
      'name' => 'fictioneer_remove_head_clutter',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_reduce_admin_bar' => array(
      'name' => 'fictioneer_reduce_admin_bar',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_bundle_stylesheets' => array(
      'name' => 'fictioneer_bundle_stylesheets',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_bundle_scripts' => array(
      'name' => 'fictioneer_bundle_scripts',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_do_not_save_comment_ip' => array(
      'name' => 'fictioneer_do_not_save_comment_ip',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_logout_redirects_home' => array(
      'name' => 'fictioneer_logout_redirects_home',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_theme_logout' => array(
      'name' => 'fictioneer_disable_theme_logout',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_consent_wrappers' => array(
      'name' => 'fictioneer_consent_wrappers',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_cookie_banner' => array(
      'name' => 'fictioneer_cookie_banner',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_cache_compatibility' => array(
      'name' => 'fictioneer_enable_cache_compatibility',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_private_cache_compatibility' => array(
      'name' => 'fictioneer_enable_private_cache_compatibility',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_ajax_comments' => array(
      'name' => 'fictioneer_enable_ajax_comments',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_comment_callback' => array(
      'name' => 'fictioneer_disable_comment_callback',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_comment_query' => array(
      'name' => 'fictioneer_disable_comment_query',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_comment_form' => array(
      'name' => 'fictioneer_disable_comment_form',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_comment_pagination' => array(
      'name' => 'fictioneer_disable_comment_pagination',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_facebook_share' => array(
      'name' => 'fictioneer_disable_facebook_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_twitter_share' => array(
      'name' => 'fictioneer_disable_twitter_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_tumblr_share' => array(
      'name' => 'fictioneer_disable_tumblr_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_reddit_share' => array(
      'name' => 'fictioneer_disable_reddit_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_mastodon_share' => array(
      'name' => 'fictioneer_disable_mastodon_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_bluesky_share' => array(
      'name' => 'fictioneer_disable_bluesky_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_delete_theme_options_on_deactivation' => array(
      'name' => 'fictioneer_delete_theme_options_on_deactivation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_jquery_migrate' => array(
      'name' => 'fictioneer_enable_jquery_migrate',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_properties' => array(
      'name' => 'fictioneer_disable_properties',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_chapter_groups' => array(
      'name' => 'fictioneer_enable_chapter_groups',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_chapter_collapsing' => array(
      'name' => 'fictioneer_disable_chapter_collapsing',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_collapse_groups_by_default' => array(
      'name' => 'fictioneer_collapse_groups_by_default',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_public_cache_compatibility' => array(
      'name' => 'fictioneer_enable_public_cache_compatibility',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_full_post_content' => array(
      'name' => 'fictioneer_show_full_post_content',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_all_blocks' => array(
      'name' => 'fictioneer_enable_all_blocks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_ajax_authentication' => array(
      'name' => 'fictioneer_enable_ajax_authentication',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_application_passwords' => array(
      'name' => 'fictioneer_disable_application_passwords',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_user_comment_editing' => array(
      'name' => 'fictioneer_enable_user_comment_editing',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_ajax_comment_form' => array(
      'name' => 'fictioneer_enable_ajax_comment_form',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_sticky_comments' => array(
      'name' => 'fictioneer_enable_sticky_comments',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_commenting' => array(
      'name' => 'fictioneer_disable_commenting',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_purge_all_caches' => array(
      'name' => 'fictioneer_purge_all_caches',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_theme_search' => array(
      'name' => 'fictioneer_disable_theme_search',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_contact_forms' => array(
      'name' => 'fictioneer_disable_contact_forms',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_storygraph_api' => array(
      'name' => 'fictioneer_enable_storygraph_api',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_restrict_rest_api' => array(
      'name' => 'fictioneer_restrict_rest_api',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_chapter_appending' => array(
      'name' => 'fictioneer_enable_chapter_appending',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 1
    ),
    'fictioneer_limit_chapter_stories_by_author' => array(
      'name' => 'fictioneer_limit_chapter_stories_by_author',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 1
    ),
    'fictioneer_see_some_evil' => array(
      'name' => 'fictioneer_see_some_evil',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_fast_ajax_comments' => array(
      'name' => 'fictioneer_enable_fast_ajax_comments',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_rate_limits' => array(
      'name' => 'fictioneer_enable_rate_limits',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_advanced_meta_fields' => array(
      'name' => 'fictioneer_enable_advanced_meta_fields',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_story_changelog' => array(
      'name' => 'fictioneer_show_story_changelog',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_font_awesome' => array(
      'name' => 'fictioneer_disable_font_awesome',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_site_age_confirmation' => array(
      'name' => 'fictioneer_enable_site_age_confirmation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_post_age_confirmation' => array(
      'name' => 'fictioneer_enable_post_age_confirmation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_extended_story_list_meta_queries' => array(
      'name' => 'fictioneer_disable_extended_story_list_meta_queries',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_extended_chapter_list_meta_queries' => array(
      'name' => 'fictioneer_disable_extended_chapter_list_meta_queries',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_count_characters_as_words' => array(
      'name' => 'fictioneer_count_characters_as_words',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_generate_footnotes_from_tooltips' => array(
      'name' => 'fictioneer_generate_footnotes_from_tooltips',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_protected_excerpt' => array(
      'name' => 'fictioneer_show_protected_excerpt',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_large_card_chapter_list' => array(
      'name' => 'fictioneer_hide_large_card_chapter_list',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_story_cards_latest_chapters' => array(
      'name' => 'fictioneer_show_story_cards_latest_chapters',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_patreon_locks' => array(
      'name' => 'fictioneer_enable_patreon_locks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_password_form_with_patreon' => array(
      'name' => 'fictioneer_hide_password_form_with_patreon',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_wp_login_link' => array(
      'name' => 'fictioneer_show_wp_login_link',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_static_partials' => array(
      'name' => 'fictioneer_enable_static_partials',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_randomize_oauth_usernames' => array(
      'name' => 'fictioneer_randomize_oauth_usernames',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_rewrite_chapter_permalinks' => array(
      'name' => 'fictioneer_rewrite_chapter_permalinks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_xmlrpc' => array(
      'name' => 'fictioneer_enable_xmlrpc',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_emojis' => array(
      'name' => 'fictioneer_disable_emojis',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_default_formatting_indent' => array(
      'name' => 'fictioneer_disable_default_formatting_indent',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_override_chapter_status_icons' => array(
      'name' => 'fictioneer_override_chapter_status_icons',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_custom_fields' => array(
      'name' => 'fictioneer_enable_custom_fields',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_anti_flicker' => array(
      'name' => 'fictioneer_enable_anti_flicker',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_hide_categories' => array(
      'name' => 'fictioneer_hide_categories',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_story_card_caching' => array(
      'name' => 'fictioneer_enable_story_card_caching',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_allow_rest_save_actions' => array(
      'name' => 'fictioneer_allow_rest_save_actions',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_global_splide' => array(
      'name' => 'fictioneer_enable_global_splide',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_log_posts' => array(
      'name' => 'fictioneer_log_posts',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_menu_transients' => array(
      'name' => 'fictioneer_disable_menu_transients',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_line_break_fix' => array(
      'name' => 'fictioneer_enable_line_break_fix',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_show_story_modified_date' => array(
      'name' => 'fictioneer_show_story_modified_date',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_chapter_list_transients' => array(
      'name' => 'fictioneer_disable_chapter_list_transients',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_shortcode_transients' => array(
      'name' => 'fictioneer_disable_shortcode_transients',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_css_skins' => array(
      'name' => 'fictioneer_enable_css_skins',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_exclude_non_stories_from_cloud_counts' => array(
      'name' => 'fictioneer_exclude_non_stories_from_cloud_counts',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_enable_ffcnr_auth' => array(
      'name' => 'fictioneer_enable_ffcnr_auth',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_redirect_scheduled_chapter_404' => array(
      'name' => 'fictioneer_redirect_scheduled_chapter_404',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_exclude_protected_from_rss' => array(
      'name' => 'fictioneer_exclude_protected_from_rss',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_exclude_protected_from_discord' => array(
      'name' => 'fictioneer_exclude_protected_from_discord',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    ),
    'fictioneer_disable_header_image_preload' => array(
      'name' => 'fictioneer_disable_header_image_preload',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    ),
    'fictioneer_enable_lastpostmodified_caching' => array(
      'name' => 'fictioneer_enable_lastpostmodified_caching',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    ),
    'fictioneer_show_scheduled_chapters' => array(
      'name' => 'fictioneer_show_scheduled_chapters',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    ),
    'fictioneer_enable_scheduled_chapter_commenting' => array(
      'name' => 'fictioneer_enable_scheduled_chapter_commenting',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox'
    ),
  ),
  'integers' => array(
    'fictioneer_user_profile_page' => array(
      'name' => 'fictioneer_user_profile_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_bookmarks_page' => array(
      'name' => 'fictioneer_bookmarks_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_stories_page' => array(
      'name' => 'fictioneer_stories_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_chapters_page' => array(
      'name' => 'fictioneer_chapters_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_recommendations_page' => array(
      'name' => 'fictioneer_recommendations_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_collections_page' => array(
      'name' => 'fictioneer_collections_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_bookshelf_page' => array(
      'name' => 'fictioneer_bookshelf_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_404_page' => array(
      'name' => 'fictioneer_404_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_authors_page' => array(
      'name' => 'fictioneer_authors_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_page_id',
      'default' => -1
    ),
    'fictioneer_comment_report_threshold' => array(
      'name' => 'fictioneer_comment_report_threshold',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_integer_one_up',
      'default' => 10
    ),
    'fictioneer_comment_link_limit_threshold' => array(
      'name' => 'fictioneer_comment_link_limit_threshold',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_integer_one_up',
      'default' => 3
    ),
    'fictioneer_words_per_minute' => array(
      'name' => 'fictioneer_words_per_minute',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_words_per_minute',
      'default' => 200
    ),
    'fictioneer_user_comment_edit_time' => array(
      'name' => 'fictioneer_user_comment_edit_time',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 15
    ),
    'fictioneer_upload_size_limit' => array(
      'name' => 'fictioneer_upload_size_limit',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'default' => 5
    ),
    'fictioneer_patreon_global_lock_amount' => array(
      'name' => 'fictioneer_patreon_global_lock_amount',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_absint_or_empty_string'
    ),
    'fictioneer_patreon_global_lock_lifetime_amount' => array(
      'name' => 'fictioneer_patreon_global_lock_lifetime_amount',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_absint_or_empty_string'
    ),
    'fictioneer_patreon_global_lock_unlock_amount' => array(
      'name' => 'fictioneer_patreon_global_lock_unlock_amount',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_absint_or_empty_string'
    )
  ),
  'floats' => array(
    'fictioneer_word_count_multiplier' => array(
      'name' => 'fictioneer_word_count_multiplier',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_positive_float_def1',
      'default' => 1.0
    )
  ),
  'strings' => array(
    'fictioneer_phrase_maintenance' => array(
      'name' => 'fictioneer_phrase_maintenance',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post'
    ),
    'fictioneer_phrase_login_modal' => array(
      'name' => 'fictioneer_phrase_login_modal',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post'
    ),
    'fictioneer_phrase_cookie_consent_banner' => array(
      'name' => 'fictioneer_phrase_cookie_consent_banner',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'fictioneer_sanitize_phrase_cookie_consent_banner'
    ),
    'fictioneer_phrase_comment_reply_notification' => array(
      'name' => 'fictioneer_phrase_comment_reply_notification',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post'
    ),
    'fictioneer_system_email_address' => array(
      'name' => 'fictioneer_system_email_address',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_email'
    ),
    'fictioneer_system_email_name' => array(
      'name' => 'fictioneer_system_email_name',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_patreon_label' => array(
      'name' => 'fictioneer_patreon_label',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_comments_notice' => array(
      'name' => 'fictioneer_comments_notice',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'wp_kses_post'
    ),
    'fictioneer_discord_client_id' => array(
      'name' => 'fictioneer_discord_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_discord_client_secret' => array(
      'name' => 'fictioneer_discord_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_discord_channel_comments_webhook' => array(
      'name' => 'fictioneer_discord_channel_comments_webhook',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_discord_channel_stories_webhook' => array(
      'name' => 'fictioneer_discord_channel_stories_webhook',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_discord_channel_chapters_webhook' => array(
      'name' => 'fictioneer_discord_channel_chapters_webhook',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_discord_invite_link' => array(
      'name' => 'fictioneer_discord_invite_link',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_twitch_client_id' => array(
      'name' => 'fictioneer_twitch_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_twitch_client_secret' => array(
      'name' => 'fictioneer_twitch_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_google_client_id' => array(
      'name' => 'fictioneer_google_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_google_client_secret' => array(
      'name' => 'fictioneer_google_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_patreon_client_id' => array(
      'name' => 'fictioneer_patreon_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_patreon_client_secret' => array(
      'name' => 'fictioneer_patreon_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_patreon_campaign_link' => array(
      'name' => 'fictioneer_patreon_campaign_link',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_patreon_url'
    ),
    'fictioneer_patreon_unlock_message' => array(
      'name' => 'fictioneer_patreon_unlock_message',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'wp_kses_post'
    ),
    'fictioneer_patreon_global_lock_tiers' => array(
      'name' => 'fictioneer_patreon_global_lock_tiers',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'fictioneer_sanitize_global_patreon_tiers'
    ),
    'fictioneer_subitem_date_format' => array(
      'name' => 'fictioneer_subitem_date_format',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_subitem_short_date_format' => array(
      'name' => 'fictioneer_subitem_short_date_format',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_contact_email_addresses' => array(
      'name' => 'fictioneer_contact_email_addresses',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_textarea_field'
    ),
    'fictioneer_upload_mime_types' => array(
      'name' => 'fictioneer_upload_mime_types',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_textarea_field',
      'default' => FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS
    ),
    'fictioneer_phrase_site_age_confirmation' => array(
      'name' => 'fictioneer_phrase_site_age_confirmation',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post'
    ),
    'fictioneer_phrase_post_age_confirmation' => array(
      'name' => 'fictioneer_phrase_post_age_confirmation',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post'
    ),
    'fictioneer_google_fonts_links' => array(
      'name' => 'fictioneer_google_fonts_links',
      'group' => 'fictioneer-settings-fonts-group',
      'sanitize_callback' => 'fictioneer_sanitize_google_fonts_links'
    ),
    'fictioneer_comment_form_selector' => array(
      'name' => 'fictioneer_comment_form_selector',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_subscribestar_client_id' => array(
      'name' => 'fictioneer_subscribestar_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_subscribestar_client_secret' => array(
      'name' => 'fictioneer_subscribestar_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field'
    ),
    'fictioneer_age_confirmation_redirect_site' => array(
      'name' => 'fictioneer_age_confirmation_redirect_site',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'fictioneer_sanitize_url'
    ),
    'fictioneer_age_confirmation_redirect_post' => array(
      'name' => 'fictioneer_age_confirmation_redirect_post',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'fictioneer_sanitize_url'
    )
  )
));

/**
 * Return label of setting if defined
 *
 * Note: These labels can differ from the ones used on the
 * settings page, which can also include instructions.
 *
 * @since 5.15.3
 *
 * @param string $option  The name of the option.
 *
 * @return string The label of the option or the option name if none found.
 */

function fictioneer_get_option_label( $option ) {
  static $labels = null;

  // Labels
  if ( $labels === null ) {
    $labels = array(
      'fictioneer_enable_maintenance_mode' => __( 'Enable maintenance mode', 'fictioneer' ),
      'fictioneer_dark_mode_as_default' => __( 'Enable dark mode as default', 'fictioneer' ),
      'fictioneer_show_authors' => __( 'Display authors on cards and posts', 'fictioneer' ),
      'fictioneer_hide_chapter_icons' => __( 'Hide chapter icons', 'fictioneer' ),
      'fictioneer_hide_taxonomies_on_story_cards' => __( 'Hide taxonomies on story cards', 'fictioneer' ),
      'fictioneer_hide_taxonomies_on_chapter_cards' => __( 'Hide taxonomies on chapter cards', 'fictioneer' ),
      'fictioneer_hide_taxonomies_on_recommendation_cards' => __( 'Hide taxonomies on recommendation cards', 'fictioneer' ),
      'fictioneer_hide_taxonomies_on_collection_cards' => __( 'Hide taxonomies on collection cards', 'fictioneer' ),
      'fictioneer_show_tags_on_story_cards' => __( 'Show tags on story cards', 'fictioneer' ),
      'fictioneer_show_tags_on_chapter_cards' => __( 'Show tags on chapter cards', 'fictioneer' ),
      'fictioneer_show_tags_on_recommendation_cards' => __( 'Show tags on recommendation cards', 'fictioneer' ),
      'fictioneer_show_tags_on_collection_cards' => __( 'Show tags on collection cards', 'fictioneer' ),
      'fictioneer_hide_taxonomies_on_pages' => __( 'Hide taxonomies on pages', 'fictioneer' ),
      'fictioneer_hide_tags_on_pages' => __( 'Hide tags on pages', 'fictioneer' ),
      'fictioneer_hide_content_warnings_on_pages' => __( 'Hide content warnings on pages', 'fictioneer' ),
      'fictioneer_enable_theme_rss' => __( 'Enable theme RSS feeds and buttons', 'fictioneer' ),
      'fictioneer_enable_oauth' => __( 'Enable OAuth 2.0 authentication', 'fictioneer' ),
      'fictioneer_enable_lightbox' => __( 'Enable lightbox', 'fictioneer' ),
      'fictioneer_enable_bookmarks' => __( 'Enable Bookmarks', 'fictioneer' ),
      'fictioneer_enable_follows' => __( 'Enable Follows (requires account)', 'fictioneer' ),
      'fictioneer_enable_checkmarks' => __( 'Enable Checkmarks (requires account)', 'fictioneer' ),
      'fictioneer_enable_reminders' => __( 'Enable Reminders (requires account)', 'fictioneer' ),
      'fictioneer_enable_suggestions' => __( 'Enable Suggestions', 'fictioneer' ),
      'fictioneer_enable_tts' => __( 'Enable Text-To-Speech (experimental)', 'fictioneer' ),
      'fictioneer_enable_seo' => __( 'Enable SEO features', 'fictioneer' ),
      'fictioneer_enable_sitemap' => __( 'Enable theme sitemap generation', 'fictioneer' ),
      'fictioneer_enable_epubs' => __( 'Enable ePUB converter (experimental)', 'fictioneer' ),
      'fictioneer_require_js_to_comment' => __( 'Require JavaScript to comment', 'fictioneer' ),
      'fictioneer_enable_ajax_comment_submit' => __( 'Enable AJAX comment submission', 'fictioneer' ),
      'fictioneer_enable_comment_link_limit' => __( 'Limit comments to [n] link(s) or less', 'fictioneer' ),
      'fictioneer_enable_comment_toolbar' => __( 'Enable comment toolbar', 'fictioneer' ),
      'fictioneer_disable_comment_bbcodes' => __( 'Disable comment BBCodes', 'fictioneer' ),
      'fictioneer_enable_ajax_comment_moderation' => __( 'Enable AJAX comment moderation', 'fictioneer' ),
      'fictioneer_enable_custom_badges' => __( 'Enable custom badges', 'fictioneer' ),
      'fictioneer_enable_patreon_badges' => __( 'Enable Patreon comment badges', 'fictioneer' ),
      'fictioneer_enable_private_commenting' => __( 'Enable private commenting', 'fictioneer' ),
      'fictioneer_enable_comment_notifications' => __( 'Enable comment reply notifications', 'fictioneer' ),
      'fictioneer_enable_comment_reporting' => __( 'Enable comment reporting', 'fictioneer' ),
      'fictioneer_disable_heartbeat' => __( 'Disable Heartbeat API', 'fictioneer' ),
      'fictioneer_remove_head_clutter' => __( 'Remove clutter from HTML head', 'fictioneer' ),
      'fictioneer_reduce_admin_bar' => __( 'Reduce admin bar items', 'fictioneer' ),
      'fictioneer_bundle_stylesheets' => __( 'Bundle CSS files into one', 'fictioneer' ),
      'fictioneer_bundle_scripts' => __( 'Bundle JavaScript files into one', 'fictioneer' ),
      'fictioneer_do_not_save_comment_ip' => __( 'Do not save comment IP addresses', 'fictioneer' ),
      'fictioneer_logout_redirects_home' => __( 'Logout redirects Home', 'fictioneer' ),
      'fictioneer_disable_theme_logout' => __( 'Disable theme logout without nonce', 'fictioneer' ),
      'fictioneer_consent_wrappers' => __( 'Add consent wrappers to embedded content', 'fictioneer' ),
      'fictioneer_cookie_banner' => __( 'Enable cookie banner and consent function', 'fictioneer' ),
      'fictioneer_enable_cache_compatibility' => __( 'Enable cache compatibility mode', 'fictioneer' ),
      'fictioneer_enable_private_cache_compatibility' => __( 'Enable private cache compatibility mode', 'fictioneer' ),
      'fictioneer_enable_ajax_comments' => __( 'Enable AJAX comment section', 'fictioneer' ),
      'fictioneer_disable_comment_callback' => __( 'Disable theme comment style (callback)', 'fictioneer' ),
      'fictioneer_disable_comment_query' => __( 'Disable theme comment query', 'fictioneer' ),
      'fictioneer_disable_comment_form' => __( 'Disable theme comment form', 'fictioneer' ),
      'fictioneer_disable_comment_pagination' => __( 'Disable theme comment pagination', 'fictioneer' ),
      'fictioneer_disable_facebook_share' => __( 'Disable Facebook share button', 'fictioneer' ),
      'fictioneer_disable_twitter_share' => __( 'Disable Twitter share button', 'fictioneer' ),
      'fictioneer_disable_tumblr_share' => __( 'Disable Tumblr share button', 'fictioneer' ),
      'fictioneer_disable_reddit_share' => __( 'Disable Reddit share button', 'fictioneer' ),
      'fictioneer_disable_mastodon_share' => __( 'Disable Mastodon share button', 'fictioneer' ),
      'fictioneer_disable_bluesky_share' => __( 'Disable Bluesky share button', 'fictioneer' ),
      'fictioneer_delete_theme_options_on_deactivation' => __( 'Delete all settings and theme mods on deactivation', 'fictioneer' ),
      'fictioneer_enable_jquery_migrate' => __( 'Enable jQuery migrate script', 'fictioneer' ),
      'fictioneer_disable_properties' => __( 'Disable Fictioneer CSS properties', 'fictioneer' ),
      'fictioneer_enable_chapter_groups' => __( 'Enable chapter groups', 'fictioneer' ),
      'fictioneer_disable_chapter_collapsing' => __( 'Disable collapsing of chapters', 'fictioneer' ),
      'fictioneer_collapse_groups_by_default' => __( 'Collapse chapter groups by default', 'fictioneer' ),
      'fictioneer_enable_public_cache_compatibility' => __( 'Enable public cache compatibility mode', 'fictioneer' ),
      'fictioneer_show_full_post_content' => __( 'Display full posts instead of excerpts', 'fictioneer' ),
      'fictioneer_enable_all_blocks' => __( 'Enable all Gutenberg blocks', 'fictioneer' ),
      'fictioneer_enable_ajax_authentication' => __( 'Enable AJAX user authentication', 'fictioneer' ),
      'fictioneer_disable_application_passwords' => __( 'Disable application passwords', 'fictioneer' ),
      'fictioneer_enable_user_comment_editing' => __( 'Enable comment editing', 'fictioneer' ),
      'fictioneer_enable_ajax_comment_form' => __( 'Enable AJAX comment form', 'fictioneer' ),
      'fictioneer_enable_sticky_comments' => __( 'Enable sticky comments', 'fictioneer' ),
      'fictioneer_disable_commenting' => __( 'Disable commenting across the site', 'fictioneer' ),
      'fictioneer_purge_all_caches' => __( 'Purge all caches on content updates', 'fictioneer' ),
      'fictioneer_disable_theme_search' => __( 'Disable advanced search', 'fictioneer' ),
      'fictioneer_disable_contact_forms' => __( 'Disable theme contact forms', 'fictioneer' ),
      'fictioneer_enable_storygraph_api' => __( 'Enable Storygraph API', 'fictioneer' ),
      'fictioneer_restrict_rest_api' => __( 'Restrict default REST API', 'fictioneer' ),
      'fictioneer_enable_chapter_appending' => __( 'Append new chapters to story', 'fictioneer' ),
      'fictioneer_limit_chapter_stories_by_author' => __( 'Restrict chapter stories by author', 'fictioneer' ),
      'fictioneer_see_some_evil' => __( 'Monitor posts for suspicious content', 'fictioneer' ),
      'fictioneer_enable_fast_ajax_comments' => __( 'Enable fast AJAX for comments', 'fictioneer' ),
      'fictioneer_enable_rate_limits' => __( 'Enable rate limiting for AJAX requests', 'fictioneer' ),
      'fictioneer_enable_advanced_meta_fields' => __( 'Enable advanced meta fields', 'fictioneer' ),
      'fictioneer_show_story_changelog' => __( 'Show story changelog button', 'fictioneer' ),
      'fictioneer_disable_font_awesome' => __( 'Disable Font Awesome integration', 'fictioneer' ),
      'fictioneer_enable_site_age_confirmation' => __( 'Enable age confirmation modal for site', 'fictioneer' ),
      'fictioneer_enable_post_age_confirmation' => __( 'Enable age confirmation modal for posts', 'fictioneer' ),
      'fictioneer_disable_extended_story_list_meta_queries' => __( 'Disable extended story list meta queries', 'fictioneer' ),
      'fictioneer_disable_extended_chapter_list_meta_queries' => __( 'Disable extended chapter list meta queries', 'fictioneer' ),
      'fictioneer_count_characters_as_words' => __( 'Count characters instead of words', 'fictioneer' ),
      'fictioneer_generate_footnotes_from_tooltips' => __( 'Generate footnotes from tooltips', 'fictioneer' ),
      'fictioneer_show_protected_excerpt' => __( 'Show excerpt on password-protected posts', 'fictioneer' ),
      'fictioneer_hide_large_card_chapter_list' => __( 'Hide chapter list on large story cards', 'fictioneer' ),
      'fictioneer_show_story_cards_latest_chapters' => __( 'Show latest chapter on large story cards', 'fictioneer' ),
      'fictioneer_enable_patreon_locks' => __( 'Enable Patreon content gate', 'fictioneer' ),
      'fictioneer_hide_password_form_with_patreon' => __( 'Hide password form on Patreon-gated posts', 'fictioneer' ),
      'fictioneer_user_profile_page' => __( 'Account page assignment', 'fictioneer' ),
      'fictioneer_bookmarks_page' => __( 'Bookmarks page assignment', 'fictioneer' ),
      'fictioneer_stories_page' => __( 'Stories page assignment', 'fictioneer' ),
      'fictioneer_chapters_page' => __( 'Chapters page assignment', 'fictioneer' ),
      'fictioneer_recommendations_page' => __( 'Recommendations page assignment', 'fictioneer' ),
      'fictioneer_collections_page' => __( 'Collections page assignment', 'fictioneer' ),
      'fictioneer_bookshelf_page' => __( 'Bookshelf page assignment', 'fictioneer' ),
      'fictioneer_404_page' => __( '404 page assignment', 'fictioneer' ),
      'fictioneer_authors_page' => __( 'Author index page assignment', 'fictioneer' ),
      'fictioneer_comment_report_threshold' => __( 'Automatic moderation report threshold', 'fictioneer' ),
      'fictioneer_comment_link_limit_threshold' => __( 'Allowed number of links in comment', 'fictioneer' ),
      'fictioneer_words_per_minute' => __( 'Words per minute', 'fictioneer' ),
      'fictioneer_user_comment_edit_time' => __( 'Minutes a comment can be edited', 'fictioneer' ),
      'fictioneer_upload_size_limit' => __( 'Size limit for file uploads', 'fictioneer' ),
      'fictioneer_word_count_multiplier' => __( 'Multiplier for displayed word counts', 'fictioneer' ),
      'fictioneer_phrase_maintenance' => __( 'Maintenance note', 'fictioneer' ),
      'fictioneer_phrase_login_modal' => __( 'Login modal', 'fictioneer' ),
      'fictioneer_phrase_cookie_consent_banner' => __( 'Cookie consent banner', 'fictioneer' ),
      'fictioneer_phrase_comment_reply_notification' => __( 'Comment reply notification email', 'fictioneer' ),
      'fictioneer_phrase_site_age_confirmation' => __( 'Age confirmation modal content for the site', 'fictioneer' ),
      'fictioneer_phrase_post_age_confirmation' => __( 'Age confirmation modal content for posts', 'fictioneer' ),
      'fictioneer_system_email_address' => __( 'System email address', 'fictioneer' ),
      'fictioneer_system_email_name' => __( 'System email name', 'fictioneer' ),
      'fictioneer_patreon_label' => __( 'Patreon badge label', 'fictioneer' ),
      'fictioneer_comments_notice' => __( 'Notice above comments', 'fictioneer' ),
      'fictioneer_discord_client_id' => __( 'Discord Client ID', 'fictioneer' ),
      'fictioneer_discord_client_secret' => __( 'Discord Client Secret', 'fictioneer' ),
      'fictioneer_discord_channel_comments_webhook' => __( 'Discord comment channel webhook', 'fictioneer' ),
      'fictioneer_discord_channel_stories_webhook' => __( 'Discord story channel webhook', 'fictioneer' ),
      'fictioneer_discord_channel_chapters_webhook' => __( 'Discord chapter channel webhook', 'fictioneer' ),
      'fictioneer_discord_invite_link' => __( 'Discord invite link', 'fictioneer' ),
      'fictioneer_twitch_client_id' => __( 'Twitch Client ID', 'fictioneer' ),
      'fictioneer_twitch_client_secret' => __( 'Twitch Client Secret', 'fictioneer' ),
      'fictioneer_google_client_id' => __( 'Google Client ID', 'fictioneer' ),
      'fictioneer_google_client_secret' => __( 'Google Client Secret', 'fictioneer' ),
      'fictioneer_patreon_client_id' => __( 'Patreon Client ID', 'fictioneer' ),
      'fictioneer_patreon_client_secret' => __( 'Patreon Client Secret', 'fictioneer' ),
      'fictioneer_patreon_campaign_link' => __( 'Patreon campaign link', 'fictioneer' ),
      'fictioneer_patreon_unlock_message' => __( 'Patreon gate message override', 'fictioneer' ),
      'fictioneer_patreon_global_lock_tiers' => __( 'Global tiers to unlock post', 'fictioneer' ),
      'fictioneer_patreon_global_lock_amount' => __( 'Global pledge threshold in cents', 'fictioneer' ),
      'fictioneer_patreon_global_lock_lifetime_amount' => __( 'Global lifetime pledge threshold in cents', 'fictioneer' ),
      'fictioneer_patreon_global_lock_unlock_amount' => __( 'Global unlock gate pledge threshold in cents', 'fictioneer' ),
      'fictioneer_subitem_date_format' => __( 'Subitem long date format', 'fictioneer' ),
      'fictioneer_subitem_short_date_format' => __( 'Subitem short date format', 'fictioneer' ),
      'fictioneer_contact_email_addresses' => __( 'Contact form email receivers', 'fictioneer' ),
      'fictioneer_upload_mime_types' => __( 'Allowed file upload mime types', 'fictioneer' ),
      'fictioneer_google_fonts_links' => __( ' Google Fonts links', 'fictioneer' ),
      'fictioneer_comment_form_selector' => __( 'Comment form CSS selector', 'fictioneer' ),
      'fictioneer_show_wp_login_link' => __( 'Show default WordPress login in modal', 'fictioneer' ),
      'fictioneer_enable_static_partials' => __( 'Enable caching of partials', 'fictioneer' ),
      'fictioneer_randomize_oauth_usernames' => __( 'Randomize OAuth 2.0 usernames', 'fictioneer' ),
      'fictioneer_rewrite_chapter_permalinks' => __( 'Rewrite chapter permalinks to include story ', 'fictioneer' ),
      'fictioneer_enable_xmlrpc' => __( 'Enable XML-RPC', 'fictioneer' ),
      'fictioneer_disable_emojis' => __( 'Disable WordPress emojis', 'fictioneer' ),
      'fictioneer_disable_default_formatting_indent' => __( 'Disable default indentation of chapter paragraphs', 'fictioneer' ),
      'fictioneer_override_chapter_status_icons' => __( 'Override chapter status icons', 'fictioneer' ),
      'fictioneer_enable_custom_fields' => __( 'Enable custom fields', 'fictioneer' ),
      'fictioneer_enable_anti_flicker' => __( 'Enable anti-flicker script', 'fictioneer' ),
      'fictioneer_hide_categories' => __( 'Hide categories on posts', 'fictioneer' ),
      'fictioneer_enable_story_card_caching' => __( 'Enable caching of story cards', 'fictioneer' ),
      'fictioneer_allow_rest_save_actions' => __( 'Allow REST requests to trigger save actions', 'fictioneer' ),
      'fictioneer_enable_global_splide' => __( 'Enable Splide slider globally', 'fictioneer' ),
      'fictioneer_log_posts' => __( 'Log all post updates', 'fictioneer' ),
      'fictioneer_disable_menu_transients' => __( 'Disable menu Transient caching', 'fictioneer' ),
      'fictioneer_enable_line_break_fix' => __( 'Enable fixing of chapter paragraphs', 'fictioneer' ),
      'fictioneer_show_story_modified_date' => __( 'Show the modified date on story pages', 'fictioneer' ),
      'fictioneer_disable_chapter_list_transients' => __( 'Disable chapter list Transient caching', 'fictioneer' ),
      'fictioneer_disable_shortcode_transients' => __( 'Disable shortcode Transient caching', 'fictioneer' ),
      'fictioneer_enable_css_skins' => __( 'Enable CSS skins (requires account)', 'fictioneer' ),
      'fictioneer_exclude_non_stories_from_cloud_counts' => __( 'Only count stories in taxonomy clouds', 'fictioneer' ),
      'fictioneer_enable_ffcnr_auth' => __( 'Enable FFCNR user authentication', 'fictioneer' ),
      'fictioneer_redirect_scheduled_chapter_404' => __( 'Redirect scheduled chapters to story or home', 'fictioneer' ),
      'fictioneer_exclude_protected_from_rss' => __( 'Exclude password-protected posts from RSS', 'fictioneer' ),
      'fictioneer_exclude_protected_from_discord' => __( 'Exclude password-protected posts from Discord notifications', 'fictioneer' ),
      'fictioneer_disable_header_image_preload' => __( 'Disable preloading of header image', 'fictioneer' ),
      'fictioneer_enable_lastpostmodified_caching' => __( 'Cache last post modified date as Transient', 'fictioneer' ),
      'fictioneer_show_scheduled_chapters' => __( 'Make scheduled chapters publicly accessible', 'fictioneer' ),
      'fictioneer_enable_scheduled_chapter_commenting' => __( 'Allow commenting on scheduled posts', 'fictioneer' )
    );
  }

  // Return label or option name if none found
  return $labels[ $option ] ?? $option;
}

// =============================================================================
// REGISTER ADMIN SETTINGS
// =============================================================================

/**
 * Registers settings for the Fictioneer theme
 *
 * @since 4.0.0
 */

function fictioneer_register_settings() {
  // Booleans
  foreach ( FICTIONEER_OPTIONS['booleans'] as $setting ) {
    register_setting(
      $setting['group'],
      $setting['name'],
      array(
        'sanitize_callback' => $setting['sanitize_callback']
      )
    );

    // Ensure option exists in the database (use fallback for check)
    if ( get_option( $setting['name'], '_not_set' ) === '_not_set' ) {
      add_option( $setting['name'], $setting['default'] ?? '' );
    }
  }

  // Integers
  foreach ( FICTIONEER_OPTIONS['integers'] as $setting ) {
    register_setting(
      $setting['group'],
      $setting['name'],
      array(
        'sanitize_callback' => $setting['sanitize_callback']
      )
    );

    // Ensure option exists in the database (use fallback for check)
    if ( get_option( $setting['name'], '_not_set' ) === '_not_set' ) {
      add_option( $setting['name'], $setting['default'] ?? '' );
    }
  }

  // Floats
  foreach ( FICTIONEER_OPTIONS['floats'] as $setting ) {
    register_setting(
      $setting['group'],
      $setting['name'],
      array(
        'sanitize_callback' => $setting['sanitize_callback']
      )
    );

    // Ensure option exists in the database (use fallback for check)
    if ( get_option( $setting['name'], '_not_set' ) === '_not_set' ) {
      add_option( $setting['name'], $setting['default'] ?? '' );
    }
  }

  // Strings
  foreach ( FICTIONEER_OPTIONS['strings'] as $setting ) {
    register_setting(
      $setting['group'],
      $setting['name'],
      array(
        'sanitize_callback' => $setting['sanitize_callback']
      )
    );

    // Ensure option exists in the database (use fallback for check)
    if ( get_option( $setting['name'], '_not_set' ) === '_not_set' ) {
      add_option( $setting['name'], $setting['default'] ?? '' );
    }
  }
}

// =============================================================================
// SPECIAL SANITIZATION CALLBACKS
// =============================================================================

/**
 * Sanitizes the 'words per minute' setting with fallback
 *
 * @since 4.0.0
 *
 * @param mixed $input  The input value to sanitize.
 *
 * @return int The sanitized integer.
 */

function fictioneer_sanitize_words_per_minute( $input ) {
  return fictioneer_sanitize_integer( $input, 200 );
}

/**
 * Sanitizes integer to be 1 or more
 *
 * @since 4.6.0
 *
 * @param mixed $input  The input value to sanitize.
 *
 * @return int The sanitized integer.
 */

function fictioneer_sanitize_integer_one_up( $input ) {
  return fictioneer_sanitize_integer( $input, 1, 1 );
}

/**
 * Sanitizes a page ID and checks whether it is valid
 *
 * @since 4.6.0
 *
 * @param mixed $input  The page ID to be sanitized.
 *
 * @return int The sanitized page ID or -1 if not a page.
 */

function fictioneer_sanitize_page_id( $input ) {
  return get_post_type( intval( $input ) ) == 'page' ? intval( $input ) : -1;
}

/**
 * Sanitizes with absint() unless it is an empty string
 *
 * @since 5.15.0
 *
 * @param mixed $input  The input value to sanitize.
 *
 * @return mixed The sanitized integer or an empty string.
 */

function fictioneer_sanitize_absint_or_empty_string( $input ) {
  if ( $input === '' ) {
    return '';
  } else {
    return absint( $input );
  }
}

/**
 * Sanitizes the phrase for the cookie consent banner
 *
 * Checks whether the input is a string and has at least 32 characters,
 * otherwise a default is returned. The content is also cleaned of any
 * problematic HTML.
 *
 * @since 4.6.0
 *
 * @param mixed $input  The content for the cookie consent banner.
 *
 * @return string The sanitized content for the cookie consent banner.
 */

function fictioneer_sanitize_phrase_cookie_consent_banner( $input ) {
  global $allowedtags;

  // Setup
  $default = __( 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. Some features are not available without, but you can limit the site to strictly necessary cookies only. See <a href="[[privacy_policy_url]]" target="_blank" tabindex="1">Privacy Policy</a>.', 'fictioneer' );

  // Return default if input is empty
  if ( ! is_string( $input ) ) {
    return $default;
  }

  // Temporarily allow tabindex attribute
  $allowedtags['a']['tabindex'] = [];

  // Apply KSES
  $output = wp_kses( stripslashes_deep( $input ), $allowedtags );

  // Disallow tabindex attribute
  unset( $allowedtags['a']['tabindex'] );

  // Return
  return strlen( $input ) < 32 ? $default : $output;
}

/**
 * Sanitizes the textarea input for Google Fonts links
 *
 * @since 5.10.0
 *
 * @param string $value  The textarea string.
 *
 * @return string The sanitized textarea string.
 */

function fictioneer_sanitize_google_fonts_links( $value ) {
  // Setup
  $value = sanitize_textarea_field( $value );
  $lines = explode( "\n", $value );
  $valid_links = [];

  // Validate and sanitize each line
  foreach ( $lines as $line ) {
    $line = trim( $line );

    if ( fictioneer_validate_google_fonts_link( $line ) ) {
      $sanitized_link = filter_var( $line, FILTER_SANITIZE_URL );

      if ( $sanitized_link !== false ) {
        $valid_links[] = $sanitized_link;
      }
    }
  }

  // Continue saving process
  return implode( "\n", array_unique( $valid_links ) );
}

/**
 * Sanitizes a comma-separated Patreon ID list into a unique array
 *
 * @since 5.15.0
 *
 * @param string $input  The comma-separated list.
 *
 * @return array The comma-separated list turned array (unique items).
 */

function fictioneer_sanitize_global_patreon_tiers( $input ) {
  $ids = fictioneer_sanitize_list_into_array( $input, array( 'absint' => 1, 'unique' => 1 ) );

  // Remove falsy values and return
  return array_filter( $ids );
}

/**
 * Sanitizes a Patreon URL
 *
 * @since 5.15.0
 * @since 5.19.1 - Split up into two functions.
 *
 * @param string $url  The URL entered.
 *
 * @return string The sanitized URL or an empty string if invalid.
 */

function fictioneer_sanitize_patreon_url( $url ) {
  return fictioneer_sanitize_url( $url, null, '#^https://(www\.)?patreon\.#' );
}

// =============================================================================
// SANITIZE OPTION FILTERS
// =============================================================================

add_filter( 'sanitize_option_fictioneer_user_profile_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_user_profile_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_bookmarks_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_bookmarks_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_stories_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_stories_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_chapters_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_chapters_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_recommendations_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_recommendations_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_collections_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_collections_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_bookshelf_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_bookshelf_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_404_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_404_page_link', $link, true );

  return $new_value;
}, 99);

add_filter( 'sanitize_option_fictioneer_authors_page', function( $new_value ) {
  $link = get_permalink( $new_value );
  update_option( 'fictioneer_authors_page_link', $link, true );

  return $new_value;
}, 99);

// =============================================================================
// UPDATED OPTION ACTIONS
// =============================================================================

/**
 * Append missing 'fictioneer_story_hidden' if extended meta queries are disabled
 *
 * @since 5.9.4
 *
 * @param mixed $old_value  The value before the update.
 * @param mixed $value      The new value.
 */

function fictioneer_update_option_disable_extended_story_list_meta_queries( $old_value, $value ) {
  if ( $value && $old_value !== $value ) {
    // Append 'fictioneer_story_hidden'
    fictioneer_append_meta_fields( 'fcn_story', 'fictioneer_story_hidden', 0 );

    // Purge cache Transients
    fictioneer_delete_transients_like( 'fictioneer_' );
  }
}
add_action(
  'update_option_fictioneer_disable_extended_story_list_meta_queries', 'fictioneer_update_option_disable_extended_story_list_meta_queries',
  10,
  2
);

/**
 * Append missing 'fictioneer_chapter_hidden' if extended meta queries are disabled
 *
 * @since 5.9.4
 *
 * @param mixed $old_value  The value before the update.
 * @param mixed $value      The new value.
 */

function fictioneer_update_option_disable_extended_chapter_list_meta_queries( $old_value, $value ) {
  if ( $value && $old_value !== $value ) {
    // Append 'fictioneer_chapter_hidden'
    fictioneer_append_meta_fields( 'fcn_chapter', 'fictioneer_chapter_hidden', 0 );

    // Purge cache Transients
    fictioneer_delete_transients_like( 'fictioneer_' );
  }
}
add_action(
  'update_option_fictioneer_disable_extended_chapter_list_meta_queries', 'fictioneer_update_option_disable_extended_chapter_list_meta_queries',
  10,
  2
);

/**
 * Rebuilds bundled fonts after Google Fonts links update
 *
 * @since 5.10.0
 *
 * @param mixed $old_value  The value before the update.
 * @param mixed $value      The new value.
 */

function fictioneer_updated_google_fonts( $old_value, $value ) {
  fictioneer_build_bundled_fonts();
}
add_action( 'update_option_fictioneer_google_fonts_links', 'fictioneer_updated_google_fonts', 10, 2 );
