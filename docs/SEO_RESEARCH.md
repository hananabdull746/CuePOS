# CuePOS technical SEO research

Google Search Central's sitemap guidance states that a public site can submit its sitemap through the Search Console Sitemaps report and recommends exposing the sitemap location in `robots.txt` using a `Sitemap:` line. The CuePOS public marketing site will therefore generate a canonical XML sitemap and declare it in the crawl rules. The guidance also notes that Search Console can report Googlebot sitemap access and sitemap processing errors.

Source: https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap

Google Search Central's structured-data guidance supports using JSON-LD markup to help Google understand qualifying pages, but structured data should describe visible, accurate page content rather than promise unsupported rich results. CuePOS will use Organization, SoftwareApplication, WebSite, and FAQPage JSON-LD only on matching public pages.

Google's SEO Starter Guide emphasizes that no technical configuration guarantees indexing or ranking. It recommends helpful, people-first content, descriptive URLs, crawlable CSS and JavaScript, canonical management, and sitemap submission as a supplementary discovery method. CuePOS will expose marketing content publicly, keep club workspaces out of indexing, use canonical URLs, and ship a search-console-ready sitemap and robots file.

Sources:
- https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data
- https://developers.google.com/search/docs/fundamentals/seo-starter-guide
