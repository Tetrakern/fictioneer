# Storygraph API

Perhaps "API" is promising a bit much, considering there are only two endpoints right now. But both are powerful. The Storygraph API allows to query all stories and chapters on a site in order to index them. This makes it possible to search and filter stories from multiple Fictioneer sites. Because the biggest disadvantage of hosting your own site is the lack of audience that archives offer. This is a possible solution, although someone still needs to utilize it.

## Configuration

You can enable the Storygraph API under **Fictioneer > General > Features**. There is not much reason not to, but the choice is left to you. Additional configurations can be made using a child theme and the `FICTIONEER_API_STORYGRAPH_*` [constants](INSTALLATION.md#constants).

## Endpoint: Story

Query this endpoint to retrieve a story and collection of associated chapters. The response is cached for performance reasons.

```
GET /wp-json/storygraph/v1/story/<id>
```

<details>
  <summary><strong>Example Response:</strong> https://fictioneer-theme.com/wp-json/storygraph/v1/story/13</summary><br>

  ```json
  {
    "id": 13,
    "guid": "https://fictioneer-theme.com/?post_type=fcn_story&#038;p=13",
    "url": "https://fictioneer-theme.com/story/katalepsis/",
    "language": "en-US",
    "title": "Katalepsis",
    "author": {
      "name": "Hungry",
      "url": "https://katalepsis.net/"
    },
    "content": "<!-- wp:paragraph -->\n<p>Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Until she meets Raine and Evelyn, that is — self-proclaimed bodyguard and bad-tempered magician — and learns she’s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Heather plunges into a world of eldritch magic and fanatic cultists, trying to stay alive, stay sane, and deal with her own blossoming attraction to dangerous women. But being ‘In The Know’ isn’t all terror and danger. Sometimes the monsters wear nice dresses and stick around for afternoon tea. Sometimes you find you have more in common with them than you think. Perhaps this is Heather’s chance to be something more than the defeated husk she’d grown up as, to find real friendship and meaning among things like herself – and perhaps, out there on the rim of the possible, to bring her twin sister back from the dead.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on <a rel=\"noreferrer noopener\" href=\"https://katalepsis.net/\" data-type=\"URL\" data-id=\"https://katalepsis.net/\" target=\"_blank\">www.katalepsis.net</a>.</strong></p>\n<!-- /wp:paragraph -->",
    "description": "Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement. Until she meets Raine and Evelyn, that is — self-proclaimed bodyguard and bad-tempered magician — and learns she’s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.",
    "words": 95558,
    "ageRating": "Teen",
    "status": "Ongoing",
    "chapterCount": 17,
    "published": 1673218246,
    "modified": 1674004959,
    "protected": false,
    "images": {
      "hotlinkAllowed": false,
      "header": "https://fictioneer-theme.com/wp-content/uploads/2023/01/katalepsis_header.jpg",
      "cover": "https://res.cloudinary.com/dmhr3ab5n/images/f_auto,q_auto/v1674220342/fictioneer-demo/katalepsis_cover/katalepsis_cover.jpg?_i=AA"
    },
    "taxonomies": {
      "tags": [
        "Drugs",
        "Magic",
        "Mental Illness",
        "Polyamory"
      ],
      "fandoms": [
        "Original"
      ],
      "warnings": [
        "Gore",
        "Violence"
      ],
      "genres": [
        "Body Horror",
        "Cosmic Horror",
        "Girls Love",
        "Romance",
        "Urban Fantasy"
      ]
    },
    "chapters": [
      {
        "id": 29,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=29",
        "url": "https://fictioneer-theme.com/chapter/mind-correlating-1-1/",
        "language": "en-US",
        "title": "mind; correlating – 1.1",
        "group": "mind; correlating",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673842132,
        "modified": 1674004959,
        "protected": false,
        "words": 8040,
        "nonChapter": false,
        "ageRating": "Teen",
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 35,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=35",
        "url": "https://fictioneer-theme.com/chapter/mind-correlating-1-2/",
        "language": "en-US",
        "title": "mind; correlating – 1.2",
        "group": "mind; correlating",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673224433,
        "modified": 1673978225,
        "protected": false,
        "words": 5949,
        "nonChapter": false,
        "ageRating": "Teen",
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 40,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=40",
        "url": "https://fictioneer-theme.com/chapter/mind-correlating-1-3/",
        "language": "en-US",
        "title": "mind; correlating – 1.3",
        "group": "mind; correlating",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673224752,
        "modified": 1673978225,
        "protected": true,
        "words": 5242,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 45,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=45",
        "url": "https://fictioneer-theme.com/chapter/mind-correlating-1-4/",
        "language": "en-US",
        "title": "mind; correlating – 1.4",
        "group": "mind; correlating",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673224920,
        "modified": 1673978225,
        "protected": false,
        "words": 4509,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 49,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=49",
        "url": "https://fictioneer-theme.com/chapter/mind-correlating-1-5/",
        "language": "en-US",
        "title": "mind; correlating – 1.5",
        "group": "mind; correlating",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225068,
        "modified": 1673978225,
        "protected": false,
        "words": 8272,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 52,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=52",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-1/",
        "language": "en-US",
        "title": "providence or atoms – 2.1",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225167,
        "modified": 1673978225,
        "protected": false,
        "words": 4249,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 57,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=57",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-2/",
        "language": "en-US",
        "title": "providence or atoms – 2.2",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225345,
        "modified": 1673978225,
        "protected": false,
        "words": 5836,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 63,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=63",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-3/",
        "language": "en-US",
        "title": "providence or atoms – 2.3",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225481,
        "modified": 1673978208,
        "protected": false,
        "words": 5173,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 66,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=66",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-4/",
        "language": "en-US",
        "title": "providence or atoms – 2.4",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225609,
        "modified": 1673978207,
        "protected": false,
        "words": 3892,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 69,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=69",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-5/",
        "language": "en-US",
        "title": "providence or atoms – 2.5",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225665,
        "modified": 1673978207,
        "protected": false,
        "words": 5761,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 74,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=74",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-6/",
        "language": "en-US",
        "title": "providence or atoms – 2.6",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225757,
        "modified": 1673978207,
        "protected": false,
        "words": 5043,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 77,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=77",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-7/",
        "language": "en-US",
        "title": "providence or atoms – 2.7",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225843,
        "modified": 1673978207,
        "protected": false,
        "words": 5640,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 80,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=80",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-8/",
        "language": "en-US",
        "title": "providence or atoms – 2.8",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225916,
        "modified": 1673978207,
        "protected": false,
        "words": 4919,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 83,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=83",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-9/",
        "language": "en-US",
        "title": "providence or atoms – 2.9",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673225981,
        "modified": 1673978207,
        "protected": false,
        "words": 6068,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 86,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=86",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-10/",
        "language": "en-US",
        "title": "providence or atoms – 2.10",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673226040,
        "modified": 1673978207,
        "protected": false,
        "words": 7155,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 89,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=89",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-11/",
        "language": "en-US",
        "title": "providence or atoms – 2.11",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673226099,
        "modified": 1673978207,
        "protected": false,
        "words": 5109,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      },
      {
        "id": 92,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_chapter&#038;p=92",
        "url": "https://fictioneer-theme.com/chapter/providence-or-atoms-2-12/",
        "language": "en-US",
        "title": "providence or atoms – 2.12",
        "group": "providence or atoms",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "published": 1673226161,
        "modified": 1673978207,
        "protected": false,
        "words": 4701,
        "nonChapter": false,
        "taxonomies": {
          "tags": [
            "Magic",
            "Mental Illness"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Lesbian",
            "Romance",
            "Urban Fantasy"
          ]
        }
      }
    ],
    "support": {
      "topwebfiction": "http://topwebfiction.com/listings/katalepsis",
      "patreon": "https://www.patreon.com/hazelyoung/posts"
    },
    "timestamp": 1676759569
  }
  ```
</details>

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
| language `string` | Language code of the _story_, not necessarily chapters.
| searchable `string\|null` | Custom string for search purposes. Not used by default.
| title `string` | Title of the story.
| author `object\|null` | Author node.
| &emsp;➞ name `string` | Author name.
| &emsp;➞ url `string\|null` | Author website.
| coAuthors `[object]\|null` | Array of co-author nodes. Same as the author node.
| content `string` | Return value of `get_the_content()` with filters applied. May need further processing.
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
| chapter_ids `[object]\|null` | Array of chapter IDs in author-defined order.
| chapters `[object]\|null` | Array of chapter nodes (not included in the `/stories` endpoint). See **Chapter Fields**.
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
| searchable `string\|null` | Custom string for search purposes. Not used by default.
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

## Endpoint: Stories

Query this endpoint to retrieve a collection of stories and associated meta data. The response is paginated and cached for performance reasons, use the `page` argument to browse through the pages.

```
GET /wp-json/storygraph/v1/stories
```

<details>
  <summary><strong>Example Response:</strong> https://fictioneer-theme.com/wp-json/storygraph/v1/stories</summary><br>

  ```json
  {
    "url": "https://fictioneer-theme.com",
    "language": "en-US",
    "storyCount": 5,
    "chapterCount": 36,
    "lastPublished": 1673661557,
    "lastModified": 1675341424,
    "stories": {
      "13": {
        "id": 13,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_story&#038;p=13",
        "url": "https://fictioneer-theme.com/story/katalepsis/",
        "language": "en-US",
        "title": "Katalepsis",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Until she meets Raine and Evelyn, that is — self-proclaimed bodyguard and bad-tempered magician — and learns she’s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Heather plunges into a world of eldritch magic and fanatic cultists, trying to stay alive, stay sane, and deal with her own blossoming attraction to dangerous women. But being ‘In The Know’ isn’t all terror and danger. Sometimes the monsters wear nice dresses and stick around for afternoon tea. Sometimes you find you have more in common with them than you think. Perhaps this is Heather’s chance to be something more than the defeated husk she’d grown up as, to find real friendship and meaning among things like herself – and perhaps, out there on the rim of the possible, to bring her twin sister back from the dead.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on <a rel=\"noreferrer noopener\" href=\"https://katalepsis.net/\" data-type=\"URL\" data-id=\"https://katalepsis.net/\" target=\"_blank\">www.katalepsis.net</a>.</strong></p>\n<!-- /wp:paragraph -->",
        "description": "Nightmares and hallucinations have plagued Heather Morell all her life, relics of schizophrenia and childhood bereavement. Until she meets Raine and Evelyn, that is — self-proclaimed bodyguard and bad-tempered magician — and learns she’s not insane at all. The spirits and monsters she sees are all too real, the god-thing in her nightmares is teaching her how to surpass human limits, and her twin sister who supposedly never existed could still be alive, somewhere Outside, beyond the walls of reality.",
        "words": 95558,
        "ageRating": "Teen",
        "status": "Ongoing",
        "chapterCount": 17,
        "published": 1673218246,
        "modified": 1674004959,
        "protected": false,
        "images": {
          "hotlinkAllowed": false,
          "header": "https://fictioneer-theme.com/wp-content/uploads/2023/01/katalepsis_header.jpg",
          "cover": "https://res.cloudinary.com/dmhr3ab5n/images/f_auto,q_auto/v1674220342/fictioneer-demo/katalepsis_cover/katalepsis_cover.jpg?_i=AA"
        },
        "taxonomies": {
          "tags": [
            "Drugs",
            "Magic",
            "Mental Illness",
            "Polyamory"
          ],
          "fandoms": [
            "Original"
          ],
          "warnings": [
            "Gore",
            "Violence"
          ],
          "genres": [
            "Body Horror",
            "Cosmic Horror",
            "Girls Love",
            "Romance",
            "Urban Fantasy"
          ]
        },
        "chapter_ids": [],
        "support": {
          "topwebfiction": "http://topwebfiction.com/listings/katalepsis",
          "patreon": "https://www.patreon.com/hazelyoung/posts"
        }
      },
      "106": {
        "id": 106,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_story&#038;p=106",
        "url": "https://fictioneer-theme.com/story/crimson-lips/",
        "language": "en-US",
        "title": "Crimson Lips",
        "author": {
          "name": "Monochromatic",
          "url": "https://www.hollowshades.com/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Amidst the beautiful and unforgiving city of Cindermere, where prejudice is rampant and passion even more so, two women forge an unlikely bond and explore the answer to a simple question: is selling your body the same as selling yourself?</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. More from the author on <a rel=\"noreferrer noopener\" href=\"https://www.hollowshades.com\" data-type=\"URL\" target=\"_blank\">www.hollowshades.com</a></strong>.</p>\n<!-- /wp:paragraph -->",
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
          "header": "https://fictioneer-theme.com/wp-content/uploads/2023/01/crimson_lips_header-scaled.jpg",
          "cover": "https://res.cloudinary.com/dmhr3ab5n/images/f_auto,q_auto/v1674220336/fictioneer-demo/crimson_lips_cover/crimson_lips_cover.jpeg?_i=AA"
        },
        "taxonomies": {
          "tags": [
            "Magic",
            "Prostitution"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Drama",
            "Romance",
            "Sex"
          ]
        },
        "chapter_ids": [],
        "support": {
          "patreon": "https://www.patreon.com/monochromatic",
          "kofi": "https://ko-fi.com/monowriting"
        }
      },
      "182": {
        "id": 182,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_story&#038;p=182",
        "url": "https://fictioneer-theme.com/story/the-last-resort/",
        "language": "en-US",
        "title": "The Last Resort",
        "author": {
          "name": "Monochromatic",
          "url": "https://www.hollowshades.com/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Finding herself at the end of her rope, the broke and directionless Sophia Majorelle decides to take a skeevy job offer to work at a hotel she’s never heard of.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Sure, she didn’t expect its owner to be a pseudo-Grim Reaper and for the hotel itself to be in the afterlife but, well, it’s not like she had anything better waiting for her.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on&nbsp;<a href=\"https://www.hollowshades.com/story/the-last-resort/\" data-type=\"URL\" data-id=\"https://www.hollowshades.com/story/the-last-resort/\" target=\"_blank\" rel=\"noreferrer noopener\">www.hollowshades.com</a></strong>.</p>\n<!-- /wp:paragraph -->",
        "description": "Finding herself at the end of her rope, the broke and directionless Sophia Majorelle decides to take a skeevy job offer to work at a hotel she’s never heard of. Sure, she didn’t expect its owner to be a pseudo-Grim Reaper and for the hotel itself to be in the afterlife but, well, it’s not like she had anything better waiting for her.",
        "words": 4865,
        "ageRating": "Teen",
        "status": "Ongoing",
        "chapterCount": 1,
        "published": 1673307903,
        "modified": 1674005207,
        "protected": false,
        "images": {
          "hotlinkAllowed": false,
          "header": "https://fictioneer-theme.com/wp-content/uploads/2023/01/the_last_resort_header.jpg",
          "cover": "https://res.cloudinary.com/dmhr3ab5n/images/w_1733,h_2560/f_auto,q_auto/v1674220314/fictioneer-demo/the_last_resort_cover/the_last_resort_cover.jpg?_i=AA"
        },
        "taxonomies": {
          "tags": [
            "Ghosts"
          ],
          "fandoms": [
            "Original"
          ],
          "genres": [
            "Comedy",
            "Drama",
            "Slice of Life"
          ]
        },
        "chapter_ids": [],
        "support": {
          "patreon": "https://www.patreon.com/monochromatic",
          "kofi": "https://ko-fi.com/monowriting"
        }
      },
      "228": {
        "id": 228,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_story&#038;p=228",
        "url": "https://fictioneer-theme.com/story/necroepilogos/",
        "language": "en-US",
        "title": "Necroepilogos",
        "author": {
          "name": "Hungry",
          "url": "https://katalepsis.net/"
        },
        "content": "<!-- wp:paragraph -->\n<p>Nothing walks the black cinder of Earth except the undead leftovers, reanimated by science so advanced it may as well be magic. Twisted into unimaginable forms by flesh-shaping and machine-grafting, the undead are the only remnant of a civilization reduced to bitter ash and organic slurry. Zombies shuffle through the ruins of nuclear fire and biological warfare and far worse, alongside rusted war-machines still holding the posts of a thousand ancient conflicts, dwarfed by god-engines turned so alien that even the extinct necromancers would have run screaming.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Elpida doesn’t know this world, but she’s up on her feet, leading a half-dozen other fresh revenants, ripped from the oblivion of eternity and disgorged shivering and naked on cold metal slabs in a womb-lab of blinking lights and blaring alarms, by machines running some ancient plan to spit them out into a world long dead.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><em>Necroepilogos</em>&nbsp;is a web serial about body horror and alienation, weird zombie-girls gluing themselves back together, mad science beyond mortal ken, and trying to cradle the flower of companionship in twitching, undead fingers.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story on <a rel=\"noreferrer noopener\" href=\"https://necroepilogos.net/\" data-type=\"URL\" data-id=\"https://katalepsis.net/\" target=\"_blank\">www.necroepilogos.net</a>.</strong></p>\n<!-- /wp:paragraph -->",
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
          "cover": "https://res.cloudinary.com/dmhr3ab5n/images/f_auto,q_auto/v1674220302/fictioneer-demo/necroepilogos_cover/necroepilogos_cover.jpeg?_i=AA"
        },
        "taxonomies": {
          "tags": [
            "Girls Love Subplot",
            "Military",
            "Monster Girls",
            "Zombies"
          ],
          "fandoms": [
            "Original"
          ],
          "warnings": [
            "Gore",
            "Profanity",
            "Violence"
          ],
          "genres": [
            "Action",
            "Girls Love",
            "Horror",
            "Post Apocalyptic",
            "Science Fiction"
          ]
        },
        "chapter_ids": [],
        "support": {
          "topwebfiction": "https://topwebfiction.com/listings/necroepilogos/",
          "patreon": "https://www.patreon.com/hazelyoung/posts"
        }
      },
      "284": {
        "id": 284,
        "guid": "https://fictioneer-theme.com/?post_type=fcn_story&#038;p=284",
        "url": "https://fictioneer-theme.com/story/the-sapphire-shadow/",
        "language": "en-US",
        "title": "The Sapphire Shadow",
        "author": {
          "name": "James Wake"
        },
        "content": "<!-- wp:paragraph -->\n<p>Nadia was born the heiress to a corporate empire, destined for a life of wealth and privilege in what used to be America. But she would rather spend her time committing high-tech heists, aided and abetted by her oldest friend, Tess. Tess is a technical genius, a snarky hacker who designed and built her own right arm, and together, they pull off a series of daring crimes.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Jackson was raised in the slums. Now she’s a cop, finally living on the right side of the old sea walls. She’s supposed to be hunting Cheshire, a reclusive hacktivist stirring up unrest, but the nightly news is full of a woman breaking the law and getting away with it, blowing kisses as she escapes. Jackson hates every minute of it.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>When Nadia uncovers a plot that could change the very nature of humanity, she is forced to confront the powers that rule her home. And Jackson has to question who, exactly, her badge serves.&nbsp;</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>In a doomed city on the edge of the future, both women are caught fighting to survive. But an unlikely romance, and an even unlikelier partnership, might be the only thing that saves Nadia.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><strong>This is an excerpt for demo purposes. You can read the full story for free on <a rel=\"noreferrer noopener\" href=\"https://offprint.net/work/k681hsimp/the-sapphire-shadow\" data-type=\"URL\" data-id=\"https://katalepsis.net/\" target=\"_blank\">www.offprint.net</a> or buy the <a rel=\"noreferrer noopener\" href=\"https://www.amazon.com/Sapphire-Shadow-James-Wake-ebook/dp/B088QMZC4R\" target=\"_blank\">ebook</a>.</strong></p>\n<!-- /wp:paragraph -->",
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
          "cover": "https://res.cloudinary.com/dmhr3ab5n/images/f_auto,q_auto/v1674220298/fictioneer-demo/sapphire_shadow_cover/sapphire_shadow_cover.jpeg?_i=AA"
        },
        "taxonomies": {
          "tags": [
            "Corporations",
            "Girls Love Subplot",
            "Heists",
            "Law Enforcement"
          ],
          "fandoms": [
            "Original"
          ],
          "warnings": [
            "Profanity",
            "Violence"
          ],
          "genres": [
            "Action",
            "Crime",
            "Cyberpunk",
            "Science Fiction"
          ]
        },
        "chapter_ids": [],
        "support": {
          "kofi": "https://ko-fi.com/james_wake"
        }
      }
    },
    "lastModifiedStory": 106,
    "separateChapters": false,
    "page": 1,
    "perPage": 10,
    "maxPages": 1,
    "timestamp": 1676817165
  }
  ```
</details>

| Argument | Description |
| :-- | :-- |
| page | Page to return. Default `1`.

### Schema

The following schema defines all fields that can exist within the response, excluding fields that are empty or `null` unless stated otherwise. So if there are no stories, the stories node will be missing. All values are escaped.

| Field | Description |
| :-- | :-- |
| url `string` | Root URL of the targeted site. In case you forgot or want to make sure.
| language `string` | Language code of the _site_, not necessarily stories or chapters.
| storyCount `integer` | Total number of published and visible stories.
| chapterCount `integer` | Total number of published chapters, including those marked as non-chapter.
| lastPublished `integer\|null` | Unix timestamp of the last published story (GMT).
| lastModified `integer\|null` | Unix timestamp of the last modified story (GMT).
| stories `object\|null` | Paginated collection of story nodes, ordered by publishing date. See **Story** endpoint.
| lastModifiedStory `integer\|null` | Foreign ID (key) of the last modified story.
| page `integer` | Current page of the story collection.
| perPage `integer` | Stories per collection page.
| maxPages `integer` | Total number of collection pages.
| timestamp `integer` | Unix timestamp of when the response was compiled (GMT). May be cached.
