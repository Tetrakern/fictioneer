<p align="center"><img src="./repo/assets/fictioneer_logo.svg?raw=true" alt="Fictioneer"></p>

<p align="center">
  <a href="https://github.com/Tetrakern/fictioneer"><img alt="Theme: 5.28" src="https://img.shields.io/badge/theme-5.28-blue?style=flat" /></a>
  <a href="LICENSE.md"><img alt="License: GPL v3" src="https://img.shields.io/badge/license-GPL%20v3-blue?style=flat" /></a>
  <a href="https://wordpress.org/download/"><img alt="WordPress 6.5+" src="https://img.shields.io/badge/WordPress-%3E%3D6.5-blue?style=flat" /></a>
  <a href="https://www.php.net/"><img alt="PHP: 7.4+" src="https://img.shields.io/badge/php-%3E%3D7.4-blue?logoColor=white&style=flat" /></a>
  <a href="https://github.com/sponsors/Tetrakern"><img alt="GitHub Sponsors" src="https://img.shields.io/github/sponsors/tetrakern" /></a>
  <a href="https://ko-fi.com/tetrakern"><img alt="Support me on Ko-fi" src="https://img.shields.io/badge/-Ko--fi-FF5E5B?logo=kofi&logoColor=white&style=flat&labelColor=434B57" /></a>
</p>

<p align="center"><strong>WordPress theme and standalone solution for publishing and reading <a href="https://en.wikipedia.org/wiki/Web_fiction">web fictions</a>.</strong></p>

<p align="center"><a href="https://fictioneer-theme.com/" target="_blank">Demo</a> &bull; <a href="https://github.com/Tetrakern/fictioneer/releases">Download</a> &bull; <a href="INSTALLATION.md">Installation</a> &bull; <a href="CUSTOMIZE.md">Customize</a> &bull; <a href="DOCUMENTATION.md">Documentation</a> &bull; <a href="API.md">API</a> &bull; <a href="DEVELOPMENT.md">Development</a> &bull; <a href="FAQ.md">FAQ</a> &bull; <a href="CREDITS.md">Credits</a> &bull; <a href="https://discord.gg/tVfDB7EbaP" target="_blank">Discord</a></p>
<br>

## About

Fictioneer was originally developed for a closed group of authors and not intended for a public release. This is still reflected in the code, which takes several liberties not considered best practice. You will most likely never find it in official libraries for that reason, meaning installation and updates need to be done manually.

The theme is intended for individuals and small collectives.

Fictioneer is open source and completely free. However, maintaining and developing a theme of these proportions takes a considerable amount of time and effort. So if you enjoy Fictioneer and have the capacity, please consider supporting me on [Patreon](https://www.patreon.com/tetrakern), [Ko-fi](https://ko-fi.com/tetrakern), or [GitHub Sponsors](https://github.com/sponsors/Tetrakern).

## Key Features

stories, chapters, collections, and recommendations &bull; customizable web reader &bull; shortcodes &bull; text-to-speech &bull; bookmarks &bull; progress tracker &bull; lightbox &bull; dark/light mode &bull; ePUB converter &bull; advanced search form &bull; sidebar &bull; OAuth 2.0 logins (Discord, Google, Twitch, and Patreon) &bull; Patreon content gate &bull; post password expiration &bull; gate content for users and roles &bull; role manager &bull; responsive layout &bull; cache aware &bull; custom comment system &bull; AJAX comments &bull; private comments &bull; comment reply subscriptions &bull; send notifications to Discord &bull; search engine optimization &bull; GDPR compliant &bull; hue, saturation, and lightness sliders &bull; translation ready &bull; compatible with Elementor

## Migration

Migrating an existing WordPress database can be a downright nightmare. Depending on what you did and the themes and plugins you used before, you may encounter severe issues matching the previous data structures to those used in Fictioneer. To make this easier, take a look at the [migration guide](MIGRATION.md).

## Free Plugins

[Fictioneer Email Notifications](https://github.com/Tetrakern/fictioneer-email-notifications): Allows readers to subscribe to selected updates via email. You can choose to receive notifications for all new content, specific post types, or selected stories and taxonomies.

## Customization & Child Themes

[Child themes](https://developer.wordpress.org/themes/advanced-topics/child-themes/) are the best way to customize Fictioneer if the provided options prove insufficient. You do not even need much programming experience for this since there are many guides and code snippets to adjust WordPress to your needs. But note that Fictioneer is not a page builder, so changing the whole layout does require expertise. Plugins may or may not work here.

* [Base child theme](https://github.com/Tetrakern/fictioneer-child-theme)
* [Minimalist child theme](https://github.com/Tetrakern/fictioneer-minimalist)
* [Action reference](ACTIONS.md)
* [Filter reference](FILTERS.md)
* [CSS snippets](INSTALLATION.md#css-snippets)
* [PHP action & filter snippets](CUSTOMIZE.md)

Since 5.20.0, the theme is compatible with the [Elementor](https://elementor.com/) page/site builder plugin. This allows you to customize parts of the theme without programming skills, although a bit knowledge about HTML and CSS is recommended. Read more about the Elementor implementation in the [documentation](https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md#elementor).

## Commissions

I do take commissions for customizations and new features, *within reason.* Just write me on Discord, and we can figure out what is feasible. However, keep in mind that any feature you pay for may be added to the theme for everyone to enjoy (exclusivity is generally more expensive). Several features have already been sponsored this way. Sharing is caring.<sup>*</sup>

<sup>* As long as that makes sense and is not detrimental.</sup>

## Support the Development

Fictioneer has been developed by mostly one author, barring [credited](CREDITS.md) code snippets. Since version 5.24.0, several developers have contributed and any help going forward is appreciated. If you are interested, or want to fork your own version, take a look at the [development](DEVELOPMENT.md) guidelines, [action](ACTIONS.md) hooks, and [filter](FILTERS.md) hooks. A theme-related base plugin can be found [here](https://github.com/Tetrakern/fictioneer-base-plugin). You can also join the [Discord](https://discord.gg/tVfDB7EbaP).

<a href="https://github.com/Tetrakern/fictioneer/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=Tetrakern/fictioneer" />
</a>

## Screenshots

<p align="center">Base Theme (Light/Dark)</p>

![Screenshot Collage](repo/assets/fictioneer_preview.jpg?raw=true)

<p align="center">Base Theme - Sidebar (Light/Dark)</p>

![Screenshot Collage](repo/assets/two_columns_layout.jpg?raw=true)

<p align="center"><a href="https://github.com/Tetrakern/fictioneer-minimalist">Minimalist Child Theme</a> - Sidebar (Light/Dark)</p>

![Screenshot Collage](repo/assets/fictioneer_minimalist.jpg?raw=true)

<p align="center">Base Theme Parts</p>

![Screenshot Collage](repo/assets/screenshots.jpg?raw=true)
