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
- Customer profile registration with unique username, email, mobile number, optional validated profile photo, username/email login, profile editing, authenticated orders, CSRF protection, password hashing, database-backed login throttling and admin-issued OTP password recovery
- PDO database layer, fresh-install SQL schema and a separate migration for the older starter database
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
6. Configure a long random `app.security_salt` and a one-time `admin.setup_key` in the private config.
7. Visit `/admin/setup.php` once to create the first administrator, then sign in at `/admin/login.php`.
8. Test registration, login, order submission and password reset before launch.

## Customer and admin dashboards

- Customers use `/account.php` as their dashboard to edit their profile, place new orders, review each order and open its private conversation at `/customer-order.php?id=ORDER_ID`.
- The owner uses `/admin/login.php` and `/admin/` as the separate admin dashboard. Each order opens at `/admin/order.php?id=ORDER_ID`, where the owner can review customer details, update the order status and reply.
- New orders automatically receive an in-dashboard acknowledgement. Customer/admin messages are stored per order in `order_messages`; these are in-site messages, not email or WhatsApp notifications.
- Existing installations must back up the database and run `database/migrations/004_order_messaging_and_admin_workflow.sql` once before uploading/using the updated dashboard files. Fresh installs get this table from `database/schema.sql`.

## Customer profile fields

The registration page collects full name, unique username, mobile/WhatsApp number, email, password and an optional JPG/PNG/WebP profile photo (maximum 2 MB). Customers can sign in using either username or email plus password. Each signed-in customer sees and edits only their own profile and order history. Profile images are stored under `uploads/profile/` with random filenames; script execution is denied in that directory. Existing customers are assigned a generated username by migration 003 and can use it or their email to sign in.

## Wallet account numbers
In the server-side `app/config/config.local.php`, edit:
- `payments.easypaisa.account_name`
- `payments.easypaisa.account_number`
- `payments.easypaisa.enabled` (set true after verifying the number)
- `payments.jazzcash.account_name`
- `payments.jazzcash.account_number`
- `payments.jazzcash.enabled`

For password recovery, the admin dashboard generates a six-digit code valid for 10 minutes; it is stored hashed and shown once to the administrator to send manually to the customer's registered email. The customer enters the code and a new password on the same reset page. Never enter wallet MPIN, wallet OTP, password or secret PIN in the website. The intended flow is order request → owner quotation → customer pays → customer submits transaction details/receipt → owner verifies funds and marks the order paid. The admin area currently includes order visibility and admin-issued OTP reset codes. Wallet payment submission, receipt verification and order-status management are a subsequent implementation phase. The order form does not collect money or claim payment has been verified.

## SEO launch checklist
- Set the real HTTPS domain in `app.base_url`.
- Confirm each page title and description.
- Set the absolute `Sitemap: https://YOUR-DOMAIN/sitemap.php` line in `robots.txt` after your domain is confirmed.
- Open `/robots.txt` and `/sitemap.php`; the sitemap intentionally returns a setup message until a valid HTTPS base URL is configured.
- Add the verified domain to Google Search Console and submit `https://YOUR-DOMAIN/sitemap.php`.
- Add Google Analytics only after the measurement ID is confirmed; do not invent tracking IDs.

## Security notes
No website is guaranteed to be unhackable. Review hosting limits, keep PHP updated, use a unique database password, restrict database privileges, back up data and test the live hosting configuration. This repository has not been deployed to InfinityFree by this change.
