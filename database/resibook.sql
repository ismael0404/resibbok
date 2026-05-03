-- =====================================================
-- ResiBook - Base de données complète v2.0
-- Plateforme immobilière : Réservation, Location, Vente
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

DROP DATABASE IF EXISTS `resibook`;
CREATE DATABASE `resibook` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `resibook`;

-- =====================================================
-- TABLE: roles
-- =====================================================
CREATE TABLE `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrateur de la plateforme'),
(2, 'owner', 'Propriétaire de biens immobiliers'),
(3, 'client', 'Client / Voyageur');

-- =====================================================
-- TABLE: users
-- =====================================================
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT NOT NULL DEFAULT 3,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT 'default.png',
  `bio` TEXT DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'Côte d''Ivoire',
  `id_document` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active','pending','suspended','rejected') DEFAULT 'active',
  `is_verified` TINYINT(1) DEFAULT 0,
  `verification_badge` TINYINT(1) DEFAULT 0,
  `rejection_reason` TEXT DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT,
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`),
  INDEX `idx_role` (`role_id`)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: categories
-- =====================================================
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(50) DEFAULT 'fa-home',
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `categories` (`name`, `icon`, `description`) VALUES
('Appartement', 'fa-building', 'Appartements modernes en ville'),
('Villa', 'fa-house-chimney', 'Villas luxueuses avec piscine'),
('Maison', 'fa-home', 'Maisons familiales confortables'),
('Studio', 'fa-door-open', 'Studios compacts et fonctionnels'),
('Résidence', 'fa-hotel', 'Résidences meublées complètes');

-- =====================================================
-- TABLE: properties (anciennement residences)
-- =====================================================
CREATE TABLE `properties` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `owner_id` INT NOT NULL,
  `category_id` INT DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `listing_type` ENUM('reservation','rental','sale') NOT NULL DEFAULT 'reservation',
  `price_per_night` DECIMAL(12,2) DEFAULT 0,
  `price_monthly` DECIMAL(12,2) DEFAULT 0,
  `price_sale` DECIMAL(12,2) DEFAULT 0,
  `address` VARCHAR(255) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `country` VARCHAR(100) DEFAULT 'Côte d''Ivoire',
  `latitude` DECIMAL(10,8) DEFAULT NULL,
  `longitude` DECIMAL(11,8) DEFAULT NULL,
  `max_guests` INT DEFAULT 2,
  `bedrooms` INT DEFAULT 1,
  `bathrooms` INT DEFAULT 1,
  `area_sqm` INT DEFAULT NULL,
  `status` ENUM('active','pending','rejected','inactive','sold','rented') DEFAULT 'pending',
  `is_featured` TINYINT(1) DEFAULT 0,
  `views_count` INT DEFAULT 0,
  `score` DECIMAL(3,2) DEFAULT 0.00,
  `rules` TEXT DEFAULT NULL,
  `cancellation_policy` ENUM('flexible','moderate','strict') DEFAULT 'moderate',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX `idx_city` (`city`),
  INDEX `idx_status` (`status`),
  INDEX `idx_owner` (`owner_id`),
  INDEX `idx_type` (`listing_type`),
  FULLTEXT INDEX `idx_search` (`title`, `description`, `city`)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: property_images
-- =====================================================
CREATE TABLE `property_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: amenities
-- =====================================================
CREATE TABLE `amenities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(50) DEFAULT 'fa-check',
  `category` VARCHAR(50) DEFAULT 'general'
) ENGINE=InnoDB;

INSERT INTO `amenities` (`name`, `icon`, `category`) VALUES
('WiFi', 'fa-wifi', 'essential'),
('Climatisation', 'fa-snowflake', 'essential'),
('Piscine', 'fa-water-ladder', 'leisure'),
('Parking', 'fa-square-parking', 'essential'),
('Cuisine équipée', 'fa-utensils', 'kitchen'),
('Machine à laver', 'fa-jug-detergent', 'essential'),
('TV', 'fa-tv', 'entertainment'),
('Jardin', 'fa-tree', 'outdoor'),
('Sécurité 24h', 'fa-shield-halved', 'safety'),
('Vue mer', 'fa-water', 'view'),
('Balcon', 'fa-archway', 'outdoor'),
('Salle de sport', 'fa-dumbbell', 'leisure');

