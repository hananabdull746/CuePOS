# CuePOS deployment on Hostinger shared hosting

CuePOS is a **pure PHP 8.x and MySQL 8.x** application. It does not require Composer, Node.js, a process manager, or WebSockets. The interface uses CDN-hosted Tailwind CSS, Font Awesome, Chart.js, and Google Fonts; keep outgoing HTTPS enabled on the hosting account.

## 1. Create the database

In Hostinger hPanel, open **Databases → MySQL Databases**. Create a database such as `yourdomain_cuepos`, create a dedicated database user with a strong password, and grant it **all privileges** on that database. Record the server host, database name, username, and password.

## 2. Import database structure and demo data

Open phpMyAdmin from hPanel, select the new database, choose **Import**, and upload `install/schema.sql`. After it completes, import `install/seed.sql`. The seed creates the demonstration Royal Snooker Club workspace and users.

## 3. Configure the application

Edit `config/db.php` and replace the sample database values with the Hostinger values. Set `APP_URL` to the full HTTPS URL, for example `https://cuepos.example.com`. Do not commit production credentials to source control.

## 4. Upload the files

Upload all repository files to the domain's `public_html` directory (or the intended subdomain document root), then extract the archive if necessary. Keep all PHP and asset folders together. Ensure the host uses PHP 8.1 or newer with `pdo_mysql` enabled.

## 5. Set directory permissions

Set `uploads/`, `logs/`, and `exports/` to **755**. Do not make them world-writable unless Hostinger support specifically requires a different ownership setting.

## 6. Log in and secure the instance

Open your configured application URL. The seeded owner login is `admin@demo.com` with password `admin123`. Sign in immediately, replace the demo password, update club details, add staff, tables, rate plans, and café menu items. The demo credentials must never remain in use on a live site.

## 7. Begin operation

Open a shift before cash operations. Operators then use **Live Tables** to start sessions, add café charges, pause/resume play, and collect payment. Owners can use the TV Display page in Settings to obtain a tokenised public screen link.

## Operational checks

Verify the login, table start/end workflows, café charging, shift reconciliation, CSV export, player CRM, finance charts, TV link, and role restrictions after installation. Check the web-server error log if PHP returns a database or permission error.
