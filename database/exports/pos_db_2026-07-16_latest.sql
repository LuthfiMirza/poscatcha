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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_chatbot_logs`
--

LOCK TABLES `admin_chatbot_logs` WRITE;
/*!40000 ALTER TABLE `admin_chatbot_logs` DISABLE KEYS */;
INSERT INTO `admin_chatbot_logs` VALUES (1,54,'wM5x43ksyzoNmyD8Ye905R2kBLf1fBqs9hMW5aZq','cek stok','cek stok','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"raw_material\",\"period\":\"all_time\"}',1,'Daftar stok bahan baku: 1. Agave Syrup - stok 1.500 ml; 2. Air - stok 48.720 ml; 3. Botol 1000 ml - stok 40 pcs; 4. Botol 2000 ml - stok 19 pcs; 5. Botol 250 ml - stok 120 pcs; 6. Botol 500 ml - stok 80 pcs; 7. Bubuk Matcha - stok 1.000 gram; 8. Bubuk Thai Tea - stok 952 gram; 9. Coconut Milk - stok 6.000 ml; 10. Creamer - stok 2.936 gram.','{\"actions\":[{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"},{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',2,NULL,NULL,'2026-07-13 16:06:07','2026-07-13 16:06:07'),(2,54,'wM5x43ksyzoNmyD8Ye905R2kBLf1fBqs9hMW5aZq','cek stok produk','cek stok produk','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"product\",\"period\":\"all_time\"}',1,'Daftar stok produk: 1. Add On Bottled (A001) - stok 0 unit; 2. Agave Syrup (A002) - stok 0 unit; 3. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit; 4. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit; 5. Ragdoll Bliss Oat Milk Cold Whisk (M001) - stok 0 unit; 6. Ragdoll Bliss Oat Milk Hot Whisk (M002) - stok 0 unit; 7. Ragdoll Bliss Signature Milk Cold Whisk (M003) - stok 0 unit; 8. Ragdoll Bliss Signature Milk Hot Whisk (M004) - stok 0 unit; 9. Ragdoll Blush Strawberry Matcha Cold Whisk (M007) - stok 0 unit; 10. Siamese Sunset Chongky Bottle 500 ml (T002) - stok 0 unit.','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',2,NULL,NULL,'2026-07-13 16:07:02','2026-07-13 16:07:02'),(3,NULL,NULL,'cek stok produk','cek stok produk','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"product\",\"period\":\"all_time\"}',1,'Daftar stok produk: 1. Add On Bottled (A001) - stok 120 unit; 2. Agave Syrup (A002) - stok 100 unit; 3. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 33 unit; 4. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 33 unit; 5. Ragdoll Bliss Oat Milk Cold Whisk (M001) - stok 55 unit; 6. Ragdoll Bliss Oat Milk Hot Whisk (M002) - stok 55 unit; 7. Ragdoll Bliss Signature Milk Cold Whisk (M003) - stok 83 unit; 8. Ragdoll Bliss Signature Milk Hot Whisk (M004) - stok 83 unit; 9. Ragdoll Blush Strawberry Matcha Cold Whisk (M007) - stok 93 unit; 10. Siamese Sunset Chongky Bottle 500 ml (T002) - stok 79 unit.','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/localhost\\/admin\\/products\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',40,NULL,NULL,'2026-07-13 16:09:29','2026-07-13 16:09:29'),(4,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok produk','cek stok produk','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"product\",\"period\":\"all_time\"}',1,'Daftar stok produk: 1. Add On Bottled (A001) - stok 120 unit; 2. Agave Syrup (A002) - stok 100 unit; 3. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit; 4. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit; 5. Ragdoll Bliss Oat Milk Cold Whisk (M001) - stok 55 unit; 6. Ragdoll Bliss Oat Milk Hot Whisk (M002) - stok 55 unit; 7. Ragdoll Bliss Signature Milk Cold Whisk (M003) - stok 83 unit; 8. Ragdoll Bliss Signature Milk Hot Whisk (M004) - stok 83 unit; 9. Ragdoll Blush Strawberry Matcha Cold Whisk (M007) - stok 93 unit; 10. Siamese Sunset Chongky Bottle 500 ml (T002) - stok 39 unit.','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',9,NULL,NULL,'2026-07-13 16:10:58','2026-07-13 16:10:58'),(5,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok rendah','cek stok rendah','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah: 1. Add On Bottled (A001) - stok 0 unit; 2. Agave Syrup (A002) - stok 0 unit; 3. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit; 4. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit; 5. Ragdoll Bliss Oat Milk Cold Whisk (M001) - stok 0 unit; 6. Ragdoll Bliss Oat Milk Hot Whisk (M002) - stok 0 unit; 7. Ragdoll Bliss Signature Milk Cold Whisk (M003) - stok 0 unit; 8. Ragdoll Bliss Signature Milk Hot Whisk (M004) - stok 0 unit; 9. Ragdoll Blush Strawberry Matcha Cold Whisk (M007) - stok 0 unit; 10. Siamese Sunset Chongky Bottle 500 ml (T002) - stok 0 unit.','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\",\"last_period\":\"all_time\"}',3,NULL,NULL,'2026-07-13 16:16:18','2026-07-13 16:16:18'),(6,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Ringkasan penjualan minggu ini','ringkasan penjualan minggu ini','ringkasan_penjualan','{\"period\":\"current_week\"}',1,'Ringkasan penjualan minggu ini: 1 transaksi, 53 item terjual, total omzet Rp2.840.000, rata-rata per transaksi Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"ringkasan_penjualan\",\"last_period\":\"current_week\"}',6,NULL,NULL,'2026-07-13 16:17:11','2026-07-13 16:17:11'),(7,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Produk terlaris bulan ini','produk terlaris bulan ini','produk_terlaris','{\"period\":\"current_month\"}',1,'Produk terlaris bulan ini: 1. Calico Swirl Coconut Matcha Cold Whisk (M005) terjual 33 item dengan omzet Rp1.320.000; 2. Siamese Sunset MegaPaw Bottle 1000 ml (T003) terjual 20 item dengan omzet Rp1.520.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"produk_terlaris\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-07-13 16:17:14','2026-07-13 16:17:14'),(8,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Penjualan per metode pembayaran bulan ini','penjualan per metode pembayaran bulan ini','penjualan_per_metode_pembayaran','{\"period\":\"current_month\"}',1,'Penjualan per metode pembayaran bulan ini: 1. Lainnya - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"penjualan_per_metode_pembayaran\",\"last_period\":\"current_month\"}',1,NULL,NULL,'2026-07-13 16:17:15','2026-07-13 16:17:15'),(9,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Sales per kasir bulan ini','sales per kasir bulan ini','sales_per_cashier','{\"period\":\"current_month\"}',1,'Penjualan per kasir bulan ini: 1. koko - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"sales_per_cashier\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-07-13 16:17:18','2026-07-13 16:17:18'),(10,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Selisih shift kasir bulan ini','selisih shift kasir bulan ini','selisih_shift_kasir','{\"period\":\"current_month\"}',1,'Ringkasan selisih shift kasir bulan ini: 1. koko | 13 Jul 2026 23:09 | selisih Rp0.','{\"actions\":[{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"selisih_shift_kasir\",\"last_period\":\"current_month\"}',6,NULL,NULL,'2026-07-13 16:17:22','2026-07-13 16:17:22'),(11,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Riwayat stock movement gula','riwayat stock movement gula','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"gula\",\"period\":\"all_time\"}',0,'Sebutkan nama produk atau product ID yang lebih spesifik untuk melihat riwayat stock movement.','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',5,NULL,NULL,'2026-07-13 16:17:23','2026-07-13 16:17:23'),(12,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Penjualan minggu ini dibanding minggu lalu','penjualan minggu ini dibanding minggu lalu','perbandingan_penjualan','{\"period\":\"current_week\",\"compare_period\":\"previous_week\"}',1,'Penjualan minggu ini dibanding minggu lalu naik Rp2.840.000. Periode: Minggu ini (13 Jul 2026 - 19 Jul 2026) vs minggu lalu (06 Jul 2026 - 12 Jul 2026). Ringkasan: omzet Rp2.840.000 vs Rp0, transaksi 1 vs 0, rata-rata/transaksi Rp2.840.000 vs Rp0. Selisih: omzet +Rp2.840.000, transaksi +1, rata-rata/transaksi +Rp2.840.000. Insight: Kenaikan omzet terutama didorong oleh bertambahnya jumlah transaksi. Saran: Pertahankan pola penjualan yang berhasil dan pastikan stok bahan untuk produk terlaris tetap aman.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"perbandingan_penjualan\",\"last_period\":\"current_week\"}',4,NULL,NULL,'2026-07-13 16:17:28','2026-07-13 16:17:28'),(13,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Riwayat stock movement gula','riwayat stock movement gula','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"gula\",\"period\":\"all_time\"}',0,'Sebutkan nama produk atau product ID yang lebih spesifik untuk melihat riwayat stock movement.','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',3,NULL,NULL,'2026-07-13 16:17:34','2026-07-13 16:17:34'),(14,NULL,NULL,'cek stok produk','cek stok produk','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"product\",\"period\":\"all_time\"}',1,'Daftar stok produk:\n1. Add On Bottled (A001) - stok 120 unit\n2. Agave Syrup (A002) - stok 100 unit\n3. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n4. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit\n5. Ragdoll Bliss Oat Milk Cold Whisk (M001) - stok 55 unit\n6. Ragdoll Bliss Oat Milk Hot Whisk (M002) - stok 55 unit\n7. Ragdoll Bliss Signature Milk Cold Whisk (M003) - stok 83 unit\n8. Ragdoll Bliss Signature Milk Hot Whisk (M004) - stok 83 unit\n9. Ragdoll Blush Strawberry Matcha Cold Whisk (M007) - stok 93 unit\n10. Siamese Sunset Chongky Bottle 500 ml (T002) - stok 39 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/localhost\\/admin\\/products\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',41,NULL,NULL,'2026-07-13 16:18:51','2026-07-13 16:18:51'),(15,NULL,NULL,'produk low stock','produk low stock','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah:\n1. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n2. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/localhost\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/localhost\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\"}',6,NULL,NULL,'2026-07-13 16:18:51','2026-07-13 16:18:51'),(16,NULL,NULL,'cek stok produk','cek stok produk','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"product\",\"period\":\"all_time\"}',1,'Daftar stok produk:\n1. Add On Bottled (A001) - stok 120 unit\n2. Agave Syrup (A002) - stok 100 unit\n3. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n4. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit\n5. Ragdoll Bliss Oat Milk Cold Whisk (M001) - stok 55 unit\n6. Ragdoll Bliss Oat Milk Hot Whisk (M002) - stok 55 unit\n7. Ragdoll Bliss Signature Milk Cold Whisk (M003) - stok 83 unit\n8. Ragdoll Bliss Signature Milk Hot Whisk (M004) - stok 83 unit\n9. Ragdoll Blush Strawberry Matcha Cold Whisk (M007) - stok 93 unit\n10. Siamese Sunset Chongky Bottle 500 ml (T002) - stok 39 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/localhost\\/admin\\/products\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',37,NULL,NULL,'2026-07-13 16:19:22','2026-07-13 16:19:22'),(17,NULL,NULL,'produk low stock','produk low stock','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah:\n1. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n2. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/localhost\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/localhost\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\"}',5,NULL,NULL,'2026-07-13 16:19:22','2026-07-13 16:19:22'),(18,NULL,NULL,'produk akan expired','produk akan expired','produk_akan_expired','{\"days\":30}',1,'Tidak ada produk yang akan expired dalam 30 hari ke depan.','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/localhost\\/admin\\/products\"}]}','{\"last_intent\":\"produk_akan_expired\"}',2,NULL,NULL,'2026-07-13 16:19:22','2026-07-13 16:19:22'),(19,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok habis','cek stok habis','cek_stok_produk','{\"product_id\":null,\"product_query\":\"habis\",\"stock_target\":\"raw_material\",\"period\":\"all_time\"}',0,'Bahan yang Anda tanyakan tidak ditemukan. Jika ingin cek menu/produk, gunakan format \"cek stok produk nama\".','{\"actions\":[{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"},{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',4,NULL,NULL,'2026-07-13 16:20:23','2026-07-13 16:20:23'),(20,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok rendah','cek stok rendah','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah:\n1. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n2. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\",\"last_period\":\"all_time\"}',6,NULL,NULL,'2026-07-13 16:20:32','2026-07-13 16:20:32'),(21,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Bantuan','bantuan','bantuan_chatbot','[]',1,'Fokus utama saya sekarang adalah insight operasional yang datanya paling kuat di POS Anda. Insight utama: 1. Ringkasan penjualan per periode. 2. Produk terlaris dan penjualan per kasir. 3. Penjualan per metode pembayaran. 4. Selisih shift kasir. 5. Riwayat stock movement produk tertentu. Analisis lanjutan: 1. Profit per produk. 2. Perbandingan penjualan antar periode. 3. Perbandingan performa kasir. 4. Transaksi terakhir kasir tertentu. 5. Tren penjualan produk. Insight tambahan saat data mendukung: 1. Cek stok produk tertentu. 2. Produk low stock. 3. Produk akan expired. 4. Stok mati atau produk tidak terjual. 5. Top kategori saat data kategori sudah lebih beragam. Contoh pertanyaan prioritas: Ringkasan penjualan minggu ini; Produk terlaris bulan ini; Penjualan per metode pembayaran bulan ini; Sales per kasir bulan ini; Selisih shift kasir bulan ini; penjualan minggu ini dibanding minggu lalu; kasir mana yang naik omzetnya bulan ini; riwayat stock movement gula.','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"},{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"bantuan_chatbot\"}',0,NULL,NULL,'2026-07-13 16:21:11','2026-07-13 16:21:11'),(22,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Ringkasan penjualan minggu ini','ringkasan penjualan minggu ini','ringkasan_penjualan','{\"period\":\"current_week\"}',1,'Ringkasan penjualan minggu ini: 1 transaksi, 53 item terjual, total omzet Rp2.840.000, rata-rata per transaksi Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"ringkasan_penjualan\",\"last_period\":\"current_week\"}',3,NULL,NULL,'2026-07-13 16:21:15','2026-07-13 16:21:15'),(23,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok rendah','cek stok rendah','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah:\n1. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n2. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\",\"last_period\":\"current_week\"}',7,NULL,NULL,'2026-07-13 16:21:20','2026-07-13 16:21:20'),(24,NULL,NULL,'riwayat stock movement gula','riwayat stock movement gula','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"gula\",\"period\":\"all_time\"}',1,'10 riwayat stock movement terbaru untuk bahan Gula Pasir sepanjang data yang tersedia:\n1. 13 Jul 2026 23:09 | OUT/Online Order - Siamese Sunset MegaPaw Bottle 1000 ml | qty 800 gram | 4.920 -> 4.120 | koko','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/localhost\\/stock_movement\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/localhost\\/purchases\\/create\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',34,NULL,NULL,'2026-07-13 16:22:12','2026-07-13 16:22:12'),(25,NULL,NULL,'riwayat stok bahan gula','riwayat stok bahan gula','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"gula\",\"period\":\"all_time\"}',1,'10 riwayat stock movement terbaru untuk bahan Gula Pasir sepanjang data yang tersedia:\n1. 13 Jul 2026 23:09 | OUT/Online Order - Siamese Sunset MegaPaw Bottle 1000 ml | qty 800 gram | 4.920 -> 4.120 | koko','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/localhost\\/stock_movement\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/localhost\\/purchases\\/create\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',4,NULL,NULL,'2026-07-13 16:22:12','2026-07-13 16:22:12'),(26,NULL,NULL,'riwayat stok produk M001','riwayat stok produk m001','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"m001\",\"period\":\"all_time\"}',0,'Tidak ada riwayat stock movement untuk produk yang diminta pada periode tersebut.','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/localhost\\/stock_movement\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',3,NULL,NULL,'2026-07-13 16:22:12','2026-07-13 16:22:12'),(27,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Ringkasan penjualan minggu ini','ringkasan penjualan minggu ini','ringkasan_penjualan','{\"period\":\"current_week\"}',1,'Ringkasan penjualan minggu ini: 1 transaksi, 53 item terjual, total omzet Rp2.840.000, rata-rata per transaksi Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"ringkasan_penjualan\",\"last_period\":\"current_week\"}',2,NULL,NULL,'2026-07-13 16:23:10','2026-07-13 16:23:10'),(28,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Produk terlaris bulan ini','produk terlaris bulan ini','produk_terlaris','{\"period\":\"current_month\"}',1,'Produk terlaris bulan ini: 1. Calico Swirl Coconut Matcha Cold Whisk (M005) terjual 33 item dengan omzet Rp1.320.000\n2. Siamese Sunset MegaPaw Bottle 1000 ml (T003) terjual 20 item dengan omzet Rp1.520.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"produk_terlaris\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-07-13 16:23:13','2026-07-13 16:23:13'),(29,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Penjualan per metode pembayaran bulan ini','penjualan per metode pembayaran bulan ini','penjualan_per_metode_pembayaran','{\"period\":\"current_month\"}',1,'Penjualan per metode pembayaran bulan ini: 1. Lainnya - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"penjualan_per_metode_pembayaran\",\"last_period\":\"current_month\"}',1,NULL,NULL,'2026-07-13 16:23:15','2026-07-13 16:23:15'),(30,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Riwayat stok produk M001','riwayat stok produk m001','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"m001\",\"period\":\"all_time\"}',0,'Tidak ada riwayat stock movement untuk produk yang diminta pada periode tersebut.','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',3,NULL,NULL,'2026-07-13 16:23:23','2026-07-13 16:23:23'),(31,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Ringkasan penjualan minggu ini','ringkasan penjualan minggu ini','ringkasan_penjualan','{\"period\":\"current_week\"}',1,'Ringkasan penjualan minggu ini: 1 transaksi, 53 item terjual, total omzet Rp2.840.000, rata-rata per transaksi Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"ringkasan_penjualan\",\"last_period\":\"current_week\"}',4,NULL,NULL,'2026-07-13 16:23:44','2026-07-13 16:23:44'),(32,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Produk terlaris bulan ini','produk terlaris bulan ini','produk_terlaris','{\"period\":\"current_month\"}',1,'Produk terlaris bulan ini: 1. Calico Swirl Coconut Matcha Cold Whisk (M005) terjual 33 item dengan omzet Rp1.320.000\n2. Siamese Sunset MegaPaw Bottle 1000 ml (T003) terjual 20 item dengan omzet Rp1.520.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"produk_terlaris\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-07-13 16:23:46','2026-07-13 16:23:46'),(33,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Penjualan per metode pembayaran bulan ini','penjualan per metode pembayaran bulan ini','penjualan_per_metode_pembayaran','{\"period\":\"current_month\"}',1,'Penjualan per metode pembayaran bulan ini: 1. Lainnya - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"penjualan_per_metode_pembayaran\",\"last_period\":\"current_month\"}',1,NULL,NULL,'2026-07-13 16:23:48','2026-07-13 16:23:48'),(34,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok produk','cek stok produk','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"product\",\"period\":\"all_time\"}',1,'Daftar stok produk:\n1. Add On Bottled (A001) - stok 120 unit\n2. Agave Syrup (A002) - stok 100 unit\n3. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n4. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit\n5. Ragdoll Bliss Oat Milk Cold Whisk (M001) - stok 55 unit\n6. Ragdoll Bliss Oat Milk Hot Whisk (M002) - stok 55 unit\n7. Ragdoll Bliss Signature Milk Cold Whisk (M003) - stok 83 unit\n8. Ragdoll Bliss Signature Milk Hot Whisk (M004) - stok 83 unit\n9. Ragdoll Blush Strawberry Matcha Cold Whisk (M007) - stok 93 unit\n10. Siamese Sunset Chongky Bottle 500 ml (T002) - stok 39 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',7,NULL,NULL,'2026-07-13 16:23:54','2026-07-13 16:23:54'),(35,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Riwayat stok bahan gula','riwayat stok bahan gula','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"gula\",\"period\":\"all_time\"}',1,'10 riwayat stock movement terbaru untuk bahan Gula Pasir sepanjang data yang tersedia:\n1. 13 Jul 2026 23:09 | OUT/Online Order - Siamese Sunset MegaPaw Bottle 1000 ml | qty 800 gram | 4.920 -> 4.120 | koko','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',6,NULL,NULL,'2026-07-13 16:25:20','2026-07-13 16:25:20'),(36,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Riwayat stok produk M001','riwayat stok produk m001','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"m001\",\"period\":\"all_time\"}',0,'Tidak ada riwayat stock movement untuk produk yang diminta pada periode tersebut.','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',2,NULL,NULL,'2026-07-13 16:25:22','2026-07-13 16:25:22'),(37,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Selisih shift kasir bulan ini','selisih shift kasir bulan ini','selisih_shift_kasir','{\"period\":\"current_month\"}',1,'Ringkasan selisih shift kasir bulan ini: 1. koko | 13 Jul 2026 23:09 | selisih Rp0.','{\"actions\":[{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"selisih_shift_kasir\",\"last_period\":\"current_month\"}',4,NULL,NULL,'2026-07-13 16:25:25','2026-07-13 16:25:25'),(38,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Sales per kasir bulan ini','sales per kasir bulan ini','sales_per_cashier','{\"period\":\"current_month\"}',1,'Penjualan per kasir bulan ini: 1. koko - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"sales_per_cashier\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-07-13 16:25:26','2026-07-13 16:25:26'),(39,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Penjualan per metode pembayaran bulan ini','penjualan per metode pembayaran bulan ini','penjualan_per_metode_pembayaran','{\"period\":\"current_month\"}',1,'Penjualan per metode pembayaran bulan ini: 1. Lainnya - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"penjualan_per_metode_pembayaran\",\"last_period\":\"current_month\"}',1,NULL,NULL,'2026-07-13 16:25:28','2026-07-13 16:25:28'),(40,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Ringkasan penjualan minggu ini','ringkasan penjualan minggu ini','ringkasan_penjualan','{\"period\":\"current_week\"}',1,'Ringkasan penjualan minggu ini: 1 transaksi, 53 item terjual, total omzet Rp2.840.000, rata-rata per transaksi Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"ringkasan_penjualan\",\"last_period\":\"current_week\"}',2,NULL,NULL,'2026-07-13 16:25:36','2026-07-13 16:25:36'),(41,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Produk terlaris bulan ini','produk terlaris bulan ini','produk_terlaris','{\"period\":\"current_month\"}',1,'Produk terlaris bulan ini: 1. Calico Swirl Coconut Matcha Cold Whisk (M005) terjual 33 item dengan omzet Rp1.320.000\n2. Siamese Sunset MegaPaw Bottle 1000 ml (T003) terjual 20 item dengan omzet Rp1.520.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"produk_terlaris\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-07-13 16:25:37','2026-07-13 16:25:37'),(42,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Penjualan per metode pembayaran bulan ini','penjualan per metode pembayaran bulan ini','penjualan_per_metode_pembayaran','{\"period\":\"current_month\"}',1,'Penjualan per metode pembayaran bulan ini: 1. Lainnya - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"penjualan_per_metode_pembayaran\",\"last_period\":\"current_month\"}',1,NULL,NULL,'2026-07-13 16:25:38','2026-07-13 16:25:38'),(43,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Sales per kasir bulan ini','sales per kasir bulan ini','sales_per_cashier','{\"period\":\"current_month\"}',1,'Penjualan per kasir bulan ini: 1. koko - 1 transaksi, omzet Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Lihat Shift\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/shifts\"}]}','{\"last_intent\":\"sales_per_cashier\",\"last_period\":\"current_month\"}',2,NULL,NULL,'2026-07-13 16:25:39','2026-07-13 16:25:39'),(44,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','Riwayat stok produk M001','riwayat stok produk m001','riwayat_stock_movement','{\"product_id\":null,\"product_query\":\"m001\",\"period\":\"all_time\"}',0,'Tidak ada riwayat stock movement untuk produk yang diminta pada periode tersebut.','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"riwayat_stock_movement\",\"last_period\":\"all_time\"}',2,NULL,NULL,'2026-07-13 16:25:41','2026-07-13 16:25:41'),(45,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok terendah','cek stok terendah','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah:\n1. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n2. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\",\"last_period\":\"all_time\"}',8,NULL,NULL,'2026-07-13 16:39:04','2026-07-13 16:39:04'),(46,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok bahan baku','cek stok bahan baku','cek_stok_produk','{\"product_id\":null,\"product_query\":null,\"stock_target\":\"raw_material\",\"period\":\"all_time\"}',1,'Daftar stok bahan baku:\n1. Agave Syrup - stok 1.500 ml\n2. Air - stok 34.600 ml\n3. Botol 1000 ml - stok 20 pcs\n4. Botol 2000 ml - stok 19 pcs\n5. Botol 250 ml - stok 120 pcs\n6. Botol 500 ml - stok 80 pcs\n7. Bubuk Matcha - stok 835 gram\n8. Bubuk Thai Tea - stok 472 gram\n9. Coconut Milk - stok 60 ml\n10. Creamer - stok 2.296 gram','{\"actions\":[{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"},{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',2,NULL,NULL,'2026-07-13 16:41:21','2026-07-13 16:41:21'),(47,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok bahan terendah','cek stok bahan terendah','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah:\n1. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n2. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\",\"last_period\":\"all_time\"}',5,NULL,NULL,'2026-07-13 16:41:45','2026-07-13 16:41:45'),(48,NULL,NULL,'cek stok bahan terendah','cek stok bahan terendah','produk_low_stock','{\"threshold\":5,\"stock_target\":\"raw_material\"}',1,'Bahan baku dengan stok paling rendah/menipis:\n1. Coconut Milk - stok 60 ml, minimum 1.000 ml','{\"actions\":[{\"label\":\"Restock\",\"url\":\"http:\\/\\/localhost\\/purchases\\/create\"},{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/localhost\\/stock_movement\"}]}','{\"last_intent\":\"produk_low_stock\"}',31,NULL,NULL,'2026-07-13 16:42:45','2026-07-13 16:42:45'),(49,NULL,NULL,'stok bahan paling rendah','stok bahan paling rendah','produk_low_stock','{\"threshold\":5,\"stock_target\":\"raw_material\"}',1,'Bahan baku dengan stok paling rendah/menipis:\n1. Coconut Milk - stok 60 ml, minimum 1.000 ml','{\"actions\":[{\"label\":\"Restock\",\"url\":\"http:\\/\\/localhost\\/purchases\\/create\"},{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/localhost\\/stock_movement\"}]}','{\"last_intent\":\"produk_low_stock\"}',1,NULL,NULL,'2026-07-13 16:42:45','2026-07-13 16:42:45'),(50,NULL,NULL,'produk low stock','produk low stock','produk_low_stock','{\"threshold\":5}',1,'Produk dengan stok menipis (<= 5) adalah:\n1. Calico Swirl Coconut Matcha Cold Whisk (M005) - stok 0 unit\n2. Calico Swirl Coconut Matcha Hot Whisk (M006) - stok 0 unit','{\"actions\":[{\"label\":\"Lihat Produk\",\"url\":\"http:\\/\\/localhost\\/admin\\/products\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/localhost\\/purchases\\/create\"}]}','{\"last_intent\":\"produk_low_stock\"}',10,NULL,NULL,'2026-07-13 16:42:45','2026-07-13 16:42:45'),(51,54,'MU7AP0pEbaukoOmT3J6hoo6lODn7Up7U5f2wNXTE','cek stok bahan terendah','cek stok bahan terendah','produk_low_stock','{\"threshold\":5,\"stock_target\":\"raw_material\"}',1,'Bahan baku dengan stok paling rendah/menipis:\n1. Coconut Milk - stok 60 ml, minimum 1.000 ml','{\"actions\":[{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"},{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"}]}','{\"last_intent\":\"produk_low_stock\"}',2,NULL,NULL,'2026-07-13 16:47:57','2026-07-13 16:47:57'),(52,54,'UOvxiu5qOSXreb4gFBS1ZviNVsrVqcO9bm3xD7FO','cek stok matcha','cek stok matcha','cek_stok_produk','{\"product_id\":null,\"product_query\":\"matcha\",\"stock_target\":\"raw_material\",\"period\":\"all_time\"}',1,'Stok bahan Bubuk Matcha saat ini 835 gram. Minimum stok 150 gram.','{\"actions\":[{\"label\":\"Lihat Stock Movement\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/stock_movement\"},{\"label\":\"Restock\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/purchases\\/create\"}]}','{\"last_intent\":\"cek_stok_produk\",\"last_period\":\"all_time\"}',5,NULL,NULL,'2026-07-15 08:51:54','2026-07-15 08:51:54'),(53,54,'UOvxiu5qOSXreb4gFBS1ZviNVsrVqcO9bm3xD7FO','tolong ringkas penjualan minggu ini','tolong ringkas penjualan minggu ini','ringkasan_penjualan','{\"period\":\"current_week\"}',1,'Ringkasan penjualan minggu ini: 1 transaksi, 53 item terjual, total omzet Rp2.840.000, rata-rata per transaksi Rp2.840.000.','{\"actions\":[{\"label\":\"Lihat Penjualan\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/sales_data\"},{\"label\":\"Laporan Profit\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/reports\\/profit\"}]}','{\"last_intent\":\"ringkasan_penjualan\",\"last_period\":\"current_week\"}',7,NULL,NULL,'2026-07-15 08:52:15','2026-07-15 08:52:15');
/*!40000 ALTER TABLE `admin_chatbot_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buyer_cart_items`
--

DROP TABLE IF EXISTS `buyer_cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buyer_cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `buyer_cart_id` bigint(20) unsigned NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `customization` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customization`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `buyer_cart_items_buyer_cart_id_product_id_unique` (`buyer_cart_id`,`product_id`),
  KEY `buyer_cart_items_product_id_foreign` (`product_id`),
  CONSTRAINT `buyer_cart_items_buyer_cart_id_foreign` FOREIGN KEY (`buyer_cart_id`) REFERENCES `buyer_carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `buyer_cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buyer_cart_items`
