# CuePOS — Global Snooker Club SaaS

CuePOS is a **multi-tenant operating system for snooker and cue-sports clubs**. It combines a public, search-ready marketing website with separate private club workspaces for live tables, POS billing, player loyalty, café operations, shifts, reports, TV displays, and tournaments.

## Product surfaces

| Surface | Purpose | Search visibility |
|---|---|---:|
| Public website | Explain CuePOS, show product capabilities and pricing, collect trials and demo requests. | Indexable |
| Club workspace | Run the daily club operation with club-specific data. | Noindex |
| Platform console | Monitor tenants, trials, subscriptions, leads and Google verification values. | Noindex |

## SaaS capabilities

CuePOS supports a club-per-tenant model, self-service 14-day trials, configurable country/time-zone/currency values, subscription-ready plan records, platform-owner administration, and future payment-provider integration. Existing club modules continue to use the tenant `club_id` boundary.

The public website includes focused snooker-club content, canonical URLs, responsive crawlable HTML, JSON-LD for Organization, SoftwareApplication, and visible FAQ content, a robots route, XML sitemap route, contact lead capture, and a Google Search Console verification setting.

> Google Search Console and a sitemap help discovery and diagnostics, but they do not guarantee inclusion or ranking. Meaningful, accurate public content and a correctly deployed HTTPS domain remain essential. See [Google’s SEO Starter Guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide).

## Installation

| Installation type | Import order |
|---|---|
| Fresh installation | `install/schema.sql` → `install/seed.sql` → `install/saas_migration.sql` |
| Existing CuePOS installation | Back up the database, then run `install/saas_migration.sql` once |

Set database configuration in `config/db.php`, then deploy all source files to a PHP 8.x + MySQL 8.x hosting environment. Full Hostinger deployment and Search Console steps are in [DEPLOY.md](DEPLOY.md).

## Initial access

The sample seed contains the platform owner `admin@demo.com` / `admin123`. Sign in at `/login.php`, change this password immediately, and access `/platform/index.php` to view tenant and subscription readiness. Do not retain the seeded password on a live environment.

## Key routes

```text
/                         Public CuePOS website
/trial.php                Create a 14-day club trial workspace
/login.php                Private workspace login
/platform/index.php       Platform-owner SaaS console
/robots.txt               Crawl directives (Apache rewrite route)
/sitemap.xml              XML public-page sitemap (Apache rewrite route)
```

## Pre-commercial-launch requirements

Before accepting paid subscriptions, configure a final domain, HTTPS, legal entity, reviewed terms and privacy policy, support email, merchant/payment provider, tax/refund policy, and Search Console ownership verification. The platform data model is payment-ready but intentionally does not claim live checkout until that integration is verified.
