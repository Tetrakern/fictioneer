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
      'group' => 'fictioneer-settings-general-group',
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
    'fictioneer_disable_telegram_share' => array(
      'name' => 'fictioneer_disable_telegram_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_whatsapp_share' => array(
      'name' => 'fictioneer_disable_whatsapp_share',
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
    'fictioneer_remove_wp_svg_filters' => array(
      'name' => 'fictioneer_remove_wp_svg_filters',
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
      'default' => 0
    ),
    'fictioneer_limit_chapter_stories_by_author' => array(
      'name' => 'fictioneer_limit_chapter_stories_by_author',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'default' => 0
    ),
    'fictioneer_disable_all_widgets' => array(
      'name' => 'fictioneer_disable_all_widgets',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_disable_widget_checkbox',
      'default' => 0
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
      'label' => __( 'Hide password form on Patreon-gated posts', 'fictioneer' ),
      'default' => 0
    )
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
    )
  ),
  'floats' => array(
    'fictioneer_word_count_multiplier' => array(
      'name' => 'fictioneer_word_count_multiplier',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_float_field',
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
      'group' => 'fictioneer-settings-general-group',
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
    )
  )
));

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
 * @see fictioneer_sanitize_integer()
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
 * @see fictioneer_sanitize_integer()
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
 * @see wp_kses_post()
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
 * Sanitizes a the checkbox and toggles "autoload" for all widgets
 *
 * @since 5.5.3
 * @link https://www.php.net/manual/en/function.filter-var.php
 *
 * @param mixed $value  The checkbox value to be sanitized.
 *
 * @return boolean True or false.
 */

function fictioneer_sanitize_disable_widget_checkbox( $value ) {
  global $wpdb;

  // Setup
  $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
  $autoload = $value ? 'no' : 'yes';

  // Toggle autoload for widget options
  $wpdb->query(
    $wpdb->prepare(
      "UPDATE {$wpdb->prefix}options SET autoload=%s WHERE option_name LIKE %s",
      $autoload,
      $wpdb->esc_like( 'widget_' ) . '%'
    )
  );

  // Return sanitized value for database update
  return filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
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
 *
 * @param string $url  The URL entered.
 *
 * @return array The sanitized URL or an empty string if invalid.
 */

function fictioneer_sanitize_patreon_url( $url ) {
  $url = sanitize_url( $url );
  $url = filter_var( $url, FILTER_VALIDATE_URL ) ? $url : '';
  $url = strpos( $url, 'https://www.patreon.com/') === 0 ? $url : '';

  return $url;
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

?>