-- =====================================================
-- TABLE: property_amenities (pivot)
-- =====================================================
CREATE TABLE `property_amenities` (
  `property_id` INT NOT NULL,
  `amenity_id` INT NOT NULL,
  PRIMARY KEY (`property_id`, `amenity_id`),
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`amenity_id`) REFERENCES `amenities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: reservations
-- =====================================================
CREATE TABLE `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `guests` INT DEFAULT 1,
  `nights` INT NOT NULL,
  `price_per_night` DECIMAL(12,2) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `service_fee` DECIMAL(12,2) DEFAULT 0,
  `total_price` DECIMAL(12,2) NOT NULL,
  `status` ENUM('pending','confirmed','cancelled','completed','rejected') DEFAULT 'pending',
  `cancellation_reason` TEXT DEFAULT NULL,
  `special_requests` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_status` (`status`),
  INDEX `idx_dates` (`check_in`, `check_out`)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: rentals (locations mensuelles)
-- =====================================================
CREATE TABLE `rentals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,
  `monthly_price` DECIMAL(12,2) NOT NULL,
  `deposit` DECIMAL(12,2) DEFAULT 0,
  `total_paid` DECIMAL(12,2) DEFAULT 0,
  `status` ENUM('pending','active','ended','cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: sales (ventes)
-- =====================================================
CREATE TABLE `sales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `buyer_id` INT NOT NULL,
  `sale_price` DECIMAL(12,2) NOT NULL,
  `commission_amount` DECIMAL(12,2) DEFAULT 0,
  `owner_amount` DECIMAL(12,2) DEFAULT 0,
  `status` ENUM('pending','completed','cancelled') DEFAULT 'pending',
  `completed_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: payments
-- =====================================================
CREATE TABLE `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reservation_id` INT DEFAULT NULL,
  `rental_id` INT DEFAULT NULL,
  `sale_id` INT DEFAULT NULL,
  `payer_id` INT NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `commission` DECIMAL(12,2) DEFAULT 0,
  `owner_amount` DECIMAL(12,2) DEFAULT 0,
  `payment_method` ENUM('mobile_money','card','bank_transfer') DEFAULT 'mobile_money',
  `payment_phone` VARCHAR(30) DEFAULT NULL,
  `payment_name` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('pending','completed','refunded','failed') DEFAULT 'pending',
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `paid_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`rental_id`) REFERENCES `rentals`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`payer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: reviews
-- =====================================================
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `reservation_id` INT DEFAULT NULL,
  `rating` TINYINT NOT NULL,
  `comment` TEXT NOT NULL,
  `owner_reply` TEXT DEFAULT NULL,
  `replied_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: favorites
-- =====================================================
CREATE TABLE `favorites` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `property_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_fav` (`user_id`, `property_id`)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: messages
-- =====================================================
CREATE TABLE `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `property_id` INT DEFAULT NULL,
  `subject` VARCHAR(255) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE SET NULL,
  INDEX `idx_conversation` (`sender_id`, `receiver_id`)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: notifications
-- =====================================================
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `link` VARCHAR(255) DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: activity_logs
-- =====================================================
CREATE TABLE `activity_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: settings
-- =====================================================
CREATE TABLE `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('commission_rate', '10', 'Taux de commission plateforme (%)'),
('site_name', 'ResiBook', 'Nom du site'),
('site_email', 'contact@resibook.com', 'Email de contact'),
('currency', 'FCFA', 'Devise'),
('min_booking_days', '1', 'Durée minimale de réservation'),
('max_booking_days', '90', 'Durée maximale de réservation');

-- =====================================================
-- DONNÉES DE TEST
-- =====================================================

-- Admin (password: password)
INSERT INTO `users` (`role_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `status`, `is_verified`, `verification_badge`) VALUES
(1, 'Super', 'Admin', 'admin@resibook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 0700000000', 'active', 1, 1);

-- Propriétaires (password: password)
INSERT INTO `users` (`role_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `city`, `status`, `is_verified`, `verification_badge`) VALUES
(2, 'Kouadio', 'Jean', 'jean@resibook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 0711111111', 'Abidjan', 'active', 1, 1),
(2, 'Koné', 'Aminata', 'aminata@resibook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 0722222222', 'Yamoussoukro', 'active', 1, 1),
(2, 'Touré', 'Ibrahim', 'ibrahim@resibook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 0733333333', 'Bouaké', 'pending', 0, 0);

-- Clients (password: password)
INSERT INTO `users` (`role_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `city`, `status`) VALUES
(3, 'Diallo', 'Fatou', 'fatou@resibook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 0744444444', 'Abidjan', 'active'),
(3, 'Bamba', 'Moussa', 'moussa@resibook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 0755555555', 'Abidjan', 'active');

-- Properties
INSERT INTO `properties` (`owner_id`, `category_id`, `title`, `slug`, `description`, `listing_type`, `price_per_night`, `price_monthly`, `price_sale`, `address`, `city`, `max_guests`, `bedrooms`, `bathrooms`, `area_sqm`, `status`, `is_featured`, `score`, `rules`, `cancellation_policy`) VALUES
(2, 2, 'Villa Prestige Cocody', 'villa-prestige-cocody', 'Magnifique villa de standing à Cocody avec piscine, jardin tropical et vue imprenable. Idéale pour des vacances luxueuses en famille.', 'reservation', 75000, 0, 0, 'Cocody Riviera Golf', 'Abidjan', 8, 4, 3, 250, 'active', 1, 4.80, 'Non-fumeur|Pas d''animaux', 'moderate'),
(2, 1, 'Appartement Vue Lagune', 'appartement-vue-lagune', 'Superbe appartement moderne face à la lagune Ebrié. Entièrement meublé avec goût, parfait pour séjours professionnels.', 'reservation', 45000, 0, 0, 'Plateau, Rue du Commerce', 'Abidjan', 4, 2, 1, 120, 'active', 1, 4.50, 'Non-fumeur', 'flexible'),
(3, 3, 'Maison Familiale Yamoussoukro', 'maison-familiale-yamoussoukro', 'Charmante maison rénovée, proche de la Basilique. Cadre paisible et authentique.', 'rental', 0, 250000, 0, 'Quartier Résidentiel', 'Yamoussoukro', 6, 3, 2, 180, 'active', 0, 4.20, 'Respecter le voisinage', 'strict'),
(2, 4, 'Studio Moderne Marcory', 'studio-moderne-marcory', 'Studio tout confort idéalement situé à Marcory Zone 4. Proche restaurants et vie nocturne.', 'reservation', 20000, 0, 0, 'Marcory Zone 4', 'Abidjan', 2, 1, 1, 45, 'active', 0, 4.00, 'Non-fumeur|Calme après 22h', 'flexible'),
(3, 5, 'Résidence Bord de Mer Assinie', 'residence-bord-mer-assinie', 'Résidence pieds dans l''eau sur la plage d''Assinie. Cadre paradisiaque pour des vacances inoubliables.', 'reservation', 55000, 0, 0, 'Plage d''Assinie', 'Assinie', 4, 2, 1, 80, 'active', 1, 4.90, 'Respecter l''environnement', 'moderate'),
(2, 2, 'Villa à Vendre Riviera', 'villa-a-vendre-riviera', 'Superbe villa 5 pièces avec piscine à vendre en Riviera Faya. Quartier calme et sécurisé.', 'sale', 0, 0, 85000000, 'Riviera Faya', 'Abidjan', 10, 5, 4, 400, 'active', 1, 0, '', 'strict');

-- Property images
INSERT INTO `property_images` (`property_id`, `image_path`, `is_primary`, `sort_order`) VALUES
(1, 'default_villa.jpg', 1, 0),
(2, 'default_appart.jpg', 1, 0),
(3, 'default_maison.jpg', 1, 0),
(4, 'default_studio.jpg', 1, 0),
(5, 'default_residence.jpg', 1, 0),
(6, 'default_villa_sale.jpg', 1, 0);

-- Amenities pour propriétés
INSERT INTO `property_amenities` (`property_id`, `amenity_id`) VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),(1,7),(1,8),(1,9),
(2,1),(2,2),(2,4),(2,5),(2,7),(2,10),
(3,1),(3,4),(3,5),(3,8),
(4,1),(4,2),(4,7),
(5,1),(5,3),(5,8),(5,10),
(6,1),(6,2),(6,3),(6,4),(6,5),(6,7),(6,8),(6,9),(6,11),(6,12);

-- Réservations
INSERT INTO `reservations` (`property_id`, `client_id`, `check_in`, `check_out`, `guests`, `nights`, `price_per_night`, `subtotal`, `service_fee`, `total_price`, `status`) VALUES
(1, 5, '2026-04-25', '2026-04-30', 4, 5, 75000, 375000, 37500, 412500, 'confirmed'),
(2, 6, '2026-05-01', '2026-05-04', 2, 3, 45000, 135000, 13500, 148500, 'pending'),
(5, 5, '2026-05-10', '2026-05-15', 3, 5, 55000, 275000, 27500, 302500, 'completed'),
(4, 6, '2026-03-20', '2026-03-22', 1, 2, 20000, 40000, 4000, 44000, 'completed');

-- Paiements
INSERT INTO `payments` (`reservation_id`, `payer_id`, `amount`, `commission`, `owner_amount`, `payment_method`, `payment_phone`, `payment_name`, `status`, `transaction_id`, `paid_at`) VALUES
(1, 5, 412500, 41250, 371250, 'mobile_money', '+225 0744444444', 'Diallo Fatou', 'completed', 'TXN-20260425-001', '2026-04-25 10:00:00'),
(3, 5, 302500, 30250, 272250, 'card', NULL, 'Diallo Fatou', 'completed', 'TXN-20260510-002', '2026-05-10 14:30:00'),
(4, 6, 44000, 4400, 39600, 'mobile_money', '+225 0755555555', 'Bamba Moussa', 'completed', 'TXN-20260320-003', '2026-03-20 09:00:00');

-- Avis
INSERT INTO `reviews` (`property_id`, `client_id`, `reservation_id`, `rating`, `comment`, `owner_reply`, `replied_at`) VALUES
(1, 5, 1, 5, 'Villa absolument magnifique ! Piscine superbe. Nous reviendrons sans hésiter.', 'Merci beaucoup Fatou ! Au plaisir de vous revoir.', '2026-05-01 10:00:00'),
(5, 5, 3, 5, 'Un vrai paradis ! La plage est à 2 pas, le bungalow est charmant.', NULL, NULL),
(4, 6, 4, 4, 'Studio propre et bien situé. Bon rapport qualité/prix.', 'Merci Moussa !', '2026-03-25 15:00:00');

-- Messages
INSERT INTO `messages` (`sender_id`, `receiver_id`, `property_id`, `subject`, `message`) VALUES
(5, 2, 1, 'Question sur la Villa', 'Bonjour, est-ce que la piscine est chauffée ?'),
(2, 5, 1, 'Re: Question sur la Villa', 'Bonjour Fatou, oui la piscine est chauffée toute l''année !'),
(4, 1, NULL, 'Demande de validation', 'Bonjour Admin, j''attends la validation de mon compte. Merci.');

-- Notifications
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `link`) VALUES
(1, 'owner_pending', 'Nouveau propriétaire', 'Ibrahim Touré attend votre validation.', 'admin/owners'),
(2, 'booking_new', 'Nouvelle réservation', 'Fatou Diallo a réservé votre Villa Prestige Cocody.', 'owner/reservations'),
(5, 'booking_confirmed', 'Réservation confirmée', 'Votre réservation à Villa Prestige Cocody est confirmée.', 'client/reservations');

-- Favoris
INSERT INTO `favorites` (`user_id`, `property_id`) VALUES
(5, 1), (5, 5), (6, 2);

-- Logs
INSERT INTO `activity_logs` (`user_id`, `action`, `description`, `ip_address`) VALUES
(1, 'login', 'Connexion administrateur', '127.0.0.1'),
(2, 'property_create', 'Création: Villa Prestige Cocody', '127.0.0.1'),
(5, 'booking_create', 'Réservation #1 créée', '127.0.0.1');

COMMIT;
