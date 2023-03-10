# Documentation

This documentation is about the Fictioneer theme. If you need help with WordPress in general, take a look at the [official documentation](https://wordpress.org/support/category/basic-usage/) or search the Internet for one of the many tutorials. For the installation, look [here](INSTALLATION.md) first and then come back once you are done.

### Table of Contents

* [Stories](#stories)
  * [Meta Fields](#meta-fields)
  * [eBooks/ePUBs](#ebooksepubs)
* [Chapters](#chapters)
  * [Meta Fields](#meta-fields-1)
  * [Text-To-Speech Engine](#text-to-speech-engine)
* [Collections](#collections)
  * [Meta Fields](#meta-fields-2)
* [Recommendations](#recommendations)
  * [Meta Fields](#meta-fields-3)
  * [Example Sentences](#example-sentences)
* [Shared Options](#shared-options)
  * [Search Engine Appearance](#search-engine-appearance)
  * [Landscape Image](#landscape-image)
  * [Page Layout](#page-layout)
  * [Comments](#comments)
  * [Additional CSS Classes](#additional-css-classes)
  * [HTML Block / litRPG Box](#html-block)
* [Shortcodes](#shortcodes)
  * [Blog](#blog)
  * [Bookmarks](#bookmarks)
  * [Contact Form](#contact-form)
  * [Cookie Buttons](#cookie-buttons)
  * [Latest Chapters](#latest-chapters)
  * [Latest Posts](#latest-posts)
  * [Latest Stories](#latest-stories)
  * [Latest Recommendations](#latest-recommendations)
  * [Latest Updates](#latest-updates)
  * [Chapter List](#chapter-list)
  * [Search Form](#search-form)
  * [Showcase](#showcase)
* [Images & Media](#images--media)
* [Users & OAuth](#users--oauth)
* [Checkmarks, Follows & Reminders](#checkmarks-follows--reminders)
* [Bookmarks](#bookmarks)
* [User Profile](#user-profile)
* [Common Problems](#common-problems)
  * [Missing Blocks](#missing-blocks)
  * [Reserved URL Slugs](#reserved-url-slugs)

## Stories

Stories are added under **Stories > Add New**. Required fields are the short description, status, and age rating. You should be thorough with the setup, especially the taxonomies if you have more than a few stories on your site, because they can be searched for. Just avoid adding excessive lists of tags.

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
| Short Description * | Content | The short description is used in the story list cards.
| Chapters | List | Add and sort chapters assigned to the story. Assignment is done in the chapters.
| Custom Pages | List | Add up to four pages as extra tabs. Requires the short name field to show up.
| Upload Ebook | File | Upload an epub, mobi, ibooks, azw, azw3, kf8, kfx, pdf, iba, or txt file.
| ePUB Preface | Content | Disclaimers/etc. for generated ePUBs. Required for the download button to show up.
| ePUB Afterword | Content | Will be appended after the last chapter in generated ePUBs.
| ePUB Custom CSS | Text | Inject custom styles into the generated ePUB. For advanced users.
| Taxonomies (Various) | List | Genres, fandoms, characters, warnings, tags, and categories (include story name).
| Cover Image | Image | Cropped to an aspect ration of 2:3 from the center.
| Status * | Select | Choose between ongoing, completed, oneshot, hiatus, and cancelled.
| Age Rating * | Select | Choose between everyone, teen, mature, and adult.
| Co-Authors | List | List of co-authors. They must be registered users, but dummies will do.
| Copyright Notice | String | Line below the content to declare copyrights if necessary.
| Top Web Fiction Link | URL | Link to your story on [Top Web Fiction](https://topwebfiction.com/).
| Sticky in all lists | Check | Stick the story to the top of the first page in lists.
| Disable ePUB download | Check | Disable ePUB downloads for the story everywhere.
| Hide thumbnail on story page | Check | Hide the cover image on the page but not in lists.
| Hide tags on story page | Check | Hide *all* taxonomies except warnings on the page but not in lists.
| Hide chapter icons | Check | Hide chapter icons.
| Disable collapsing of chapters | Check | Disable collapsing of long chapter lists (13+ as 5\|n\|5 per group).
| Hide chapter icons | Check | Hide chapter icons.
| Disable chapter groups | Check | Ignore chapter groups.
| Custom Story CSS | Text | Inject custom styles for the story and chapters. For advanced users.
| Custom Header Image | Image | Override the default header image for the story and chapters.
| Custom CSS | Text | Inject custom styles into the story page (but not chapters).
| Support Links (Various) | URL | Links to subscription campaigns. Falls back to the author’s profile if left blank.
| Disable commenting | Check | Disable new comments but keep the current ones visible.

### eBooks/ePUBs

A manually uploaded eBook will always supersede an automatically generated ePUB on the site, as this is a deliberate action. Which also means you need to keep it up-to-date yourself and there are no download statistics. If you want the generated ePUB, you need to fill the Preface content for the story, which should contain copyrights and disclaimers. Because once a file is on the Internet, it will stay on the Internet. Make sure everything is legally sound before that.

**Supported:** Epubs only support paragraphs, headings, lists, tables, blockquotes, pullquotes, images, spacers, and custom HTML at your own peril. Anything else will be filtered out, such as videos.

**Sensitive Content:** You can mark sensitive content in chapters and provide an alternative, which users can choose from. Generated ePUBs always use the sensitive (uncensored) content, not the alternative if provided.

#### Example Disclaimer for Originals:
> This is a work of fiction. Names, characters, business, events and incidents are the products of the author’s imagination. Any resemblance to actual persons, living or dead, or actual events is purely coincidental.
>
> Copyright &#169; `AUTHOR`. All rights reserved.

#### Example Disclaimer for Fanfictions:
> This is a work of fan fiction and not written for profit. Names, characters, business, events and incidents are the products of the author’s imagination. Any resemblance to actual persons, living or dead, or actual events is purely coincidental. Any trademarked characters and elements used belong to their respective copyright holders, who bear no responsibility for this work.
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
| Chapter Icon * | String | Free [Font Awesome](https://fontawesome.com/search) class string. Defaults to `fa-solid fa-book`.
| Chapter Text Icon | String | Overrides icon with a text string, good for combining with symbol fonts.
| Chapter Short Title | String | Optional short chapter title, not currently used by the base theme.
| Chapter Prefix | String | Prepended to the title in chapter lists. Not used in generated ePUBs.
| Co-Authors | List | List of co-authors. They must be registered users, but dummies will do.
| Unlisted? | Check | Hide the chapter in all lists, but keep it accessible with the link.
| Not a Chapter? | Check | Exclude the chapter from chapter counts.
| Hide Title? | Check | Hide the title and author on chapter pages.
| Hide Support Links? | Check | Hide support links at the end of the chapter.
| Chapter Age Rating | Select | Choose between everyone, teen, mature, and adult.
| Chapter Warning | String | _Short_ warning displayed in chapter lists and above the chapter title.
| Warning Notes | Text | Additional warning notes rendered above the chapter title.
| Warning Color | Color | Change the color of the warning, but consider the legibility in light/dark mode.

### Text-To-Speech Engine

Must be enabled in the settings and is started from the paragraph tools. Makes use of the free [Web Speech API](https://developer.mozilla.org/en-US/docs/Web/API/Web_Speech_API) that all modern browsers support, which can be wonky at times but produces surprisingly decent results. Primarily meant as accessibility feature for the reading-impaired. Absolutely _not_ fail-proof and depends on the browser and operating system; additional permissions may be necessary.

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
| Collection Items * | List | Add and sort posts, pages, stories, chapters, recommendations, and collections.
| Short Description * | Content | The short description is used in the collection list cards.
| Taxonomies (Various) | List | Genres, fandoms, characters, warnings, tags, and categories (include story name).
| Collection Cover Image | Image | Cropped to an aspect ration of 2:3 from the center.

## Recommendations

Recommendations are added under **Recommendations > Add New**. Required fields are the author of the recommended story, primary URL, general URLs, and "one sentence" abbreviation as description on small cards. Large cards use the normal excerpt. Recommendations are meant to be personal promotions of great stories by your fellow authors and to shine light on hidden gems.

### Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| One Sentence * | String | 150 characters or less "elevator pitch" to describe the story.
| Author * | String | The author of the recommended story.
| Primary URL * | String | Primary link to the recommendation or author’s website.
| URLs * | Text | Special formatted list of links to the recommendation, one per line.
| Support | Text | Special formatted list of links to support the author, one per line.
| Taxonomies (Various) | List | Genres, fandoms, characters, warnings, tags, and categories.
| Recommendation Cover Image | Image | Cropped to an aspect ration of 2:3 from the center.

### Example Sentences

Think of the sentence as elevator pitch, something you can tell within a few seconds to get the point across. Skip the details, hint at the plot, describe the concept — the story has all the time to tell itself later. Because more often than not, readers will only glimpse at a story while browsing. Recommendations are not prominently featured on _your_ site, after all.

> Rebellious corporate heiress and her genius friend commit high-tech heists in a doomed city on the edge of the future.

> Schoolgirl gets reincarnated into a fantasy world, though not as heroine but as tentacle monster!

> Haunted student discovers her nightmares of gods and horrors from beyond reality are no hallucinations after all.

> Girl from the slums discovers her talent for necromancy and learns to love the existential terror she becomes.

> Two women forge an unlikely bond and explore the answer to a simple question: is selling your body the same as selling yourself?

> Reanimated girls from different epochs roam the ruins of civilizations on the scarred corpse of Earth.

## Pages

Pages work the same as always in WordPress, just with some additional fields and template options. [Change the template](https://wordpress.org/support/article/pages/#page-templates) in the settings sidebar, new options being **Chapters**, **Collections**, **Recommendations**, **Bookmarks**, **Bookshelf**, **Bookshelf AJAX**, **Taxonomies**, **No Title Page**, **Stories**, and **User Profile**. You can assign these template pages to certain tasks under **Fictioneer > General > Page Assignments**.

* **Chapters:** Shows a list of all visible chapters ordered by publishing date, descending.
* **Stories:** Shows a list of all visible stories ordered by publishing date, descending.
* **Collections:** Shows a list of all visible collections ordered by publishing date, descending.
* **Recommendations:** Shows a list of all visible recommendations ordered by publishing date, descending.
* **Bookmarks:** Shows bookmarks without the need for a shortcode. Cache compatible.
* **Bookshelf:** Shows paginated lists of an user’s Follows, Reminders, and finished stories.
* **Bookshelf AJAX:** Cache compatible version of the Bookshelf, fetching the content after the page has loaded.
* **No Title Page:** Default page template but without the heading. Good for a frontpage.
* **Taxonomies:** Shows details about all taxonomies used on the site, with count and definition (if provided).
* **User Profile:** Frontend account profile to keep users out of the admin. Must never be cached!

### Meta Fields

| Field | Type | Explanation
| :-- | :-: | :--
| Short Name | String | Shortened name of the page required for custom tabs in stories.
| Filter & Search ID | String | Custom identifier to be used with plugin. Does nothing on its own.

## Shared Options

These fields and options are available in most post types, which does not mean they make sense everywhere.

### Search Engine Appearance

Metadata for search engine results, schema graphs, and social media embeds. If left blank, defaults will be derived from the post content. You can use `{{title}}`, `{{site}}`, and `{{excerpt}}` as placeholders. Titles should not exceed 70 characters but this is not enforced. The Open Graph image is either set manually (click on the box) or defaults to the post thumbnail, parent thumbnail, or site default in that order. Whether these services actually display the offered data is entirely up to them. After all, you could write anything in there.

![SEO Appearance](repo/assets/seo_appearance.jpg?raw=true)

### Landscape Image

Allows you to choose an alternate featured/cover image that is used for thumbnails which are wider than high, such as with the _showcase_ shortcode. The aspect ratio is between 2:1 and 3:1 depending on the viewport, cropped from the center and fit to cover. Chapters default to their parent story as usual.

**Supports:** Posts, Pages, Stories, Chapters, Collections, Recommendations

### Page Layout

Allows you to choose an alternate header image instead of the site default and inject custom CSS into the \<head>, affecting the look of the whole page. Chapters inherit these changes from their parent story or use their own. You can use this to give each story or chapter an individual touch, but be careful with the CSS.

**Supports:** Pages, Stories, Chapters, Collections, Recommendations

### Support Links

A collection of optional support links: Patreon, Ko-fi, SubscribeStar, PayPal, and a generic donation link for anything else. They are displayed in several places, such as under each chapter unless disabled. You can set different links per chapter and story, defaulting to the parent or author profile if left empty.

**Supports:** Posts, Stories, Chapters

### Comments

Option to disable commenting on the page. Unlike the "allow comments" option, this will only disable the comment form but still show the current comments.

**Supports:** Posts, Pages, Stories, Chapters

### Additional CSS Classes

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
| `overflow-x` | Adds horizontal scrolling if a block is too wide. Not necessary on tables.
| `no-auto-lightbox` | Prevents the lightbox script from being applied if added to an `<img>` element.

### HTML Block

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

## Shortcodes

[Shortcodes](https://wordpress.org/support/article/shortcode-block/) are bracket-enclosed keywords placed within the content that WordPress automatically interprets into code, adding features or objects without the need for programming. This should be done inside a _shortcode_ block. Since most elements created by shortcodes have no margins, the _spacer_ block can be a good addition before and/or after.

### Blog

Renders paginated blog posts akin to the default blog page, but with options. Makes use of the main query pagination variable, so only use this once per page. Optional parameters are **per_page**, **author**, **exclude_cat_ids**, **exclude_tag_ids**, **categories**, **tags**, **rel**, and **class**.

* **per_page:** Number of posts per page. Defaults to theme settings.
* **author:** Only show chapters of a specific author. Make sure to write the name right.
* **exclude_cat_ids:** Comma-separated list of category IDs, if you want to exclude some.
* **exclude_tag_ids:** Comma-separated list of tag IDs, if you want to exclude some.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.

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

Renders a two-column grid of small bookmark cards, ordered by date of creation. The bookmarks are stored in the browser and appended to the document via JavaScript. You can combine this with the `show-if-bookmarks hidden` additional CSS classes, displaying a headline or other element only if bookmarks are present. Optional parameters are **count** and **show_empty**.

* **count:** Limit bookmarks to any positive number. Default `-1` (all).
* **show_empty:** Whether to show a "no bookmarks" note or nothing if empty. Default `false`.

```
[fictioneer_bookmarks]
```

```
[fictioneer_bookmarks count="8" show_empty="true"]
```

![Bookmarks](repo/assets/shortcode_example_bookmarks.jpg?raw=true)

### Chapter List

Renders a list of chapters identical to those on story pages, ordered by sequence in the source. Must have either the **story** or **chapters** parameter. Optional parameters are **count**, **offset**, **group**, **heading**, and **class**.

* **story:** ID of a single story. You need either this or **chapters**.
* **chapters:** Comma-separated list of chapter IDs. You need either this or **story**.
* **count:** Limit chapters to any positive number. Default `-1` (all).
* **offset:** Skip a number of chapters, which can make sense if you query all.
* **heading:** Show a heading with collapse toggle above the list.
* **group:** Only show chapters with a specific group name, which can transcend stories.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_chapter_list story="69"]
```

```
[fictioneer_chapter_list story="69" count="10" offset="2"]
```

```
[fictioneer_chapter_list chapters="13,21,34" heading="Pigs are a lot bigger than you expect" group="You could ride it"]
```

![Chapter List](repo/assets/shortcode_example_chapter_list_1.jpg?raw=true)

### Contact Form

Renders a contact form with various (optional) fields. Submissions are validated, sanitized, have basic spam protection, and are checked against the WordPress disallow list under **Settings > Discussions**. If all steps are passed, the submission is sent to the email addresses listed under **Fictioneer > General > Contact Form Receivers**, which are never revealed to the public. If empty, the admin email address is used instead. Optional parameters are **title**, **submit**, **privacy_policy**, **required**, **email**, **name**, **text_[1-6]**, **check_[1-6]**, and **class**.

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

Renders a two-column grid of small cards, showing the latest four chapters ordered by publishing date, descending. Optional parameters are **count**, **type**, **author**, **order**, **orderby**, **spoiler**, **source**, **chapters**, **categories**, **tags**, **fandoms**, **genres**, **characters**, **rel**, and **class**.

* **count:** Limit chapters to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default`, `simple`, or `compact`. The other variants are smaller with less data.
* **author:** Only show chapters of a specific author. Make sure to write the name right.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `modified` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **spoiler:** The excerpt is obfuscated, set `true` if you want to reveal it. Default `false`.
* **source:** Set `false` to hide the author and story nodes. Default `true`.
* **chapters:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_latest_chapters]
```

```
[fictioneer_latest_chapters genres="adventure, historical" characters="indiana jones"]
```

```
[fictioneer_latest_chapters count="10" type="compact" author="Tetrakern" order="asc" orderby="modified" spoiler="true" source="false" chapters="1,2,3,5,8,13,21,34"]
```

![Latest Chapters](repo/assets/shortcode_example_latest_chapters.jpg?raw=true)

### Latest Posts

Renders the last blog post or a list of blog posts, ignoring sticky posts, ordered by publishing date, descending. Optional parameters are **count**, **author**, **posts**, **categories**, **tags**, **rel**, and **class**.

* **count:** Limit posts to any positive number, although you should keep it reasonable. Default `1`.
* **author:** Only show posts of a specific author. Make sure to write the name right.
* **posts:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.

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

Renders a two-column grid of small cards, showing the latest four recommendations ordered by publishing date, descending. Optional parameters are **count**, **type**, **author**, **order**, **orderby**, **recommendations**, **categories**, **tags**, **fandoms**, **genres**, **characters**, **rel**, and **class**.

* **count:** Limit recommendations to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default` or `compact`. The compact variant is smaller with less data.
* **author:** Only show recommendations by a specific author. Make sure to write the name right.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `modified` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **recommendations:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_latest_recommendations]
```

```
[fictioneer_latest_recommendations genres="isekai" fandoms="original, fanfiction"]
```

```
[fictioneer_latest_recommendations count="10" type="compact" author="Tetrakern" order="asc" orderby="rand" recommendations="1,2,3,5,8,13,21,34"]
```

![Latest Recommendations](repo/assets/shortcode_example_latest_recommendations.jpg?raw=true)

### Latest Stories

Renders a two-column grid of small cards, showing the latest four stories ordered by publishing date, descending. Optional parameters are **count**, **type**, **author**, **order**, **orderby**, **stories**, **exclude_cat_ids**, **exclude_tag_ids**, **categories**, **tags**, **fandoms**, **genres**, **characters**, **rel**, and **class**.

* **count:** Limit stories to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default` or `compact`. The compact variant is smaller with less data.
* **author:** Only show stories of a specific author. Make sure to spell the _username_ right.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `modified` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **stories:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **exclude_cat_ids:** Comma-separated list of category IDs to exclude.
* **exclude_tag_ids:** Comma-separated list of tag IDs to exclude.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_latest_stories]
```

```
[fictioneer_latest_stories genres="adventure, cyberpunk" characters="Rebecca" rel="or"]
```

```
[fictioneer_latest_stories count="10" type="compact" author="Tetrakern" order="asc" orderby="modified" stories="1,2,3,5,8,13,21,34"]
```

![Latest Stories](repo/assets/shortcode_example_latest_stories.jpg?raw=true)

### Latest Updates

Renders a two-column grid of small cards, showing the latest four updated stories ordered by date of the last chapter change, descending. Optional parameters are **count**, **type**, **author**, **order**, **stories**, **categories**, **tags**, **fandoms**, **genres**, **characters**, **rel**, and **class**.

* **count:** Limit updates to any positive number, although you should keep it reasonable. Default `4`.
* **type:** Either `default`, `simple`, or `compact`. The other variants are smaller with less data.
* **author:** Only show updates of a specific author. Make sure to write the name right.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **stories:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **categories:** Comma-separated list of category names (case-insensitive), if you want to pick from a curated pool.
* **tags:** Comma-separated list of tag names (case-insensitive), if you want to pick from a curated pool.
* **fandoms:** Comma-separated list of fandom names (case-insensitive), if you want to pick from a curated pool.
* **genres:** Comma-separated list of genre names (case-insensitive), if you want to pick from a curated pool.
* **characters:** Comma-separated list of character names (case-insensitive), if you want to pick from a curated pool.
* **rel:** Relationship between different taxonomies, either `AND` or `OR`. Default `AND`.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_latest_updates]
```

```
[fictioneer_latest_updates genres="romance, drama" fandoms="original"]
```

```
[fictioneer_latest_updates count="10" type="simple" author="Tetrakern" order="asc" stories="1,2,3,5,8,13,21,34"]
```

![Latest Updates](repo/assets/shortcode_example_latest_updates.jpg?raw=true)

### Search Form

Renders the search form with advanced options (if not disabled in the settings). Optional parameters are **simple** and **placeholder**.

* **simple:** Set `true` to hide the advanced search options. Default `false`.
* **placeholder:** Change the placeholder text.

```
[fictioneer_search]
```

```
[fictioneer_search simple="true" placeholder="What are you looking for?"]
```

![Contact Form](repo/assets/shortcode_example_search_1.jpg?raw=true)

### Showcase

Renders dynamic grid of thumbnails with title, showing the latest eight posts of the specified type ordered by publishing date, descending. Requires **for** parameter. Optional parameters are **count**, **author**, **order**, **orderby**, **posts**, **no_cap**, and **class**. The thumbnail is either the **Landscape Image** or **Cover Image** (if available), with chapters defaulting to the parent story.

* **for:** Desired post type, either `stories`, `chapters`, `collections`, or `recommendations`.
* **count:** Limit posts to any positive number, although you should keep it reasonable. Default `8`.
* **author:** Only show posts for a specific author. Make sure to write the name right.
* **order:** Either `desc` (descending) or `asc` (ascending). Default `desc`.
* **orderby:** The default is `date`, but you can also use `rand` and [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **posts:** Comma-separated list of post IDs, if you want to pick from a curated pool.
* **no_cap:** Set `true` if you want to hide the caption.
* **class:** Additional CSS classes, separated by whitespace.

```
[fictioneer_showcase for="collections"]
```

```
[fictioneer_showcase for="collections" count="10" author="Tetrakern" order="asc" posts="1,2,3,5,8,13,21,34"]
```

![Showcase](repo/assets/shortcode_example_showcase.jpg?raw=true)

## Images & Media

You can upload images and other media either in the Media Library or directly via drag-and-drop into the editor, as explained in the [official documentation](https://wordpress.org/support/article/media-library-screen/). Make sure to scale and compress your images, because 20 MB of header art will slow down your site to a crawl. There is not much else to add except for one vital concept many new website owners are unaware of: **never hotlink images** unless you have explicit permission. Normal links that need to be clicked are fine.

**Hotlinking:** Refers to embedding images (or other media) that are hosted on an external server, offloading the work and stealing bandwidth. This can cause high cost for the victim and get you into legal trouble, although many servers block hotlinking preemptively for that reason. Imagine this happening to you, having to pay for people merrily posting your images everywhere (copying and re-uploading excluded).

If you are now concerned, you can take a breather. This issue will most likely not immediately (or ever) affect you unless you plan to serve many images per post, page, or chapter. Managed hosts normally also take care of this themselves to save bandwidth, and content delivery networks (CDN) offer their own solutions if necessary. Just keep it in mind and do not become an offender yourself.

## Users & OAuth

Fictioneer offers the option to enable user authentication via the OAuth 2.0 protocol, which you probably know as the annoying "Log in with Google" popover. There are no annoying popovers here, but the functionality remains the same. Instead of username/email and password, you authenticate with a social media account: Discord, Google, Patreon, or Twitch (if [set up](INSTALLATION.md#connections-tab)).

This automatically creates and connects a subscriber account, which makes commenting convenient and allows subscribers to track their progress with Checkmarks, Follows, and Reminders. You also most likely do not need any of that or the potential headache that comes with user management. Unless you host dozens to hundreds of stories, perhaps from several authors, you are better off without. Even then, be aware that a community site requires more server resources, which translates to either bad performance or higher cost.

**Note:** Make sure you have a proper [Privacy Policy](PRIVACY.md) set up before you allow registrations. Fictioneer does not collect undue data and this is an informed, deliberate action. However, privacy is always an issue. This is why subscribers should have the option to self-delete their data and accounts at any time, sparing you a lot of potential trouble (i.e. "the right of erasure").

## Checkmarks, Follows & Reminders

Checkmarks, Follows, and Reminders are progress tracking features for logged-in subscribers that need to be enabled in the settings. But unless you host dozens or hundreds of stories, they are mostly a gimmick. Single serials do not need them and readers are quite capable of keep track with browser features alone. Please refer to [Users & OAuth](#users--oauth) for more considerations regarding user registration. If you decide to enable these features, you should also assign a Bookshelf page in the theme settings.

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

## Common Problems

Common problems and how to avoid/fix them.

### Missing Blocks

This is not an error but intentional, the theme only allows blocks that are properly integrated. But you can enable the rest under **Fictioneer > General > Compatibility > Enable all default Gutenberg blocks**. There is no guarantee they will work or look good.

### Reserved URL Slugs

There are a few reserved URL slugs you must not use in permalinks, otherwise you will run into 404 error pages or infinite redirect loops. Albeit unlikely, this could happen if you choose post titles similar in name to these slugs. You can change permalinks in the [settings sidebar](https://wordpress.org/support/article/settings-sidebar/) if that ever becomes the case. Reserved slugs:

* oauth2
* download-epub
* fictioneer-logout
