-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2026 at 06:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kosaja_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `region_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `object_type` varchar(100) NOT NULL,
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `previous_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`previous_state`)),
  `new_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_state`)),
  `reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `actor_id`, `region_id`, `action`, `object_type`, `object_id`, `previous_state`, `new_state`, `reason`, `created_at`) VALUES
(1, 1, 5, 'VERIFY_PROVIDER', 'provider_profile', 1, '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-07 15:21:11\"}', '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-07 16:19:52\"}', 'Pengujian otomatis verifikasi identitas provider', '2026-10-07 16:19:52'),
(2, 1, 5, 'VERIFY_PROVIDER', 'provider_profile', 1, '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-07 16:19:52\"}', '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-08 15:43:07\"}', 'Pengujian otomatis verifikasi identitas provider', '2026-10-08 15:43:07'),
(6, 1, 5, 'VERIFY_PROVIDER', 'provider_profile', 1, '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-08 15:43:07\"}', '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-08 15:51:11\"}', 'Pengujian otomatis verifikasi identitas provider', '2026-10-08 15:51:11'),
(10, NULL, 5, 'EXPIRE_RESERVATION', 'reservations', 1, '{\"status\":\"active\"}', '{\"status\":\"expired\"}', 'Batas waktu pembayaran (Expiration Window) telah terlewati tanpa konfirmasi bayar.', '2026-10-08 16:21:43'),
(11, 4, 5, 'PAYMENT_SUCCESS', 'payments', 1, NULL, '{\"invoice_id\":1,\"amount_paid\":1254500,\"gateway_trx_id\":\"MID-TEST-1791477721\",\"payment_method\":\"bsi_va\",\"status\":\"success\",\"paid_at\":\"2026-10-08 16:42:01\"}', 'Pembayaran termin awal Rp 1.254.500 berhasil diverifikasi.', '2026-10-08 16:42:01'),
(12, 4, 5, 'CREATE_CONTRACT', 'contracts', 1, NULL, '{\"application_id\":\"4\",\"tenant_id\":\"4\",\"provider_id\":\"3\",\"property_id\":\"1\",\"unit_id\":\"1\",\"start_date\":\"2026-10-15\",\"end_date\":\"2027-04-15\",\"agreed_price\":\"1250000.00\",\"payment_schedule\":\"monthly\",\"status\":\"active\"}', 'Kontrak sewa resmi terbentuk otomatis setelah pembayaran pertama sukses.', '2026-10-08 16:42:01'),
(13, NULL, 5, 'RENTAL_ACTIVE', 'units', 1, '{\"status\":\"reserved\"}', '{\"status\":\"occupied\"}', 'Unit kamar Kamar A-01 (Lantai 1) kini aktif ditempati penyewa Budi Pratama (Penyewa)', '2026-10-08 16:42:01'),
(14, 4, 5, 'INVOICE_OVERDUE', 'invoices', 8, '{\"status\":\"pending\"}', '{\"status\":\"overdue\"}', 'Masa tenggang (grace period 3 hari) berakhir untuk invoice #INV-8.', '2026-10-09 15:21:54'),
(15, 4, 5, 'INVOICE_OVERDUE', 'invoices', 17, '{\"status\":\"pending\"}', '{\"status\":\"overdue\"}', 'Masa tenggang (grace period 3 hari) berakhir untuk invoice #INV-17.', '2026-10-09 15:22:12'),
(16, 4, 5, 'INVOICE_PAYMENT_SUCCESS', 'invoices', 19, '{\"status\":\"pending\"}', '{\"status\":\"paid\",\"payment_id\":2}', 'Pembayaran tagihan rutin #INV-19 sebesar Rp 1.204.500 sukses.', '2026-10-09 15:22:12'),
(17, 1, 5, 'DISPUTE_INVESTIGATING', 'disputes', 1, '{\"status\":\"opened\"}', '{\"status\":\"investigating\"}', 'Admin memulai mediasi.', '2026-10-09 15:22:12'),
(18, 1, 5, 'REFUND_PROCESSED', 'refunds', 1, NULL, '{\"amount\":250000}', 'Refund diproses', '2026-10-09 15:22:12'),
(19, 1, 5, 'DISPUTE_DECIDED', 'disputes', 1, '{\"status\":\"investigating\"}', '{\"status\":\"resolved\"}', 'Dispute selesai', '2026-10-09 15:22:12'),
(20, 4, 5, 'PAYMENT_SUCCESS', 'payments', 3, NULL, '{\"invoice_id\":20,\"amount_paid\":1254500,\"gateway_trx_id\":\"MID-TEST-1791559341\",\"payment_method\":\"bsi_va\",\"status\":\"success\",\"paid_at\":\"2026-10-09 15:22:21\"}', 'Pembayaran termin awal Rp 1.254.500 berhasil diverifikasi.', '2026-10-09 15:22:21'),
(21, 4, 5, 'CREATE_CONTRACT', 'contracts', 4, NULL, '{\"application_id\":\"7\",\"tenant_id\":\"4\",\"provider_id\":\"3\",\"property_id\":\"1\",\"unit_id\":\"1\",\"start_date\":\"2026-10-16\",\"end_date\":\"2027-04-16\",\"agreed_price\":\"1250000.00\",\"payment_schedule\":\"monthly\",\"status\":\"active\"}', 'Kontrak sewa resmi terbentuk otomatis setelah pembayaran pertama sukses.', '2026-10-09 15:22:21'),
(22, NULL, 5, 'RENTAL_ACTIVE', 'units', 1, '{\"status\":\"reserved\"}', '{\"status\":\"occupied\"}', 'Unit kamar Kamar A-01 (Lantai 1) kini aktif ditempati penyewa Budi Pratama (Penyewa)', '2026-10-09 15:22:21'),
(23, NULL, 5, 'EXPIRE_RESERVATION', 'reservations', 6, '{\"status\":\"active\"}', '{\"status\":\"expired\"}', 'Batas waktu pembayaran (Expiration Window) telah terlewati tanpa konfirmasi bayar.', '2026-10-09 15:22:29'),
(24, 1, 5, 'VERIFY_PROVIDER', 'provider_profile', 1, '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-08 15:51:11\"}', '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2027-04-07\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-09 15:23:11\"}', 'Pengujian otomatis verifikasi identitas provider', '2026-10-09 15:23:11'),
(28, 4, 5, 'INVOICE_OVERDUE', 'invoices', 32, '{\"status\":\"pending\"}', '{\"status\":\"overdue\"}', 'Masa tenggang (grace period 3 hari) berakhir untuk invoice #INV-32.', '2026-10-09 15:25:22'),
(29, 4, 5, 'INVOICE_PAYMENT_SUCCESS', 'invoices', 34, '{\"status\":\"pending\"}', '{\"status\":\"paid\",\"payment_id\":4}', 'Pembayaran tagihan rutin #INV-34 sebesar Rp 1.204.500 sukses.', '2026-10-09 15:25:22'),
(30, 1, 5, 'DISPUTE_INVESTIGATING', 'disputes', 2, '{\"status\":\"opened\"}', '{\"status\":\"investigating\"}', 'Admin memulai mediasi.', '2026-10-09 15:25:22'),
(31, 1, 5, 'REFUND_PROCESSED', 'refunds', 2, NULL, '{\"amount\":250000}', 'Refund diproses', '2026-10-09 15:25:22'),
(32, 1, 5, 'DISPUTE_DECIDED', 'disputes', 2, '{\"status\":\"investigating\"}', '{\"status\":\"resolved\"}', 'Dispute selesai', '2026-10-09 15:25:22'),
(33, 5, 5, 'PAYMENT_SUCCESS', 'payments', 5, NULL, '{\"invoice_id\":35,\"amount_paid\":1254500,\"gateway_trx_id\":\"MID-E2E-1791559713\",\"payment_method\":\"bsi_va\",\"status\":\"success\",\"paid_at\":\"2026-10-09 15:28:33\"}', 'Pembayaran termin awal Rp 1.254.500 berhasil diverifikasi.', '2026-10-09 15:28:33'),
(34, 5, 5, 'CREATE_CONTRACT', 'contracts', 6, NULL, '{\"application_id\":\"10\",\"tenant_id\":\"5\",\"provider_id\":\"3\",\"property_id\":\"1\",\"unit_id\":\"1\",\"start_date\":\"2026-10-12\",\"end_date\":\"2027-04-12\",\"agreed_price\":\"1250000.00\",\"payment_schedule\":\"monthly\",\"status\":\"active\"}', 'Kontrak sewa resmi terbentuk otomatis setelah pembayaran pertama sukses.', '2026-10-09 15:28:33'),
(35, NULL, 5, 'RENTAL_ACTIVE', 'units', 1, '{\"status\":\"reserved\"}', '{\"status\":\"occupied\"}', 'Unit kamar Kamar A-01 (Lantai 1) kini aktif ditempati penyewa Siti Aisyah (Penyewa)', '2026-10-09 15:28:33'),
(36, 5, 5, 'INVOICE_PAYMENT_SUCCESS', 'invoices', 36, '{\"status\":\"upcoming\"}', '{\"status\":\"paid\",\"payment_id\":6}', 'Pembayaran tagihan rutin #INV-36 sebesar Rp 1.254.500 sukses.', '2026-10-09 15:28:33'),
(37, 5, 5, 'PAYMENT_SUCCESS', 'payments', 7, NULL, '{\"invoice_id\":41,\"amount_paid\":1254500,\"gateway_trx_id\":\"MID-E2E-1791560856\",\"payment_method\":\"bsi_va\",\"status\":\"success\",\"paid_at\":\"2026-10-09 15:47:37\"}', 'Pembayaran termin awal Rp 1.254.500 berhasil diverifikasi.', '2026-10-09 15:47:37'),
(38, 5, 5, 'CREATE_CONTRACT', 'contracts', 7, NULL, '{\"application_id\":\"11\",\"tenant_id\":\"5\",\"provider_id\":\"3\",\"property_id\":\"1\",\"unit_id\":\"1\",\"start_date\":\"2026-10-12\",\"end_date\":\"2027-04-12\",\"agreed_price\":\"1250000.00\",\"payment_schedule\":\"monthly\",\"status\":\"active\"}', 'Kontrak sewa resmi terbentuk otomatis setelah pembayaran pertama sukses.', '2026-10-09 15:47:37'),
(39, NULL, 5, 'RENTAL_ACTIVE', 'units', 1, '{\"status\":\"reserved\"}', '{\"status\":\"occupied\"}', 'Unit kamar Kamar A-01 (Lantai 1) kini aktif ditempati penyewa Siti Aisyah (Penyewa)', '2026-10-09 15:47:37'),
(40, 5, 5, 'INVOICE_PAYMENT_SUCCESS', 'invoices', 42, '{\"status\":\"upcoming\"}', '{\"status\":\"paid\",\"payment_id\":8}', 'Pembayaran tagihan rutin #INV-42 sebesar Rp 1.254.500 sukses.', '2026-10-09 15:47:37'),
(41, 4, 5, 'INVOICE_OVERDUE', 'invoices', 53, '{\"status\":\"pending\"}', '{\"status\":\"overdue\"}', 'Masa tenggang (grace period 3 hari) berakhir untuk invoice #INV-53.', '2026-10-09 16:02:14'),
(42, 4, 5, 'INVOICE_PAYMENT_SUCCESS', 'invoices', 55, '{\"status\":\"pending\"}', '{\"status\":\"paid\",\"payment_id\":9}', 'Pembayaran tagihan rutin #INV-55 sebesar Rp 1.204.500 sukses.', '2026-10-09 16:02:14'),
(43, 1, 5, 'DISPUTE_INVESTIGATING', 'disputes', 5, '{\"status\":\"opened\"}', '{\"status\":\"investigating\"}', 'Admin memulai mediasi.', '2026-10-09 16:02:14'),
(44, 1, 5, 'REFUND_PROCESSED', 'refunds', 5, NULL, '{\"amount\":250000}', 'Refund diproses', '2026-10-09 16:02:14'),
(45, 1, 5, 'DISPUTE_DECIDED', 'disputes', 5, '{\"status\":\"investigating\"}', '{\"status\":\"resolved\"}', 'Dispute selesai', '2026-10-09 16:02:14'),
(46, 5, 5, 'PAYMENT_SUCCESS', 'payments', 10, NULL, '{\"invoice_id\":56,\"amount_paid\":1254500,\"gateway_trx_id\":\"MID-E2E-1791561739\",\"payment_method\":\"bsi_va\",\"status\":\"success\",\"paid_at\":\"2026-10-09 16:02:19\"}', 'Pembayaran termin awal Rp 1.254.500 berhasil diverifikasi.', '2026-10-09 16:02:19'),
(47, 5, 5, 'CREATE_CONTRACT', 'contracts', 9, NULL, '{\"application_id\":\"13\",\"tenant_id\":\"5\",\"provider_id\":\"3\",\"property_id\":\"1\",\"unit_id\":\"1\",\"start_date\":\"2026-10-12\",\"end_date\":\"2027-04-12\",\"agreed_price\":\"1250000.00\",\"payment_schedule\":\"monthly\",\"status\":\"active\"}', 'Kontrak sewa resmi terbentuk otomatis setelah pembayaran pertama sukses.', '2026-10-09 16:02:19'),
(48, NULL, 5, 'RENTAL_ACTIVE', 'units', 1, '{\"status\":\"reserved\"}', '{\"status\":\"occupied\"}', 'Unit kamar Kamar A-01 (Lantai 1) kini aktif ditempati penyewa Siti Aisyah (Penyewa)', '2026-10-09 16:02:19'),
(49, 5, 5, 'INVOICE_PAYMENT_SUCCESS', 'invoices', 57, '{\"status\":\"upcoming\"}', '{\"status\":\"paid\",\"payment_id\":11}', 'Pembayaran tagihan rutin #INV-57 sebesar Rp 1.254.500 sukses.', '2026-10-09 16:02:19'),
(50, 1, 5, 'USER_STATUS_TOGGLED', 'users', 5, '{\"status\":\"active\"}', '{\"status\":\"suspended\"}', 'Super Admin mengubah status akun Siti Aisyah (Penyewa) menjadi SUSPENDED', '2026-10-11 13:30:39'),
(51, 1, 5, 'MONETIZATION_POLICY_UPDATED', 'system_config', 1, NULL, '{\"free_period_months\":3,\"listing_fee\":10000,\"gateway_fee\":4500}', 'Super Admin memperbarui skema monetisasi platform: Free Period 3 bulan, Listing Fee Rp 10.000, Gateway Fee Rp 4.500', '2026-10-11 13:33:34'),
(52, 1, 5, 'MONETIZATION_POLICY_UPDATED', 'system_config', 1, NULL, '{\"free_period_months\":6,\"listing_fee\":10000,\"gateway_fee\":4500}', 'Super Admin memperbarui skema monetisasi platform: Free Period 6 bulan, Listing Fee Rp 10.000, Gateway Fee Rp 4.500', '2026-10-11 13:33:53'),
(53, 1, 5, 'USER_CREATED', 'users', 10, NULL, '{\"name\":\"Admin Daerah Kota Yogyakarta\",\"email\":\"admin.jogja@kosaja.id\",\"role\":\"admin_daerah\",\"region_id\":3}', 'Super Admin mendaftarkan akun baru: Admin Daerah Kota Yogyakarta (Peran: ADMIN_DAERAH, Wilayah: Kota Yogyakarta)', '2026-10-11 14:02:20'),
(54, 1, 5, 'USER_CREATED', 'users', 19, NULL, '{\"name\":\"Naufal Mubarak\",\"email\":\"yusmadi23@gmail.com\",\"role\":\"admin_daerah\",\"region_id\":9}', 'Super Admin mendaftarkan akun baru: Naufal Mubarak (Peran: ADMIN_DAERAH, Wilayah: Banda Aceh)', '2026-10-11 15:42:29'),
(55, 1, 5, 'ADMIN_REGION_ASSIGNED', 'users', 19, '{\"region_id\":\"9\",\"region_name\":\"Banda Aceh\"}', '{\"region_id\":10,\"region_name\":\"Aceh Besar\"}', 'Super Admin memperbarui wilayah tugas Naufal Mubarak dari [Banda Aceh] menjadi [Aceh Besar]', '2026-10-11 15:42:50'),
(56, 1, 5, 'ADMIN_REGION_ASSIGNED', 'users', 19, '{\"region_id\":\"10\",\"region_name\":\"Aceh Besar\"}', '{\"region_id\":9,\"region_name\":\"Banda Aceh\"}', 'Super Admin memperbarui wilayah tugas Naufal Mubarak dari [Aceh Besar] menjadi [Banda Aceh]', '2026-10-11 15:43:00'),
(57, NULL, 5, 'EXPIRE_RESERVATION', 'reservations', 2, '{\"status\":\"active\"}', '{\"status\":\"expired\"}', 'Batas waktu pembayaran (Expiration Window) telah terlewati tanpa konfirmasi bayar.', '2026-10-12 15:04:50'),
(58, NULL, 5, 'EXPIRE_RESERVATION', 'reservations', 3, '{\"status\":\"active\"}', '{\"status\":\"expired\"}', 'Batas waktu pembayaran (Expiration Window) telah terlewati tanpa konfirmasi bayar.', '2026-10-12 15:04:50'),
(59, 1, NULL, 'USER_ROLE_SWITCHED', 'users', 1, NULL, '{\"role\":\"super_admin\"}', 'Beralih role ke akun: Super Administrator (SUPER ADMIN)', '2026-10-12 22:29:31'),
(60, 1, NULL, 'USER_ROLE_SWITCHED', 'users', 1, NULL, '{\"role\":\"super_admin\"}', 'Beralih role ke akun: Super Administrator (SUPER ADMIN)', '2026-10-12 22:30:30'),
(61, 2, 2, 'USER_ROLE_SWITCHED', 'users', 2, NULL, '{\"role\":\"admin_daerah\"}', 'Beralih role ke akun: Admin Daerah Sleman (ADMIN DAERAH)', '2026-10-12 22:31:14'),
(62, 19, 9, 'USER_LOGOUT', 'users', 19, NULL, NULL, 'Pengguna Naufal Mubarak (Banda Aceh) (ADMIN DAERAH) keluar (logout) dari sistem.', '2026-10-12 22:34:20'),
(63, 19, 9, 'USER_LOGIN', 'users', 19, NULL, '{\"role\":\"admin_daerah\",\"ip\":\"::1\"}', 'Pengguna Naufal Mubarak (Banda Aceh) (ADMIN DAERAH) berhasil masuk ke sistem KosAja.', '2026-10-12 22:34:42'),
(64, 1, 5, 'VERIFY_PROVIDER', 'provider_profile', 1, '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2026-10-15\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-09 15:23:11\"}', '{\"id\":\"1\",\"user_id\":\"3\",\"verification_status\":\"verified\",\"rejection_reason\":null,\"id_card_photo\":\"ktp_ahmad.jpg\",\"bank_name\":\"Bank Syariah Indonesia (BSI)\",\"bank_account_number\":\"7123456789\",\"free_period_end\":\"2026-10-15\",\"created_at\":\"2026-10-07 15:21:11\",\"updated_at\":\"2026-10-12 23:31:47\"}', 'Pengujian otomatis verifikasi identitas provider', '2026-10-12 23:31:47'),
(65, 5, 5, 'PAYMENT_SUCCESS', 'payments', 12, NULL, '{\"invoice_id\":62,\"amount_paid\":1254500,\"gateway_trx_id\":\"MID-E2E-1791822738\",\"payment_method\":\"bsi_va\",\"status\":\"success\",\"paid_at\":\"2026-10-12 23:32:18\"}', 'Pembayaran termin awal Rp 1.254.500 berhasil diverifikasi.', '2026-10-12 23:32:18'),
(66, 5, 5, 'CREATE_CONTRACT', 'contracts', 10, NULL, '{\"application_id\":\"14\",\"tenant_id\":\"5\",\"provider_id\":\"3\",\"property_id\":\"1\",\"unit_id\":\"1\",\"start_date\":\"2026-10-15\",\"end_date\":\"2027-04-15\",\"agreed_price\":\"1250000.00\",\"payment_schedule\":\"monthly\",\"status\":\"active\"}', 'Kontrak sewa resmi terbentuk otomatis setelah pembayaran pertama sukses.', '2026-10-12 23:32:18'),
(67, NULL, 5, 'RENTAL_ACTIVE', 'units', 1, '{\"status\":\"reserved\"}', '{\"status\":\"occupied\"}', 'Unit kamar Kamar A-01 (Lantai 1) kini aktif ditempati penyewa Siti Aisyah (Penyewa)', '2026-10-12 23:32:18'),
(68, 3, NULL, 'PROVIDER_FREE_PERIOD_CONVERTED', 'provider_profiles', 3, NULL, '{\"first_contract_id\":10,\"first_invoice_id\":62}', 'Masa Free Period onboarding provider selesai lebih awal karena transaksi sewa pertama berhasil lunas (FR-PM-002). Masa transisi 3 hari aktif (FR-PM-004).', '2026-10-12 23:32:18'),
(69, 4, 5, 'INVOICE_OVERDUE', 'invoices', 9, '{\"status\":\"pending\"}', '{\"status\":\"overdue\"}', 'Masa tenggang (grace period 3 hari) berakhir untuk invoice #INV-9.', '2026-10-12 23:32:18'),
(70, 5, 5, 'INVOICE_PAYMENT_SUCCESS', 'invoices', 63, '{\"status\":\"upcoming\"}', '{\"status\":\"paid\",\"payment_id\":13}', 'Pembayaran tagihan rutin #INV-63 sebesar Rp 1.254.500 sukses.', '2026-10-12 23:32:18');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `agreed_price` decimal(15,2) NOT NULL,
  `payment_schedule` enum('full_upfront','monthly','3_monthly','6_monthly','yearly') NOT NULL DEFAULT 'monthly',
  `status` enum('active','completed','terminated') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `application_id`, `tenant_id`, `provider_id`, `property_id`, `unit_id`, `start_date`, `end_date`, `agreed_price`, `payment_schedule`, `status`, `created_at`, `updated_at`) VALUES
(2, 5, 4, 3, 1, 1, '2026-10-09', '2027-04-09', 1250000.00, 'monthly', 'active', '2026-10-09 15:21:54', '2026-10-09 15:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `disputes`
--

CREATE TABLE `disputes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `raised_by_id` bigint(20) UNSIGNED NOT NULL,
  `against_user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `evidence_url` varchar(255) DEFAULT NULL,
  `status` enum('opened','investigating','resolved','closed') NOT NULL DEFAULT 'opened',
  `is_escalated` tinyint(1) DEFAULT 0,
  `escalation_notes` text DEFAULT NULL,
  `escalated_at` datetime DEFAULT NULL,
  `decision` enum('none','no_refund','partial_refund','full_refund') NOT NULL DEFAULT 'none',
  `decision_notes` text DEFAULT NULL,
  `decided_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `decided_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `rental_amount` decimal(15,2) NOT NULL,
  `gateway_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('upcoming','pending','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `contract_id`, `tenant_id`, `rental_amount`, `gateway_fee`, `total_amount`, `due_date`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 4, 1250000.00, 4500.00, 1254500.00, '2026-10-09', 'paid', '2026-10-09 15:21:54', '2026-10-09 15:21:54'),
(3, 2, 4, 1250000.00, 4500.00, 1254500.00, '2026-11-09', 'upcoming', '2026-10-09 15:21:54', '2026-10-09 15:21:54'),
(4, 2, 4, 1250000.00, 4500.00, 1254500.00, '2026-12-09', 'upcoming', '2026-10-09 15:21:54', '2026-10-09 15:21:54'),
(5, 2, 4, 1250000.00, 4500.00, 1254500.00, '2027-01-09', 'upcoming', '2026-10-09 15:21:54', '2026-10-09 15:21:54'),
(6, 2, 4, 1250000.00, 4500.00, 1254500.00, '2027-02-09', 'upcoming', '2026-10-09 15:21:54', '2026-10-09 15:21:54'),
(7, 2, 4, 1250000.00, 4500.00, 1254500.00, '2027-03-09', 'upcoming', '2026-10-09 15:21:54', '2026-10-09 15:21:54'),
(8, 2, 4, 1000000.00, 4500.00, 1004500.00, '2026-10-04', 'overdue', '2026-10-09 15:21:54', '2026-10-09 15:21:54'),
(9, 2, 4, 1000000.00, 4500.00, 1004500.00, '2026-10-07', 'overdue', '2026-10-09 15:21:54', '2026-10-12 23:32:18'),
(10, 2, 4, 1200000.00, 4500.00, 1204500.00, '2026-10-12', 'pending', '2026-10-09 15:21:54', '2026-10-09 15:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price_monthly` decimal(15,2) DEFAULT NULL,
  `price_yearly` decimal(15,2) DEFAULT NULL,
  `publication_status` enum('draft','pending_review','published','inactive') NOT NULL DEFAULT 'draft',
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `unit_id`, `title`, `description`, `price_monthly`, `price_yearly`, `publication_status`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kamar Nyaman AC Kamar Mandi Dalam Dekat Kampus UGM Pogung', 'Kamar kos putri luas 3x4 meter dilengkapi tempat tidur spring bed, lemari pakaian, meja belajar, AC, dan kamar mandi dalam dengan shower.', 1250000.00, 14000000.00, 'published', NULL, '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(2, 3, 'Kos Putra Mewah Seturan Fasilitas Lengkap Dekat UPN & YKPN', 'Kamar kos putra eksklusif full furnished, AC, Water Heater, Smart TV, WiFi kencang, parkir mobil leluasa.', 1750000.00, 19500000.00, 'published', NULL, '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(3, 4, 'Kontrakan Rumah Paviliun 2 KT Nyaman & Asri di Jombor Sleman', 'Rumah sewa siap huni cocok untuk pasutri baru. Lingkungan perumahan tenang, dekat Jalan Magelang dan Jogja City Mall.', 2200000.00, 24000000.00, 'published', NULL, '2026-10-07 15:21:11', '2026-10-07 15:21:11');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-10-07-000001', 'App\\Database\\Migrations\\CreateUsersAndProfilesTable', 'default', 'App', 1791386394, 1),
(2, '2026-10-07-000002', 'App\\Database\\Migrations\\CreateRegionsAndPropertiesTable', 'default', 'App', 1791386394, 1),
(3, '2026-10-07-000003', 'App\\Database\\Migrations\\CreateUnitsAndListingsTable', 'default', 'App', 1791386394, 1),
(4, '2026-10-07-000004', 'App\\Database\\Migrations\\CreateTransactionsAndBookingsTable', 'default', 'App', 1791386394, 1),
(5, '2026-10-07-000005', 'App\\Database\\Migrations\\CreateContractsAndInvoicesTable', 'default', 'App', 1791386394, 1),
(6, '2026-10-07-000006', 'App\\Database\\Migrations\\CreateAuditLogsTable', 'default', 'App', 1791386394, 1),
(7, '2026-10-07-000007', 'App\\Database\\Migrations\\CreateFavoritesTable', 'default', 'App', 1791474227, 2),
(8, '2026-10-08-000008', 'App\\Database\\Migrations\\AddPasutriAndNotesToRentalApplications', 'default', 'App', 1791475886, 3),
(9, '2026-10-08-000009', 'App\\Database\\Migrations\\CreateNotificationLogsTable', 'default', 'App', 1791477230, 4),
(10, '2026-10-09-000010', 'App\\Database\\Migrations\\CreateDisputesAndRefundsTable', 'default', 'App', 1791558766, 5),
(11, '2026-10-09-000011', 'App\\Database\\Migrations\\AddRegionIdToUsersAndCreateReportsAndSettlementsTable', 'default', 'App', 1791560916, 6),
(12, '2026-10-12-000012', 'App\\Database\\Migrations\\AddRegionIdToAuditLogsTable', 'default', 'App', 1791818479, 7),
(13, '2026-10-12-000013', 'App\\Database\\Migrations\\BackfillAuditLogsRegionId', 'default', 'App', 1791818531, 8),
(14, '2026-10-12-000014', 'App\\Database\\Migrations\\AlignProviderFreePeriodTo30Days', 'default', 'App', 1791822669, 9);

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recipient_phone` varchar(30) NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_role` varchar(50) DEFAULT NULL,
  `event_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','simulated','failed') NOT NULL DEFAULT 'simulated',
  `channel` varchar(50) NOT NULL DEFAULT 'whatsapp',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_logs`
--

INSERT INTO `notification_logs` (`id`, `recipient_phone`, `recipient_name`, `recipient_role`, `event_type`, `message`, `status`, `channel`, `created_at`) VALUES
(1, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_approved', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nKabar gembira! Pengajuan sewa Anda untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah *DISETUJUI* oleh pemilik.\n\n🔒 Unit kamar saat ini telah dikunci khusus untuk Anda (*RESERVED*).\n⏰ Batas Waktu Pembayaran: *09 Oct 2026, 16:40 WIB*.\n\nSilakan selesaikan proses checkout dan pembayaran sewa melalui tautan berikut:\n👉 http://localhost:8080/checkout/2\n\nTerima kasih telah memilih hunian di KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:40:56'),
(2, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_rejected', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTerima kasih telah mengajukan sewa untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\nMohon maaf, permohonan Anda saat ini *BELUM DAPAT DITERIMA* oleh pemilik hunian dengan alasan:\n💬 \"_Kamar sedang dalam perbaikan AC_\"\n\nAnda dapat mencari alternatif hunian lain yang siap huni di KosAja:\n👉 http://localhost:8080/search\n\nSemoga lekas menemukan hunian yang cocok!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:40:56'),
(3, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_approved', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nKabar gembira! Pengajuan sewa Anda untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah *DISETUJUI* oleh pemilik.\n\n🔒 Unit kamar saat ini telah dikunci khusus untuk Anda (*RESERVED*).\n⏰ Batas Waktu Pembayaran: *09 Oct 2026, 16:41 WIB*.\n\nSilakan selesaikan proses checkout dan pembayaran sewa melalui tautan berikut:\n👉 http://localhost:8080/checkout/3\n\nTerima kasih telah memilih hunian di KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:41:44'),
(4, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_rejected', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTerima kasih telah mengajukan sewa untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\nMohon maaf, permohonan Anda saat ini *BELUM DAPAT DITERIMA* oleh pemilik hunian dengan alasan:\n💬 \"_Kamar sedang dalam perbaikan AC_\"\n\nAnda dapat mencari alternatif hunian lain yang siap huni di KosAja:\n👉 http://localhost:8080/search\n\nSemoga lekas menemukan hunian yang cocok!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:41:44'),
(5, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_approved', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nKabar gembira! Pengajuan sewa Anda untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah *DISETUJUI* oleh pemilik.\n\n🔒 Unit kamar saat ini telah dikunci khusus untuk Anda (*RESERVED*).\n⏰ Batas Waktu Pembayaran: *09 Oct 2026, 16:42 WIB*.\n\nSilakan selesaikan proses checkout dan pembayaran sewa melalui tautan berikut:\n👉 http://localhost:8080/checkout/4\n\nTerima kasih telah memilih hunian di KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:42:01'),
(6, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_rejected', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTerima kasih telah mengajukan sewa untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\nMohon maaf, permohonan Anda saat ini *BELUM DAPAT DITERIMA* oleh pemilik hunian dengan alasan:\n💬 \"_Kamar sedang dalam perbaikan AC_\"\n\nAnda dapat mencari alternatif hunian lain yang siap huni di KosAja:\n👉 http://localhost:8080/search\n\nSemoga lekas menemukan hunian yang cocok!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:42:01'),
(7, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'payment_success', '*KosAja Notification*\n\nAlhamdulillah! Pembayaran sewa Anda sebesar *Rp 1.254.500* telah *BERHASIL DITERIMA*.\n\n📄 Kontrak Sewa Resmi *#CTR-1* kini telah *AKTIF*.\n🏠 Properti: *Kos Muslim Putri Barokah Pogung* — Kamar *Kamar A-01 (Lantai 1)*\n📅 Periode Sewa: *15 Oct 2026 s/d 15 Apr 2027*\n\nSurat perjanjian sewa digital Anda dapat diakses dan diunduh di:\n👉 http://localhost:8080/contracts/1\n\nSelamat menempati hunian baru Anda bersama KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:42:01'),
(8, '6281234567892', 'Haji Ahmad Santoso (Penyedia)', 'provider', 'tenant_rental_active', '*KosAja Notification*\n\nHalo *Haji Ahmad Santoso (Penyedia)*,\n\nPenyewa baru atas nama *Budi Pratama (Penyewa)* telah berhasil menyelesaikan pembayaran sewa pertama sebesar *Rp 1.250.000* untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\n✅ Status Unit: *TERISI (OCCUPIED / ACTIVE RENTAL)*\n📄 Kontrak Aktif: *#CTR-1*\n\nSilakan pantau rincian kontrak dan jadwal serah terima kunci di Dasbor KosAja:\n👉 http://localhost:8080/contracts/1\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-08 16:42:01'),
(9, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_overdue', '*KosAja Notification*\n\n🚨 *PEMBERITAHUAN TAGIHAN OVERDUE*\n\nHalo *Budi Pratama (Penyewa)*,\n\nMasa tenggang (Grace Period 3 hari) untuk tagihan sewa kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah berakhir.\n\nStatus tagihan Anda kini resmi menjadi *OVERDUE (TUNGGAKAN)*.\nNominal: *Rp 1.004.500*\nJatuh Tempo Semula: *04 Oct 2026*\n\nMohon segera lakukan pelunasan hari ini guna menghindari penghentian sewa sepihak oleh pengelola:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:21:54'),
(10, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:21:54'),
(11, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_reminder_h3', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nPengingat dari KosAja: Tagihan sewa kamar Anda untuk *Kos Muslim Putri Barokah Pogung* (Kamar *Kamar A-01 (Lantai 1)*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *12 Oct 2026*.\n\n💰 Jumlah Tagihan: *Rp 1.204.500*\n📅 Batas Waktu: *12 Oct 2026*\n\nUntuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\nTerima kasih atas kerja sama Anda!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:21:54'),
(12, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:12'),
(13, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_overdue', '*KosAja Notification*\n\n🚨 *PEMBERITAHUAN TAGIHAN OVERDUE*\n\nHalo *Budi Pratama (Penyewa)*,\n\nMasa tenggang (Grace Period 3 hari) untuk tagihan sewa kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah berakhir.\n\nStatus tagihan Anda kini resmi menjadi *OVERDUE (TUNGGAKAN)*.\nNominal: *Rp 1.004.500*\nJatuh Tempo Semula: *04 Oct 2026*\n\nMohon segera lakukan pelunasan hari ini guna menghindari penghentian sewa sepihak oleh pengelola:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:12'),
(14, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:12'),
(15, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_reminder_h3', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nPengingat dari KosAja: Tagihan sewa kamar Anda untuk *Kos Muslim Putri Barokah Pogung* (Kamar *Kamar A-01 (Lantai 1)*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *12 Oct 2026*.\n\n💰 Jumlah Tagihan: *Rp 1.204.500*\n📅 Batas Waktu: *12 Oct 2026*\n\nUntuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\nTerima kasih atas kerja sama Anda!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:12'),
(16, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_reminder_h3', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nPengingat dari KosAja: Tagihan sewa kamar Anda untuk *Kos Muslim Putri Barokah Pogung* (Kamar *Kamar A-01 (Lantai 1)*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *12 Oct 2026*.\n\n💰 Jumlah Tagihan: *Rp 1.204.500*\n📅 Batas Waktu: *12 Oct 2026*\n\nUntuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\nTerima kasih atas kerja sama Anda!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:12'),
(17, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'dispute_resolved', '*KosAja Notification*\n\n⚖️ *Keputusan Dispute & Komplain KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nLaporan dispute Anda terkait kontrak *#CTR-3* (Kos Muslim Putri Barokah Pogung - Kamar A-01 (Lantai 1)) telah ditinjau dan diputuskan oleh Administrator Backoffice:\n\n📋 *Status:* RESOLVED\n🏛️ *Keputusan:* *Pengembalian Dana Sebagian (Partial Refund)*\n💬 *Catatan Admin:* \"_Kompensasi disetujui sebesar Rp 250.000._\"\n\n💵 *Nominal Refund:* *Rp 250.000*\nDana telah diproses kembali ke metode pembayaran Anda.\n\nTerima kasih telah mempercayakan kenyamanan dan keadilan sewa di KosAja.\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:12'),
(18, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_approved', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nKabar gembira! Pengajuan sewa Anda untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah *DISETUJUI* oleh pemilik.\n\n🔒 Unit kamar saat ini telah dikunci khusus untuk Anda (*RESERVED*).\n⏰ Batas Waktu Pembayaran: *10 Oct 2026, 15:22 WIB*.\n\nSilakan selesaikan proses checkout dan pembayaran sewa melalui tautan berikut:\n👉 http://localhost:8080/checkout/5\n\nTerima kasih telah memilih hunian di KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:21'),
(19, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'application_rejected', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTerima kasih telah mengajukan sewa untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\nMohon maaf, permohonan Anda saat ini *BELUM DAPAT DITERIMA* oleh pemilik hunian dengan alasan:\n💬 \"_Kamar sedang dalam perbaikan AC_\"\n\nAnda dapat mencari alternatif hunian lain yang siap huni di KosAja:\n👉 http://localhost:8080/search\n\nSemoga lekas menemukan hunian yang cocok!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:21'),
(20, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'payment_success', '*KosAja Notification*\n\nAlhamdulillah! Pembayaran sewa Anda sebesar *Rp 1.254.500* telah *BERHASIL DITERIMA*.\n\n📄 Kontrak Sewa Resmi *#CTR-4* kini telah *AKTIF*.\n🏠 Properti: *Kos Muslim Putri Barokah Pogung* — Kamar *Kamar A-01 (Lantai 1)*\n📅 Periode Sewa: *16 Oct 2026 s/d 16 Apr 2027*\n\nSurat perjanjian sewa digital Anda dapat diakses dan diunduh di:\n👉 http://localhost:8080/contracts/4\n\nSelamat menempati hunian baru Anda bersama KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:21'),
(21, '6281234567892', 'Haji Ahmad Santoso (Penyedia)', 'provider', 'tenant_rental_active', '*KosAja Notification*\n\nHalo *Haji Ahmad Santoso (Penyedia)*,\n\nPenyewa baru atas nama *Budi Pratama (Penyewa)* telah berhasil menyelesaikan pembayaran sewa pertama sebesar *Rp 1.250.000* untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\n✅ Status Unit: *TERISI (OCCUPIED / ACTIVE RENTAL)*\n📄 Kontrak Aktif: *#CTR-4*\n\nSilakan pantau rincian kontrak dan jadwal serah terima kunci di Dasbor KosAja:\n👉 http://localhost:8080/contracts/4\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:22:21'),
(22, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:25:22'),
(23, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_overdue', '*KosAja Notification*\n\n🚨 *PEMBERITAHUAN TAGIHAN OVERDUE*\n\nHalo *Budi Pratama (Penyewa)*,\n\nMasa tenggang (Grace Period 3 hari) untuk tagihan sewa kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah berakhir.\n\nStatus tagihan Anda kini resmi menjadi *OVERDUE (TUNGGAKAN)*.\nNominal: *Rp 1.004.500*\nJatuh Tempo Semula: *04 Oct 2026*\n\nMohon segera lakukan pelunasan hari ini guna menghindari penghentian sewa sepihak oleh pengelola:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:25:22'),
(24, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:25:22'),
(25, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_reminder_h3', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nPengingat dari KosAja: Tagihan sewa kamar Anda untuk *Kos Muslim Putri Barokah Pogung* (Kamar *Kamar A-01 (Lantai 1)*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *12 Oct 2026*.\n\n💰 Jumlah Tagihan: *Rp 1.204.500*\n📅 Batas Waktu: *12 Oct 2026*\n\nUntuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\nTerima kasih atas kerja sama Anda!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:25:22'),
(26, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_reminder_h3', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nPengingat dari KosAja: Tagihan sewa kamar Anda untuk *Kos Muslim Putri Barokah Pogung* (Kamar *Kamar A-01 (Lantai 1)*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *12 Oct 2026*.\n\n💰 Jumlah Tagihan: *Rp 1.204.500*\n📅 Batas Waktu: *12 Oct 2026*\n\nUntuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\nTerima kasih atas kerja sama Anda!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:25:22'),
(27, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'dispute_resolved', '*KosAja Notification*\n\n⚖️ *Keputusan Dispute & Komplain KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nLaporan dispute Anda terkait kontrak *#CTR-5* (Kos Muslim Putri Barokah Pogung - Kamar A-01 (Lantai 1)) telah ditinjau dan diputuskan oleh Administrator Backoffice:\n\n📋 *Status:* RESOLVED\n🏛️ *Keputusan:* *Pengembalian Dana Sebagian (Partial Refund)*\n💬 *Catatan Admin:* \"_Kompensasi disetujui sebesar Rp 250.000._\"\n\n💵 *Nominal Refund:* *Rp 250.000*\nDana telah diproses kembali ke metode pembayaran Anda.\n\nTerima kasih telah mempercayakan kenyamanan dan keadilan sewa di KosAja.\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:25:22'),
(28, '6281234567894', 'Siti Aisyah (Penyewa)', 'tenant', 'payment_success', '*KosAja Notification*\n\nAlhamdulillah! Pembayaran sewa Anda sebesar *Rp 1.254.500* telah *BERHASIL DITERIMA*.\n\n📄 Kontrak Sewa Resmi *#CTR-6* kini telah *AKTIF*.\n🏠 Properti: *Kos Muslim Putri Barokah Pogung* — Kamar *Kamar A-01 (Lantai 1)*\n📅 Periode Sewa: *12 Oct 2026 s/d 12 Apr 2027*\n\nSurat perjanjian sewa digital Anda dapat diakses dan diunduh di:\n👉 http://localhost:8080/contracts/6\n\nSelamat menempati hunian baru Anda bersama KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:28:33'),
(29, '6281234567892', 'Haji Ahmad Santoso (Penyedia)', 'provider', 'tenant_rental_active', '*KosAja Notification*\n\nHalo *Haji Ahmad Santoso (Penyedia)*,\n\nPenyewa baru atas nama *Siti Aisyah (Penyewa)* telah berhasil menyelesaikan pembayaran sewa pertama sebesar *Rp 1.250.000* untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\n✅ Status Unit: *TERISI (OCCUPIED / ACTIVE RENTAL)*\n📄 Kontrak Aktif: *#CTR-6*\n\nSilakan pantau rincian kontrak dan jadwal serah terima kunci di Dasbor KosAja:\n👉 http://localhost:8080/contracts/6\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:28:33'),
(30, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:28:33'),
(31, '6281234567894', 'Siti Aisyah (Penyewa)', 'tenant', 'payment_success', '*KosAja Notification*\n\nAlhamdulillah! Pembayaran sewa Anda sebesar *Rp 1.254.500* telah *BERHASIL DITERIMA*.\n\n📄 Kontrak Sewa Resmi *#CTR-7* kini telah *AKTIF*.\n🏠 Properti: *Kos Muslim Putri Barokah Pogung* — Kamar *Kamar A-01 (Lantai 1)*\n📅 Periode Sewa: *12 Oct 2026 s/d 12 Apr 2027*\n\nSurat perjanjian sewa digital Anda dapat diakses dan diunduh di:\n👉 http://localhost:8080/contracts/7\n\nSelamat menempati hunian baru Anda bersama KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:47:37'),
(32, '6281234567892', 'Haji Ahmad Santoso (Penyedia)', 'provider', 'tenant_rental_active', '*KosAja Notification*\n\nHalo *Haji Ahmad Santoso (Penyedia)*,\n\nPenyewa baru atas nama *Siti Aisyah (Penyewa)* telah berhasil menyelesaikan pembayaran sewa pertama sebesar *Rp 1.250.000* untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\n✅ Status Unit: *TERISI (OCCUPIED / ACTIVE RENTAL)*\n📄 Kontrak Aktif: *#CTR-7*\n\nSilakan pantau rincian kontrak dan jadwal serah terima kunci di Dasbor KosAja:\n👉 http://localhost:8080/contracts/7\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:47:37'),
(33, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 15:47:37'),
(34, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:14'),
(35, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_overdue', '*KosAja Notification*\n\n🚨 *PEMBERITAHUAN TAGIHAN OVERDUE*\n\nHalo *Budi Pratama (Penyewa)*,\n\nMasa tenggang (Grace Period 3 hari) untuk tagihan sewa kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah berakhir.\n\nStatus tagihan Anda kini resmi menjadi *OVERDUE (TUNGGAKAN)*.\nNominal: *Rp 1.004.500*\nJatuh Tempo Semula: *04 Oct 2026*\n\nMohon segera lakukan pelunasan hari ini guna menghindari penghentian sewa sepihak oleh pengelola:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:14'),
(36, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:14'),
(37, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_reminder_h3', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nPengingat dari KosAja: Tagihan sewa kamar Anda untuk *Kos Muslim Putri Barokah Pogung* (Kamar *Kamar A-01 (Lantai 1)*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *12 Oct 2026*.\n\n💰 Jumlah Tagihan: *Rp 1.204.500*\n📅 Batas Waktu: *12 Oct 2026*\n\nUntuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\nTerima kasih atas kerja sama Anda!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:14'),
(38, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_reminder_h3', '*KosAja Notification*\n\nHalo *Budi Pratama (Penyewa)*,\n\nPengingat dari KosAja: Tagihan sewa kamar Anda untuk *Kos Muslim Putri Barokah Pogung* (Kamar *Kamar A-01 (Lantai 1)*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *12 Oct 2026*.\n\n💰 Jumlah Tagihan: *Rp 1.204.500*\n📅 Batas Waktu: *12 Oct 2026*\n\nUntuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\nTerima kasih atas kerja sama Anda!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:14'),
(39, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'dispute_resolved', '*KosAja Notification*\n\n⚖️ *Keputusan Dispute & Komplain KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nLaporan dispute Anda terkait kontrak *#CTR-8* (Kos Muslim Putri Barokah Pogung - Kamar A-01 (Lantai 1)) telah ditinjau dan diputuskan oleh Administrator Backoffice:\n\n📋 *Status:* RESOLVED\n🏛️ *Keputusan:* *Pengembalian Dana Sebagian (Partial Refund)*\n💬 *Catatan Admin:* \"_Kompensasi disetujui sebesar Rp 250.000._\"\n\n💵 *Nominal Refund:* *Rp 250.000*\nDana telah diproses kembali ke metode pembayaran Anda.\n\nTerima kasih telah mempercayakan kenyamanan dan keadilan sewa di KosAja.\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:14'),
(40, '6281234567894', 'Siti Aisyah (Penyewa)', 'tenant', 'payment_success', '*KosAja Notification*\n\nAlhamdulillah! Pembayaran sewa Anda sebesar *Rp 1.254.500* telah *BERHASIL DITERIMA*.\n\n📄 Kontrak Sewa Resmi *#CTR-9* kini telah *AKTIF*.\n🏠 Properti: *Kos Muslim Putri Barokah Pogung* — Kamar *Kamar A-01 (Lantai 1)*\n📅 Periode Sewa: *12 Oct 2026 s/d 12 Apr 2027*\n\nSurat perjanjian sewa digital Anda dapat diakses dan diunduh di:\n👉 http://localhost:8080/contracts/9\n\nSelamat menempati hunian baru Anda bersama KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:19'),
(41, '6281234567892', 'Haji Ahmad Santoso (Penyedia)', 'provider', 'tenant_rental_active', '*KosAja Notification*\n\nHalo *Haji Ahmad Santoso (Penyedia)*,\n\nPenyewa baru atas nama *Siti Aisyah (Penyewa)* telah berhasil menyelesaikan pembayaran sewa pertama sebesar *Rp 1.250.000* untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\n✅ Status Unit: *TERISI (OCCUPIED / ACTIVE RENTAL)*\n📄 Kontrak Aktif: *#CTR-9*\n\nSilakan pantau rincian kontrak dan jadwal serah terima kunci di Dasbor KosAja:\n👉 http://localhost:8080/contracts/9\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:19'),
(42, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_grace_period_warning', '*KosAja Notification*\n\n⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\nHalo *Budi Pratama (Penyewa)*,\n\nTagihan sewa Anda sebesar *Rp 1.004.500* telah melewati tanggal jatuh tempo (07 Oct 2026).\nSaat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *2 hari* sebelum tagihan berstatus OVERDUE.\n\nSegera selesaikan pembayaran agar hunian tetap nyaman:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-09 16:02:19'),
(43, '6281234567894', 'Siti Aisyah (Penyewa)', 'tenant', 'payment_success', '*KosAja Notification*\n\nAlhamdulillah! Pembayaran sewa Anda sebesar *Rp 1.254.500* telah *BERHASIL DITERIMA*.\n\n📄 Kontrak Sewa Resmi *#CTR-10* kini telah *AKTIF*.\n🏠 Properti: *Kos Muslim Putri Barokah Pogung* — Kamar *Kamar A-01 (Lantai 1)*\n📅 Periode Sewa: *15 Oct 2026 s/d 15 Apr 2027*\n\nSurat perjanjian sewa digital Anda dapat diakses dan diunduh di:\n👉 http://localhost:8080/contracts/10\n\nSelamat menempati hunian baru Anda bersama KosAja!\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-12 23:32:18'),
(44, '6281234567892', 'Haji Ahmad Santoso (Penyedia)', 'provider', 'tenant_rental_active', '*KosAja Notification*\n\nHalo *Haji Ahmad Santoso (Penyedia)*,\n\nPenyewa baru atas nama *Siti Aisyah (Penyewa)* telah berhasil menyelesaikan pembayaran sewa pertama sebesar *Rp 1.250.000* untuk kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung*.\n\n✅ Status Unit: *TERISI (OCCUPIED / ACTIVE RENTAL)*\n📄 Kontrak Aktif: *#CTR-10*\n\nSilakan pantau rincian kontrak dan jadwal serah terima kunci di Dasbor KosAja:\n👉 http://localhost:8080/contracts/10\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-12 23:32:18'),
(45, '6281234567893', 'Budi Pratama (Penyewa)', 'tenant', 'invoice_overdue', '*KosAja Notification*\n\n🚨 *PEMBERITAHUAN TAGIHAN OVERDUE*\n\nHalo *Budi Pratama (Penyewa)*,\n\nMasa tenggang (Grace Period 3 hari) untuk tagihan sewa kamar *Kamar A-01 (Lantai 1)* di *Kos Muslim Putri Barokah Pogung* telah berakhir.\n\nStatus tagihan Anda kini resmi menjadi *OVERDUE (TUNGGAKAN)*.\nNominal: *Rp 1.004.500*\nJatuh Tempo Semula: *07 Oct 2026*\n\nMohon segera lakukan pelunasan hari ini guna menghindari penghentian sewa sepihak oleh pengelola:\n👉 http://localhost:8080/tenant/dashboard#invoices\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._', 'simulated', 'whatsapp', '2026-10-12 23:32:18');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL,
  `gateway_trx_id` varchar(255) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `status` enum('pending','success','failed','refunded') NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `region_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('kos','kontrakan','rumah_sewa','lainnya') NOT NULL DEFAULT 'kos',
  `target_gender` enum('putra','putri','campur_keluarga') NOT NULL,
  `description` text DEFAULT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `rules` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `provider_id`, `region_id`, `name`, `type`, `target_gender`, `description`, `address`, `latitude`, `longitude`, `rules`, `created_at`, `updated_at`) VALUES
(1, 3, 5, 'Kos Muslim Putri Barokah Pogung', 'kos', 'putri', 'Kos putri eksklusif, aman, tenang, dan dekat dengan Fakultas Kedokteran & Teknik UGM. Mengutamakan kenyamanan belajar dan kepatuhan syariat.', 'Jl. Pogung Baru No. 12, Pogung Kidul, Sinduadi, Mlati / Depok, Sleman, D.I. Yogyakarta', -7.76120000, 110.37340000, '1. Wajib menjaga kesopanan & ketertiban.\n2. Lawan jenis dilarang masuk kamar (hanya di ruang tamu terbuka).\n3. Gerbang ditutup pukul 22.00 WIB demi keamanan.', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(2, 3, 5, 'Kos Putra Insan Cendekia Seturan', 'kos', 'putra', 'Kos putra modern fasilitas lengkap kamar mandi dalam, AC, WiFi 100Mbps. Dekat UPN, YKPN, dan Atma Jaya.', 'Jl. Seturan Raya Gg. Mawar No. 4, Caturtunggal, Depok, Sleman', -7.77850000, 110.41120000, '1. Bebas rokok di dalam kamar.\n2. Jam tenang mulai pukul 23:00 WIB.', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(3, 3, 6, 'Paviliun Keluarga Harmoni Jombor', 'kontrakan', 'campur_keluarga', 'Kontrakan paviliun 2 kamar tidur sangat cocok untuk pasangan suami istri baru atau keluarga kecil.', 'Jl. Magelang KM 6.5, Sinduadi, Mlati, Sleman', -7.75540000, 110.36210000, 'Wajib menyertakan fotokopi Buku Nikah / Kartu Keluarga bagi penyewa pasangan.', '2026-10-07 15:21:11', '2026-10-07 15:21:11');

-- --------------------------------------------------------

--
-- Table structure for table `property_facilities`
--

CREATE TABLE `property_facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `facility_name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_facilities`
--

INSERT INTO `property_facilities` (`id`, `property_id`, `facility_name`, `icon`, `created_at`) VALUES
(1, 1, 'WiFi Berkecepatan Tinggi', 'bi-wifi', '2026-10-07 15:21:11'),
(2, 1, 'Dapur Bersama + Kulkas', 'bi-cup-hot', '2026-10-07 15:21:11'),
(3, 1, 'Parkir Motor Luas & Aman (CCTV)', 'bi-shield-check', '2026-10-07 15:21:11'),
(4, 1, 'Mushola Bersama', 'bi-building', '2026-10-07 15:21:11'),
(5, 2, 'WiFi Fiber 100 Mbps', 'bi-wifi', '2026-10-07 15:21:11'),
(6, 2, 'AC Tiap Kamar', 'bi-snow', '2026-10-07 15:21:11'),
(7, 2, 'Parkir Mobil & Motor', 'bi-car-front', '2026-10-07 15:21:11'),
(8, 3, '2 Kamar Tidur & Ruang Tamu', 'bi-door-open', '2026-10-07 15:21:11'),
(9, 3, 'Carport Mobil Pribadi', 'bi-car-front', '2026-10-07 15:21:11'),
(10, 3, 'Listrik Token 2200W', 'bi-lightning', '2026-10-07 15:21:11');

-- --------------------------------------------------------

--
-- Table structure for table `property_photos`
--

CREATE TABLE `property_photos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_photos`
--

INSERT INTO `property_photos` (`id`, `property_id`, `photo_url`, `caption`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 1, 'assets/images/sample-kos-1.jpg', 'Tampak Depan Gedung Kos', 1, '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(2, 2, 'assets/images/sample-kos-2.jpg', 'Fasad Kos Modern', 1, '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(3, 3, 'assets/images/sample-kos-3.jpg', 'Tampak Depan Rumah Kontrakan', 1, '2026-10-07 15:21:11', '2026-10-07 15:21:11');

-- --------------------------------------------------------

--
-- Table structure for table `provider_profiles`
--

CREATE TABLE `provider_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `verification_status` enum('pending','verified','rejected','suspended') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `id_card_photo` varchar(255) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account_number` varchar(100) DEFAULT NULL,
  `free_period_end` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provider_profiles`
--

INSERT INTO `provider_profiles` (`id`, `user_id`, `verification_status`, `rejection_reason`, `id_card_photo`, `bank_name`, `bank_account_number`, `free_period_end`, `created_at`, `updated_at`) VALUES
(1, 3, 'verified', NULL, 'ktp_ahmad.jpg', 'Bank Syariah Indonesia (BSI)', '7123456789', '2026-10-15', '2026-10-07 15:21:11', '2026-10-12 23:32:18');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dispute_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `refund_type` enum('full','partial') NOT NULL DEFAULT 'full',
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'completed',
  `reason` text NOT NULL,
  `processed_by_id` bigint(20) UNSIGNED NOT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `level` enum('province','city','operational') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `parent_id`, `name`, `level`, `created_at`, `updated_at`) VALUES
(1, NULL, 'D.I. Yogyakarta', 'province', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(2, 1, 'Kabupaten Sleman', 'city', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(3, 1, 'Kota Yogyakarta', 'city', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(4, 1, 'Kabupaten Bantul', 'city', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(5, 2, 'Depok (UGM / UNY / UPN)', 'operational', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(6, 2, 'Mlati (UTY / Jombor)', 'operational', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(7, 3, 'Gondokusuman (UKDW / Lempuyangan)', 'operational', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(8, NULL, 'Aceh', 'province', '2026-10-11 15:16:31', '2026-10-11 15:16:31'),
(9, 8, 'Kota Banda Aceh', 'city', '2026-10-11 15:17:29', '2026-10-11 15:17:29'),
(10, 8, 'Kabupaten Aceh Besar', 'city', '2026-10-11 15:17:47', '2026-10-11 15:17:47'),
(11, 8, 'Kabupaten Simeulue', 'city', '2026-10-11 15:40:31', '2026-10-11 15:40:31');

-- --------------------------------------------------------

--
-- Table structure for table `rental_applications`
--

CREATE TABLE `rental_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `listing_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `duration_months` int(11) NOT NULL,
  `payment_schedule` enum('full_upfront','monthly','3_monthly','6_monthly','yearly') NOT NULL DEFAULT 'monthly',
  `marital_status` varchar(50) DEFAULT NULL,
  `rental_amount` decimal(15,2) NOT NULL,
  `gateway_fee` decimal(15,2) DEFAULT 0.00,
  `applicant_notes` text DEFAULT NULL,
  `marriage_certificate_url` varchar(255) DEFAULT NULL,
  `is_family_verified` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected','cancelled','expired') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rental_applications`
--

INSERT INTO `rental_applications` (`id`, `tenant_id`, `listing_id`, `unit_id`, `start_date`, `duration_months`, `payment_schedule`, `marital_status`, `rental_amount`, `gateway_fee`, `applicant_notes`, `marriage_certificate_url`, `is_family_verified`, `status`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(2, 4, 1, 1, '2026-10-15', 6, 'monthly', 'lajang', 1250000.00, 4500.00, 'Uji coba Sprint 6 Payment and Contract', NULL, 0, 'expired', NULL, '2026-10-08 16:40:56', '2026-10-12 15:04:50'),
(3, 4, 1, 1, '2026-10-15', 6, 'monthly', 'lajang', 1250000.00, 4500.00, 'Uji coba Sprint 6 Payment and Contract', NULL, 0, 'expired', NULL, '2026-10-08 16:41:44', '2026-10-12 15:04:50'),
(5, 4, 1, 1, '2026-10-09', 6, 'monthly', 'lajang', 1250000.00, 4500.00, NULL, NULL, 0, 'approved', NULL, '2026-10-09 15:21:54', '2026-10-09 15:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL,
  `object_type` enum('listing','property','provider') NOT NULL DEFAULT 'listing',
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `region_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `evidence_url` varchar(255) DEFAULT NULL,
  `status` enum('open','investigating','resolved','dismissed') NOT NULL DEFAULT 'open',
  `admin_notes` text DEFAULT NULL,
  `resolved_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reporter_id`, `object_type`, `object_id`, `region_id`, `title`, `reason`, `evidence_url`, `status`, `admin_notes`, `resolved_by_id`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 4, 'listing', 1, 5, 'Fasilitas WiFi dan AC tidak sesuai deskripsi listing', 'Di iklan dicantumkan AC split 1 PK dan WiFi 50Mbps, namun saat survei AC rusak dan belum diperbaiki pemilik.', NULL, 'open', NULL, NULL, NULL, '2026-10-07 15:49:16', '2026-10-07 15:49:16'),
(2, 5, 'property', 2, 5, 'Klaim lingkungan kos putri tidak sesuai realita', 'Papan nama properti tidak mencantumkan aturan jam malam tamu lawan jenis dengan jelas.', NULL, 'investigating', 'Admin Sleman sedang menghubungi pemilik kos untuk verifikasi penegakan aturan tata tertib.', 2, NULL, '2026-10-08 15:49:16', '2026-10-09 15:49:16');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `expires_at` datetime NOT NULL,
  `status` enum('active','converted','expired','cancelled') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `application_id`, `expires_at`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, '2026-10-09 16:40:56', 'expired', '2026-10-08 16:40:56', '2026-10-12 15:04:50'),
(3, 3, '2026-10-09 16:41:44', 'expired', '2026-10-08 16:41:44', '2026-10-12 15:04:50');

-- --------------------------------------------------------

--
-- Table structure for table `settlements`
--

CREATE TABLE `settlements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gross_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `platform_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account_number` varchar(100) DEFAULT NULL,
  `bank_account_holder` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `payout_reference` varchar(100) DEFAULT NULL,
  `processed_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `status` enum('available','reserved','occupied','maintenance','inactive') NOT NULL DEFAULT 'available',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `property_id`, `name`, `size`, `floor`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kamar A-01 (Lantai 1)', '3 x 4 m', 'Lantai 1', 'available', '2026-10-07 15:21:11', '2026-10-12 23:32:18'),
(2, 1, 'Kamar A-02 (Lantai 1)', '3 x 4 m', 'Lantai 1', 'occupied', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(3, 2, 'Kamar Deluxe 101', '4 x 4 m', 'Lantai 1', 'available', '2026-10-07 15:21:11', '2026-10-09 15:22:29'),
(4, 3, 'Unit Paviliun Seluruhnya', '6 x 10 m (60 m2)', '1 Lantai', 'available', '2026-10-07 15:21:11', '2026-10-07 15:21:11');

-- --------------------------------------------------------

--
-- Table structure for table `unit_photos`
--

CREATE TABLE `unit_photos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `gender` enum('L','P') DEFAULT NULL,
  `role` enum('tenant','provider','admin_daerah','super_admin') NOT NULL DEFAULT 'tenant',
  `region_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','suspended','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `whatsapp`, `gender`, `role`, `region_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'superadmin@kosaja.id', '$2y$10$1fyeFKPkG1xzHdgZqP9LzO4qwkFTFm9hIQl/ABvlIxHYV5ITZvu3C', '081234567890', 'L', 'super_admin', NULL, 'active', '2026-10-07 15:21:11', '2026-10-09 15:24:07'),
(2, 'Admin Daerah Sleman', 'admin.sleman@kosaja.id', '$2y$10$jG5LtmEDs4e4mxE2RSzHouAaQNeD0HwKvQRimVnUo4K9fi94CvYfq', '081234567891', 'L', 'admin_daerah', 2, 'active', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(3, 'Haji Ahmad Santoso (Penyedia)', 'ahmad@owner.kosaja.id', '$2y$10$jG5LtmEDs4e4mxE2RSzHouAaQNeD0HwKvQRimVnUo4K9fi94CvYfq', '081234567892', 'L', 'provider', NULL, 'active', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(4, 'Budi Pratama (Penyewa)', 'budi@gmail.com', '$2y$10$jG5LtmEDs4e4mxE2RSzHouAaQNeD0HwKvQRimVnUo4K9fi94CvYfq', '081234567893', 'L', 'tenant', NULL, 'active', '2026-10-07 15:21:11', '2026-10-07 15:21:11'),
(5, 'Siti Aisyah (Penyewa)', 'siti@gmail.com', '$2y$10$jG5LtmEDs4e4mxE2RSzHouAaQNeD0HwKvQRimVnUo4K9fi94CvYfq', '081234567894', 'P', 'tenant', NULL, 'suspended', '2026-10-07 15:21:11', '2026-10-11 13:30:39'),
(10, 'Admin Daerah Kota Yogyakarta', 'admin.jogja@kosaja.id', '$2y$10$WK0jE.e5cAWYBMZraYVqaOVZ.vK1kM7kkOtnBOWMP.IiI8y6wkC9K', '081299887766', 'L', 'admin_daerah', 3, 'active', '2026-10-11 14:02:20', '2026-10-11 14:02:20'),
(19, 'Naufal Mubarak (Banda Aceh)', 'yusmadi23@gmail.com', '$2y$10$YtzmQ7b3V0Ok.UgjkMVL5./Cu43lR6HIMKmH.fADTQMwtoEwmwFiG', '081362549920', 'L', 'admin_daerah', 9, 'active', '2026-10-11 15:42:29', '2026-10-11 15:43:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_actor_id_foreign` (`actor_id`),
  ADD KEY `object_type_object_id` (`object_type`,`object_id`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contracts_application_id_foreign` (`application_id`),
  ADD KEY `contracts_tenant_id_foreign` (`tenant_id`),
  ADD KEY `contracts_provider_id_foreign` (`provider_id`),
  ADD KEY `contracts_property_id_foreign` (`property_id`),
  ADD KEY `contracts_unit_id_foreign` (`unit_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `disputes`
--
ALTER TABLE `disputes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disputes_contract_id_foreign` (`contract_id`),
  ADD KEY `disputes_invoice_id_foreign` (`invoice_id`),
  ADD KEY `disputes_raised_by_id_foreign` (`raised_by_id`),
  ADD KEY `disputes_against_user_id_foreign` (`against_user_id`),
  ADD KEY `disputes_decided_by_id_foreign` (`decided_by_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_listing_id` (`user_id`,`listing_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_contract_id_foreign` (`contract_id`),
  ADD KEY `invoices_tenant_id_foreign` (`tenant_id`),
  ADD KEY `status` (`status`),
  ADD KEY `due_date` (`due_date`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_id` (`unit_id`),
  ADD KEY `publication_status` (`publication_status`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_phone` (`recipient_phone`),
  ADD KEY `event_type` (`event_type`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `properties_provider_id_foreign` (`provider_id`),
  ADD KEY `region_id` (`region_id`);

--
-- Indexes for table `property_facilities`
--
ALTER TABLE `property_facilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_facilities_property_id_foreign` (`property_id`);

--
-- Indexes for table `property_photos`
--
ALTER TABLE `property_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_photos_property_id_foreign` (`property_id`);

--
-- Indexes for table `provider_profiles`
--
ALTER TABLE `provider_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refunds_dispute_id_foreign` (`dispute_id`),
  ADD KEY `refunds_payment_id_foreign` (`payment_id`),
  ADD KEY `refunds_invoice_id_foreign` (`invoice_id`),
  ADD KEY `refunds_processed_by_id_foreign` (`processed_by_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regions_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `rental_applications`
--
ALTER TABLE `rental_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rental_applications_tenant_id_foreign` (`tenant_id`),
  ADD KEY `rental_applications_listing_id_foreign` (`listing_id`),
  ADD KEY `rental_applications_unit_id_foreign` (`unit_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_reporter_id_foreign` (`reporter_id`),
  ADD KEY `reports_region_id_foreign` (`region_id`),
  ADD KEY `reports_resolved_by_id_foreign` (`resolved_by_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_application_id_foreign` (`application_id`),
  ADD KEY `status` (`status`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `settlements`
--
ALTER TABLE `settlements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settlements_provider_id_foreign` (`provider_id`),
  ADD KEY `settlements_contract_id_foreign` (`contract_id`),
  ADD KEY `settlements_processed_by_id_foreign` (`processed_by_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_property_id_foreign` (`property_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `unit_photos`
--
ALTER TABLE `unit_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_photos_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_region_id` (`region_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `disputes`
--
ALTER TABLE `disputes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `property_facilities`
--
ALTER TABLE `property_facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `property_photos`
--
ALTER TABLE `property_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `provider_profiles`
--
ALTER TABLE `provider_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `rental_applications`
--
ALTER TABLE `rental_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settlements`
--
ALTER TABLE `settlements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `unit_photos`
--
ALTER TABLE `unit_photos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `rental_applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contracts_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disputes`
--
ALTER TABLE `disputes`
  ADD CONSTRAINT `disputes_against_user_id_foreign` FOREIGN KEY (`against_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disputes_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `disputes_decided_by_id_foreign` FOREIGN KEY (`decided_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `disputes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `disputes_raised_by_id_foreign` FOREIGN KEY (`raised_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `properties_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_facilities`
--
ALTER TABLE `property_facilities`
  ADD CONSTRAINT `property_facilities_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `property_photos`
--
ALTER TABLE `property_photos`
  ADD CONSTRAINT `property_photos_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider_profiles`
--
ALTER TABLE `provider_profiles`
  ADD CONSTRAINT `provider_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_dispute_id_foreign` FOREIGN KEY (`dispute_id`) REFERENCES `disputes` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `refunds_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `refunds_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `refunds_processed_by_id_foreign` FOREIGN KEY (`processed_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `regions`
--
ALTER TABLE `regions`
  ADD CONSTRAINT `regions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `rental_applications`
--
ALTER TABLE `rental_applications`
  ADD CONSTRAINT `rental_applications_listing_id_foreign` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rental_applications_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rental_applications_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reports_resolved_by_id_foreign` FOREIGN KEY (`resolved_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `rental_applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `settlements`
--
ALTER TABLE `settlements`
  ADD CONSTRAINT `settlements_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `settlements_processed_by_id_foreign` FOREIGN KEY (`processed_by_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `settlements_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `unit_photos`
--
ALTER TABLE `unit_photos`
  ADD CONSTRAINT `unit_photos_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_region_id` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
