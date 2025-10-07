-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-09-2025 a las 02:00:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `market_club`
--

-- Crear la base de datos si no existe y seleccionarla
CREATE DATABASE IF NOT EXISTS `market_club` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `market_club`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `session_id`, `subtotal`, `tax_amount`, `shipping_amount`, `total_amount`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 3389.93, 644.09, 10000.00, 14034.02, NULL, '2025-09-12 05:01:35', '2025-09-12 05:01:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `product_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_snapshot`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(6, 'Bebidas Alcohólicas', 'bebidas-alcoholicas', 'Cervezas, vinos, licores y otras bebidas alcohólicas', NULL, 1, '2025-09-12 05:33:05', '2025-09-12 05:33:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_11_153457_create_personal_access_tokens_table', 1),
(5, '2025_09_11_153520_create_categories_table', 1),
(6, '2025_09_11_153523_create_products_table', 1),
(7, '2025_09_11_153525_create_orders_table', 1),
(8, '2025_09_11_153530_create_order_items_table', 1),
(9, '2025_09_11_210120_add_phone_country_to_users_table', 1),
(10, '2025_09_11_220147_add_role_to_users_table', 1),
(11, '2025_09_11_223554_create_payment_transactions_table', 1),
(12, '2025_09_11_225739_create_carts_table', 1),
(13, '2025_09_11_225747_create_cart_items_table', 1),
(14, '2025_09_11_225757_create_wishlists_table', 1),
(15, '2025_09_11_230000_create_product_types_table', 1),
(16, '2025_09_11_234800_add_product_type_to_products_table', 1),
(17, '2025_09_12_003928_add_slug_to_products_table', 2),
(18, '2025_09_12_141130_add_is_wholesaler_to_users_table', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`shipping_address`)),
  `billing_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`billing_address`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `wompi_transaction_id` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'CARD',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'COP',
  `status` varchar(255) NOT NULL DEFAULT 'PENDING',
  `wompi_status` varchar(255) DEFAULT NULL,
  `wompi_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`wompi_response`)),
  `customer_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_data`)),
  `payment_url` varchar(255) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 12, 'auth_token', '28e83d035ff669cc59c6ba90723a2d3bb337abeb5449c3e598fcdca990a1ebc1', '[\"*\"]', NULL, NULL, '2025-09-12 19:05:06', '2025-09-12 19:05:06'),
