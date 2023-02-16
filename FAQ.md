# Frequently Asked Questions

#### Q: How do I ... in WordPress?

This FAQ is about the Fictioneer theme. If you have questions about the WordPress content management system, better visit the [official support site](https://wordpress.org/support/) or search the Internet for tutorials. There are literally thousands.

#### Q: How do I set up my own site?

Assuming this is your first WordPress site, take a look at the [Installation](INSTALLATION.md) guide. If that proves insufficient, [search](https://www.google.com/search?q=wordpress+step+by+step+tutorial) the Internet for one of the many step-by-step tutorials or ask/hire someone. Just don’t take the first cheap host or offer you come across, do your own research.

#### Q: Does the theme support multiple authors?

Yes, you can use the theme for multiple authors. Just enable the option to "display authors on cards and posts" under **Fictioneer > General** and assign the author role as needed. Do not make everyone an administrator! Keep also in mind that huge visitor counts will require a more powerful server. The theme is intended for individuals and small collectives.

#### Q: Can this be an archive for user contributed content?

Well yes, but actually no. While this is technically possible, the theme lacks a frontend submission system for user content and you do not want to grant random people access to the admin panel. WordPress is also not a performant choice to run community sites, requiring too many server resources.

#### Q: Can I use special characters in names and titles?

Probably, to an extend? The usual suspects have been tested to ensure Extended Latin is working, but you should not try to push it with zalgo text or worse. Some special characters are even actively filtered out to prevent certain attacks.

#### Q: Is this a block theme with full-site editing?

No. Fictioneer was developed as standalone solution and to work out of the box, looking good and offering the best reading experience achievable. Full-site editing is the polar opposite of that goal.

#### Q: Is the theme available in other languages?

The theme is translation-ready but at the point of writing, only English is available. Feel free to [add your own translation](https://developer.wordpress.org/apis/internationalization/localization/) to the **languages** folder under the `fictioneer` text-domain or use a plugin. Be aware that there are JavaScript translations as well.

#### Q: Why is the ePUB download button not showing up?

You probably forgot to add a preface, which is required and should contain a copyright notice. Because once something is on the Internet, it will stay on the Internet. Make sure everything is in order. Also possible is that you somehow disabled the ePUB on the story or forgot to enable the feature in the settings.

#### Q: Why does the ePUB download only return an empty file?

The ePUB converter is held together by rubber band and wishful thinking, with many points of failure. That is why the feature is marked as *experimental*. Likely causes include incompatible HTML in the content, CDNs that return exotic URLs, oversized images, not enough PHP memory or missing extensions (dom, zip), missing writing/reading permissions on the server, or caching (exclude `/download-epub`).

#### Q: What does the fingerprint icon on comments mean?

That is just an unique hash to distinguish commenters with the same nickname and assign the comment edit script if you are authenticated. There are always malicious actors on the Internet who may pose as others to cause strife. Guest commenters do not have a fingerprint since their submissions are inherently untrustworthy, although a [gravatar](https://gravatar.com/) can serve the same purpose.

#### Q: Are there notification emails for story updates?

There are not. Emails are highly problematic, even simple transactional emails about profile changes or new replies to your comments. They are expensive operations, unsafe, and have a good chance to be flagged as spam which can discredit your domain. You do not want to send emails in bulk unless you have a dedicated subscription service. Regardless, the theme does not offer this feature. Better rely on the mailers offered by your Ko-fi, Patreon, SubScribeStar, and so forth. They are better at this.

#### Q: Does the Patreon plugin work with the theme?

This has not been tested but most likely not.

#### Q: Does the Jetpack plugin work with the theme?

Partially, depending on what features you want. Anything affecting the layout (aside from Gutenberg blocks) may cause issues and will probably fit poorly into the style without custom adjustments. The comment form is working but disables several theme features. The post subscription feature will not work for stories, chapters, and any other custom post types — only posts, which is by design. Analytics and dashboard features should be fine, though.
