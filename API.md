# Storygraph API

Perhaps "API" is promising a bit much

## Configuration

## Endpoint: Story

Query this endpoint to retrieve a story and collection of associated chapters. The response is cached for performance reasons.

```
GET /wp-json/storygraph/v1/story/<id>
```

| Argument | Description |
| :-- | :-- |
| id | ID of the story. Required.

### Schema

The following schema defines all fields that can exist within the response, excluding fields that are empty or `null` unless stated otherwise. So if there are no chapters, the chapter node will be missing. All values are escaped.

| Story Field | Description |
| :-- | :-- |
| id `integer` | ID of the story (on the site).
| guid `string` | Sufficiently unique global ID based on the original URL, type, and ID. Unreliable as link.
| url `string` | Current URL of the story.
| language `string` | Language code of the story, not necessarily chapters.
| title `string` | Title of the story.
| author `object\|null` | Author node.
| &emsp;➞ name `string` | Author name.
| &emsp;➞ url `string\|null` | Author website.
| coAuthors `[object]\|null` | Array of co-author nodes. Same as the author node.
| content `string` | Return value of `get_the_content()` without filters applied. Needs processing.
| description `string` | HTML string from TinyMCE. Needs processing.
| words `integer` | Total number of words of all chapters.
| ageRating `string` | Either Everyone, Teen, Mature, or Adult.
| chapterCount `integer` | Total number of chapters, excluding those marked as non-chapters.
| published `integer` | Unix timestamp of when the story was published (GMT).
| modified `integer` | Unix timestamp of when the story was last updated (GMT).
| protected `boolean` | Whether the story is protected by a password.
| images `object\|null` | Images node.
| &emsp;➞ hotlinkAllowed `boolean` | Whether hotlinking images is allowed. Copy and host them yourself otherwise.
| &emsp;➞ header `string\|null` | URL of header image.
| &emsp;➞ cover `string\|null` | URL of cover image.
| taxonomies `object\|null` | Taxonomy collection node.
| &emsp;➞ tags `[string]\|null` | Array of tags.
| &emsp;➞ fandoms `[string]\|null` | Array of fandoms.
| &emsp;➞ characters `[string]\|null` | Array of characters.
| &emsp;➞ warnings `[string]\|null` | Array of warnings.
| &emsp;➞ genres `[string]\|null` | Array of genres.
| chapters `[object]\|null` | Array of chapter nodes. See **Chapter Fields**.
| support `object\|null` | Support collection node.
| &emsp;➞ topwebfiction `string\|null` | URL to story’s TopWebFiction entry.
| &emsp;➞ patreon `string\|null` | URL to the story’s or author’s Patreon page.
| &emsp;➞ kofi `string\|null` | URL to the story’s or author’s Ko-fi page.
| &emsp;➞ subscribestar `string\|null` | URL to the story’s or author’s SubscribeStar page.
| &emsp;➞ paypal `string\|null` | URL to the story’s or author’s PayPal address.
| &emsp;➞ donation `string\|null` | URL to the story’s or author’s donation page.
| timestamp `integer` | Unix timestamp of when the response was compiled (GMT). May be cached.

<br>

| Chapter Field | Description |
| :-- | :-- |
| id `integer` | ID of the chapter (on the site).
| guid `string` | Sufficiently unique global ID based on the original URL, type, and ID. Unreliable as link.
| url `string` | Current URL of the chapter.
| language `string` | Language code of the chapter.
| prefix `string\|null` | Title prefix, such as "Act 1".
| title `string` | Title of the chapter.
| group `string\|null` | Group of the chapter.
| author `object\|null` | Author node.
| &emsp;➞ name `string` | Author name.
| &emsp;➞ url `string\|null` | Author website.
| coAuthors `[object]\|null` | Array of co-author nodes. Same as the author node.
| published `integer` | Unix timestamp of when the chapter was published (GMT).
| modified `integer` | Unix timestamp of when the chapter was last updated (GMT).
| protected `boolean` | Whether the chapter is protected by a password.
| words `integer` | Number of words.
| nonChapter `boolean` | Whether the chapter is marked as non-chapter.
| ageRating `string\|null` | Either Everyone, Teen, Mature, or Adult (if different from story).
| warning `string\|null` | Simple content warning notice.
| taxonomies `object\|null` | Taxonomy collection node.
| &emsp;➞ tags `[string]\|null` | Array of tags.
| &emsp;➞ fandoms `[string]\|null` | Array of fandoms.
| &emsp;➞ characters `[string]\|null` | Array of characters.
| &emsp;➞ warnings `[string]\|null` | Array of warnings.
| &emsp;➞ genres `[string]\|null` | Array of genres.

