# CuePOS Pakistan per-table pricing update

CuePOS now uses a simple table-based model for Pakistani snooker clubs.

| Plan | Monthly price | Table eligibility | Included operations |
|---|---:|---:|---|
| Free Plan | Free | 1 table | Basic billing |
| Pro Plan | Rs. 449 per table | Up to 10 tables | Daily billing and player records |
| Premium Plan | Rs. 400 per table | More than 10 tables | Café, reporting, and full club management |

For an **existing installation** that already has the SaaS tables, open phpMyAdmin, select the CuePOS database, choose **Import**, and upload `install/pakistan_pricing_update.sql`. The migration keeps existing subscriptions valid, turns prior Starter or Basic records into Pro where needed, and preserves historical plans without deleting subscription data.

For a **new installation**, use `install/cuepos_clean_install.sql` only. It already contains the correct Free, Pro, and Premium plan data, so no second pricing import is needed.
