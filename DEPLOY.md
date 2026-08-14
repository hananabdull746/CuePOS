# CuePOS global SaaS deployment on Hostinger shared hosting

CuePOS is a **pure PHP 8.x and MySQL 8.x** multi-tenant SaaS for snooker clubs. It runs on shared hosting without Composer, Node.js, a process manager, WebSockets, or a build pipeline. The public marketing website is crawlable, while the club workspace, platform console, and APIs are intentionally non-indexable.

> Before a commercial global launch, provide a final domain, legal business identity, support email, payment provider, tax setup, privacy policy review, and terms-of-service review. The code is subscription-ready; live card payments must not be represented as active until a verified payment provider has been configured.

## 1. Point the domain and enable HTTPS

Point the final domain, for example `cuepos.com`, to the Hostinger web root and enable an SSL certificate in hPanel. The simplest first launch uses the same domain for public pages and club login at `https://cuepos.com/login.php`. A later configuration can direct `app.cuepos.com` to the same codebase or a separate document root for application-only access.

## 2. Create the MySQL database

In Hostinger hPanel, open **Databases → MySQL Databases**. Create a database such as `yourdomain_cuepos`, create a dedicated user with a strong password, and grant the user all privileges on that database. Record the database host, name, username, and password.

## 3. Import the database in the required order

Open phpMyAdmin, select the new database, choose **Import**, and run the following files in order.

| Order | File | Purpose |
|---:|---|---|
| 1 | `install/schema.sql` | Creates the CuePOS operational schema. |
| 2 | `install/seed.sql` | Adds the demonstration Royal Snooker Club workspace and sample operational data. |
| 3 | `install/saas_migration.sql` | Adds SaaS plans, subscriptions, tenant settings, memberships, public leads, trial onboarding, and platform settings. |

For an existing CuePOS installation, back up the database first, then run **only** `install/saas_migration.sql` once. Do not re-import `schema.sql` or `seed.sql` into a live database.

## 4. Configure the application

Edit `config/db.php` and replace the database values. Set `APP_URL` to the final HTTPS domain, such as `https://cuepos.com`. The app can also read `CUEPOS_PUBLIC_SITE_URL` if the public marketing site and app subdomain use different domains. Never commit production credentials to source control.

## 5. Upload the application

Upload the full repository to `public_html` or the document root for the domain. Keep `.htaccess` at the document root; it provides the public `/robots.txt` and `/sitemap.xml` routes plus baseline security headers. Confirm that the host runs PHP 8.1 or newer with `pdo_mysql` enabled and that Apache rewrite rules are permitted.

Set `uploads/`, `logs/`, and `exports/` to permission mode **755**. Do not set world-writable permissions unless Hostinger support has explicitly diagnosed an ownership issue.

## 6. Secure the initial platform owner

The seed creates a platform owner at `admin@demo.com` with password `admin123`. Sign in through `/login.php`, change the password immediately, and do not leave the demo credentials enabled. The platform console is available at `/platform/index.php` for the seeded platform owner and is where global tenants, trials, and verification settings are monitored.

## 7. Configure the public SaaS

Review the public pages at the homepage, `features.php`, `pricing.php`, `how-it-works.php`, `snooker-club-software.php`, `faq.php`, and `contact.php`. Replace planning prices, support details, legal templates, and brand assets before promoting the site. Trial registration at `trial.php` creates a separate club tenant, owner account, plan subscription record, starter rate plan, and starter tables.

## 8. Connect a payment provider before accepting paid subscriptions

CuePOS records plans, trials, and subscription states but deliberately does not process a payment until a merchant account and provider integration have been verified. Choose an appropriate provider for the business jurisdiction, configure checkout and webhooks, validate tax treatment and refunds, and test cancellation and failed-payment paths before advertising paid plans.

## 9. Prepare Google Search Console

The application provides `/robots.txt` and `/sitemap.xml`. The public sitemap includes only marketing and legal pages; club dashboards, APIs, receipts, platform administration, and operational modules are excluded and emit no-index controls. Google Search Console can help diagnose sitemap processing but does not guarantee inclusion or ranking. [1] [2]

Create a Search Console property for the final HTTPS domain. Verify ownership through your preferred method. For HTML meta-tag verification, copy only the token value into **Platform → Platform Settings → Google site verification token**, then publish the setting. Submit `https://your-domain.example/sitemap.xml` in the Search Console Sitemaps report. Use URL Inspection to request review of the homepage and core product pages after deployment.

## 10. Final launch checks

Verify the following items on the live domain before announcing CuePOS.

| Area | Required check |
|---|---|
| Tenant isolation | Create a second trial club and confirm it cannot access the first club’s tables, players, reports, or sessions. |
| Authentication | Confirm demo credentials have been changed and platform-console access is restricted to approved platform administrators. |
| Billing readiness | Confirm the provider, pricing, refunds, taxes, and subscription lifecycle are fully configured before paid collection. |
| Crawling | Open `/robots.txt` and `/sitemap.xml` publicly, verify canonical URLs use the final HTTPS domain, and validate page source metadata. |
| Indexing | Verify ownership in Search Console and submit the sitemap; then monitor coverage, URL inspection, and structured-data reports. |
| Legal and support | Publish reviewed privacy and terms pages, a support email, and a method for customer data requests. |

## References

1. [Build and submit a sitemap — Google Search Central](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)
2. [SEO Starter Guide — Google Search Central](https://developers.google.com/search/docs/fundamentals/seo-starter-guide)
