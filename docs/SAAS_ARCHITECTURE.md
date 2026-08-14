# CuePOS global snooker-club SaaS architecture

## Product boundary

CuePOS will remain dedicated to **snooker and cue-sports clubs**. The product contains two deliberately separate surfaces: a public marketing website that can be crawled and indexed, and authenticated club workspaces that remain private. The separation makes the product easier to explain to prospective clubs while keeping operational and customer data inaccessible to search engines.

| Surface | Audience | Indexability | Primary purpose |
|---|---|---:|---|
| `cuepos.com` | Club owners and decision-makers | Indexable | Explain the product, answer search intent, collect trials and demo requests. |
| `app.cuepos.com` or `/login.php` | Club staff | Noindex | Sign in and operate each club workspace. |
| Platform administration | CuePOS operator | Noindex | Manage tenants, plans, trial status, subscriptions, and inbound leads. |

## Tenancy model

A **club** is the tenant boundary. Every operational record already carries a `club_id`, which is retained as the primary isolation key. The SaaS extension adds a tenant slug, lifecycle status, locale/time-zone preferences, plans, subscriptions, and memberships. A self-service trial creates a club, its owner, a membership record, default billing plans, starter settings, and default tables in a single database transaction.

| Model | Purpose |
|---|---|
| `clubs` extension | Adds `slug`, `timezone`, `country_code`, `locale`, `trial_ends_at`, and tenant status. |
| `plans` | Holds pricing, feature limits, table limits, staff limits, and public-plan visibility. |
| `subscriptions` | Tracks a club's selected plan, trial or paid state, billing cadence, and provider references. |
| `club_memberships` | Allows a global user account to join one or more clubs, while the session stores the active club. |
| `trial_signups` | Auditable self-service onboarding and verification workflow. |
| `demo_requests` | Stores leads created from public marketing pages. |
| `platform_settings` | Stores configurable business-level settings, analytics IDs, and site-verification values. |

The first public release will be **payment-provider ready**, rather than claiming live payment processing before the merchant account, tax posture, checkout provider, and legal terms are supplied. Customers can start a trial and choose a plan; the platform owner can approve or activate paid subscriptions through the platform console. Stripe, Paddle, or a regionally suitable provider can be connected as a separate verified launch step.

## SEO and discovery model

Public pages use descriptive URLs, unique titles and descriptions, canonical URLs, crawlable HTML, mobile-first responsive layouts, and JSON-LD that accurately reflects visible content. The sitemap contains only public canonical pages. `robots.txt` exposes the sitemap and prevents crawling of the application, internal API, configuration, exports, logs, and uploads. Authenticated responses will also emit an `X-Robots-Tag: noindex, nofollow` header.

| Public URL | Search purpose |
|---|---|
| `/` | Snooker club management software overview. |
| `/features.php` | Product capability search intent. |
| `/pricing.php` | Plan and pricing consideration. |
| `/how-it-works.php` | Evaluation and onboarding process. |
| `/snooker-club-software.php` | Dedicated category landing page. |
| `/contact.php` | Demo and sales enquiry conversion. |
| `/faq.php` | Visible FAQ content with matching structured data. |

> Search Console can help Google discover and diagnose a submitted sitemap, but it does not guarantee that a page will be indexed or rank. CuePOS will ship the required technical assets and people-first product content; domain ownership verification and actual sitemap submission occur after deployment.

## Deployment boundary

The application remains compatible with PHP 8.x and MySQL on shared hosting. The recommended production topology uses the root domain for marketing and the `app` subdomain for the app, both pointing to the same codebase or to separate document roots with shared configuration. A single-domain deployment remains supported using `/login.php` for application entry. In either configuration, HTTPS, a final domain, business email, a privacy policy, terms of service, and a chosen payment provider are required before a paid global launch.

## Implementation sequence

The codebase will add the public site and technical SEO assets first, then onboarding and the platform owner workspace. Existing per-club POS routes remain backward compatible. Database changes are supplied as an additive migration for existing clubs and are also merged into the clean-install schema.

## References

1. [Build and submit a sitemap — Google Search Central](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
2. [Structured data introduction — Google Search Central](https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data)
3. [SEO Starter Guide — Google Search Central](https://developers.google.com/search/docs/fundamentals/seo-starter-guide)
