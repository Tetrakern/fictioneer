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
      'label' => __( 'Enable maintenance mode', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_dark_mode_as_default' => array(
      'name' => 'fictioneer_dark_mode_as_default',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable dark mode as default', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_authors' => array(
      'name' => 'fictioneer_show_authors',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Display authors on cards and posts', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_chapter_icons' => array(
      'name' => 'fictioneer_hide_chapter_icons',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide chapter icons', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_story_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_story_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide taxonomies on story cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_chapter_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_chapter_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide taxonomies on chapter cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_recommendation_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_recommendation_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide taxonomies on recommendation cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_collection_cards' => array(
      'name' => 'fictioneer_hide_taxonomies_on_collection_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide taxonomies on collection cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_tags_on_story_cards' => array(
      'name' => 'fictioneer_show_tags_on_story_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Show tags on story cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_tags_on_chapter_cards' => array(
      'name' => 'fictioneer_show_tags_on_chapter_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Show tags on chapter cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_tags_on_recommendation_cards' => array(
      'name' => 'fictioneer_show_tags_on_recommendation_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Show tags on recommendation cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_tags_on_collection_cards' => array(
      'name' => 'fictioneer_show_tags_on_collection_cards',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Show tags on collection cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_taxonomies_on_pages' => array(
      'name' => 'fictioneer_hide_taxonomies_on_pages',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide taxonomies on pages', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_tags_on_pages' => array(
      'name' => 'fictioneer_hide_tags_on_pages',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide tags on pages', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_content_warnings_on_pages' => array(
      'name' => 'fictioneer_hide_content_warnings_on_pages',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide content warnings on pages', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_theme_rss' => array(
      'name' => 'fictioneer_enable_theme_rss',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable theme RSS feeds and buttons', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_oauth' => array(
      'name' => 'fictioneer_enable_oauth',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable OAuth 2.0 authentication', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_lightbox' => array(
      'name' => 'fictioneer_enable_lightbox',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable lightbox', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_bookmarks' => array(
      'name' => 'fictioneer_enable_bookmarks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Bookmarks', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_follows' => array(
      'name' => 'fictioneer_enable_follows',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Follows (requires account)', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_checkmarks' => array(
      'name' => 'fictioneer_enable_checkmarks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Checkmarks (requires account)', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_reminders' => array(
      'name' => 'fictioneer_enable_reminders',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Reminders (requires account)', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_suggestions' => array(
      'name' => 'fictioneer_enable_suggestions',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Suggestions', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_tts' => array(
      'name' => 'fictioneer_enable_tts',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Text-To-Speech (experimental)', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_seo' => array(
      'name' => 'fictioneer_enable_seo',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable SEO features', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_sitemap' => array(
      'name' => 'fictioneer_enable_sitemap',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable theme sitemap generation', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_epubs' => array(
      'name' => 'fictioneer_enable_epubs',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable ePUB converter (experimental)', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_require_js_to_comment' => array(
      'name' => 'fictioneer_require_js_to_comment',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Require JavaScript to comment', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_ajax_comment_submit' => array(
      'name' => 'fictioneer_enable_ajax_comment_submit',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable AJAX comment submission', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_comment_link_limit' => array(
      'name' => 'fictioneer_enable_comment_link_limit',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Limit comments to [n] link(s) or less', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_comment_toolbar' => array(
      'name' => 'fictioneer_enable_comment_toolbar',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable comment toolbar', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_comment_bbcodes' => array(
      'name' => 'fictioneer_disable_comment_bbcodes',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable comment BBCodes', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_ajax_comment_moderation' => array(
      'name' => 'fictioneer_enable_ajax_comment_moderation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable AJAX comment moderation', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_custom_badges' => array(
      'name' => 'fictioneer_enable_custom_badges',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable custom badges', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_patreon_badges' => array(
      'name' => 'fictioneer_enable_patreon_badges',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Patreon badges', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_private_commenting' => array(
      'name' => 'fictioneer_enable_private_commenting',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable private commenting', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_comment_notifications' => array(
      'name' => 'fictioneer_enable_comment_notifications',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable comment reply notifications', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_comment_reporting' => array(
      'name' => 'fictioneer_enable_comment_reporting',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable comment reporting', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_heartbeat' => array(
      'name' => 'fictioneer_disable_heartbeat',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Heartbeat API', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_remove_head_clutter' => array(
      'name' => 'fictioneer_remove_head_clutter',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Remove clutter from HTML head', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_reduce_admin_bar' => array(
      'name' => 'fictioneer_reduce_admin_bar',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Reduce admin bar items', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_bundle_stylesheets' => array(
      'name' => 'fictioneer_bundle_stylesheets',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Bundle CSS files into one', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_bundle_scripts' => array(
      'name' => 'fictioneer_bundle_scripts',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Bundle JavaScript files into one', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_do_not_save_comment_ip' => array(
      'name' => 'fictioneer_do_not_save_comment_ip',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Do not save comment IP addresses', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_logout_redirects_home' => array(
      'name' => 'fictioneer_logout_redirects_home',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Logout redirects Home', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_theme_logout' => array(
      'name' => 'fictioneer_disable_theme_logout',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable theme logout without nonce', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_consent_wrappers' => array(
      'name' => 'fictioneer_consent_wrappers',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Add consent wrappers to embedded content', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_cookie_banner' => array(
      'name' => 'fictioneer_cookie_banner',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable cookie banner and consent function', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_cache_compatibility' => array(
      'name' => 'fictioneer_enable_cache_compatibility',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable cache compatibility mode', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_private_cache_compatibility' => array(
      'name' => 'fictioneer_enable_private_cache_compatibility',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable private cache compatibility mode', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_ajax_comments' => array(
      'name' => 'fictioneer_enable_ajax_comments',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable AJAX comment section', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_comment_callback' => array(
      'name' => 'fictioneer_disable_comment_callback',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable theme comment style (callback)', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_comment_query' => array(
      'name' => 'fictioneer_disable_comment_query',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable theme comment query', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_comment_form' => array(
      'name' => 'fictioneer_disable_comment_form',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable theme comment form', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_comment_pagination' => array(
      'name' => 'fictioneer_disable_comment_pagination',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable theme comment pagination', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_facebook_share' => array(
      'name' => 'fictioneer_disable_facebook_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Facebook share button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_twitter_share' => array(
      'name' => 'fictioneer_disable_twitter_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Twitter share button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_tumblr_share' => array(
      'name' => 'fictioneer_disable_tumblr_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Tumblr share button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_reddit_share' => array(
      'name' => 'fictioneer_disable_reddit_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Reddit share button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_mastodon_share' => array(
      'name' => 'fictioneer_disable_mastodon_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Mastodon share button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_telegram_share' => array(
      'name' => 'fictioneer_disable_telegram_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Telegram share button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_whatsapp_share' => array(
      'name' => 'fictioneer_disable_whatsapp_share',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Whatsapp share button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_delete_theme_options_on_deactivation' => array(
      'name' => 'fictioneer_delete_theme_options_on_deactivation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Delete all settings and theme mods on deactivation', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_remove_wp_svg_filters' => array(
      'name' => 'fictioneer_remove_wp_svg_filters',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Remove global WordPress SVG filters', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_jquery_migrate' => array(
      'name' => 'fictioneer_enable_jquery_migrate',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable jQuery migrate script', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_properties' => array(
      'name' => 'fictioneer_disable_properties',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Fictioneer CSS properties', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_chapter_groups' => array(
      'name' => 'fictioneer_enable_chapter_groups',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable chapter groups', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_chapter_collapsing' => array(
      'name' => 'fictioneer_disable_chapter_collapsing',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable collapsing of chapters', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_collapse_groups_by_default' => array(
      'name' => 'fictioneer_collapse_groups_by_default',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Collapse chapter groups by default', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_public_cache_compatibility' => array(
      'name' => 'fictioneer_enable_public_cache_compatibility',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable public cache compatibility mode', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_full_post_content' => array(
      'name' => 'fictioneer_show_full_post_content',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Display full posts instead of excerpts', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_all_blocks' => array(
      'name' => 'fictioneer_enable_all_blocks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable all Gutenberg blocks', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_ajax_authentication' => array(
      'name' => 'fictioneer_enable_ajax_authentication',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable AJAX user authentication', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_application_passwords' => array(
      'name' => 'fictioneer_disable_application_passwords',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable application passwords', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_user_comment_editing' => array(
      'name' => 'fictioneer_enable_user_comment_editing',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable comment editing', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_ajax_comment_form' => array(
      'name' => 'fictioneer_enable_ajax_comment_form',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable AJAX comment form', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_sticky_comments' => array(
      'name' => 'fictioneer_enable_sticky_comments',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable sticky comments', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_commenting' => array(
      'name' => 'fictioneer_disable_commenting',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable commenting across the site', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_purge_all_caches' => array(
      'name' => 'fictioneer_purge_all_caches',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Purge all caches on content updates', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_theme_search' => array(
      'name' => 'fictioneer_disable_theme_search',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable advanced search', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_contact_forms' => array(
      'name' => 'fictioneer_disable_contact_forms',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable theme contact forms', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_storygraph_api' => array(
      'name' => 'fictioneer_enable_storygraph_api',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Storygraph API', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_restrict_rest_api' => array(
      'name' => 'fictioneer_restrict_rest_api',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Restrict Default REST API', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_chapter_appending' => array(
      'name' => 'fictioneer_enable_chapter_appending',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Append new chapters to story', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_limit_chapter_stories_by_author' => array(
      'name' => 'fictioneer_limit_chapter_stories_by_author',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Restrict chapter stories by author', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_all_widgets' => array(
      'name' => 'fictioneer_disable_all_widgets',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_disable_widget_checkbox',
      'label' => __( 'Disable all widgets', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_see_some_evil' => array(
      'name' => 'fictioneer_see_some_evil',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Monitor posts for suspicious content', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_fast_ajax_comments' => array(
      'name' => 'fictioneer_enable_fast_ajax_comments',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable fast AJAX for comments', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_rate_limits' => array(
      'name' => 'fictioneer_enable_rate_limits',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable rate limiting for AJAX requests', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_advanced_meta_fields' => array(
      'name' => 'fictioneer_enable_advanced_meta_fields',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable advanced meta fields', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_story_changelog' => array(
      'name' => 'fictioneer_show_story_changelog',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Show story changelog button', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_font_awesome' => array(
      'name' => 'fictioneer_disable_font_awesome',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable Font Awesome integration', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_site_age_confirmation' => array(
      'name' => 'fictioneer_enable_site_age_confirmation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable age confirmation modal for site', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_post_age_confirmation' => array(
      'name' => 'fictioneer_enable_post_age_confirmation',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable age confirmation modal for posts', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_extended_story_list_meta_queries' => array(
      'name' => 'fictioneer_disable_extended_story_list_meta_queries',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable extended story list meta queries', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_disable_extended_chapter_list_meta_queries' => array(
      'name' => 'fictioneer_disable_extended_chapter_list_meta_queries',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Disable extended chapter list meta queries', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_count_characters_as_words' => array(
      'name' => 'fictioneer_count_characters_as_words',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Count characters instead of words', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_show_protected_excerpt' => array(
      'name' => 'fictioneer_show_protected_excerpt',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Show excerpt on password-protected posts', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_hide_large_card_chapter_list' => array(
      'name' => 'fictioneer_hide_large_card_chapter_list',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Hide latest chapter list on large story cards', 'fictioneer' ),
      'default' => 0
    ),
    'fictioneer_enable_patreon_locks' => array(
      'name' => 'fictioneer_enable_patreon_locks',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_checkbox',
      'label' => __( 'Enable Patreon tiers to unlock protected posts', 'fictioneer' ),
      'default' => 0
    )
  ),
  'integers' => array(
    'fictioneer_user_profile_page' => array(
      'name' => 'fictioneer_user_profile_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( 'Account page (Template: User Profile) &bull; Do not cache!', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_bookmarks_page' => array(
      'name' => 'fictioneer_bookmarks_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( 'Bookmarks page (Template: Bookmarks)', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_stories_page' => array(
      'name' => 'fictioneer_stories_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( 'Stories page (Template: Stories)', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_chapters_page' => array(
      'name' => 'fictioneer_chapters_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( 'Chapters page (Template: Chapters)', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_recommendations_page' => array(
      'name' => 'fictioneer_recommendations_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( 'Recommendations page (Template: Recommendations)', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_collections_page' => array(
      'name' => 'fictioneer_collections_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( 'Collections page (Template: Collections)', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_bookshelf_page' => array(
      'name' => 'fictioneer_bookshelf_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( 'Bookshelf page (Template: Bookshelf) &bull; Do not cache!', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_404_page' => array(
      'name' => 'fictioneer_404_page',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_page_id',
      'label' => __( '404 page &bull; Add content to your 404 page, make it useful!', 'fictioneer' ),
      'default' => -1
    ),
    'fictioneer_comment_report_threshold' => array(
      'name' => 'fictioneer_comment_report_threshold',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_integer_one_up',
      'label' => __( 'Automatic moderation report threshold.', 'fictioneer' ),
      'default' => 10
    ),
    'fictioneer_comment_link_limit_threshold' => array(
      'name' => 'fictioneer_comment_link_limit_threshold',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_integer_one_up',
      'label' => __( 'Comment link limit.', 'fictioneer' ),
      'default' => 5
    ),
    'fictioneer_words_per_minute' => array(
      'name' => 'fictioneer_words_per_minute',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_words_per_minute',
      'label' => __( 'Calculate reading time with [n] words per minute.', 'fictioneer' ),
      'default' => 200
    ),
    'fictioneer_user_comment_edit_time' => array(
      'name' => 'fictioneer_user_comment_edit_time',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'label' => __( 'Minutes a comment can be edited. -1 for no limit.', 'fictioneer' ),
      'default' => 15
    ),
    'fictioneer_upload_size_limit' => array(
      'name' => 'fictioneer_upload_size_limit',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_integer',
      'label' => __( '<span>Limit file uploads to</span> %s <span>MB or less for user roles with the "Upload Limit" restriction.</span>', 'fictioneer' ),
      'default' => 5
    )
  ),
  'floats' => array(
    'fictioneer_word_count_multiplier' => array(
      'name' => 'fictioneer_word_count_multiplier',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_sanitize_float_field',
      'label' => __( 'Multiplier for displayed word counts.', 'fictioneer' ),
      'default' => 1.0
    )
  ),
  'strings' => array(
    'fictioneer_phrase_maintenance' => array(
      'name' => 'fictioneer_phrase_maintenance',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post',
      'label' => __( 'Maintenance note', 'fictioneer' ),
      'default' => __( 'Website under planned maintenance. Please check back later.', 'fictioneer' ),
      'placeholder' => __( 'Website under planned maintenance. Please check back later.', 'fictioneer' )
    ),
    'fictioneer_phrase_login_modal' => array(
      'name' => 'fictioneer_phrase_login_modal',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post',
      'label' => __( 'Login modal', 'fictioneer' ),
      'default' => __( '<p>Log in with a social media account to set up a profile. You can change your nickname later.</p>', 'fictioneer' ),
      'placeholder' => __( '<p>Log in with a social media account to set up a profile. You can change your nickname later.</p>', 'fictioneer' )
    ),
    'fictioneer_phrase_cookie_consent_banner' => array(
      'name' => 'fictioneer_phrase_cookie_consent_banner',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'fictioneer_validate_phrase_cookie_consent_banner',
      'label' => __( 'Cookie consent banner', 'fictioneer' ),
      'default' => __( 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. Some features are not available without, but you can limit the site to strictly necessary cookies only. See <a href="[[privacy_policy_url]]" target="_blank" tabindex="1">Privacy Policy</a>.', 'fictioneer' ),
      'placeholder' => __( 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. Some features are not available without, but you can limit the site to strictly necessary cookies only. See <a href="[[privacy_policy_url]]" target="_blank" tabindex="1">Privacy Policy</a>.', 'fictioneer' )
    ),
    'fictioneer_phrase_comment_reply_notification' => array(
      'name' => 'fictioneer_phrase_comment_reply_notification',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post',
      'label' => __( 'Comment reply notification email', 'fictioneer' ),
      'default' => __( 'Hello [[comment_name]],<br><br>you have a new reply to your comment in <a href="[[post_url]]">[[post_title]]</a>.<br><br><br><strong>[[reply_name]] replied on [[reply_date]]:</strong><br><br><fieldset>[[reply_content]]</fieldset><br><br><a href="[[unsubscribe_url]]">Unsubscribe from this comment.</a>', 'fictioneer' ),
      'placeholder' => __( 'Hello [[comment_name]],<br><br>you have a new reply to your comment in <a href="[[post_url]]">[[post_title]]</a>.<br><br><br><strong>[[reply_name]] replied on [[reply_date]]:</strong><br><br><fieldset>[[reply_content]]</fieldset><br><br><a href="[[unsubscribe_url]]">Unsubscribe from this comment.</a>', 'fictioneer' )
    ),
    'fictioneer_system_email_address' => array(
      'name' => 'fictioneer_system_email_address',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'fictioneer_validate_email_address',
      'label' => __( 'System email address', 'fictioneer' ),
      'default' => '',
      'placeholder' => 'noreply@example.com'
    ),
    'fictioneer_system_email_name' => array(
      'name' => 'fictioneer_system_email_name',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'System email name', 'fictioneer' ),
      'default' => '',
      'placeholder' => 'Site Name'
    ),
    'fictioneer_patreon_label' => array(
      'name' => 'fictioneer_patreon_label',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Patreon badge label', 'fictioneer' ),
      'default' => '',
      'placeholder' => _x( 'Patron', 'Default Patreon supporter badge label.', 'fictioneer' )
    ),
    'fictioneer_comments_notice' => array(
      'name' => 'fictioneer_comments_notice',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'wp_kses_post',
      'label' => __( 'Notice above comments. Leave empty to hide. HTML allowed.', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_discord_client_id' => array(
      'name' => 'fictioneer_discord_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Discord Client ID', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_discord_client_secret' => array(
      'name' => 'fictioneer_discord_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Discord Client Secret', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_discord_channel_comments_webhook' => array(
      'name' => 'fictioneer_discord_channel_comments_webhook',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Discord comment channel webhook &bull; Shows excerpts of (private) comments!', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_discord_channel_stories_webhook' => array(
      'name' => 'fictioneer_discord_channel_stories_webhook',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Discord story channel webhook', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_discord_channel_chapters_webhook' => array(
      'name' => 'fictioneer_discord_channel_chapters_webhook',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Discord chapter channel webhook', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_discord_invite_link' => array(
      'name' => 'fictioneer_discord_invite_link',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Discord invite link', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_twitch_client_id' => array(
      'name' => 'fictioneer_twitch_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Twitch Client ID', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_twitch_client_secret' => array(
      'name' => 'fictioneer_twitch_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Twitch Client Secret', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_google_client_id' => array(
      'name' => 'fictioneer_google_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Google Client ID', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_google_client_secret' => array(
      'name' => 'fictioneer_google_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Google Client Secret', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_patreon_client_id' => array(
      'name' => 'fictioneer_patreon_client_id',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Patreon Client ID', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_patreon_client_secret' => array(
      'name' => 'fictioneer_patreon_client_secret',
      'group' => 'fictioneer-settings-connections-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Patreon Client Secret', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_subitem_date_format' => array(
      'name' => 'fictioneer_subitem_date_format',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Subitem long date format', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_subitem_short_date_format' => array(
      'name' => 'fictioneer_subitem_short_date_format',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'Subitem short date format', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_contact_email_addresses' => array(
      'name' => 'fictioneer_contact_email_addresses',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_textarea_field',
      'label' => __( 'Contact form receivers (one email address per line)', 'fictioneer' ),
      'default' => '',
      'placeholder' => ''
    ),
    'fictioneer_upload_mime_types' => array(
      'name' => 'fictioneer_upload_mime_types',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_textarea_field',
      'label' => __( 'Comma-separated list of allowed <a href="%s" target="_blank" rel="noreferrer">mime types</a> for user roles with the "Upload Restriction". Must be among the allowed mime type and file extensions of WordPress.', 'fictioneer' ),
      'default' => FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS,
      'placeholder' => FICTIONEER_DEFAULT_UPLOAD_MIME_TYPE_RESTRICTIONS
    ),
    'fictioneer_phrase_site_age_confirmation' => array(
      'name' => 'fictioneer_phrase_site_age_confirmation',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post',
      'label' => __( 'Age confirmation modal content for the site.', 'fictioneer' ),
      'default' => __( 'This website is intended for an adult audience. Please confirm that you are of legal age (18+) or leave the website.', 'fictioneer' ),
      'placeholder' => __( 'This website is intended for an adult audience. Please confirm that you are of legal age (18+) or leave the website.', 'fictioneer' )
    ),
    'fictioneer_phrase_post_age_confirmation' => array(
      'name' => 'fictioneer_phrase_post_age_confirmation',
      'group' => 'fictioneer-settings-phrases-group',
      'sanitize_callback' => 'wp_kses_post',
      'label' => __( 'Age confirmation modal content for posts.', 'fictioneer' ),
      'default' => __( 'This content is intended for an adult audience. Please confirm that you are of legal age (18+) or leave the website.', 'fictioneer' ),
      'placeholder' => __( 'This content is intended for an adult audience. Please confirm that you are of legal age (18+) or leave the website.', 'fictioneer' )
    ),
    'fictioneer_google_fonts_links' => array(
      'name' => 'fictioneer_google_fonts_links',
      'group' => 'fictioneer-settings-fonts-group',
      'sanitize_callback' => 'fictioneer_sanitize_google_fonts_links',
      'label' => __( 'List of Google Fonts links', 'fictioneer' ),
      'default' => '',
      'placeholder' => __( 'https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap', 'fictioneer' )
    ),
    'fictioneer_comment_form_selector' => array(
      'name' => 'fictioneer_comment_form_selector',
      'group' => 'fictioneer-settings-general-group',
      'sanitize_callback' => 'sanitize_text_field',
      'label' => __( 'CSS selector for the comment form. Attempts to make scripts work with comment plugin. Clear theme cache after updating.', 'fictioneer' ),
      'default' => '',
      'placeholder' => '#comment'
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
      add_option( $setting['name'], $setting['default'] );
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
      add_option( $setting['name'], $setting['default'] );
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
      add_option( $setting['name'], $setting['default'] );
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
      add_option( $setting['name'], $setting['default'] );
    }
  }
}

// =============================================================================
// SPECIAL SANITIZATION CALLBACKS
// =============================================================================

/**
 * Validates the 'words per minute' setting with fallback
 *
 * @since 4.0.0
 * @see fictioneer_sanitize_integer()
 *
 * @param int $input  The input value to sanitize.
 *
 * @return int The sanitized integer.
 */

function fictioneer_validate_words_per_minute( $input ) {
  return fictioneer_sanitize_integer( $input, 200 );
}

/**
 * Validates integer to be 1 or more
 *
 * @since 4.6.0
 * @see fictioneer_sanitize_integer()
 *
 * @param int $input  The input value to sanitize.
 *
 * @return int The sanitized integer.
 */

function fictioneer_validate_integer_one_up( $input ) {
  return fictioneer_sanitize_integer( $input, 1, 1 );
}

/**
 * Checks whether an ID is a valid page ID
 *
 * @since 4.6.0
 *
 * @param int $input  The page ID to be sanitized.
 *
 * @return int The sanitized page ID or -1 if not a page.
 */

function fictioneer_validate_page_id( $input ) {
  return get_post_type( intval( $input ) ) == 'page' ? intval( $input ) : -1;
}

/**
 * Validates an email address
 *
 * @since 4.6.0
 * @see sanitize_email()
 *
 * @param int $input  The email address to be sanitized.
 *
 * @return string The email address if valid or an empty string if not.
 */

function fictioneer_validate_email_address( $input ) {
  $email = sanitize_email( $input );
  return $email ? $email : '';
}

/**
 * Validates the phrase for the cookie consent banner
 *
 * Checks whether the input is a string and has at least 32 characters,
 * otherwise a default is returned. The content is also cleaned of any
 * problematic HTML.
 *
 * @since 4.6.0
 * @see wp_kses_post()
 *
 * @param int $input  The content for the cookie consent banner.
 *
 * @return string The sanitized content for the cookie consent banner.
 */

function fictioneer_validate_phrase_cookie_consent_banner( $input ) {
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
 * @param string|boolean $value  The checkbox value to be sanitized.
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
