# Marklink

Auto-expose Markdown for WordPress websites and create `llms.txt`.

[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
![WordPress 5.0+](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP 7.4+](https://img.shields.io/badge/PHP-7.4%2B-purple)

## What it does

Marklink is a WordPress plugin that makes your content available as Markdown. Activate it and every published post/page gets a `.md` endpoint. You also get auto-generated site indexes at `/llms.txt` and `/llms-full.txt`.

### Endpoints

| URL | What you get |
|---|---|
| `yoursite.com/any-page.md` | That page as Markdown |
| `yoursite.com/index.md` | Homepage as Markdown |
| `yoursite.com/llms.txt` | Recent posts index |
| `yoursite.com/llms-full.txt` | Full content archive |

You can also request any page as Markdown with the `Accept: text/markdown` header.

### Features

- `.md` endpoints for every published post and page
- `Accept: text/markdown` header support
- Auto-generated indexes at `/llms.txt` and `/llms-full.txt`
- Filters out posts with "copy" or "sample" in the title/slug
- Works with custom post types
- Settings page for excluded words, post limits, post types, and endpoint toggles
- Runs entirely on your server, no external requests

## Installation

### From zip

1. Download or build `marklink.zip`
2. Go to **Plugins > Add New > Upload Plugin** in WP admin
3. Upload the zip and activate
4. Head to **Settings > Marklink** to configure
5. If endpoints 404, go to **Settings > Permalinks** and hit Save to flush rewrite rules

### Manual

1. Copy the `marklink/` folder to `wp-content/plugins/`
2. Activate from the Plugins screen
3. Configure at **Settings > Marklink**
4. Flush permalinks if needed (step 5 above)

## Usage

```bash
# Get a page as Markdown
curl https://yoursite.com/about.md

# Use the Accept header instead
curl -H "Accept: text/markdown" https://yoursite.com/about/

# Get the site index
curl https://yoursite.com/llms.txt
```

## FAQ

**The endpoints return 404.**
Go to **Settings > Permalinks** and click Save. This flushes the rewrite rules.

**Does this expose private content?**
No. Only published posts and pages are served.

**Does this contact external services?**
No. Runs entirely on your server.

**Does it work with caching plugins?**
Yes. You might want to exclude `.md` URLs and `/llms.txt` from your cache if you need real-time updates.

**Can I customize which posts are excluded?**
Go to **Settings > Marklink** and edit the "Excluded words" field.

## Changelog

### 0.0.1

- Initial release
- `.md` endpoints for posts and pages
- `Accept: text/markdown` header support
- `/llms.txt` and `/llms-full.txt` indexes
- Settings page
- Custom post type support

## License

GPLv2 or later. See [LICENSE](LICENSE).
