# Installation

This guide is mainly written for people who never had their own WordPress site before and may not have the skills to figure this out by themselves. Feel free to skip ahead. That being said, there are still some parts of interest for veterans in regards to the theme.

### Table of Contents

* [Choosing a Host](#choosing-a-host)
* [Installing WordPress](#installing-wordpress)
  * [Configuring WordPress](#configuring-wordpress)
  * [Securing WordPress](#securing-wordpress)
* [Legal Considerations](#legal-considerations)
* [How to Install/Update the Fictioneer Theme](#how-to-installupdate-the-fictioneer-theme)
  * [Updating the Theme](#updating-the-theme)
  * [Optional: Install Plugin Dependencies](#optional-install-plugin-dependencies)
  * [Optional: Additional Plugins](#optional-additional-plugins)
  * [Optional: Caching](#optional-caching)
  * [Recommended: Must-Use Plugins](#recommended-must-use-plugins)
  * [Warning: SEO Plugins](#warning-seo-plugins)
  * [Warning: CSS Minification/Combination](#warning-css-minificationcombination)
* [How to Configure the Fictioneer Theme](#how-to-configure-the-fictioneer-theme)
  * [General Tab](#general-tab)
  * [Roles Tab](#roles-tab)
  * [Connections Tab](#connections-tab)
  * [Phrases Tab](#phrases-tab)
  * [ePUBs Tab](#epubs-tab)
  * [SEO Tab](#seo-tab)
  * [Tools Tab](#tools-tab)
  * [Log Tab](#log)
* [How to Customize the Fictioneer Theme](#how-to-customize-the-fictioneer-theme)
  * [Move the Title/Logo](#move-the-titlelogo)
  * [Minimum/Maximum Values](#minimummaximum-values)
  * [Menus](#menus)
  * [Constants](#constants)

## Choosing a Host

First, you need to choose a host for your site, the place where your site "lives". This may very well be the hardest part, because bad choices are annoying to fix and cost money. For that matter, you are encouraged to do your own research — [Online Media Masters](https://onlinemediamasters.com/) is a good place to start. If you feel completely lost, asking for help is entirely justified.

Your choice will ultimately come down to two schools: managed hosting or not. Managed hosting takes away the burden of having to, well, manage your server. Configuration, maintenance, security, and performance issues are covered by the provider — at a price. For example, on [WordPress.com](https://wordpress.com/pricing/) you would need at least the Business plan to use the Fictioneer theme. Non-managed hosting is more affordable and less restrictive, but you need a bit of technical know-how or someone helping you.

If the hosting cost are too much for you alone, there is also the option to share a site with other authors and split the bill. Typically with the administrator holding the contract. Just make sure you trust everyone and write down the obligations and rights of all participants involved. Always prepare for the fallout.

## Installing WordPress

The installation process for WordPress is [documented on the official site](https://wordpress.org/support/article/how-to-install-wordpress/) and in many guides only a quick search away. Nowadays, most hosts offer a one-click installation service as well. Note that the latter often comes with pre-installed plugins that you may want to get rid of, especially analytics plugins which tend to violate data privacy laws.

Fictioneer is best used on a fresh install due to its complexity and possible conflicts with existing plugins or customizations. Which does not mean you cannot switch or migrate, but it would be an ordeal. For example, Fictioneer has custom post types for stories and chapters, so you would need to either [convert existing posts](https://wordpress.org/plugins/post-type-switcher/) or upload them anew (which would disassociate all comments). They also have several additional settings, making automatic conversion scripts risky. Depending on how many posts you have, this may take a while.

### Configuring WordPress

Everything installed? Head to **[Settings](https://wordpress.org/support/article/administration-screens/#general)** in the admin panel to configure your site. You can follow a guide, but this should all be fairly obvious. For the purpose of working with the theme, you are most interested in the **Reading**, **Discussion**, and **Permalinks** submenus.

* **Reading:** If you want a static page like in the demo, you can set this here. Of course, you need to [create the pages](https://wordpress.org/support/article/pages/) for blog and front page first. Best use the “No Title Page” template. Keep the number of blog posts and feed items somewhere between 8 and 20.

* **Discussion:** Most of this is up to you, but the number and order of comments does not necessarily behave as you would expect. Comments are always nested in the theme, regardless of the checkbox, but the depth is honored and should be anywhere up to 5. Break comments into pages with 8 to 50 comments each, the first page being displayed by default, and newer comments at the top. The theme really does not work well with anything else but you are welcome to try.

    * **Disallowed Comment Keys:** For simple yet reliable comment spam protection, you are advised to use the [compiled disallow list by slorp](https://github.com/splorp/wordpress-comment-blacklist). Just copy the content of the blacklist into the [Disallowed Comment Key](https://wordpress.org/support/article/comment-moderation/#comment-blocking) field. Check your comment trash occasionally as this can lead to false positives. You can search for less restrictive lists too.

* **Permalinks:** You want the permalink structure set to "Post name". As an off-note, whenever some pages do not show up even though they clearly should, come back here and save to update the permalink structure. You would be surprised how many issues that solves.

* **Open Graph Default Image:** Only used when you enable the SEO features and no (known) SEP plugin is running. This image will be shown in search engine results and social media embeds if no other image is provided by individual posts, such as story cover images. Can be set under **Appearance > Customize > Site Identity**.

* **Author websites:** Technically not a required setting, but authors may want to fill out the website field in their profile. These are added as Open Graph author meta tags used by search engines and social media embeds. If left blank, the generated author page of the site will be used instead, which might be what you want anyway.

### Securing WordPress

You can greatly improve your site security and performance by adding policies to the **.htaccess** file located in the WordPress root directory. Managed hosting plans normally do this for you. Make a backup and add the following lines anywhere before `# BEGIN WordPress` or after `# END WordPress`. If something goes wrong wrong, just remove everything again or restore the backup. You can also use a (cache) plugin. This is just the basics, far more is possible but please refer to a proper guide.

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

There is not much to consider aside from the [data privacy](https://wordpress.com/go/website-building/how-to-write-and-add-a-privacy-policy-to-your-wordpress-site/) issue, which depends on your country of residence and where your host server is located. However, to preempt any legal trouble, you want to assume the strictest laws apply — the [GDPR](https://en.wikipedia.org/wiki/General_Data_Protection_Regulation) and [CCPA](https://en.wikipedia.org/wiki/California_Consumer_Privacy_Act). Fictioneer is compliant with both unless you change things, but you also need to add a [Privacy Policy](PRIVACY.md). And forget about Google Analytics.

## How to Install/Update the Fictioneer Theme

![Upload Theme Screen](repo/assets/appearance_upload_theme.jpg?raw=true)

After you have set up your WordPress site, you can install the theme. Since Fictioneer is not available in the official theme library, you need to do this manually. Either by uploading the *unpacked* theme folder into the `/wp-content/themes/` directory via FTP or by uploading the `.zip` file in the admin panel under **Appearance > Themes > Add New > Upload Theme**.

When you are done, activate the theme under **Appearance > Themes**. If you want to use a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/), which is installed the same way, activate that instead (you need both the main and the child theme). Head to the newly added Fictioneer menu page in the admin sidebar afterwards. Here you need to [configure](#How-to-configure-the-fictioneer-theme) the theme. You may also want to [customize](#How-to-customize-the-fictioneer-theme) the look.

### Updating the Theme

Updating the theme works the same as installing the theme. If done in the admin panel, you will be warned that the theme is already installed and given a quick comparison, prompting you to confirm the overwrite. Make sure you still fulfill all the requirements, namely your WordPress and PHP versions. You can find this information in the info tab on the [site health screen](https://wordpress.org/support/article/site-health-screen/).

Note that any changes made to the theme files will be undone — which you should not have done in the first place. Always use a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) for modifications to avoid this issue. Your theme options and Customizer settings are preserved, however.

### Optional: Install Plugin Dependencies

Fictioneer relies heavily on one developer plugin, [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/). ACF is already bundled into the theme, but you may want to install it separately to stay on the latest version or use it for your own modifications — which should happen in a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/). You cannot access the bundled version in order to prevent non-developer users from accidentally breaking the theme. Changing ACF fields can cause difficult to repair damage!

### Optional: Additional Plugins

The [plugin ecosystem](https://wordpress.org/plugins/) of WordPress is vast and often confusing. There are plugins for almost everything, in variants, free or premium or "freemium". You often find articles about "must-have" plugins — you are well advised to question those. Too many plugins can slow down your site, open vulnerabilities, or conflict with the theme. Fictioneer is designed as standalone solution and technically works without additional plugins. However, nothing is ever complete, so here are a few recommendations anyway.

* [Autoptimize](https://wordpress.org/plugins/autoptimize/): Optimization plugin to speed up your site. Best used for its aggregation and deferment of static resources, such as styles and scripts, solving browser cache issues along the way. The other options are nice if not already covered elsewhere.

  <details>
    <summary>Example settings</summary><br>
    <blockquote>
      <sup>Assume missing options are off, empty, or left to default.</sup>
      <h4>[JS, CSS & HTML] JavaScript Options:</h4>
      <ul>
        <li>- [x] Optimize JavaScript code?</li>
        <li>- [x] Aggregate JS-files?</li>
      </ul>
      <h4>[JS, CSS & HTML] CSS Options:</h4>
      <ul>
        <li>- [x] Optimize CSS Code?</li>
        <li>- [x] Aggregate CSS-files?</li>
        <li>- [x] Generate data: URIs for images?</li>
      </ul>
      <h4>[JS, CSS & HTML] Misc Options:</h4>
      <ul>
        <li>- [x] Save aggregated script/css as static files?</li>
        <li>- [x] Enable 404 fallbacks?</li>
        <li>- [x] Also optimize for logged in editors/administrators?</li>
        <li>- [x] Disable extra compatibility logic?</li>
      </ul>
      <h4>[Extra] Extra Auto-Optimizations:</h4>
      <ul>
        <li>- [x] Google Fonts: Leave as is</li>
        <li>- [x] Remove emojis</li>
      </ul>
    </blockquote><br>
  </details>

* [Cloudinary](https://wordpress.org/plugins/cloudinary-image-management-and-manipulation-in-the-cloud-cdn/): Great "plug-and-play" image CDN and optimizer with generous free plan. Offloading your images to a content delivery network improves performance and loading times. Also, your images will be properly sized and compressed.

  <details>
    <summary>Example settings</summary><br>
    <p>Follow the <a href="https://cloudinary.com/documentation/wordpress_integration">official guide</a> to set up your Cloudinary account and the plugin. You do not need to "register" the CND with other optimization or cache plugins — it will just work.</p>
    <blockquote>
      <sup>Assume missing options are off, empty, or left to default.</sup>
      <h4>[General settings] Media Library Sync Settings:</h4>
      <ul>
        <li>- [x] Sync method: Auto sync</li>
      </ul>
      <h4>[General settings] Cloudinary folder path:</h4>
      <p>&numsp;To keep things orderly, use a folder name that relates to your site.</p>
      <h4>[General settings] Storage:</h4>
      <ul>
        <li>- [x] Cloudinary and WordPress</li>
      </ul>
      <h4>[Image settings] Image optimization:</h4>
      <ul>
        <li>- [x] Optimize images on my site.</li>
        <li>- [x] Image format: Auto</li>
        <li>- [x] Image quality: Auto</li>
      </ul>
      <h4>[Lazy loading] Lazy loading:</h4>
      <ul>
        <li>- [x] Enable lazy loading</li>
        <li>- [x] Lazy loading threshold: 100px</li>
        <li>- [x] Pre-loader color: You decide!</li>
        <li>- [x] Pre-loader animation: You decide!</li>
        <li>- [x] Placeholder generation type: You decide!</li>
        <li>- [x] DPR settings: Auto (2x)</li>
      </ul>
      <h4>[Responsive images] Breakpoints:</h4>
      <ul>
        <li>- [ ] Enable responsive images (OFF - this increases usage)</li>
      </ul>
    </blockquote><br>
  </details>

* [Cloudflare](https://wordpress.org/plugins/cloudflare/): Global content delivery network designed to make your site secure, private, fast, and reliable. Can be used for caching or to enhance a cache plugin further. Unfortunately, the setup is not trivial and you should refer to specific guides or ask for help.

  <details>
    <summary>Cache considerations</summary><br>
    <p>Cloudflare can be problematic if you want to capitalize on the "Cache Everything" option because without a paid plan, you cannot make exceptions for logged-in users. This means visitors might see personalized content of the first user to populate the cache — not good! Imagine your account details being leaked like that. It also does not easily cooperate with on-site caching solutions.</p>
    <p>That being said, the free tier can be persuaded! Ditch the official plugin and install <a href="https://wordpress.org/plugins/wp-cloudflare-page-cache/">Super Page Cache for Cloudflare</a> instead. Same as before, refer to proper guides. Make sure you have the following settings and be prepared that it might still not work! Test this!</p>
    <blockquote>
      <h4>[Plugin: Cache] Don't cache the following dynamic contents:</h4>
      <ul>
        <li>- [x] Page 404 (is_404)</li>
        <li>- [x] Feeds (is_feed)</li>
        <li>- [x] Search Pages (is_search)</li>
        <li>- [x] Ajax Requests</li>
        <li>- [x] WP JSON endpoints</li>
      </ul>
      <h4>[Plugin: Cache] Prevent the following URIs to be cached:</h4>
      &numsp;<sup>Append to defaults. The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).</sup><br>
      &numsp;<code>/oauth2*</code><br>
      &numsp;<code>/download-epub*</code><br>
      &numsp;<code>/account*</code><br>
      &numsp;<code>/bookshelf*</code><br>
      &numsp;<code>/*commentcode=*</code>
      <h4>[Fictioneer: General] Page Assignments:</h4>
      <ul>
        <li>- [ ] Account: None (default dashboard profile)</li>
        <li>- [x] Bookshelf: Page with the Bookshelf AJAX template (if you need this)</li>
      </ul>
      <h4>[Fictioneer: General] Comments:</h4>
      <ul>
        <li>- [x] Enable AJAX comment submission</li>
      </ul>
      <h4>[Fictioneer: General] Security & Privacy:</h4>
      <ul>
        <li>- [ ] Block admin panel access for subscribers (OFF)</li>
      </ul>
      <h4>[Fictioneer: General] Compatibility:</h4>
      <ul>
        <li>- [x] Enable cache compatibility mode</li>
        <li>- [x] Enable AJAX user authentication</li>
        <li>- [x] Enable AJAX comment form (best performance) ... or ... comment section (best compatibility)</li>
      </ul>
    </blockquote>
    <p>Optional: Install <a href="https://wordpress.org/plugins/wp-super-cache/">WP Super Cache</a> with the extreme settings. Cannot wrongly cache dynamic pages if there are none!</p><br>
  </details>

* [WPS Limit Login](https://wordpress.org/plugins/wps-limit-login/): Protects you from brute-force attacks by limiting the number of login attempts within a certain period of time. The sibling plugin [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/) moves the whole login page to a new URL, if you want to go one step further.

  <details>
    <summary>User authentication</summary><br>
    <p>Fictioneer does not have a frontend login form and the login page is not recommended for subscribers, so hiding it serves as additional security layer. Note that the optional OAuth 2.0 authentication system via Discord, Google, etc. is not affected by these plugins.</p><br>
  </details>

* [Sucuri Security - Auditing, Malware Scanner and Hardening](https://wordpress.org/plugins/sucuri-scanner/): The free version is meant to complement your security posture and comes with hardening, malware scanner, core file integrity checking, event logging, email alerts for important issues, and more.

  <details>
    <summary>Notes</summary><br>
    <p>There is not much to screw up, but you should refer to a proper guide for your own peace of mind. Because Sucuri has a tendency to be overzealous with scary warnings and until you set up a whitelist, you will see many false positives. Better safe than sorry.</p>
    <blockquote>
      <h4>Typical false positives:</h4>
      &numsp;<code>error_log-* (potentially any log from any plugin)</code><br>
      &numsp;<code>.htaccess.bk (generated backup of .htaccess)</code><br>
    </blockquote><br>
  </details>

* [UpdraftPlus](https://wordpress.org/plugins/updraftplus/): One of the most popular and convenient backup plugins. If your host does not offer backups or you want to stay in control, this is a good choice to keep your site safe in the event of a disaster.

  <details>
    <summary>Why you want backups</summary><br>
    <p>To quote the plugin’s own premonition: "The day may come when you get hacked, when something goes wrong with an update, your server crashes or your hosting company goes bust — without good backups, you lose everything." The free version is perfectly adequate, allowing you to schedule daily backups saved directly to a remote destination of your choice.</p><br>
  </details>

* [EWWW Image Optimizer](https://wordpress.org/plugins/ewww-image-optimizer/): An optimization plugin to properly scale, compress, and (optionally) convert your images. Large file sizes reduce your website’s speed and search rank. Redundant if you use an image CDN like Cloudinary, but they can work together.

  <details>
    <summary>Example settings</summary><br>
    <p>As a matter of fact, you do not need this kind of plugin at all if you pay a modicum of attention to the images you upload. One of the most common yet easy to fix mistakes is uploading over-sized images. Obviously, if your header image is 20 MB, your loading time will go down the drain. Your site will be even faster without the overhead of this plugin if you just pre-optimize your images.</p>
    <blockquote>
      <sup>Follow the initial setup guide, then head to <strong>Settings > EWWW Image Optimizer</strong> to review the settings. Also take a look at the <a href="https://docs.ewww.io/article/4-getting-started">official documentation</a>. Assume missing options are off, empty, or left to default.</sup>
      <h4>Basic settings:</h4>
      <ul>
        <li>- [x] Stick with free mode for now</li>
        <li>- [x] Remove Metadata</li>
        <li>- [x] Resize Images: 1920|1920 (3840|2160 if you need 4k images)</li>
        <li>- [ ] Add Missing Dimensions (OFF - this can break the layout)</li>
        <li>- [x] Lazy Load: Improves actual and perceived loading time ...</li>
        <li>- [ ] Lazy Load: Automatic Scaling (OFF)</li>
        <li>- [ ] WebP Conversion (OFF - smaller but unreliable in quality)</li>
      </ul>
    </blockquote><br>
  </details>

### Optional: Caching

Technically just another plugin, but one that will make your site significantly faster. [Caching](https://wordpress.org/support/article/optimization-caching/) saves your posts and pages as static files to be served later instead of rendering them anew on each request. Guests see the same content anyway, so why waste resources? Only logged-in users can have individual content that must not be cached, such as their account profile. Following are a few cache plugins that have proven to work well with the theme. Do this after you configured your site.

**Note:** Caches require to be purged occasionally, especially after you updated the theme, settings, or plugins. Your site might show outdated pages otherwise. With *known* plugins, Fictioneer automatically purges post caches when you publish or edit content. Other cache plugins require some custom code or need to be purged manually. Inconvenient, but workable.

**Cacheable Query Vars:** Most cache plugins automatically exclude pages with query vars (`/?foo=bar`), because they tend to have dynamic content. However, there are some query vars that can be safely cached if the plugin recognizes them as separate URLs: `pg` (page), `tab`, and technically `order` as well. You may have even more.

**Rule of Thumb:** Is something missing or misplaced, purge the cache! Chapter order wrong? Purge the cache! Collections outdated? Purge the cache! Page flashing red? That’s right, call an exorcist and purge the cache!

* [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/): Made by [Automattic](https://automattic.com/), a main contributor to WordPress the *software* and owner of WordPress.com the *service* (do not confuse them), this free cache plugin is a great choice if you want simple and reliable. It is also completely free.

  <details>
    <summary>Recommended settings</summary><br>
    <p>This is the "safest" advanced setup in the regard that you do not need to mess with server files. Expert mode is a tick faster and not actually complicated, but if the terms ".htaccess" and "mod_rewrite" make you feel queasy, you are perfectly fine with simple mode.</p>
    <blockquote>
      <sup>Assume missing options are off, empty, or left to default.</sup>
      <h4>[Advanced] Caching:</h4>
      <ul>
        <li>- [x] Enable Caching</li>
      </ul>
      <h4>[Advanced] Cache Delivery Method:</h4>
      <ul>
        <li>- [x] Simple</li>
      </ul>
      <h4>[Advanced] Miscellaneous:</h4>
      <ul>
        <li>- [x] Cache Restrictions: Disable caching for logged in visitors</li>
        <li>- [x] Don’t cache pages with GET parameters.</li>
        <li>- [x] Compress pages so they’re served more quickly to visitors.</li>
        <li>- [x] Cache rebuild. Serve a supercache file to anonymous users while a new file is being generated.</li>
      </ul>
      <h4>[Advanced] Advanced:</h4>
      <ul>
        <li>- [x] Extra homepage checks.</li>
        <li>- [x] Only refresh current page when comments made.</li>
        <li>- [x] List the newest cached pages on this page.</li>
      </ul>
      <h4>[Advanced] Expiry Time & Garbage Collection:</h4>
      <ul>
        <li>- [x] Cache Timeout: 7200</li>
        <li>- [x] Timer: 600</li>
      </ul>
      <h4>[Advanced] Accepted Filenames & Rejected URIs:</h4>
      <ul>
        <li>- [x] Feeds</li>
        <li>- [x] Search Pages</li>
      </ul>
      <h4>[Advanced] Rejected URL Strings:</h4>
      &numsp;<sup>The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).</sup><br>
      &numsp;<code>/oauth2</code><br>
      &numsp;<code>/download-epub</code><br>
      &numsp;<code>/account</code><br>
      &numsp;<code>/bookshelf</code><br>
      &numsp;<code>/wp-json/storygraph</code><br>
      &numsp;<code>/wp-json/fictioneer</code>
    </blockquote><br>
  </details>

  <details>
    <summary>Extreme settings</summary><br>
    <p>This is the most "aggressive" setup meant to carry membership sites on cheaper hosts, e.g. sites with many simultaneous requests by logged-in visitors who would normally not be served supercached files. Generating individual pages in large numbers within a short amount of time can overwhelm a server, leading to timeout errors. An issue you are unlikely to encounter as long as you do not have thousands of daily visitors. But in that case, just extend the recommended settings with the following ones.</p>
    <blockquote>
      <sup>Assume missing options are off, empty, or left to default.</sup>
      <h4>[Advanced] Miscellaneous:</h4>
      <ul>
        <li>- [x] Cache Restrictions: Enable caching for all visitors</li>
        <li>- [x] Make known users anonymous so they’re served supercached static files.</li>
      </ul>
      <hr>
      Great, now your site is broken for logged-in users! Or rather, they are treated like guests and cannot see their personal content or post comments anymore. To resolve this, head to the <a href="#general-tab">Fictioneer general settings</a> and activate the following options. Clear the cache afterwards. Yes, the admin bar is now gone. Yes, you can still get into the admin with the <code>…/wp-admin</code> link. No, password protected posts no longer work.<br>
      <h4>[General] Page Assignments:</h4>
      <ul>
        <li>- [ ] Account: None (default dashboard profile)</li>
        <li>- [x] Bookshelf: Page with the Bookshelf AJAX template (if you need this)</li>
      </ul>
      <h4>[General] Security & Privacy:</h4>
      <ul>
        <li>- [ ] Block admin panel access for subscribers (OFF)</li>
      </ul>
      <h4>[General] Compatibility:</h4>
      <ul>
        <li>- [x] Enable public cache compatibility mode</li>
        <li>- [x] Enable AJAX user authentication</li>
        <li>- [x] Enable AJAX comment form (best performance) ... or ... comment section (best compatibility)</li>
      </ul>
    </blockquote><br>
  </details>

* [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/): Comprehensive suite of caching and performance features with great compatibility regardless of host. But a rather involved setup and requires a subscription to make the most of it. Please refer to a guide for installation.

  <details>
    <summary>Cache exceptions</summary><br>
    <p>As long as you only serve cached pages to unauthenticated users, you can hardly do wrong. To make absolutely sure everything works, please add the following exceptions under <strong>Performance > Page Cache</strong>.</p>
    <blockquote>
      <h4>[Page Cache] Never cache the following pages:</h4>
      &numsp;<sup>The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).</sup><br>
      &numsp;<code>/oauth2*</code><br>
      &numsp;<code>/download-epub*</code><br>
      &numsp;<code>/account*</code><br>
      &numsp;<code>/bookshelf*</code><br>
      &numsp;<code>/wp-json/storygraph</code><br>
      &numsp;<code>/wp-json/fictioneer</code>
    </blockquote><br>
  </details>

* [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/): The most powerful of the listed cache plugins and also completely free — if you can get it running. As server-side cache, your host must support [LiteSpeed](https://docs.litespeedtech.com/lscache/), which is usually a prominent selling point so you would know.

  <details>
    <summary>Example settings</summary><br>
    <p>LiteSpeed Cache offers you far more than what is covered here, so please refer to more comprehensive guides if you want to take advantage of that. However, combined with the other recommended plugins, you can do without.</p>
    <blockquote>
      <sup>Assume missing options are off, empty, or left to default.</sup>
      <h4>[1 - Cache] Cache Control Settings:</h4>
      <ul>
        <li>- [x] Enable Cache</li>
        <li>- [ ] Cache Logged-in Users (OFF)</li>
        <li>- [ ] Cache Commenters (OFF)</li>
        <li>- [x] Cache REST API</li>
        <li>- [x] Cache Login Page</li>
        <li>- [x] Cache favicon.ico</li>
        <li>- [x] Cache PHP Resources</li>
        <li>- [ ] Cache Mobile (OFF)</li>
      </ul>
      <h4>[2 - TTL] TTL:</h4>
      <ul>
        <li>- [x] Default Public Cache TTL: 28800</li>
        <li>- [x] Default Private Cache TTL: 1800</li>
        <li>- [x] Default Front Page TTL: 604800</li>
        <li>- [x] Default Feed TTL: 604800</li>
        <li>- [x] Default REST TTL: 28800</li>
      </ul>
      <h4>[3 - Purge] Purge Settings:</h4>
      <ul>
        <li>- [x] Purge All On Upgrade</li>
        <li>- [x] Auto Purge Rules For Publish/Update: All pages</li>
        <li>- [ ] Serve Stale (OFF)</li>
      </ul>
      <h4>[4 - Excludes] Do Not Cache URIs:</h4>
      &numsp;<sup>The "account" and "bookshelf" URI fragments may differ on your site (since you can name them).</sup><br>
      &numsp;<code>/oauth2</code><br>
      &numsp;<code>/download-epub</code><br>
      &numsp;<code>/account</code><br>
      &numsp;<code>/bookshelf</code><br>
      &numsp;<code>/wp-json/storygraph</code><br>
      &numsp;<code>/wp-json/fictioneer</code><br>
      <h4>[4 - Excludes] Do Not Cache Query Strings:</h4>
      &numsp;<code>commentcode</code><br>
      <h4>[4 - Excludes] Do Not Cache Roles:</h4>
      <ul>
        <li>- [x] Administrator</li>
        <li>- [x] Moderator</li>
        <li>- [x] Editor</li>
        <li>- [x] Author</li>
      </ul>
      <h4>[5 - ESI] ESI Settings:</h4>
      <ul>
        <li>- [x] Enable ESI</li>
        <li>- [x] Cache Admin Bar</li>
        <li>- [x] Cache Comment Form</li>
      </ul>
      <h4>[5 - ESI] ESI Nonces:</h4>
      &numsp;<code>oauth_nonce</code><br>
      &numsp;<code>fictioneer_nonce</code><br>
      &numsp;<code>fictioneer-ajax-nonce</code><br>
      <h4>[5 - ESI] Vary Group:</h4>
      <ul>
        <li>- [x] Administrator: 99</li>
        <li>- [x] Moderator: 50</li>
        <li>- [x] Editor: 40</li>
        <li>- [x] Author: 30</li>
        <li>- [x] Contributor: 20</li>
        <li>- [x] Subscriber: 0</li>
      </ul>
      <h4>[7 - Browser] Browser Cache Settings:</h4>
      <ul>
        <li>- [x] Browser Cache</li>
        <li>- [x] Browser Cache TTL: 31557600</li>
      </ul>
    </blockquote><br>
  </details>

### Recommended: Must-Use Plugins

[Must-Use Plugins](https://wordpress.org/documentation/article/must-use-plugins/) are not installed but have to be manually copied into the **wp-content/mu-plugins** folder (does not exist by default). They are always loaded, in alphabetical order, and before any other plugin or theme. This behavior can be exploited to boost performance. When you look into the Fictioneer theme folder, you will find an mu-plugins subfolder with two must-use plugins ready to be copied over.

**Disable ACF on Frontend** was created by [Bill Erickson](https://www.billerickson.net/code/disable-acf-frontend/) and disables the ACF plugin on the frontend if you installed it separately. The integrated version is disabled by default. You cannot use its functions in that case, however.

**Fictioneer 001 Fast Requests** accelerates AJAX and REST requests by disabling non-allow-listed plugins during selected theme actions. Depending on the number of plugins you have installed, this can boost your request performance significantly. However, it will prevent the plugins from working during these requests, although that has no effect on the theme’s default functionality. Be not afraid to edit the file and extend the allow list, it will not be overwritten when you update the theme. Or add your own plugin files. This is one of the best speed optimizations you can make.

If problems arise, you can just delete the plugin files.

### Warning: SEO Plugins

While search engine optimization plugins such as [Yoast](https://wordpress.org/plugins/wordpress-seo/) and [AIOSEO](https://wordpress.org/plugins/all-in-one-seo-pack/) are usually the way to go, they are not recommended here. Fictioneer already ships with a search engine optimization — not perfect, but tailored to the purpose. Third party plugins do not understand the theme, never mind web fictions. They assume everything to be topic-based articles or products, leading to faulty results unless you teach them and that requires custom code. They also lock essential features behind a subscription that Fictioneer provides for free.

And just to take a step back here and be real: SEO is important. Certainly. Unfortunately. But if you actually try to optimize your *prose* for keywords density, word complexity, sentence and paragraph length, or any other statistical insanity to beseech the great algorithm, you have a poison in your mind.

### Warning: CSS Minification/Combination

The theme’s CSS comes already minified and while additional optimizations such as combining files or filtering out *presumably* unused styles can further improve speed, it can also easily break your layout. This has been proven to be an issue with Cloudflare’s auto-minify feature, for example, which removes whitespaces in `clamp()` functions that are required for them to work. An especially insidious case that you might struggle to pinpoint as it happens during the network request, not on your own server.

## How to Configure the Fictioneer Theme

![General Settings Preview](repo/assets/settings_general_preview.jpg?raw=true)

### General Tab

Most of the theme’s configuration is found here, the options being largely self-explanatory. Please note that you will probably not need all the features available, such as Checkmarks or Follows. These are for sites with many authors or stories; publishing a weekly serial is better off saving the server resources. Some additional explanations:

* **System Email Address/Name:** Used for no-reply transactional emails, such as comment reply notifications.
* **Contact Form Receivers:** Submitted contact forms are sent to those email addresses. One per line.
* **Add consent wrappers to embedded content:** Required to be GDPR compliant if you use embeds.
* **Page Assignments:** Only set what you actually need. Used for breadcrumbs and menu items.
* **Enable Storygraph API:** Allows external services to index and search your site to reach a larger audience. Recommended.
* **Enable OAuth 2.0 authentication:** Allows visitors to register with social media accounts, but be aware of the implications!
* **Enable AJAX comment form/section:** If you have trouble with caching. Try the form first to save resources.
* **Enable AJAX user authentication:** If you have trouble with [Nonces](https://developer.wordpress.org/apis/security/nonces/) and/or users not being properly logged-in. Use this as *last resort* to bypass the cache.
* **Disable theme comment {…}:** If you want to use different comments. Disables most of the other comment options as well.

<br>

![Roles Settings Preview](repo/assets/settings_roles_preview.jpg?raw=true)

### Roles Tab

The integrated role manager to add and, edit, and remove roles. Not the most sophisticated compared to dedicated plugins, but it comes with custom capabilities tailored to the theme. Because Fictioneer offers some powerful options and tools you may want to keep away from certain user groups. If the roles have not been properly initialized when you activated the theme, you can do that under the **Tools** tab. For reference, look at the default [WordPress capabilities](https://wordpress.org/documentation/article/roles-and-capabilities/).

<details>
  <summary>New Capabilities</summary><br>

  * **Shortcodes:** Without this capability, shortcodes are stripped when you save a post.
  * **Select Page Template:** You cannot change the page template without this.
  * **Custom Page CSS:** Inject CSS into the header for an unique style. Dangerous!
  * **Custom ePUB CSS:** Inject CSS into the ePUB for an unique style. Dangerous!
  * **SEO Meta:** Show and edit the SEO meta for posts (if enabled in the settings).
  * **Make Sticky:** You can make posts and stories stick to the top in lists.
  * **Edit Permalink:** Customize the permalink slug derived from the title. Dangerous!
  * **All Blocks:** Your block options are quite limited without this, for *sanity* reasons.
  * **Story Pages:** Allows you to attach up to four pages to your stories as extra tabs.
  * **Edit Date:** Makes it possible to change the publishing date *after* publishing.
  * **Reduced Profile:** Removes clutter from the admin profile page, like the color schemes.
  * **Edit Only Others Comments:** Limits moderators to only editing comments, not all posts.
  * **Upload Limit:** Enforce the file size limit from the General Settings.
  * **Upload Restrictions:** Enforce the file type restrictions from the General Settings.
  * **Adminbar Access:** Overrides individual settings to show or hide the adminbar.
  * **Admin Panel Access:** Required to access the admin panel, including your admin profile.
  * **Dashboard Access:** Required if you want to see the dashboard admin page.
  * **Show Badge:** Shows the role name as comment badge. Can be overridden in your profile.
  * **Allow Self Delete:** Allows you to delete your own account. Default for subscribers.
  * **Privacy Clearance:** Grants access to sensible data like emails and IP addresses.
  * **Read Others Files:** Allows you to see uploaded files from *other* users.
  * **Edit Others Files:** Allows you to edit uploaded files from *other* users.
  * **Delete Others Files:** Allows you to delete uploaded files from *other* users.
  * **Manage {Taxonomy}:** Lets you see the overview list table of the taxonomy.
  * **Assign {Taxonomy}:** Lets you assign the taxonomy to your posts.
  * **Edit {Taxonomy}:** Lets you create and edit taxonomies of this type.
  * **Delete {Taxonomy}:** Lets you delete taxonomies of this type.

</details>

<br>

![Connections Settings Preview](repo/assets/settings_connections_preview.jpg?raw=true)

### Connections Tab

Anything that connects with external service providers goes here, such as the Client ID and Secret for OAuth 2.0 applications. Please refer to respective tutorials on how to set them up and always, *always* keep those credentials confidential. If you enter a [Discord webhook](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks) here, notifications about new comments will be sent directly into a channel on your server (leave free if you do not want that). This should be a hidden moderation channel because it will receive excerpts of private comments.

* [Discord Developer Portal](https://discord.com/developers/docs/topics/oauth2)
* [Twitch Developer Portal](https://dev.twitch.tv/docs/authentication/register-app)
* [Patreon Developer Portal](https://docs.patreon.com/#oauth)
* [Google Developer Portal](https://developers.google.com/identity/protocols/oauth2)

The OAuth request redirect URI should be akin to `https://your-domain.com/oauth2`, the important part being the `/oauth2` endpoint. Note that the service providers can be picky, such as rejecting an URI that includes "www" if that is not actually part of your website’s address. Use the _exact_ string you see in your browser’s address bar.

<br>

![Phrases Settings Preview](repo/assets/settings_phrases_preview.png?raw=true)

### Phrases Tab

Allows for some minor translations and changes, such as the cookie notice banner or comment reply notification email. More customization can be achieved with the theme’s [translation filter](FILTERS.md#apply_filters-fictioneer_filter_translations-strings-). But if you want to translate the theme into a new language, you will need to include the proper [translation files](https://developer.wordpress.org/plugins/internationalization/localization/) or use a plugin.

<br>

![ePUBs Settings Preview](repo/assets/settings_epubs_preview.jpg?raw=true)

### ePUBs Tab

Lists all generated ePUBs with statistics, download links, and options to delete them. File names are equal to the story’s `post_name`, which is the slug inside the permalink and *not* the title. They are cleaned of any special characters and are also used to query associated stories. If you change the permalink, they will no longer match and a new ePUB will be generated, leaving the old one orphaned. This is not terrible but takes up space.

**Failed ePUBs:** Indicated by an empty "download" file. The generation of ePUBs can fail due to several circumstances, such as missing writing permissions along the path of `wp-content/uploads/epubs` or non-conform content in the story or chapters. Unfortunately, ePUBs are rather picky regarding allowed HTML and while the converter tries to sanitize the content, this is not fail-proof. Alternatively, you can just upload a file yourself instead of relying on the converter, not limited to the ePUB format.

### SEO Tab

Only available if you enable the SEO features and no (known) SEO plugin is running. Lists all generated Open Graph meta data and schemas used by search engines and social media embeds, created and cached when a post is first visited until modified or purged. Whether these services actually display the offered data is entirely up to them. You cannot force Google to show your custom description, for example. After all, you could write *anything* in there. This tab is mostly informative, but you can purge the cached meta data or schemas if that should become necessary.

If you want to set up a default Open Graph image for search engine results and embeds, you can do that in the **Customizer** under **Site Identity**. This image will always be used if there is not a more specific one, like the thumbnail for posts.

### Tools Tab

A collection of actions to add, update, revert, fix, or purge certain items. For example, you can add a proper moderator role if missing or convert tags into genres. Everything is thoroughly explained. But the only action you will most likely need more than once is **Purge Story Data Caches**, which should be done whenever you change chapter or story settings.

If the user roles lack permissions, such as authors not being able to add stories and chapters, use the **Initialize Roles** action. This also restores the defaults if you mess something up, although it will not reset capabilities outside the theme’s scope. Most administrative capabilities are left untouched for security reasons.

### Log

A log of administrative actions performed which concern the theme.

## How to Customize the Fictioneer Theme

![Customizer HSL Sliders](repo/assets/customizer_hsl_sliders_demo.gif?raw=true)

There are two ways to customize the theme. The obvious one is the Customizer of WordPress under **Appearance > Customize**. Here you can upload a header image and logo, set a site title, change the color scheme, and modify the layout to some extend. The interface and live preview make this straightforward. If the color options are too demanding (and they are), you may want to stick to the hue, saturation, and lightness sliders. Also consult the many guides about WordPress customization.

The second way is to directly modify the templates, styles, and scripts. This is indefinitely more powerful but requires some developer skills — and you can easily break your site. The theme’s files can be modified under **Appearance > Theme File Editor**, although you should never actually do this. Always create a [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/) because any code changes you make, regardless of quality, will be overwritten again when you update the theme.

### Move the Title/Logo

![Customizer HSL Sliders](repo/assets/customizer_move_title_logo.jpg?raw=true)

In order to move the title or logo, you need a bit of custom CSS. This can be added directly under **Customize > Additional CSS**. Depending on whether you have a logo or not, you will have one of the following HTML/CSS combinations (and then some, but this is the relevant part).

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
  <div class="header__title">
    <h1 class="header__title-heading">
      <a href="#" class="header__title-link" rel="home">Title</a>
    </h1>
    <div class="header__title-tagline">Tagline</div>
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

On desktop, submenus are rendered as dropdown. On mobile, the **Navigation** only shows the top level menu items in a scrollable track, but the mobile menu is an unfolded list of all items if not specifically excluded with optional CSS classes.

* `not-in-mobile-menu`: As you can guess, this will hide the menu item in the mobile menu. However, submenu items will still be shown, so you can use this to hide superfluous dropdown parents.
* `static-menu-item`: For menu items without link. Changes the cursor and cannot be selected by keyboard (subitems can).

### Constants

Some options are not available in the settings because tempering with them can break the theme or result in unexpected behavior. Those options are defined via constants in the **function.php**. If you want to change them, you need a child [child theme](https://developer.wordpress.org/themes/advanced-topics/child-themes/). Just override them in the child theme’s own **function.php**, but only if you know what you are doing!

```php
define( 'CONSTANT_NAME', value );
```

| Constant | Type | Explanation
| :--- | :---: | :---
| CHILD_VERSION | string\|boolean | Version number of the child theme. Default `false`.
| CHILD_NAME | string\|boolean | Name of the child theme. Default `false`.
| FICTIONEER_OAUTH_ENDPOINT | string | URI slug to call the OAuth script. Default `'oauth2'`.
| FICTIONEER_EPUB_ENDPOINT | string | URI slug to call the ePUB script. Default `'download-epub'`.
| FICTIONEER_LOGOUT_ENDPOINT | string | URI slug to call the logout script. Default `'fictioneer-logout'`.
| FICTIONEER_PRIMARY_FONT_CSS | string | CSS name of the primary font. Default `'Open Sans'`.
| FICTIONEER_PRIMARY_FONT_NAME | string | Display name of the primary font. Default `'Open Sans'`.
| FICTIONEER_SITE_CHARSET | string | Charset of the site (e.g. 'UTF-8'). Default `get_bloginfo( 'charset' )`.
| FICTIONEER_SITE_LANGUAGE | string | Language of the site (e.g. 'en-US'). Default `get_bloginfo( 'language' )`.
| FICTIONEER_SITE_NAME | string | Name of the site. Default `get_bloginfo( 'name' )`.
| FICTIONEER_SITE_DESCRIPTION | string | Description of the site. Default `get_bloginfo( 'description' )`.
| FICTIONEER_TTS_REGEX | string | Splits chapter text into sentences for the text-to-speech feature. Default `'([.!?:"\'\u201C\u201D])\s+(?=[A-Z"\'\u201C\u201D])'`.
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
| FICTIONEER_STORY_FOOTER_B480_DATE | string | Story page footer date format (<= 480px). Default `"M j, 'y"`.
| FICTIONEER_COMMENTCODE_TTL | integer | How long guests can see their private/unapproved comments in _seconds_. Default `600`.
| FICTIONEER_AJAX_TTL | integer | How long to cache certain AJAX requests locally in _milliseconds_. Default `60000`.
| FICTIONEER_AJAX_LOGIN_TTL | integer | How long to cache AJAX authentications locally in _milliseconds_. Default `15000`.
| FICTIONEER_AJAX_POST_DEBOUNCE_RATE | integer | How long to debounce AJAX requests of the same type _milliseconds_. Default `700`.
| FICTIONEER_AUTHOR_KEYWORD_SEARCH_LIMIT | integer | Maximum number of authors in the advanced search suggestions. Default `100`.
| FICTIONEER_UPDATE_CHECK_TIMEOUT | integer | Timeout between checks for theme updates in _seconds_. Default `3600`.
| FICTIONEER_API_STORYGRAPH_CACHE_TTL | integer | How long Storygraph responses are cached in _seconds_. Default `3600`.
| FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE | integer | How many items the Storygraph `/stories` endpoint returns. Default 10.
| FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY | integer | Maximum number of story custom pages. Default `4`.
| FICTIONEER_CHAPTER_FOLDING_THRESHOLD | integer | Threshold before and after folding in chapter lists. Default `5`.
| FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION | integer | Expiration duration for shortcode Transients in seconds. Default `300`.
| FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT | integer | Timeout between comment count refreshes for stories in seconds. Default `900`.
| FICTIONEER_CACHE_PURGE_ASSIST | boolean | Whether to call the cache purge assist function on post updates. Default `true`.
| FICTIONEER_RELATIONSHIP_PURGE_ASSIST | boolean | Whether to purge related post caches. Default `true`.
| FICTIONEER_CHAPTER_LIST_TRANSIENTS | boolean | Whether to cache chapter lists on story pages as Transients. Default `true`.
| FICTIONEER_SHOW_SEARCH_IN_MENUS | boolean | Whether to show search page links in menus. Default `true`.
| FICTIONEER_THEME_SWITCH | boolean | Whether to show the theme switch in child themes (back to base). Default `true`.
| FICTIONEER_ATTACHMENT_PAGES | boolean | Whether to enable pages for attachments (no theme templates). Default `false`.
| FICTIONEER_SHOW_OAUTH_HASHES | boolean | Whether to show OAuth ID hashes in user profiles (admin only). Default `false`.
| FICTIONEER_DISALLOWED_KEY_NOTICE | boolean | Whether to show feedback for rejected comment content. Default `true`.
| FICTIONEER_FILTER_STORY_CHAPTERS | boolean | Whether to filter selectable chapters by assigned story. Default `true`.
| FICTIONEER_COLLAPSE_COMMENT_FORM | boolean | Whether hide comment form inputs until the textarea is clicked. Default `true`.
| FICTIONEER_API_STORYGRAPH_IMAGES | boolean | Whether to add image links to the Storygraph. Default `true`.
| FICTIONEER_API_STORYGRAPH_HOTLINK | boolean | Whether hotlinking images from the Storygraph is allowed. Default `false`.
| FICTIONEER_API_STORYGRAPH_CHAPTERS | boolean | Whether to add chapters to the Storygraph `/stories` endpoint. Default `true`.
| FICTIONEER_API_STORYGRAPH_TRANSIENTS | boolean | Whether to cache Storygraph responses as Transients. Default `true`.
| FICTIONEER_DISABLE_ACF_JSON_IMPORT | boolean | Whether to disable the ACF JSON field import. Dangerous. Default `false`.
| FICTIONEER_ENABLE_STICKY_CARDS | boolean | Whether to allow sticky cards. Expensive. Default `true`.
| FICTIONEER_ENABLE_STORY_DATA_META_CACHE | boolean | Whether to "cache" story data in a meta field. Default `true`.
| FICTIONEER_ENABLE_FRONTEND_ACF | boolean | Whether to load ACF on the frontend. Default `false`.
| FICTIONEER_ENABLE_MENU_TRANSIENTS | boolean | Whether to cache nav menus as Transients. Default `true`.
