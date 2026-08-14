-- CuePOS Pakistan pricing update for an existing SaaS installation.
-- Run once after install/saas_migration.sql. It keeps existing subscription records valid.
START TRANSACTION;

-- Rename the former Starter plan in place, preserving existing subscriptions that reference it.
UPDATE plans SET slug='basic',name='Basic Plan',tagline='Built for growing clubs',description='Up to five tables with reliable daily billing, player records and essential club operations.',price_monthly=499,price_yearly=4990,currency_code='PKR',table_limit=5,staff_limit=5,feature_flags=JSON_OBJECT('basic_billing',true,'cafe',false,'reports',false,'tournaments',false),is_public=1,sort_order=2 WHERE slug='starter';

-- Free plan is the low-friction entry point for a single table.
INSERT INTO plans(slug,name,tagline,description,price_monthly,price_yearly,currency_code,table_limit,staff_limit,feature_flags,is_public,sort_order) VALUES ('free','Free Plan','Start with one table','One table, basic billing and a simple way to experience CuePOS in your club.',0,0,'PKR',1,1,JSON_OBJECT('basic_billing',true,'cafe',false,'reports',false,'tournaments',false),1,1) ON DUPLICATE KEY UPDATE name=VALUES(name),tagline=VALUES(tagline),description=VALUES(description),price_monthly=VALUES(price_monthly),price_yearly=VALUES(price_yearly),currency_code=VALUES(currency_code),table_limit=VALUES(table_limit),staff_limit=VALUES(staff_limit),feature_flags=VALUES(feature_flags),is_public=1,sort_order=1;

-- Pro remains the complete operating plan with unlimited table allowance, café, and reports.
INSERT INTO plans(slug,name,tagline,description,price_monthly,price_yearly,currency_code,table_limit,staff_limit,feature_flags,is_public,sort_order) VALUES ('pro','Pro Plan','Run the complete club','Unlimited tables with café operations, full reporting and advanced club management.',999,9990,'PKR',9999,25,JSON_OBJECT('basic_billing',true,'cafe',true,'reports',true,'tournaments',true,'unlimited_tables',true),1,3) ON DUPLICATE KEY UPDATE name=VALUES(name),tagline=VALUES(tagline),description=VALUES(description),price_monthly=VALUES(price_monthly),price_yearly=VALUES(price_yearly),currency_code=VALUES(currency_code),table_limit=VALUES(table_limit),staff_limit=VALUES(staff_limit),feature_flags=VALUES(feature_flags),is_public=1,sort_order=3;

-- Retire Enterprise from public pricing without deleting any historical subscription references.
UPDATE plans SET is_public=0 WHERE slug='enterprise';
COMMIT;
