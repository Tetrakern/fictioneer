# Installation

This guide is mainly written for people who never had their own WordPress site before and may not have the skills to figure this out by themselves. Feel free to skip ahead. That being said, there are still some parts of interest for veterans in regards to the theme.

Click the outline toggle in the top-right corner to see the table of contents.

## Choosing a Host

First, you need to choose a host for your site, the place where your site "lives". This may very well be the hardest part, because bad choices are annoying to fix and cost money. For that matter, you are encouraged to do your own research — [Online Media Masters](https://onlinemediamasters.com/) is a good place to start. If you feel completely lost, asking for help is entirely justified.

Your choice will ultimately come down to two schools: managed hosting or not. Managed hosting takes away the burden of having to, well, manage your server. Configuration, maintenance, security, and performance issues are covered by the provider — at a price. For example, on [WordPress.com](https://wordpress.com/pricing/) you would need at least the Business plan to use the Fictioneer theme. Non-managed hosting is more affordable and less restrictive, but you need a bit of technical know-how or someone helping you.

If the hosting cost are too much for you alone, there is also the option to share a site with other authors and split the bill. Typically with the administrator holding the contract. Just make sure you trust everyone and write down the obligations and rights of all participants involved. Always prepare for the fallout.

## Installing WordPress

The installation process for WordPress is [documented on the official site](https://wordpress.org/support/article/how-to-install-wordpress/) and in many guides only a quick search away. Nowadays, most hosts offer a one-click installation service as well. Note that the latter often comes with pre-installed plugins that you may want to get rid of, especially analytics plugins which tend to violate data privacy laws.

Fictioneer is best used on a fresh install due to its complexity and possible conflicts with existing plugins or customizations. Which does not mean you cannot switch or migrate, but it would be an ordeal. For example, Fictioneer has custom post types for stories and chapters, so you would need to either [convert existing posts](https://wordpress.org/plugins/post-type-switcher/) or upload them anew (which would disassociate all comments). They also have several additional settings, making automatic conversion scripts risky. Depending on how many posts you have, this may take a while.

### Configuring WordPress

Everything installed? Head to **[Settings](https://wordpress.org/support/article/administration-screens/#general)** in the admin panel to configure your site. You can follow a guide, but this should all be fairly obvious. For the purpose of working with the theme, you are most interested in the **Reading**, **Discussion**, and **Permalinks** submenus.

* **Reading:** If you want a static page like in the demo, you can set this here. Of course, you need to [create the pages](https://wordpress.org/support/article/pages/) for blog and front page first. Best use the "No Title Page" or "Story Page" (for single-story sites) template. Keep the number of blog posts and feed items somewhere between 8 and 20.

* **Discussion:** Most of this is up to you, but the number and order of comments does not necessarily behave as you would expect. Comments are always nested in the theme, regardless of the checkbox, but the depth is honored and should be anywhere up to 5. Break comments into pages with 8 to 50 comments each, the first page being displayed by default, and newer comments at the top. The theme really does not work well with anything else but you are welcome to try.

    * **Disallowed Comment Keys:** For simple yet reliable comment spam protection, you are advised to use the [compiled disallow list by slorp](https://github.com/splorp/wordpress-comment-blacklist). Just copy the content of the blacklist into the [Disallowed Comment Key](https://wordpress.org/support/article/comment-moderation/#comment-blocking) field. Check your comment trash occasionally as this can lead to false positives. You can search for less restrictive lists too.

* **Permalinks:** You want the permalink structure set to "Post name". As an off-note, whenever some pages do not show up even though they clearly should, come back here and save to update the permalink structure. You would be surprised how many issues that solves, including the OAuth authentication not working.

* **Open Graph Default Image:** Only used when you enable the SEO features and no (known) SEO plugin is running. This image will be shown in search engine results and social media embeds if no other image is provided by individual posts, such as story cover images. Can be set under **Appearance > Customize > Site Identity**.

* **Author websites:** Technically not a required setting, but authors may want to fill out the website field in their profile. These are added as Open Graph author meta tags used by search engines and social media embeds. If left blank, the generated author page of the site will be used instead, which might be what you want anyway.

### Securing WordPress & Browser Caching

You can greatly improve your site security and performance by adding policies to the **.htaccess** file located in the WordPress root directory. Managed hosting plans normally do this for you (if you ask). Make a backup and add the following lines anywhere before `# BEGIN WordPress` or after `# END WordPress`. If something goes wrong wrong, just remove everything again or restore the backup. You can also use a (cache) plugin to do it for you. This is just the basics, far more is possible, but please refer to a proper guide.

  <details>
    <summary>Example Policies</summary><br>

```
# === BEGIN FICTIONEER ===

# Disable directory browsing
Options -Indexes

# Deny POST requests using HTTP 1.0
<IfModule mod_rewrite.c>
  RewriteCond %{THE_REQUEST} ^POST(.*)HTTP/(0\.9|1\.0)$ [NC]
  RewriteRule .* - [F,L]
</IfModule>

# protect wp-config.php
<files wp-config.php>
  order allow,deny
  deny from all
</files>

# Security policies
<ifModule mod_headers.c>
  Header set Strict-Transport-Security "max-age=31536000" env=HTTPS
  Header set X-XSS-Protection "1; mode=block"
  Header set X-Content-Type-Options nosniff
  Header set X-Frame-Options SAMEORIGIN
  Header set Referrer-Policy: strict-origin-when-cross-origin
  Header set Cross-Origin-Opener-Policy "same-origin"
  Header set Cross-Origin-Resource-Policy "same-site"
  Header set Cross-Origin-Embedder-Policy "require-corp; report-to='default'"
  Header unset X-Powered-By
</ifModule>

# Add file types
AddType application/x-font-ttf .ttf
AddType application/x-font-opentype .otf
AddType application/x-font-woff .woff
AddType application/x-font-woff2 .woff2
AddType image/svg+xml .svg

# Enable compression
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/xml
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE application/json
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/x-font-truetype
  AddOutputFilterByType DEFLATE application/x-font-ttf
  AddOutputFilterByType DEFLATE font/opentype
  AddOutputFilterByType DEFLATE font/otf
  AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>

# Browser cache policies
<IfModule mod_expires.c>
  ExpiresActive on

  # Icons
  ExpiresByType image/x-icon "access plus 1 year"
  ExpiresByType image/vnd.microsoft.icon "access plus 30 days"

  # Images
  ExpiresByType image/jpg "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/avif "access plus 1 year"
  ExpiresByType image/tiff "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"

  # Audio/Video
  ExpiresByType audio/ogg "access plus 1 year"
  ExpiresByType audio/mpeg "access plus 1 year"
  ExpiresByType audio/flac "access plus 1 year"
  ExpiresByType audio/mp3 "access plus 1 year"
  ExpiresByType video/ogg "access plus 1 year"
  ExpiresByType video/mp4 "access plus 1 year"
  ExpiresByType video/webm "access plus 1 year"
  ExpiresByType video/mpeg "access plus 1 year"
  ExpiresByType video/quicktime "access plus 1 year"

  # CSS/JS
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType text/javascript "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
  ExpiresByType application/x-javascript "access plus 1 month"

  # Fonts
  ExpiresByType application/x-font-ttf "access plus 1 year"
  ExpiresByType application/x-font-woff "access plus 1 year"
  ExpiresByType application/x-font-woff2 "access plus 1 year"
  ExpiresByType application/x-font-opentype "access plus 1 year"

  # Others
  ExpiresByType application/pdf "access plus 1 day"
  ExpiresByType application/epub+zip "access plus 1 day"
  ExpiresByType application/x-mobipocket-ebook "access plus 1 day"
</IfModule>

# === END FICTIONEER ===
```

  </details>

## Legal Considerations

There is not much to consider aside from the [data privacy](https://wordpress.com/go/website-building/how-to-write-and-add-a-privacy-policy-to-your-wordpress-site/) issue, which depends on your country of residence and where your host server is located. However, to preempt any legal trouble, you want to assume the strictest laws apply — the [GDPR](https://en.wikipedia.org/wiki/General_Data_Protection_Regulation) and [CCPA](https://en.wikipedia.org/wiki/California_Consumer_Privacy_Act). Fictioneer is compliant with both unless you change things, but you also need to add a [Privacy Policy](PRIVACY.md). And forget about Google Analytics or Fonts.

## How to Install/Update the Fictioneer Theme

![Upload Theme Screen](repo/assets/appearance_upload_theme.jpg?raw=true)

**ATTENTION! Always download the zip file from the [latest stable release](https://github.com/Tetrakern/fictioneer/releases); NOT the source code and NOT via GitHub’s green "Code" button — unless you are a developer and you know what you are doing. Ensure that the extracted directory is named "fictioneer".**

After you have set up your WordPress site, you can install the theme. Since Fictioneer is not available in the official theme library, you need to do this manually. Either by uploading the *unpacked* theme files into the `/wp-content/themes/fictioneer` directory via FTP or by uploading the `.zip` file in the admin panel under **Appearance > Themes > Add New > Upload Theme**.

When you are done, activate the theme under **Appearance > Themes**. If you want to use a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/), which is installed the same way, activate that instead (you need both the main and the child theme). Head to the newly added Fictioneer menu page in the admin sidebar afterwards. Here you need to [configure](#How-to-configure-the-fictioneer-theme) the theme. You may also want to [customize](#How-to-customize-the-fictioneer-theme) the look.

### Updating the Theme

Updating the theme works the same as installing the theme. If done in the admin panel, you will be warned that the theme is already installed and given a quick comparison, prompting you to confirm the overwrite. Make sure you still fulfill all the requirements, namely your WordPress and PHP versions. You can find this information in the info tab on the [site health screen](https://wordpress.org/support/article/site-health-screen/).

Note that any changes made to the theme files will be undone — which you should not have done in the first place. Always use a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) for modifications to avoid this issue. Your theme options and Customizer settings are preserved, however.

### Issue: Jetpack Boost

Jetpack is a WordPress plugin that is sometimes forced upon users. [Jetpack Boost](https://jetpack.com/support/jetpack-boost/) is an optional feature of the plugin that is occasionally auto-enabled and is intended to make your site faster. However, it often causes issues by breaking the site because it concatenates scripts and styles without considering the consequences. This problem can also occur with other overzealous "optimization" plugins.

To resolve this, you have two options: either turn off Jetpack Boost or add the theme scripts to the exclusion list. Some of them may work when concatenated, but they were not tested.

```
fictioneer-dynamic-scripts, fictioneer-application-scripts, fictioneer-lightbox, fictioneer-mobile-menu-scripts, fictioneer-consent-scripts, fictioneer-chapter-scripts, fictioneer-dmp, fictioneer-tts-scripts, fictioneer-story-scripts, fictioneer-user-scripts, fictioneer-user-profile-scripts, fictioneer-bookmarks-scripts, fictioneer-follows-scripts, fictioneer-checkmarks-scripts, fictioneer-reminders-scripts, fictioneer-comments-scripts, fictioneer-ajax-comments-scripts, fictioneer-ajax-bookshelf-scripts, fictioneer-dev-scripts, fcnen-frontend-scripts, fcnmm-script, fictioneer-child-script
```

### Optional: Additional Plugins

The [plugin ecosystem](https://wordpress.org/plugins/) of WordPress is vast and often confusing. There are plugins for almost everything, in variants, free or premium or "freemium". You often find articles about "must-have" plugins — you are well advised to question those. Too many plugins can slow down your site, open vulnerabilities, or conflict with the theme. Fictioneer is designed as standalone solution and technically works without additional plugins. However, nothing is ever complete, so here are a few plugins of note anyway.

* [Autoptimize](https://wordpress.org/plugins/autoptimize/): Optimization plugin to speed up your site. Best used for its aggregation and deferment of static resources, such as styles and scripts, solving browser cache issues along the way. The other options are nice if not already covered elsewhere.

  <details>
    <summary>Example settings</summary><br>
    <blockquote>
      Assume missing options are off, empty, or left to default.<br><br>
      <strong>[JS, CSS & HTML] JavaScript Options:</strong>
      <ul>
        <li>- [x] Optimize JavaScript code?</li>
        <li>- [x] Aggregate JS-files?</li>
      </ul><br>
      <strong>[JS, CSS & HTML] CSS Options:</strong>
      <ul>
        <li>- [x] Optimize CSS Code?</li>
        <li>- [x] Aggregate CSS-files?</li>
        <li>- [x] Generate data: URIs for images?</li>
      </ul><br>
      <strong>[JS, CSS & HTML] Misc Options:</strong>
      <ul>
        <li>- [x] Save aggregated script/css as static files?</li>
        <li>- [x] Enable 404 fallbacks?</li>
        <li>- [x] Also optimize for logged in editors/administrators?</li>
        <li>- [x] Disable extra compatibility logic?</li>
      </ul><br>
      <strong>[Extra] Extra Auto-Optimizations:</strong>
      <ul>
        <li>- [x] Google Fonts: Leave as is</li>
        <li>- [x] Remove emojis</li>
      </ul>
    </blockquote>
  </details>

* [Cloudflare](https://wordpress.org/plugins/cloudflare/): Global content delivery network designed to make your site secure, private, fast, and reliable. Can be used for caching or to enhance a cache plugin further. Unfortunately, the setup is not trivial and you should refer to specific guides or ask for help.

  <details>
    <summary>Cache considerations</summary><br>
    <p>Cloudflare can be problematic if you want to capitalize on the "Cache Everything" option because without a paid plan, you cannot make exceptions for logged-in users. This means visitors might see personalized content of the first user to populate the cache — not good! Imagine your account details being leaked like that. It also does not easily cooperate with on-site caching solutions.</p>
    <p>That being said, the free tier can be persuaded! Ditch the official plugin and install <a href="https://wordpress.org/plugins/wp-cloudflare-page-cache/">Super Page Cache for Cloudflare</a> instead. Same as before, refer to proper guides. Make sure you have the following settings and be prepared that it might still not work! Test this!</p>
    <blockquote>
      <strong>[Plugin: Cache] Don't cache the following dynamic contents:</strong>
      <ul>
        <li>- [x] Page 404 (is_404)</li>
        <li>- [x] Feeds (is_feed)</li>
        <li>- [x] Search Pages (is_search)</li>
        <li>- [x] Ajax Requests</li>
        <li>- [x] WP JSON endpoints</li>
      </ul><br>
      <strong>[Plugin: Cache] Prevent the following URIs to be cached:</strong><br>
      &numsp;Append to defaults. The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).<br>
      &numsp;<code>/oauth2*</code><br>
      &numsp;<code>/download-epub*</code><br>
      &numsp;<code>/account*</code><br>
      &numsp;<code>/bookshelf*</code><br>
      &numsp;<code>/*commentcode=*</code><br><br>
      <strong>[Fictioneer: General] Page Assignments:</strong>
      <ul>
        <li>- [ ] Account: None (default dashboard profile)</li>
        <li>- [x] Bookshelf: Page with the Bookshelf AJAX template (if you need this)</li>
      </ul><br>
      <strong>[Fictioneer: General] Comments:</strong>
      <ul>
        <li>- [x] Enable AJAX comment submission</li>
      </ul><br>
      <strong>[Fictioneer: General] Security & Privacy:</strong>
      <ul>
        <li>- [ ] Block admin panel access for subscribers (OFF)</li>
      </ul><br>
      <strong>[Fictioneer: General] Compatibility:</strong>
      <ul>
        <li>- [x] Enable cache compatibility mode</li>
        <li>- [x] Enable AJAX user authentication</li>
        <li>- [x] Enable AJAX comment form (best performance) ... or ... comment section (best compatibility)</li>
      </ul>
    </blockquote>
    <p>Optional: Install <a href="https://wordpress.org/plugins/wp-super-cache/">WP Super Cache</a> with the extreme settings. Cannot wrongly cache dynamic pages if there are none!</p>
  </details>

* [WPS Limit Login](https://wordpress.org/plugins/wps-limit-login/): Protects you from brute-force attacks by limiting the number of login attempts within a certain period of time. The sibling plugin [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/) moves the whole login page to a new URL, if you want to go one step further.

  <details>
    <summary>User authentication</summary><br>
    <p>Fictioneer does not have a frontend login form and the login page is not recommended for subscribers, so hiding it serves as additional security layer. Note that the optional OAuth 2.0 authentication system via Discord, Google, etc. is not affected by these plugins.</p>
  </details>

* [Sucuri Security - Auditing, Malware Scanner and Hardening](https://wordpress.org/plugins/sucuri-scanner/): The free version is meant to complement your security posture and comes with hardening, malware scanner, core file integrity checking, event logging, email alerts for important issues, and more.

  <details>
    <summary>Notes</summary><br>
    <p>There is not much to screw up, but you should refer to a proper guide for your own peace of mind. Because Sucuri has a tendency to be overzealous with scary warnings and until you set up a whitelist, you will see many false positives. Better safe than sorry.</p>
    <blockquote>
      <strong>Typical false positives:</strong><br>
      &numsp;<code>error_log-* (potentially any log from any plugin)</code><br>
      &numsp;<code>.htaccess.bk (generated backup of .htaccess)</code><br>
    </blockquote>
  </details>

* [UpdraftPlus](https://wordpress.org/plugins/updraftplus/): One of the most popular and convenient backup plugins. If your host does not offer backups or you want to stay in control, this is a good choice to keep your site safe in the event of a disaster.

  <details>
    <summary>Why you want backups</summary><br>
    <p>To quote the plugin’s own premonition: "The day may come when you get hacked, when something goes wrong with an update, your server crashes or your hosting company goes bust — without good backups, you lose everything." The free version is perfectly adequate, allowing you to schedule daily backups saved directly to a remote destination of your choice.</p>
  </details>

* [EWWW Image Optimizer](https://wordpress.org/plugins/ewww-image-optimizer/): An optimization plugin to properly scale, compress, and (optionally) convert your images. Large file sizes reduce your website’s speed and search rank. Redundant if you use an image CDN like Cloudinary, but they can work together.

  <details>
    <summary>Example settings</summary><br>
    <p>As a matter of fact, you do not need this kind of plugin at all if you pay a modicum of attention to the images you upload. One of the most common yet easy to fix mistakes is uploading over-sized images. Obviously, if your header image is 20 MB, your loading time will go down the drain. Your site will be even faster without the overhead of this plugin if you just pre-optimize your images.</p>
    <blockquote>
      Follow the initial setup guide, then head to <strong>Settings > EWWW Image Optimizer</strong> to review the settings. Also take a look at the <a href="https://docs.ewww.io/article/4-getting-started">official documentation</a>. Assume missing options are off, empty, or left to default.<br><br>
      <strong>Basic settings:</strong>
      <ul>
        <li>- [x] Stick with free mode for now</li>
        <li>- [x] Remove Metadata</li>
        <li>- [x] Resize Images: 1920|1920 (3840|2160 if you need 4k images)</li>
        <li>- [ ] Add Missing Dimensions (OFF - this can break the layout)</li>
        <li>- [x] Lazy Load: Improves actual and perceived loading time ...</li>
        <li>- [ ] Lazy Load: Automatic Scaling (OFF)</li>
        <li>- [ ] WebP Conversion (OFF - smaller but unreliable in quality)</li>
      </ul>
    </blockquote>
  </details>

* [Plugin Load Filter](https://wordpress.org/plugins/plugin-load-filter/): This plugin allows you to disable other plugins based on specific conditions, such as the post type. This is useful if you have many plugins that you only need for selected pages, helping to avoid degrading your site performance with unnecessary overhead.

### Optional: Caching

Technically just another plugin, but one that will make your site significantly faster. [Caching](https://wordpress.org/support/article/optimization-caching/) saves your posts and pages as static files to be served later instead of rendering them anew on each request. Guests see the same content anyway, so why waste resources? Only logged-in users can have individual content that must not be cached, such as their account profile. Following are a few cache plugins that have proven to work well with the theme. Do this after you configured your site.

**Note:** Caches require to be purged occasionally, especially after you updated the theme, settings, or plugins. Your site might show outdated pages otherwise. With *known* plugins, Fictioneer automatically purges post caches when you publish or edit content. Other cache plugins require some custom code or need to be purged manually. Inconvenient, but workable.

**Cacheable Query Vars:** Most cache plugins automatically exclude pages with query vars (`/?foo=bar`), because they tend to have dynamic content. However, there are some query vars that can be safely cached if the plugin recognizes them as separate URLs: `pg` (page), `tab`, and technically `order` as well. You may have even more.

**Minifying CSS/JS/HTML:** While this can bring a *tiny* performance boost, it also often leads to scripts not working, missing fonts, and display issues. Cloudflare is known to break CSS properties with overzealous minification, LiteSpeed tends to mess up relative file paths in the CSS, and purging presumably "redundant" whitespaces from the HTML can cause gaps between elements or words to disappear. You can try it out, but watch the results and more importantly the console for errors.

**Rule of Thumb:** Is something missing or misplaced, purge the cache! Chapter order wrong? Purge the cache! Collections outdated? Purge the cache! Page flashing red? That’s right, call an exorcist and purge the cache!

* [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/): Made by [Automattic](https://automattic.com/), a main contributor to WordPress the *software* and owner of WordPress.com the *service* (do not confuse them), this free cache plugin is a great choice if you want simple and reliable. It is also completely free.

  <details>
    <summary>Recommended settings</summary><br>
    <p>This is the "safest" advanced setup in the regard that you do not need to mess with server files. Expert mode is a tick faster and not actually complicated, but if the terms ".htaccess" and "mod_rewrite" make you feel queasy, you are perfectly fine with simple mode.</p>
    <blockquote>
      Assume missing options are off, empty, or left to default.<br><br>
      <strong>[Advanced] Caching:</strong>
      <ul>
        <li>- [x] Enable Caching</li>
      </ul><br>
      <strong>[Advanced] Cache Delivery Method:</strong>
      <ul>
        <li>- [x] Simple</li>
      </ul><br>
      <strong>[Advanced] Miscellaneous:</strong>
      <ul>
        <li>- [x] Cache Restrictions: Disable caching for logged in visitors</li>
        <li>- [x] Don’t cache pages with GET parameters.</li>
        <li>- [x] Compress pages so they’re served more quickly to visitors.</li>
        <li>- [x] Cache rebuild. Serve a supercache file to anonymous users while a new file is being generated.</li>
      </ul><br>
      <strong>[Advanced] Advanced:</strong>
      <ul>
        <li>- [x] Extra homepage checks.</li>
        <li>- [x] Only refresh current page when comments made.</li>
        <li>- [x] List the newest cached pages on this page.</li>
      </ul><br>
      <strong>[Advanced] Expiry Time & Garbage Collection:</strong>
      <ul>
        <li>- [x] Cache Timeout: 7200</li>
        <li>- [x] Timer: 600</li>
      </ul><br>
      <strong>[Advanced] Accepted Filenames & Rejected URIs:</strong>
      <ul>
        <li>- [x] Feeds</li>
        <li>- [x] Search Pages</li>
      </ul><br>
      <strong>[Advanced] Rejected URL Strings:</strong><br>
      &numsp;The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).<br>
      &numsp;<code>/oauth2</code><br>
      &numsp;<code>/download-epub</code><br>
      &numsp;<code>/account</code><br>
      &numsp;<code>/bookshelf</code><br>
      &numsp;<code>/wp-json/fictioneer</code>
    </blockquote>
  </details>

  <details>
    <summary>Extreme settings</summary><br>
    <p>This is the most "aggressive" setup meant to carry membership sites on cheaper hosts, e.g. sites with many simultaneous requests by logged-in visitors who would normally not be served supercached files. Generating individual pages in large numbers within a short amount of time can overwhelm a server, leading to timeout errors. An issue you are unlikely to encounter as long as you do not have thousands of daily visitors. But in that case, just extend the recommended settings with the following ones.</p>
    <blockquote>
      Assume missing options are off, empty, or left to default.<br><br>
      <strong>[Advanced] Miscellaneous:</strong>
      <ul>
        <li>- [x] Cache Restrictions: Enable caching for all visitors</li>
        <li>- [x] Make known users anonymous so they’re served supercached static files.</li>
      </ul>
      <hr>
      Great, now your site is broken for logged-in users! Or rather, they are treated like guests and cannot see their personal content or post comments anymore. To resolve this, head to the <a href="#general-tab">Fictioneer general settings</a> and activate the following options. Clear the cache afterwards. Yes, the admin bar is now gone. Yes, you can still get into the admin with the <code>…/wp-admin</code> link. No, password protected posts no longer work.<br><br>
      <strong>[General] Page Assignments:</strong>
      <ul>
        <li>- [ ] Account: None (default dashboard profile)</li>
        <li>- [x] Bookshelf: Page with the Bookshelf AJAX template (if you need this)</li>
      </ul><br>
      <strong>[General] Security & Privacy:</strong>
      <ul>
        <li>- [ ] Block admin panel access for subscribers (OFF)</li>
      </ul><br>
      <strong>[General] Compatibility:</strong>
      <ul>
        <li>- [x] Enable public cache compatibility mode</li>
        <li>- [x] Enable AJAX user authentication</li>
        <li>- [x] Enable AJAX comment form (best performance) ... or ... comment section (best compatibility)</li>
      </ul>
    </blockquote>
  </details>

* [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/): Comprehensive suite of caching and performance features with great compatibility regardless of host. But a rather involved setup and requires a subscription to make the most of it. Please refer to a guide for installation.

  <details>
    <summary>Cache exceptions</summary><br>
    <p>As long as you only serve cached pages to unauthenticated users, you can hardly do wrong. To make absolutely sure everything works, please add the following exceptions under <strong>Performance > Page Cache</strong>.</p>
    <blockquote>
      <strong>[Page Cache] Never cache the following pages:</strong><br>
      &numsp;The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).<br>
      &numsp;<code>/oauth2*</code><br>
      &numsp;<code>/download-epub*</code><br>
      &numsp;<code>/account*</code><br>
      &numsp;<code>/bookshelf*</code><br>
      &numsp;<code>/wp-json/fictioneer</code>
    </blockquote>
  </details>

* [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/): The most powerful of the listed cache plugins and also completely free — if you can get it running. As server-side cache, your host must support [LiteSpeed](https://docs.litespeedtech.com/lscache/), which is usually a prominent selling point so you would know.

  <details>
    <summary>Example settings</summary><br>
    <p>LiteSpeed Cache offers you far more than what is covered here, so please refer to more comprehensive guides if you want to take advantage of that. However, combined with the other recommended plugins, you can do without.</p>
    <blockquote>
      Assume missing options are off, empty, or left to default.<br><br>
      <strong>[1 - Cache] Cache Control Settings:</strong>
      <ul>
        <li>- [x] Enable Cache</li>
        <li>- [ ] Cache Logged-in Users (OFF)</li>
        <li>- [ ] Cache Commenters (OFF)</li>
        <li>- [x] Cache REST API</li>
        <li>- [x] Cache Login Page</li>
        <li>- [x] Cache favicon.ico</li>
        <li>- [x] Cache PHP Resources</li>
        <li>- [ ] Cache Mobile (OFF)</li>
      </ul><br>
      <strong>[2 - TTL] TTL:</strong>
      <ul>
        <li>- [x] Default Public Cache TTL: 28800</li>
        <li>- [x] Default Private Cache TTL: 1800</li>
        <li>- [x] Default Front Page TTL: 604800</li>
        <li>- [x] Default Feed TTL: 604800</li>
        <li>- [x] Default REST TTL: 28800</li>
      </ul><br>
      <strong>[3 - Purge] Purge Settings:</strong>
      <ul>
        <li>- [x] Purge All On Upgrade</li>
        <li>- [x] Auto Purge Rules For Publish/Update: All pages</li>
        <li>- [ ] Serve Stale (OFF)</li>
      </ul><br>
      <strong>[4 - Excludes] Do Not Cache URIs:</strong><br>
      &numsp;The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).<br>
      &numsp;<code>/oauth2</code><br>
      &numsp;<code>/download-epub</code><br>
      &numsp;<code>/account</code><br>
      &numsp;<code>/bookshelf</code><br>
      &numsp;<code>/wp-json/fictioneer</code><br><br>
      <strong>[4 - Excludes] Do Not Cache Query Strings:</strong><br>
      &numsp;<code>commentcode</code><br><br>
      <strong>[4 - Excludes] Do Not Cache Roles:</strong>
      <ul>
        <li>- [x] Administrator</li>
        <li>- [x] Moderator</li>
        <li>- [x] Editor</li>
        <li>- [x] Author</li>
      </ul><br>
      <strong>[5 - ESI] ESI Settings:</strong>
      <ul>
        <li>- [x] Enable ESI</li>
        <li>- [x] Cache Admin Bar</li>
        <li>- [x] Cache Comment Form</li>
      </ul><br>
      <strong>[5 - ESI] ESI Nonces:</strong><br>
      &numsp;<code>oauth_nonce</code><br>
      &numsp;<code>fictioneer_nonce</code><br>
      &numsp;<code>fictioneer-ajax-nonce</code><br><br>
      <strong>[5 - ESI] Vary Group:</strong>
      <ul>
        <li>- [x] Administrator: 99</li>
        <li>- [x] Moderator: 50</li>
        <li>- [x] Editor: 40</li>
        <li>- [x] Author: 30</li>
        <li>- [x] Contributor: 20</li>
        <li>- [x] Subscriber: 0</li>
      </ul><br>
      <strong>[7 - Browser] Browser Cache Settings:</strong>
      <ul>
        <li>- [x] Browser Cache</li>
        <li>- [x] Browser Cache TTL: 31557600</li>
      </ul>
    </blockquote>
  </details>

### Recommended: Must-Use Plugins

[Must-Use Plugins](https://wordpress.org/documentation/article/must-use-plugins/) are not installed but have to be copied into the **wp-content/mu-plugins** directory (does not exist by default). They are always loaded, in alphabetical order, and before any other plugin or theme. This behavior can be exploited to boost performance. When you look into the Fictioneer theme directory, you will find an mu-plugins subdirectory with plugin files ready to be copied over.

**Since 5.20.2,** you can add and remove the theme’s mu-plugins quickly under **Fictioneer > Plugins**. It will also show you whether an update is available, but it won’t install it automatically in case you customized the file. Check occasionally.

If problems arise, you can just delete the plugin files.

**[Fictioneer 001 Fast Requests](https://github.com/Tetrakern/fictioneer/tree/main/mu-plugins)** accelerates AJAX and REST requests by disabling non-allow-listed plugins during selected theme actions. Depending on the number of plugins you have installed, this can boost your request performance significantly. However, it will prevent the plugins from working during these requests, although that has no effect on the theme’s default functionality. Be not afraid to edit the file and extend the allow list, it will not be overwritten when you update the theme. Or add your own plugin files. This is one of the best speed optimizations you can make.

**[Fictioneer 002 Elementor Control](https://github.com/Tetrakern/fictioneer/tree/main/mu-plugins)** disables the Elementor plugin on all pages except those with Canvas templates. Elementor unfortunately consumes a significant amount of server resources, so limiting its use to necessary pages is ideal for maintaining a faster site. This assumes you do not need Elementor elsewhere; if you do, this approach won’t work for you. Alternatively, you can use the [Plugin Load Filter](https://wordpress.org/plugins/plugin-load-filter/) plugin for more precise control, though it requires more configuration.

### Warning: SEO Plugins

While search engine optimization plugins such as [Yoast](https://wordpress.org/plugins/wordpress-seo/) and [AIOSEO](https://wordpress.org/plugins/all-in-one-seo-pack/) are usually the way to go, they are not recommended here. Fictioneer already ships with a search engine optimization — not perfect, but tailored to the purpose. Third party plugins do not understand the theme, never mind web fictions. They assume everything to be topic-based articles or products, leading to faulty results unless you teach them and that requires custom code. They also lock essential features behind a subscription that Fictioneer provides for free.

And just to take a step back here and be real: SEO is important. Certainly. Unfortunately. But if you actually try to optimize your *prose* for keywords density, word complexity, sentence and paragraph length, or any other statistical insanity to beseech the great algorithm, you have a poison in your mind.

### Warning: CSS Minification/Combination

The theme’s CSS comes already minified and while additional optimizations such as combining files or filtering out *presumably* unused styles can further improve speed, it can also easily break your layout. This has been proven to be an issue with Cloudflare’s auto-minify feature, for example, which removes whitespaces in `clamp()` functions that are required for them to work. An especially insidious case that you might struggle to pinpoint as it happens during the request, not on your own server.

## How to Configure the Fictioneer Theme

![General Settings Preview](repo/assets/settings_general_preview.jpg?raw=true)

### General Tab

Most of the theme’s configuration is found here, the options being largely self-explanatory. Please note that you will probably not need all the features available, such as Checkmarks or Follows. These are for sites with many authors or stories; publishing a weekly serial is better off saving the server resources. Some changes require you to purge the theme caches after updating under **Fictioneer > Tools**. The more obscure options have a (?) button to show a helper modal.

### Roles Tab

![Roles Settings Preview](repo/assets/settings_roles_preview.jpg?raw=true)

The integrated role manager to add and, edit, and remove roles. Not the most sophisticated compared to dedicated plugins, but it comes with custom capabilities tailored to the theme. Because Fictioneer offers some powerful options and tools you may want to keep away from certain user groups. If the roles have not been properly initialized when you activated the theme, you can do that under the **Tools** tab. For reference, look at the default [WordPress capabilities](https://wordpress.org/documentation/article/roles-and-capabilities/).

**Note:** The "read" capability is what grants access to the admin user profile and dashboard, which is not obvious just going by the name.

<details>
  <summary>New Capabilities</summary><br>

  * **Shortcodes:** Without this capability, shortcodes are stripped when you save a post.
  * **Select Page Template:** You cannot change the page template without this.
  * **Custom Page CSS:** Inject CSS into the header for an unique style. Dangerous!
  * **Custom ePUB CSS:** Inject CSS into the ePUB for an unique style. Dangerous!
  * **Custom ePUB Upload:** Assign custom ePUB file from the media library to the story.
  * **Custom Page Header:** Change the header image for selected pages.
  * **SEO Meta:** Show and edit the SEO meta for posts (if enabled in the settings).
  * **Make Sticky:** You can make posts and stories stick to the top in lists.
  * **Edit Permalink:** Customize the permalink slug derived from the title. Dangerous!
  * **All Blocks:** Your block options are quite limited without this, for *sanity* reasons.
  * **Story Pages:** Allows you to attach up to four pages to your stories as extra tabs.
  * **Edit Date:** Makes it possible to change the publishing date *after* publishing.
  * **Assign Patreon Tiers:** Allows you to set Patreon tiers and pledge thresholds for posts.
  * **Reduced Profile:** Removes clutter from the admin profile page, like the color schemes.
  * **Only Moderate Comments:** Limits moderators to only editing comments, not all posts.
  * **Upload Limit:** Enforce the file size limit from the General Settings.
  * **Upload Restrictions:** Enforce the file type restrictions from the General Settings.
  * **Adminbar Access:** Overrides individual settings to show or hide the adminbar.
  * **Admin Panel Access:** Required to access the admin panel, including your admin profile.
  * **Dashboard Access:** Required if you want to see the dashboard admin page.
  * **Show Badge:** Shows the role name as comment badge. Can be overridden in your profile.
  * **Moderate Post Comments:** Allows you limited comment moderation on your own posts. AJAX only.
  * **Allow Self Delete:** Allows you to delete your own account. Default for subscribers.
  * **Privacy Clearance:** Grants access to sensible data like emails and IP addresses.
  * **Read Others Files:** Allows you to see uploaded files from *other* users.
  * **Edit Others Files:** Allows you to edit uploaded files from *other* users.
  * **Delete Others Files:** Allows you to delete uploaded files from *other* users.
  * **Unlock Posts:** Allows you to unlock selected password-protected posts for users.
  * **Manage {Taxonomy}:** Lets you see the overview list table of the taxonomy.
  * **Assign {Taxonomy}:** Lets you assign the taxonomy to your posts.
  * **Edit {Taxonomy}:** Lets you create and edit taxonomies of this type.
  * **Delete {Taxonomy}:** Lets you delete taxonomies of this type.
  * **Ignore {Post Type} Passwords:** You bypass passwords for this post type.

</details>

### Plugins Tab

![Connections Settings Preview](repo/assets/settings_plugins_preview.jpg?raw=true)

This tab is only visible if theme-related plugins are installed and active. Whether the displayed cards are purely informative or hold functions is up to the plugin author. This does not replace the default plugin page of WordPress.

### Connections Tab

![Connections Settings Preview](repo/assets/settings_connections_preview.jpg?raw=true)

Anything that connects with external service providers goes here, such as the Client ID and Secret for OAuth 2.0 applications. Please refer to respective tutorials on how to set them up and always, *always* keep those credentials confidential.

If you enter a [Discord webhook](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks) here, notifications about new comments, stories, and/or chapters will be sent directly into a channel on your server (leave free if you do not want that). Make sure comments are sent to a hidden moderation channel since it will receive excerpts of private comments. Note that webhooks cease to work if used for more than one app (for security reasons).

* [Discord Developer Portal](https://discord.com/developers/docs/topics/oauth2)
* [Twitch Developer Portal](https://dev.twitch.tv/docs/authentication/register-app)
* [Patreon Developer Portal](https://docs.patreon.com/#oauth)
* [Google Developer Portal](https://developers.google.com/identity/protocols/oauth2)

The OAuth request redirect URI should be akin to `https://your-domain.com/oauth2`, the important part being the `/oauth2` endpoint. Note that the service providers can be picky, such as rejecting an URI that includes "www" if that is not actually part of your website’s address. Use the _exact_ string you see in your browser’s address bar. If the redirect returns 404, you usually need to flush your permalinks under **Settings > Permalinks** (just save).

For more details on Patreon, see [Patreon Integration](#patreon-integration) further down.

### Phrases Tab

![Phrases Settings Preview](repo/assets/settings_phrases_preview.png?raw=true)

Allows for some minor translations and changes, such as the cookie notice banner or comment reply notification email. More customization can be achieved with the theme’s [translation filter](FILTERS.md#apply_filters-fictioneer_filter_translations_static-strings-). But if you want to translate the theme into a new language, you will need to include the proper [translation files](https://developer.wordpress.org/plugins/internationalization/localization/) or use a plugin. You can find a .POT template file in the languages folder.

### Fonts Tab

![Phrases Settings Preview](repo/assets/settings_fonts_preview.jpg?raw=true)

An overview of all installed fonts, with the options to enable/disable them. You can also include Google Fonts here, but be aware that this violates the GDPR. If you want to install custom fonts, take a look at the [Custom Fonts](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#custom-fonts) section further down.

### ePUBs Tab

![ePUBs Settings Preview](repo/assets/settings_epubs_preview.jpg?raw=true)

Lists all generated ePUBs with statistics, download links, and options to delete them. File names are equal to the story’s `post_name`, which is the slug inside the permalink and *not* the title. They are cleaned of any special characters and are also used to query associated stories. If you change the permalink, they will no longer match and a new ePUB will be generated, leaving the old one orphaned. This is not terrible but takes up space.

**Failed ePUBs:** Indicated by an empty "download" file. The generation of ePUBs can fail due to several circumstances, such as missing writing permissions along the path of `wp-content/uploads/epubs` or non-conform content in the story or chapters. Unfortunately, ePUBs are rather picky regarding allowed HTML and while the converter tries to sanitize the content, this is not fail-proof. Alternatively, you can just upload a file yourself instead of relying on the converter, not limited to the ePUB format.

### SEO Tab

![ePUBs Settings Preview](repo/assets/settings_seo_preview.png?raw=true)

Only available if you enable the SEO features and no (known) SEO plugin is running. Lists all generated Open Graph meta data and schemas used by search engines and social media embeds, created and cached when a post is first visited until modified or purged. Note that most page templates (besides list templates) and collections do not have schemas, appearing grayed out.

Whether these services actually display the offered data is entirely up to them. You cannot force Google to show your custom description, for example. After all, you could write *anything* in there. This tab is mostly informative, but you can purge the cached meta data or schemas if that should become necessary.

If you want to set up a default Open Graph image for search engine results and embeds, you can do that in the **Customizer** under **Site Identity**. This image will always be used if there is not a more specific one, like the thumbnail for posts.

### Tools Tab

A collection of actions to add, update, revert, fix, or purge certain items. For example, you can add a proper moderator role if missing or convert tags into genres. Everything is thoroughly explained. But the only action you will most likely need more than once is **Purge Theme Caches**, which should be done whenever you change chapter or story settings.

If the user roles lack permissions, such as authors not being able to add stories and chapters, use the **Initialize Roles** action. This also restores the defaults if you mess something up, although it will not reset capabilities outside the theme’s scope. Most administrative capabilities are left untouched for security reasons.

### Log

A log of administrative actions performed which concern the theme.

## Patreon Integration

You can grant logged-in users access to password-protected content via Patreon membership, either by selected tiers or pledge thresholds or both. This requires you to enable and set up the OAuth 2.0 authentication for Patreon, allowing users to log in with their Patreon account and import their membership data. The official Patreon plugin for WordPress works too, but the integration with the theme is not seamless (different registration process, compatibility with caching and performance impact unknown).

**Fictioneer > General > Feature:**
* Enable OAuth 2.0 authentication
* Enable Patreon content gate

After setting up the [OAuth 2.0 connection](#connections-tab), add a campaign link and import your tiers. This is a unique request limited to administrators and only works for the campaign of your client. No, you cannot have different campaigns for different authors. Changes to tiers on Patreon are **not** automatically synchronized, you have to pull them yourself (but this should rarely be necessary).

Once you are done, you can apply tiers and pledge thresholds in cents (e.g. 350 for $3.50) to individual posts or set them globally. **Posts always use the lowest** requirements if you do both. Note that you still need to set a post password, because this feature only hijacks the WordPress password check. Removing a password will also suspend the Patreon gate. To keep this compatible with cache plugins, the gate is not passed down from stories to chapters.

**Options:**
* **Campaign Link (Global):** Link to your campaign, required for the link button to show up.
* **Gate Message Override (Global):** Replaces the message below the Patreon link on locked posts.
* **Tiers (Post/Global):** Comma-separated list of tier IDs, which you can see after pulling them.
* **Threshold (Post/Global):** Pledge amount in cents (e.g. 350 for $3.50) independent of tiers.
* **Lifetime Threshold (Global):** Use the total of all paid pledges, regardless of current status.
* **Unlock Threshold (Global):** Gate regular user post unlocks behind a pledge amount in cents.
* **Hide Password Forms (Global):** Hides the normal password form on gated posts.

Membership data is valid for one week by default, per user, refreshed whenever they log in with Patreon. They can log in with other accounts in the meantime. This can cause users to retain access rights for longer than their membership status allows (up to six days), which is a consequence of the theme not keeping a continuous connection to Patreon for security reasons — but if you get hacked, their Patreon accounts will be safe in turn. Security is rarely convenient.

You can increase or reduce the expiration time with the `FICTIONEER_PATREON_EXPIRATION_TIME` constant in a child theme, but it should not be less than three days (which is the maximum login time before you are automatically logged out).

**Caching:** If you use a cache plugin, make sure that password-protected posts are not cached or this will not work properly. The LiteSpeed Cache plugin should be fine, but anything else might need additional configuration.

![Patreon Gate Settings](repo/assets/connection_settings_patreon_gate.jpg?raw=true)

## How to Customize the Fictioneer Theme

![Customizer HSL Sliders](repo/assets/customizer_hsl_sliders_demo.gif?raw=true)

There are two ways to customize the theme. The obvious one is the Customizer of WordPress under **Appearance > Customize**. Here you can upload a header image and logo, set a site title, change the color scheme, and modify the layout to some extend. The interface and live preview make this straightforward. If the color options are too demanding (and they are), you may want to stick to the hue, saturation, and lightness sliders. Also consult the many guides about WordPress customization.

The second way is to directly modify the templates, styles, and scripts. This is indefinitely more powerful but requires some developer skills — and you can easily break your site. The theme’s files can be modified under **Appearance > Theme File Editor**, although you should never actually do this. Always create a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) because any code changes you make, regardless of quality, will be overwritten again when you update the theme. You can find a pre-made base child theme [here](https://github.com/Tetrakern/fictioneer-child-theme).

### Demo Layout

As per popular demand, here is a small guide on how to mimic the demo site. Please be aware that the demo is more for showing off features than being a production example. Make sure to look at the available [shortcodes](DOCUMENTATION.md#shortcodes) and their possible configurations. If you are new to WordPress, better read a guide about using the CMS first because the basics are not covered here.

First, create two new pages with the "No Title Page" template, one called "Home" and the other "Posts" (or whatever you like). Then go to **Settings > Reading > Your homepage displays** and set it to ["A static page"](https://wordpress.org/documentation/article/create-a-static-front-page/). Assign the pages you created. Now you can add blocks and shortcodes to your "Home" page; just leave the "Posts" page empty. Similar, you can add list pages for Stories, Chapters, etc. with the corresponding templates and assign them under **Fictioneer > General > Page Assignments**. You do not need all of them.

For simplicity, here is the copied content of the demo home page (minus some site-specific things). Put that into the code editor view and adjust it as needed. When you switch back to the visual editor, everything should be properly formatted as blocks.

<details>
  <summary>Editor content</summary><br>

```html
<!-- wp:shortcode -->
[fictioneer_latest_posts count="1"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:shortcode -->
[fictioneer_article_cards per_page="2" ignore_sticky="1"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Latest Stories</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_stories count="10"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Latest Updates</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_updates count="6"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Latest Chapters</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_chapters count="6"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Latest Recommendations</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_recommendations count="6"]
<!-- /wp:shortcode -->

<!-- wp:heading {"className":"show-if-bookmarks hidden"} -->
<h2 class="wp-block-heading show-if-bookmarks hidden">Bookmarks</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_bookmarks count="10"]
<!-- /wp:shortcode -->
```

</details>

### Header Style

![Customizer HSL Sliders](repo/assets/customizer_header_style_preview.jpg?raw=true)

You can choose between three different header styles: **default**, **top**, and **split** — or **none** at all, if that is what you want. The **default style** is what you see on the screenshots and demo site, optionally with title, tagline, and/or logo. The **top style** puts the site identity above the navigation and removes the header image. And the **split style** is a mix of both, with the identity above but a header image below the navigation.

### CSS Snippets

![Customizer HSL Sliders](repo/assets/developer_tools_preview.jpg?raw=true)

While the customization options are not as extensive as with multi-purpose themes or page builders, you can achieve quite a lot with some simple [CSS](https://developer.mozilla.org/en-US/docs/Web/CSS) snippets. Easy to learn, hard to master. However, following are several snippets you can use and modify to your needs. Just put them into **Customizer > Additional CSS** or a child theme. This is by far the most powerful way of customization — there are over 500 properties and virtually infinite possible values and combinations that can be assigned to each and every element.

**Developer Tools:** Your very best friend! You can open them by right-clicking anywhere on the site and hitting **Inspect**, directly highlighting the element you are on. Hit **\[Option\] + \[⌘\] + \[J\]** (on macOS) or **\[Shift\] + \[CTRL\] + \[J\]** (on Windows/Linux) if you want to use the keyboard. Here you can see the HTML and applied CSS styles; you can even manipulate them to see what happens. There are many tutorials online on how to use the tools, please consult one first if you are new.

In order to target an element with CSS, you first need to find a valid [selector](https://developer.mozilla.org/en-US/docs/Learn/CSS/Building_blocks/Selectors). This is usually a class, one of the whitespace-separated values as shown in the **Elements Inspector** (prepended with a single dot). They are best in terms of [performance](https://developer.mozilla.org/en-US/docs/Learn/Performance/CSS) and compatibility. You can even chain them for more [specificity](https://developer.mozilla.org/en-US/docs/Web/CSS/Specificity). The **Styles Inspector** lists the currently applied properties and values.

#### Dark/Light Mode & Media Queries

Quite often, you need to apply specific styles depending on the theme mode or screen size. Especially colors are a concern here, as some that pop on a light background might vanish on a dark one. Another issue are the constraints imposed by mobile viewports: there is rarely enough space. Luckily, this can be accounted for.

```css
/* Only applied to viewport sizes 768px and up. */
@media only screen and (min-width: 768px) {
  .selector {
    property: value;
  }
}

/* Only applied to viewport sizes 767px and down. */
@media only screen and (max-width: 767px) {
  .selector {
    property: value;
  }
}

/* Always applied. */
.selector {
  property: value;
}

/* Only applied in light mode (chained selector). */
:root[data-mode="light"] .selector {
  property: value;
}

/* Only applied in dark mode (chained selector). */
:root[data-mode="dark"] .selector {
  property: value;
}
```

#### Overwrite Custom Properties

[Custom properties](https://developer.mozilla.org/en-US/docs/Web/CSS/--*), also known as CSS variables, contain values which can be assigned to style properties using the `var()` function. They are scoped to the selector(s) they are declared on, but typically the `:root` to make them available everywhere. Fictioneer makes liberal use of custom properties (see [here](https://github.com/Tetrakern/fictioneer/blob/main/src/scss/common/_properties.scss)) and you can change a lot just by overwriting them. But be careful, they can cause severe performance issues if made dynamic.

```css
/* Make the *sticky* navigation background 10% transparent (always). */
:root {
  --navigation-background-end-opacity: 0.9;
}

/* Make the *sticky* navigation background 10% transparent (only in dark mode). */
:root[data-mode="dark"] {
  --navigation-background-end-opacity: 0.9;
}

/* Make the navigation background always visible. */
:root {
  --navigation-background-start-opacity: 1;
}
```

#### Button Colors

The button colors are based on the background `var(--bg-x)` and foreground `var(--fg-x)` CSS properties, with `x` being the numerical assignment of the shade as seen in the Customizer. This can make them challenging to modify. The reason for the lack of color input options is that there are simply too many properties attached to buttons. However, you can still modify them with custom CSS! Below is the default property setup for the buttons.

```css
/* Dark Mode */
:root, :root[data-theme=base] {
  --button-font-weight: 500;
  --button-box-shadow: none;
  --button-color-active: var(--fg-inverted);
  --button-background-active: var(--bg-100);
  --button-border-active: 1px solid transparent;
  --button-barberpole: var(--bg-500);
  --button-oauth-connected: var(--button-background-active);

  --button-primary-background: var(--bg-400);
  --button-primary-background-hover: var(--bg-300);
  --button-primary-background-disabled: var(--bg-500);
  --button-primary-color: var(--fg-400);
  --button-primary-color-hover: var(--fg-300);
  --button-primary-color-disabled: var(--fg-700);
  --button-primary-filter-disabled: saturate(.7) opacity(.3) brightness(1.4);

  --button-secondary-background: transparent;
  --button-secondary-background-hover: var(--bg-500);
  --button-secondary-background-disabled: repeating-linear-gradient(-45deg, rgb(255 255 255 / 6%), rgb(255 255 255 / 6%) 2px, transparent 2px, transparent 4px);
  --button-secondary-color: var(--fg-600);
  --button-secondary-color-hover: var(--fg-400);
  --button-secondary-color-disabled: var(--fg-800);
  --button-secondary-border: 1px solid var(--bg-300);
  --button-secondary-border-hover: 1px solid var(--bg-200);
  --button-secondary-border-disabled: 1px solid var(--bg-300);

  --button-warning-background: var(--red-500);
  --button-warning-background-hover: var(--red-600);
  --button-warning-color: #fff;
  --button-warning-color-hover: #fff;

  --button-suggestion-color: var(--fg-inverted);
  --button-suggestion-color-hover: var(--fg-inverted);
  --button-suggestion-background: var(--bg-100);
  --button-suggestion-background-hover: var(--bg-50);

  --button-quick-background: var(--bg-500);
  --button-quick-background-hover: var(--bg-300);
  --button-quick-color: var(--fg-600);
  --button-quick-color-hover: var(--fg-400);

  --button-file-block-color: var(--fg-inverted);
  --button-file-block-color-hover: var(--fg-inverted);
  --button-file-block-background: var(--bg-100);
  --button-file-block-background-hover: var(--bg-50);
}

/* Light Mode Overrides */
:root[data-mode=light] {
  --button-color-active: var(--fg-inverted);
  --button-background-active: var(--bg-700);
  --button-barberpole: var(--bg-300);

  --button-primary-background: var(--bg-600);
  --button-primary-background-hover: var(--bg-700);
  --button-primary-background-disabled: var(--bg-400);
  --button-primary-color: var(--fg-inverted);
  --button-primary-color-hover: var(--fg-inverted);
  --button-primary-color-disabled: var(--fg-700);
  --button-primary-filter-disabled: opacity(.6);

  --button-secondary-background-hover: var(--bg-300);
  --button-secondary-background-disabled: repeating-linear-gradient(-45deg, hsl(var(--bg-950-free) / 6%), hsl(var(--bg-950-free) / 6%) 2px, transparent 2px, transparent 4px);
  --button-secondary-border: 1px solid var(--bg-400);
  --button-secondary-border-hover: 1px solid var(--bg-500);
  --button-secondary-border-disabled: 1px solid var(--bg-400);

  --button-suggestion-background: var(--bg-600);
  --button-suggestion-background-hover: var(--bg-700);

  --button-quick-background: var(--bg-600);
  --button-quick-background-hover: var(--bg-700);
  --button-quick-color: var(--fg-inverted);
  --button-quick-color-hover: var(--fg-inverted);

  --button-file-block-color: var(--fg-inverted);
  --button-file-block-color-hover: var(--fg-inverted);
  --button-file-block-background: var(--bg-600);
  --button-file-block-background-hover: var(--bg-700);
}
```

#### Top-Header & Navigation Backgrounds

Assuming you have set the **Header Style** to **top** or **split**, the following snippet makes the navigation background always visible regardless of scroll position and adds a semi-transparent background color to the header. This might come in handy if your site has a background image.

```css
:root {
  --top-header-background: hsl(calc(221deg + var(--hue-rotate)) calc(16% * var(--saturation)) clamp(10%, 20% * var(--darken), 60%) / 70%); /* Example dynamic HSL color with 70% opacity; rgb(43 48 59 / 70%). */
  --navigation-background-start-opacity: 1;
}

.top-header {
  padding-bottom: 1rem; /* Bottom spacing inside the header. */
}

.main-navigation {
  margin-top: 0; /* Close the gab between navigation and header. */
}

/* Applied to two chained selectors; the first one only affects direct descendants (>) */
.main-navigation__list > .menu-item,
.main-navigation .icon-menu__item {
  border-radius: 0 !important; /* The !important enforces the value, even though the selector is too weak */
}
```

#### Background Overlay & Filters

Assuming you have set a background image for your site, this snippet adds an overlay that allows you to tint and filter said image — at a cost of performance. Usually, you are better off using an image already prepared for your needs, but this is one way to apply dynamic adjustments for dark or light mode. A simple semi-transparent color might do, but you can also go nuts with [mix-blend-mode](https://developer.mozilla.org/en-US/docs/Web/CSS/mix-blend-mode) and [backdrop-filter](https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter).

```css
.site {
  background: transparent;
}

/* Example: Semi-transparent black overlay. */
body::before {
  content: "";
  position: fixed;
  inset: 0;
  z-index: -1000;
  display: block;
  background-color: rgb(0 0 0 / 50%);
}

/* Example: Backdrop filters (beware performance impact) */
body::before {
  content: "";
  position: fixed;
  inset: 0;
  z-index: -1000;
  display: block;
  backdrop-filter: blur(3px) sepia(90%) brightness(0.5);
  -webkit-backdrop-filter: blur(3px) sepia(90%) brightness(0.5); /* For Safari. */
}
```

#### Merge Top-Header & Navigation

![Customizer HSL Sliders](repo/assets/merged_header_and_nav_preview.jpg?raw=true)

You want the navigation next to your top-aligned header, without changing the HTML? Hacky, but possible. The actual values and result will depend on the size of your header and number of menu items, as this can lead to overlapping elements if you are not careful. Also, depending on your background, you may need to adjust some colors for both light and dark mode.

**2024/01/23:** Updated the reset on sticky CSS.

**Site Title - Minimum Size:** 40px<br>
**Site Title - Maximum Size:** 40px<br>
**Tagline - Minimum Size:** 12px<br>
**Tagline - Maximum Size:** 16px

```css
@media only screen and (min-width: 1024px) {
  .main-navigation {
    margin-top: calc(-1 * var(--navigation-height) + 5px); /* Adjust to fit your site. */
  }

  .main-navigation__wrapper {
    position: relative;
    align-items: flex-end;
    flex-direction: column-reverse;
  }

  .main-navigation__right {
    position: absolute;
    top: 0;
    right: 0;
    transform: translateY(calc(-100% - 2px)); /* Adjust to fit your site. */
  }

  .main-navigation.is-sticky .main-navigation__left {
    display: flex;
    justify-content: flex-end; /* Use space-between if you add the optional ::before block.  */
    gap: 2rem;
    padding-left: 1rem;
    width: 100%;
    max-width: 100%;
  }

  /* Optional: Add this if you want to show something on the left side when the navigation becomes sticky. */
  .main-navigation.is-sticky .main-navigation__left::before {
    content: "FICTIONEER";
    flex: 0 0 auto;
    display: block;
    font-weight: 700;
    line-height: var(--navigation-height);
  }
}

/* Optional: Add this if you want to reset the changes when the navigation becomes sticky. */
:root:not(.no-nav-sticky) body:not(.scrolled-to-top) .main-navigation.is-sticky .main-navigation__wrapper {
  flex-direction: row;
}

/* Added on 2024/01/23. */
:root:not(.no-nav-sticky) body:not(.scrolled-to-top) .main-navigation.is-sticky .main-navigation__left {
  justify-content: flex-start;
  padding-left: 0;
}

:root:not(.no-nav-sticky) body:not(.scrolled-to-top) .main-navigation.is-sticky .main-navigation__right {
  transform: none;
}
```

#### Overlay Navigation

You want the navigation on top of the header image? Just go to **Appearance > Customize > Layout** and change the Header Style to "Overlay". You may want to adjust the header image, title, tagline, or logo (if any) as well. Additional customizations require some CSS. Note that the following snippets are *examples*; do not mindlessly copy and paste them, adjust them to your needs.

```css
/* Semi-transparent navigation bar. */
.header-style-overlay .main-navigation {
  --navigation-background-start-opacity: .72;
  --navigation-background-end-opacity: .9;
  backdrop-filter: blur(4px); /* Blurs everything behind the bar; can decrease render performance. */
  -webkit-backdrop-filter: blur(4px); /* ... same but works in Safari. */
}

/* Remove shadow when site is scrolled to the top. */
.header-style-overlay .scrolled-to-top {
  --navigation-drop-shadow: none;
}

/* Increase the height on mobile and up. */
:root {
  --navigation-height: 48px;
}

/* Increase the height on desktop and up. */
@media only screen and (min-width: 1024px) {
  :root {
    --navigation-height: 60px;
  }
}

/* Avoid increasing the height of submenus. */
.sub-menu {
  --navigation-height: 40px;
}
```

#### Card Grids

You can change the minimum width of cards and gap spacing under **Appearance > Customize > Layout**, usually in combination with an increased site width. But if you want to have a grid on the list page templates as well, for example Stories and Chapters, you need some custom CSS. Be aware that targeting the `.card-list` class may be convenient to convert all card lists to grids, but can have unintended side effects since the class is used in many places. Better be specific.

```css
/* Targeting the unique IDs of the lists is safe. */
#list-of-stories, #list-of-chapters {
  --card-list-row-gap: max(4cqw, 2rem); /* Larger by default than shortcode grids! */
  --card-list-col-gap: max(4cqw, 2rem); /* Larger by default than shortcode grids! */
  --card-list-template-columns: repeat(auto-fill, minmax(308px, 1fr));
}

/* Fallback for older browsers that do not support container queries. */
@supports (width: 1cqw) {
  #list-of-stories, #list-of-chapters {
    --card-list-row-gap: 2rem;
    --card-list-col-gap: 2rem;
  }
}
```

#### Custom Header/Page Style

Both the header and page style can be set to "Custom CSS", but you will notice there is no interface appearing. That’s because the CSS is supposed to be in the **Additional CSS** section. The option only applies the necessary root classes for the styles to work in the first place. There are many ways to modify the shape of a container, but it usually boils down to a [polygon](https://developer.mozilla.org/en-US/docs/Web/CSS/basic-shape/polygon), [mask image](https://developer.mozilla.org/en-US/docs/Web/CSS/mask-image), or both. Note that this is **not** easy.

A good starting point for masks is [haikai](https://app.haikei.app/), but add `preserveAspectRatio="none"` after the viewBox or the SVG won’t stretch properly. Make sure that your style looks good on both desktop and mobile viewports, which can be difficult to achieve. The following examples use [clamp()](https://developer.mozilla.org/en-US/docs/Web/CSS/clamp) and [calc()](https://developer.mozilla.org/en-US/docs/Web/CSS/calc) functions, but media queries or a fits-all approach work too.

```css
/* Example: Wave page style */

:root.page-style-mask-image-wave-a:not(.minimal) .main__background {
  filter: var(--page-drop-shadow);
}

:root.page-style-mask-image-wave-a:not(.minimal) .main__background::before {
  --mp: top calc(-1 * clamp(5px, 0.7633587786vw + 2.1374045802px, 8px)) left 0, top clamp(22px, 2.2900763359vw + 13.4122137405px, 31px) left 0; /* mask-position */
  --ms: 100% clamp(28px, 3.3078880407vw + 15.5954198473px, 41px), 100% calc(100% - clamp(22px, 2.2900763359vw + 13.4122137405px, 31px)); /* mask-size */
  --mr: repeat-x, no-repeat; /* mask-repeat */
  --mi: url('data:image/svg+xml,%3Csvg width="100%25" height="100%25" id="svg" viewBox="0 0 1440 690" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" class="transition duration-300 ease-in-out delay-150"%3E%3Cpath d="M 0,700 L 0,262 C 85.6267942583732,192.2488038277512 171.2535885167464,122.49760765550238 260,138 C 348.7464114832536,153.50239234449762 440.6124401913876,254.25837320574163 541,315 C 641.3875598086124,375.74162679425837 750.2966507177035,396.46889952153106 854,374 C 957.7033492822965,351.53110047846894 1056.200956937799,285.86602870813397 1153,260 C 1249.799043062201,234.133971291866 1344.8995215311006,248.06698564593302 1440,262 L 1440,700 L 0,700 Z" stroke="none" stroke-width="0" fill="%23000000" fill-opacity="1" class="transition-all duration-300 ease-in-out delay-150 path-0"%3E%3C/path%3E%3C/svg%3E'), var(--data-image-2x2-black); /* mask-image */
  border-radius: 56px / clamp(6px, 1.5267175573vw + 0.2748091603px, 12px);
}
```

```css
/* Example: Battered page style */

:root.page-style-polygon-chamfered:not(.minimal) .main__background {
  filter: var(--page-drop-shadow);
}

:root.page-style-polygon-chamfered:not(.minimal) .main__background::before {
  --m: clamp(6px, 1.3392857143vw + 1.7142857143px, 12px);
  clip-path: polygon(0% var(--m), var(--m) 0%, calc(100% - var(--m)) 0%, 100% var(--m), 100% calc(100% - var(--m)), calc(100% - var(--m)) 100%, var(--m) 100%, 0% calc(100% - var(--m))); /* List of dynamically calculated X/Y points. */
}
```

```css
/* Example: Battered header image style */

:root.header-image-style-polygon-battered .header-background {
  border-radius: 0 !important;
}

:root.header-image-style-polygon-battered .header-background__wrapper {
  border-radius: 0 !important;
  clip-path: var(--polygon-battered-half);
}

@media only screen and (min-width: 768px) {
  :root.header-image-style-polygon-battered .header-background__wrapper {
    margin-left: 4px; /* Space for shadow that would be cut off. */
    margin-right: 4px; /* Space for shadow that would be cut off. */
    clip-path: var(--polygon-battered);
  }
}

:root.header-image-style-polygon-battered:not(.inset-header-image) .header-background__wrapper {
  --polygon-battered: var(--polygon-battered-half);
  margin-left: 0;
  margin-right: 0;
}
```

#### Uppercase Site Title

Instead of just uppercasing the string, which would make it uppercase everywhere including search engine results, you can use a little bit of CSS to achieve this clean and proper. You will have to inspect your site to find the right selector, but it should be either one of those: `header__title-link`, `top-header__link`, or `wide-header-identity__title-link`.

```css
/* Or use all three like a lazy person. */

.header__title-link,
.top-header__link,
.wide-header-identity__title-link {
  text-transform: uppercase;
}

```

#### Dynamic Main Container Offset

You can add an offset to the main container in the Customizer, but that one is static. Maybe you need one that changes with the width of the site or something? There are many ways to achieve this, but you probably want to go with a `clamp()` function. To spare you the high school math, you can use an [online tool to calculate it](https://utopia.fyi/clamp/calculator/). Assign the result to the `--main-offset` custom property in a deeper scope than the root.

```css
/* Example: This interpolates from 32px at 375px width and 48px at 768px width. */

body {
  --main-offset: clamp(32px, 16.7328px + 4.0712vi, 48px);
}
```

#### Make Single Pages Wider

Perhaps you want to make your landing page wider, to show off more or larger cards? But without changing the global site width, because that would look silly for chapters? All you need for that is the ID of your page, which you can find in the address bar of your browser on the edit screen, and the `--site-width` custom property. We will scope the changes to that ID and that ID only; you can repeat the process for other IDs, of course.

WordPress automatically adds a class with the ID to the body, like `.page-id-69`. Using that as scope, you can affect the whole site only for this one instance. However, you may want to limit it to the `.main` container class. Otherwise, you site might display extreme layout flickers when the user visits another page. Obviously, you can do more than just increase the width.

```css
/* Example: Increase the width of the main container on the page with ID 69. */

.page-id-69 .main {
  --site-width: 1300px;
}
```

#### Border Under Navigation

You can remove the drop-shadow from the navigation bar and add a border instead. Mind that the navigation bar gains a background color when you scroll down, which you may want to customize or disable as well. Additional padding might also be necessary for this to look good. If you want the border to only appear when the navigation is sticky, add it to the `.main-navigation__background` instead.

```css
/* Example: Semi-transparent border in black (light mode) and white (dark mode). */

.main-navigation__background {
  filter: none;
  box-shadow: none;
}

.main-navigation {
  border-bottom: 1px solid rgb(0 0 0 / 7%);
}

:root[data-mode="dark"] .main-navigation {
  border-bottom: 1px solid rgb(255 255 255 / 7%);
}
```

#### Dark/Light Variants for the Logo/Header Image

You cannot set different logo/header image variants for light or dark mode. This has been removed in a far earlier version of the theme *because it was a mess*, with every image field being doubled. Anyway, you can achieve this with some CSS just as well — or JavaScript, but that becomes complicated. The two easiest options are either to replace the image or apply a [filter](https://developer.mozilla.org/en-US/docs/Web/CSS/filter), the latter being preferable if you can make it work.

```css
:root[data-mode="dark"] .custom-logo {
  content: url("https..."); /* Override the img src. */
}

:root[data-mode="dark"] .header-background__image {
  filter: invert(1); /* Invert the image, if that still looks good. */
}
```

#### Move the Title/Logo

![Customizer HSL Sliders](repo/assets/customizer_move_title_logo.jpg?raw=true)

In order to move the title or logo, you need a bit of custom CSS. This can be added directly under **Appearance > Customize > Additional CSS**. Depending on whether you have a logo or not, you will have one of the following HTML/CSS combinations (and then some, but this is the relevant part).

What you are interested in are the `position`, `transform`, and/or `text-align` properties. `transform` changes the origin point (kinda) of the element, which is normally the top-left corner, so that it can be better offset. The `text-align` only works on the title. If you overwrite those values, you can move the element or text around. If you want to offset from the right or bottom, you need to add `top: unset;` or `left: unset;` or both. Make sure the title or logo still fits on mobile. See references for [position](https://developer.mozilla.org/en-US/docs/Web/CSS/position), [transform](https://developer.mozilla.org/en-US/docs/Web/CSS/transform), and [text-align](https://developer.mozilla.org/en-US/docs/Web/CSS/text-align).

<table>
<tr>
<th>HTML</th>
<th>CSS</th>
</tr>
<tr>
<td>

```html
<header class="header hide-on-fullscreen">
  <div class="header__content">
    <div class="header__title">
      <div class="header__title-heading">
        <a href="#" class="header__title-link" rel="home">Title</a>
      </div>
      <div class="header__title-tagline">Tagline</div>
    </div>
  </div>
</header>
```

</td>
<td>

```css
.header__title {
  position: relative;
  top: 40%;
  /* ... */
  transform: translateY(-50%);
  /* ... */
}
```

</td>
</tr>
<tr></tr>
<tr>
<td>

```html
<header class="header hide-on-fullscreen">
  <div class="header__logo">
    <a href="#" class="custom-logo-link" rel="home">
      <img width="x" height="y" src="#">
    </a>
  </div>
</header>
```

</td>
<td>

```css
.header__logo {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate3d(-50%, -50%, 0);
  /* ... */
}
```

</td>
</tr>
</table>

### Minimum/Maximum Values

![Customizer HSL Sliders](repo/assets/dynamic_scaling_demo.gif?raw=true)

The minimum and maximum values found in the Customizer are used to calculate [clamps](https://developer.mozilla.org/en-US/docs/Web/CSS/clamp), which are responsible for the dynamic scaling of the site. Viewport refers to the actual screen dimensions, again with a minimum (fixed) and maximum (site width). Everything is interpolated between those values. Use the built-in responsive display modes at the bottom of the Customizer to review your changes. Do not forget to check "Use custom layout properties" or your settings will be ignored.

### Menus

![Menu Screen](repo/assets/menu_screen_options.jpg?raw=true)

Fictioneer comes with two menu locations, **Navigation** and **Footer Menu**, located precisely where you would expect. You can read up on how to create and add menus in the [official documentation](https://codex.wordpress.org/WordPress_Menu_User_Guide). The only thing of note here are the special CSS classes you can assign to menu items for certain effects (whitespace-separated). Make sure to enable the additional menu properties under Screen Options at the top.

On desktop, submenus are rendered as dropdown. On mobile, the **Navigation** shows either the top level items in a scrollable track (overflow) or only the mobile menu button (collapse). You can set that in the **Customizer**. The mobile menu is an unfolded list of all items if not specifically excluded with optional CSS classes.

* `not-in-mobile-menu`: As you can guess, this will hide the menu item in the mobile menu. However, submenu items will still be shown, so you can use this to hide superfluous dropdown parents.
* `static-menu-item`: For menu items without link. Changes the cursor and cannot be selected by keyboard (subitems can).

### Queries

In order to keep the database tidy, Fictioneer does not save or keep "falsy" (`""`, `0`, `null`, `false`, `[]`) meta values. This can cause issues with [meta queries](https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters) looking for these values, because posts without are excluded from the results. The most common troublemakers here are `fictioneer_story_sticky`, `fictioneer_story_hidden`, and `fictioneer_chapter_hidden`. There are multiple solutions for this.

**1) Use an extended (but slower) meta query:**

```php
$query_args = array(
  …
  'meta_query' => array(
    'relation' => 'OR',
    array(
      'key' => 'fictioneer_chapter_hidden',
      'value' => '0'
    ),
    array(
      'key' => 'fictioneer_chapter_hidden',
      'compare' => 'NOT EXISTS'
    )
  )
);
```

**2) Allow the desired "falsy" meta fields to be saved:**

```php
add_filter( 'fictioneer_filter_falsy_meta_allow_list', function( $allowed ) {
  $allowed[] = 'fictioneer_story_sticky'; // For example

  return $allowed;
});
```

You can then append missing meta fields with value `0` under **Fictioneer > Tools**. The filter will also prevent those rows from being deleted when you optimize the database.

**3) Hook into `posts_clauses` (complicated; example from the theme):**

```php
/**
 * Filters sticky stories to the top and accounts for missing meta fields
 *
 * @since 5.7.3
 * @since 5.9.4 - Check orderby by components, extend allow list.
 *
 * @param array    $clauses   An associative array of WP_Query SQL clauses.
 * @param WP_Query $wp_query  The WP_Query instance.
 *
 * @return string The updated or unchanged SQL clauses.
 */

function fictioneer_clause_sticky_stories( $clauses, $wp_query ) {
  global $wpdb;

  // Setup
  $vars = $wp_query->query_vars;
  $allowed_queries = ['stories_list', 'latest_stories', 'latest_stories_compact', 'author_stories'];
  $allowed_orderby = ['', 'date', 'modified', 'title', 'meta_value', 'name', 'ID', 'post__in'];
  $given_orderby = $vars['orderby'] ?? [''];
  $given_orderby = is_array( $given_orderby ) ? $given_orderby : explode( ' ', $vars['orderby'] );

  // Return if query is not allowed
  if (
    ! in_array( $vars['fictioneer_query_name'] ?? 0, $allowed_queries ) ||
    ! empty( array_diff( $given_orderby, $allowed_orderby ) )
  ) {
    return $clauses;
  }

  // Update clauses to set missing meta key to 0
  $clauses['join'] .= " LEFT JOIN $wpdb->postmeta AS m ON ($wpdb->posts.ID = m.post_id AND m.meta_key = 'fictioneer_story_sticky')";
  $clauses['orderby'] = "COALESCE(m.meta_value+0, 0) DESC, " . $clauses['orderby'];
  $clauses['groupby'] = "$wpdb->posts.ID";

  // Pass to query
  return $clauses;
}

if ( FICTIONEER_ENABLE_STICKY_CARDS ) {
  add_filter( 'posts_clauses', 'fictioneer_clause_sticky_stories', 10, 2 );
}
```

### Font Awesome

Fictioneer loads the free version of [Font Awesome 6.4.2](https://fontawesome.com/) by default and unless you want to use a different one or encounter compatibility issues (usually when a plugin includes FA as well), no action is required here.

* If you want to include it via plugin (perhaps a Pro Kit) or custom function, disable the theme version under **Fictioneer > General > Compatibility**.

* If you want to change the CDN link and integrity hash, do that by overwriting the `FICTIONEER_FA_CDN` and `FICTIONEER_FA_INTEGRITY` constants in a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/). You can set the integrity to `null` if not needed.

### Custom Fonts

**Note:** The Font Library introduced in WP 6.5 has been disabled due to incompatibilities with the theme, specifically the chapter formatting system allowing to choose fonts. This may change in the future, but for now it should be avoided.

You can add custom fonts, either by uploading a configuration folder to `/themes/your-child-theme/fonts/` or with a CDN like Google Fonts. The latter is far more convenient, though it also violates the GDPR and is therefore not recommended except for testing. Delivering fonts from your server is legally safe, but can affect performance if you do not [leverage browser caching](#securing-wordpress--browser-caching) or use a cache plugin (which you should).

Following is an explanation of both methods on the example of [Noto Sans](https://fonts.google.com/noto/specimen/Noto+Sans?noto.query=noto+sans), which has also great variants for logographic writing systems if you require that. Mind that not all fonts you find on the Internet are free to use.

Purge the theme caches under **Fictioneer > Tools** after adding or removing a font. You may have to force-refresh too. Once everything is in order and refreshed, you can see the font listed under **Fictioneer > Fonts**. With that, you can assign the fonts to specific parts of the theme under **Appearance > Customize > Fonts**. More is possible with custom CSS.

#### A) Upload a font configuration folder

This method requires some preparation. Take a look at the [roboto-serif](https://github.com/Tetrakern/fictioneer/tree/main/fonts/roboto-serif) default font folder; you will find several .woff2 files, one .css file, and one .json file. You can replicate that with relative ease using the [Google Fonts Webhelper](https://gwfh.mranftl.com/fonts/noto-sans) and a text editor of your choice. Search for "Noto Sans", select the charsets and styles you need (typically 300-700), then change the folder prefix to `../fonts/noto-sans/`. Copy the provided CSS into a new font.css file, download and unpack the archive, put everything into a "noto-sans" folder. You can rename the files, remove the comments, and minify the CSS if you got the patience. Just make sure everything is still correct.

The font.json file may seem a bit challenging, but is actually mostly informative. The only name-value pairs of importance right now are **skip**, **chapter**, **remove**, **key**, **name**, and **family**. You are encouraged to fill out the rest regardless, in case it becomes required in the future. Here is a pre-made one for Noto Sans:

<details>
  <summary>Explanations</summary><br>

| Key | Type | Explanation
| :--- | :---: | :---
| skip | boolean | Whether to skip the CSS bundling (if loaded by other means or a system font). Default `false`.
| remove | boolean | Whether to remove the font instead of adding it. Default `false`.
| chapter | boolean | Whether to make the font available in chapters. Default `false`.
| version | string | Version number. Default empty.
| key * | string | Unique identifier and key for the associative font array.
| name * | string | Display name.
| family * | string | CSS value for the font-family property (no extra quotes).
| alt | string | Fallback font-family value stack, e.g. "Helvetica, Arial". Default empty.
| type | string | Design type, such as "sans-serif", "serif", or "monospace". Default empty.
| styles | string[] | Available font-styles. Default empty.
| weights | integer[] | Available font-weights. Default empty.
| charsets | string[] | Supported writing systems. Default empty.
| formats | string[] | Formats of the font files, such as .woff2, .woff, .ttf, and more. Default empty.
| about | string | Description of the font for the admin page. Default empty.
| note | string | Special note about the font for the admin page. Default empty.
| preview | string | Changes the example sentence displayed on the admin page. Default empty.
| sources | object | Collection of sub-objects listing sources for the font. Default empty.

\* Required key-value pairs.

</details><br>

```json
{
  "skip": false,
  "remove": false,
  "chapter": true,
  "version": "35",
  "key": "noto-sans",
  "name": "Noto Sans",
  "family": "Noto Sans",
  "alt": "",
  "type": "sans-serif",
  "styles": ["normal", "italic"],
  "weights": [300, 400, 500, 600, 700],
  "charsets": ["cyrillic", "cyrillic-ext", "devanagari", "greek", "greek-ext", "latin", "latin-ext", "vietnamese"],
  "formats": ["woff2"],
  "about": "Noto Sans is an unmodulated (“sans serif”) design for texts in the Latin, Cyrillic and Greek scripts, which is also suitable as the complementary choice for other script-specific Noto Sans fonts.",
  "note": "",
  "preview": "The quick brown fox jumps over the lazy dog.",
  "sources": {
    "googleFonts": {
      "name": "Google Fonts",
      "url": "https://fonts.google.com/noto/specimen/Noto+Sans"
    },
    "googleWebfontsHelper": {
      "name": "Google Webfonts Helper",
      "url": "https://gwfh.mranftl.com/fonts/noto-sans?subsets=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin,latin-ext,vietnamese"
    }
  }
}
```

You can find a collection of pre-made font folders under [/repo/fonts/](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts). Currently available:

* [Noto Sans](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans): The Noto Sans font from Google.
* [Noto Sans JP](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-jp): Noto Sans variant for Japanese.
* [Noto Sans KR](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-kr): Noto Sans variant for Korean.
* [Noto Sans TC](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-tc): Noto Sans variant for Traditional Chinese.
* [Noto Sans SC](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-sc): Noto Sans variant for Simplified Chinese.
* [Special Elite](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/special-elite): Typewriter-like font good for headings or special sections.
* [Verdana](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/verdana): Web safe font and example for adding pre-installed device fonts.

#### B) Load from the Google Fonts CDN

Visit [Google Fonts](https://fonts.google.com/) and browse for a font you like. On the **Specimen** tab, scroll down to **Styles** and select what you need, typically everything from 300 to 700 if you want to cover all cases of the theme. If some styles are missing, you can still use the font — just perhaps not as primary one. On the right, under **Use on the web**, choose the **\<link\>** option and copy the link of the href attribute (nothing else). Make sure only one font is selected, because bundled font links are currently not understood by the theme.

```
https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap
```

### FFCNR (Fast Fictioneer Requests)

FFCNR is an alternative bootloader that utilizes the `SHORTINIT` constant to load a minimal WordPress environment. Although `SHORTINIT` is not officially documented, you can find guides about it with a quick search. In this mode, almost nothing is loaded: no themes, no plugins, and only a small subset of WordPress itself. For implementation details, refer to `ffcnr.php` in the theme directory.

In FFCNR mode, you have access to the `$wpdb` object for database communication, anything defined in `wp-config.php`, and `apply_filters()` and `do_action()` functionality. However, **you do not** have access to user authentication functions like `wp_get_current_user()` or permission checks such as `current_user_can()`. Additionally, you cannot use conditionals like `is_single()`, utility functions like `esc_attr()` or `get_permalink()`, or content functions such as `the_content()`.

FFCNR is meant exclusively for performance-critical requests, such as AJAX calls to retrieve or write data to the database. It is not secure and should never be used for sensitive operations unless you fully understand the implications. For that reason, no further guidance will be provided here. If you cannot figure this out on your own, you should not be experimenting with it.

### Constants

Some options are not available in the settings because tempering with them can break the theme or result in unexpected behavior. Those options are defined via constants in the **function.php**. If you want to change them, you need a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) or access to your **wp-config.php**. Just override them in the child theme’s own **function.php** or config, but only if you know what you are doing!

```php
define( 'CONSTANT_NAME', value );
```

| Constant | Type | Explanation
| :--- | :---: | :---
| CHILD_VERSION | string\|null | Version number of the child theme. Default `null`.
| CHILD_NAME | string\|null | Name of the child theme. Default `null`.
| FICTIONEER_OAUTH_ENDPOINT | string | URI slug to call the OAuth script. Default `'oauth2'`.
| FICTIONEER_EPUB_ENDPOINT | string | URI slug to call the ePUB script. Default `'download-epub'`.
| FICTIONEER_LOGOUT_ENDPOINT | string | URI slug to call the logout script. Default `'fictioneer-logout'`.
| FICTIONEER_PRIMARY_FONT_CSS | string | CSS name of the primary font. Default `'Open Sans'`.
| FICTIONEER_PRIMARY_FONT_NAME | string | Display name of the primary font. Default `'Open Sans'`.
| FICTIONEER_TTS_REGEX | string | Splits chapter text into sentences for the text-to-speech feature. Default `'([.!?:"\'\u201C\u201D])\s+(?=[A-Z"\'\u201C\u201D])'`.
| FICTIONEER_DEFAULT_CHAPTER_ICON | string | Chapter icon Font Awesome (Free) classes. Default `'fa-solid fa-book'`.
| FICTIONEER_LATEST_UPDATES_LI_DATE | string | Latest Updates shortcode list item date format. Default `'M j'`.
| FICTIONEER_LATEST_UPDATES_FOOTER_DATE | string | Latest Updates shortcode footer date format. Default `"M j, 'y"`.
| FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE | string | Latest Chapters shortcode footer date format. Default `"M j, 'y"`.
| FICTIONEER_LATEST_STORIES_FOOTER_DATE | string | Latest Stories shortcode footer date format. Default `"M j, 'y"`.
| FICTIONEER_CARD_STORY_LI_DATE | string | Story card list item date format. Default `"M j, 'y"`.
| FICTIONEER_CARD_STORY_FOOTER_DATE | string | Story card footer date format. Default `"M j, 'y"`.
| FICTIONEER_CARD_CHAPTER_FOOTER_DATE | string | Chapter card footer date format. Default `"M j, 'y"`.
| FICTIONEER_CARD_COLLECTION_LI_DATE | string | Collection card list item date format. Default `"M j, 'y"`.
| FICTIONEER_CARD_COLLECTION_FOOTER_DATE | string | Collection card footer date format. Default `"M j, 'y"`.
| FICTIONEER_CARD_POST_FOOTER_DATE | string | Post card footer date format. Default `"M j, 'y"`
| FICTIONEER_CARD_PAGE_FOOTER_DATE | string | Page card footer date format. Default `"M j, 'y"`.
| FICTIONEER_CARD_ARTICLE_FOOTER_DATE | string | Article card footer date format. Default `"M j, 'y"`.
| FICTIONEER_STORY_FOOTER_B480_DATE | string | Story page footer date format (<= 480px). Default `"M j, 'y"`.
| FICTIONEER_FA_CDN | string | Font Awesome CDN URL.
| FICTIONEER_FA_INTEGRITY | string | Font Awesome integrity SHA384 hash.
| FICTIONEER_DISCORD_EMBED_COLOR | string | Color code for Discord notifications. Default `'9692513'`.
| FICTIONEER_TRUNCATION_ELLIPSIS | string | Appended to truncated strings. Default `…`.
| FICTIONEER_AGE_CONFIRMATION_REDIRECT | string | Redirect URL if a visitor reject the age confirmation. Default `https://search.brave.com/`.
| FICTIONEER_DEFAULT_SITE_WIDTH | integer | Default site width. Default `960`.
| FICTIONEER_COMMENTCODE_TTL | integer | How long guests can see their private/unapproved comments in _seconds_. Default `600`.
| FICTIONEER_AJAX_TTL | integer | How long to cache certain AJAX requests locally in _milliseconds_. Default `60000`.
| FICTIONEER_AJAX_POST_DEBOUNCE_RATE | integer | How long to debounce AJAX requests of the same type in _milliseconds_. Default `700`.
| FICTIONEER_AUTHOR_KEYWORD_SEARCH_LIMIT | integer | Maximum number of authors in the advanced search suggestions. Default `100`.
| FICTIONEER_UPDATE_CHECK_TIMEOUT | integer | Timeout between checks for theme updates in _seconds_. Default `43200`.
| FICTIONEER_API_STORYGRAPH_CACHE_TTL | integer | How long Storygraph responses are cached in _seconds_. Default `3600`.
| FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE | integer | How many items the Storygraph `/stories` endpoint returns. Default 25.
| FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY | integer | Maximum number of story custom pages. Default `4`.
| FICTIONEER_CHAPTER_FOLDING_THRESHOLD | integer | Threshold before and after folding in chapter lists. Default `5`.
| FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION | integer | Expiration duration for shortcode Transients in seconds. Default `300`.
| FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT | integer | Timeout between comment count refreshes for stories in _seconds_. Default `900`.
| FICTIONEER_REQUESTS_PER_MINUTE | integer | Maximum requests per minute and action if the rate limit is enabled. Default `5`.
| FICTIONEER_QUERY_ID_ARRAY_LIMIT | integer | Maximum allowed IDs in 'post__{not}_in' query arguments. Default `1000`.
| FICTIONEER_PATREON_EXPIRATION_TIME | integer | Time until a user’s Patreon data expires in seconds. Default `WEEK_IN_SECONDS`.
| FICTIONEER_PARTIAL_CACHE_EXPIRATION_TIME | integer | Time until a cached partial expires in seconds. Default `4 * HOUR_IN_SECONDS`.
| FICTIONEER_CARD_CACHE_LIMIT | integer | Number of story cards cached if the feature is enabled. Default `50`.
| FICTIONEER_CARD_CACHE_EXPIRATION_TIME | integer | Time until the whole story card cache expires in seconds. Default `HOUR_IN_SECONDS`.
| FICTIONEER_STORY_CARD_CHAPTER_LIMIT | integer | Maximum number of chapters shown on story cards. Default 3.
| FICTIONEER_OAUTH_COOKIE_EXPIRATION | integer | Expiration time of the OAuth 2.0 login cookie in seconds. Default `259200` (3 days).
| FICTIONEER_CACHE_PURGE_ASSIST | boolean | Whether to call the cache purge assist function on post updates. Default `true`.
| FICTIONEER_RELATIONSHIP_PURGE_ASSIST | boolean | Whether to purge related post caches. Default `true`.
| FICTIONEER_SHOW_SEARCH_IN_MENUS | boolean | Whether to show search page links in menus. Default `true`.
| FICTIONEER_THEME_SWITCH | boolean | Whether to show the theme switch in child themes (back to base). Default `true`.
| FICTIONEER_ATTACHMENT_PAGES | boolean | Whether to enable pages for attachments (no theme templates). Default `false`.
| FICTIONEER_SHOW_OAUTH_HASHES | boolean | Whether to show OAuth ID hashes in user profiles (admin only). Default `false`.
| FICTIONEER_DISALLOWED_KEY_NOTICE | boolean | Whether to show feedback for rejected comment content. Default `true`.
| FICTIONEER_FILTER_STORY_CHAPTERS | boolean | Whether to filter selectable chapters by assigned story. Default `true`.
| FICTIONEER_COLLAPSE_COMMENT_FORM | boolean | Whether hide comment form inputs until the textarea is clicked. Default `true`.
| FICTIONEER_API_STORYGRAPH_IMAGES | boolean | Whether to add image links to the Storygraph. Default `true`.
| FICTIONEER_API_STORYGRAPH_HOTLINK | boolean | Whether hotlinking images from the Storygraph is allowed. Default `false`.
| FICTIONEER_ENABLE_STICKY_CARDS | boolean | Whether to allow sticky cards. Expensive. Default `true`.
| FICTIONEER_ENABLE_STORY_DATA_META_CACHE | boolean | Whether to "cache" story data in a meta field. Default `true`.
| FICTIONEER_ENABLE_CHAPTER_INDEX_META_CACHE | boolean | Whether to "cache" the chapter index HTML (modal) in a meta field. Default `true`.
| FICTIONEER_ENABLE_AUTHOR_STATS_META_CACHE | boolean | Whether to "cache" the author statistics in a meta field. Default `true`.
| FICTIONEER_ORDER_STORIES_BY_LATEST_CHAPTER | boolean | Whether to order updated stories based on the latest chapter added, excluding stories without chapters. Default `false`.
| FICTIONEER_ENABLE_STORY_CHANGELOG | boolean | Whether changes to the story chapter list should be logged. Default `true`.
| FICTIONEER_DEFER_SCRIPTS | boolean | Whether to defer scripts or load them in the footer. Default `true`.
| FICTIONEER_ENABLE_ASYNC_ONLOAD_PATTERN | boolean | Whether the [onload pattern](https://www.filamentgroup.com/lab/load-css-simpler/) for asynchronous CSS loading is used. Default `true`.
| FICTIONEER_SHOW_LATEST_CHAPTERS_ON_STORY_CARDS | boolean | Whether to show the latest instead of the first chapters on story cards. Default `false`.
| FICTIONEER_LIST_SCHEDULED_CHAPTERS | boolean | Whether to show scheduled chapters in lists. Default `false`.
| FICTIONEER_ENABLE_ALL_AUTHOR_PROFILES | boolean | Whether to enable all author profile pages. Default `false`.
| FICTIONEER_EXAMPLE_CHAPTER_ICONS | array | Collection of example Font Awesome icon class strings.
