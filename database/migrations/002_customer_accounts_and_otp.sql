-- OPTIONAL: Run once only against a database made with the original starter schema. Back up first.
CREATE TABLE customers (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, full_name VARCHAR(100) NOT NULL, email VARCHAR(190) NOT NULL,
 phone VARCHAR(30) NOT NULL, password_hash VARCHAR(255) NOT NULL, auth_version INT UNSIGNED NOT NULL DEFAULT 1, is_active TINYINT(1) NOT NULL DEFAULT 1,
 email_verified_at DATETIME NULL, last_login_at DATETIME NULL, created_at DATETIME NOT NULL, updated_at DATETIME NULL,
 PRIMARY KEY(id), UNIQUE KEY uq_customers_email(email), KEY idx_customers_active(is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE orders ADD COLUMN customer_id BIGINT UNSIGNED NULL AFTER id;
ALTER TABLE orders ADD KEY idx_orders_customer_created(customer_id,created_at);
ALTER TABLE orders ADD CONSTRAINT fk_orders_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL;
CREATE TABLE password_reset_requests (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, customer_id BIGINT UNSIGNED NOT NULL, email VARCHAR(190) NOT NULL,
 status ENUM('pending','otp_issued','used','expired','cancelled') NOT NULL DEFAULT 'pending',
 otp_hash VARCHAR(255) NULL, expires_at DATETIME NULL, attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
 requested_ip_hash CHAR(64) NOT NULL, created_at DATETIME NOT NULL, issued_at DATETIME NULL, used_at DATETIME NULL,
 PRIMARY KEY(id), KEY idx_reset_status_created(status,created_at), KEY idx_reset_customer_status(customer_id,status),
 CONSTRAINT fk_reset_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE auth_rate_limits (
 rate_key CHAR(64) NOT NULL, attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
 window_started_at DATETIME NOT NULL, blocked_until DATETIME NULL, PRIMARY KEY(rate_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
