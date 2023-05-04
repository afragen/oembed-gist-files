# oEmbed Gists and Files

Plugin URI:        https://github.com/afragen/oembed-gist-files
Description:       oEmbed Gist or files within Gists.
Tags:              gist, oembed, embed
Contributors:      afragen, costdev
License:           MIT
Requires at least: 5.9
Requires PHP:      7.1
Tested up to:      6.2
Stable tag:        1.0.0

oEmbed Gist or files within Gists.

## Description
Use an Embed block and enter the URL for the Gist or the URL for a specific file within the Gist. If using the Classic Editor, place the URL on a line by itself. Not as a link.

Support for `wp_oembed_get()`.

No shortcodes.

## Changelog

#### 1.0.0 / 2023-05-03
* update to capture file name of Gist, fixes edge cases for file names without extensions

#### 0.8.0 / 2023-02-25
* convert from array operations to string operations for performance
* add support for `wp_oembed_get()`

#### 0.7.1 / 2022-08-05
* dot org release

#### 0.7.0 /2022-08-04
* initial release
