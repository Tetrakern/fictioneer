# Documentation

This documentation is about the Fictioneer theme. If you need help with WordPress in general, take a look at the [official documentation](https://wordpress.org/support/category/basic-usage/) or search the Internet for one of the many tutorials. For the installation, look [here](INSTALLATION.md) first and then come back once you are done.

Click the outline toggle in the top-right corner to see the table of contents.

## Front Page

You may want to set up a front page like the demo site or make it a landing page in case of a single-story site. Both can be achieved with blocks, shortcodes, and some custom CSS or HTML if necessary. Obviously, you can always add a custom page template in your child theme if you have the skill for that, which can look like pretty much anything.

Under **Settings > Reading**, set **Your homepage displays** to "A static page" and assign your **Homepage** and **Posts page**. Create new pages if you did not already, give them sensible names. For your **Homepage**, choose the "No Title Page" or "Story Page" page template.

The "Story Page" template is for single-story sites and has more shortcode options, plus meta fields for the story ID and header. Alternatively, you can choose the "Story Mirror" template, which will mirror the story post of the specified story ID. If you want to treat your Homepage as singular page and not show the actual story post, you can set a redirect in the story to your base address (enable the advanced meta fields in the settings).

For simplicity, here is the copied content of the [demo homepage](https://fictioneer-theme.com/) and [demo story page](https://fictioneer-theme.com/story-page/). Put that into the code editor view and adjust it as needed (the IDs will be obviously different for you). When you switch back to the visual editor, everything should be properly formatted as blocks.

<details>
  <summary>Demo Homepage</summary><br>

```html
<!-- wp:shortcode -->
[fictioneer_latest_posts count="1"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:shortcode -->
[fictioneer_article_cards per_page="2" ignore_sticky="true"]
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

<details>
  <summary>Demo Story Page</summary><br>

```html
<!-- wp:image {"id":24,"width":"300px","sizeSlug":"large","linkDestination":"none","align":"right","style":{"border":{"width":"0px","style":"none"}},"className":"is-style-default"} -->
<figure class="wp-block-image alignright size-large is-resized has-custom-border is-style-default"><img src="https://res.cloudinary.com/dmhr3ab5n/images/w_640,h_1024,c_scale/v1674220342/fictioneer-demo/katalepsis_cover/katalepsis_cover.jpg?_i=AA" alt="Katalepsis Cover" class="wp-image-24" style="border-style:none;border-width:0px;width:300px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Until she meets Raine and Evelyn, that is — self-proclaimed bodyguard and bad-tempered magician — and learns she’s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Heather plunges into a world of eldritch magic and fanatic cultists, trying to stay alive, stay sane, and deal with her own blossoming attraction to dangerous women. But being ‘In The Know’ isn’t all terror and danger. Sometimes the monsters wear nice dresses and stick around for afternoon tea. Sometimes you find you have more in common with them than you think. Perhaps this is Heather’s chance to be something more than the defeated husk she’d grown up as, to find real friendship and meaning among things like herself – and perhaps, out there on the rim of the possible, to bring her twin sister back from the dead.</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"is-style-default"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-default"/>
<!-- /wp:separator -->

<!-- wp:paragraph -->
<p>Katalepsis is an Ancient Greek word which means ‘comprehension’, or perhaps more accurately, ‘insight’.<br><br>Katalepsis is a serial web novel about cosmic horror and human fragility, urban fantasy and lesbian romance, set in a sleepy English university town.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>New chapters are currently posted once a week, on Saturdays.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>If you just finished the official ebook or audiobook of Volume I, the story resumes&nbsp;<a href="https://katalepsis.net/2019/09/07/no-nook-of-english-ground-5-1/">here</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>If you are enjoying the story and want to see more, please consider&nbsp;<a href="https://www.patreon.com/hazelyoung">donating via the Patreon page!</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Front cover art by&nbsp;<a href="https://noctilia.artstation.com/">Noctilia</a>, header art by&nbsp;<a href="https://www.deviantart.com/yivels">Yivel</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><em>Disclaimer/warning: Please note that Katalepsis is intended for a mature audience. This is a horror story, after all. For more information, see the FAQ&nbsp;<a href="https://katalepsis.net/faq/">here</a>.</em></p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[fictioneer_story_actions story_id="13" follow="0" reminder="0"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height":"2rem"} -->
<div style="height:2rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2 class="wp-block-heading">First &amp; Latest Chapter</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_chapters count="2" post_ids="29,92" orderby="post__in" spoiler="true" vertical="true" seamless="true" aspect_ratio="4/1" type="simple" source="0" lightbox="0"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height":"1rem"} -->
<div style="height:1rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2 class="wp-block-heading">[fictioneer_story_data story_id="13" data="chapter_count"] Chapters</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_chapter_list story_id="13" group="mind; correlating" heading="mind; correlating"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height":"1.5rem"} -->
<div style="height:1.5rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:shortcode -->
[fictioneer_chapter_list story_id="13" group="providence or atoms" heading="providence or atoms"]
<!-- /wp:shortcode -->

<!-- wp:html -->
<div style="margin-top: 1.5rem;"><a href="/katalepsis-table-of-contents/" class="button" style="width: 100%; background: var(--chapter-li-background, var(--content-li-background)); color: var(--fg-700); padding: 1.25rem; display: grid; place-content: center; border: none;">More Chapters</a></div>
<!-- /wp:html -->

<!-- wp:shortcode -->
[fictioneer_story_comments story_id="13" header="true"]
<!-- /wp:shortcode -->
```

</details>

The demo story page uses some [custom page CSS](#extra-meta-fields) to change the page background. You can also do this globally under **Appearance > Customize**, but this is one way to modify individual pages.

```css
.main__background {
  background-color: var(--site-bg-color);
  box-shadow: none;
}
```

**Story Page Shortcodes:**
* [Story Actions](#story-actions-shortcode)
* [Story Section](#story-section-shortcode)
* [Story Comments](#story-comments-shortcode)
* [Story Data](#story-data-shortcode)

## Menus

The theme has two menu locations: Navigation and Footer Menu. You can create and assign menus under **Appearance > Menu**; they do not exist by default. For detailed instructions, refer to the [WordPress Codex Menu User Guide](https://codex.wordpress.org/WordPress_Menu_User_Guide). Note that the mobile menu will unfold nested menus, while the mobile navigation bar will display only the top-level items, depending on the mobile style you have chosen. The footer menu is not configured for nested items. Too many top-level items may look bad or break the layout.

**Optional CSS classes:**
* `not-in-mobile-menu` - Prevents menu items from showing up in the mobile menu.
* `static-menu-item` - Makes the menu item unclickable; good for submenu headers.
* `only-admins|-editors|-moderators|-authors` - Restricts the menu item to certain roles.
* `hide-if-logged-in` - Hides the menu item if the user is logged in.
* `hide-if-logged-out` - Hides the menu item if the user is logged out.

### Taxonomy Submenus

In addition to the Taxonomies page template, you can also add a submenu for each taxonomy in the main navigation. This works for categories, tags, genres, fandoms, characters, and warnings. To do this, add a custom link as a menu item with `#` as the link, then assign it **one** of the following trigger CSS classes (check the screen options if you cannot see the input). This should work on all levels, but it is recommended to keep it at the top level. The menu link and submenu will only be visible on desktop viewports.

![Genres Submenu](repo/assets/genres_submenu.png?raw=true)

**Menu classes (use one per menu item):**
* `trigger-term-menu-categories` - Submenu for categories.
* `trigger-term-menu-tags` - Submenu for tags.
* `trigger-term-menu-genres` - Submenu for genres.
* `trigger-term-menu-fandoms` - Submenu for fandoms.
* `trigger-term-menu-characters` - Submenu for characters.
* `trigger-term-menu-warnings` - Submenu for warnings.

<details>
  <summary>Screenshot from the admin menu interface</summary>

![Genres Submenu Setup](repo/assets/menu_custom_link_genres_submenu.png?raw=true)

</details>

<br>

**Optional CSS classes:**
* `columns-2|4|5` - Change the number of columns to 2, 4, or 5 (default is 3).

**Optional custom CSS:**
Due to its size, the taxonomy submenu can cause layout issues depending on where the parent item is placed. There are too many cases to consider individually, so here is some CSS for you to modify as needed. Add this CSS to the [Custom CSS section](https://wordpress.org/documentation/article/customizer/#additional-css) in the Customizer, and only keep the properties you actually change.

<details>
  <summary>Show default CSS definitions</summary>

```css
.nav-terms-submenu {
  --gap: 0px;
  --width: 500px;
  --columns: 3;
  font-size: 15px;
  width: 1000px;
  max-width: min(calc(100vw - 20px), calc(var(--width) + var(--gap) * (var(--columns) - 1)));
  /*
  How to move the menu horizontally:
  transform: translateX(-25%);

  Align the menu to the right:
  right: 0;
  */
}

.nav-terms-submenu__note {
  text-transform: uppercase;
  font-family: var(--ff-note);
  font-size: 12px;
  font-weight: 600;
  padding: .75rem 1rem 0;
  opacity: .5;
}
```

</details>

## Sidebar

You can enable the optional sidebar under **Appearance > Customize > Layout**, choosing either left or right alignment along with other options. Typically, this also requires some manual adjustments to the layout. Increasing the site width is recommended to accommodate the new column; 1036px is a good start for a 256px wide sidebar (leaving 700px content space). Note that the sidebar will only show up once you add widgets to it.

Enabling the sidebar also reduces the default page padding, which can be overridden further down with custom layout properties. If space becomes an issue, consider reducing the horizontal page padding to zero and turning off the page background for an open site appearance.

**Notes:**
* The Latest Posts widget always hides the thumbnail, regardless of block settings.
* Use the optional `no-theme-style` CSS class to remove the widget styling if needed.
* The style under **Appearance > Widgets** is NOT fully representative of the frontend.

## Stories

Stories are added under **Stories > Add New**. Required fields are the short description, status, and age rating. You should be thorough with the setup, especially the taxonomies if you have more than a few stories on your site, because they can be searched for. Just avoid adding excessive lists of tags. Also note that stories are not supposed to be used like chapters, for example as oneshot, because they lack all chapter features, including comments.

![Story Header](repo/assets/story_explanation_1.jpg?raw=true)

The layout will adjust itself if certain fields are left empty, such as the cover image or taxonomies. With a blank title, the date and time will be used instead. Cover images are displayed with an aspect ratio of 2:3, although the image itself does not need to follow these dimensions as it will be cropped from the center.

![Story Chapter List](repo/assets/story_explanation_2.jpg?raw=true)

The share, feed, and action buttons are displayed depending on your theme settings. The Blog tab lists 160 characters long excerpts of the latest posts associated with the story via category. Up to four custom pages can be added as extra tabs, with any content, which requires them to have the short name field. Chapters assigned to the story can be added and sorted in the editor, but chapter groups and icons are assigned in the chapters.

**Subscribe:** Opens a popup menu with links to any support campaigns (Patreon, Ko-fi, and SubscribeStar) as well as the RSS aggregator services [Feedly](https://feedly.com/) and [Inoreader](https://www.inoreader.com/). There is no default email subscription system.

**Follow & Read Later:** These buttons belong to the optional Follows and Reminders features, allowing logged-in subscribers to better track stories. This is mainly for sites that host a large number of stories.

![Story Cards](repo/assets/story_explanation_3.jpg?raw=true)

Story cards are more compact story displays meant for browsing, collapsed even further on small viewports. Instead of the content, only the *first paragraph* of the short description will be shown. Make sure to write something appealing, it may be the only chance your story gets to catch attention. Tags are normally not rendered to save space, but you can change that in the settings.

Story cards are used in the Stories [page template](https://wordpress.org/support/article/pages/#page-templates), collections, search, and featured lists in posts.

### Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| Short Description | Content | The short description is used in the story list cards.
| Chapters | List | Add and sort chapters assigned to the story. Assignment is done in the chapters.
| Custom Pages | List | Add up to four pages as extra tabs. Requires the short name field to show up.
| Upload Ebook | File | Upload an epub, mobi, ibooks, azw, azw3, kf8, kfx, pdf, iba, or txt file.
| ePUB Preface | Content | Disclaimers/etc. for generated ePUBs. Required for the download button to show up.
| ePUB Afterword | Content | Will be appended after the last chapter in generated ePUBs.
| ePUB Custom CSS | Text | Inject custom styles into the generated ePUB. For advanced users.
| Taxonomies (Various) | List | Genres, fandoms, characters, warnings, tags, and categories (include story name).
| Cover Image | Image | Cropped to an aspect ration of 2:3 from the center.
| Status | Select | Choose between ongoing, completed, oneshot, hiatus, and cancelled.
| Age Rating | Select | Choose between everyone, teen, mature, and adult.
| Co-Authors (A) | List | List of co-authors. They must be registered users, but dummies will do.
| Copyright Notice | String | Line below the content to declare copyrights if necessary.
| Top Web Fiction Link | URL | Link to your story on [Top Web Fiction](https://topwebfiction.com/).
| Sticky in lists | Check | Stick the story to the top of the first page in lists.
| Hide story in lists | Check | Hide the story in lists; it can still be accessed with the link.
| Hide thumbnail on story page | Check | Hide the cover image on the page but not in lists.
| Hide tags on story page | Check | Hide *all* taxonomies except warnings on the page but not in lists.
| Hide chapter icons | Check | Hide chapter icons.
| Disable collapsing of chapters | Check | Disable collapsing of long chapter lists (13+ as 5\|n\|5 per group).
| Disable chapter groups | Check | Disable chapter groups for the story altogether.
| Disable ePUB download | Check | Disable ePUB downloads for the story everywhere.
| Custom Story CSS | Text | Inject custom styles for the story and chapters. For advanced users.
| Redirect Link (A) | URL | Redirect to a different URL when the post is accessed. Make sure you know what you are doing.
| Support Links (Various) | URL | Links to subscription campaigns. Falls back to the author’s profile if left blank.

<sup>**(A)** for Advanced: These meta fields are hidden unless you check the "Enable advanced meta fields" option under **Fictioneer > General > Compatibility.** Most sites just do not need these.</sup>

### eBooks/ePUBs

A manually uploaded eBook will always supersede an automatically generated ePUB on the site, as this is a deliberate action. Which also means you need to keep it up-to-date yourself and there are no download statistics. If you want the generated ePUB, you need to fill the Preface content for the story, which should contain copyrights and disclaimers. Because once a file is on the Internet, it will stay on the Internet. Make sure everything is legally sound before that.

**Supported:** Epubs only support paragraphs, headings, lists, tables, blockquotes, pullquotes, images, spacers, and custom HTML at your own peril. Anything else will be filtered out, such as videos.

**Sensitive Content:** You can mark sensitive content in chapters and provide an alternative, which users can choose from. Generated ePUBs always use the sensitive (uncensored) content, not the alternative if provided.

#### Example Disclaimer for Originals:
> This is a work of fiction. Names, characters, businesses, events and incidents are the products of the author’s imagination. Any resemblance to actual persons, living or dead, or actual events is purely coincidental.
>
> Copyright &#169; `AUTHOR`. All rights reserved.

#### Example Disclaimer for Fanfictions:
> This is a work of fan fiction and not written for profit. Names, characters, businesses, events and incidents are the products of the author’s imagination. Any resemblance to actual persons, living or dead, or actual events is purely coincidental. Any trademarked characters and elements used belong to their respective copyright holders, who bear no responsibility for this work.
>
> Original Content Copyright &#169; `AUTHOR`. All rights reserved.

## Chapters

Chapters are added under **Chapters > Add New**. The only required field is the chapter icon, which is pre-selected by default (book). But you need to select a story if you want the chapter to show up in said story’s chapter list. This is not limited to your own stories, so you can publish guest chapters for others, although the owners still need to list them. As with stories, you should be thorough with the setup.

![Chapter List Item](repo/assets/chapter_explanation_1.jpg?raw=true)

The display of a chapter listed on a story page is controlled from within the chapter. The icon, warning, and chapter group are assigned here, although icons can be disabled globally or per story. Make sure to spell the chapter group correctly each time, because there is no hand-holding and different names result in different groups. Groups can also cause chapters to be reordered if not in sequence, but the order within a group is still derived from the story’s chapter list. You need at least two groups for groups to be displayed and ungrouped chapters will be collected under "Unassigned".

**Checkmarks:** These icon buttons belong to the optional Checkmarks feature, allowing logged-in subscribers to mark chapters and stories as read. This is mainly for sites that host a large number of stories.

![Chapter Screen](repo/assets/chapter_explanation_2.jpg?raw=true)

The fullscreen toggle is not available on iOS, which at the time of writing does not support the fullscreen API. The navigation buttons are derived from the story’s chapter list. You can open the paragraph tools by clicking on a paragraph; Bookmarks, Suggestions, and Text-to-Speech (TTS) must be enabled in the settings first. Bookmarks are per chapter and linked to a paragraph, the color is only a gimmick and does _not_ indicate you have more than one.

**Formatting Modal:** Opened with the Formatting button. Allows readers to customize how chapters are displayed, including: site brightness, site saturation, site width, font size, letter spacing, line height, paragraph spacing, font saturation, font family, font color, and font weight as well as toggles for text indent, text justify, light/dark mode, paragraph tools, author notes, comments, and sensitive content.

![Sensitive Content Warning](repo/assets/sensitive_content_warning.jpg?raw=true)

This notice appears above the title if you add a chapter warning, not to be confused with the content warning taxonomy. The warning is also shown in the chapter list of the story. Keep it short, there is not much space. You can change the color and add an additional explanation as well. The toggle allows to hide any sensitive content marked with the `sensitive-content` CSS class and show an alternative marked with `sensitive-alternative` if provided.

### Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| Story | Select | The story the chapter belongs to. Required if you want it listed.
| Card/List Title | String | Alternative title meant to be suitable for cards and lists with little space.
| Group | String | Chapter group assignment. Mind the spelling and order of chapters.
| Foreword | Content | Foreword rendered above the chapter title.
| Afterword | Content | Afterword rendered below the chapter content.
| Password Note | Content | Optional note if there is a password requirement.
| Taxonomies (Various) | List | Genres, fandoms, characters, warnings, tags, and categories (include story name).
| Chapter Cover Image | Image | Cropped to an aspect ration of 2:3 from the center. Defaults to the story cover.
| Excerpt | Text | Chapter excerpt used in cards. If empty, part of the content will be used.
| Icon | String | Free [Font Awesome](https://fontawesome.com/search) class string. Defaults to `fa-solid fa-book`.
| Text Icon (A) | String | Overrides icon with a text string, good for combining with symbol fonts.
| Short Title (A) | String | Optional short chapter title, not used by default (intended for child themes).
| Prefix (A) | String | Prepended to the title in chapter lists. Not used in generated ePUBs.
| Co-Authors (A) | List | List of co-authors. They must be registered users, but dummies will do.
| Age Rating | Select | Choose between everyone, teen, mature, and adult.
| Warning | String | _Short_ warning displayed in chapter lists and above the chapter title.
| Warning Notes | Text | Additional warning notes rendered above the chapter title.
| Unlisted (but accessible with link) | Check | Hide the chapter in all lists, but keep it accessible with the link.
| Do not count as chapter | Check | Exclude the chapter from chapter counts.
| Hide title in chapter | Check | Hide the title and author on chapter pages.
| Hide support links | Check | Hide support links at the end of the chapter.

<sup>**(A)** for Advanced: These meta fields are hidden unless you check the "Enable advanced meta fields" option under **Fictioneer > General > Compatibility.** Most sites just do not need these.</sup>

### Chapter Titles

As you can take away from the meta fields, there are several optional chapter titles and title-related fields. This can be confusing, so here is where and how these fields are actually used. Blank fields are obviously not rendered.

* **Small Cards (Shortcodes):** List Title *or* Title
* **Large Chapter Cards (List Templates):** Title *and* List Title (on mobile)
* **Large Story Cards (List Templates):** List Title *or* Title
* **Chapter Index (Popup/Mobile):** List Title *or* Title
* **Chapter Lists (Story/Shortcode):** Prefix + Title

### Text-To-Speech Engine

Must be enabled in the settings and is started from the paragraph tools. Makes use of the free [Web Speech API](https://developer.mozilla.org/en-US/docs/Web/API/Web_Speech_API) that all modern browsers support, which can be wonky at times but produces surprisingly decent results. Primarily meant as accessibility feature for the reading-impaired. Absolutely _not_ fail-proof and depends on the browser and operating system; additional permissions may be necessary on the playback device (this is outside your control).

**Supported:** Only first level children of the content container are read, and only paragraphs and headings. If you want tables, quotes, and more to be read, add the desired output as paragraph with the `hidden` CSS class.

**Note:** Browsers have only one instance of this engine. That means if you have another one running in a different tab, perhaps a different site altogether, they will interfere with each other. You can even control the output of other sites.

![Text-To-Speech Interface](repo/assets/tts.jpg?raw=true)

## Collections

Collections are added under **Collections > Add New**. Required fields are the short description and items featured in the collection, which may include posts, pages, stories, chapters, recommendations, and even other collections. The purpose is to group different items with a common context, such as sequels or stories set in a shared universe.

![Collection Screen](repo/assets/collection_explanation.jpg?raw=true)

### Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| Card/List Title | String | Alternative title meant to be suitable for cards and lists with little space.
| Collection Items | List | Add and sort posts, pages, stories, chapters, recommendations, and collections.
| Short Description | Content | The short description is used in the collection list cards.
| Taxonomies (Various) | List | Genres, fandoms, characters, warnings, tags, and categories (include story name).
| Collection Cover Image | Image | Cropped to an aspect ration of 2:3 from the center.

## Recommendations

Recommendations are added under **Recommendations > Add New**. Required fields are the author of the recommended story, primary URL, general URLs, and "one sentence" abbreviation as description on small cards. Large cards use the normal excerpt. Recommendations are meant to be personal promotions of great stories by your fellow authors and to shine light on hidden gems.

### Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| One Sentence | String | 150 characters or less "elevator pitch" to describe the story.
| Author | String | The author of the recommended story.
| Primary URL | String | Primary link to the recommendation or author’s website.
| URLs | Text | Special formatted list of links to the recommendation, one per line.
| Support | Text | Special formatted list of links to support the author, one per line.
| Taxonomies (Various) | List | Genres, fandoms, characters, warnings, tags, and categories.
| Recommendation Cover Image | Image | Cropped to an aspect ration of 2:3 from the center.

### Example Sentences

Think of the sentence as elevator pitch, something you can tell within a few seconds to get the point across. Skip the details, hint at the plot, describe the concept — the story has all the time to tell itself later. Because more often than not, readers will only glimpse at a story while browsing. Recommendations are not prominently featured on _your_ site, after all.

> Rebellious heiress and her genius friend commit high-tech heists in a doomed city on the edge of tomorrow.

> Schoolgirl gets reincarnated into a fantasy world, though not as heroine but as tentacle monster!

> Haunted student discovers her nightmares of gods and horrors from beyond reality are no hallucinations after all.

> Girl from the slums discovers her talent for necromancy and learns to embrace being an existential terror.

> Two women forge an unlikely bond and explore a simple question: is selling your body the same as selling yourself?

> Reanimated girls from different eras roam the ruins of civilization on the scarred corpse of Earth.

> Dying magical girl eats the hearts of living nightmares to cheat death.

## Pages

Pages work the same as always in WordPress, just with some additional fields and template options. [Change the template](https://wordpress.org/support/article/pages/#page-templates) in the settings sidebar. You can assign these template pages to certain tasks under **Fictioneer > General > Page Assignments**.

### Page Templates

* **Chapters:** Shows a list of all visible chapters ordered by publishing date, descending.
* **Stories:** Shows a list of all visible stories ordered by publishing date, descending.
* **Collections:** Shows a list of all visible collections ordered by publishing date, descending.
* **Recommendations:** Shows a list of all visible recommendations ordered by publishing date, descending.
* **Bookmarks:** Shows bookmarks without the need for a shortcode. Cache compatible.
* **Bookshelf:** Shows paginated lists of a user’s Follows, Reminders, and finished stories.
* **Bookshelf AJAX:** Cache compatible version of the Bookshelf, fetching the content after the page has loaded.
* **No Title Page:** Default page template but without the heading. Good for a frontpage.
* **Story Mirror:** Renders the page exactly like a story (set via meta field).
* **Story Page:** Front page template for single-story sites, allowing the use of all `[fictioneer_story_*]` shortcodes.
* **Author Index:** Shows an index of all authors sorted by the display name’s first letter.
* **Author Index (Advanced):** The same as the Author Index page template, but with additional meta data.
* **Index:** Shows an index of all stories sorted by the title’s first letter.
* **Index (Advanced):** The same as the Index page template, but with additional meta data.
* **Taxonomies:** Shows details about all taxonomies used on the site, with count and definition (if provided).
* **User Profile:** Frontend account profile to keep users out of the admin. Must never be cached!
* **Canvas (Page):** Renders the page container blank without comments. Meant to be used with page builder plugins.
* **Canvas (Main):** Renders the main container without page or padding. Meant to be used with page builder plugins.
* **Canvas (Site):** Renders a completely blank site. Meant to be used with page builder plugins.

### Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| Short Name | String | Shortened name of the page required for custom tabs in stories.
| Filter & Search ID | String | Custom identifier to be used with plugin. Does nothing on its own.
| Story ID | String | ID of a story post. Only used by the "Story Page/Mirror" page templates.
| Show story header | Check | Renders the story header for the given story ID. "Story Page" template only.

### Customize Stories Template Query

You may want to only list selected stories, for example those belonging to a certain category. While there isn’t a convenient meta field for this due to the numerous possible parameters, you can customize the output using the [fictioneer_filter_stories_query_args](FILTERS.md#apply_filters-fictioneer_filter_stories_query_args-query_args-post_id-) filter in a child theme. Ensure that the name of your filter function is unique, or else.

```php
/**
 * Modifies the Stories template query
 *
 * @since x.x.x
 * @link https://developer.wordpress.org/reference/classes/wp_query/
 *
 * @param array $query_args  Query arguments.
 * @param int   $post_id     Post ID of the page.
 *
 * @return array Modified query arguments
 */

function child_query_stories_by_category( $query_args, $post_id ) {
  // Use the post ID found in the editor URL to target specific pages
  if ( $post_id == 35 ) {
    // Add your desired query parameter
    $query_args['category_name'] = 'some-category-slug';
  }

  // Optional: Add conditions for other post IDs
  // if ( $post_id == 40 ) {
  //   $query_args['tag'] = 'stuff';
  // }

  // Continue filter
  return $query_args;
}
add_filter( 'fictioneer_filter_stories_query_args', 'child_query_stories_by_category', 10, 2 );
```

## Shared Options

These fields and options are available in most post types, which does not mean they make sense everywhere. Some require certain feature to be enabled and set up, such as the Patreon integration.

### Extra Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| Landscape Image | Image | Alternative image for when the rendered width is greater than the height.
| Header Image | Image | Overrides the default header image, passed down to by chapters in the case of stories.
| Custom Page CSS | Text | Inject custom styles into the page (not passed down to chapters).
| Patreon Tiers | List | Patreon tiers that ignore the password protection (if set up).
| Patreon Amount Cents | Number | Patreon pledge threshold to ignore the password protection (if set up).
| Expire Post Password | Date | Choose a date and time to automatically remove the post password (set your time zone).
| Chapters inherit Patreon settings | Check | Chapters inherit any tiers/cents set for the story.
| Disable new comments | Check | Disable new comments but keep the current ones visible.
| Disable page padding | Check | Removes the page padding, which may be useful for page builders.
| Disable sidebar | Check | Disable the sidebar on this post or page (if any).

### SEO & Meta Tags

Metadata for search engine results, schema graphs, and social media embeds. If left blank, defaults will be derived from the post content. You can use `{{title}}`, `{{site}}`, and `{{excerpt}}` as placeholders. Titles should not exceed 70 characters but this is not enforced. The Open Graph image is either set manually (click on the box) or defaults to the post thumbnail, parent thumbnail, or site default in that order. Whether these services actually display the offered data is entirely up to them. After all, you could write anything in there.

![SEO Appearance](repo/assets/seo_appearance.jpg?raw=true)

### Support Links

A collection of optional support links: Patreon, Ko-fi, SubscribeStar, PayPal, and a generic donation link for anything else. They are displayed in several places, such as under each chapter unless disabled. You can set different links per chapter and story, defaulting to the parent or author profile if left empty.

## Additional CSS Classes

You can add additional CSS classes to paragraphs and other blocks for extra styles and functions. Just select a block in the editor and scroll down to the **Advanced** section in the [block settings](https://wordpress.org/support/article/working-with-blocks/#block-settings) panel. This can be your own or classes provided by the theme, which are highlighted in the editor as shown in the image.

You can also apply additional classes to single words or phrases. Switch to the code editor in the options menu (the three dots in the top-right corner) and wrap the desired part like `<span class="spoiler">word</span>`. Make sure to properly close the tag and do not span over multiple blocks unless you know what you are doing, in which case you would not need this guide.

![Additional CSS Classes](repo/assets/additional_css_classes_1.jpg?raw=true)

| Class | Effect
| :-- | :--
| `sensitive-content` | Hides a block if the **Hide Sensitive Content** chapter formatting option is active.
| `sensitive-alternative` | Shows a block if the **Hide Sensitive Content** chapter formatting option is active.
| `spoiler` | Blanks out a block (or span) until clicked to be revealed.
| `hidden` | Hides a block. Useful for text-to-speech if there is an image or other non-readable element.
| `outside-epub` | Hides a block inside ePUBs. Combine with `inside-epub` to have two variants.
| `inside-epub` | Hides a block outside ePUBs. Combine with `outside-epub` to have two variants.
| `skip-tts` | Blocks with this class will be ignored by the text-to-speech engine. Does not work on spans.
| `skip-tools` | Prevents the paragraph tools from being toggled. Can be used on the paragraph or a span inside.
| `show-if-bookmarks` | Must be used together with `hidden`, which is removed if bookmark cards are present (via shortcode).
| `no-indent` | Suppresses text indentation regardless of settings.
| `list` | Applies list styles if missing.
| `link` | Applies link styles if missing.
| `esc-link` | Prevents link styles from being applied.
| `no-wrap` | Prevents whitespaces from being wrapped to the next line.
| `full-width` | Forces blocks to be as wide as the space allows. Works well with tables.
| `min-480` | Forces blocks to be at least 480px wide regardless of space. Works well with tables.
| `min-640` | Forces blocks to be at least 640px wide regardless of space. Works well with tables.
| `min-768` | Forces blocks to be at least 768px wide regardless of space. Works well with tables.
| `only-admins` | Makes element only visible for administrators.
| `only-editors` | Makes element only visible to editors or higher.
| `only-moderators` | Makes element only visible to moderators or higher.
| `only-authors` | Makes element only visible to authors or higher.
| `overflow-x` | Adds horizontal scrolling if a block is too wide. Not necessary on tables.
| `no-auto-lightbox` | Prevents the lightbox script from being applied if added to an `<img>` element.
| `hide-below-desktop` | Hides element below viewport widths of less than 1024px.
| `hide-below-tablet` | Hides element below viewport widths of less than 768px.
| `hide-below-640` | Hides element below viewport widths of less than 640px.
| `hide-below-480` | Hides element below viewport widths of less than 480px.
| `hide-below-400` | Hides element below viewport widths of less than 400px.
| `hide-below-375` | Hides element below viewport widths of less than 375px.
| `show-below-desktop` | Only show element below viewport widths of less than 1024px.
| `show-below-tablet` | Only show element below viewport widths of less than 768px.
| `show-below-640` | Only show element below viewport widths of less than 640px.
| `show-below-480` | Only show element below viewport widths of less than 480px.
| `show-below-400` | Only show element below viewport widths of less than 400px.
| `show-below-375` | Only show element below viewport widths of less than 375px.
| `hide-if-logged-in` | Hides element if the user is logged in.
| `hide-if-logged-out` | Hides element if the user is logged out.
| `no-theme-spacing` | Removes the top and bottom spacing applied by the theme.
| `no-theme-style` | Removes the styling applied by the theme (for some blocks).
| `padding-[top\|right\|bottom\|left]` | Applies directional theme page padding.
| `bg-[50\|100\|200\|...\|800\|900\|950]` | Forces the respective theme background color.
| `fg-[100\|200\|...\|800\|900\|950]` | Forces the respective theme text color.
| `max-site-width` | Applies the theme’s max site width (mainly useful for page builders).
| `header-polygon` | Applies the header clip-path chosen in the Customizer (if any). Does not work for masks.
| `page-polygon` | Applies the page clip-path chosen in the Customizer (if any). Does not work for masks.
| `cover-hidable` | Hides element if the user disables covers in the site settings modal.

## HTML Block

The custom HTML block is the best way to add special elements to the content, such as status screens in [litRPGs](https://en.wikipedia.org/wiki/LitRPG). The preview option in the editor helps if you are just dabbling. This can be further enhanced with inline styles or custom CSS classes, but you need to account for the dark/light mode and generated ePUBs as well. The following example is baked into the theme and sure to work, just change the content or remove what you do not need.

<details>
  <summary>HTML for litRPG box</summary><br>
  <p>You can safely use <code>h1</code>, <code>h2</code>, <code>h3</code>, <code>h4</code>, <code>h5</code>, <code>h6</code>, <code>table</code>, <code>thead</code>, <code>tbody</code>, <code>tr</code>, <code>th</code>, <code>td</code>, <code>strong</code>, <code>b</code>, <code>u</code>, <code>s</code>, <code>em</code>, <code>br</code>, <code>ins</code>, <code>del</code>, <code>sup</code>, <code>sub</code>, <code>hr</code>, <code>dl</code>, <code>dt</code>, <code>dd</code>, <code>p</code>, <code>small</code>, <code>ul</code>, <code>ol</code>, and <code>li</code>.</p>

```html
<div class="litrpg-box">
  <div class="litrpg-frame">
    <div class="litrpg-body">
      <!-- Start Content -->
      <h3>Cypher has earned 2 Power Points!</h3>
      <small style="margin: -1em 0 .25em; opacity: 0.65;"><strong>Power Level:</strong> 9 &ensp; <strong>Gender:</strong> Female &ensp; <strong>Age:</strong> 24</small>
      <table>
        <tbody>
          <tr>
            <td><strong>Strength</strong><br>5</td>
            <td><strong>Stamina</strong><br>—</td>
            <td><strong>Agility</strong><br>5</td>
            <td><strong>Dexterity</strong><br>0</td>
          </tr>
          <tr>
            <td><strong>Fighting</strong><br>5</td>
            <td><strong>Intellect</strong><br>3 <ins>&#9650;1</ins></td>
            <td><strong>Awareness</strong><br>4 <del>&#9660;1</del></td>
            <td><strong>Presence</strong><br>2</td>
          </tr>
        </tbody>
      </table>
      <hr>
      <table>
        <tbody>
          <tr>
            <td><strong>Dodge</strong><br>5</td>
            <td><strong>Parry</strong><br>5</td>
            <td><strong>Fortitude</strong><br>—</td>
            <td><strong>Will</strong><br>5</td>
            <td><strong>Toughness</strong><br>13</td>
          </tr>
        </tbody>
      </table>
      <hr>
      <dl>
        <dt>Advantages:</dt>
        <dd>Close Attack 8, Ranged Attack 4, Attractive, Power Attack, Luck 3, Quick Draw, Eidetic Memory</dd>
      </dl>
      <dl>
        <dt>Skills:</dt>
        <dd>Acrobatics 5, Athletics 5, Insight 5, Intimidation 6, Investigation 3, Perception 7, Stealth 5, Treatment 3, Close Combat 9, Expertise: Cybertechnology 11</dd>
      </dl>
      <dl>
        <dt>Powers:</dt>
        <dd>Armor (Protection 13) &bull; Cyborg (Immunity: Fortitude) &bull; Cyberarms (Enhanced Strength 3) &bull; Cyberlegs (Speed 2, Safe Fall) &bull; White Blood (Regeneration 2) &bull; Sensor Array (Counters Visual Concealment, Counters Visual Illusions, Darkvision, Direction Sense, Extended Vision 2, Radio)</dd>
      </dl>
      <p>Experience: 0/200</p>
      <small style="opacity: 0.65;"><a href="https://www.d20herosrd.com/" target="blank" rel="noopener">Mutants &amp; Masterminds OGL Content</a></small>
      <!-- End Content -->
    </div>
  </div>
</div>
```

</details>

![LITRPG Box](repo/assets/litrpg_boxes.jpg?raw=true)

## Patreon Gate

You can grant logged-in users access to password-protected content via Patreon membership, either by selected tiers or pledge thresholds or both. See [installation guide](INSTALLATION.md#patreon-integration) for more details. Prices are stored in **cents** (¢100 to $1), independent of your campaign currency. You still need to set a password for the post and stories **do not** pass down gates to chapters due to technical reasons.

**Caching:** If you use a cache plugin, make sure that password-protected posts are not cached or this might not work properly. The LiteSpeed Cache, WP Super Cache, and W3 Total Cache plugins should be fine, but anything else might need additional configuration.

**Free Tier:** If you want to gate content behind the free tier (only following, not paying), you can just add the tier alongside the others. If that is too inconvenient because you got too many tiers, you can use the pledge threshold to include any tier equal to or above a certain amount in cents (e.g. 300 for $3.00), either globally or post by post.

## Unlock Posts

You can grant logged-in users access to password-protected content by unlocking specific posts. Just open the admin profile page of the user, search for the posts you want to unlock, add them and save. Chapters inherit the unlock of the story. Roles other than administrators require both the **Edit Users** and **Unlock Posts** capabilities to assign unlocked posts to users, which can be assigned in the role manager.

**Caching:** If you use a cache plugin, make sure that password-protected posts are not cached or this might not work properly. The LiteSpeed Cache, WP Super Cache, and W3 Total Cache plugins should be fine, but anything else might need additional configuration.

**Patreon Gate:** Post unlocks are normally independent of Patreon, but you can gate them behind a global pledge threshold in cents to limit the feature to paying patrons only. This is in addition to any other Patreon gates.

![Unlock Posts](repo/assets/user_unlock_posts.jpg?raw=true)

## Shortcodes

[Shortcodes](https://wordpress.org/support/article/shortcode-block/) are bracket-enclosed keywords placed within the content that WordPress automatically interprets into code, adding features or objects without the need for programming. This should be done inside a _shortcode_ block, although it would work outside too. Since most elements created by shortcodes have no margins, the _spacer_ block can be a good addition before and/or after.

**Attention!** Shortcode queries are cached as [Transients](https://developer.wordpress.org/apis/transients/) to reduce their performance impact, especially if you have more than one per page. This means they will not update immediately (except if you have a cache plugin active, which disabled this feature). By default, the Transients expire after 300 seconds (5 minutes), which can be changed via the `FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION` constant in a child theme. You can disable the Transients by setting the constant to `-1`.

### Story Actions Shortcode

Renders the action row of the specified story. All buttons and links will work as if on the story post, aside from the sharing modal which always refers to the current page. This only works on pages with the "Story Page" template set and is intended to create a single-story-centric front page.

* **story_id:** The ID of the story.
* **class:** Additional CSS classes, separated by whitespace.
* **follow:** Whether to render the Follow button (if enabled). Default `true`.
* **reminder:** Whether to render the Reminder button (if enabled). Default `true`.
* **subscribe:** Whether to render the Subscribe button (if enabled). Default `true`.
* **download:** Whether to render the ePUB/eBook download button (if enabled). Default `true`.
* **rss:** Whether to render the RSS link (if enabled). Default `true`.
* **share:** Whether to render the Share modal button (if enabled). Default `true`.
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_story_actions story_id="106"]
```

```
[fictioneer_story_actions story_id="182" follow="0" reminder="0" share="0"]
```

### Story Section Shortcode

Renders the chapters, groups, and tabs of the specified story. It will look just as if on the story post. This only works on pages with the "Story Page" template set and is intended to create a single-story-centric front page.

* **story_id:** The ID of the story.
* **class:** Additional CSS classes, separated by whitespace.
* **tabs:** Whether to render the tabs (if any). Default `false`.
* **blog:** Whether to render the blog tab. Default `false`.
* **pages:** Whether to render the custom page tabs. Default `false`.
* **scheduled:** Whether to render the scheduled chapter note. Default `false`.
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_story_section story_id="106"]
```

```
[fictioneer_story_section story_id="182" tabs="true" pages="true"]
```

### Story Comments Shortcode

Renders the button to load the collective comments made on chapters in the story. Not to be confused with the comments you can make on the page, which are completely separate. This only works on pages with the "Story Page" template set and is intended to create a single-story-centric front page.

* **story_id:** The ID of the story.
* **class:** Additional CSS classes, separated by whitespace.
* **header:** Whether to render the heading with count. Default `true`.

```
[fictioneer_story_comments story_id="13"]
```

```
[fictioneer_story_comments story_id="13" header="0"]
```

### Story Data Shortcode

Renders a single datum from the specified story, such as the **word count** or **age rating**. You can use this to show your own self-updating statistics. Just omit the shortcode block and write it directly into the text.

* **data:** The requested data, singular. Choose between `word_count`, `chapter_count`, `status`, `icon` (status), `age_rating`, `rating_letter`, `comment_count`, `id`, `date`, `time`, `datetime`, `categories`, `tags`, `genres`, `fandoms`, `characters`, and `warnings`.
* **story_id:** The ID of the story. Defaults to current post ID.
* **format:** Special formatting for some data. Mostly used for counts, use `short` or `raw`.
* **date_format:** Formatting string for the date. Defaults to your WordPress settings.
* **time_format:** Formatting string for the time. Defaults to your WordPress settings.
* **separator:** String between list items, such as tags. Defaults to `", "` (comma + whitespace).
* **tag:** Wrapping HTML tag. Defaults to `span`.
* **class:** Additional CSS classes, separated by whitespace.
* **inner_class:** Additional CSS classes for nested elements (if any), separated by whitespace.
* **style:** Inline CSS style applied to the wrapping element.
* **inner_style:** Inline CSS style applied to nested elements (if any).

```
The example story Katalepsis has [fictioneer_story_data story_id="13" data="chapter_count"] chapters featured on this site, containing a total of [fictioneer_story_data story_id="13" data="word_count"] words.
```

```
You can format the word count with "raw" ([fictioneer_story_data story_id="13" data="word_count" format="raw"]) or "short" ([fictioneer_story_data story_id="13" data="word_count" format="short"]).
```

```
Katalepsis has the following tags: [fictioneer_story_data story_id="13" data="tags" separator=" | " inner_style="color: var(--red-500);"].
```

### Subscribe Button Shortcode

Renders a subscribe button for the specified story.

* **story_id:** The ID of the story the button is for.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_subscribe_button story_id="228"]
```

### Terms Shortcode

Renders a group of terms similar to those on story pages, either globally or for a specific post.

* **type:** The queried taxonomy. Choose between `category`, `tag`, `genre`, `fandom`, `character`, and `warning`. Default `tag`.
* **post_id:** Query only terms for a specific post. Default `0` (none).
* **count:** Limit the number of items. Default `-1` (all).
* **order:** Either DESC (descending) or ASC (ascending). Default `desc`.
* **orderby:** The default is `count`, but you can also use `name` and [more](https://developer.wordpress.org/reference/classes/wp_term_query/__construct/).
* **show_empty:** Whether to show empty terms. Default `false`.
* **show_count:** Whether to show the term count. Default `false`.
* **class:** Additional CSS classes, separated by whitespace.
* **inner_class:** Additional CSS classes for nested elements (if any), separated by whitespace.
* **style:** Inline CSS style applied to the wrapping element.
* **inner_style:** Inline CSS style applied to nested elements (if any).
* **empty:** Override message for empty query results.

```
[fictioneer_terms count="15"]
```

```
[fictioneer_terms type="genre" post_id="228" inner_class="_secondary" show_count="true" style="margin: 2rem 0;" empty=""]
```

### Font Awesome Shortcode

Renders a *free* [Font Awesome](https://fontawesome.com/) icon, which you could technically do manually in the code editor as well. Somewhat more convenient, I guess? Just omit the shortcode block and write it directly into the text. This shortcode also works if your role lacks the shortcode capability.

* **class:** Font Awesome CSS classes, separated by whitespace. You can custom ones, too.

```
Have some [fictioneer_fa class="fa-solid fa-mug-hot"]
```

### Article Cards

Renders a multi-column grid of paginated medium cards ordered by publishing date, descending. Unless you provide the **count** parameter, only add this once per page since it uses the main query page argument. The thumbnail is either the **Landscape Image** or **Cover Image**, depending on the aspect ratio and availability, with chapters defaulting to the parent story.

* **post_type:** Comma-separated list of post types to query. Default `post`.
* **post_ids:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **per_page:** Number of posts per page. Defaults to theme settings.
* **count:** Limit articles to any positive number, disabling the pagination.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `modified` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **ignore_sticky:** Whether sticky posts should be ignored or not. Default `false`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **author:** Only show recommendations by a specific author. Make sure to use the url-safe nice_name.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **seamless:** Whether to remove the gap between the image and frame. Default `false` (Customizer setting).
* **thumbnail:** Whether to show the thumbnail/cover image. Default `true` (Customizer setting).
* **lightbox:** Whether clicking on the thumbnail/cover image opens the lightbox or post link. Default `true`.
* **terms:** Either `inline`, `pills`, or `none`. Default `inline`.
* **max_terms:** Maximum number of shown taxonomies. Default `10`.
* **date_format:** String to override the [date format](https://wordpress.org/documentation/article/customize-date-and-time-format/). Default `''`.
* **footer:** Whether to show the footer (if any). Default `true`.
* **footer_author:** Whether to show the post author. Default `true`.
* **footer_date:** Whether to show the post date. Default `true`.
* **footer_comments:** Whether to show the post comment count. Default `true`.
* **aspect_ratio:** CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) value for the image (X/Y). Default `3/1`.
* **class:** Additional CSS classes, separated by whitespace.
* **splide:** Configuration JSON to turn the grid into a slider. See [Slider](#slider).
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_article_cards]
```

```
[fictioneer_article_cards post_type="post" per_page="4" ignore_sticky="true"]
```

```
[fictioneer_article_cards post_type="story, chapter" count="8" ignore_protected="true"]
```

```
[fictioneer_article_cards post_type="story, chapter" seamless="true" aspect_ratio="4/1"]
```

![Article Cards](repo/assets/shortcode_example_article_cards.jpg?raw=true)
![Article Cards](repo/assets/shortcode_example_article_cards_2.png?raw=true)

### Blog

Renders paginated blog posts akin to the main blog page, but with options. Only add this once per page since it uses the main query page argument, avoid combining it with the Article Cards shortcode.

* **per_page:** Number of posts per page. Defaults to theme settings.
* **ignore_sticky:** Whether sticky posts should be ignored or not. Default `false`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **author:** Only show posts of a specific author. Make sure to use the url-safe nice_name.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_blog]
```

```
[fictioneer_blog class="foo bar baz" per_page="5" exclude_cat_ids="1,23,24" categories="news"]
```

```
[fictioneer_blog categories="uncategorized"]
```

### Bookmarks

Renders a multi-column grid of small bookmark cards, ordered by date of creation. The bookmarks are stored in the browser and appended to the document via JavaScript. You can combine this with the `show-if-bookmarks hidden` additional CSS classes, displaying a headline or other element only if bookmarks are present.

* **count:** Limit bookmarks to any positive number. Default `-1` (all).
* **show_empty:** Whether to show a "no bookmarks" note or nothing if empty. Default `false`.
* **seamless:** Whether to remove the gap between the image and frame. Default `false` (Customizer setting).
* **thumbnail:** Whether to show the thumbnail/cover image. Default `true` (Customizer setting).

```
[fictioneer_bookmarks]
```

```
[fictioneer_bookmarks count="8" show_empty="true"]
```

```
[fictioneer_bookmarks count="8" seamless="true" thumbnail="0"]
```

![Bookmarks](repo/assets/shortcode_example_bookmarks.jpg?raw=true)

### Chapter List

Renders a list of chapters identical to those on story pages, ordered by sequence in the source. Must have either the **story_id** or **chapter_ids** parameter, but not both.

* **story_id:** ID of a single story. You need either this or **chapters**.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **chapter_ids:** Comma-separated list of chapter IDs. You need either this or **story**.
* **count:** Limit chapters to any positive number. Default `-1` (all).
* **offset:** Skip a number of chapters, which can make sense if you query all.
* **heading:** Show a heading with collapse toggle above the list.
* **group:** Only show chapters with a specific group name, which can transcend stories.
* **class:** Additional CSS classes, separated by whitespace. `no-auto-collapse` prevents default group collapsing (if set).
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_chapter_list story="69"]
```

```
[fictioneer_chapter_list class="foobar no-auto-collapse" story="69" count="10" offset="2"]
```

```
[fictioneer_chapter_list chapters="13,21,34" heading="Pigs are a lot bigger than you expect" group="You could ride it"]
```

![Chapter List](repo/assets/shortcode_example_chapter_list_1.jpg?raw=true)

### Contact Form

Renders a contact form with various (optional) fields. Submissions are validated, sanitized, have basic spam protection, and are checked against the WordPress disallow list under **Settings > Discussions**. If all steps are passed, the submission is sent to the email addresses listed under **Fictioneer > General > Contact Form Receivers**, which are never revealed to the public. If empty, the admin email address is used instead.

* **title:** Title of the form shown in emails. Defaults to "Nameless Form".
* **submit:** Label of the submit button. Defaults to "Submit".
* **privacy_policy:** Whether the privacy policy must be accepted. Default `false`.
* **required:** Whether all fields must be filled out. Default `false`.
* **email:** Sender email address for replies.
* **name:** Sender name for personal replies.
* **text_[1-6]:** Custom text fields 1 to 6, e.g. **text_1** to **text_6**.
* **check_[1-6]:** Custom checkboxes 1 to 6, e.g. **check_1** to **check_6**.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_contact_form]
```

```
[fictioneer_contact_form email="Email Address (required)" check_1="Totally not a robot" title="Human Test" privacy_policy="true" required="true"]
```

```
[fictioneer_contact_form email="Email Address (optional)" name="Your Name (optional)" text_1="Topic (optional)" title="Privacy Policy Contact Form" privacy_policy="true"]
```

![Contact Form](repo/assets/shortcode_example_contact_form_1.jpg?raw=true)

### Cookie Buttons

Renders two buttons to deal with cookies, "Reset Consent" and "Clear Cookies". Best used in the Cookies section of your Privacy Policy.

```
[fictioneer_cookie_buttons]
```

![Bookmarks](repo/assets/shortcode_example_cookie_buttons.jpg?raw=true)

### Latest Chapters

Renders a multi-column grid of small cards, showing the latest four chapters ordered by publishing date, descending. Note that the `list` type behaves a bit different with the parameters.

* **count:** Limit chapters to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default`, `simple`, `compact`, or `list`. The other variants are smaller with less data.
* **author:** Only show chapters of a specific author. Make sure to use the url-safe nice_name.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `modified` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **spoiler:** The excerpt is obfuscated, set `true` if you want to reveal it. Default `false`.
* **source:** Whether to show the author and story nodes. Default `true`.
* **post_ids:** Comma-separated list of chapter post IDs, if you want to pick from a curated pool.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **vertical:** Whether to render the cards with the image on top. Default `false`.
* **seamless:** Whether to remove the gap between the image and frame. Default `false` (Customizer setting).
* **thumbnail:** Whether to show the thumbnail/cover image. Default `true` (Customizer setting).
* **lightbox:** Whether clicking on the thumbnail/cover image opens the lightbox or post link. Default `true`.
* **infobox:** Whether to show the info box and toggle on compact versions. Default `true`.
* **date_format:** String to override the [date format](https://wordpress.org/documentation/article/customize-date-and-time-format/). Default `''`.
* **footer:** Whether to show the footer (if any). Default `true`.
* **footer_author:** Whether to show the chapter author. Default `true`.
* **footer_words:** Whether to show the chapter word count. Default `true`.
* **footer_date:** Whether to show the chapter date. Default `true`.
* **footer_comments:** Whether to show the chapter comment count (not in `list`). Default `true`.
* **footer_status:** Whether to show the chapter story status. Default `true`.
* **footer_rating:** Whether to show the chapter age rating. Default `true`.
* **aspect_ratio:** CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) value for the image (X/Y; vertical only). Default `3/1`.
* **class:** Additional CSS classes, separated by whitespace.
* **splide:** Configuration JSON to turn the grid into a slider. See [Slider](#slider).
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_latest_chapters]
```

```
[fictioneer_latest_chapters genres="adventure, historical" characters="indiana jones"]
```

```
[fictioneer_latest_chapters count="10" type="compact" author="Tetrakern" order="asc" orderby="modified" spoiler="true" source="false" chapters="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_chapters source="false" vertical="true" seamless="true" aspect_ratio="5/1"]
```

![Latest Chapters](repo/assets/shortcode_example_latest_chapters.jpg?raw=true)
![Latest Chapters](repo/assets/shortcode_example_latest_chapters_2.png?raw=true)

```
[fictioneer_latest_chapters type="list" count="2"]
```

```
[fictioneer_latest_chapters type="list" orderby="rand" count="2" source="false" footer_status="false"]
```

![Latest Chapters](repo/assets/shortcode_example_latest_chapters_3.png?raw=true)

### Latest Posts

Renders the last blog post or a list of blog posts, ignoring sticky posts, ordered by publishing date, descending.

* **count:** Limit posts to any positive number, although you should keep it reasonable. Default `1`.
* **author:** Only show posts of a specific author. Make sure to use the url-safe nice_name.
* **post_ids:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_latest_posts]
```

```
[fictioneer_latest_posts count="16" tags="world building, characters" categories="blog, tutorials" rel="or"]
```

```
[fictioneer_latest_posts count="4" author="Tetrakern" posts="1,2,3,5,8,13,21,34"]
```

![Latest Posts](repo/assets/shortcode_example_latest_posts.jpg?raw=true)

### Latest Recommendations

Renders a multi-column grid of small cards, showing the latest four recommendations ordered by publishing date, descending.

* **count:** Limit recommendations to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default` or `compact`. The compact variant is smaller with less data.
* **author:** Only show recommendations by a specific author. Make sure to use the url-safe nice_name.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `modified` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **post_ids:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **vertical:** Whether to render the cards with the image on top. Default `false`.
* **seamless:** Whether to remove the gap between the image and frame. Default `false` (Customizer setting).
* **thumbnail:** Whether to show the thumbnail/cover image. Default `true` (Customizer setting).
* **terms:** Either `inline`, `pills`, or `none`. Default `inline`.
* **max_terms:** Maximum number of shown taxonomies. Default `10`.
* **lightbox:** Whether clicking on the thumbnail/cover image opens the lightbox or post link. Default `true`.
* **infobox:** Whether to show the info box and toggle on compact versions. Default `true`.
* **aspect_ratio:** CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) value for the image (X/Y; vertical only). Default `3/1`.
* **class:** Additional CSS classes, separated by whitespace.
* **splide:** Configuration JSON to turn the grid into a slider. See [Slider](#slider).
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_latest_recommendations]
```

```
[fictioneer_latest_recommendations genres="isekai" fandoms="original, fanfiction"]
```

```
[fictioneer_latest_recommendations count="10" type="compact" author="Tetrakern" order="asc" orderby="rand" recommendations="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_recommendations vertical="true" seamless="true"]
```

![Latest Recommendations](repo/assets/shortcode_example_latest_recommendations.jpg?raw=true)
![Latest Recommendations](repo/assets/shortcode_example_latest_recommendations_2.png?raw=true)

### Latest Stories

Renders a multi-column grid of small cards, showing the latest four stories ordered by publishing date, descending. Note that the `list` type behaves a bit different with the parameters.

* **count:** Limit stories to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default`, `compact`, or `list`. The compact variant is smaller with less data.
* **author:** Only show stories of a specific author. Make sure to spell the _username_ right.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `modified` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **post_ids:** Comma-separated list of story post IDs, if you want to pick from a curated pool.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **source:** Whether to show the author node. Default `true`.
* **vertical:** Whether to render the cards with the image on top. Default `false`.
* **seamless:** Whether to remove the gap between the image and frame. Default `false` (Customizer setting).
* **thumbnail:** Whether to show the thumbnail/cover image. Default `true` (Customizer setting).
* **lightbox:** Whether clicking on the thumbnail/cover image opens the lightbox or post link. Default `true`.
* **infobox:** Whether to show the info box and toggle on compact versions. Default `true`.
* **date_format:** String to override the [date format](https://wordpress.org/documentation/article/customize-date-and-time-format/). Default `''`.
* **terms:** Either `inline`, `pills`, or `none`. Default `inline`.
* **max_terms:** Maximum number of shown taxonomies. Default `10`.
* **footer:** Whether to show the footer (if any). Default `true`.
* **footer_author:** Whether to show the author. Default `true`.
* **footer_chapters:** Whether to show the chapter count (not in `list`). Default `true`.
* **footer_words:** Whether to show the word count. Default `true`.
* **footer_date:** Whether to show the date. Default `true`.
* **footer_status:** Whether to show the status. Default `true`.
* **footer_rating:** Whether to show the age rating. Default `true`.
* **footer_comments:** Whether to show the post comment count. Default `false`.
* **aspect_ratio:** CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) value for the image (X/Y; vertical only). Default `3/1`.
* **class:** Additional CSS classes, separated by whitespace.
* **splide:** Configuration JSON to turn the grid into a slider. See [Slider](#slider).
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_latest_stories]
```

```
[fictioneer_latest_stories genres="adventure, cyberpunk" characters="Rebecca" rel="or"]
```

```
[fictioneer_latest_stories count="10" type="compact" author="Tetrakern" order="asc" orderby="modified" stories="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_stories count="2" author="Hungry" seamless="true"]
```

```
[fictioneer_latest_stories type="compact" vertical="true" aspect_ratio="3/2"]
```

![Latest Stories](repo/assets/shortcode_example_latest_stories.jpg?raw=true)
![Latest Stories](repo/assets/shortcode_example_latest_stories_3.png?raw=true)
![Latest Stories](repo/assets/shortcode_example_latest_stories_2.png?raw=true)

```
[fictioneer_latest_stories type="list"]
```

```
[fictioneer_latest_stories type="list" footer_status="false" footer_rating="false" terms="pills" aspect_ratio="2/3"]
```

![Latest Stories](repo/assets/shortcode_example_latest_stories_4.png?raw=true)

### Latest Updates

Renders a multi-column grid of small cards, showing the latest four updated stories ordered by date of the last chapter change, descending. Note that the `list` type behaves a bit different with the parameters.

* **count:** Limit updates to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default`, `simple`, `single`, `compact`, or `list`. The other variants are smaller with less data.
* **single:** Whether to show only one chapter item (included in type `single`). Default `false`.
* **author:** Only show updates of a specific author. Make sure to use the url-safe nice_name.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **post_ids:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **source:** Whether to show the author node. Default `true`.
* **vertical:** Whether to render the cards with the image on top. Default `false`.
* **seamless:** Whether to remove the gap between the image and frame. Default `false` (Customizer setting).
* **thumbnail:** Whether to show the thumbnail/cover image. Default `true` (Customizer setting).
* **lightbox:** Whether clicking on the thumbnail/cover image opens the lightbox or post link. Default `true`.
* **infobox:** Whether to show the info box and toggle on compact versions. Default `true`.
* **aspect_ratio:** CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) value for the image (X/Y; vertical only). Default `3/1`.
* **words:** Whether to show the word count of chapter items. Default `true`.
* **date:** Whether to show the date of chapter items. Default `true`.
* **date_format:** String to override the [date format](https://wordpress.org/documentation/article/customize-date-and-time-format/). Default `''`.
* **nested_date_format:** String to override any nested [date formats](https://wordpress.org/documentation/article/customize-date-and-time-format/). Default `''`.
* **terms:** Either `inline`, `pills`, or `none`. Default `inline`.
* **max_terms:** Maximum number of shown taxonomies. Default `10`.
* **footer:** Whether to show the footer (if any). Default `true`.
* **footer_author:** Whether to show the story/chapter author. Default `true`.
* **footer_chapters:** Whether to show the story chapter count (not in `list`). Default `true`.
* **footer_words:** Whether to show the story word count. Default `true`.
* **footer_date:** Whether to show the story date. Default `true`.
* **footer_status:** Whether to show the story status. Default `true`.
* **footer_rating:** Whether to show the story age rating. Default `true`.
* **footer_comments:** Whether to show the post comment count. Default `false`.
* **class:** Additional CSS classes, separated by whitespace.
* **splide:** Configuration JSON to turn the grid into a slider. See [Slider](#slider).
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_latest_updates]
```

```
[fictioneer_latest_updates genres="romance, drama" fandoms="original"]
```

```
[fictioneer_latest_updates count="10" type="simple" author="Tetrakern" order="asc" stories="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_updates type="compact" order="asc" post_ids="13,106" seamless="true"]
```

```
[fictioneer_latest_updates type="compact" vertical="true" seamless="true"]
```

```
[fictioneer_latest_updates type="simple" single="true" date="0"]
```

![Latest Updates](repo/assets/shortcode_example_latest_updates.jpg?raw=true)
![Latest Updates](repo/assets/shortcode_example_latest_updates_3.png?raw=true)
![Latest Updates](repo/assets/shortcode_example_latest_updates_2.png?raw=true)
![Latest Updates](repo/assets/shortcode_example_latest_updates_4.png?raw=true)

```
[fictioneer_latest_updates type="list" count="2" nested_date_format="m/d/Y"]
```

```
[fictioneer_latest_updates type="list" count="2" seamless="true" date="false" words="false" footer_rating="false" terms="pills"]
```

![Latest Updates](repo/assets/shortcode_example_latest_updates_5.png?raw=true)

### Search Form

Renders the search form with advanced options (if not disabled in the settings).

* **simple:** Set `true` to hide the advanced search options. Default `false`.
* **placeholder:** Change the placeholder text.
* **type:** Preselect either "any", "story", "chapter", "recommendation", "collection", or "post".
* **expanded:** Whether the advanced form is expanded. Default `false`.
* **tags:** Preselect tags as comma-separated list of term IDs.
* **genres:** Preselect genres as comma-separated list of term IDs.
* **fandoms:** Preselect fandoms as comma-separated list of term IDs.
* **characters:** Preselect characters as comma-separated list of term IDs.
* **warnings:** Preselect warnings as comma-separated list of term IDs.

```
[fictioneer_search]
```

```
[fictioneer_search simple="true" placeholder="What are you looking for?"]
```

```
[fictioneer_search tags="569" fandoms="200,199"]
```

![Contact Form](repo/assets/shortcode_example_search_1.jpg?raw=true)

### Showcase

Renders dynamic grid of thumbnails with title, showing the latest eight posts of the specified type ordered by publishing date, descending. Requires **for** parameter. The thumbnail is either the **Landscape Image** (if available) or **Cover Image**, with chapters defaulting to the parent story.

* **for:** Desired post type, either `stories`, `chapters`, `collections`, or `recommendations`.
* **count:** Limit posts to any positive number, although you should keep it reasonable. Default `8`.
* **author:** Only show posts for a specific author. Make sure to use the url-safe nice_name.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `rand` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **post_ids:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **post_status:** Either `publish` or `future`, albeit others are possible (but why?). Note that by default, any post status except `publish` redirects to a 404 page for guests and users without higher permissions. Default `publish`.
* **ignore_protected:** Whether protected posts should be ignored or not. Default `false`.
* **only_protected:** Whether to query only protected posts or not. Default `false`.
* **author_ids:** Only show posts of a comma-separated list of author IDs.
* **exclude_author_ids:** Comma-separated list of author IDs to exclude.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **no_cap:** Set `true` if you want to hide the caption.
* **aspect_ratio:** CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) value for the item (X/Y).
* **height:** Override the item height. Superseded by `aspect_ratio`.
* **width:** Override the item minimum width (it will still be stretched to fill the space).
* **class:** Additional CSS classes, separated by whitespace.
* **splide:** Configuration JSON to turn the grid into a slider. See [Slider](#slider).
* **quality:** Either `'snippet'`, `'thumbnail'`, `'cover'`, `'medium'`, `'medium_large'`, `'large'`, or `'full'`. Default `'medium'`.
* **cache:** Whether the shortcode should be cached. Default `true`.

```
[fictioneer_showcase for="collections"]
```

```
[fictioneer_showcase for="collections" count="10" author="Tetrakern" order="asc" posts="1,2,3,5,8,13,21,34"]
```

![Showcase](repo/assets/shortcode_example_showcase.jpg?raw=true)

```
[fictioneer_showcase for="stories" count="4" aspect_ratio="2/3" min_width="150px"]
```

![Showcase](repo/assets/shortcode_example_showcase_2.jpg?raw=true)

### Slider

Any shortcode with the `splide` parameter can be turned into a slider. [Splide](https://splidejs.com/) is a flexible and lightweight slider that comes with [many options](https://splidejs.com/guides/options/) for customization, although applying them may be challenging if you are not familiar with [JSONs](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Objects/JSON). You can look up the details yourself.

The `splide` parameter only accepts JSON strings, such as `splide="{'type':'loop','perPage':3}"`. Note that you need to use **single quotes** due to the shortcode syntax. If there is even a minor error, the JSON will be rejected with a note, and the shortcode will default to its standard layout. Not all parameter combinations have been tested with Splide, so custom CSS may be required in some cases.

If you do not want to initialize a slider on page load, you can add the `no-auto-splide` CSS class via the `class` parameter in the shortcode or custom HTML (where the `splide` class is). Normally, Splide’s assets are only enqueued when a shortcode with the necessary parameter is found in the post content, but you can enable Splide globally under **Fictioneer > General > Compatibility**.

```
[fictioneer_latest_stories count="6" splide="{'type': 'loop', 'gap': '1.5rem', 'autoplay': true, 'perPage': 2, 'breakpoints': {'767': {'perPage': 1}}}"]
```

<p align="center">
  <img src="repo/assets/shortcode_example_latest_stories_slider.gif?raw=true" alt="Slider Preview" />
</p>

#### Custom HTML Sliders

If you enable Splide globally (or have a slider shortcode on the same page), you can use the HTML block to create your own Slider. Just copy the base [structure](https://splidejs.com/guides/structure/) and add any slides you like, although you will need to style them yourself using custom CSS. Initialize the slider with the [data attribute JSON](https://splidejs.com/guides/options/#by-data-attribute), but this time with double quotes as shown in the example. Unlike with shortcodes, navigation arrows are enabled by default but can be turned off with `"arrows:" false`.

```html
<section class="splide" data-splide='{"type": "loop", "interval": 3000, "gap": "1.5rem", "autoplay": true, "perPage": 3, "breakpoints": {"767": {"perPage": 2, "arrows": false}, "479": {"perPage": 1}}}'>
  <div class="splide__track">
    <ul class="splide__list">
      <li class="splide__slide example-side">Slide 01</li>
      <li class="splide__slide example-side">Slide 02</li>
      <li class="splide__slide example-side">Slide 03</li>
      <li class="splide__slide example-side">Slide 04</li>
      <li class="splide__slide example-side">Slide 05</li>
      <li class="splide__slide example-side">Slide 06</li>
    </ul>
  </div>
</section>
```

### Sidebar

Renders the theme sidebar (not displayed anywhere by default). Requires the "Disable all widgets" theme setting to be off. Note that the sidebar has next to no styling.

* **name:** Name of the sidebar in case you add some. Default `fictioneer-sidebar`.

```
[fictioneer_sidebar]
```

```
[fictioneer_sidebar name="other-sidebar"]
```

### Tooltip (Modal) & Footnotes

Includes a tooltip modal for the wrapped content, indicated by a dotted underline. Just omit the shortcode block and write it directly into the text. This shortcode also works if your role lacks the shortcode capability. If you enable the footnotes feature under **Fictioneer > General > Features**, the tooltips will also be appended to the content as footnotes.

**Formatting:** You can use HTML in the shortcode content attribute, as far as the post sanitizer and user role allows. However, be cautious of nested quotation marks and square brackets, as they can break the shortcode — use [HTML entities](https://developer.mozilla.org/en-US/docs/Glossary/Character_reference) instead.

**Footnotes:** There is no difference in use to append the tooltips as footnotes, complete with anchor link. Just enable the rendering globally. You can disable individual footnotes with the `footnote="false"` parameter (but not the other way around).

```
[fcnt content='This is a note.']note[/fcnt]
```

```
[fcnt header='Are you dense?' content='This <em>typically</em> refers to forms that are either literal <strong>skeletons</strong> or the underlying bearing *structure* of objects.']skeletal shapes[/fcnt]
```

![Tooltip Modal](repo/assets/fcnt_example.jpg?raw=true)

![Footnote](repo/assets/fcnt_example_2.jpg?raw=true)

## Elementor

If you have the Elementor plugin installed, consider using the [Fictioneer 002 Elementor Control](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#recommended-must-use-plugins) plugin if you only need it for the Canvas page templates. If you have the Pro version and want to use the theme builder, this may not be an option, but you can customize the following locations: `header`, `footer`, `nav_bar`, `nav_menu`, `mobile_nav_menu`, `story_header`, and `page_background`.

**Page Background**

This location can be confusing. The page background is actually a separate element in the theme, positioned under the content container and made inaccessible. This allows for various styling shenanigans without ever affecting the content, such as clip-paths and masks applied to an inner `::before` pseudo-element. The page styles from the Customizer make heavy use of this. If you overwrite this location, you must ensure to properly move it to the background. The base default CSS is as follows:

```css
.main__background {
  pointer-events: none;
  user-select: none;
  position: absolute;
  inset: var(--page-inset-top, 0px) 0 0 0;
  z-index: 0;
  background-color: var(--page-bg-color);
  box-shadow: var(--page-box-shadow);
  contain: layout style;
}
```

**Hints:**

* The `nav_bar` location also overwrites the `nav_menu` location.
* Overwriting the navigation is possible but generally a poor life choice.
* Elementor disables several WordPress block styles when applied to a page...
* ... which can affect other elements, such as the Post Content header variant.
* Use the `padding-[top|right|bottom|left]` CSS classes to apply theme page padding.
* Use the `bg-[50|100|200|...|800|900|950]` CSS classes to force theme background colors.
* Use the `fg-[100|200|...|800|900|950]` CSS classes to force theme text colors.
* Use the `max-site-width` CSS class to apply the theme’s max site width.
* Use the `page-polygon` CSS class to apply the page clip-path chosen in the Customizer (if any).
* Use the `header-polygon` CSS class to apply the header clip-path chosen in the Customizer (if any).
* The grunge, layered peaks/steps, ringbook, and wave header/page masks have no utility classes.
* Some of the [content utility CSS classes](#additional-css-classes) will also work in Elementor.
* You can toggle the mobile menu with a label element targeting the `mobile-menu-toggle` ID.
* You can select the theme fonts in Elementor, grouped under "Fictioneer".
* You can use the theme shortcodes in the shortcode widget.
* The position and expected content of the header depend on your Customizer choices.
* The global Elementor text colors have been overwritten with theme colors.
* Elementor does not understand the theme’s display modes, colors, or HSL settings (use the color classes).
* Elementor makes your site slower unless you have a good cache plugin.
* Use the Canvas-type page templates if you want to drastically customize a page.
* Fictioneer is not meant for site editors; there are limitations you have to live with.

## Images & Media

You can upload images and other media either in the Media Library or directly via drag-and-drop into the editor, as explained in the [official documentation](https://wordpress.org/support/article/media-library-screen/). Make sure to scale and compress your images, because 20 MB of header art will slow down your site to a crawl. There is not much else to add except for one vital concept many new website owners are unaware of: **never hotlink images** unless you have explicit permission. Normal links that need to be clicked are fine.

**Hotlinking:** Refers to embedding images (or other media) that are hosted on an external server, offloading the work and stealing bandwidth. This can cause high cost for the victim and get you into legal trouble, although many servers block hotlinking preemptively for that reason. Imagine this happening to you, having to pay for people merrily posting your images everywhere (copying and re-uploading excluded).

If you are now concerned, you can take a breather. This issue will most likely not immediately (or ever) affect you unless you plan to serve many images per post, page, or chapter. Managed hosts normally also take care of this themselves to save bandwidth, and content delivery networks (CDN) offer their own solutions if necessary. Just keep it in mind and do not become an offender yourself.

## Users & OAuth

Fictioneer offers the option to enable user authentication via the OAuth 2.0 protocol, which you probably know as the annoying "Log in with Google" popover. There are no annoying popovers here, but the functionality remains the same. Instead of username/email and password, you authenticate with a social media account: Discord, Google, Patreon, or Twitch (if [set up](INSTALLATION.md#connections-tab)).

This automatically creates and connects a subscriber account, which makes commenting convenient and allows subscribers to track their progress with Checkmarks, Follows, and Reminders. You also most likely do not need any of that or the potential headache that comes with user management. Unless you host dozens to hundreds of stories, perhaps from several authors, you are better off without. Even then, be aware that a community site requires more server resources, which translates to either bad performance or higher cost.

**Note:** Make sure you have a proper [Privacy Policy](PRIVACY.md) set up before you allow registrations. Fictioneer does not collect undue data and this is an informed, deliberate action. However, privacy is always an issue. This is why subscribers should have the option to self-delete their data and accounts at any time, sparing you a lot of potential trouble (i.e. "the right of erasure").

If everything is set up and the link does not work, flush your permalink structure under **Settings > Permalinks** (just save, you do not need to change anything).

## Checkmarks, Follows & Reminders

Checkmarks, Follows, and Reminders are progress tracking features for logged-in subscribers that need to be enabled in the settings. But unless you host dozens or hundreds of stories, they are mostly a gimmick. Single serials do not need them and readers are quite capable of keeping track with browser features alone. Please refer to [Users & OAuth](#users--oauth) for more considerations regarding user registration. If you decide to enable these features, you should also assign a Bookshelf page in the theme settings.

**Checkmarks:** You can mark chapters and stories as being read, the latter being displayed in your Finished list. This is also reflected in card lists with a check icon in the top-right corner.

**Follows:** You can follow stories to get update notifications on the site (up to 16, not via email) and see them in your Follows list. This is also reflected in card lists with a star icon in the top-right corner.

**Reminders:** You can mark stories to be read later and see them in your Reminders list. This is also reflected in card lists with a clock icon in the top-right corner.

## Bookmarks

Bookmarks are a progress tracking feature that does not require an account. They are only processed client-side and stored locally in the browser, which means they are not available in different browsers on the same or across devices. This convenience can be achieved with an account, though. Bookmarks save the scroll position of a paragraph in a chapter, with an excerpt, thumbnail, progress in percent, and color. You can only have one bookmark per chapter, up to a maximum of 50 bookmarks total.

## User Profile

The default WordPress user profile has been extended with a new Fictioneer section. You also have the option to greatly reduce the profile of _subscribers_ under **Fictioneer > General > Security & Privacy > Reduce subscriber user profile**, getting rid of superfluous fields. Other menus are hidden by default, but it is recommended to use the frontend profile with the User Profile page template.

**Subscribers:**

* **Fingerprint:** Unique user hash used to distinguish commenters with the same nickname. Impersonation is a thing.
* **Profile Flags:** Checkboxes to always use a gravatar, disable your avatar, hide your badge, and persist comment reply notification.
* **OAuth 2.0 Connections:** Connect or disconnect social media accounts as logins: Discord, Google, Patreon, and Twitch.
* **Data:** Summary of submitted data by the user, such as comments, and the option to delete it.

**Authors:**

* **Author Page:** Show the content of a selected page in your author profile instead of the biographical info.
* **Support Message:** Customize the message above support links in chapters.

**Moderators:**

* **Moderation Flags:** Checkboxes to disable selected user capabilities, such as the avatar or commenting.
* **Moderation Message:** Custom message shown in the user’s profile. This can be something nice.

**Administrators:**

* **Badge Override:** Override a user’s badge with a custom string. Do not bully.
* **External Avatar URL:** External link to an avatar image hosted on a CDN.

## Color Variables

You may wonder what the numbers 50-950 in the Customizer color sections are about. These refer to the variable names that hold the respective color, such as `var(--red-500)` or `var(--fg-500)`. Each color is actually a function that adapts to the user’s own settings on the frontend (saturation, lightness, etc.). So using these colors is recommended, because a simple hex code does not care for the user’s preferences.

If you ever want to apply colors with CSS, you can do it like this: `color: var(--fg-500);` or `background-color: var(--bg-700);`. The color options in the post editor are already accounted for, so you do not need to worry there. The most common prefixes are `--bg-#` for background, `--fg-#` for foreground (text), `--primary-#` for links, as well as `--red-#` and `--green-#`. More can be found in the [_properties.scss](src/scss/common/_properties.scss).

## Common Problems

Common problems and how to avoid/fix them.

### Missing Blocks

This is not an error but intentional, the theme only allows blocks that are properly integrated. But you can enable the rest under **Fictioneer > General > Compatibility > Enable all Gutenberg blocks**. There is no guarantee they will work or look good.

### Reserved URL Slugs

There are a few reserved URL slugs you must not use in permalinks, otherwise you will run into 404 error pages or infinite redirect loops. Albeit unlikely, this could happen if you choose post titles similar in name to these slugs. You can change permalinks in the [settings sidebar](https://wordpress.org/support/article/settings-sidebar/) if that ever becomes the case. Reserved slugs:

* oauth2
* download-epub
* fictioneer-logout

### Some block settings look bad or do nothing!

Some block settings lack styling in the theme or have been disabled because they do not work well with the layout. For example, the Latest Posts block ignores the thumbnail settings. Custom font sizes and colors should really be only used on headings or paragraphs.
