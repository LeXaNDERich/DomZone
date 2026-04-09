CREATE DATABASE IF NOT EXISTS `r98595b7_dommzon`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `r98595b7_dommzon`;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categories_name` (`name`),
  UNIQUE KEY `uk_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(180) NOT NULL,
  `description` TEXT NOT NULL,
  `image_path` VARCHAR(255) NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_products_category_id` (`category_id`),
  KEY `idx_products_featured` (`is_featured`),
  CONSTRAINT `fk_products_category_id`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Колонка `image_path` уже есть в CREATE TABLE выше.
-- Если ты прогоняешь скрипт на старой БД без этой колонки, добавь её вручную:
-- ALTER TABLE `products` ADD COLUMN `image_path` VARCHAR(255) NULL AFTER `description`;

CREATE TABLE IF NOT EXISTS `contact_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_requests_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `customer_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `comment` TEXT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` VARCHAR(40) NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_created_at` (`created_at`),
  CONSTRAINT `fk_orders_user_id`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `idx_order_items_product_id` (`product_id`),
  CONSTRAINT `fk_order_items_order_id`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product_id`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`name`, `slug`) VALUES
  ('Уборка', 'uborka'),
  ('Инструменты', 'instrumenty'),
  ('Кухня', 'kuhnya'),
  ('Сад', 'sad')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `products` (`category_id`, `name`, `description`, `image_path`, `price`, `is_featured`)
SELECT c.id, p.name, p.description, p.image_path, p.price, p.is_featured
FROM (
  SELECT 'Уборка' AS category_name, 'Швабра с отжимом ProClean' AS name, 'Легкая швабра с телескопической ручкой и микрофиброй.' AS description, '/assets/image/1.jpg' AS image_path, 1890.00 AS price, 1 AS is_featured
  UNION ALL SELECT 'Инструменты', 'Набор отверток Master 12в1', 'Универсальный комплект для дома и мелкого ремонта.', '/assets/image/2.jpg', 1290.00, 1
  UNION ALL SELECT 'Кухня', 'Органайзер для специй', 'Компактное хранение баночек, экономия места на кухне.', '/assets/image/3.jpg', 790.00, 1
  UNION ALL SELECT 'Сад', 'Секатор садовый SharpCut', 'Острые лезвия из закаленной стали для аккуратной обрезки.', '/assets/image/4.jpg', 1150.00, 1
  UNION ALL SELECT 'Уборка', 'Перчатки хозяйственные UltraGrip', 'Плотный латекс, надежный захват и защита рук.', '/assets/image/5.jpg', 240.00, 0
  UNION ALL SELECT 'Инструменты', 'Ящик для инструментов BuildBox', 'Прочный пластиковый корпус и удобная система секций.', '/assets/image/6.jpg', 2190.00, 0
  UNION ALL SELECT 'Уборка', 'Универсальное чистящее средство CleanMax', 'Эффективно удаляет жир и налет, подходит для кухни и ванной.', '/assets/image/7.jpg', 540.00, 1
  UNION ALL SELECT 'Инструменты', 'Набор инструментов ToolBox Home', 'Базовый набор для бытового ремонта: молоток, плоскогубцы и ключи.', '/assets/image/8.jpg', 3290.00, 1
) AS p
JOIN `categories` c ON c.name = p.category_name
WHERE NOT EXISTS (
  SELECT 1 FROM `products` existing WHERE existing.`name` = p.`name`
);

UPDATE `products`
SET `image_path` = '/assets/image/1.jpg'
WHERE `name` = 'Швабра с отжимом ProClean';

UPDATE `products`
SET `image_path` = '/assets/image/2.jpg'
WHERE `name` = 'Набор отверток Master 12в1';

UPDATE `products`
SET `image_path` = '/assets/image/3.jpg'
WHERE `name` = 'Органайзер для специй';

UPDATE `products`
SET `image_path` = '/assets/image/4.jpg'
WHERE `name` = 'Секатор садовый SharpCut';

UPDATE `products`
SET `image_path` = '/assets/image/5.jpg'
WHERE `name` = 'Перчатки хозяйственные UltraGrip';

UPDATE `products`
SET `image_path` = '/assets/image/6.jpg'
WHERE `name` = 'Ящик для инструментов BuildBox';

UPDATE `products`
SET `image_path` = '/assets/image/7.jpg'
WHERE `name` = 'Универсальное чистящее средство CleanMax';

UPDATE `products`
SET `image_path` = '/assets/image/8.jpg'
WHERE `name` = 'Набор инструментов ToolBox Home';

INSERT INTO `products` (`category_id`, `name`, `description`, `image_path`, `price`, `is_featured`)
SELECT
  c.id,
  CONCAT('Хозтовар #', LPAD(seq.n, 2, '0')) AS name,
  CONCAT('Практичный товар для дома и быта. Фото №', seq.n, '.') AS description,
  CONCAT('/assets/image/', seq.n, '.jpg') AS image_path,
  CAST((290 + (seq.n * 45)) AS DECIMAL(10,2)) AS price,
  CASE WHEN seq.n <= 12 THEN 1 ELSE 0 END AS is_featured
FROM (
  SELECT (tens.d * 10 + ones.d + 1) AS n
  FROM
    (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4) AS tens
    CROSS JOIN
    (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
     UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS ones
  WHERE (tens.d * 10 + ones.d + 1) <= 50
) AS seq
JOIN `categories` c ON c.slug = CASE MOD(seq.n, 4)
  WHEN 1 THEN 'uborka'
  WHEN 2 THEN 'instrumenty'
  WHEN 3 THEN 'kuhnya'
  ELSE 'sad'
END
WHERE NOT EXISTS (
  SELECT 1
  FROM `products` p
  WHERE p.`name` = CONCAT('Хозтовар #', LPAD(seq.n, 2, '0'))
);
