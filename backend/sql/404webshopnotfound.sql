-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 27. Apr 2026 um 14:43
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `404webshopnotfound`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `salutation` varchar(10) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `payment_info` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `salutation`, `firstname`, `lastname`, `address`, `zip`, `city`, `email`, `username`, `password`, `payment_info`, `role`, `is_active`, `created_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, 'test@test.at', 'testuser', '$2y$10$ozT6w7/iK1Vf1IB6BJxep.8xSVgu6wa8P6UMYAhhrOR/F0wRCZ4Oq', NULL, 'customer', 1, '2026-04-26 23:19:36');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tabellenstruktur fur Tabelle `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tabellenstruktur fur Tabelle `product`
--

CREATE TABLE `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `rating` DECIMAL(2,1) DEFAULT 0.0,
    `image` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cart_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_product_cart` (`user_id`, `product_id`),
  KEY `idx_cart_user_id` (`user_id`),
  CONSTRAINT `fk_cart_user`
      FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
          ON DELETE CASCADE
);




--
-- Indizes fur Tabelle `wishlist`
--

ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_user_id` (`user_id`);

ALTER TABLE `wishlist`
    ADD CONSTRAINT `fk_wishlist_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE;

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT fur Tabelle `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `products` (`name`, `description`, `category`, `price`, `rating`, `image`) VALUES
   ('JUAN LAURA Schokoladen | Kakaomasse', 'Peru Pichari – Chuncho 100% | 70g', 'Schokolade', 9.90, 4.8, 'schoko1.png'),
   ('PUMATIY | Dunkle Schokolade & Chili', 'Peru Cusco – Aji 70% | 50g', 'Schokolade', 9.90, 4.6, 'schoko2.png'),
   ('PUMATIY Schokolade | Kakaomasse', 'Zeremonieller Kakao Peru Cusco – Chuncho 100% | 100g', 'Schokolade', 14.90, 4.9, 'schoko3.png'),
   ('KUYAY | Dunkle Schokolade & Blaubeeren', 'Peru Chocolat with Blueberry 70% | 70g', 'Schokolade', 9.90, 4.7, 'schoko4.png'),
   ('FJAK Chocolate | Dunkle Schokolade Belize', 'Trio Reserve Microlot 70% | BIO | 60g', 'Schokolade', 14.90, 4.8, 'schoko5.png'),
   ('CHOCOLATE & Love | Dunkle Schokolade & Minze', 'Mint Crunch 67% | BIO | 80g', 'Schokolade', 5.80, 4.5, 'schoko6.png');


-- Gutschein Tabelle und Testgutschein hinzufügen

CREATE TABLE `coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `value` DECIMAL(10,2) NOT NULL
);

INSERT INTO `coupons` (`code`, `value`)
VALUES ('TEST10', 10.00);