/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - db_bioskop
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_bioskop` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `db_bioskop`;

/*Table structure for table `tb_admin` */

DROP TABLE IF EXISTS `tb_admin`;

CREATE TABLE `tb_admin` (
  `admin_id` char(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass_admin` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` varchar(255) DEFAULT 'Jimbaran',
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_admin` */

insert  into `tb_admin`(`admin_id`,`nama`,`email`,`pass_admin`,`no_telepon`,`alamat`,`tanggal_buat`) values 
('adm096','mas amba','ambasing@gmail.com','$2y$10$VzCrnbWPQ8U2JHUA87SBwuT/l/Cb2IL693lvwukrtkXJlbO56MuHu','23456789','','2024-12-09 13:58:05'),
('adm438','Ucok wiguna','ucok96@gmail.com','$2y$10$Kd9qLBup4xmNLbX8LyAaH.jNTvFOdcEoCw8v3NrxEqyVTRfhOUNwG','12345','','2024-12-09 13:56:29'),
('adm712','rich','krishnarichad123@gmail.com','$2y$10$UYbb.4kt5WFxDmKCtUdmd.zhs/69rmnSU8Sk8oDYEQzjOIHIa6Wzm','65','qeg','2024-12-13 21:53:51'),
('adm713','RIchad Krizzzzna','krishnarichad@gmail.com','$2y$10$.CI.GykUy.2WTvfgavwGJekEpdL./jaNVEVaST8G9EbLtN4jkYY3u','23456789','qwefedae','2024-12-13 21:48:03');

/*Table structure for table `tb_booking` */

DROP TABLE IF EXISTS `tb_booking`;

