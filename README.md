# Hammad Graphic — Business Website

Security-first PHP/MySQL company website for InfinityFree shared hosting.

## Stack
- PHP 8.1+ and Apache
- MySQL with PDO and native prepared statements
- HTML, CSS and vanilla JavaScript
- Server-rendered pages with technical SEO foundations

## Included
- Homepage, About, Services, Contact and project-order pages
- Graphic design, brand identity, social media, print, document, website and SEO service catalogue
- Order request validation, CSRF protection, honeypot and basic session rate limiting
- PDO database layer and SQL schema
- EasyPaisa/JazzCash configuration placeholders (account numbers intentionally blank)
- Page titles/descriptions, canonical URLs when configured, Open Graph metadata, ProfessionalService JSON-LD, robots.txt and XML sitemap endpoint
- Session cookie protections, security headers, output escaping and private application/database directories

## Local setup (XAMPP)
1. Use PHP 8.1+ with Apache, PDO MySQL and mbstring enabled.
2. Copy `app/config/config.example.php` to `app/config/config.local.php`.
3. Set your database name, username and password in the local config only.
4. Create a MySQL database and import `database/schema.sql` using phpMyAdmin.
5. Put the project folder under XAMPP `htdocs` and open it through Apache.

## InfinityFree setup
1. Create a MySQL database in the InfinityFree control panel and import `database/schema.sql` through phpMyAdmin.
2. Upload the project contents to `htdocs`.
3. Create `app/config/config.local.php` on the server by copying the example and filling the database credentials shown by InfinityFree.
4. Set `app.base_url` to your exact HTTPS domain, without a trailing slash.
5. Keep `app/config/config.local.php` private; it is excluded from Git.
6. Test the homepage and order form after database setup.

## Wallet account numbers
In the server-side `app/config/config.local.php`, edit:
- `payments.easypaisa.account_name`
- `payments.easypaisa.account_number`
- `payments.easypaisa.enabled` (set true after verifying the number)
- `payments.jazzcash.account_name`
- `payments.jazzcash.account_number`
- `payments.jazzcash.enabled`

Never enter wallet MPIN, OTP, password or secret PIN in the website. The intended flow is order request → owner quotation → customer pays → customer submits transaction details/receipt → owner verifies funds and marks the order paid. The payment collection and owner admin interface are the next implementation phase; the current order form does not collect money or claim payment has been verified.

## SEO launch checklist
- Set the real HTTPS domain in `app.base_url`.
- Confirm each page title and description.
- Open `/robots.txt` and `/sitemap.php`; the sitemap intentionally returns a setup message until a valid HTTPS base URL is configured.
- Add the verified domain to Google Search Console and submit `https://YOUR-DOMAIN/sitemap.php`.
- Add Google Analytics only after the measurement ID is confirmed; do not invent tracking IDs.

## Security notes
No website is guaranteed to be unhackable. Review hosting limits, keep PHP updated, use a unique database password, restrict database privileges, back up data and test the live hosting configuration. This repository has not been deployed to InfinityFree by this change.
