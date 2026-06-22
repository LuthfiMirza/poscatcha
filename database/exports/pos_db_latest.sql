-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: 127.0.0.1    Database: pos_db
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `pos_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `pos_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `pos_db`;

--
-- Table structure for table `admin_chatbot_logs`
--

DROP TABLE IF EXISTS `admin_chatbot_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_chatbot_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(120) DEFAULT NULL,
  `question` varchar(500) NOT NULL,
  `normalized_question` varchar(500) DEFAULT NULL,
  `intent` varchar(80) NOT NULL,
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameters`)),
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `response_summary` text DEFAULT NULL,
  `response_meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_meta`)),
  `context_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context_snapshot`)),
  `latency_ms` int(10) unsigned NOT NULL DEFAULT 0,
  `feedback` varchar(20) DEFAULT NULL,
  `feedback_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_chatbot_logs_user_id_foreign` (`user_id`),
  KEY `admin_chatbot_logs_session_id_index` (`session_id`),
  KEY `admin_chatbot_logs_intent_index` (`intent`),
  KEY `admin_chatbot_logs_success_index` (`success`),
  KEY `admin_chatbot_logs_feedback_index` (`feedback`),
  CONSTRAINT `admin_chatbot_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_chatbot_logs`
--

LOCK TABLES `admin_chatbot_logs` WRITE;
/*!40000 ALTER TABLE `admin_chatbot_logs` DISABLE KEYS */;
INSERT INTO `admin_chatbot_logs` VALUES (1,21,'3VtL23269LBsTy3Bsii47iE1witNP9a9FPnIl45M','Ringkasan penjualan minggu ini','ringkasan penjualan minggu ini','ringkasan_penjualan','{\"period\":\"current_week\"}',0,'Belum ada transaksi penjualan untuk periode yang diminta.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"}]}','{\"last_intent\":\"ringkasan_penjualan\",\"last_period\":\"current_week\"}',6,NULL,NULL,'2026-06-22 07:48:29','2026-06-22 07:48:29'),(2,21,'3VtL23269LBsTy3Bsii47iE1witNP9a9FPnIl45M','Produk terlaris bulan ini','produk terlaris bulan ini','produk_terlaris','{\"period\":\"current_month\"}',0,'Belum ada data penjualan untuk periode yang diminta.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"}]}','{\"last_intent\":\"produk_terlaris\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-06-22 07:48:32','2026-06-22 07:48:32'),(3,21,'3VtL23269LBsTy3Bsii47iE1witNP9a9FPnIl45M','Produk terlaris bulan ini','produk terlaris bulan ini','produk_terlaris','{\"period\":\"current_month\"}',0,'Belum ada data penjualan untuk periode yang diminta.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"}]}','{\"last_intent\":\"produk_terlaris\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-06-22 07:48:33','2026-06-22 07:48:33'),(4,21,'3VtL23269LBsTy3Bsii47iE1witNP9a9FPnIl45M','cek stok syrup','cek stok syrup','cek_stok_produk','{\"product_id\":null,\"product_query\":\"syrup\",\"period\":\"all_time\"}',1,'Stok Agave Syrup (A002) saat ini 0 unit. Harga jual Rp3.000, profit Rp3.000, expired 22 Jun 2027.','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"},{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\",\"last_product_id\":\"A002\",\"last_product_name\":\"Agave Syrup\"}',6,NULL,NULL,'2026-06-22 07:48:46','2026-06-22 07:48:46');
/*!40000 ALTER TABLE `admin_chatbot_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cashier_id` varchar(20) NOT NULL,
  `product_id` varchar(5) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_profit` int(11) NOT NULL,
  `product_price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `sub_total` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cashier_shifts`
--

DROP TABLE IF EXISTS `cashier_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cashier_shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cashier_id` bigint(20) unsigned NOT NULL,
  `shift_start` datetime NOT NULL,
  `shift_end` datetime DEFAULT NULL,
  `opening_cash` decimal(10,2) NOT NULL,
  `closing_cash` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cashier_shifts_cashier_id_foreign` (`cashier_id`),
  CONSTRAINT `cashier_shifts_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cashier_shifts`
--

LOCK TABLES `cashier_shifts` WRITE;
/*!40000 ALTER TABLE `cashier_shifts` DISABLE KEYS */;
INSERT INTO `cashier_shifts` VALUES (1,22,'2026-06-22 15:01:24',NULL,0.00,NULL,NULL,'open','2026-06-22 08:01:24','2026-06-22 08:01:24');
/*!40000 ALTER TABLE `cashier_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` varchar(6) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `added_by` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_category_id_unique` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'MATCHA','Matcha Menu','admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(2,'THAI','Thai Tea','admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(3,'ADDON','Add On','admin','2026-06-22 07:33:06','2026-06-22 07:33:06');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detail_sales`
--

DROP TABLE IF EXISTS `detail_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` varchar(30) NOT NULL,
  `cashier_id` varchar(20) NOT NULL,
  `product_id` varchar(5) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_profit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `product_price` int(11) NOT NULL,
  `buy_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `sub_total` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_sales_sale_id_foreign` (`sale_id`),
  CONSTRAINT `detail_sales_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_sales`
--

LOCK TABLES `detail_sales` WRITE;
/*!40000 ALTER TABLE `detail_sales` DISABLE KEYS */;
INSERT INTO `detail_sales` VALUES (1,'INV-20260622-0001','22','T004','Siamese Sunset Party Size 2000 ml',105600.00,152000,46400.00,1,152000,'2026-06-22 09:25:44','2026-06-22 09:25:44'),(2,'INV-20260623-0001','22','T001','Siamese Sunset Normal Bottle 250 ml',132000.00,19000,5800.00,10,190000,'2026-06-22 17:02:20','2026-06-22 17:02:20');
/*!40000 ALTER TABLE `detail_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_02_02_162757_create_permission_tables',1),(5,'2025_03_08_085212_create_categories_table',1),(6,'2025_03_08_092922_create_products_table',1),(7,'2025_03_19_153912_create_stock_movements_table',1),(8,'2025_04_05_155201_create_carts_table',1),(9,'2025_04_20_004345_create_sales_table',1),(10,'2025_04_20_004735_create_detail_sales_table',1),(11,'2026_04_23_000001_create_suppliers_table',1),(12,'2026_04_23_000002_create_purchases_table',1),(13,'2026_04_23_000003_create_purchase_items_table',1),(14,'2026_04_23_000004_add_buy_price_to_products_and_source_to_stock_movements_table',1),(15,'2026_04_23_000005_update_buy_price_and_profit_columns_for_reports',1),(16,'2026_04_23_000006_create_cashier_shifts_table',1),(17,'2026_04_23_000007_add_shift_id_to_sales_table',1),(18,'2026_04_27_000001_create_admin_chatbot_logs_table',1),(19,'2026_06_22_000001_create_raw_materials_tables',1),(20,'2026_06_22_000002_convert_purchase_items_to_raw_materials',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',22),(1,'App\\Models\\User',23),(2,'App\\Models\\User',21);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'cashier-dashboard','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(2,'sell-products','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(3,'delete-selled-products','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(4,'add-products','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(5,'edit-products','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(6,'delete-products','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(7,'view-products','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(8,'admin-dashboard','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(9,'create-cashiers','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(10,'edit-cashiers','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(11,'delete-cashiers','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(12,'view-cashiers','web','2026-06-22 07:46:09','2026-06-22 07:46:09');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_recipes`
--

DROP TABLE IF EXISTS `product_recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_recipes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) NOT NULL,
  `raw_material_id` bigint(20) unsigned NOT NULL,
  `quantity_required` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_recipes_product_id_raw_material_id_unique` (`product_id`,`raw_material_id`),
  KEY `product_recipes_raw_material_id_foreign` (`raw_material_id`),
  CONSTRAINT `product_recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `product_recipes_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_recipes`
--

LOCK TABLES `product_recipes` WRITE;
/*!40000 ALTER TABLE `product_recipes` DISABLE KEYS */;
INSERT INTO `product_recipes` VALUES (1,'M001',1,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(2,'M001',3,40.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(3,'M001',4,180.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(4,'M001',10,10.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(5,'M001',17,120.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(6,'M001',12,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(7,'M002',1,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(8,'M002',3,60.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(9,'M002',4,180.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(10,'M002',10,10.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(11,'M002',12,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(12,'M003',1,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(13,'M003',3,40.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(14,'M003',5,180.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(15,'M003',10,10.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(16,'M003',17,120.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(17,'M003',12,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(18,'M004',1,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(19,'M004',3,60.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(20,'M004',5,180.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(21,'M004',10,10.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(22,'M004',12,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(23,'M005',1,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(24,'M005',3,40.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(25,'M005',18,180.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(26,'M005',10,10.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(27,'M005',17,120.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(28,'M005',12,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(29,'M006',1,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(30,'M006',3,60.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(31,'M006',18,180.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(32,'M006',10,10.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(33,'M006',12,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(34,'M007',1,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(35,'M007',3,40.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(36,'M007',5,160.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(37,'M007',19,30.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(38,'M007',10,5.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(39,'M007',17,120.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(40,'M007',12,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(41,'T001',2,6.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(42,'T001',3,160.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(43,'T001',6,25.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(44,'T001',7,40.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(45,'T001',8,8.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(46,'T001',9,10.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(47,'T001',13,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(48,'T002',2,12.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(49,'T002',3,320.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(50,'T002',6,50.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(51,'T002',7,80.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(52,'T002',8,16.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(53,'T002',9,20.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(54,'T002',14,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(55,'T003',2,24.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(56,'T003',3,640.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(57,'T003',6,100.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(58,'T003',7,160.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(59,'T003',8,32.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(60,'T003',9,40.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(61,'T003',15,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(62,'T004',2,48.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(63,'T004',3,1280.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(64,'T004',6,200.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(65,'T004',7,320.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(66,'T004',8,64.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(67,'T004',9,80.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(68,'T004',16,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(69,'A001',13,1.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(70,'A002',11,15.00,'2026-06-22 09:20:06','2026-06-22 09:20:06');
/*!40000 ALTER TABLE `product_recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_category` varchar(6) NOT NULL,
  `product_image` varchar(35) NOT NULL,
  `product_price` int(11) NOT NULL,
  `buy_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `product_profit` int(11) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_expired` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_product_id_unique` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'M001','Ragdoll Bliss Oat Milk Cold Whisk','MATCHA','m001.jpeg',41000,17500.00,23500,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(2,'M002','Ragdoll Bliss Oat Milk Hot Whisk','MATCHA','m002.jpeg',36000,16000.00,20000,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(3,'M003','Ragdoll Bliss Signature Milk Cold Whisk','MATCHA','m003.jpeg',36000,13000.00,23000,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(4,'M004','Ragdoll Bliss Signature Milk Hot Whisk','MATCHA','m004.jpeg',32000,12200.00,19800,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(5,'M005','Calico Swirl Coconut Matcha Cold Whisk','MATCHA','m005.jpeg',40000,15500.00,24500,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(6,'M006','Calico Swirl Coconut Matcha Hot Whisk','MATCHA','m006.jpeg',36000,14500.00,21500,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(7,'M007','Ragdoll Blush Strawberry Matcha Cold Whisk','MATCHA','m007.jpeg',42000,16500.00,25500,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(8,'T001','Siamese Sunset Normal Bottle 250 ml','THAI','t001.jpeg',19000,5800.00,13200,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(9,'T002','Siamese Sunset Chongky Bottle 500 ml','THAI','t002.jpeg',38000,11600.00,26400,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(10,'T003','Siamese Sunset MegaPaw Bottle 1000 ml','THAI','t003.jpeg',76000,23200.00,52800,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(11,'T004','Siamese Sunset Party Size 2000 ml','THAI','t004.jpeg',152000,46400.00,105600,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(12,'A001','Add On Bottled','ADDON','a001.jpeg',2000,1000.00,1000,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57'),(13,'A002','Agave Syrup','ADDON','a002.jpeg',3000,3000.00,0,0,'2027-06-22','2026-06-22 07:33:06','2026-06-22 09:15:57');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `raw_material_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` varchar(5) DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `buy_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_raw_material_id_foreign` (`raw_material_id`),
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES (1,1,15,NULL,10.00,40.00,'2026-06-22 09:40:44','2026-06-22 09:40:44');
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_number` varchar(30) NOT NULL,
  `supplier_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_purchase_number_unique` (`purchase_number`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_created_by_foreign` (`created_by`),
  CONSTRAINT `purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,'PO-20260622-0001',NULL,'Tatang Gelas','2026-06-22','INV123410',NULL,21,'2026-06-22 09:40:44','2026-06-22 09:40:44');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raw_material_stock_movements`
--

DROP TABLE IF EXISTS `raw_material_stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raw_material_stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `raw_material_id` bigint(20) unsigned NOT NULL,
  `transaction_id` varchar(35) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `reason` varchar(80) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `quantity_before` decimal(12,2) NOT NULL,
  `quantity_after` decimal(12,2) NOT NULL,
  `action_by` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `raw_material_stock_movements_raw_material_id_foreign` (`raw_material_id`),
  CONSTRAINT `raw_material_stock_movements_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raw_material_stock_movements`
--

LOCK TABLES `raw_material_stock_movements` WRITE;
/*!40000 ALTER TABLE `raw_material_stock_movements` DISABLE KEYS */;
INSERT INTO `raw_material_stock_movements` VALUES (1,1,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(2,2,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(3,3,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(4,4,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(5,5,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(6,6,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(7,7,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(8,8,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(9,9,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(10,10,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(11,11,NULL,'in','Initial Menu Setup',0.00,0.00,0.00,'admin','2026-06-22 07:33:06','2026-06-22 07:33:06'),(12,1,NULL,'in','Stok awal contoh',1000.00,0.00,1000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(13,2,NULL,'in','Stok awal contoh',1000.00,0.00,1000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(14,3,NULL,'in','Stok awal contoh',50000.00,0.00,50000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(15,4,NULL,'in','Stok awal contoh',10000.00,0.00,10000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(16,5,NULL,'in','Stok awal contoh',15000.00,0.00,15000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(17,6,NULL,'in','Stok awal contoh',5000.00,0.00,5000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(18,7,NULL,'in','Stok awal contoh',8000.00,0.00,8000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(19,8,NULL,'in','Stok awal contoh',3000.00,0.00,3000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(20,9,NULL,'in','Stok awal contoh',5000.00,0.00,5000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(21,10,NULL,'in','Stok awal contoh',2000.00,0.00,2000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(22,11,NULL,'in','Stok awal contoh',1500.00,0.00,1500.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(23,12,NULL,'in','Stok awal contoh',300.00,0.00,300.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(24,13,NULL,'in','Stok awal contoh',120.00,0.00,120.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(25,14,NULL,'in','Stok awal contoh',80.00,0.00,80.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(26,15,NULL,'in','Stok awal contoh',40.00,0.00,40.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(27,16,NULL,'in','Stok awal contoh',20.00,0.00,20.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(28,17,NULL,'in','Stok awal contoh',20000.00,0.00,20000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(29,18,NULL,'in','Stok awal contoh',6000.00,0.00,6000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(30,19,NULL,'in','Stok awal contoh',3000.00,0.00,3000.00,'admin','2026-06-22 09:20:06','2026-06-22 09:20:06'),(31,2,'INV-20260622-0001','out','Product Sales - Siamese Sunset Party Size 2000 ml',48.00,1000.00,952.00,'ariz','2026-06-22 09:25:44','2026-06-22 09:25:44'),(32,3,'INV-20260622-0001','out','Product Sales - Siamese Sunset Party Size 2000 ml',1280.00,50000.00,48720.00,'ariz','2026-06-22 09:25:44','2026-06-22 09:25:44'),(33,6,'INV-20260622-0001','out','Product Sales - Siamese Sunset Party Size 2000 ml',200.00,5000.00,4800.00,'ariz','2026-06-22 09:25:44','2026-06-22 09:25:44'),(34,7,'INV-20260622-0001','out','Product Sales - Siamese Sunset Party Size 2000 ml',320.00,8000.00,7680.00,'ariz','2026-06-22 09:25:44','2026-06-22 09:25:44'),(35,8,'INV-20260622-0001','out','Product Sales - Siamese Sunset Party Size 2000 ml',64.00,3000.00,2936.00,'ariz','2026-06-22 09:25:44','2026-06-22 09:25:44'),(36,9,'INV-20260622-0001','out','Product Sales - Siamese Sunset Party Size 2000 ml',80.00,5000.00,4920.00,'ariz','2026-06-22 09:25:44','2026-06-22 09:25:44'),(37,16,'INV-20260622-0001','out','Product Sales - Siamese Sunset Party Size 2000 ml',1.00,20.00,19.00,'ariz','2026-06-22 09:25:44','2026-06-22 09:25:44'),(38,15,'PO-20260622-0001','in','Restock Purchase',10.00,40.00,50.00,'admin','2026-06-22 09:40:44','2026-06-22 09:40:44'),(39,2,'INV-20260623-0001','out','Product Sales - Siamese Sunset Normal Bottle 250 ml',60.00,952.00,892.00,'ariz','2026-06-22 17:02:20','2026-06-22 17:02:20'),(40,3,'INV-20260623-0001','out','Product Sales - Siamese Sunset Normal Bottle 250 ml',1600.00,48720.00,47120.00,'ariz','2026-06-22 17:02:20','2026-06-22 17:02:20'),(41,6,'INV-20260623-0001','out','Product Sales - Siamese Sunset Normal Bottle 250 ml',250.00,4800.00,4550.00,'ariz','2026-06-22 17:02:20','2026-06-22 17:02:20'),(42,7,'INV-20260623-0001','out','Product Sales - Siamese Sunset Normal Bottle 250 ml',400.00,7680.00,7280.00,'ariz','2026-06-22 17:02:20','2026-06-22 17:02:20'),(43,8,'INV-20260623-0001','out','Product Sales - Siamese Sunset Normal Bottle 250 ml',80.00,2936.00,2856.00,'ariz','2026-06-22 17:02:20','2026-06-22 17:02:20'),(44,9,'INV-20260623-0001','out','Product Sales - Siamese Sunset Normal Bottle 250 ml',100.00,4920.00,4820.00,'ariz','2026-06-22 17:02:20','2026-06-22 17:02:20'),(45,13,'INV-20260623-0001','out','Product Sales - Siamese Sunset Normal Bottle 250 ml',10.00,120.00,110.00,'ariz','2026-06-22 17:02:20','2026-06-22 17:02:20');
/*!40000 ALTER TABLE `raw_material_stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raw_materials`
--

DROP TABLE IF EXISTS `raw_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raw_materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `minimum_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `raw_materials_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raw_materials`
--

LOCK TABLES `raw_materials` WRITE;
/*!40000 ALTER TABLE `raw_materials` DISABLE KEYS */;
INSERT INTO `raw_materials` VALUES (1,'Bubuk Matcha','gram',1000.00,150.00,'2026-06-22 07:33:06','2026-06-22 09:20:06'),(2,'Bubuk Thai Tea','gram',892.00,150.00,'2026-06-22 07:33:06','2026-06-22 17:02:20'),(3,'Air','ml',47120.00,5000.00,'2026-06-22 07:33:06','2026-06-22 17:02:20'),(4,'Susu Oat (Oat Milk)','ml',10000.00,2000.00,'2026-06-22 07:33:06','2026-06-22 09:20:06'),(5,'Susu Fresh Milk','ml',15000.00,3000.00,'2026-06-22 07:33:06','2026-06-22 09:20:06'),(6,'Susu Kental Manis (SKM)','ml',4550.00,1000.00,'2026-06-22 07:33:06','2026-06-22 17:02:20'),(7,'Susu Evaporasi','ml',7280.00,1500.00,'2026-06-22 07:33:06','2026-06-22 17:02:20'),(8,'Creamer','gram',2856.00,500.00,'2026-06-22 07:33:06','2026-06-22 17:02:20'),(9,'Gula Pasir','gram',4820.00,1000.00,'2026-06-22 07:33:06','2026-06-22 17:02:20'),(10,'Vanilla Syrup','ml',2000.00,300.00,'2026-06-22 07:33:06','2026-06-22 09:20:06'),(11,'Agave Syrup','ml',1500.00,250.00,'2026-06-22 07:33:06','2026-06-22 09:20:06'),(12,'Cup','pcs',300.00,50.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(13,'Botol 250 ml','pcs',110.00,30.00,'2026-06-22 09:20:06','2026-06-22 17:02:20'),(14,'Botol 500 ml','pcs',80.00,20.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(15,'Botol 1000 ml','pcs',50.00,10.00,'2026-06-22 09:20:06','2026-06-22 09:40:44'),(16,'Botol 2000 ml','pcs',19.00,5.00,'2026-06-22 09:20:06','2026-06-22 09:25:44'),(17,'Es Batu','gram',20000.00,3000.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(18,'Coconut Milk','ml',6000.00,1000.00,'2026-06-22 09:20:06','2026-06-22 09:20:06'),(19,'Strawberry Syrup','ml',3000.00,500.00,'2026-06-22 09:20:06','2026-06-22 09:20:06');
/*!40000 ALTER TABLE `raw_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,2),(9,2),(10,2),(11,2),(12,2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'cashier','web','2026-06-22 07:46:09','2026-06-22 07:46:09'),(2,'admin','web','2026-06-22 07:46:09','2026-06-22 07:46:09');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` varchar(35) NOT NULL,
  `shift_id` bigint(20) unsigned DEFAULT NULL,
  `cashier_id` varchar(20) NOT NULL,
  `total` int(11) NOT NULL,
  `payment_method` varchar(1) NOT NULL,
  `pay` int(11) NOT NULL,
  `change` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_sale_id_unique` (`sale_id`),
  KEY `sales_shift_id_foreign` (`shift_id`),
  CONSTRAINT `sales_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `cashier_shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (4,'INV-20260622-0001',1,'22',152000,'3',152000,0,'2026-06-22 09:25:44','2026-06-22 09:25:44'),(5,'INV-20260623-0001',1,'22',190000,'3',190000,0,'2026-06-22 17:02:20','2026-06-22 17:02:20');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('34yvy1reqc5qinXiCocVh0fS0Qrbbarzbf5wPg51',22,'127.0.0.1','Symfony','YTo0OntzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyMjtzOjY6Il90b2tlbiI7czo0MDoidUlTOFNtM1puTEVxN3h2eHA1ZnIzVFFKR1ZPUDJZQ1pmRFdDSkE0ZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly9sb2NhbGhvc3Qvc2VsbGluZ19wcm9kdWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1782147475),('3VtL23269LBsTy3Bsii47iE1witNP9a9FPnIl45M',21,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiQ1Y2bm9QRXBDWVlaYk1Kc2NuZTI4RVA5U1BwMXlqQ2ZxZDZaUFNjZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZF9hZG1pbiI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjY2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vcmVwb3J0cy9wcm9maXQvZXhwb3J0L3BkZj9zb3J0PWhpZ2hlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyMTtzOjIyOiJhZG1pbl9jaGF0Ym90X21lc3NhZ2VzIjthOjk6e2k6MDthOjU6e3M6NDoicm9sZSI7czo5OiJhc3Npc3RhbnQiO3M6NDoidGV4dCI7czoxMzA6IkhhbG8hIFNheWEgYmlzYSBiYW50dSBjZWsgc3RvaywgcGVuanVhbGFuLCBwcm9maXQsIHNoaWZ0LCBkYW4gYW5hbGlzaXMgcGVyYmFuZGluZ2FuLiBTZW11YSBqYXdhYmFuIGRpYW1iaWwgbGFuZ3N1bmcgZGFyaSBkYXRhYmFzZS4iO3M6NDoidGltZSI7czo1OiIxNDo0NyI7czo0OiJtZXRhIjthOjI6e3M6NjoiaW50ZW50IjtzOjE1OiJiYW50dWFuX2NoYXRib3QiO3M6Nzoic3VjY2VzcyI7YjoxO31zOjc6ImFjdGlvbnMiO2E6Mjp7aTowO2E6Mjp7czo1OiJsYWJlbCI7czoxMjoiTGloYXQgUHJvZHVrIjtzOjM6InVybCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3Byb2R1Y3RzIjt9aToxO2E6Mjp7czo1OiJsYWJlbCI7czoxNToiTGloYXQgUGVuanVhbGFuIjtzOjM6InVybCI7czozMjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3NhbGVzX2RhdGEiO319fWk6MTthOjM6e3M6NDoicm9sZSI7czo0OiJ1c2VyIjtzOjQ6InRleHQiO3M6MzA6IlJpbmdrYXNhbiBwZW5qdWFsYW4gbWluZ2d1IGluaSI7czo0OiJ0aW1lIjtzOjU6IjE0OjQ4Ijt9aToyO2E6Nzp7czo0OiJyb2xlIjtzOjk6ImFzc2lzdGFudCI7czo0OiJ0ZXh0IjtzOjU3OiJCZWx1bSBhZGEgdHJhbnNha3NpIHBlbmp1YWxhbiB1bnR1ayBwZXJpb2RlIHlhbmcgZGltaW50YS4iO3M6NDoidGltZSI7czo1OiIxNDo0OCI7czo0OiJtZXRhIjthOjU6e3M6NjoiaW50ZW50IjtzOjE5OiJyaW5na2FzYW5fcGVuanVhbGFuIjtzOjc6InN1Y2Nlc3MiO2I6MDtzOjEwOiJsYXRlbmN5X21zIjtpOjY7czoxMzoiaW5zaWdodF9sYWJlbCI7czoxMzoiSW5zaWdodCBVdGFtYSI7czoxMjoiaW5zaWdodF90aWVyIjtzOjc6InByaW1hcnkiO31zOjc6ImFjdGlvbnMiO2E6MTp7aTowO2E6Mjp7czo1OiJsYWJlbCI7czoxNToiTGloYXQgUGVuanVhbGFuIjtzOjM6InVybCI7czozMjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3NhbGVzX2RhdGEiO319czo2OiJsb2dfaWQiO2k6MTtzOjg6ImZlZWRiYWNrIjtOO31pOjM7YTozOntzOjQ6InJvbGUiO3M6NDoidXNlciI7czo0OiJ0ZXh0IjtzOjI1OiJQcm9kdWsgdGVybGFyaXMgYnVsYW4gaW5pIjtzOjQ6InRpbWUiO3M6NToiMTQ6NDgiO31pOjQ7YTo3OntzOjQ6InJvbGUiO3M6OToiYXNzaXN0YW50IjtzOjQ6InRleHQiO3M6NTI6IkJlbHVtIGFkYSBkYXRhIHBlbmp1YWxhbiB1bnR1ayBwZXJpb2RlIHlhbmcgZGltaW50YS4iO3M6NDoidGltZSI7czo1OiIxNDo0OCI7czo0OiJtZXRhIjthOjU6e3M6NjoiaW50ZW50IjtzOjE1OiJwcm9kdWtfdGVybGFyaXMiO3M6Nzoic3VjY2VzcyI7YjowO3M6MTA6ImxhdGVuY3lfbXMiO2k6MjtzOjEzOiJpbnNpZ2h0X2xhYmVsIjtzOjEzOiJJbnNpZ2h0IFV0YW1hIjtzOjEyOiJpbnNpZ2h0X3RpZXIiO3M6NzoicHJpbWFyeSI7fXM6NzoiYWN0aW9ucyI7YToxOntpOjA7YToyOntzOjU6ImxhYmVsIjtzOjE1OiJMaWhhdCBQZW5qdWFsYW4iO3M6MzoidXJsIjtzOjMyOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2FsZXNfZGF0YSI7fX1zOjY6ImxvZ19pZCI7aToyO3M6ODoiZmVlZGJhY2siO047fWk6NTthOjM6e3M6NDoicm9sZSI7czo0OiJ1c2VyIjtzOjQ6InRleHQiO3M6MjU6IlByb2R1ayB0ZXJsYXJpcyBidWxhbiBpbmkiO3M6NDoidGltZSI7czo1OiIxNDo0OCI7fWk6NjthOjc6e3M6NDoicm9sZSI7czo5OiJhc3Npc3RhbnQiO3M6NDoidGV4dCI7czo1MjoiQmVsdW0gYWRhIGRhdGEgcGVuanVhbGFuIHVudHVrIHBlcmlvZGUgeWFuZyBkaW1pbnRhLiI7czo0OiJ0aW1lIjtzOjU6IjE0OjQ4IjtzOjQ6Im1ldGEiO2E6NTp7czo2OiJpbnRlbnQiO3M6MTU6InByb2R1a190ZXJsYXJpcyI7czo3OiJzdWNjZXNzIjtiOjA7czoxMDoibGF0ZW5jeV9tcyI7aToyO3M6MTM6Imluc2lnaHRfbGFiZWwiO3M6MTM6Ikluc2lnaHQgVXRhbWEiO3M6MTI6Imluc2lnaHRfdGllciI7czo3OiJwcmltYXJ5Ijt9czo3OiJhY3Rpb25zIjthOjE6e2k6MDthOjI6e3M6NToibGFiZWwiO3M6MTU6IkxpaGF0IFBlbmp1YWxhbiI7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zYWxlc19kYXRhIjt9fXM6NjoibG9nX2lkIjtpOjM7czo4OiJmZWVkYmFjayI7Tjt9aTo3O2E6Mzp7czo0OiJyb2xlIjtzOjQ6InVzZXIiO3M6NDoidGV4dCI7czoxNDoiY2VrIHN0b2sgc3lydXAiO3M6NDoidGltZSI7czo1OiIxNDo0OCI7fWk6ODthOjc6e3M6NDoicm9sZSI7czo5OiJhc3Npc3RhbnQiO3M6NDoidGV4dCI7czo5NzoiU3RvayBBZ2F2ZSBTeXJ1cCAoQTAwMikgc2FhdCBpbmkgMCB1bml0LiBIYXJnYSBqdWFsIFJwMy4wMDAsIHByb2ZpdCBScDMuMDAwLCBleHBpcmVkIDIyIEp1biAyMDI3LiI7czo0OiJ0aW1lIjtzOjU6IjE0OjQ4IjtzOjQ6Im1ldGEiO2E6NTp7czo2OiJpbnRlbnQiO3M6MTU6ImNla19zdG9rX3Byb2R1ayI7czo3OiJzdWNjZXNzIjtiOjE7czoxMDoibGF0ZW5jeV9tcyI7aTo2O3M6MTM6Imluc2lnaHRfbGFiZWwiO047czoxMjoiaW5zaWdodF90aWVyIjtOO31zOjc6ImFjdGlvbnMiO2E6Mzp7aTowO2E6Mjp7czo1OiJsYWJlbCI7czoxMjoiTGloYXQgUHJvZHVrIjtzOjM6InVybCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3Byb2R1Y3RzIjt9aToxO2E6Mjp7czo1OiJsYWJlbCI7czoyMDoiTGloYXQgU3RvY2sgTW92ZW1lbnQiO3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc3RvY2tfbW92ZW1lbnQiO31pOjI7YToyOntzOjU6ImxhYmVsIjtzOjc6IlJlc3RvY2siO3M6MzoidXJsIjtzOjM4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcHVyY2hhc2VzL2NyZWF0ZSI7fX1zOjY6ImxvZ19pZCI7aTo0O3M6ODoiZmVlZGJhY2siO047fX1zOjIxOiJhZG1pbl9jaGF0Ym90X2NvbnRleHQiO2E6NDp7czoxMToibGFzdF9pbnRlbnQiO3M6MTU6ImNla19zdG9rX3Byb2R1ayI7czoxMToibGFzdF9wZXJpb2QiO3M6ODoiYWxsX3RpbWUiO3M6MTU6Imxhc3RfcHJvZHVjdF9pZCI7czo0OiJBMDAyIjtzOjE3OiJsYXN0X3Byb2R1Y3RfbmFtZSI7czoxMToiQWdhdmUgU3lydXAiO319',1782148186),('XTEwXjbqIl3uge75udPBbVGkfCDouqOQyfImcqod',22,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoidmlyQ0I0RmVVdFdWeklnREZiZGV1Y3RhZ1NqZ0pIS2s3QmtRVlVIOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcmludC1yZWNlaXB0L0lOVi0yMDI2MDYyMy0wMDAxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjI7fQ==',1782147740);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(5) DEFAULT NULL,
  `transaction_id` varchar(35) DEFAULT NULL,
  `product_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `source` varchar(30) NOT NULL DEFAULT 'product',
  `reason` varchar(20) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `action_by` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_id_foreign` (`product_id`),
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(70) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (21,'admin','admin@gmail.com','2026-06-22 07:46:10','$2y$12$CcKQssVmpMo6IgZ.m3xOTOOT9sTiJ8dMMb0HpPsbai8hq.mqEpBlW',NULL,'2026-06-22 07:46:10','2026-06-22 07:46:10'),(22,'ariz','ariz@gmail.com','2026-06-22 07:46:10','$2y$12$fqMh7ib56iXz7Sk0XYPT1ugThuiAehJ8vpDDFVI03EuCMOxzVTXCm',NULL,'2026-06-22 07:46:10','2026-06-22 07:46:10'),(23,'koko','koko@gmail.com','2026-06-22 07:46:10','$2y$12$iPkN9UmhuwE/8lOuoob0c.Wh55E4/aQsfjU3jdIpaGZZdJCxBhdkW',NULL,'2026-06-22 07:46:10','2026-06-22 07:46:10');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-23  0:12:29
