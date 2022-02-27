
CREATE TABLE user (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  surname VARCHAR(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  login VARCHAR(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  password VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

-- -----------------------------------------
CREATE TABLE rss_channel (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_user INT UNSIGNED NOT NULL,
  title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  subtitle VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
)ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

ALTER TABLE rss_channel
  ADD CONSTRAINT FK1_rss_channel FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE;

-- -----------------------------------------
CREATE TABLE rss_entry (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 id_rss_channel INT UNSIGNED NOT NULL,
 title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 link VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 published_at DATETIME,
 updated_at DATETIME,
 summary TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
 content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

ALTER TABLE rss_entry
    ADD CONSTRAINT FK1_rss_entry FOREIGN KEY (id_rss_channel) REFERENCES rss_channel (id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE INDEX I1_rss_entry ON rss_entry (title);
CREATE INDEX I2_rss_entry ON rss_entry (published_at);

-- -----------------------------------------
CREATE TABLE rss_entry_flag (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_user INT UNSIGNED NOT NULL,
    name VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    color VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
)ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

ALTER TABLE rss_entry_flag
    ADD CONSTRAINT FK1_rss_entry_flag FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE;

-- -----------------------------------------
CREATE TABLE rss_entry_user_status (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_rss_entry INT UNSIGNED NOT NULL,
    id_rss_entry_flag INT UNSIGNED,
    id_user INT UNSIGNED NOT NULL,
    read_at DATETIME,
    color VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
)ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

ALTER TABLE rss_entry_user_status
    ADD CONSTRAINT FK1_rss_entry_user_status FOREIGN KEY (id_rss_entry) REFERENCES rss_entry (id) ON UPDATE CASCADE ON DELETE CASCADE,
    ADD CONSTRAINT FK2_rss_entry_user_status FOREIGN KEY (id_rss_entry_flag) REFERENCES rss_entry_flag (id) ON UPDATE CASCADE ON DELETE SET NULL,
    ADD CONSTRAINT FK3_rss_entry_user_status FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE;