(2, 'App\\Models\\User', 12, 'auth_token', '873082d677a5c0004b6ed29e06d3374a5ec19a466aa32b08532ea8c52350c03e', '[\"*\"]', NULL, NULL, '2025-09-12 19:07:37', '2025-09-12 19:07:37'),
(3, 'App\\Models\\User', 13, 'auth_token', '0105869d0438c5cfefdaaead15d3ff934aa7a95134419ff506461cdd94d43822', '[\"*\"]', NULL, NULL, '2025-09-12 19:08:46', '2025-09-12 19:08:46'),
(4, 'App\\Models\\User', 14, 'auth_token', 'f6b88a311739fbd8120ca355c1cf224289c88cbdac6ee07a0bb6c6118ce53006', '[\"*\"]', NULL, NULL, '2025-09-12 19:20:22', '2025-09-12 19:20:22'),
(5, 'App\\Models\\User', 14, 'auth_token', '4efed8fc8e6994cd954dfd903e087ce30281973008f463a9f4c69b8ab1b54293', '[\"*\"]', NULL, NULL, '2025-09-12 19:21:31', '2025-09-12 19:21:31'),
(6, 'App\\Models\\User', 16, 'auth_token', '47f89b1c7b333b64f4b49c2707ea9e7db3a43d4db484764758a8ec4d2c06c1ec', '[\"*\"]', NULL, NULL, '2025-09-12 19:37:13', '2025-09-12 19:37:13'),
(7, 'App\\Models\\User', 17, 'auth_token', '1c6572463758803b5d1e4a0e7ec09cccf799c66a1abbdeced26287484cce5c4c', '[\"*\"]', NULL, NULL, '2025-09-12 19:40:29', '2025-09-12 19:40:29'),
(8, 'App\\Models\\User', 18, 'auth_token', 'e4f7bb4cf7117061927cd6ecbe4f8465453c5b9d43258d86f0fbb757fbc3d969', '[\"*\"]', NULL, NULL, '2025-09-12 19:50:10', '2025-09-12 19:50:10'),
(9, 'App\\Models\\User', 18, 'auth_token', '8767d4f4e89c994f4f0932c89c117c2cd818b788bd7009ccffadcf9688fc96a8', '[\"*\"]', NULL, NULL, '2025-09-12 19:50:36', '2025-09-12 19:50:36'),
(10, 'App\\Models\\User', 18, 'auth_token', '76d039350095aa8cc917838f926ba694c99a9e099e4f73cd16e37ef56d9cb8d6', '[\"*\"]', NULL, NULL, '2025-09-12 20:01:48', '2025-09-12 20:01:48'),
(11, 'App\\Models\\User', 18, 'auth_token', 'ad05c143f48bd87583c785fed5299c6fd5cdf14f35126225586b6747afa53c63', '[\"*\"]', NULL, NULL, '2025-09-13 03:38:40', '2025-09-13 03:38:40'),
(12, 'App\\Models\\User', 17, 'auth_token', 'ec301341123b3df3a5de939665041144bdbd4fda9428977642e5e86ab3406afe', '[\"*\"]', NULL, NULL, '2025-09-14 03:51:00', '2025-09-14 03:51:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `product_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`)),
  `product_specific_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_specific_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `price`, `sale_price`, `sku`, `stock_quantity`, `image`, `gallery`, `is_active`, `is_featured`, `category_id`, `product_type_id`, `attributes`, `product_specific_data`, `created_at`, `updated_at`) VALUES
(221, 'ADNAMS GHOST SHIP', 'adnams-ghost-ship', 'Cerveza Inglaterra en botella de 330ml. Producto importado de alta calidad.', 29000.00, NULL, 'BEER-BADF07E1', 67, NULL, NULL, 1, 1, 6, 1, NULL, '{\"country_of_origin\":\"Inglaterra\",\"volume_ml\":\"500\",\"packaging_type\":\"lata\",\"alcohol_content\":6,\"beer_style\":\"lager\",\"brewery\":\"ADNAMS GHOST\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(222, 'ADNAMS KOBOLD ENGLISH LAGER', 'adnams-kobold-english-lager', 'Cerveza Inglaterra en botella de 330ml. Producto importado de alta calidad.', 29000.00, NULL, 'BEER-F479631E', 55, 'products/2025/09/efb0958b-48ab-4b94-a3c9-db0950bac38f.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"ADNAMS KOBOLD\",\"country_of_origin\":\"Inglaterra\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(223, 'AGUILA ORIGINAL LATA X 330ML', 'aguila-original-lata-x-330ml', 'Cerveza Colombia en lata de 330ml. Producto importado de alta calidad.', 4500.00, NULL, 'BEER-FD0D664A', 18, 'products/2025/09/ac5d0667-9135-4522-a8a5-29b8647a5215.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"AGUILA ORIGINAL\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(224, 'ANDINA DORADA', 'andina-dorada', 'Cerveza Colombia en botella de 330ml. Producto importado de alta calidad.', 4000.00, NULL, 'BEER-90EF2CC3', 77, 'products/2025/09/8131ab49-f311-48ef-91f2-7eb8a2a1de03.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"ANDINA DORADA\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(225, 'ARTESANAL BLONDE ALE', 'artesanal-blonde-ale', 'Cerveza Colombia en botella de 330ml. Producto importado de alta calidad.', 10000.00, NULL, 'BEER-43E089E8', 57, 'products/2025/09/33c3d1e6-1d21-4a81-9e6f-6e0287bc53c5.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"blonde\",\"brewery\":\"ARTESANAL BLONDE\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(226, 'ARTESANAL PORTER', 'artesanal-porter', 'Cerveza Colombia en botella de 330ml. Producto importado de alta calidad.', 10000.00, NULL, 'BEER-50BC3793', 17, 'products/2025/09/3a8ca232-7333-4f6f-996d-fb7f02c5acde.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"porter\",\"brewery\":\"ARTESANAL PORTER\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(227, 'ARTESANAL SAISON', 'artesanal-saison', 'Cerveza Colombia en botella de 330ml. Producto importado de alta calidad.', 10000.00, NULL, 'BEER-C8CB28CC', 40, 'products/2025/09/0655c233-7fd3-434a-81ee-cac16d78e6f1.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"ARTESANAL SAISON\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(228, 'BARRIL BITBURGER 5LTR', 'barril-bitburger-5ltr', 'Cerveza Alemania en barril de 5000ml. Producto importado de alta calidad.', 148000.00, NULL, 'BEER-D79BDD8B', 100, 'products/2025/09/71ee472a-5b40-4889-9d33-55c4bd2707b5.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"BARRIL BITBURGER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(229, 'BENEDIK TINER BARRILITO 5 LTRS', 'benedik-tiner-barrilito-5-ltrs', 'Cerveza Alemania en barril de 5000ml. Producto importado de alta calidad.', 154000.00, NULL, 'BEER-3F24772A', 79, NULL, NULL, 1, 1, 6, 1, NULL, '{\"country_of_origin\":\"Alemania\",\"volume_ml\":\"5000\",\"packaging_type\":\"barril\",\"alcohol_content\":5,\"beer_style\":\"lager\",\"brewery\":\"BENEDIK TINER\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(230, 'BENEDIKTINER LATA 500ML', 'benediktiner-lata-500ml', 'Cerveza Alemania en lata de 500ml. Producto importado de alta calidad.', 13000.00, NULL, 'BEER-860EBCAF', 80, 'products/2025/09/7fb7a620-89ce-45b8-9a76-451f5f27243d.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"BENEDIKTINER LATA\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(231, 'BIRRA PERONI BOTELLA 330', 'birra-peroni-botella-330', 'Cerveza Italia en botella de 330ml. Producto importado de alta calidad.', 9500.00, NULL, 'BEER-E0650423', 80, 'products/2025/09/8f07bd0b-9b92-4288-b5a0-862bd5c9ea8c.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"BIRRA PERONI\",\"country_of_origin\":\"Italia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(232, 'BITBURGER DRIVE 0.0%', 'bitburger-drive-00', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 10000.00, NULL, 'BEER-6C0C6327', 99, 'products/2025/09/4d07b987-0c46-48b4-94c6-ca1e3eb0c721.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"0\",\"beer_style\":\"lager\",\"brewery\":\"BITBURGER DRIVE\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(233, 'BITBURGER PREMIUM PILS', 'bitburger-premium-pils', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 14000.00, NULL, 'BEER-461DD78A', 58, 'products/2025/09/95a885b6-ed7f-437b-b1df-10aa7d7e4dfc.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"pilsner\",\"brewery\":\"BITBURGER PREMIUM\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(234, 'BITBURGER PREMIUM X 500 ML', 'bitburger-premium-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18800.00, NULL, 'BEER-8E07A3C1', 32, 'products/2025/09/23485f1c-7874-4d37-96c4-59bf261360c9.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"BITBURGER PREMIUM\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(235, 'BITBURGUER LATA + 13,5 FREE', 'bitburguer-lata-135-free', 'Cerveza Alemania en lata de 330ml. Producto importado de alta calidad.', 16000.00, NULL, 'BEER-FAF60CEE', 91, NULL, NULL, 1, 0, 6, 1, NULL, '{\"country_of_origin\":\"Alemania\",\"volume_ml\":\"330\",\"packaging_type\":\"lata\",\"alcohol_content\":5,\"beer_style\":\"lager\",\"brewery\":\"BITBURGUER LATA\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(236, 'BREWDOG ELVIS JUICE LATA', 'brewdog-elvis-juice-lata', 'Cerveza Escocia en lata de 330ml. Producto importado de alta calidad.', 16500.00, NULL, 'BEER-A0259E76', 49, 'products/2025/09/12328890-cebd-4d77-b0ef-78716d1b032a.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"BREWDOG ELVIS\",\"country_of_origin\":\"Escocia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(237, 'BREWDOG HAZY JANE', 'brewdog-hazy-jane', 'Cerveza Escocia en botella de 330ml. Producto importado de alta calidad.', 17000.00, NULL, 'BEER-0FAE3537', 86, 'products/2025/09/6b77ef09-48e1-4b26-94ad-7fec4927bf97.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"BREWDOG HAZY\",\"country_of_origin\":\"Escocia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(238, 'BREWDOG HAZY JANE LATA 330ML', 'brewdog-hazy-jane-lata-330ml', 'Cerveza Escocia en lata de 330ml. Producto importado de alta calidad.', 15000.00, NULL, 'BEER-FC41C60E', 88, 'products/2025/09/cb36c0fc-c152-4d5f-beb3-905b694ccb02.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"BREWDOG HAZY\",\"country_of_origin\":\"Escocia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(239, 'BRUDER MANGO GRANADILLA', 'bruder-mango-granadilla', 'Cerveza Colombia en botella de 330ml. Producto importado de alta calidad.', 12000.00, NULL, 'BEER-879CD943', 70, 'products/2025/09/08aabd54-d680-4dd0-ae07-5e709226a38f.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"BRUDER MANGO\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(240, 'BRUGSE ZOT BLOND 330', 'brugse-zot-blond-330', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 26000.00, NULL, 'BEER-88BA6650', 82, 'products/2025/09/74692787-67b1-4ec2-9a6f-124c772c2b24.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"blonde\",\"brewery\":\"BRUGSE ZOT\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(241, 'CERVERZA HOFBRAU MUNCHEN BOTELLA X 500 ML', 'cerverza-hofbrau-munchen-botella-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18800.00, NULL, 'BEER-4C9196DE', 46, NULL, NULL, 1, 0, 6, 1, NULL, '{\"country_of_origin\":\"Alemania\",\"volume_ml\":\"500\",\"packaging_type\":\"botella\",\"alcohol_content\":6,\"beer_style\":\"lager\",\"brewery\":\"CERVERZA HOFBRAU\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(242, 'CERVEZA 1906', 'cerveza-1906', 'Cerveza España en botella de 330ml. Producto importado de alta calidad.', 17000.00, NULL, 'BEER-ADA1DF4E', 39, 'products/2025/09/34209c59-77ca-4bee-86d7-67a0bd5c354a.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA 1906\",\"country_of_origin\":\"Espa\\u00f1a\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(243, 'CERVEZA 8.6 BLACK DARK BEER X 500 ML', 'cerveza-86-black-dark-beer-x-500-ml', 'Cerveza Países Bajos en botella de 500ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-E4408D78', 51, 'products/2025/09/7836a27f-a3fc-4667-b756-d1ee380835f8.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"8.6\",\"beer_style\":\"dark\",\"brewery\":\"CERVEZA 8.6\",\"country_of_origin\":\"Pa\\u00edses Bajos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(244, 'CERVEZA 8.6 EXTREME STRONG BEER X 500 ML', 'cerveza-86-extreme-strong-beer-x-500-ml', 'Cerveza Países Bajos en botella de 500ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-BF6A5388', 44, 'products/2025/09/5da421b9-e71e-4534-a0bd-5690f3dfd06b.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"8.6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA 8.6\",\"country_of_origin\":\"Pa\\u00edses Bajos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(245, 'CERVEZA 8.6 ORIGINAL BLOND BEER X 500 ML', 'cerveza-86-original-blond-beer-x-500-ml', 'Cerveza Países Bajos en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-FCF1BB90', 80, 'products/2025/09/12c1678a-29dc-4da2-a4a7-83c705268524.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"8.6\",\"beer_style\":\"blonde\",\"brewery\":\"CERVEZA 8.6\",\"country_of_origin\":\"Pa\\u00edses Bajos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(246, 'CERVEZA 8.6 RED RED BEER X 500 ML', 'cerveza-86-red-red-beer-x-500-ml', 'Cerveza Países Bajos en botella de 500ml. Producto importado de alta calidad.', 21000.00, NULL, 'BEER-EF16D7DC', 23, NULL, NULL, 1, 1, 6, 1, NULL, '{\"country_of_origin\":\"Pa\\u00edses Bajos\",\"volume_ml\":\"500\",\"packaging_type\":\"botella\",\"alcohol_content\":8.6,\"beer_style\":\"lager\",\"brewery\":\"CERVEZA 8.6\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(247, 'CERVEZA ARTESANAL IPA', 'cerveza-artesanal-ipa', 'Cerveza Colombia en botella de 330ml. Producto importado de alta calidad.', 13600.00, NULL, 'BEER-F6AF9AAE', 95, NULL, NULL, 1, 0, 6, 1, NULL, '{\"country_of_origin\":\"Colombia\",\"volume_ml\":\"330\",\"packaging_type\":\"botella\",\"alcohol_content\":5,\"beer_style\":\"ipa\",\"brewery\":\"CERVEZA ARTESANAL\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(248, 'CERVEZA ASAHI SUPER DRY BOTELLA 330', 'cerveza-asahi-super-dry-botella-330', 'Cerveza Japón en botella de 330ml. Producto importado de alta calidad.', 10000.00, NULL, 'BEER-FBD1C557', 65, 'products/2025/09/42ec6a9a-54c9-4eac-8348-70f7b573f924.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA ASAHI\",\"country_of_origin\":\"Jap\\u00f3n\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(249, 'CERVEZA BBC UNIDAD', 'cerveza-bbc-unidad', 'Cerveza Colombia en botella de 330ml. Producto importado de alta calidad.', 5000.00, NULL, 'BEER-DC63B62B', 39, 'products/2025/09/a3ffbf4a-5d2e-4544-8298-2d113b7a8790.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA BBC\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(250, 'CERVEZA CORONA EXTRA 330 ML', 'cerveza-corona-extra-330-ml', 'Cerveza México en botella de 330ml. Producto importado de alta calidad.', 7000.00, NULL, 'BEER-BF07253A', 81, 'products/2025/09/e9a66393-6469-479d-a548-17b3997aaae2.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA CORONA\",\"country_of_origin\":\"M\\u00e9xico\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(251, 'CERVEZA COSTEÑA', 'cerveza-costena', 'Cerveza Colombia en lata de 330ml. Producto importado de alta calidad.', 4000.00, NULL, 'BEER-F066C1FC', 54, 'products/2025/09/af9ff64d-2f79-4928-9a49-c6b1270e97b5.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA COSTE\\u00d1A\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:30'),
(252, 'CERVEZA CUSQUEÑA', 'cerveza-cusquena', 'Cerveza Perú en botella de 330ml. Producto importado de alta calidad.', 8500.00, NULL, 'BEER-68852689', 97, 'products/2025/09/1a8c8c96-8b8b-4f86-a95b-b75d089c725d.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA CUSQUE\\u00d1A\",\"country_of_origin\":\"Per\\u00fa\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(253, 'CERVEZA CZECHVAR CZECH LAGER X 330 ML', 'cerveza-czechvar-czech-lager-x-330-ml', 'Cerveza República Checa en botella de 330ml. Producto importado de alta calidad.', 13000.00, NULL, 'BEER-AA22509A', 42, 'products/2025/09/2db416c9-fa65-46ed-bfe0-4587d2eb6f91.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA CZECHVAR\",\"country_of_origin\":\"Rep\\u00fablica Checa\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(254, 'CERVEZA DUNKEL ERDINGER X 500 ML', 'cerveza-dunkel-erdinger-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 23000.00, NULL, 'BEER-F37258EC', 37, 'products/2025/09/f75e36e7-5c4c-4e0f-8b84-826e24fc7447.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"dark\",\"brewery\":\"CERVEZA DUNKEL\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(255, 'CERVEZA DUVEL BELGIAN GOLDEN ALE X 330 ML', 'cerveza-duvel-belgian-golden-ale-x-330-ml', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 26000.00, NULL, 'BEER-26628D0D', 22, 'products/2025/09/56696992-0747-4d6b-b868-88434ab92443.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"ale\",\"brewery\":\"CERVEZA DUVEL\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(256, 'CERVEZA ERDINGER WEIBBIER', 'cerveza-erdinger-weibbier', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 22000.00, NULL, 'BEER-117BBA77', 43, 'products/2025/09/0375e709-667d-4e74-9947-be5afd6eb8f7.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA ERDINGER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(257, 'CERVEZA ESTRELLA GALICIA  XUND', 'cerveza-estrella-galicia-xund', 'Cerveza España en botella de 330ml. Producto importado de alta calidad.', 17000.00, NULL, 'BEER-6EFED111', 68, 'products/2025/09/9e95a84c-e405-4aef-865c-2fe8ca38cb84.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA ESTRELLA\",\"country_of_origin\":\"Espa\\u00f1a\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(258, 'CERVEZA ESTRELLA GALICIA LATA', 'cerveza-estrella-galicia-lata', 'Cerveza España en lata de 330ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-E847FE6A', 43, 'products/2025/09/f944224c-ac4b-4ff9-86d5-8ccbd7a295ed.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA ESTRELLA\",\"country_of_origin\":\"Espa\\u00f1a\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(259, 'CERVEZA GERMAN RED STEAM BREW X 500 ML', 'cerveza-german-red-steam-brew-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-1C16FA2F', 13, 'products/2025/09/8a3f453c-5be2-43ed-9521-fe49f8ac3a8e.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA GERMAN\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(260, 'CERVEZA GULDEN DRAAK X UND', 'cerveza-gulden-draak-x-und', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 34000.00, NULL, 'BEER-8F595474', 39, NULL, NULL, 1, 1, 6, 1, NULL, '{\"country_of_origin\":\"B\\u00e9lgica\",\"volume_ml\":\"330\",\"packaging_type\":\"botella\",\"alcohol_content\":5,\"beer_style\":\"lager\",\"brewery\":\"CERVEZA GULDEN\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(261, 'CERVEZA HOLLANDIA PREMIUM', 'cerveza-hollandia-premium', 'Cerveza Países Bajos en botella de 330ml. Producto importado de alta calidad.', 14000.00, NULL, 'BEER-8E0788AD', 11, 'products/2025/09/1683b948-0fbe-4249-a487-8ce1c24d6933.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA HOLLANDIA\",\"country_of_origin\":\"Pa\\u00edses Bajos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(262, 'CERVEZA IRON MAIDEN X 500 ML', 'cerveza-iron-maiden-x-500-ml', 'Cerveza Inglaterra en botella de 500ml. Producto importado de alta calidad.', 22000.00, NULL, 'BEER-0BF6D39F', 63, 'products/2025/09/af4f9578-6b63-4f2d-bcb1-c2a722b764ac.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA IRON\",\"country_of_origin\":\"Inglaterra\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(263, 'CERVEZA LA CHOUFFE 40 X UND', 'cerveza-la-chouffe-40-x-und', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 25000.00, NULL, 'BEER-43586A15', 99, 'products/2025/09/a6761bfd-8ccf-4cce-8e04-7a1013c46406.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA LA\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(264, 'CERVEZA LIEFMANS FRUITESSE BOTELLA X 250 ML', 'cerveza-liefmans-fruitesse-botella-x-250-ml', 'Cerveza Bélgica en botella de 250ml. Producto importado de alta calidad.', 20000.00, NULL, 'BEER-8E0ACB67', 49, 'products/2025/09/244a540e-5b95-4ea5-bfa7-06aa0191998a.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA LIEFMANS\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(265, 'CERVEZA MAHOU', 'cerveza-mahou', 'Cerveza España en botella de 330ml. Producto importado de alta calidad.', 9000.00, NULL, 'BEER-3327D0F4', 91, NULL, NULL, 1, 0, 6, 1, NULL, '{\"country_of_origin\":\"Espa\\u00f1a\",\"volume_ml\":\"330\",\"packaging_type\":\"botella\",\"alcohol_content\":5,\"beer_style\":\"lager\",\"brewery\":\"CERVEZA MAHOU\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(266, 'CERVEZA MODELO ESPECIAL', 'cerveza-modelo-especial', 'Cerveza México en botella de 330ml. Producto importado de alta calidad.', 9000.00, NULL, 'BEER-21851CC6', 90, 'products/2025/09/b2706e61-dff2-4321-9f12-313dd480d73f.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA MODELO\",\"country_of_origin\":\"M\\u00e9xico\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(267, 'CERVEZA PALE ALE STEAM BREW X 500 ML', 'cerveza-pale-ale-steam-brew-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-33429B51', 70, 'products/2025/09/3a032fb4-ec03-43c9-98ce-d2380f97f199.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"pale_ale\",\"brewery\":\"CERVEZA PALE\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(268, 'CERVEZA PILSEN LATA X 330', 'cerveza-pilsen-lata-x-330', 'Cerveza Colombia en lata de 330ml. Producto importado de alta calidad.', 4500.00, NULL, 'BEER-DB148626', 63, 'products/2025/09/da84b314-7b8d-4975-8b52-8b8f3d116ad6.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"pilsner\",\"brewery\":\"CERVEZA PILSEN\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(269, 'CERVEZA POKER LATA X 330 ML', 'cerveza-poker-lata-x-330-ml', 'Cerveza Colombia en lata de 330ml. Producto importado de alta calidad.', 4000.00, NULL, 'BEER-FF3AF601', 83, 'products/2025/09/7c57ad58-a46a-407b-a83b-557fe56abee5.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA POKER\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(270, 'CERVEZA REDDS LATA X 269 ML', 'cerveza-redds-lata-x-269-ml', 'Cerveza Colombia en lata de 269ml. Producto importado de alta calidad.', 4800.00, NULL, 'BEER-57BE3920', 68, 'products/2025/09/5c69c2ff-0634-49ff-bded-e6e9dbded244.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA REDDS\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(271, 'CERVEZA SOL BNR 330', 'cerveza-sol-bnr-330', 'Cerveza México en botella de 330ml. Producto importado de alta calidad.', 5000.00, NULL, 'BEER-911BFDFF', 88, 'products/2025/09/2aa228ee-8f07-4a59-8680-6f29e9f26272.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA SOL\",\"country_of_origin\":\"M\\u00e9xico\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(272, 'CERVEZA STEAM BREW 500ML XUND IMPERIAL IPA', 'cerveza-steam-brew-500ml-xund-imperial-ipa', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-D8750B8E', 45, 'products/2025/09/b5217f36-67b7-4449-8384-367224755eb8.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"ipa\",\"brewery\":\"CERVEZA STEAM\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(273, 'CERVEZA STEAM BREW X 500 ML', 'cerveza-steam-brew-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-0252458F', 83, 'products/2025/09/8981637f-11d7-481d-805a-b1a90863ef91.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA STEAM\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(274, 'CERVEZA STELLA ARTOIS', 'cerveza-stella-artois', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 7000.00, NULL, 'BEER-3AEE6446', 76, 'products/2025/09/523992a4-959c-47b1-9286-fd16ed7099fc.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA STELLA\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(275, 'CERVEZA TEKATE', 'cerveza-tekate', 'Cerveza México en botella de 330ml. Producto importado de alta calidad.', 4000.00, NULL, 'BEER-C26CB244', 91, 'products/2025/09/f1eb9aa3-c624-4d7e-b085-9d1c48097f30.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA TEKATE\",\"country_of_origin\":\"M\\u00e9xico\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(276, 'CERVEZA WEIDMANN X 500 ML', 'cerveza-weidmann-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-77DDDC39', 97, 'products/2025/09/a4364ca4-0f26-4d73-952c-c726afa434dd.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"CERVEZA WEIDMANN\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(277, 'CLUB COLOMBIA DORADA LATA X 330 ML', 'club-colombia-dorada-lata-x-330-ml', 'Cerveza Colombia en lata de 330ml. Producto importado de alta calidad.', 5000.00, NULL, 'BEER-CB461C4F', 93, 'products/2025/09/c542ce78-9b08-44a7-bc2b-3ceaf5ae497b.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"CLUB COLOMBIA\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(278, 'DAB LATA', 'dab-lata', 'Cerveza Alemania en lata de 330ml. Producto importado de alta calidad.', 8500.00, NULL, 'BEER-074636A6', 45, 'products/2025/09/a4992a6a-9e81-4839-9aa2-3ecbdfd68f40.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"DAB LATA\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(279, 'DELIRIUM ARGENTUM', 'delirium-argentum', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 30000.00, NULL, 'BEER-0F1EC7B7', 30, 'products/2025/09/d46e62ec-a137-4825-8a38-a99be7c8a9f9.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"DELIRIUM ARGENTUM\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(280, 'DELIRIUM CHRISTMAS', 'delirium-christmas', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 31000.00, NULL, 'BEER-99AD2722', 74, 'products/2025/09/52b64e4d-fba2-462e-a0db-2bf3b1cff004.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"DELIRIUM CHRISTMAS\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(281, 'DELIRIUM RED', 'delirium-red', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 33000.00, NULL, 'BEER-2642D5AC', 89, 'products/2025/09/d6dd397e-d2b1-47cf-bef3-46d1346a18af.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"DELIRIUM RED\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(282, 'DELIRIUM TREMEMS BOTELLA NR 330ML X UND', 'delirium-tremems-botella-nr-330ml-x-und', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 31000.00, NULL, 'BEER-2BDBEE6C', 85, NULL, NULL, 1, 1, 6, 1, NULL, '{\"country_of_origin\":\"B\\u00e9lgica\",\"volume_ml\":\"330\",\"packaging_type\":\"botella\",\"alcohol_content\":6,\"beer_style\":\"lager\",\"brewery\":\"DELIRIUM TREMEMS\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(283, 'EICHBAUM DUNKEL LATA', 'eichbaum-dunkel-lata', 'Cerveza Alemania en lata de 330ml. Producto importado de alta calidad.', 15000.00, NULL, 'BEER-9C774FCC', 92, 'products/2025/09/c98fe264-05c2-4c78-b875-47225bb63bc9.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"dark\",\"brewery\":\"EICHBAUM DUNKEL\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(284, 'ERDINGER ALKOHOL FREE BOT 500ML', 'erdinger-alkohol-free-bot-500ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-8D695D41', 62, 'products/2025/09/288cfba7-cbc4-4cd9-be91-35650487fc38.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"0\",\"beer_style\":\"lager\",\"brewery\":\"ERDINGER ALKOHOL\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(285, 'ERDINGER URWEISSE BOT 500', 'erdinger-urweisse-bot-500', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 22500.00, NULL, 'BEER-BDE314CA', 31, 'products/2025/09/01a310dc-67fd-428e-8898-da2be56cd144.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"ERDINGER URWEISSE\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(286, 'ERDONGER PIKANTUS BOTELLA NR 500ML X UND', 'erdonger-pikantus-botella-nr-500ml-x-und', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 24000.00, NULL, 'BEER-311F1D30', 35, 'products/2025/09/42e39f04-f2f8-4459-b201-1f2a6cc4b1a1.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"ERDONGER PIKANTUS\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(287, 'FLENSBURGER DUNKEL', 'flensburger-dunkel', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-84A243C9', 52, 'products/2025/09/4ee1a40a-55d4-474f-854e-1381ccd26a76.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"dark\",\"brewery\":\"FLENSBURGER DUNKEL\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(288, 'FLENSBURGER PILSENER', 'flensburger-pilsener', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-EAC47F01', 29, 'products/2025/09/5bb005dd-73b3-4577-8c25-624fb62c8d36.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"pilsner\",\"brewery\":\"FLENSBURGER PILSENER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:13'),
(289, 'FLENSBURGER WEIZEN', 'flensburger-weizen', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-DB50D319', 90, 'products/2025/09/f98f9299-5467-4181-a37a-8dd386393918.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"wheat\",\"brewery\":\"FLENSBURGER WEIZEN\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(290, 'FLORIS FRAMBOISE BOTELLA  NR 330ML X UND', 'floris-framboise-botella-nr-330ml-x-und', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 28500.00, NULL, 'BEER-7E0CA34D', 28, 'products/2025/09/183de702-1386-4d11-bbc4-9fdf54ad55e7.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"FLORIS FRAMBOISE\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(291, 'GERMÁN BEBER AC DC', 'german-beber-ac-dc', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 20000.00, NULL, 'BEER-362037CD', 44, 'products/2025/09/6d08d416-5e92-4745-ba1f-23a8b3f6acc2.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"GERM\\u00c1N BEBER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(292, 'GOLD INTENSE BEER', 'gold-intense-beer', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-A75FFD43', 83, 'products/2025/09/f6df202d-053a-4f77-946a-a53924bd7bb5.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"GOLD INTENSE\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(293, 'HB HOFBRAU DUNKEL', 'hb-hofbrau-dunkel', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-0ACAF1D2', 56, 'products/2025/09/434cd43b-24b9-481f-a0cb-b6a1b1b5ef3d.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"dark\",\"brewery\":\"HB HOFBRAU\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(294, 'HEINEKEN X 330 BOTELLA VIDRIO', 'heineken-x-330-botella-vidrio', 'Cerveza Países Bajos en botella de 330ml. Producto importado de alta calidad.', 5000.00, NULL, 'BEER-1B340851', 32, 'products/2025/09/02930e3a-9aca-4c37-a106-fcd313e267e1.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"HEINEKEN X\",\"country_of_origin\":\"Pa\\u00edses Bajos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(295, 'INDICA IPA COAST BOT X 355', 'indica-ipa-coast-bot-x-355', 'Cerveza Estados Unidos en botella de 355ml. Producto importado de alta calidad.', 25000.00, NULL, 'BEER-B254D74C', 43, 'products/2025/09/f5b16f63-e1e0-4ca4-b826-4dacc70d9612.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"ipa\",\"brewery\":\"INDICA IPA\",\"country_of_origin\":\"Estados Unidos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(296, 'INNIS AND GUNN ORIGINAL', 'innis-and-gunn-original', 'Cerveza Escocia en botella de 330ml. Producto importado de alta calidad.', 21000.00, NULL, 'BEER-67E6E29A', 73, 'products/2025/09/01cd9b07-8032-499e-9a11-9128c64e7f79.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"INNIS AND\",\"country_of_origin\":\"Escocia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(297, 'KARLSBRAU LAGER', 'karlsbrau-lager', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 10000.00, NULL, 'BEER-E23763CC', 13, 'products/2025/09/c2a99ae1-189c-43df-b37b-430ae7921ed7.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"KARLSBRAU LAGER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(298, 'KARLSBRAU URPILS', 'karlsbrau-urpils', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 10000.00, NULL, 'BEER-FE4E7711', 47, 'products/2025/09/b2d3cc80-8981-4302-a38f-579f423afe09.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"pilsner\",\"brewery\":\"KARLSBRAU URPILS\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(299, 'KOSTRITZER BARRILITO 5LTRS', 'kostritzer-barrilito-5ltrs', 'Cerveza Alemania en barril de 5000ml. Producto importado de alta calidad.', 160000.00, NULL, 'BEER-246D149A', 89, 'products/2025/09/2550f54d-3f57-4960-bea3-c44169832d7d.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"KOSTRITZER BARRILITO\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(300, 'KOSTRITZER LATA 500ML', 'kostritzer-lata-500ml', 'Cerveza Alemania en lata de 500ml. Producto importado de alta calidad.', 14000.00, NULL, 'BEER-5C6895A3', 65, 'products/2025/09/d5a39874-5cd8-47dd-be2f-ab48c36a9fdf.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"KOSTRITZER LATA\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(301, 'KRISTOFFEL BLOND', 'kristoffel-blond', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 21000.00, NULL, 'BEER-8484C63A', 60, 'products/2025/09/2e2f959c-532a-4867-883f-18f8f76bba86.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"blonde\",\"brewery\":\"KRISTOFFEL BLOND\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(302, 'KRISTOFFEL ROSE', 'kristoffel-rose', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 23000.00, NULL, 'BEER-7756CD11', 69, 'products/2025/09/f99215fa-a934-4026-a613-925cce21c998.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"KRISTOFFEL ROSE\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(303, 'KRISTOFFEL WHITE', 'kristoffel-white', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 21000.00, NULL, 'BEER-08105F3C', 45, 'products/2025/09/c8213197-9b8a-44c7-9457-80f5f74e9923.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"KRISTOFFEL WHITE\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(304, 'LAGER BEER SHINGHA LATA X 330ML', 'lager-beer-shingha-lata-x-330ml', 'Cerveza Tailandia en lata de 330ml. Producto importado de alta calidad.', 11500.00, NULL, 'BEER-83D312A0', 65, 'products/2025/09/dc75b40e-0085-409c-b4ec-c53d88deb01e.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"LAGER BEER\",\"country_of_origin\":\"Tailandia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(305, 'LAGER BEER SINGHA BOTELLA X330ML', 'lager-beer-singha-botella-x330ml', 'Cerveza Tailandia en botella de 330ml. Producto importado de alta calidad.', 12700.00, NULL, 'BEER-65621912', 36, 'products/2025/09/38a90d5e-7b04-4076-a2b2-8bdd025498ae.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"LAGER BEER\",\"country_of_origin\":\"Tailandia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(306, 'MILLER LITE LATA X310', 'miller-lite-lata-x310', 'Cerveza Estados Unidos en lata de 310ml. Producto importado de alta calidad.', 4000.00, NULL, 'BEER-E6B7BECF', 20, 'products/2025/09/ec3af2bf-b9d2-48dc-89ea-6dece438115e.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"MILLER LITE\",\"country_of_origin\":\"Estados Unidos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(307, 'PAULANER WEISSBIER LATA  500  ML', 'paulaner-weissbier-lata-500-ml', 'Cerveza Alemania en lata de 500ml. Producto importado de alta calidad.', 25000.00, NULL, 'BEER-A889CA50', 11, 'products/2025/09/f37dd14a-c72e-4cf1-b4d5-b9c87ceea66b.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"wheat\",\"brewery\":\"PAULANER WEISSBIER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(308, 'PILSEN LATON', 'pilsen-laton', 'Cerveza Colombia en lata de 330ml. Producto importado de alta calidad.', 5000.00, NULL, 'BEER-D681913E', 38, 'products/2025/09/f7f06a5b-ea1a-4963-a299-352ff9bf9682.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"pilsner\",\"brewery\":\"PILSEN LATON\",\"country_of_origin\":\"Colombia\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(309, 'REEPER B.WEISSBIER LATA', 'reeper-bweissbier-lata', 'Cerveza Alemania en lata de 330ml. Producto importado de alta calidad.', 18500.00, NULL, 'BEER-8AB7C414', 69, 'products/2025/09/0c9f73e2-b449-46eb-bb2a-3dd299fdaf72.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"wheat\",\"brewery\":\"REEPER B.WEISSBIER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(310, 'SAPPORO PREMIUM BEER', 'sapporo-premium-beer', 'Cerveza Japón en botella de 330ml. Producto importado de alta calidad.', 15900.00, NULL, 'BEER-40CB852A', 45, 'products/2025/09/a55dc2c6-5f6e-407f-8716-6f51bf9a6ce1.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"lager\",\"brewery\":\"SAPPORO PREMIUM\",\"country_of_origin\":\"Jap\\u00f3n\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(311, 'SCHOFFERHOFER', 'schofferhofer', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 24000.00, NULL, 'BEER-E90CF4DD', 31, 'products/2025/09/427b2aa2-3c2f-4e36-9f66-275cee8d2969.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"SCHOFFERHOFER\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(312, 'SCHOFFERHOFER TORONJA GRAPEFRUIT', 'schofferhofer-toronja-grapefruit', 'Cerveza Alemania en botella de 330ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-1DF7829F', 65, 'products/2025/09/5f79bb65-2299-456d-9a64-bd4c6a7f0b8b.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"SCHOFFERHOFER TORONJA\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(313, 'ST.IDESBALD  BOT 330', 'stidesbald-bot-330', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 22000.00, NULL, 'BEER-80C1B671', 82, 'products/2025/09/fb0650ee-3622-4fb1-b835-abbf9a7ef932.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"ST.IDESBALD\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(314, 'STRAFFE HENDRIK TRIPEL X 330', 'straffe-hendrik-tripel-x-330', 'Cerveza Bélgica en botella de 330ml. Producto importado de alta calidad.', 26000.00, NULL, 'BEER-75935199', 55, 'products/2025/09/92ce00fb-2ba3-43e7-bd45-3957ba6d7447.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"4\",\"beer_style\":\"lager\",\"brewery\":\"STRAFFE HENDRIK\",\"country_of_origin\":\"B\\u00e9lgica\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(315, 'TANGERINE LOST COAST', 'tangerine-lost-coast', 'Cerveza Estados Unidos en botella de 330ml. Producto importado de alta calidad.', 25000.00, NULL, 'BEER-4A2F0AFD', 33, 'products/2025/09/c2cff9cc-e093-45c9-bb43-bf80e62c9a98.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"lager\",\"brewery\":\"TANGERINE LOST\",\"country_of_origin\":\"Estados Unidos\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(316, 'TRES CORDILLERAS VIDRIO X300ML', 'tres-cordilleras-vidrio-x300ml', 'Cerveza Colombia en botella de 300ml. Producto importado de alta calidad.', 5000.00, NULL, 'BEER-7EF61C9B', 48, NULL, NULL, 1, 0, 6, 1, NULL, '{\"country_of_origin\":\"Colombia\",\"volume_ml\":\"300\",\"packaging_type\":\"botella\",\"alcohol_content\":5,\"beer_style\":\"lager\",\"brewery\":\"TRES CORDILLERAS\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(317, 'TROOPER FEAR OF THE DARK LATA 500 ML', 'trooper-fear-of-the-dark-lata-500-ml', 'Cerveza Inglaterra en lata de 500ml. Producto importado de alta calidad.', 19000.00, NULL, 'BEER-E2C87458', 16, NULL, NULL, 1, 0, 6, 1, NULL, '{\"country_of_origin\":\"Inglaterra\",\"volume_ml\":\"500\",\"packaging_type\":\"lata\",\"alcohol_content\":6,\"beer_style\":\"dark\",\"brewery\":\"TROOPER FEAR\"}', '2025-09-12 06:12:54', '2025-09-12 06:12:54'),
(318, 'TROOPER IPA BOTELLA 500  ML', 'trooper-ipa-botella-500-ml', 'Cerveza Inglaterra en botella de 500ml. Producto importado de alta calidad.', 27000.00, NULL, 'BEER-FD8833BC', 69, 'products/2025/09/39ff768b-be50-4778-a756-3b7ac4315e7b.png', NULL, 1, 1, 6, 1, NULL, '{\"alcohol_content\":\"6\",\"beer_style\":\"ipa\",\"brewery\":\"TROOPER IPA\",\"country_of_origin\":\"Inglaterra\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14'),
(319, 'WHEAT PALE ALE STEAM BREW X 500 ML', 'wheat-pale-ale-steam-brew-x-500-ml', 'Cerveza Alemania en botella de 500ml. Producto importado de alta calidad.', 18000.00, NULL, 'BEER-0AC28C21', 16, 'products/2025/09/a4294d41-0c10-438a-a6d1-b621a14a9c46.png', NULL, 1, 0, 6, 1, NULL, '{\"alcohol_content\":\"5\",\"beer_style\":\"pale_ale\",\"brewery\":\"WHEAT PALE\",\"country_of_origin\":\"Alemania\"}', '2025-09-12 06:12:54', '2025-09-13 21:17:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_types`
--

CREATE TABLE `product_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `fields_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fields_config`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `product_types`
--

INSERT INTO `product_types` (`id`, `name`, `slug`, `description`, `fields_config`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cervezas', 'cervezas', 'Productos de cerveza con características específicas como país de origen, tamaño, tipo de envase, etc.', '{\"country\":{\"type\":\"select\",\"label\":\"Pa\\u00eds de Origen\",\"required\":true,\"options\":{\"Colombia\":\"Colombia\",\"Alemania\":\"Alemania\",\"B\\u00e9lgica\":\"B\\u00e9lgica\",\"M\\u00e9xico\":\"M\\u00e9xico\",\"Estados Unidos\":\"Estados Unidos\",\"Reino Unido\":\"Reino Unido\",\"Rep\\u00fablica Checa\":\"Rep\\u00fablica Checa\",\"Irlanda\":\"Irlanda\",\"Pa\\u00edses Bajos\":\"Pa\\u00edses Bajos\",\"Espa\\u00f1a\":\"Espa\\u00f1a\",\"Argentina\":\"Argentina\",\"Brasil\":\"Brasil\",\"Chile\":\"Chile\",\"Per\\u00fa\":\"Per\\u00fa\",\"Ecuador\":\"Ecuador\"}},\"size_ml\":{\"type\":\"select\",\"label\":\"Tama\\u00f1o (ml)\",\"required\":true,\"options\":{\"250\":\"250 ml\",\"330\":\"330 ml\",\"355\":\"355 ml\",\"473\":\"473 ml\",\"500\":\"500 ml\",\"650\":\"650 ml\",\"750\":\"750 ml\",\"1000\":\"1000 ml\"}},\"container_type\":{\"type\":\"select\",\"label\":\"Tipo de Envase\",\"required\":true,\"options\":{\"botella\":\"Botella\",\"lata\":\"Lata\",\"barril\":\"Barril\",\"growler\":\"Growler\"}},\"alcohol_content\":{\"type\":\"number\",\"label\":\"Contenido de Alcohol (%)\",\"required\":false,\"min\":0,\"max\":100,\"step\":0.1},\"beer_style\":{\"type\":\"select\",\"label\":\"Estilo de Cerveza\",\"required\":false,\"options\":{\"lager\":\"Lager\",\"pilsner\":\"Pilsner\",\"ale\":\"Ale\",\"ipa\":\"IPA\",\"stout\":\"Stout\",\"porter\":\"Porter\",\"wheat\":\"Wheat Beer\",\"pale_ale\":\"Pale Ale\",\"amber\":\"Amber\",\"brown\":\"Brown Ale\",\"blonde\":\"Blonde\",\"dark\":\"Dark Beer\",\"light\":\"Light Beer\",\"craft\":\"Craft Beer\",\"imported\":\"Imported\"}},\"ibu\":{\"type\":\"number\",\"label\":\"IBU (International Bitterness Units)\",\"required\":false,\"min\":0,\"max\":120},\"srm\":{\"type\":\"number\",\"label\":\"SRM (Standard Reference Method)\",\"required\":false,\"min\":1,\"max\":40},\"brewery\":{\"type\":\"text\",\"label\":\"Cervecer\\u00eda\",\"required\":false,\"maxlength\":255},\"ingredients\":{\"type\":\"textarea\",\"label\":\"Ingredientes\",\"required\":false,\"rows\":3},\"tasting_notes\":{\"type\":\"textarea\",\"label\":\"Notas de Cata\",\"required\":false,\"rows\":4}}', 1, '2025-09-12 05:01:35', '2025-09-12 05:01:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('VbMntomyWR4l3tAGq7BNA3GBFqpLOr84xDCCFNn7', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQzVUUkVmRlF3NHZtRWxnVko1R0NVUTRLMHdFejJYeUZvM2lmRVBabiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9wcm9kdWN0cy9jcmVhdGUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1757806115);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('customer','admin','super_admin') NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_wholesaler` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `is_active`, `is_wholesaler`, `phone`, `country`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', 'customer', 1, 0, NULL, NULL, '2025-09-12 05:01:35', '$2y$12$FILFIINBqZSSqu0QD.dl6uKQkmKlVGn27dmB.a6qtlCJ3l53SmvO.', 'TQQPymFCd0', '2025-09-12 05:01:35', '2025-09-12 05:01:35'),
(7, 'Super Admin', 'admin@marketclub.com', 'super_admin', 1, 0, '+573001234567', 'Colombia', NULL, '$2y$12$cxlOjbPipijuQTZqEGFfd.Gqa6avAi4vFTypR81PF6gvbZqOmwJSa', NULL, '2025-09-12 18:55:58', '2025-09-12 18:55:58'),
(8, 'Admin', 'admin2@marketclub.com', 'admin', 1, 0, '+573001234568', 'Colombia', NULL, '$2y$12$bYSIFAjCXXAJfuqHP7yqAOaBB0M9kzb/1J7z8attM.2omFLEZzefC', NULL, '2025-09-12 18:55:58', '2025-09-12 18:55:58'),
(9, 'Juan Pérez', 'juan@example.com', 'customer', 1, 0, '+573001234569', 'Colombia', NULL, '$2y$12$00XJ.W0tNekt1TshZar6uO0x5TkL4OkEWEufdrcI61CTbLyEufSGS', NULL, '2025-09-12 18:55:59', '2025-09-12 18:55:59'),
(10, 'María García', 'maria@example.com', 'customer', 1, 0, '+573001234570', 'Colombia', NULL, '$2y$12$RcUDJAcXF/L2zq4EtctodeTOrZF1.iZ.3oqyEEwuw2bWRrjOWlnUa', NULL, '2025-09-12 18:55:59', '2025-09-12 18:55:59'),
(11, 'Carlos López', 'carlos@example.com', 'customer', 1, 0, '+573001234571', 'Colombia', NULL, '$2y$12$dARUwdRWcLG3KcHBNe4T4ec.yt/jRSDBCc7Fb9cEjL102WifumS/.', NULL, '2025-09-12 18:55:59', '2025-09-12 18:55:59'),
(15, 'Cliente Mayorista', 'mayorista@test.com', 'customer', 1, 1, '+573001234999', 'Colombia', NULL, '$2y$12$Ibc1K76vgc81PZImalRM5uhanWBmWdnpBh5KXeiX/YyQ0si8FK5J6', NULL, '2025-09-12 19:35:55', '2025-09-12 19:35:55'),
(17, 'bastian alzate', 'bastianalzate@gmail.com', 'customer', 1, 1, '3043345434', 'Colombia', NULL, '$2y$12$ZP2DDOK9NhlU1ryq7IxaTeIbcyX/VpFD.DtI1yRQBseFoiqIA4BO2', NULL, '2025-09-12 19:40:29', '2025-09-12 19:40:29'),
(18, 'bastian 2', 'bastianalzate2@gmail.com', 'customer', 1, 1, '3043345434', 'Colombia', NULL, '$2y$12$EI3RgtUBgIk.5AV4m4bRw.30TFTQZ3FOCN8k9/vfEhYTHSgmnN1kq', NULL, '2025-09-12 19:50:10', '2025-09-12 19:50:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `carts_user_id_session_id_unique` (`user_id`,`session_id`),
  ADD KEY `carts_user_id_session_id_index` (`user_id`,`session_id`);

--
-- Indices de la tabla `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_cart_id_product_id_unique` (`cart_id`,`product_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`),
  ADD KEY `cart_items_cart_id_product_id_index` (`cart_id`,`product_id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_transactions_wompi_transaction_id_unique` (`wompi_transaction_id`),
  ADD UNIQUE KEY `payment_transactions_reference_unique` (`reference`),
  ADD KEY `payment_transactions_order_id_status_index` (`order_id`,`status`),
  ADD KEY `payment_transactions_wompi_transaction_id_index` (`wompi_transaction_id`),
  ADD KEY `payment_transactions_reference_index` (`reference`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_product_type_id_foreign` (`product_type_id`);

--
-- Indices de la tabla `product_types`
--
ALTER TABLE `product_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_types_slug_unique` (`slug`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`),
  ADD KEY `wishlists_user_id_created_at_index` (`user_id`,`created_at`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=323;

--
-- AUTO_INCREMENT de la tabla `product_types`
--
ALTER TABLE `product_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_product_type_id_foreign` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
