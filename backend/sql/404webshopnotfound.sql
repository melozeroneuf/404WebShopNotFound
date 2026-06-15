-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Jun 2026 um 23:53
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
-- Tabellenstruktur für Tabelle `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `expires_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `value`, `expires_at`) VALUES
(1, 'TEST10', 10.00, NULL),
(2, 'TEST20', 20.00, '2026-06-16'),
(3, 'TEST30', 30.00, '2026-06-16');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'offen',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `created_at`) VALUES
(1, 1, 19.80, 'bezahlt', '2026-06-15 19:51:49');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `name`, `price`, `quantity`) VALUES
(1, 1, 1, 'JUAN LAURA Schokoladen | Kakaomasse', 9.90, 2),
(2, 1, 2, 'PUMATIY | Dunkle Schokolade & Chili', 9.90, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `rating`, `image`, `is_active`, `created_at`) VALUES
(1, 'JUAN LAURA Schokoladen | Kakaomasse', 'Peru Pichari – Chuncho 100% | 70g', 'Schokolade', 9.90, 4.8, 'schoko1.png', 1, '2026-06-02 19:12:03'),
(2, 'PUMATIY | Dunkle Schokolade & Chili', 'Peru Cusco – Aji 70% | 50g', 'Schokolade', 9.90, 4.6, 'schoko2.png', 1, '2026-06-02 19:12:03'),
(3, 'PUMATIY Schokolade | Kakaomasse', 'Zeremonieller Kakao Peru Cusco – Chuncho 100% | 100g', 'Schokolade', 14.90, 4.9, 'schoko3.png', 1, '2026-06-02 19:12:03'),
(4, 'KUYAY | Dunkle Schokolade & Blaubeeren', 'Peru Chocolat with Blueberry 70% | 70g', 'Schokolade', 9.90, 4.7, 'schoko4.png', 1, '2026-06-02 19:12:03'),
(5, 'FJAK Chocolate | Dunkle Schokolade Belize', 'Trio Reserve Microlot 70% | BIO | 60g', 'Schokolade', 14.90, 4.8, 'schoko5.png', 1, '2026-06-02 19:12:03'),
(6, 'CHOCOLATE & Love | Dunkle Schokolade & Minze', 'Mint Crunch 67% | BIO | 80g', 'Schokolade', 5.80, 4.5, 'schoko6.png', 1, '2026-06-02 19:12:03'),
(9, 'FRIIS-HOLM Pralinen & Whisky | Filled Chocolate Stauning Danish Whisky', 'Stauning KAOS Triple Malt Whisky | 8 Pralinen | 80g', 'Pralinen & Trüffel', 23.90, 4.9, 'praline1.png', 1, '2026-06-15 21:18:34'),
(10, 'LA HIGUERA Feigenpralinen in dunkler Schokolade | Rabitos Royale Dark', 'Feigenpralinen in dunkler Schokolade | 142g', 'Pralinen & Trüffel', 18.90, 4.8, 'praline2.png', 1, '2026-06-15 21:20:13'),
(11, 'MAJANI Nougat Cremino 100 Stück Packung | FIAT-Creme Noir', 'Zartbitter-Nougat Cremini | 100 Stück | 1013g', 'Pralinen & Trüffel', 69.90, 4.8, 'praline3.png', 1, '2026-06-15 21:21:42'),
(12, 'ANTICA TORRONERIA PIEMONTESE Schokoladentrüffel 10er-Etui | Tartufi Dolci', 'Mini-Schokoladentrüffel gemischt | 10 Stück | 70g', 'Pralinen & Trüffel', 8.60, 4.7, 'praline4.png', 1, '2026-06-15 21:22:45'),
(13, 'BLANXART Schokoladentrüffel | Trufas de chocolate', 'Dunkle Schokoladentrüffel mit Kakaopulver | 100g', 'Pralinen & Trüffel', 8.90, 4.8, 'praline5.png', 1, '2026-06-15 21:23:43'),
(14, 'ANTICA TORRONERIA PIEMONTESE Schokoladentrüffel 12er-Etui | Tartufi Dolci', 'Gemischte Schokoladentrüffel | 12 Stück | 165g', 'Pralinen & Trüffel', 13.95, 4.8, 'praline6.png', 1, '2026-06-15 21:24:43'),
(15, 'NOALYA Pistaziencreme 620 | Crema al Pistacchio', 'Pistaziencreme mit 35% Pistazien | 200g', 'Schoko- & Nusscremes', 12.90, 4.9, 'schokocreme1.png', 1, '2026-06-15 21:26:46'),
(16, 'NOALYA Schokoladencreme 604 | Crema alla Nocciola', 'Schoko-Haselnuss-Creme mit 45% Haselnüssen | 200g', 'Schoko- & Nusscremes', 11.95, 4.8, 'schokocreme2.png', 1, '2026-06-15 21:26:46'),
(17, 'NOALYA Schokoladencreme 605 | Crema alla Nocciola', 'Haselnusscreme mit Schokolade | 200g', 'Schoko- & Nusscremes', 11.95, 4.8, 'schokocreme3.png', 1, '2026-06-15 21:26:46'),
(18, 'SCYAVURU Orangencreme | Crema all\'Arancia', 'Süße Orangencreme | 200g', 'Schoko- & Nusscremes', 6.90, 4.6, 'schokocreme4.png', 1, '2026-06-15 21:28:31'),
(19, 'SCYAVURU Süße Haselnusscreme | Crema alla Nocciola', 'Haselnusscreme 15% | 200g', 'Schoko- & Nusscremes', 7.95, 4.7, 'schokocreme5.png', 1, '2026-06-15 21:28:31'),
(20, 'SCYAVURU Süße Pistaziencreme Sizilien | Pistacchio Crema', 'Pistaziencreme 25% | 200g', 'Schoko- & Nusscremes', 11.40, 4.8, 'schokocreme6.png', 1, '2026-06-15 21:28:31'),
(21, 'A. MORIN Kakaopulver Elfenbeinküste | Côte d\'Ivoire Guémon 100%', 'Single-Origin Kakaopulver | Elfenbeinküste | 200g', 'Trinkschokoladen', 16.90, 4.8, 'trinkschokolade1.png', 1, '2026-06-15 21:31:12'),
(22, 'A. MORIN Kakaopulver Pur Cacao | Madagascar Sambirano 100%', 'Bio Kakaopulver | Madagascar Sambirano | 200g', 'Trinkschokoladen', 16.90, 4.9, 'trinkschokolade2.png', 1, '2026-06-15 21:31:12'),
(23, 'A. MORIN Kakaopulver Pur Cacao | Panama Dabaiba 100%', 'Bio Kakaopulver | Panama Dabaiba | 200g', 'Trinkschokoladen', 17.90, 4.8, 'trinkschokolade3.png', 1, '2026-06-15 21:31:12'),
(24, 'A. MORIN Kakaopulver Pur Cacao | Peru Chanchamayo 100%', 'Bio Kakaopulver | Peru Chanchamayo | 200g', 'Trinkschokoladen', 16.90, 4.9, 'trinkschokolade4.png', 1, '2026-06-15 21:31:12'),
(25, 'A. MORIN Kakaopulver Pur Cacao | République Dominicaine Yamasa 100%', 'Bio Kakaopulver | Dominikanische Republik Yamasa | 200g', 'Trinkschokoladen', 17.90, 4.8, 'trinkschokolade5.png', 1, '2026-06-15 21:32:30'),
(26, 'A. MORIN Kakaopulver Pur Cacao | São Tomé Agua Grande 100%', 'Bio Kakaopulver | São Tomé Agua Grande | 200g', 'Trinkschokoladen', 17.90, 4.8, 'trinkschokolade6.png', 1, '2026-06-15 21:32:30'),
(27, 'CASA KAKAU Rohe Kakaobohnen | Ecuador', 'Rohe Kakaobohnen aus Ecuador | 200g', 'Kakaobohnen & Nibs', 9.95, 4.8, 'bohne1.png', 1, '2026-06-15 21:34:11'),
(28, 'CHOCO DEL SOL Geröstete Kakaonibs', 'Bio Kakaonibs geröstet | 100g', 'Kakaobohnen & Nibs', 8.90, 4.7, 'bohne2.png', 1, '2026-06-15 21:34:11'),
(29, 'CHOCO DEL SOL Geröstete Kakaobohnen | Belize', 'Bio Kakaobohnen geröstet | Belize | 100g', 'Kakaobohnen & Nibs', 7.90, 4.8, 'bohne3.png', 1, '2026-06-15 21:34:11'),
(30, 'CHOCO DEL SOL Rohe Kakaobohnen | Belize', 'Bio Kakaobohnen roh | Belize | 100g', 'Kakaobohnen & Nibs', 6.90, 4.7, 'bohne4.png', 1, '2026-06-15 21:34:11'),
(31, 'CHOCO DEL SOL Schokoladendragees | Kakaobohnen', 'Geröstete Kakaobohnen in Milchschokolade | 100g', 'Kakaobohnen & Nibs', 9.90, 4.7, 'bohne5.png', 1, '2026-06-15 21:35:15'),
(32, 'EDELMOND Rohe Kakaonibs | Low Cadmium 100%', 'Rohe Kakaonibs aus Kolumbien | 450g', 'Kakaobohnen & Nibs', 29.90, 4.9, 'bohne6.png', 1, '2026-06-15 21:35:15');

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
(1, NULL, NULL, NULL, NULL, NULL, NULL, 'test@test.at', 'testuser', '$2y$10$ozT6w7/iK1Vf1IB6BJxep.8xSVgu6wa8P6UMYAhhrOR/F0wRCZ4Oq', NULL, 'customer', 1, '2026-04-26 23:19:36'),
(2, NULL, NULL, NULL, NULL, NULL, NULL, 'wi24b087@technikum-wien.at', 'wi24b087', '$2y$10$bylEkpdl/qz6TBECTaPJO.qyytpYac7lSQ.WMiH.jp1I2AcUWHPhC', NULL, 'admin', 1, '2026-05-17 18:04:50');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_product_cart` (`user_id`,`product_id`),
  ADD KEY `idx_cart_user_id` (`user_id`);

--
-- Indizes für die Tabelle `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indizes für die Tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indizes für die Tabelle `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT für Tabelle `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
