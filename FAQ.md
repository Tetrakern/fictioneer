# Frequently Asked Questions

### Q: How do I ... in WordPress?

This FAQ is about the Fictioneer theme. If you have questions about the WordPress content management system, better visit the [official support site](https://wordpress.org/support/) or search the Internet for tutorials. There are literally thousands.

### Q: How do I set up my own site?

Assuming this is your first WordPress site, take a look at the [Installation](INSTALLATION.md) guide. If that proves insufficient, [search](https://www.google.com/search?q=wordpress+step+by+step+tutorial) the Internet for one of the many step-by-step tutorials or ask/hire someone. Just don’t take the first cheap host or offer you come across, do your own research.

### Q: Does the theme support multiple authors?

Yes, you can use the theme for multiple authors. Just enable the option to "display authors on cards and posts" under **Fictioneer > General** and assign the author role as needed. Do not make everyone an administrator! Keep also in mind that huge visitor counts will require a more powerful server. The theme is intended for individuals and small collectives.

If you want to display multiple authors **per story or chapter,** you need to check the "Enable advanced meta fields" option under **Fictioneer > General > Compatibility.** This will display several extra inputs that most sites just do not need, hence they are hidden by default. Note that the co-authors field is a comma-separated list of IDs (i.e. positive numbers), not names.

### Q: Can this be an archive for user contributed content?

Technically, yes. However, this is not recommended for several reasons. The theme lacks a frontend content submission system, which means you need to grant random people access to the admin panels. While the included role manager allows you to restrict a user’s capabilities, WordPress was never intended for unsafe authors. You will need to watch and moderate everyone, making this only viable for smaller communities with a trustworthy team.

WordPress is also not a performant choice to run community sites, requiring too many server resources. You will need a good host or your own hardware, plus caching and technical expertise. This is going to be a headache. But some people have already done it despite all warnings and it seems to work fine.

### Q: Can I use special characters in names and titles?

Probably, to an extend? The usual suspects have been tested to ensure Extended Latin is working, but you should not try to push it with zalgo text or worse. Some special characters are even actively filtered out to prevent certain attacks.

### Q: Is this a block theme with full-site editing?

No. Fictioneer was developed as standalone solution and to work out of the box, looking good and offering the best reading experience achievable. Full-site editing is the polar opposite of that goal. But you can use page builder plugins, to a certain degree.

### Q: Is the theme available in other languages?

The theme is translation-ready. Feel free to [add your own translation](https://developer.wordpress.org/apis/internationalization/localization/) to the [languages folder](/languages) under the `'fictioneer'` text-domain or use a plugin. There is a .POT template file available. Check the folder for available languages.

### Q: Why is the ePUB download button not showing up?

You probably forgot to add a preface, which is required and should contain a copyright notice. Because once something is on the Internet, it will stay on the Internet. Make sure everything is in order. Also possible is that you somehow disabled the ePUB on the story or forgot to enable the feature in the settings.

### Q: Why does the ePUB download only return an empty file?

The ePUB converter is held together by rubber band and wishful thinking, with many points of failure. That is why the feature is marked as *experimental*. Likely causes include incompatible HTML in the content, CDNs that return exotic URLs, oversized images, not enough PHP memory or missing extensions (dom, zip), missing writing/reading permissions on the server, or caching (exclude `/download-epub`).

### Q: What does the fingerprint icon on comments mean?

That is just an unique hash to distinguish commenters with the same nickname and assign the comment edit script if you are authenticated. There are always malicious actors on the Internet who may pose as others to cause strife. Guest commenters do not have a fingerprint since their submissions are inherently untrustworthy, although a [gravatar](https://gravatar.com/) can serve the same purpose.

### Q: Are there notification emails for story updates?

There are not. Emails are highly problematic, even simple transactional emails about profile changes or new replies to your comments. They are expensive operations, unsafe, and have a good chance to be flagged as spam which can discredit your domain. You do not want to send emails in bulk unless you have a dedicated subscription service. Regardless, the theme does not offer this feature. Better rely on the mailers offered by your Ko-fi, Patreon, SubScribeStar, and so forth. They are better at this.

### Q: Does the Patreon plugin work with the theme?

Apparently, yes. However, there are some factors to consider before using the plugin. The integration into the theme’s style leaves to be desired, although you could fix that with some custom CSS. More concerning is the impact on site speed, which has been reported to be severe in the past. This might have been fixed, there are no benchmarks on the matter. Unclear whether it works with cache plugins. Lastly, it is not compatible with the theme’s authentication system, instead using it’s own.

### Q: Does the Jetpack plugin work with the theme?

Partially, depending on what features you want. Anything affecting the layout (aside from Gutenberg blocks) may cause issues and will probably fit poorly into the style without custom adjustments. The comment form is working but disables several theme features. The post subscription feature will not work for stories, chapters, and any other custom post types — only posts, which is by design. Analytics and dashboard features should be fine, though.

### Q: What happened to Fictioneer version 1 to 4?

They were never released or distributed, version 5 is the first public one.

### Q: Why do the OAuth links return a 404 error?

After activating the feature, you need to flush the permalinks. You can do this under **Settings > Permalinks** by just saving, you do not need to change anything.

### Q: Can I use stories without chapters as oneshots?

Nobody can stop you from doing that, but it is wrong. Stories do not have the same capabilities as chapters and the theme does not understand the concept. There will be no formatting options, bookmarks, text-to-speech, suggestions, comments, and so forth. SEO data and API responses may also be messed up. If you can live with that, go ahead.

### Q: Can I use the theme for Japanese, Korean, Chinese, and other writing systems?

Apparently, even though this was never considered during development. WordPress is multi-lingual by default and the theme can be translated, which can be done with certain plugins too. You can change the word count script to count characters instead, with an optional multiplier for approximation. You will most likely need to add a [custom font](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#custom-fonts), though.

**Resources:**
* Fictioneer > General > Chapters & Stories > Count characters instead of words
* Fictioneer > General > Chapters & Stories > Multiply the displayed word counts with \[\_\_\_\]
* Fictioneer > General > Chapters & Stories > Calculate reading time with \[\_\_\_\] words per minute
* [Installation guide for custom fonts](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#custom-fonts)
  * [Noto Sans JP](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-jp): Noto Sans variant for Japanese.
  * [Noto Sans KR](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-kr): Noto Sans variant for Korean.
  * [Noto Sans TC](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-tc): Noto Sans variant for Traditional Chinese.
  * [Noto Sans SC](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-sc): Noto Sans variant for Simplified Chinese.