--

LOCK TABLES `buyer_cart_items` WRITE;
/*!40000 ALTER TABLE `buyer_cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `buyer_cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buyer_carts`
--

DROP TABLE IF EXISTS `buyer_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buyer_carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `buyer_carts_user_id_unique` (`user_id`),
  CONSTRAINT `buyer_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buyer_carts`
--

LOCK TABLES `buyer_carts` WRITE;
/*!40000 ALTER TABLE `buyer_carts` DISABLE KEYS */;
INSERT INTO `buyer_carts` VALUES (16,57,'2026-07-13 16:09:01','2026-07-13 16:09:01');
/*!40000 ALTER TABLE `buyer_carts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,'55','A001','Add On Bottled',1000,2000,2,4000,'2026-07-13 17:49:58','2026-07-13 17:52:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cashier_shifts`
--

LOCK TABLES `cashier_shifts` WRITE;
/*!40000 ALTER TABLE `cashier_shifts` DISABLE KEYS */;
INSERT INTO `cashier_shifts` VALUES (2,56,'2026-07-13 23:09:48','2026-07-13 23:10:07',0.00,0.00,'buka','closed','2026-07-13 16:09:48','2026-07-13 16:10:07'),(3,55,'2026-07-14 00:49:55',NULL,200000.00,NULL,NULL,'open','2026-07-13 17:49:55','2026-07-13 17:49:55');
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
INSERT INTO `categories` VALUES (1,'MATCHA','Matcha Menu','admin','2026-06-22 00:33:06','2026-06-22 00:33:06'),(2,'THAI','Thai Tea','admin','2026-06-22 00:33:06','2026-06-22 00:33:06'),(3,'ADDON','Add On','admin','2026-06-22 00:33:06','2026-06-22 00:33:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_sales`
--

LOCK TABLES `detail_sales` WRITE;
/*!40000 ALTER TABLE `detail_sales` DISABLE KEYS */;
INSERT INTO `detail_sales` VALUES (3,'INV-20260713-0001','56','M005','Calico Swirl Coconut Matcha Cold Whisk',808500.00,40000,15500.00,33,1320000,'2026-07-13 16:10:01','2026-07-13 16:10:01'),(4,'INV-20260713-0001','56','T003','Siamese Sunset MegaPaw Bottle 1000 ml',1056000.00,76000,23200.00,20,1520000,'2026-07-13 16:10:01','2026-07-13 16:10:01');
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_02_02_162757_create_permission_tables',1),(5,'2025_03_08_085212_create_categories_table',1),(6,'2025_03_08_092922_create_products_table',1),(7,'2025_03_19_153912_create_stock_movements_table',1),(8,'2025_04_05_155201_create_carts_table',1),(9,'2025_04_20_004345_create_sales_table',1),(10,'2025_04_20_004735_create_detail_sales_table',1),(11,'2026_04_23_000001_create_suppliers_table',1),(12,'2026_04_23_000002_create_purchases_table',1),(13,'2026_04_23_000003_create_purchase_items_table',1),(14,'2026_04_23_000004_add_buy_price_to_products_and_source_to_stock_movements_table',1),(15,'2026_04_23_000005_update_buy_price_and_profit_columns_for_reports',1),(16,'2026_04_23_000006_create_cashier_shifts_table',1),(17,'2026_04_23_000007_add_shift_id_to_sales_table',1),(18,'2026_04_27_000001_create_admin_chatbot_logs_table',1),(19,'2026_06_22_000001_create_raw_materials_tables',1),(20,'2026_06_22_000002_convert_purchase_items_to_raw_materials',1),(21,'2026_06_25_000001_create_online_ordering_tables',1),(22,'2026_06_25_000002_add_online_ordering_columns_to_sales_table',1),(23,'2026_06_25_000003_add_customization_to_online_ordering_items',1),(24,'2026_06_30_000001_create_order_status_histories_table',1),(25,'2026_07_15_000001_add_packaging_fields_to_purchase_items_table',2);
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
INSERT INTO `model_has_roles` VALUES (71,'App\\Models\\User',55),(71,'App\\Models\\User',56),(72,'App\\Models\\User',54),(73,'App\\Models\\User',57);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `subtotal` int(10) unsigned NOT NULL,
  `customization` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customization`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (12,11,'M005','Calico Swirl Coconut Matcha Cold Whisk',40000,33,1320000,'{\"ice_level\":\"Normal Ice\",\"sugar_level\":\"Normal Sugar\",\"add_ons\":[]}','2026-07-13 16:09:18','2026-07-13 16:09:18'),(13,11,'T003','Siamese Sunset MegaPaw Bottle 1000 ml',76000,20,1520000,'{\"ice_level\":\"Normal Ice\",\"sugar_level\":\"Normal Sugar\",\"add_ons\":[]}','2026-07-13 16:09:18','2026-07-13 16:09:18'),(14,12,'A001','Add On Bottled',2000,3,6000,'{\"ice_level\":\"Normal Ice\",\"sugar_level\":\"Normal Sugar\",\"add_ons\":[]}','2026-07-13 17:52:50','2026-07-13 17:52:50');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_histories`
--

DROP TABLE IF EXISTS `order_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) DEFAULT NULL,
  `from_payment_status` varchar(30) DEFAULT NULL,
  `to_payment_status` varchar(30) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_status_histories_actor_id_foreign` (`actor_id`),
  KEY `order_status_histories_order_id_created_at_index` (`order_id`,`created_at`),
  KEY `order_status_histories_action_index` (`action`),
  CONSTRAINT `order_status_histories_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_status_histories_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_histories`
--

LOCK TABLES `order_status_histories` WRITE;
/*!40000 ALTER TABLE `order_status_histories` DISABLE KEYS */;
INSERT INTO `order_status_histories` VALUES (15,11,56,'payment_verified',NULL,'pending','waiting_verification','paid','Pembayaran QRIS diverifikasi.','2026-07-13 16:09:56','2026-07-13 16:09:56'),(16,11,56,'confirmed','pending','confirmed',NULL,'paid','Pesanan dikonfirmasi dan stok dikurangi.','2026-07-13 16:09:58','2026-07-13 16:09:58'),(17,11,56,'processing','confirmed','processing',NULL,'paid','Pesanan mulai diproses.','2026-07-13 16:09:59','2026-07-13 16:09:59'),(18,11,56,'completed','processing','completed','paid','paid','Pesanan selesai dan penjualan tercatat.','2026-07-13 16:10:01','2026-07-13 16:10:01'),(19,12,55,'payment_verified',NULL,'pending','waiting_verification','paid','Pembayaran QRIS diverifikasi.','2026-07-13 17:52:58','2026-07-13 17:52:58'),(20,12,55,'confirmed','pending','confirmed',NULL,'paid','Pesanan dikonfirmasi dan stok dikurangi.','2026-07-13 17:52:59','2026-07-13 17:52:59');
/*!40000 ALTER TABLE `order_status_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_code` varchar(40) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(20) NOT NULL,
  `payment_status` varchar(30) NOT NULL DEFAULT 'unpaid',
  `fulfillment_type` varchar(20) NOT NULL DEFAULT 'pickup',
  `total_price` int(10) unsigned NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `confirmed_by` bigint(20) unsigned DEFAULT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `cancelled_by` bigint(20) unsigned DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `stock_deducted_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `processing_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_confirmed_by_foreign` (`confirmed_by`),
  KEY `orders_completed_by_foreign` (`completed_by`),
  KEY `orders_cancelled_by_foreign` (`cancelled_by`),
  KEY `orders_status_index` (`status`),
  KEY `orders_payment_status_index` (`payment_status`),
  CONSTRAINT `orders_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (11,57,'ORD-20260713-230918-1OGB','completed','qris','paid','pickup',2840000,NULL,56,56,NULL,NULL,'2026-07-13 16:09:58','2026-07-13 16:09:58','2026-07-13 16:09:59','2026-07-13 16:10:01',NULL,'2026-07-13 16:09:18','2026-07-13 16:10:01'),(12,57,'ORD-20260714-005250-YQTK','confirmed','qris','paid','pickup',6000,NULL,55,NULL,NULL,NULL,'2026-07-13 17:52:59','2026-07-13 17:52:59',NULL,NULL,NULL,'2026-07-13 17:52:50','2026-07-13 17:52:59');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
INSERT INTO `permissions` VALUES (1,'cashier-dashboard','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(2,'sell-products','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(3,'delete-selled-products','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(4,'add-products','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(5,'edit-products','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(6,'delete-products','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(7,'view-products','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(8,'admin-dashboard','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(9,'create-cashiers','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(10,'edit-cashiers','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(11,'delete-cashiers','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(12,'view-cashiers','web','2026-07-13 16:05:34','2026-07-13 16:05:34');
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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_recipes`
--

LOCK TABLES `product_recipes` WRITE;
/*!40000 ALTER TABLE `product_recipes` DISABLE KEYS */;
INSERT INTO `product_recipes` VALUES (1,'M001',1,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(2,'M001',3,40.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(3,'M001',4,180.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(4,'M001',10,10.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(5,'M001',17,120.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(6,'M001',12,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(7,'M002',1,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(8,'M002',3,60.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(9,'M002',4,180.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(10,'M002',10,10.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(11,'M002',12,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(12,'M003',1,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(13,'M003',3,40.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(14,'M003',5,180.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(15,'M003',10,10.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(16,'M003',17,120.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(17,'M003',12,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(18,'M004',1,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(19,'M004',3,60.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(20,'M004',5,180.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(21,'M004',10,10.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(22,'M004',12,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(23,'M005',1,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(24,'M005',3,40.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(25,'M005',18,180.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(26,'M005',10,10.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(27,'M005',17,120.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(28,'M005',12,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(29,'M006',1,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(30,'M006',3,60.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(31,'M006',18,180.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(32,'M006',10,10.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(33,'M006',12,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(34,'M007',1,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(35,'M007',3,40.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(36,'M007',5,160.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(37,'M007',19,30.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(38,'M007',10,5.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(39,'M007',17,120.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(40,'M007',12,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(41,'T001',2,6.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(42,'T001',3,160.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(43,'T001',6,25.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(44,'T001',7,40.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(45,'T001',8,8.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(46,'T001',9,10.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(47,'T001',13,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(48,'T002',2,12.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(49,'T002',3,320.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(50,'T002',6,50.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(51,'T002',7,80.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(52,'T002',8,16.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(53,'T002',9,20.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(54,'T002',14,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(55,'T003',2,24.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(56,'T003',3,640.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(57,'T003',6,100.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(58,'T003',7,160.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(59,'T003',8,32.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(60,'T003',9,40.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(61,'T003',15,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(62,'T004',2,48.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(63,'T004',3,1280.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(64,'T004',6,200.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(65,'T004',7,320.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(66,'T004',8,64.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(67,'T004',9,80.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(68,'T004',16,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(69,'A001',13,1.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(70,'A002',11,15.00,'2026-06-22 02:20:06','2026-06-22 02:20:06'),(74,'P0001',2,18.00,'2026-07-15 17:16:29','2026-07-15 17:16:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'M001','Ragdoll Bliss Oat Milk Cold Whisk','MATCHA','m001.jpeg',41000,11060.00,29940,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(2,'M002','Ragdoll Bliss Oat Milk Hot Whisk','MATCHA','m002.jpeg',36000,10960.00,25040,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(3,'M003','Ragdoll Bliss Signature Milk Cold Whisk','MATCHA','m003.jpeg',36000,4850.00,31150,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(4,'M004','Ragdoll Bliss Signature Milk Hot Whisk','MATCHA','m004.jpeg',32000,4750.00,27250,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(5,'M005','Calico Swirl Coconut Matcha Cold Whisk','MATCHA','m005.jpeg',40000,6560.00,33440,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(6,'M006','Calico Swirl Coconut Matcha Hot Whisk','MATCHA','m006.jpeg',36000,6460.00,29540,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(7,'M007','Ragdoll Blush Strawberry Matcha Cold Whisk','MATCHA','m007.jpeg',42000,6640.00,35360,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(8,'T001','Siamese Sunset Normal Bottle 250 ml','THAI','t001.jpeg',19000,4825.00,14175,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(9,'T002','Siamese Sunset Chongky Bottle 500 ml','THAI','t002.jpeg',38000,9150.00,28850,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(10,'T003','Siamese Sunset MegaPaw Bottle 1000 ml','THAI','t003.jpeg',76000,17800.00,58200,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(11,'T004','Siamese Sunset Party Size 2000 ml','THAI','t004.jpeg',152000,34600.00,117400,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(12,'A001','Add On Bottled','ADDON','a001.jpeg',2000,1000.00,1000,0,'2027-06-22','2026-06-22 00:33:06','2026-06-22 02:15:57'),(13,'A002','Agave Syrup','ADDON','a002.jpeg',3000,2250.00,750,0,'2027-06-22','2026-06-22 00:33:06','2026-07-15 17:39:53'),(25,'P0001','thaitea roasted','THAI','20260716_001456_9WsIxCRkOh.jpeg',35000,4050.00,30950,10,'2026-08-06','2026-07-15 17:14:56','2026-07-15 17:28:43'),(26,'P0002','thaitea roasted1','THAI','20260716_003048_puJtvlCn5D.jpeg',35000,0.00,35000,10,'2026-07-30','2026-07-15 17:30:48','2026-07-15 17:30:48');
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
  `package_quantity` decimal(12,2) NOT NULL DEFAULT 1.00,
  `package_size` decimal(12,2) NOT NULL DEFAULT 1.00,
  `package_label` varchar(50) DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `buy_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_raw_material_id_foreign` (`raw_material_id`),
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES (4,2,18,NULL,5.00,1000.00,'oatside',5000.00,100000.00,'2026-07-15 17:06:55','2026-07-15 17:06:55'),(5,4,2,NULL,1.00,400.00,'pouch',400.00,90000.00,'2026-07-15 17:27:48','2026-07-15 17:27:48'),(6,5,5,NULL,2.00,1000.00,'ultra',2000.00,21000.00,'2026-07-15 17:34:48','2026-07-15 17:34:48'),(7,6,11,NULL,1.00,100.00,'sample',100.00,15000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(8,6,3,NULL,1.00,1000.00,'liter',1000.00,1000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(9,6,15,NULL,1.00,10.00,'pcs',10.00,25000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(10,6,16,NULL,1.00,10.00,'pcs',10.00,40000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(11,6,13,NULL,1.00,10.00,'pcs',10.00,10000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(12,6,14,NULL,1.00,10.00,'pcs',10.00,15000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(13,6,1,NULL,1.00,100.00,'sample',100.00,30000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(14,6,8,NULL,1.00,100.00,'sample',100.00,4000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(15,6,12,NULL,1.00,20.00,'pcs',20.00,10000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(16,6,17,NULL,1.00,1000.00,'kg',1000.00,1000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(17,6,9,NULL,1.00,1000.00,'kg',1000.00,17000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(18,6,19,NULL,1.00,100.00,'sample',100.00,8000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(19,6,7,NULL,1.00,405.00,'kaleng',405.00,12150.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(20,6,6,NULL,1.00,370.00,'kaleng',370.00,9250.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(21,6,4,NULL,1.00,1000.00,'karton',1000.00,45000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53'),(22,6,10,NULL,1.00,100.00,'sample',100.00,8000.00,'2026-07-15 17:39:53','2026-07-15 17:39:53');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (2,'PO-20260715-0001',1,NULL,'2026-07-15',NULL,NULL,54,'2026-07-15 15:42:58','2026-07-15 15:42:58'),(4,'PO-20260716-001',NULL,'Referensi marketplace','2026-07-16','PRICE-REF-THAITEA-400G','Restock awal untuk trigger harga resep Thai Tea Roasted. Referensi harga: ChaTraMue/Thai tea powder sekitar Rp90.000 per 400 gram.',54,'2026-07-15 17:27:48','2026-07-15 17:27:48'),(5,'PO-20260716-0002',NULL,'joko coconut','2026-07-16',NULL,NULL,54,'2026-07-15 17:34:48','2026-07-15 17:34:48'),(6,'PO-20260716-003',NULL,'Price trigger baseline','2026-07-16','PRICE-TRIGGER-RAW-MATERIALS','Restock kecil untuk mengisi harga satuan awal bahan baku yang belum punya riwayat restock.',54,'2026-07-15 17:39:53','2026-07-15 17:39:53');
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raw_material_stock_movements`
--

LOCK TABLES `raw_material_stock_movements` WRITE;
/*!40000 ALTER TABLE `raw_material_stock_movements` DISABLE KEYS */;
INSERT INTO `raw_material_stock_movements` VALUES (1,1,'ORD-20260713-230918-1OGB','out','Online Order - Calico Swirl Coconut Matcha Cold Whisk',165.00,1000.00,835.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(2,3,'ORD-20260713-230918-1OGB','out','Online Order - Calico Swirl Coconut Matcha Cold Whisk',1320.00,48720.00,47400.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(3,10,'ORD-20260713-230918-1OGB','out','Online Order - Calico Swirl Coconut Matcha Cold Whisk',330.00,2000.00,1670.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(4,12,'ORD-20260713-230918-1OGB','out','Online Order - Calico Swirl Coconut Matcha Cold Whisk',33.00,300.00,267.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(5,17,'ORD-20260713-230918-1OGB','out','Online Order - Calico Swirl Coconut Matcha Cold Whisk',3960.00,20000.00,16040.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(6,18,'ORD-20260713-230918-1OGB','out','Online Order - Calico Swirl Coconut Matcha Cold Whisk',5940.00,6000.00,60.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(7,2,'ORD-20260713-230918-1OGB','out','Online Order - Siamese Sunset MegaPaw Bottle 1000 ml',480.00,952.00,472.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(8,3,'ORD-20260713-230918-1OGB','out','Online Order - Siamese Sunset MegaPaw Bottle 1000 ml',12800.00,47400.00,34600.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(9,6,'ORD-20260713-230918-1OGB','out','Online Order - Siamese Sunset MegaPaw Bottle 1000 ml',2000.00,4800.00,2800.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(10,7,'ORD-20260713-230918-1OGB','out','Online Order - Siamese Sunset MegaPaw Bottle 1000 ml',3200.00,7680.00,4480.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(11,8,'ORD-20260713-230918-1OGB','out','Online Order - Siamese Sunset MegaPaw Bottle 1000 ml',640.00,2936.00,2296.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(12,9,'ORD-20260713-230918-1OGB','out','Online Order - Siamese Sunset MegaPaw Bottle 1000 ml',800.00,4920.00,4120.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(13,15,'ORD-20260713-230918-1OGB','out','Online Order - Siamese Sunset MegaPaw Bottle 1000 ml',20.00,40.00,20.00,'koko','2026-07-13 16:09:58','2026-07-13 16:09:58'),(14,13,'ORD-20260714-005250-YQTK','out','Online Order - Add On Bottled',3.00,120.00,117.00,'ariz','2026-07-13 17:52:59','2026-07-13 17:52:59'),(16,18,'PO-20260715-0001','adjustment','Reverse Purchase',5.00,65.00,60.00,'admin','2026-07-15 15:42:14','2026-07-15 15:42:14'),(19,18,'PO-20260715-0001','adjustment','Reverse Purchase',5.00,65.00,60.00,'admin','2026-07-15 17:06:55','2026-07-15 17:06:55'),(20,18,'PO-20260715-0001','in','Restock Purchase',5000.00,60.00,5060.00,'admin','2026-07-15 17:06:55','2026-07-15 17:06:55'),(21,2,'PO-20260716-001','in','Restock',400.00,472.00,872.00,'admin','2026-07-15 17:27:48','2026-07-15 17:27:48'),(22,5,'PO-20260716-0002','in','Restock Purchase',2000.00,15000.00,17000.00,'admin','2026-07-15 17:34:48','2026-07-15 17:34:48'),(23,11,'PO-20260716-003','in','Price Trigger',100.00,1500.00,1600.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(24,3,'PO-20260716-003','in','Price Trigger',1000.00,34600.00,35600.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(25,15,'PO-20260716-003','in','Price Trigger',10.00,20.00,30.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(26,16,'PO-20260716-003','in','Price Trigger',10.00,19.00,29.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(27,13,'PO-20260716-003','in','Price Trigger',10.00,117.00,127.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(28,14,'PO-20260716-003','in','Price Trigger',10.00,80.00,90.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(29,1,'PO-20260716-003','in','Price Trigger',100.00,835.00,935.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(30,8,'PO-20260716-003','in','Price Trigger',100.00,2296.00,2396.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(31,12,'PO-20260716-003','in','Price Trigger',20.00,267.00,287.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(32,17,'PO-20260716-003','in','Price Trigger',1000.00,16040.00,17040.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(33,9,'PO-20260716-003','in','Price Trigger',1000.00,4120.00,5120.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(34,19,'PO-20260716-003','in','Price Trigger',100.00,3000.00,3100.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(35,7,'PO-20260716-003','in','Price Trigger',405.00,4480.00,4885.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(36,6,'PO-20260716-003','in','Price Trigger',370.00,2800.00,3170.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(37,4,'PO-20260716-003','in','Price Trigger',1000.00,10000.00,11000.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53'),(38,10,'PO-20260716-003','in','Price Trigger',100.00,1670.00,1770.00,'admin','2026-07-15 17:39:53','2026-07-15 17:39:53');
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
INSERT INTO `raw_materials` VALUES (1,'Bubuk Matcha','gram',935.00,150.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(2,'Bubuk Thai Tea','gram',872.00,150.00,'2026-06-22 00:33:06','2026-07-15 17:27:48'),(3,'Air','ml',35600.00,5000.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(4,'Susu Oat (Oat Milk)','ml',11000.00,2000.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(5,'Susu Fresh Milk','ml',17000.00,3000.00,'2026-06-22 00:33:06','2026-07-15 17:34:48'),(6,'Susu Kental Manis (SKM)','ml',3170.00,1000.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(7,'Susu Evaporasi','ml',4885.00,1500.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(8,'Creamer','gram',2396.00,500.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(9,'Gula Pasir','gram',5120.00,1000.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(10,'Vanilla Syrup','ml',1770.00,300.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(11,'Agave Syrup','ml',1600.00,250.00,'2026-06-22 00:33:06','2026-07-15 17:39:53'),(12,'Cup','pcs',287.00,50.00,'2026-06-22 02:20:06','2026-07-15 17:39:53'),(13,'Botol 250 ml','pcs',127.00,30.00,'2026-06-22 02:20:06','2026-07-15 17:39:53'),(14,'Botol 500 ml','pcs',90.00,20.00,'2026-06-22 02:20:06','2026-07-15 17:39:53'),(15,'Botol 1000 ml','pcs',30.00,10.00,'2026-06-22 02:20:06','2026-07-15 17:39:53'),(16,'Botol 2000 ml','pcs',29.00,5.00,'2026-06-22 02:20:06','2026-07-15 17:39:53'),(17,'Es Batu','gram',17040.00,3000.00,'2026-06-22 02:20:06','2026-07-15 17:39:53'),(18,'Coconut Milk','ml',5060.00,1000.00,'2026-06-22 02:20:06','2026-07-15 17:06:55'),(19,'Strawberry Syrup','ml',3100.00,500.00,'2026-06-22 02:20:06','2026-07-15 17:39:53');
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
INSERT INTO `role_has_permissions` VALUES (1,71),(2,71),(3,71),(4,71),(5,71),(6,71),(7,71),(8,72),(9,72),(10,72),(11,72),(12,72);
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
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (71,'cashier','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(72,'admin','web','2026-07-13 16:05:34','2026-07-13 16:05:34'),(73,'buyer','web','2026-07-13 16:05:34','2026-07-13 16:05:34');
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
  `source` varchar(20) NOT NULL DEFAULT 'offline',
  `order_id` bigint(20) unsigned DEFAULT NULL,
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
  UNIQUE KEY `sales_order_id_unique` (`order_id`),
  KEY `sales_shift_id_foreign` (`shift_id`),
  KEY `sales_source_index` (`source`),
  CONSTRAINT `sales_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `cashier_shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (4,'INV-20260713-0001','online',11,2,'56',2840000,'Q',2840000,0,'2026-07-13 16:10:01','2026-07-13 16:10:01');
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
INSERT INTO `sessions` VALUES ('1eKMvm87j43Hr6tTQVogGNLnbf2zURGuEE85BuQc',NULL,'127.0.0.1','curl/8.7.1','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWURpcG9OSWt5TWdKeU1CSm5KTHdvUmRJejZVaFF2NVhORFFGNHZEUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3B1cmNoYXNlcy9jcmVhdGUiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3B1cmNoYXNlcy9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1784134373),('7v0IkRDajLiY7IV5k4bXTLnUSXaFGSekk53E503X',54,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.21459.0 Chrome/148.0.7778.271 Electron/42.5.1 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiaXBCRHdQRmd1emZLdndwcURnQmlBM0tSTE5CcnFhWkJlTEJiWHFKUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wdXJjaGFzZXMvY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTQ7czoyMjoiYWRtaW5fY2hhdGJvdF9tZXNzYWdlcyI7YToxOntpOjA7YTo1OntzOjQ6InJvbGUiO3M6OToiYXNzaXN0YW50IjtzOjQ6InRleHQiO3M6MTMwOiJIYWxvISBTYXlhIGJpc2EgYmFudHUgY2VrIHN0b2ssIHBlbmp1YWxhbiwgcHJvZml0LCBzaGlmdCwgZGFuIGFuYWxpc2lzIHBlcmJhbmRpbmdhbi4gU2VtdWEgamF3YWJhbiBkaWFtYmlsIGxhbmdzdW5nIGRhcmkgZGF0YWJhc2UuIjtzOjQ6InRpbWUiO3M6NToiMjM6NDUiO3M6NDoibWV0YSI7YToyOntzOjY6ImludGVudCI7czoxNToiYmFudHVhbl9jaGF0Ym90IjtzOjc6InN1Y2Nlc3MiO2I6MTt9czo3OiJhY3Rpb25zIjthOjI6e2k6MDthOjI6e3M6NToibGFiZWwiO3M6MTI6IkxpaGF0IFByb2R1ayI7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0cyI7fWk6MTthOjI6e3M6NToibGFiZWwiO3M6MTU6IkxpaGF0IFBlbmp1YWxhbiI7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zYWxlc19kYXRhIjt9fX19czoyMToiYWRtaW5fY2hhdGJvdF9jb250ZXh0IjthOjA6e319',1784134466),('BkKjm8xPTtnbvrFxezYu32IXdI8s0ytvdMjeEt4m',54,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.21459.0 Chrome/148.0.7778.271 Electron/42.5.1 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMzI1Y0dZVWNYcnV2cmlxc2ZpNWJXRXNWSFVuQzVmZWRiVkRsN3ZUYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODEyMy9hZG1pbi9wcm9kdWN0cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU0O3M6MjI6ImFkbWluX2NoYXRib3RfbWVzc2FnZXMiO2E6MTp7aTowO2E6NTp7czo0OiJyb2xlIjtzOjk6ImFzc2lzdGFudCI7czo0OiJ0ZXh0IjtzOjEzMDoiSGFsbyEgU2F5YSBiaXNhIGJhbnR1IGNlayBzdG9rLCBwZW5qdWFsYW4sIHByb2ZpdCwgc2hpZnQsIGRhbiBhbmFsaXNpcyBwZXJiYW5kaW5nYW4uIFNlbXVhIGphd2FiYW4gZGlhbWJpbCBsYW5nc3VuZyBkYXJpIGRhdGFiYXNlLiI7czo0OiJ0aW1lIjtzOjU6IjIzOjA0IjtzOjQ6Im1ldGEiO2E6Mjp7czo2OiJpbnRlbnQiO3M6MTU6ImJhbnR1YW5fY2hhdGJvdCI7czo3OiJzdWNjZXNzIjtiOjE7fXM6NzoiYWN0aW9ucyI7YToyOntpOjA7YToyOntzOjU6ImxhYmVsIjtzOjEyOiJMaWhhdCBQcm9kdWsiO3M6MzoidXJsIjtzOjM2OiJodHRwOi8vbG9jYWxob3N0OjgxMjMvYWRtaW4vcHJvZHVjdHMiO31pOjE7YToyOntzOjU6ImxhYmVsIjtzOjE1OiJMaWhhdCBQZW5qdWFsYW4iO3M6MzoidXJsIjtzOjMyOiJodHRwOi8vbG9jYWxob3N0OjgxMjMvc2FsZXNfZGF0YSI7fX19fXM6MjE6ImFkbWluX2NoYXRib3RfY29udGV4dCI7YTowOnt9fQ==',1784133499),('eWBKGDd8UedGVxUoFcgDN08r1XNYafjUrIdvkbsZ',NULL,'127.0.0.1','curl/8.7.1','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRE9GUWNsSGxIRmwyM2M0ajdvb1lzcmlNUE1nbWw1RmpKTXJaN3ZYUCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkZF9wcm9kdWN0X2FkbWluIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZGRfcHJvZHVjdF9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1784134022),('hvg95D6d4YgbJHbSILC7sAsD6WougecF6h37LYrX',54,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/125.0.6422.26 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMkxvV0xBRFlydjduWWx1QXlJNEcxcU1HUjhlT0tneW1IazNWdGFwdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODEyMy9hZG1pbi9wcm9kdWN0cy8yMy9yZWNpcGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1NDtzOjIyOiJhZG1pbl9jaGF0Ym90X21lc3NhZ2VzIjthOjE6e2k6MDthOjU6e3M6NDoicm9sZSI7czo5OiJhc3Npc3RhbnQiO3M6NDoidGV4dCI7czoxMzA6IkhhbG8hIFNheWEgYmlzYSBiYW50dSBjZWsgc3RvaywgcGVuanVhbGFuLCBwcm9maXQsIHNoaWZ0LCBkYW4gYW5hbGlzaXMgcGVyYmFuZGluZ2FuLiBTZW11YSBqYXdhYmFuIGRpYW1iaWwgbGFuZ3N1bmcgZGFyaSBkYXRhYmFzZS4iO3M6NDoidGltZSI7czo1OiIyMzowNyI7czo0OiJtZXRhIjthOjI6e3M6NjoiaW50ZW50IjtzOjE1OiJiYW50dWFuX2NoYXRib3QiO3M6Nzoic3VjY2VzcyI7YjoxO31zOjc6ImFjdGlvbnMiO2E6Mjp7aTowO2E6Mjp7czo1OiJsYWJlbCI7czoxMjoiTGloYXQgUHJvZHVrIjtzOjM6InVybCI7czozNjoiaHR0cDovL2xvY2FsaG9zdDo4MTIzL2FkbWluL3Byb2R1Y3RzIjt9aToxO2E6Mjp7czo1OiJsYWJlbCI7czoxNToiTGloYXQgUGVuanVhbGFuIjtzOjM6InVybCI7czozMjoiaHR0cDovL2xvY2FsaG9zdDo4MTIzL3NhbGVzX2RhdGEiO319fX1zOjIxOiJhZG1pbl9jaGF0Ym90X2NvbnRleHQiO2E6MDp7fX0=',1784131666),('ijhMKtrSZuQHqDetEED7iSzRCTmEkDtyO4Nhj6Qq',54,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/125.0.6422.26 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoib2Jva0p5NE1GWDZGOE5tdHpsdU1YZDV1QTZUZXd4N2VyZWlmQTd3diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODEyMy9hZGRfcHJvZHVjdF9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU0O3M6MjI6ImFkbWluX2NoYXRib3RfbWVzc2FnZXMiO2E6MTp7aTowO2E6NTp7czo0OiJyb2xlIjtzOjk6ImFzc2lzdGFudCI7czo0OiJ0ZXh0IjtzOjEzMDoiSGFsbyEgU2F5YSBiaXNhIGJhbnR1IGNlayBzdG9rLCBwZW5qdWFsYW4sIHByb2ZpdCwgc2hpZnQsIGRhbiBhbmFsaXNpcyBwZXJiYW5kaW5nYW4uIFNlbXVhIGphd2FiYW4gZGlhbWJpbCBsYW5nc3VuZyBkYXJpIGRhdGFiYXNlLiI7czo0OiJ0aW1lIjtzOjU6IjIzOjA2IjtzOjQ6Im1ldGEiO2E6Mjp7czo2OiJpbnRlbnQiO3M6MTU6ImJhbnR1YW5fY2hhdGJvdCI7czo3OiJzdWNjZXNzIjtiOjE7fXM6NzoiYWN0aW9ucyI7YToyOntpOjA7YToyOntzOjU6ImxhYmVsIjtzOjEyOiJMaWhhdCBQcm9kdWsiO3M6MzoidXJsIjtzOjM2OiJodHRwOi8vbG9jYWxob3N0OjgxMjMvYWRtaW4vcHJvZHVjdHMiO31pOjE7YToyOntzOjU6ImxhYmVsIjtzOjE1OiJMaWhhdCBQZW5qdWFsYW4iO3M6MzoidXJsIjtzOjMyOiJodHRwOi8vbG9jYWxob3N0OjgxMjMvc2FsZXNfZGF0YSI7fX19fXM6MjE6ImFkbWluX2NoYXRib3RfY29udGV4dCI7YTowOnt9fQ==',1784131605),('KFSyl5fRSzfQLMk3UQkQeZn1BtdYj4caqWkwrm8e',54,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/125.0.6422.26 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoielhwSTh0VTI3cVNubW00emxld0FnMm5RSHo5bm9WS0VjUjdtTEZLMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0cy8yNC9yZWNpcGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1NDtzOjIyOiJhZG1pbl9jaGF0Ym90X21lc3NhZ2VzIjthOjE6e2k6MDthOjU6e3M6NDoicm9sZSI7czo5OiJhc3Npc3RhbnQiO3M6NDoidGV4dCI7czoxMzA6IkhhbG8hIFNheWEgYmlzYSBiYW50dSBjZWsgc3RvaywgcGVuanVhbGFuLCBwcm9maXQsIHNoaWZ0LCBkYW4gYW5hbGlzaXMgcGVyYmFuZGluZ2FuLiBTZW11YSBqYXdhYmFuIGRpYW1iaWwgbGFuZ3N1bmcgZGFyaSBkYXRhYmFzZS4iO3M6NDoidGltZSI7czo1OiIyMzo0OSI7czo0OiJtZXRhIjthOjI6e3M6NjoiaW50ZW50IjtzOjE1OiJiYW50dWFuX2NoYXRib3QiO3M6Nzoic3VjY2VzcyI7YjoxO31zOjc6ImFjdGlvbnMiO2E6Mjp7aTowO2E6Mjp7czo1OiJsYWJlbCI7czoxMjoiTGloYXQgUHJvZHVrIjtzOjM6InVybCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3Byb2R1Y3RzIjt9aToxO2E6Mjp7czo1OiJsYWJlbCI7czoxNToiTGloYXQgUGVuanVhbGFuIjtzOjM6InVybCI7czozMjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3NhbGVzX2RhdGEiO319fX1zOjIxOiJhZG1pbl9jaGF0Ym90X2NvbnRleHQiO2E6MDp7fX0=',1784134143),('nDscF4yQtBiTmlyjCr7H2eEqyYRXWldI1umAWzNC',54,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/125.0.6422.26 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiQkNndzJmeUNtTXI4S3dYc0k1N0RURTMzeVA4bDBJdlliQnBnUmE5TSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU0O3M6MjI6ImFkbWluX2NoYXRib3RfbWVzc2FnZXMiO2E6MTp7aTowO2E6NTp7czo0OiJyb2xlIjtzOjk6ImFzc2lzdGFudCI7czo0OiJ0ZXh0IjtzOjEzMDoiSGFsbyEgU2F5YSBiaXNhIGJhbnR1IGNlayBzdG9rLCBwZW5qdWFsYW4sIHByb2ZpdCwgc2hpZnQsIGRhbiBhbmFsaXNpcyBwZXJiYW5kaW5nYW4uIFNlbXVhIGphd2FiYW4gZGlhbWJpbCBsYW5nc3VuZyBkYXJpIGRhdGFiYXNlLiI7czo0OiJ0aW1lIjtzOjU6IjIzOjQ5IjtzOjQ6Im1ldGEiO2E6Mjp7czo2OiJpbnRlbnQiO3M6MTU6ImJhbnR1YW5fY2hhdGJvdCI7czo3OiJzdWNjZXNzIjtiOjE7fXM6NzoiYWN0aW9ucyI7YToyOntpOjA7YToyOntzOjU6ImxhYmVsIjtzOjEyOiJMaWhhdCBQcm9kdWsiO3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vcHJvZHVjdHMiO31pOjE7YToyOntzOjU6ImxhYmVsIjtzOjE1OiJMaWhhdCBQZW5qdWFsYW4iO3M6MzoidXJsIjtzOjMyOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2FsZXNfZGF0YSI7fX19fXM6MjE6ImFkbWluX2NoYXRib3RfY29udGV4dCI7YTowOnt9fQ==',1784134184),('PF0HCrfiVSANzafnXVeAeBhsckA01xHq3dYvwB2m',54,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiQ2VtOFV0TUZYZDdxM0tnSmk4MTZBcFNaU2lnTTU4T0RQa3Y3dXE2ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0cy8yNi9yZWNpcGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1NDtzOjIyOiJhZG1pbl9jaGF0Ym90X21lc3NhZ2VzIjthOjE6e2k6MDthOjU6e3M6NDoicm9sZSI7czo5OiJhc3Npc3RhbnQiO3M6NDoidGV4dCI7czoxMzA6IkhhbG8hIFNheWEgYmlzYSBiYW50dSBjZWsgc3RvaywgcGVuanVhbGFuLCBwcm9maXQsIHNoaWZ0LCBkYW4gYW5hbGlzaXMgcGVyYmFuZGluZ2FuLiBTZW11YSBqYXdhYmFuIGRpYW1iaWwgbGFuZ3N1bmcgZGFyaSBkYXRhYmFzZS4iO3M6NDoidGltZSI7czo1OiIyMjoyOCI7czo0OiJtZXRhIjthOjI6e3M6NjoiaW50ZW50IjtzOjE1OiJiYW50dWFuX2NoYXRib3QiO3M6Nzoic3VjY2VzcyI7YjoxO31zOjc6ImFjdGlvbnMiO2E6Mjp7aTowO2E6Mjp7czo1OiJsYWJlbCI7czoxMjoiTGloYXQgUHJvZHVrIjtzOjM6InVybCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3Byb2R1Y3RzIjt9aToxO2E6Mjp7czo1OiJsYWJlbCI7czoxNToiTGloYXQgUGVuanVhbGFuIjtzOjM6InVybCI7czozMjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3NhbGVzX2RhdGEiO319fX1zOjIxOiJhZG1pbl9jaGF0Ym90X2NvbnRleHQiO2E6MDp7fX0=',1784137277),('pgbOCDoieP3zbyymf8W2biQevJTb1BQcFAlKOl1p',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.21459.0 Chrome/148.0.7778.271 Electron/42.5.1 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiUTcxY3NuWndmcm1iMUt6aUVIOUp0d1hMV0V0UUZaOTNzSjVzUWwzYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1784132517);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (10,'P0001','-','thaitea roasted',1,'product','Add Product',0,10,'admin','2026-07-15 17:14:56','2026-07-15 17:14:56'),(11,'P0002','-','thaitea roasted1',1,'product','Add Product',0,10,'admin','2026-07-15 17:30:48','2026-07-15 17:30:48');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'coconut','(+62) 80981273087123','jalan joko','2026-07-15 15:41:12','2026-07-15 15:41:12');
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (54,'admin','admin@gmail.com','2026-07-13 16:05:34','$2y$12$25z72/tFKTgYVmAa0CCZQ.ZOR14JW5KrzrukTm2ZpFJBWnyyrGs/2',NULL,'2026-07-13 16:05:34','2026-07-13 16:05:34'),(55,'ariz','ariz@gmail.com','2026-07-13 16:05:35','$2y$12$cuDrxW12urgT.Hq4gNpoYuf.1Vkm2Oe6L.XtoqVAfUMO/ESWraM92',NULL,'2026-07-13 16:05:35','2026-07-13 16:05:35'),(56,'koko','koko@gmail.com','2026-07-13 16:05:35','$2y$12$mWl7Jd8cz4vAIj3CJmnjS.zcrMase5eQcmJunhrHe5UvcnK1OHr3W',NULL,'2026-07-13 16:05:35','2026-07-13 16:05:35'),(57,'test','test@gmail.com','2026-07-13 17:37:47','$2y$12$Xzjz/IIJatU42u53oxe7r.bUu3fHRygX1ofhTslRA6X2pEAoMN64u',NULL,'2026-07-13 16:08:47','2026-07-13 17:37:47');
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

-- Dump completed on 2026-07-16  0:42:30
