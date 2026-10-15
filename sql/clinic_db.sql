-- ==========================================================
-- ระบบนัดหมายคลินิก (Clinic Appointment System)
-- Database Schema & Sample Data
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30
-- ==========================================================
-- วิธีใช้:
--   1. เปิด phpMyAdmin
--   2. ไปที่แท็บ Import
--   3. เลือกไฟล์นี้ แล้วกด Go
-- ==========================================================
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE DATABASE IF NOT EXISTS `clinic_db` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `clinic_db`;


DROP TABLE IF EXISTS `tb_appointments`;
DROP TABLE IF EXISTS `tb_patients`;
DROP TABLE IF EXISTS `tb_doctors`;




/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_appointments`
--

CREATE TABLE `tb_appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL COMMENT 'เวลาเริ่ม',
  `end_time` datetime NOT NULL COMMENT 'เวลาสิ้นสุด',
  `status` enum('pending','arrived','cancelled') DEFAULT 'pending' COMMENT 'สถานะ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='ตารางนัดหมาย';

-- --------------------------------------------------------

--
-- Table structure for table `tb_doctors`
--

CREATE TABLE `tb_doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'ชื่อแพทย์'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='ตารางรายชื่อแพทย์';

--
-- Dumping data for table `tb_doctors`
--

INSERT INTO `tb_doctors` (`id`, `name`) VALUES
(1, 'นพ.สมชาย ใจดี'),
(2, 'พญ.สมหญิง รักษ์ดี'),
(3, 'นพ.วิชัย เก่งกาจ'),
(4, 'พญ.มาลี สุขใจ');

-- --------------------------------------------------------

--
-- Table structure for table `tb_patients`
--

CREATE TABLE `tb_patients` (
  `id` int(11) NOT NULL,
  `hn` varchar(20) NOT NULL COMMENT 'รหัสผู้ป่วย',
  `first_name` varchar(80) NOT NULL COMMENT 'ชื่อ',
  `last_name` varchar(80) NOT NULL COMMENT 'นามสกุล',
  `phone` varchar(10) NOT NULL COMMENT 'เบอร์โทรศัพท์',
  `birth_date` date NOT NULL COMMENT 'วันเกิด',
  `registered_at` date NOT NULL COMMENT 'วันที่ลงทะเบียน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='ตารางเก็บรายชื่อผู้ป่วย';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_appointments`
--
ALTER TABLE `tb_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_time` (`doctor_id`,`start_time`,`end_time`),
  ADD KEY `idx_start_time` (`start_time`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_appt_patient` (`patient_id`);

--
-- Indexes for table `tb_doctors`
--
ALTER TABLE `tb_doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_patients`
--
ALTER TABLE `tb_patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hn` (`hn`),
  ADD KEY `phone` (`phone`),
  ADD KEY `name` (`first_name`,`last_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_appointments`
--
ALTER TABLE `tb_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_doctors`
--
ALTER TABLE `tb_doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_patients`
--
ALTER TABLE `tb_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_appointments`
--
ALTER TABLE `tb_appointments`
  ADD CONSTRAINT `fk_appt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `tb_doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `tb_patients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