<details>
  <summary><strong>Example Response:</strong> https://fictioneer-theme.com/wp-json/storygraph/v1/story/13</summary><br>

  ```json
  {
    "id": 13,
    "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_story&#038;p=13",
    "url": "https:\/\/fictioneer-theme.com\/story\/katalepsis\/",
    "language": "en-US",
    "title": "Katalepsis",
    "author": {
      "name": "Hungry",
      "url": "https:\/\/katalepsis.net\/"
    },
    "content": "<!-- wp:paragraph -->\n<p>Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Until she meets Raine and Evelyn, that is \u2014 self-proclaimed bodyguard and bad-tempered magician \u2014 and learns she\u2019s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Heather plunges into a world of eldritch magic and fanatic cultists, trying to stay alive, stay sane, and deal with her own blossoming attraction to dangerous women. But being \u2018In The Know\u2019 isn\u2019t all terror and danger. Sometimes the monsters wear nice dresses and stick around for afternoon tea. Sometimes you find you have more in common with them than you think. Perhaps this is Heather\u2019s chance to be something more than the defeated husk she\u2019d grown up as, to find real friendship and meaning among things like herself \u2013 and perhaps, out there on the rim of the possible, to bring her twin sister back from the dead.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on <a rel=\"noreferrer noopener\" href=\"https:\/\/katalepsis.net\/\" data-type=\"URL\" data-id=\"https:\/\/katalepsis.net\/\" target=\"_blank\">www.katalepsis.net<\/a>.<\/strong><\/p>\n<!-- \/wp:paragraph -->",
    "description": "Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement. Until she meets Raine and Evelyn, that is \u2014 self-proclaimed bodyguard and bad-tempered magician \u2014 and learns she\u2019s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.",
    "words": 95558,
    "ageRating": "Teen",
    "status": "Ongoing",
    "chapterCount": 17,
    "published": 1673218246,
    "modified": 1674004959,
    "protected": false,
    "images": {
      "hotlinkAllowed": false,
      "header": "https:\/\/fictioneer-theme.com\/wp-content\/uploads\/2023\/01\/katalepsis_header.jpg",
      "cover": "https:\/\/res.cloudinary.com\/dmhr3ab5n\/images\/f_auto,q_auto\/v1674220342\/fictioneer-demo\/katalepsis_cover\/katalepsis_cover.jpg?_i=AA"
    },
    "taxonomies": {
      "tags": ["Drugs", "Magic", "Mental Illness", "Polyamory"],
      "fandoms": ["Original"],
      "warnings": ["Gore", "Violence"],
      "genres": ["Body Horror", "Cosmic Horror", "Girls Love", "Romance", "Urban Fantasy"]
    },
    "chapters": [{
      "id": 29,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=29",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-1\/",
      "language": "en-US",
      "title": "mind; correlating \u2013 1.1",
      "group": "mind; correlating",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673842132,
      "modified": 1674004959,
      "protected": false,
      "words": "8040",
      "nonChapter": false,
      "ageRating": "Teen",
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 35,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=35",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-2\/",
      "language": "en-US",
      "title": "mind; correlating \u2013\u00a01.2",
      "group": "mind; correlating",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673224433,
      "modified": 1673978225,
      "protected": false,
      "words": "5949",
      "nonChapter": false,
      "ageRating": "Teen",
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 40,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=40",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-3\/",
      "language": "en-US",
      "title": "mind; correlating \u2013\u00a01.3",
      "group": "mind; correlating",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673224752,
      "modified": 1673978225,
      "protected": true,
      "words": "5242",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 45,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=45",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-4\/",
      "language": "en-US",
      "title": "mind; correlating \u2013\u00a01.4",
      "group": "mind; correlating",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673224920,
      "modified": 1673978225,
      "protected": false,
      "words": "4509",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 49,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=49",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-5\/",
      "language": "en-US",
      "title": "mind; correlating \u2013\u00a01.5",
      "group": "mind; correlating",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225068,
      "modified": 1673978225,
      "protected": false,
      "words": "8272",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 52,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=52",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-1\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.1",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225167,
      "modified": 1673978225,
      "protected": false,
      "words": "4249",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 57,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=57",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-2\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.2",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225345,
      "modified": 1673978225,
      "protected": false,
      "words": "5836",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 63,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=63",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-3\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.3",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225481,
      "modified": 1673978208,
      "protected": false,
      "words": "5173",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 66,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=66",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-4\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.4",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225609,
      "modified": 1673978207,
      "protected": false,
      "words": "3892",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 69,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=69",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-5\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.5",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225665,
      "modified": 1673978207,
      "protected": false,
      "words": "5761",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 74,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=74",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-6\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.6",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225757,
      "modified": 1673978207,
      "protected": false,
      "words": "5043",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 77,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=77",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-7\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.7",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225843,
      "modified": 1673978207,
      "protected": false,
      "words": "5640",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 80,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=80",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-8\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.8",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225916,
      "modified": 1673978207,
      "protected": false,
      "words": "4919",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 83,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=83",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-9\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.9",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673225981,
      "modified": 1673978207,
      "protected": false,
      "words": "6068",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 86,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=86",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-10\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.10",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673226040,
      "modified": 1673978207,
      "protected": false,
      "words": "7155",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 89,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=89",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-11\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.11",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673226099,
      "modified": 1673978207,
      "protected": false,
      "words": "5109",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }, {
      "id": 92,
      "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=92",
      "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-12\/",
      "language": "en-US",
      "title": "providence or atoms \u2013\u00a02.12",
      "group": "providence or atoms",
      "author": {
        "name": "Hungry",
        "url": "https:\/\/katalepsis.net\/"
      },
      "published": 1673226161,
      "modified": 1673978207,
      "protected": false,
      "words": "4701",
      "nonChapter": false,
      "taxonomies": {
        "tags": ["Magic", "Mental Illness"],
        "fandoms": ["Original"],
        "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
      }
    }],
    "support": {
      "topwebfiction": "http:\/\/topwebfiction.com\/listings\/katalepsis",
      "patreon": "https:\/\/www.patreon.com\/hazelyoung\/posts"
    },
    "timestamp": 1676730691
  }
  ```

  Prettified by [beautifier.io](https://beautifier.io/).
</details>

## Endpoint: Stories

Query this endpoint to retrieve a collection of stories and associated meta data. The response is paginated and cached for performance reasons, use the `page` argument to browse through the pages.

```
GET /wp-json/storygraph/v1/stories
```

| Argument | Description |
| :-- | :-- |
| page | Page to return. Default `1`.

### Schema

The following schema defines all fields that can exist within the response, excluding fields that are empty or `null` unless stated otherwise. So if there are no chapters, the chapter node will be missing. All values are escaped.

| Field | Description |
| :-- | :-- |
| url `string` | Root URL of the targeted site. In case you forgot or want to make sure.
| language `string` | Language code of the _site_, not necessarily stories or chapters.
| storyCount `integer` | Total number of published and visible stories.
| chapterCount `integer` | Total number of published and visible chapters.
| lastPublished `integer\|null` | Unix timestamp of the last published story (GMT).
| lastModified `integer\|null` | Unix timestamp of the last modified story (GMT).
| stories `object\|null` | Paginated collection of story nodes, ordered by publishing date. See **Story** endpoint.
| lastModifiedStory `integer\|null` | Foreign ID (key) of the last modified story.
| separateChapters `boolean` | Whether chapters are included or must be requested via the **Story** endpoint.
| page `integer` | Current page of the story collection.
| perPage `integer` | Stories per collection page.
| maxPages `integer` | Total number of collection pages.
| timestamp `integer` | Unix timestamp of when the response was compiled (GMT). May be cached.

<details>
  <summary><strong>Example Response:</strong> https://fictioneer-theme.com/wp-json/storygraph/v1/stories</summary><br>

  ```json
  {
    "url": "https:\/\/fictioneer-theme.com",
    "language": "en-US",
    "storyCount": 5,
    "chapterCount": 36,
    "lastPublished": 1673661557,
    "lastModified": 1675341424,
    "stories": {
      "284": {
        "id": 284,
        "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_story&#038;p=284",
        "url": "https:\/\/fictioneer-theme.com\/story\/the-sapphire-shadow\/",
        "language": "en-US",
        "title": "The Sapphire Shadow",
        "author": {
          "name": "James Wake"
        },
        "content": "<!-- wp:paragraph -->\n<p>Nadia was born the heiress to a corporate empire, destined for a life of wealth and privilege in what used to be America. But she would rather spend her time committing high-tech heists, aided and abetted by her oldest friend, Tess. Tess is a technical genius, a snarky hacker who designed and built her own right arm, and together, they pull off a series of daring crimes.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Jackson was raised in the slums. Now she\u2019s a cop, finally living on the right side of the old sea walls. She\u2019s supposed to be hunting Cheshire, a reclusive hacktivist stirring up unrest, but the nightly news is full of a woman breaking the law and getting away with it, blowing kisses as she escapes. Jackson hates every minute of it.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>When Nadia uncovers a plot that could change the very nature of humanity, she is forced to confront the powers that rule her home. And Jackson has to question who, exactly, her badge serves.&nbsp;<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>In a doomed city on the edge of the future, both women are caught fighting to survive. But an unlikely romance, and an even unlikelier partnership, might be the only thing that saves Nadia.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story for free on <a rel=\"noreferrer noopener\" href=\"https:\/\/offprint.net\/work\/k681hsimp\/the-sapphire-shadow\" data-type=\"URL\" data-id=\"https:\/\/katalepsis.net\/\" target=\"_blank\">www.offprint.net<\/a> or buy the <a rel=\"noreferrer noopener\" href=\"https:\/\/www.amazon.com\/Sapphire-Shadow-James-Wake-ebook\/dp\/B088QMZC4R\" target=\"_blank\">ebook<\/a>.<\/strong><\/p>\n<!-- \/wp:paragraph -->",
        "description": "Nadia was born the heiress to a corporate empire, destined for a life of wealth and privilege in what used to be America. But she would rather spend her time committing high-tech heists, aided and abetted by her oldest friend, Tess. Tess is a technical genius, a snarky hacker who designed and built her own right arm, and together, they pull off a series of daring crimes.",
        "words": 21574,
        "ageRating": "Mature",
        "status": "Completed",
        "chapterCount": 5,
        "published": 1673661557,
        "modified": 1674005323,
        "protected": false,
        "images": {
          "hotlinkAllowed": false,
          "cover": "https:\/\/res.cloudinary.com\/dmhr3ab5n\/images\/f_auto,q_auto\/v1674220298\/fictioneer-demo\/sapphire_shadow_cover\/sapphire_shadow_cover.jpeg?_i=AA"
        },
        "taxonomies": {
          "tags": ["Corporations", "Girls Love Subplot", "Heists", "Law Enforcement"],
          "fandoms": ["Original"],
          "warnings": ["Profanity", "Violence"],
          "genres": ["Action", "Crime", "Cyberpunk", "Science Fiction"]
        },
        "chapters": [{
          "id": 292,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=292",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/01-compulsive\/",
          "language": "en-US",
          "title": "01 &#8211; Compulsive",
          "author": {
            "name": "James Wake"
          },
          "published": 1673842110,
          "modified": 1674005323,
          "protected": false,
          "words": "3143",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Corporations", "Girls Love Subplot", "Heists", "Law Enforcement"],
            "fandoms": ["Original"],
            "warnings": ["Profanity", "Violence"],
            "genres": ["Action", "Crime", "Cyberpunk", "Science Fiction"]
          }
        }, {
          "id": 302,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=302",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/02-pilot-run\/",
          "language": "en-US",
          "title": "02 &#8211; Pilot Run",
          "author": {
            "name": "James Wake"
          },
          "published": 1673748373,
          "modified": 1673978180,
          "protected": false,
          "words": "3514",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Corporations", "Girls Love Subplot", "Heists", "Law Enforcement"],
            "fandoms": ["Original"],
            "warnings": ["Profanity", "Violence"],
            "genres": ["Action", "Crime", "Cyberpunk", "Science Fiction"]
          }
        }, {
          "id": 306,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=306",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/03-law-of-the-land\/",
          "language": "en-US",
          "title": "03 &#8211; Law of the Land",
          "author": {
            "name": "James Wake"
          },
          "published": 1673748432,
          "modified": 1673978180,
          "protected": false,
          "words": "3832",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Corporations", "Girls Love Subplot", "Heists", "Law Enforcement"],
            "fandoms": ["Original"],
            "warnings": ["Profanity", "Violence"],
            "genres": ["Action", "Crime", "Cyberpunk", "Science Fiction"]
          }
        }, {
          "id": 312,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=312",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/04-la-garrud\/",
          "language": "en-US",
          "title": "04 &#8211; La Garrud",
          "author": {
            "name": "James Wake"
          },
          "published": 1673748696,
          "modified": 1673978180,
          "protected": false,
          "words": "3531",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Corporations", "Girls Love Subplot", "Heists"],
            "fandoms": ["Original"],
            "warnings": ["Profanity", "Violence"],
            "genres": ["Action", "Crime", "Cyberpunk", "Science Fiction"]
          }
        }, {
          "id": 315,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=315",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/05-cheshire-cat\/",
          "language": "en-US",
          "title": "05 &#8211; Cheshire Cat",
          "author": {
            "name": "James Wake"
          },
          "published": 1673748746,
          "modified": 1673978180,
          "protected": false,
          "words": "7554",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Corporations", "Girls Love Subplot", "Heists"],
            "fandoms": ["Original"],
            "warnings": ["Profanity", "Violence"],
            "genres": ["Action", "Crime", "Cyberpunk", "Science Fiction"]
          }
        }],
        "support": {
          "kofi": "https:\/\/ko-fi.com\/james_wake"
        }
      },
      "228": {
        "id": 228,
        "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_story&#038;p=228",
        "url": "https:\/\/fictioneer-theme.com\/story\/necroepilogos\/",
        "language": "en-US",
        "title": "Necroepilogos",
        "author": {
          "name": "Hungry",
          "url": "https:\/\/katalepsis.net\/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Nothing walks the black cinder of Earth except the undead leftovers, reanimated by science so advanced it may as well be magic. Twisted into unimaginable forms by flesh-shaping and machine-grafting, the undead are the only remnant of a civilization reduced to bitter ash and organic slurry. Zombies shuffle through the ruins of nuclear fire and biological warfare and far worse, alongside rusted war-machines still holding the posts of a thousand ancient conflicts, dwarfed by god-engines turned so alien that even the extinct necromancers would have run screaming.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Elpida doesn\u2019t know this world, but she\u2019s up on her feet, leading a half-dozen other fresh revenants, ripped from the oblivion of eternity and disgorged shivering and naked on cold metal slabs in a womb-lab of blinking lights and blaring alarms, by machines running some ancient plan to spit them out into a world long dead.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><em>Necroepilogos<\/em>&nbsp;is a web serial about body horror and alienation, weird zombie-girls gluing themselves back together, mad science beyond mortal ken, and trying to cradle the flower of companionship in twitching, undead fingers.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on <a rel=\"noreferrer noopener\" href=\"https:\/\/necroepilogos.net\/\" data-type=\"URL\" data-id=\"https:\/\/katalepsis.net\/\" target=\"_blank\">www.necroepilogos.net<\/a>.<\/strong><\/p>\n<!-- \/wp:paragraph -->",
        "description": "Nothing walks the black cinder of Earth except the undead leftovers, reanimated by science so advanced it may as well be magic. Twisted into unimaginable forms by flesh-shaping and machine-grafting, the undead are the only remnant of a civilization reduced to bitter ash and organic slurry.",
        "words": 39720,
        "ageRating": "Mature",
        "status": "Ongoing",
        "chapterCount": 10,
        "published": 1673653064,
        "modified": 1674005406,
        "protected": false,
        "images": {
          "hotlinkAllowed": false,
          "cover": "https:\/\/res.cloudinary.com\/dmhr3ab5n\/images\/f_auto,q_auto\/v1674220302\/fictioneer-demo\/necroepilogos_cover\/necroepilogos_cover.jpeg?_i=AA"
        },
        "taxonomies": {
          "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
          "fandoms": ["Original"],
          "warnings": ["Gore", "Profanity", "Violence"],
          "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
        },
        "chapters": [{
          "id": 236,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=236",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-1\/",
          "language": "en-US",
          "title": "corpus \u2013 1.1",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673842114,
          "modified": 1674005406,
          "protected": false,
          "words": "2388",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 246,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=246",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-2\/",
          "language": "en-US",
          "title": "corpus \u2013 1.2",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673653897,
          "modified": 1673978181,
          "protected": false,
          "words": "4067",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 253,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=253",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-3\/",
          "language": "en-US",
          "title": "corpus \u2013 1.3",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673654683,
          "modified": 1673978181,
          "protected": false,
          "words": "4011",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 257,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=257",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-4\/",
          "language": "en-US",
          "title": "corpus \u2013 1.4",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673654793,
          "modified": 1673978181,
          "protected": false,
          "words": "4475",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 261,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=261",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-5\/",
          "language": "en-US",
          "title": "corpus \u2013 1.5",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673654907,
          "modified": 1673978181,
          "protected": false,
          "words": "3933",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 265,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=265",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-6\/",
          "language": "en-US",
          "title": "corpus \u2013 1.6",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673655197,
          "modified": 1673978181,
          "protected": false,
          "words": "4469",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 269,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=269",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-7\/",
          "language": "en-US",
          "title": "corpus \u2013 1.7",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673655282,
          "modified": 1673978181,
          "protected": false,
          "words": "5236",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 272,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=272",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/corpus-1-8\/",
          "language": "en-US",
          "title": "corpus \u2013 1.8",
          "group": "corpus",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673655338,
          "modified": 1673978181,
          "protected": false,
          "words": "4545",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 276,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=276",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/rapax-2-1\/",
          "language": "en-US",
          "title": "rapax \u2013 2.1",
          "group": "rapax",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673655606,
          "modified": 1673978180,
          "protected": false,
          "words": "3220",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }, {
          "id": 280,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=280",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/rapax-2-2\/",
          "language": "en-US",
          "title": "rapax \u2013 2.2",
          "group": "rapax",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673655821,
          "modified": 1673978180,
          "protected": false,
          "words": "3376",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Girls Love Subplot", "Military", "Monster Girls", "Zombies"],
            "fandoms": ["Original"],
            "warnings": ["Gore", "Profanity", "Violence"],
            "genres": ["Action", "Girls Love", "Horror", "Post Apocalyptic", "Science Fiction"]
          }
        }],
        "support": {
          "topwebfiction": "https:\/\/topwebfiction.com\/listings\/necroepilogos\/",
          "patreon": "https:\/\/www.patreon.com\/hazelyoung\/posts"
        }
      },
      "182": {
        "id": 182,
        "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_story&#038;p=182",
        "url": "https:\/\/fictioneer-theme.com\/story\/the-last-resort\/",
        "language": "en-US",
        "title": "The Last Resort",
        "author": {
          "name": "Monochromatic",
          "url": "https:\/\/www.hollowshades.com\/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Finding herself at the end of her rope, the broke and directionless Sophia Majorelle decides to take a skeevy job offer to work at a hotel she\u2019s never heard of.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Sure, she didn\u2019t expect its owner to be a pseudo-Grim Reaper and for the hotel itself to be in the afterlife but, well, it\u2019s not like she had anything better waiting for her.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on&nbsp;<a href=\"https:\/\/www.hollowshades.com\/story\/the-last-resort\/\" data-type=\"URL\" data-id=\"https:\/\/www.hollowshades.com\/story\/the-last-resort\/\" target=\"_blank\" rel=\"noreferrer noopener\">www.hollowshades.com<\/a><\/strong>.<\/p>\n<!-- \/wp:paragraph -->",
        "description": "Finding herself at the end of her rope, the broke and directionless Sophia Majorelle decides to take a skeevy job offer to work at a hotel she\u2019s never heard of. Sure, she didn\u2019t expect its owner to be a pseudo-Grim Reaper and for the hotel itself to be in the afterlife but, well, it\u2019s not like she had anything better waiting for her.",
        "words": 4865,
        "ageRating": "Teen",
        "status": "Ongoing",
        "chapterCount": 1,
        "published": 1673307903,
        "modified": 1674005207,
        "protected": false,
        "images": {
          "hotlinkAllowed": false,
          "header": "https:\/\/fictioneer-theme.com\/wp-content\/uploads\/2023\/01\/the_last_resort_header.jpg",
          "cover": "https:\/\/res.cloudinary.com\/dmhr3ab5n\/images\/w_1733,h_2560\/f_auto,q_auto\/v1674220314\/fictioneer-demo\/the_last_resort_cover\/the_last_resort_cover.jpg?_i=AA"
        },
        "taxonomies": {
          "tags": ["Ghosts"],
          "fandoms": ["Original"],
          "genres": ["Comedy", "Drama", "Slice of Life"]
        },
        "chapters": [{
          "id": 195,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=195",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/of-bridges-and-buses\/",
          "language": "en-US",
          "title": "Of Bridges and Buses",
          "author": {
            "name": "Monochromatic",
            "url": "https:\/\/www.hollowshades.com\/"
          },
          "published": 1673842121,
          "modified": 1674005207,
          "protected": false,
          "words": "4865",
          "nonChapter": false,
          "warning": "Spooky Ghosts! Wooooo",
          "taxonomies": {
            "tags": ["Ghosts"],
            "fandoms": ["Original"],
            "genres": ["Comedy", "Drama", "Slice of Life"]
          }
        }],
        "support": {
          "patreon": "https:\/\/www.patreon.com\/monochromatic",
          "kofi": "https:\/\/ko-fi.com\/monowriting"
        }
      },
      "106": {
        "id": 106,
        "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_story&#038;p=106",
        "url": "https:\/\/fictioneer-theme.com\/story\/crimson-lips\/",
        "language": "en-US",
        "title": "Crimson Lips",
        "author": {
          "name": "Monochromatic",
          "url": "https:\/\/www.hollowshades.com\/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Amidst the beautiful and unforgiving city of Cindermere, where prejudice is rampant and passion even more so, two women forge an unlikely bond and explore the answer to a simple question: is selling your body the same as selling yourself?<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. More from the author on <a rel=\"noreferrer noopener\" href=\"https:\/\/www.hollowshades.com\" data-type=\"URL\" target=\"_blank\">www.hollowshades.com<\/a><\/strong>.<\/p>\n<!-- \/wp:paragraph -->",
        "description": "Amidst the beautiful and unforgiving city of Cindermere, where prejudice is rampant and passion even more so, two women forge an unlikely bond and explore the answer to a simple question: is selling your body the same as selling yourself?",
        "words": 6234,
        "ageRating": "Teen",
        "status": "Completed",
        "chapterCount": 3,
        "published": 1673255267,
        "modified": 1675341424,
        "protected": false,
        "images": {
          "hotlinkAllowed": false,
          "header": "https:\/\/fictioneer-theme.com\/wp-content\/uploads\/2023\/01\/crimson_lips_header-scaled.jpg",
          "cover": "https:\/\/res.cloudinary.com\/dmhr3ab5n\/images\/f_auto,q_auto\/v1674220336\/fictioneer-demo\/crimson_lips_cover\/crimson_lips_cover.jpeg?_i=AA"
        },
        "taxonomies": {
          "tags": ["Magic", "Prostitution"],
          "fandoms": ["Original"],
          "genres": ["Drama", "Romance", "Sex"]
        },
        "chapters": [{
          "id": 773,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=773",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/ends-beginning-and-the-carriage-that-took-us-there-2\/",
          "language": "en-US",
          "title": "End\u2019s Beginning and The Carriage that took us there",
          "author": {
            "name": "Monochromatic",
            "url": "https:\/\/www.hollowshades.com\/"
          },
          "published": 1675338224,
          "modified": 1675341283,
          "protected": false,
          "words": "1216",
          "nonChapter": false,
          "ageRating": "Mature",
          "taxonomies": {
            "tags": ["Magic", "Prostitution"],
            "fandoms": ["Original"],
            "genres": ["Drama", "LGBT+", "Romance", "Sex"]
          }
        }, {
          "id": 778,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=778",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/the-mansion-2\/",
          "language": "en-US",
          "title": "The Mansion",
          "author": {
            "name": "Monochromatic",
            "url": "https:\/\/www.hollowshades.com\/"
          },
          "published": 1672573852,
          "modified": 1675341398,
          "protected": false,
          "words": "2551",
          "nonChapter": false,
          "ageRating": "Mature",
          "taxonomies": {
            "tags": ["Magic", "Prostitution"],
            "fandoms": ["Original"],
            "genres": ["Drama", "LGBT+", "Romance", "Sex"]
          }
        }, {
          "id": 786,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=786",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/the-little-girl-2\/",
          "language": "en-US",
          "title": "The Little Girl",
          "author": {
            "name": "Monochromatic",
            "url": "https:\/\/www.hollowshades.com\/"
          },
          "published": 1672576421,
          "modified": 1675341423,
          "protected": false,
          "words": "2467",
          "nonChapter": false,
          "ageRating": "Mature",
          "taxonomies": {
            "tags": ["Magic", "Prostitution"],
            "fandoms": ["Original"],
            "genres": ["Drama", "LGBT+", "Romance", "Sex"]
          }
        }],
        "support": {
          "patreon": "https:\/\/www.patreon.com\/monochromatic",
          "kofi": "https:\/\/ko-fi.com\/monowriting"
        }
      },
      "13": {
        "id": 13,
        "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_story&#038;p=13",
        "url": "https:\/\/fictioneer-theme.com\/story\/katalepsis\/",
        "language": "en-US",
        "title": "Katalepsis",
        "author": {
          "name": "Hungry",
          "url": "https:\/\/katalepsis.net\/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Until she meets Raine and Evelyn, that is \u2014 self-proclaimed bodyguard and bad-tempered magician \u2014 and learns she\u2019s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Heather plunges into a world of eldritch magic and fanatic cultists, trying to stay alive, stay sane, and deal with her own blossoming attraction to dangerous women. But being \u2018In The Know\u2019 isn\u2019t all terror and danger. Sometimes the monsters wear nice dresses and stick around for afternoon tea. Sometimes you find you have more in common with them than you think. Perhaps this is Heather\u2019s chance to be something more than the defeated husk she\u2019d grown up as, to find real friendship and meaning among things like herself \u2013 and perhaps, out there on the rim of the possible, to bring her twin sister back from the dead.<\/p>\n<!-- \/wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on <a rel=\"noreferrer noopener\" href=\"https:\/\/katalepsis.net\/\" data-type=\"URL\" data-id=\"https:\/\/katalepsis.net\/\" target=\"_blank\">www.katalepsis.net<\/a>.<\/strong><\/p>\n<!-- \/wp:paragraph -->",
        "description": "Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement. Until she meets Raine and Evelyn, that is \u2014 self-proclaimed bodyguard and bad-tempered magician \u2014 and learns she\u2019s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.",
        "words": 95558,
        "ageRating": "Teen",
        "status": "Ongoing",
        "chapterCount": 17,
        "published": 1673218246,
        "modified": 1674004959,
        "protected": false,
        "images": {
          "hotlinkAllowed": false,
          "header": "https:\/\/fictioneer-theme.com\/wp-content\/uploads\/2023\/01\/katalepsis_header.jpg",
          "cover": "https:\/\/res.cloudinary.com\/dmhr3ab5n\/images\/f_auto,q_auto\/v1674220342\/fictioneer-demo\/katalepsis_cover\/katalepsis_cover.jpg?_i=AA"
        },
        "taxonomies": {
          "tags": ["Drugs", "Magic", "Mental Illness", "Polyamory"],
          "fandoms": ["Original"],
          "warnings": ["Gore", "Violence"],
          "genres": ["Body Horror", "Cosmic Horror", "Girls Love", "Romance", "Urban Fantasy"]
        },
        "chapters": [{
          "id": 29,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=29",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-1\/",
          "language": "en-US",
          "title": "mind; correlating \u2013 1.1",
          "group": "mind; correlating",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673842132,
          "modified": 1674004959,
          "protected": false,
          "words": "8040",
          "nonChapter": false,
          "ageRating": "Teen",
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 35,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=35",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-2\/",
          "language": "en-US",
          "title": "mind; correlating \u2013\u00a01.2",
          "group": "mind; correlating",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673224433,
          "modified": 1673978225,
          "protected": false,
          "words": "5949",
          "nonChapter": false,
          "ageRating": "Teen",
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 40,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=40",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-3\/",
          "language": "en-US",
          "title": "mind; correlating \u2013\u00a01.3",
          "group": "mind; correlating",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673224752,
          "modified": 1673978225,
          "protected": true,
          "words": "5242",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 45,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=45",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-4\/",
          "language": "en-US",
          "title": "mind; correlating \u2013\u00a01.4",
          "group": "mind; correlating",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673224920,
          "modified": 1673978225,
          "protected": false,
          "words": "4509",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 49,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=49",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/mind-correlating-1-5\/",
          "language": "en-US",
          "title": "mind; correlating \u2013\u00a01.5",
          "group": "mind; correlating",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225068,
          "modified": 1673978225,
          "protected": false,
          "words": "8272",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 52,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=52",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-1\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.1",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225167,
          "modified": 1673978225,
          "protected": false,
          "words": "4249",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 57,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=57",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-2\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.2",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225345,
          "modified": 1673978225,
          "protected": false,
          "words": "5836",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 63,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=63",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-3\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.3",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225481,
          "modified": 1673978208,
          "protected": false,
          "words": "5173",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 66,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=66",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-4\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.4",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225609,
          "modified": 1673978207,
          "protected": false,
          "words": "3892",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 69,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=69",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-5\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.5",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225665,
          "modified": 1673978207,
          "protected": false,
          "words": "5761",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 74,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=74",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-6\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.6",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225757,
          "modified": 1673978207,
          "protected": false,
          "words": "5043",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 77,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=77",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-7\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.7",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225843,
          "modified": 1673978207,
          "protected": false,
          "words": "5640",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 80,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=80",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-8\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.8",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225916,
          "modified": 1673978207,
          "protected": false,
          "words": "4919",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 83,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=83",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-9\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.9",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673225981,
          "modified": 1673978207,
          "protected": false,
          "words": "6068",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 86,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=86",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-10\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.10",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673226040,
          "modified": 1673978207,
          "protected": false,
          "words": "7155",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 89,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=89",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-11\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.11",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673226099,
          "modified": 1673978207,
          "protected": false,
          "words": "5109",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }, {
          "id": 92,
          "guid": "https:\/\/fictioneer-theme.com\/?post_type=fcn_chapter&#038;p=92",
          "url": "https:\/\/fictioneer-theme.com\/chapter\/providence-or-atoms-2-12\/",
          "language": "en-US",
          "title": "providence or atoms \u2013\u00a02.12",
          "group": "providence or atoms",
          "author": {
            "name": "Hungry",
            "url": "https:\/\/katalepsis.net\/"
          },
          "published": 1673226161,
          "modified": 1673978207,
          "protected": false,
          "words": "4701",
          "nonChapter": false,
          "taxonomies": {
            "tags": ["Magic", "Mental Illness"],
            "fandoms": ["Original"],
            "genres": ["Body Horror", "Cosmic Horror", "Lesbian", "Romance", "Urban Fantasy"]
          }
        }],
        "support": {
          "topwebfiction": "http:\/\/topwebfiction.com\/listings\/katalepsis",
          "patreon": "https:\/\/www.patreon.com\/hazelyoung\/posts"
        }
      }
    },
    "lastModifiedStory": 106,
    "separateChapters": false,
    "page": 1,
    "perPage": 10,
    "maxPages": 1,
    "timestamp": 1676724903
  }
  ```

  Prettified by [beautifier.io](https://beautifier.io/).
</details>
