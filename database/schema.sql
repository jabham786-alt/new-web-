-- Hammad Graphic database schema (fresh install).
SET NAMES utf8mb4;
SET time_zone = '+05:00';
CREATE TABLE IF NOT EXISTS customers (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, full_name VARCHAR(100) NOT NULL, email VARCHAR(190) NOT NULL,
 phone VARCHAR(30) NOT NULL, password_hash VARCHAR(255) NOT NULL, auth_version INT UNSIGNED NOT NULL DEFAULT 1, is_active TINYINT(1) NOT NULL DEFAULT 1,
 email_verified_at DATETIME NULL, last_login_at DATETIME NULL, created_at DATETIME NOT NULL, updated_at DATETIME NULL,
 PRIMARY KEY(id), UNIQUE KEY uq_customers_email(email), KEY idx_customers_active(is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS admin_users (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, username VARCHAR(60) NOT NULL, email VARCHAR(190) NOT NULL,
 password_hash VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1, last_login_at DATETIME NULL,
 created_at DATETIME NOT NULL, PRIMARY KEY(id), UNIQUE KEY uq_admin_username(username), UNIQUE KEY uq_admin_email(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS orders (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, customer_id BIGINT UNSIGNED NULL, order_reference VARCHAR(24) NOT NULL,
 customer_name VARCHAR(100) NOT NULL, email VARCHAR(190) NOT NULL, phone VARCHAR(30) NOT NULL,
 service_key VARCHAR(80) NOT NULL, service_title VARCHAR(160) NOT NULL, project_details TEXT NOT NULL,
 budget VARCHAR(80) NULL, status ENUM('new','reviewing','quoted','in_progress','completed','cancelled') NOT NULL DEFAULT 'new',
 quoted_amount DECIMAL(10,2) NULL, currency CHAR(3) NOT NULL DEFAULT 'PKR',
 payment_status ENUM('unpaid','submitted','under_review','paid','rejected','refunded') NOT NULL DEFAULT 'unpaid',
 created_at DATETIME NOT NULL, updated_at DATETIME NULL, PRIMARY KEY(id), UNIQUE KEY uq_orders_reference(order_reference),
 KEY idx_orders_customer_created(customer_id,created_at), KEY idx_orders_email(email),
 KEY idx_orders_status_created(status,created_at), KEY idx_orders_payment_status(payment_status),
 CONSTRAINT fk_orders_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS password_reset_requests (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, customer_id BIGINT UNSIGNED NOT NULL, email VARCHAR(190) NOT NULL,
 status ENUM('pending','otp_issued','used','expired','cancelled') NOT NULL DEFAULT 'pending',
 otp_hash VARCHAR(255) NULL, expires_at DATETIME NULL, attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
 requested_ip_hash CHAR(64) NOT NULL, created_at DATETIME NOT NULL, issued_at DATETIME NULL, used_at DATETIME NULL,
 PRIMARY KEY(id), KEY idx_reset_status_created(status,created_at), KEY idx_reset_customer_status(customer_id,status),
 CONSTRAINT fk_reset_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS auth_rate_limits (
 rate_key CHAR(64) NOT NULL, attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
 window_started_at DATETIME NOT NULL, blocked_until DATETIME NULL, PRIMARY KEY(rate_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS payment_submissions (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, order_id BIGINT UNSIGNED NOT NULL,
 payment_method ENUM('easypaisa','jazzcash') NOT NULL, transaction_reference VARCHAR(120) NOT NULL,
 receipt_path VARCHAR(255) NULL, amount DECIMAL(10,2) NOT NULL,
 status ENUM('submitted','verified','rejected') NOT NULL DEFAULT 'submitted',
 submitted_at DATETIME NOT NULL, verified_at DATETIME NULL, PRIMARY KEY(id), KEY idx_payment_order(order_id),
 CONSTRAINT fk_payment_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS admin_settings (
 setting_key VARCHAR(100) NOT NULL, setting_value TEXT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
