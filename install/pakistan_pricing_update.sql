-- CuePOS Pakistan per-table pricing update for an existing SaaS installation.
-- Run once after install/saas_migration.sql. It preserves existing subscriptions.
START TRANSACTION;

-- If a previous pricing update was applied, move its former full-featured Pro plan aside first.
UPDATE plans SET slug='premium' WHERE slug='pro' AND NOT EXISTS (SELECT 1 FROM (SELECT slug FROM plans) AS current_plans WHERE current_plans.slug='premium');

-- Preserve existing Starter or Basic subscriptions by converting their plan record into Pro.
UPDATE plans SET slug='pro' WHERE slug='basic' AND NOT EXISTS (SELECT 1 FROM (SELECT slug FROM plans) AS current_plans WHERE current_plans.slug='pro');
UPDATE plans SET slug='pro' WHERE slug='starter' AND NOT EXISTS (SELECT 1 FROM (SELECT slug FROM plans) AS current_plans WHERE current_plans.slug='pro');

-- Free Plan: one table and basic billing.
INSERT INTO plans(slug,name,tagline,description,price_monthly,price_yearly,currency_code,table_limit,staff_limit,feature_flags,is_public,sort_order) VALUES ('free','Free Plan','Start with one table','One table, basic billing and a simple way to experience CuePOS in your club.',0,0,'PKR',1,1,JSON_OBJECT('basic_billing',true,'cafe',false,'reports',false,'tournaments',false),1,1) ON DUPLICATE KEY UPDATE name=VALUES(name),tagline=VALUES(tagline),description=VALUES(description),price_monthly=VALUES(price_monthly),price_yearly=VALUES(price_yearly),currency_code=VALUES(currency_code),table_limit=VALUES(table_limit),staff_limit=VALUES(staff_limit),feature_flags=VALUES(feature_flags),is_public=1,sort_order=1;

-- Pro Plan: Rs. 449 per table per month, valid for up to 10 tables.
INSERT INTO plans(slug,name,tagline,description,price_monthly,price_yearly,currency_code,table_limit,staff_limit,feature_flags,is_public,sort_order) VALUES ('pro','Pro Plan','Pay per table as your club grows','Rs. 449 per table per month for up to 10 tables.',449,0,'PKR',10,10,JSON_OBJECT('basic_billing',true,'cafe',false,'reports',false,'tournaments',false,'per_table_billing',true,'rate_per_table',449,'tier_breakpoint',10),1,2) ON DUPLICATE KEY UPDATE name=VALUES(name),tagline=VALUES(tagline),description=VALUES(description),price_monthly=VALUES(price_monthly),price_yearly=VALUES(price_yearly),currency_code=VALUES(currency_code),table_limit=VALUES(table_limit),staff_limit=VALUES(staff_limit),feature_flags=VALUES(feature_flags),is_public=1,sort_order=2;

-- Premium Plan: Rs. 400 per table per month for clubs operating more than 10 tables.
INSERT INTO plans(slug,name,tagline,description,price_monthly,price_yearly,currency_code,table_limit,staff_limit,feature_flags,is_public,sort_order) VALUES ('premium','Premium Plan','For clubs with more than 10 tables','Rs. 400 per table per month for clubs with more than 10 tables, with café operations, reports and full club management.',400,0,'PKR',9999,25,JSON_OBJECT('basic_billing',true,'cafe',true,'reports',true,'tournaments',true,'unlimited_tables',true,'per_table_billing',true,'rate_per_table',400,'minimum_tables',11),1,3) ON DUPLICATE KEY UPDATE name=VALUES(name),tagline=VALUES(tagline),description=VALUES(description),price_monthly=VALUES(price_monthly),price_yearly=VALUES(price_yearly),currency_code=VALUES(currency_code),table_limit=VALUES(table_limit),staff_limit=VALUES(staff_limit),feature_flags=VALUES(feature_flags),is_public=1,sort_order=3;

-- Enterprise is no longer presented as a public plan, but historical data is retained.
UPDATE plans SET is_public=0 WHERE slug='enterprise';
COMMIT;