CREATE TABLE `tb_booking` (
  `booking_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` char(10) DEFAULT NULL,
  `schedule_id` char(10) DEFAULT NULL,
  `jumlah_kursi` int(11) DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `schedule_id` FOREIGN KEY (`schedule_id`) REFERENCES `tb_schedule` (`schedule_id`) ON DELETE CASCADE,
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_booking` */

insert  into `tb_booking`(`booking_id`,`user_id`,`schedule_id`,`jumlah_kursi`) values 
(67,'usr003','scd001',3),
(68,'usr001','scd001',4),
(69,'usr003','scd001',2),
(70,'usr001','scd001',2),
(71,'usr003','scd001',2),
(72,'usr001','scd001',2);

/*Table structure for table `tb_booking_detail` */

DROP TABLE IF EXISTS `tb_booking_detail`;

CREATE TABLE `tb_booking_detail` (
  `booking_detail_id` int(10) NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) NOT NULL,
  `seat_id` char(10) NOT NULL,
  `kode_va` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`booking_detail_id`),
  KEY `booking_id` (`booking_id`),
  KEY `seat_id` (`seat_id`),
  CONSTRAINT `booking_id` FOREIGN KEY (`booking_id`) REFERENCES `tb_booking` (`booking_id`),
  CONSTRAINT `tb_booking_detail_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `tb_seat` (`seat_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_booking_detail` */

insert  into `tb_booking_detail`(`booking_detail_id`,`booking_id`,`seat_id`,`kode_va`) values 
(154,67,'seat025',NULL),
(155,67,'seat026',NULL),
(156,67,'seat027',NULL),
(157,68,'seat028',NULL),
(158,68,'seat009',NULL),
(159,68,'seat030',NULL),
(160,68,'seat011',NULL),
(161,69,'seat020',NULL),
(162,69,'seat040',NULL),
(163,70,'seat015',NULL),
(164,70,'seat036',NULL),
(165,71,'seat002',NULL),
(166,71,'seat001',NULL),
(167,72,'seat013',NULL),
(168,72,'seat006',NULL);

/*Table structure for table `tb_movie` */

DROP TABLE IF EXISTS `tb_movie`;

CREATE TABLE `tb_movie` (
  `movie_id` char(10) NOT NULL,
  `poster` varchar(255) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `genre` varchar(25) NOT NULL,
  `durasi` int(11) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `sinopsis` varchar(255) NOT NULL,
  `trailer_url` text DEFAULT NULL,
  `tanggal_tambah` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_movie` */

insert  into `tb_movie`(`movie_id`,`poster`,`judul`,`genre`,`durasi`,`rating`,`sinopsis`,`trailer_url`,`tanggal_tambah`) values 
('mov001','https://example.com/poster1.jpg','Film A','Aksi',120,8.5,'Sinopsis Film A','https://youtube.com/trailer1','2024-12-08 18:31:53'),
('mov002','https://example.com/poster2.jpg','Film B','Drama',150,7.2,'Sinopsis Film B','https://youtube.com/trailer2','2024-12-08 18:31:53'),
('mov003','https://example.com/poster3.jpg','Film C','Komedi',90,6.8,'Sinopsis Film C','https://youtube.com/trailer3','2024-12-08 18:31:53'),
('mov004','https://example.com/poster4.jpg','Film D','Horor',100,7.5,'Sinopsis Film D','https://youtube.com/trailer4','2024-12-08 18:31:53'),
('mov005','https://example.com/poster5.jpg','Film E','Petualangan',130,9.0,'Sinopsis Film E','https://youtube.com/trailer5','2024-12-08 18:31:53');

/*Table structure for table `tb_schedule` */

DROP TABLE IF EXISTS `tb_schedule`;

CREATE TABLE `tb_schedule` (
  `schedule_id` char(10) NOT NULL,
  `movie_id` char(10) NOT NULL,
  `studio_id` char(10) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `harga_tiket` int(11) NOT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `movie_id` (`movie_id`),
  KEY `studio_id` (`studio_id`),
  CONSTRAINT `tb_schedule_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `tb_movie` (`movie_id`) ON DELETE CASCADE,
  CONSTRAINT `tb_schedule_ibfk_2` FOREIGN KEY (`studio_id`) REFERENCES `tb_studio` (`studio_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_schedule` */

insert  into `tb_schedule`(`schedule_id`,`movie_id`,`studio_id`,`tanggal`,`waktu_mulai`,`waktu_selesai`,`harga_tiket`) values 
('scd001','mov001','std001','2025-10-27','12:00:00','14:00:00',30000),
('scd002','mov001','std001','2025-10-27','15:00:00','17:00:00',30000),
('scd003','mov002','std002','2025-10-27','18:00:00','20:30:00',35000),
('scd004','mov002','std002','2025-10-28','21:30:00','24:00:00',35000);

/*Table structure for table `tb_seat` */

DROP TABLE IF EXISTS `tb_seat`;

CREATE TABLE `tb_seat` (
  `seat_id` char(10) NOT NULL,
  `studio_id` char(10) NOT NULL,
  `nomor_kursi` varchar(10) NOT NULL,
  `status` enum('Available','Booked') DEFAULT NULL,
  PRIMARY KEY (`seat_id`),
  KEY `studio_id` (`studio_id`),
  CONSTRAINT `tb_seat_ibfk_1` FOREIGN KEY (`studio_id`) REFERENCES `tb_studio` (`studio_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_seat` */

insert  into `tb_seat`(`seat_id`,`studio_id`,`nomor_kursi`,`status`) values 
('seat001','std001','A1','Booked'),
('seat002','std001','A2','Booked'),
('seat003','std001','A3','Available'),
('seat004','std001','A4','Available'),
('seat005','std001','A5','Available'),
('seat006','std001','B1','Booked'),
('seat007','std001','B2','Available'),
('seat008','std001','B3','Available'),
('seat009','std001','B4','Booked'),
('seat010','std001','B5','Available'),
('seat011','std001','C1','Booked'),
('seat012','std001','C2','Available'),
('seat013','std001','C3','Booked'),
('seat014','std001','C4','Available'),
('seat015','std001','C5','Booked'),
('seat016','std001','D1','Available'),
('seat017','std001','D2','Available'),
('seat018','std001','D3','Available'),
('seat019','std001','D4','Available'),
('seat020','std001','D5','Booked'),
('seat021','std001','E1','Available'),
('seat022','std001','E2','Available'),
('seat023','std001','E3','Available'),
('seat024','std001','E4','Available'),
('seat025','std001','E5','Booked'),
('seat026','std001','F1','Booked'),
('seat027','std001','F2','Booked'),
('seat028','std001','F3','Booked'),
('seat029','std001','F4','Available'),
('seat030','std001','F5','Booked'),
('seat031','std001','G1','Available'),
('seat032','std001','G2','Available'),
('seat033','std001','G3','Available'),
('seat034','std001','G4','Available'),
('seat035','std001','G5','Available'),
('seat036','std001','H1','Booked'),
('seat037','std001','H2','Available'),
('seat038','std001','H3','Available'),
('seat039','std001','H4','Available'),
('seat040','std001','H5','Booked'),
('seat41','std002','A1','Available');

/*Table structure for table `tb_studio` */

DROP TABLE IF EXISTS `tb_studio`;

CREATE TABLE `tb_studio` (
  `studio_id` char(10) NOT NULL,
  `nama_studio` varchar(255) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  PRIMARY KEY (`studio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_studio` */

insert  into `tb_studio`(`studio_id`,`nama_studio`,`kapasitas`) values 
('std001','Ufotable',200),
('std002','Bimasakti',150),
('std003','Ngawidio',150);

/*Table structure for table `tb_transaksi` */

DROP TABLE IF EXISTS `tb_transaksi`;

CREATE TABLE `tb_transaksi` (
  `transaksi_id` char(10) NOT NULL,
  `booking_id` int(10) NOT NULL,
  `payment_method` enum('M-Banking','Cash') NOT NULL,
  `nama_bank` enum('BCA','BNI','MANDIRI') DEFAULT NULL,
  `no_rek` varchar(50) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `kode_va` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Completed','Failed') NOT NULL DEFAULT 'Pending',
  `tanggal_transaksi` datetime DEFAULT current_timestamp(),
  `bukti_pembayaran` blob DEFAULT NULL,
  PRIMARY KEY (`transaksi_id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `fk_booking` FOREIGN KEY (`booking_id`) REFERENCES `tb_booking` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_transaksi` */

insert  into `tb_transaksi`(`transaksi_id`,`booking_id`,`payment_method`,`nama_bank`,`no_rek`,`total_price`,`kode_va`,`status`,`tanggal_transaksi`,`bukti_pembayaran`) values 
('TR00067',67,'M-Banking','BCA','357468579680876',90000.00,NULL,'Pending','2024-12-23 08:48:54',''),
('TR00068',68,'M-Banking','BCA','3750185',120000.00,NULL,'Pending','2024-12-23 08:56:11',''),
('TR00069',69,'M-Banking','BCA','689038526048',60000.00,NULL,'Pending','2024-12-23 08:59:46',''),
('TR00070',70,'M-Banking','BCA','5366246264',60000.00,NULL,'Pending','2024-12-23 09:04:08',''),
('TR00071',71,'M-Banking','BCA','3762928',60000.00,NULL,'Pending','2024-12-23 09:16:32',''),
('TR00072',72,'M-Banking','BCA','357468579680876',60000.00,NULL,'Pending','2024-12-23 09:17:51','');

/*Table structure for table `tb_user` */

DROP TABLE IF EXISTS `tb_user`;

CREATE TABLE `tb_user` (
  `user_id` char(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass_user` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` varchar(255) DEFAULT 'Jimbaran',
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_user` */

insert  into `tb_user`(`user_id`,`nama`,`email`,`pass_user`,`no_telepon`,`alamat`,`tanggal_buat`) values 
('usr001','Richie (suami Ruan Mei)','krishnarichad123@gmail.com','e7f4f8bd246c235418280d1f124e14f0','65','Jimbaran','2024-12-14 20:28:19'),
('usr002','RIchad Krizzzzna','krishnarichad@gmail.com','bccda64e9ab94613672c2e2b06e02dbd','523','Jimbaran','2024-12-22 22:49:30'),
('usr003','RIchad Krizzzzna','ambasing@gmail.com','bc0b715100c1cd24bbc2471fa636f267','536356','Jimbaran','2024-12-23 01:54:50');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
