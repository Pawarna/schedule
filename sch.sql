-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for faculty_scheduling
CREATE DATABASE IF NOT EXISTS `faculty_scheduling` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `faculty_scheduling`;

-- Dumping structure for table faculty_scheduling.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table faculty_scheduling.admins: ~1 rows (approximately)
INSERT INTO `admins` (`id`, `username`, `password`) VALUES
	(1, 'admin', '$2y$10$uYknPNtsxFtoBt88kFLkG.QssGCERz8qjwwumJo3bEK3TMrA6U3L.');

-- Dumping structure for table faculty_scheduling.courses
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `credits` int NOT NULL,
  `semester` enum('odd','even') NOT NULL,
  `semester_number` int NOT NULL,
  `program_id` int DEFAULT NULL,
  `lecturer_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `program_id` (`program_id`),
  KEY `lecturer_id` (`lecturer_id`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`),
  CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table faculty_scheduling.courses: ~28 rows (approximately)
INSERT INTO `courses` (`id`, `code`, `name`, `credits`, `semester`, `semester_number`, `program_id`, `lecturer_id`) VALUES
	(1, 'INF101', 'Pemrograman Dasar', 3, 'odd', 1, 1, 1),
	(2, 'INF102', 'Struktur Data', 3, 'odd', 3, 1, 7),
	(3, 'INF103', 'Kecerdasan Buatan', 3, 'odd', 5, 1, 9),
	(4, 'INF201', 'Pemrograman Berorientasi Objek', 4, 'even', 2, 1, 1),
	(5, 'INF202', 'Basis Data', 3, 'even', 4, 1, 13),
	(6, 'INF203', 'Jaringan Komputer', 3, 'even', 6, 1, 9),
	(7, 'SIP101', 'Mekanika Teknik', 3, 'odd', 1, 2, 2),
	(8, 'SIP102', 'Analisis Struktur', 3, 'odd', 3, 2, 3),
	(9, 'SIP103', 'Desain Struktur Beton', 3, 'odd', 5, 2, 10),
	(10, 'SIP201', 'Teknik Fondasi', 4, 'even', 2, 2, 2),
	(11, 'SIP202', 'Manajemen Proyek Konstruksi', 3, 'even', 4, 2, 14),
	(12, 'SIP203', 'Struktur Baja', 3, 'even', 6, 2, 10),
	(13, 'IND101', 'Penelitian Operasi', 3, 'odd', 1, 3, 6),
	(14, 'IND102', 'Manajemen Produksi', 3, 'odd', 3, 3, 8),
	(15, 'IND103', 'Optimasi Sistem', 3, 'odd', 5, 3, 11),
	(16, 'IND201', 'Sistem Informasi Manufaktur', 3, 'even', 2, 3, 6),
	(17, 'IND202', 'Manajemen Rantai Pasok', 4, 'even', 4, 3, 15),
	(18, 'IND203', 'Ergonomi', 3, 'even', 6, 3, 8),
	(19, 'MES101', 'Termodinamika', 3, 'odd', 1, 4, 4),
	(20, 'MES102', 'Mekanika Fluida', 3, 'odd', 3, 4, 5),
	(21, 'MES103', 'Sistem Manufaktur', 3, 'odd', 5, 4, 12),
	(22, 'MES201', 'Desain Mesin', 4, 'even', 2, 4, 4),
	(23, 'MES202', 'Transfer Panas', 3, 'even', 4, 4, 16),
	(24, 'MES203', 'Dinamika Mesin', 3, 'even', 6, 4, 5),
	(25, 'TEK101', 'Matematika Teknik I', 3, 'odd', 1, 1, 9),
	(26, 'TEK102', 'Fisika Teknik', 3, 'odd', 1, 4, 5),
	(27, 'TEK201', 'Matematika Teknik II', 3, 'even', 2, 2, 3),
	(28, 'TEK202', 'Pengantar CAD', 3, 'even', 2, 3, 11);

