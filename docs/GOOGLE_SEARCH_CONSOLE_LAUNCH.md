# CuePOS Google Search Console launch checklist

CuePOS ships with an indexable public website, a canonical sitemap route, crawl directives, structured data, and private-workspace no-index controls. These are prerequisites for a clean search launch, not a guarantee of indexing or rankings. Google’s crawler ultimately decides which pages appear in search results. [1]

## Before verification

Use the final production domain over HTTPS. Replace planning text, prices, support email, legal information, and any demonstration data that appears publicly. Confirm that `/robots.txt` and `/sitemap.xml` resolve publicly through the server’s Apache rewrite configuration. The sitemap must show only public marketing and legal pages; it must never include a club dashboard, receipt, operational module, API, or platform-console URL.

| Check | Expected result |
|---|---|
| `https://your-domain.example/` | Public CuePOS homepage returns `200 OK` with a final HTTPS canonical URL. |
| `https://your-domain.example/robots.txt` | Allows public pages, disallows app internals, and declares the sitemap URL. |
| `https://your-domain.example/sitemap.xml` | Valid XML containing public canonical URLs on the final domain. |
| `https://your-domain.example/login.php` | Contains `noindex,nofollow`; it is not included in the sitemap. |
| Authenticated pages | Return an `X-Robots-Tag: noindex, nofollow` header and HTML no-index metadata. |

## Verify ownership

Create a Domain or URL-prefix property in [Google Search Console](https://search.google.com/search-console/about) for the final domain. Select an ownership-verification method that the business can maintain. For HTML meta-tag verification, copy the verification token from Search Console, sign in as the CuePOS platform owner, open **Platform → Platform Settings**, and paste only the token into **Google site verification token**. This makes the required meta tag appear on the public site.

Use DNS verification when the business controls the domain DNS and wants to verify the entire domain property. Do not paste a DNS record into CuePOS; add it through the domain registrar or hosting DNS panel instead.

## Submit and monitor

After ownership verification succeeds, open the Search Console **Sitemaps** report and submit the final sitemap URL, for example `https://cuepos.com/sitemap.xml`. Search Console can report when Googlebot accesses the sitemap and can identify processing errors. [2] Use URL Inspection for the homepage, features page, pricing page, and snooker-club-software page after their final content is published.

Continue producing accurate, useful, club-owner-focused content. Do not generate thin location pages, duplicate pages, keyword-stuffed copy, or structured data that does not match visible page content. Google recommends helpful, people-first content and explains that technical SEO does not guarantee a particular page will be indexed or rank. [1]

## Current integration status

This codebase is **Search Console ready**. It does not submit a sitemap automatically, because a final verified domain and authorised Google account are still required. This session has no configured Google Search Console connector, so submission must be completed manually after deployment or through an approved future integration.

## References

1. [SEO Starter Guide — Google Search Central](https://developers.google.com/search/docs/fundamentals/seo-starter-guide)
2. [Build and submit a sitemap — Google Search Central](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
