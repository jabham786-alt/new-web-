-- Run once on the existing database AFTER migration 002. Back up the database first.
ALTER TABLE customers ADD COLUMN username VARCHAR(30) NULL AFTER full_name;
ALTER TABLE customers ADD COLUMN profile_photo VARCHAR(255) NULL AFTER phone;
UPDATE customers SET username=CONCAT('customer_',id) WHERE username IS NULL OR username='';
ALTER TABLE customers MODIFY username VARCHAR(30) NOT NULL;
ALTER TABLE customers ADD UNIQUE KEY uq_customers_username(username);