-- Dumping structure for table faculty_scheduling.lecturers
CREATE TABLE IF NOT EXISTS `lecturers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table faculty_scheduling.lecturers: ~16 rows (approximately)
INSERT INTO `lecturers` (`id`, `name`, `email`) VALUES
	(1, 'Dr. Andi Wijaya', 'andi.wijaya@ft.ac.id'),
	(2, 'Prof. Budi Santoso', 'budi.santoso@ft.ac.id'),
	(3, 'Dr. Clara Putri', 'clara.putri@ft.ac.id'),
	(4, 'Dr. Dedi Kurniawan', 'dedi.kurniawan@ft.ac.id'),
	(5, 'Prof. Eko Prasetyo', 'eko.prasetyo@ft.ac.id'),
	(6, 'Dr. Fita Rahayu', 'fita.rahayu@ft.ac.id'),
	(7, 'Dr. Guntur Nugroho', 'guntur.nugroho@ft.ac.id'),
	(8, 'Prof. Hana Susanti', 'hana.susanti@ft.ac.id'),
	(9, 'Dr. Indra Saputra', 'indra.saputra@ft.ac.id'),
	(10, 'Dr. Joko Widodo', 'joko.widodo@ft.ac.id'),
	(11, 'Dr. Karina Lestari', 'karina.lestari@ft.ac.id'),
	(12, 'Prof. Luthfi Ahmad', 'luthfi.ahmad@ft.ac.id'),
	(13, 'Dr. Maya Sari', 'maya.sari@ft.ac.id'),
	(14, 'Dr. Nanda Pratama', 'nanda.pratama@ft.ac.id'),
	(15, 'Dr. Oscar Nugroho', 'oscar.nugroho@ft.ac.id'),
	(16, 'Prof. Putri Wulandari', 'putri.wulandari@ft.ac.id');

-- Dumping structure for table faculty_scheduling.programs
CREATE TABLE IF NOT EXISTS `programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `student_count_sem1` int NOT NULL,
  `student_count_sem2` int NOT NULL,
  `student_count_sem3` int NOT NULL,
  `student_count_sem4` int NOT NULL,
  `student_count_sem5` int NOT NULL,
  `student_count_sem6` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table faculty_scheduling.programs: ~4 rows (approximately)
INSERT INTO `programs` (`id`, `name`, `student_count_sem1`, `student_count_sem2`, `student_count_sem3`, `student_count_sem4`, `student_count_sem5`, `student_count_sem6`) VALUES
	(1, 'Informatika', 100, 100, 100, 100, 100, 100),
	(2, 'Teknik Sipil', 100, 100, 100, 100, 100, 100),
	(3, 'Teknik Industri', 100, 50, 30, 100, 130, 100),
	(4, 'Teknik Mesin', 10, 10, 10, 10, 10, 10);

-- Dumping structure for table faculty_scheduling.rooms
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `capacity` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table faculty_scheduling.rooms: ~14 rows (approximately)
INSERT INTO `rooms` (`id`, `name`, `capacity`) VALUES
	(1, 'Ruang 301', 50),
	(2, 'Ruang 302', 50),
	(3, 'Ruang 303', 50),
	(4, 'Ruang 304', 50),
	(5, 'Ruang 305', 50),
	(6, 'Ruang 401', 40),
	(7, 'Ruang 402', 40),
	(8, 'Ruang 403', 40),
	(9, 'Ruang 404', 40),
	(10, 'Lab Terpadu', 40),
	(11, 'Lab Teknik', 40),
	(12, 'Ruang 501', 50),
	(13, 'Ruang 502', 50),
	(14, 'Ruang 503', 50),
	(15, 'GP 303', 35),
	(16, 'GP 304', 50),
	(17, 'GP 305', 40);

-- Dumping structure for table faculty_scheduling.schedules
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int DEFAULT NULL,
  `lecturer_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `time_slot_id` int DEFAULT NULL,
  `semester` enum('odd','even') NOT NULL,
  `class_label` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `lecturer_id` (`lecturer_id`),
  KEY `room_id` (`room_id`),
  KEY `time_slot_id` (`time_slot_id`),
  CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`),
  CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  CONSTRAINT `schedules_ibfk_4` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table faculty_scheduling.schedules: ~0 rows (approximately)
