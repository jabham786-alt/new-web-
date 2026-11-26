-- Add blog publishing to an existing Hammad Graphic database.
-- Back up the database before running. Safe to run more than once.
CREATE TABLE IF NOT EXISTS blog_posts (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 title VARCHAR(180) NOT NULL,
 slug VARCHAR(200) NOT NULL,
 excerpt VARCHAR(500) NOT NULL,
 content MEDIUMTEXT NOT NULL,
 cover_image VARCHAR(255) NULL,
 status ENUM('draft','published') NOT NULL DEFAULT 'draft',
 author_id BIGINT UNSIGNED NULL,
 published_at DATETIME NULL,
 created_at DATETIME NOT NULL,
 updated_at DATETIME NULL,
 PRIMARY KEY(id), UNIQUE KEY uq_blog_slug(slug),
 KEY idx_blog_status_published(status,published_at),
 CONSTRAINT fk_blog_author FOREIGN KEY(author_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
