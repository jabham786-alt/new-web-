-- Hammad Graphic: order conversations and customer/admin replies.
-- Back up the database, then run this migration once on the existing database.
CREATE TABLE IF NOT EXISTS order_messages (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 order_id BIGINT UNSIGNED NOT NULL,
 sender_type ENUM('customer','admin') NOT NULL,
 sender_id BIGINT UNSIGNED NULL,
 message TEXT NOT NULL,
 created_at DATETIME NOT NULL,
 PRIMARY KEY(id),
 KEY idx_order_messages_order_created(order_id,created_at),
 CONSTRAINT fk_order_messages_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
