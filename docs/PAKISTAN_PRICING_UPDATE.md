# CuePOS Pakistan pricing update

This update configures CuePOS for Pakistani snooker clubs with the following public plans.

| Plan | Monthly price | Tables | Included operations |
|---|---:|---:|---|
| Free Plan | Free | 1 | Basic billing |
| Basic Plan | Rs. 499 | Up to 5 | Core daily billing and player records |
| Pro Plan | Rs. 999 | Unlimited | Café, reports, and complete club management |

For an **existing installation** that already has the SaaS tables, open phpMyAdmin, select the CuePOS database, choose **Import**, and upload `install/pakistan_pricing_update.sql`. The update preserves existing subscription records; it changes the former Starter plan into Basic and removes Enterprise from public pricing without deleting historic data.

For a **new installation**, use `install/cuepos_clean_install.sql` only. It already contains the Pakistan pricing setup, so no second pricing import is needed.
