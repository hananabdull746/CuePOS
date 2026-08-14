# CuePOS

CuePOS is a dark-mode **snooker club management SaaS** designed for Pakistani club owners. It is deliberately built for Hostinger-style shared hosting using pure PHP 8.x, PDO/MySQL, HTML, vanilla JavaScript, and CDN-hosted Tailwind CSS.

## Included capabilities

| Area | Included workflow |
|---|---|
| Floor POS | Live table grid, per-minute/frame/slab/challenge billing, 3-second polling, pause/resume, held bills, and payment completion |
| Café | Session-linked café charges, stock deduction, and low-stock notifications |
| CRM | Player profiles, tier badges, loyalty balances, visit history, ledger field, and player search |
| Operations | Shift opening/closing and cash reconciliation, expense capture, inventory viewing, and café-menu management |
| Reporting | Dashboard charts, finance reports, CSV export, and direct FPDF session-report export |
| Engagement | Tournaments, leaderboard, token-protected TV display, printable receipt, and WhatsApp receipt share link |
| Governance | Role checks, session regeneration, password verification, prepared statements, CSRF tokens, audit log, and upload/log/export directory isolation |

## First installation

1. Import `install/schema.sql` through phpMyAdmin.
2. Import `install/seed.sql` through phpMyAdmin.
3. Set the database credentials and public URL in `config/db.php`.
4. Upload the complete repository to the hosting document root.
5. Set `uploads`, `logs`, and `exports` to permission mode `755`.
6. Sign in using `admin@demo.com` / `admin123`, then change the demo password immediately.

The full Hostinger procedure is in [DEPLOY.md](DEPLOY.md).

## Project layout

```text
api/            Authenticated JSON endpoints for live operations
assets/         Application CSS and vanilla JavaScript
config/         PDO and application helpers
includes/       Shared layout, components, receipt, and bootstrap code
install/        MySQL schema and demonstration data
libs/fpdf/      Vendored FPDF library for shared-hosting PDF exports
modules/        CRM, café, inventory, finance, session, shift, tournament, and settings screens
```

## Local validation example

A local PHP/MySQL environment can serve the project without package installation in the project itself:

```bash
CUEPOS_DB_NAME=cuepos CUEPOS_DB_USER=cuepos_user CUEPOS_DB_PASS=secret \
CUEPOS_APP_URL=http://127.0.0.1:8090 php -S 0.0.0.0:8090 -t .
```

This command is only for local development. Deploy the PHP files directly to shared hosting for production.
