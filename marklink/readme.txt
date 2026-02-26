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

Marklink provides a simple, automated way to make your WordPress site's content accessible to AI models and other systems that prefer Markdown format.

**Key Features:**

* **Markdown Endpoints** — Access any post or page as Markdown by appending `.md` to the URL (e.g., `yoursite.com/about.md`)
* **Header-Based Delivery** — Send an `Accept: text/markdown` header to get any page in Markdown format
* **Site Indexes** — Automatically generated content indexes at `/llms.txt` (recent 20 posts) and `/llms-full.txt` (complete archive)
* **Smart Filtering** — Automatically excludes posts with "copy" or "sample" in the title or slug
* **Settings Page** — Configure excluded words, post limits, post types, and toggle endpoints on/off
* **Custom Post Types** — Supports all public custom post types

**Use Cases:**

* Feed your content to AI models for analysis, summarization, or training
* Create simple text-based APIs for third-party integrations
* Generate documentation sites from your WordPress content
* Enable RSS-like consumption without the complexity
* Provide developer-friendly access to your content

**How It Works:**

1. **Individual Content** — Visit `yoursite.com/page-slug.md` to get that page as Markdown
2. **Homepage** — Visit `yoursite.com/index.md` to get your homepage as Markdown
3. **Content Index** — Visit `yoursite.com/llms.txt` for a list of recent posts with Markdown links
4. **Full Archive** — Visit `yoursite.com/llms-full.txt` for a complete content index

**Example Usage:**

`curl https://yoursite.com/about.md`

`curl -H "Accept: text/markdown" https://yoursite.com/about/`

`curl https://yoursite.com/llms.txt`

**Markdown Conversion:**

The plugin converts WordPress content to Markdown, including:

* Headings (h1–h6)
* Bold and italic text
* Links
* Lists
* Horizontal rules
* Paragraphs and line breaks

**Performance & Security:**

* Minimal database queries
* Only published content is accessible
* Respects WordPress post status and permissions
* Lightweight code with zero dependencies

**Privacy:**

Marklink does not collect, store, or transmit any user data. It does not contact any external servers or third-party services. The plugin only reformats your existing published WordPress content into Markdown.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/marklink` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Settings → Marklink** to configure the plugin.
4. Visit **Settings → Permalinks** and click Save if endpoints don't work right away (this flushes the rewrite rules).

== Frequently Asked Questions ==

= Does this expose all my content? =

Only published posts and pages are accessible through the Markdown endpoints. Unpublished, draft, and private content remains protected.

= Can I customize which posts are excluded from the indexes? =

Yes. Go to **Settings → Marklink** and edit the "Excluded words" field. Posts with those words in the title or slug will be excluded.

= Does this affect my site's performance? =

The plugin is very lightweight and only processes requests specifically for Markdown endpoints. Regular site visitors are unaffected.

= Can I use this with caching plugins? =

Yes! The plugin works with most caching plugins. You may want to exclude `.md` URLs and `/llms.txt` from caching for real-time content updates.

= Does this work with custom post types? =

Yes, the plugin automatically detects and supports all public custom post types registered on your site.

= Can I disable the /llms.txt endpoints? =

Yes. Go to **Settings → Marklink** and uncheck "Site indexes".

= What about images and media? =

Images are removed from the output to keep the content clean for LLM consumption.

= Does this plugin contact any external services? =

No. Marklink operates entirely on your server. It does not send data to or receive data from any external service.

= Is this GDPR compliant? =

The plugin does not collect or store any user data. It simply serves your existing WordPress content in a different format.

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
