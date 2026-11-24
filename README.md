# Business / Company Website

Security-first PHP + MySQL website designed for deployment on InfinityFree shared hosting.

## Technology
- PHP 8.x (use the PHP version enabled in the hosting control panel)
- MySQL with PDO and native prepared statements
- HTML5, CSS3, vanilla JavaScript
- Apache `.htaccess` protections

## Project status
Phase 1 establishes the security baseline and a minimal public landing page. Business branding, page content, database schema, customer forms, and admin features are intentionally not assumed yet.

## Structure
```
.
├── app/
│   ├── .htaccess
│   ├── bootstrap.php
│   ├── config/
│   │   └── config.example.php
│   └── security/
│       ├── csrf.php
│       ├── headers.php
│       └── helpers.php
├── assets/
│   └── css/
│       └── style.css
├── .gitignore
├── .htaccess
└── index.php
```

## Local setup
1. Use PHP 8.x and Apache (for example, XAMPP).
2. Copy `app/config/config.example.php` to `app/config/config.local.php`.
3. Put local database credentials only in `config.local.php`; never commit that file.
4. Open the project through Apache, not as a `file://` URL.

The landing page does not need a database connection yet. Database configuration is prepared for the next phase.

## InfinityFree deployment
1. Upload the project files into the account's `htdocs` directory.
2. Create `app/config/config.local.php` on the server using the values from the hosting control panel.
3. Enable HTTPS in the hosting control panel and test the site over HTTPS.
4. Do not upload backups, database dumps, credentials, or development files into the public web root.

## Security baseline
- Output escaping helper for HTML.
- CSRF token generation and validation helper for future state-changing forms.
- Secure, HTTP-only, SameSite session cookie settings.
- Content Security Policy and defensive response headers.
- PDO prepared-statement database helper.
- Directory listing disabled; application internals denied from direct HTTP access.
- Secrets excluded from Git.

## Important
No website can be guaranteed to withstand every attack. Security must be maintained through updates, least privilege, monitoring, backups, and recurring tests. Phase 1 is a foundation, not a completed security audit or a production certification.
