=== Marklink ===
Contributors: nkapila6
Tags: markdown, llm, ai, export, content
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Export your WordPress content as Markdown for easy consumption by Large Language Models.

== Description ==

Marklink turns your WordPress site into a Markdown-friendly source for LLMs and other tools. Install it, activate it, and your published content is available as `.md` right away.

* Append `.md` to any post or page URL to get its Markdown version (e.g. `yoursite.com/about.md`)
* Send an `Accept: text/markdown` header to get Markdown from any page
* Auto-generated indexes at `/llms.txt` (recent posts) and `/llms-full.txt` (full archive)
* Filters out posts with "copy" or "sample" in the title or slug
* Settings page to control excluded words, post limits, post types, and which endpoints are active
* Works with custom post types out of the box

**How to use it:**

1. Visit `yoursite.com/page-slug.md` to get that page as Markdown
2. Visit `yoursite.com/index.md` for your homepage
3. Visit `yoursite.com/llms.txt` for a list of recent posts
4. Visit `yoursite.com/llms-full.txt` for everything

**Examples:**

`curl https://yoursite.com/about.md`

`curl -H "Accept: text/markdown" https://yoursite.com/about/`

`curl https://yoursite.com/llms.txt`

The plugin handles headings, bold/italic, links, lists, horizontal rules, and line breaks. Images are stripped to keep things clean for LLM consumption.

Runs entirely on your server with zero external requests. Only published content is served. No user data is collected or stored.

== Installation ==

1. Upload the `marklink` folder to `/wp-content/plugins/`, or install through the WordPress plugin installer.
2. Activate it from the Plugins screen.
3. Head to **Settings > Marklink** to configure.
4. If the endpoints 404, go to **Settings > Permalinks** and hit Save to flush rewrite rules.

== Frequently Asked Questions ==

= Does this expose all my content? =

No. Only published posts and pages are served. Drafts, private, and password-protected content are not exposed.

= Can I customize which posts are excluded? =

Yes. Go to **Settings > Marklink** and edit the "Excluded words" field.

= Does this slow down my site? =

No. The plugin only runs when someone actually requests a `.md` URL or index file. Normal visitors won't notice anything.

= Does it work with caching plugins? =

Yes. You might want to exclude `.md` URLs and `/llms.txt` from your cache if you want real-time updates.

= Does it support custom post types? =

Yes, any public post type registered on your site.

= Can I turn off /llms.txt? =

Yes. Uncheck "Site indexes" in **Settings > Marklink**.

= What about images? =

They're stripped from the Markdown output to keep things clean for LLMs.

= Does this contact external services? =

No. Everything runs on your server. Nothing is sent anywhere.

= Is this GDPR compliant? =

Yes. No user data is collected or stored.

== Changelog ==

= 0.0.1 =
* Initial release
* Markdown endpoint support (.md URLs)
* Accept header support (text/markdown)
* Site index generation (/llms.txt, /llms-full.txt)
* Settings page (excluded words, post limit, post types, endpoint toggles)
* Support for custom post types

== Upgrade Notice ==

= 0.0.1 =
Initial release. Install and activate to start exporting content as Markdown.
