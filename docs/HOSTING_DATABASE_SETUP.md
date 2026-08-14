# CuePOS database connection setup for `sql311.ezyro.com`

The deployed CuePOS application currently falls back to local development values (`localhost`, `cuepos`, `root`, and an empty password). Replace the first configuration constants in `config/db.php` with the database information shown in your hosting panel.

> Do **not** put your actual vPanel password in GitHub, in this guide, or in a screenshot. Enter it only in the deployed server’s `config/db.php` file through File Manager or SFTP.

## Exact configuration

Open the deployed file at `public_html/config/db.php`. Replace **only lines 3–6** with the following. Keep the remaining file unchanged.

```php
define('DB_HOST', 'sql311.ezyro.com');
define('DB_NAME', 'ezyro_42654285_cuepos');
define('DB_USER', 'ezyro_42654285');
define('DB_PASS', 'PASTE_YOUR_VPANEL_PASSWORD_HERE');
```

If you have a custom domain, also set the application URL line to the final HTTPS address, for example:

```php
define('APP_URL', 'https://your-domain.example');
```

Save the file and reload the site in an incognito/private browser window. Do not add spaces before or after the password inside the quotes.

## Required hosting checks

| Check | What to confirm |
|---|---|
| Correct password | `DB_PASS` must be the current **vPanel password**, not the MySQL username and not a placeholder. If you recently changed the vPanel password, update `DB_PASS` to the new value. |
| Correct database | In phpMyAdmin, select `ezyro_42654285_cuepos`. It should contain CuePOS tables such as `clubs`, `users`, `tables`, `sessions`, and `plans`. |
| Schema import | For a fresh SaaS install, import `install/schema.sql`, then `install/seed.sql`, then `install/saas_migration.sql`, in that order. |
| File location | The edited file must be the live copy at `public_html/config/db.php`, not a copy on your computer or in another extracted folder. |
| PHP extension | In the hosting panel, use PHP 8.1+ and ensure the `pdo_mysql` / MySQL PDO extension is enabled. |

## If the error remains

Create a temporary file at `public_html/test_db.php` with the code below. Replace only the password. Open `https://your-domain.example/test_db.php`, note the exact result, then **delete `test_db.php` immediately**.

```php
<?php
try {
    $db = new PDO(
        'mysql:host=sql311.ezyro.com;dbname=ezyro_42654285_cuepos;charset=utf8mb4',
        'ezyro_42654285',
        'PASTE_YOUR_VPANEL_PASSWORD_HERE',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo 'Database connection succeeded.';
} catch (Throwable $e) {
    echo 'Connection error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
```

| Test result | Meaning and next action |
|---|---|
| `Database connection succeeded.` | The database details are correct. Re-check that the live CuePOS `config/db.php` was edited and saved in the correct `public_html` folder. |
| `Access denied` | The vPanel password is incorrect, changed, or has an invisible leading/trailing space. Reset or confirm the vPanel password and update `DB_PASS`. |
| `Unknown database` | The database has not been created, its name differs, or the account has not been linked to it. Confirm the exact name in vPanel. |
| `Connection refused` / host error | Use exactly `sql311.ezyro.com`; if it still fails, ask hosting support to confirm external PHP-to-MySQL access and the correct MySQL host for account `ezyro_42654285`. |
| `could not find driver` | Enable the `pdo_mysql` PHP extension or ask hosting support to enable it. |

After the connection works, delete `test_db.php`, keep `config/db.php` private, and change the seeded CuePOS owner password if it is still `admin123`.