INSERT INTO `schedules` (`id`, `course_id`, `lecturer_id`, `room_id`, `time_slot_id`, `semester`, `class_label`) VALUES
	(236, 4, 1, 17, 23, 'even', 'A'),
	(237, 4, 1, 4, 3, 'even', 'B'),
	(238, 4, 1, 11, 5, 'even', 'C'),
	(239, 5, 13, 3, 15, 'even', 'A'),
	(240, 5, 13, 4, 17, 'even', 'B'),
	(241, 5, 13, 16, 16, 'even', 'C'),
	(242, 6, 9, 12, 18, 'even', 'A'),
	(243, 6, 9, 4, 4, 'even', 'B'),
	(244, 6, 9, 4, 9, 'even', 'C'),
	(245, 10, 2, 9, 7, 'even', 'A'),
	(246, 10, 2, 15, 14, 'even', 'B'),
	(247, 10, 2, 8, 6, 'even', 'C'),
	(248, 11, 14, 14, 2, 'even', 'A'),
	(249, 11, 14, 9, 25, 'even', 'B'),
	(250, 11, 14, 16, 15, 'even', 'C'),
	(251, 12, 10, 7, 1, 'even', 'A'),
	(252, 12, 10, 4, 24, 'even', 'B'),
	(253, 12, 10, 1, 6, 'even', 'C'),
	(254, 27, 3, 7, 17, 'even', 'A'),
	(255, 27, 3, 7, 4, 'even', 'B'),
	(256, 27, 3, 4, 12, 'even', 'C'),
	(257, 16, 6, 15, 24, 'even', 'A'),
	(258, 16, 6, 16, 22, 'even', 'B'),
	(259, 17, 15, 1, 13, 'even', 'A'),
	(260, 17, 15, 1, 15, 'even', 'B'),
	(261, 17, 15, 17, 6, 'even', 'C'),
	(262, 18, 8, 9, 19, 'even', 'A'),
	(263, 18, 8, 4, 25, 'even', 'B'),
	(264, 18, 8, 11, 25, 'even', 'C'),
	(265, 28, 11, 16, 14, 'even', 'A'),
	(266, 28, 11, 5, 7, 'even', 'B'),
	(267, 22, 4, 3, 13, 'even', 'A'),
	(268, 23, 16, 6, 7, 'even', 'A'),
	(269, 24, 5, 9, 14, 'even', 'A'),
	(270, 1, 1, 11, 22, 'odd', 'A'),
	(271, 1, 1, 10, 4, 'odd', 'B'),
	(272, 1, 1, 7, 8, 'odd', 'C'),
	(273, 2, 7, 16, 5, 'odd', 'A'),
	(274, 2, 7, 2, 14, 'odd', 'B'),
	(275, 2, 7, 7, 6, 'odd', 'C'),
	(276, 3, 9, 9, 4, 'odd', 'A'),
	(277, 3, 9, 14, 13, 'odd', 'B'),
	(278, 3, 9, 4, 25, 'odd', 'C'),
	(279, 25, 9, 9, 16, 'odd', 'A'),
	(280, 25, 9, 4, 7, 'odd', 'B'),
	(281, 25, 9, 16, 6, 'odd', 'C'),
	(282, 7, 2, 13, 18, 'odd', 'A'),
	(283, 7, 2, 11, 9, 'odd', 'B'),
	(284, 7, 2, 17, 14, 'odd', 'C'),
	(285, 8, 3, 4, 21, 'odd', 'A'),
	(286, 8, 3, 13, 10, 'odd', 'B'),
	(287, 8, 3, 14, 12, 'odd', 'C'),
	(288, 9, 10, 10, 13, 'odd', 'A'),
	(289, 9, 10, 17, 22, 'odd', 'B'),
	(290, 9, 10, 15, 14, 'odd', 'C'),
	(291, 13, 6, 3, 6, 'odd', 'A'),
	(292, 13, 6, 17, 1, 'odd', 'B'),
	(293, 13, 6, 16, 23, 'odd', 'C'),
	(294, 14, 8, 11, 9, 'odd', 'A'),
	(295, 15, 11, 8, 1, 'odd', 'A'),
	(296, 15, 11, 15, 7, 'odd', 'B'),
	(297, 15, 11, 12, 3, 'odd', 'C'),
	(298, 15, 11, 17, 20, 'odd', 'D'),
	(299, 19, 4, 14, 17, 'odd', 'A'),
	(300, 20, 5, 3, 19, 'odd', 'A'),
	(301, 21, 12, 16, 4, 'odd', 'A'),
	(302, 26, 5, 4, 2, 'odd', 'A');

-- Dumping structure for table faculty_scheduling.time_slots
CREATE TABLE IF NOT EXISTS `time_slots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table faculty_scheduling.time_slots: ~25 rows (approximately)
INSERT INTO `time_slots` (`id`, `day`, `start_time`, `end_time`) VALUES
	(1, 'Monday', '07:30:00', '09:30:00'),
	(2, 'Monday', '09:30:00', '11:30:00'),
	(3, 'Monday', '12:30:00', '14:30:00'),
	(4, 'Monday', '14:30:00', '16:30:00'),
	(5, 'Monday', '16:30:00', '18:30:00'),
	(6, 'Tuesday', '07:30:00', '09:30:00'),
	(7, 'Tuesday', '09:30:00', '11:30:00'),
	(8, 'Tuesday', '12:30:00', '14:30:00'),
	(9, 'Tuesday', '14:30:00', '16:30:00'),
	(10, 'Tuesday', '16:30:00', '18:30:00'),
	(11, 'Wednesday', '07:30:00', '09:30:00'),
	(12, 'Wednesday', '09:30:00', '11:30:00'),
	(13, 'Wednesday', '12:30:00', '14:30:00'),
	(14, 'Wednesday', '14:30:00', '16:30:00'),
	(15, 'Wednesday', '16:30:00', '18:30:00'),
	(16, 'Thursday', '07:30:00', '09:30:00'),
	(17, 'Thursday', '09:30:00', '11:30:00'),
	(18, 'Thursday', '12:30:00', '14:30:00'),
	(19, 'Thursday', '14:30:00', '16:30:00'),
	(20, 'Thursday', '16:30:00', '18:30:00'),
	(21, 'Friday', '07:30:00', '09:30:00'),
	(22, 'Friday', '09:30:00', '11:30:00'),
	(23, 'Friday', '12:30:00', '14:30:00'),
	(24, 'Friday', '14:30:00', '16:30:00'),
	(25, 'Friday', '16:30:00', '18:30:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
