-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2025 at 06:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `all_students`
--

CREATE TABLE `all_students` (
  `registration_num` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `combination` varchar(100) DEFAULT NULL,
  `year` varchar(30) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `expired_otp` datetime DEFAULT NULL,
  `create_otp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `all_students`
--

INSERT INTO `all_students` (`registration_num`, `name`, `combination`, `year`, `email`, `password`, `otp`, `expired_otp`, `create_otp`) VALUES
('2021s18921', 'Ravindu', 'P2', 'first_year', 'madushanpanditha2001@gmail.com', '$2y$10$Ad86oaYuT6IxDnvnwlNbh.jYBalnmqtiRckv00WuZ0A2iRmNEsQ8W', '232919', '2025-01-21 11:04:46', '2025-01-21 10:59:46'),
('2021s18922', 'kavindu', 'P3', 'second_year', 'lakshan@gmail.com', '$2y$10$pX67Tw9M2fV/olIXtTMeIeFhh3p/EPLATz6o85R3.wqNuzDizewJS', NULL, NULL, NULL),
('2021s18923', 'lavindu', 'P4', 'third_year', 'minthaka@gmail.com', 'lllllll', NULL, NULL, NULL),
('2021s18924', 'navindu', 'P3', 'fourth_year', 'dilshan@gmail.com', 'nnnnnnn', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dean`
--

CREATE TABLE `dean` (
  `dean_id` int(11) NOT NULL,
  `dean_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `expired_otp` datetime DEFAULT NULL,
  `create_otp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean`
--

INSERT INTO `dean` (`dean_id`, `dean_name`, `email`, `password`, `otp`, `expired_otp`, `create_otp`) VALUES
(1, 'Prof.Upul Sonnadara', 'dean@stu.cmb.ac.lk', '$2y$10$b6dpsVY4znNiPRJWk0a9MuUNzNXwEFIB0b7z.ZpEVYCLcT4LblIHK', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `first_year`
--

CREATE TABLE `first_year` (
  `id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `semester` enum('semester 1','semester 2') NOT NULL,
  `no_of_hours` int(1) NOT NULL,
  `credits` int(11) DEFAULT NULL,
  `registered_students` int(10) NOT NULL,
  `department` varchar(50) NOT NULL,
  `Lecturer_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `first_year`
--

INSERT INTO `first_year` (`id`, `course_id`, `course_name`, `semester`, `no_of_hours`, `credits`, `registered_students`, `department`, `Lecturer_name`) VALUES
(16, 'PH 1005', 'Modern Physics and Special Relativity', 'semester 1', 2, 2, 100, 'Physics', 'Janaka Adassuriya'),
(17, 'PH 1007', 'Astronomy 1', 'semester 1', 2, 2, 180, 'Physics', 'KPSC Jayaratne'),
(29, 'AM1011', 'Fundamental Applied Mathematics', 'semester 1', 2, 2, 100, 'Mathematics', 'J.K. Wijerathna'),
(30, 'AM1012', 'Vector Calculus', 'semester 1', 2, 2, 200, 'Mathematics', 'P. D. D. Gallage'),
(31, 'PM 1011', 'Foundations of Mathematics', 'semester 1', 2, 2, 100, 'Mathematics', 'C. J. Wijeratne'),
(32, 'PM 1012', 'Introduction to Number Theory', 'semester 1', 2, 2, 100, 'Mathematics', 'B. L. Samarasekera'),
(33, 'FM 1011', 'Financial Mathematics I', 'semester 1', 2, 2, 100, 'Mathematics', 'S. S. N. Perera'),
(34, 'FM 1013', 'Linear Programming', 'semester 1', 2, 2, 80, 'Mathematics', 'S.A.K.P. de Silva'),
(35, 'MS 1011', 'Computing for Finance', 'semester 1', 1, 1, 80, 'Mathematics', 'S. S. N. Perera'),
(36, 'AM 1108', 'Mathematics for Biological Sciences', 'semester 1', 2, 2, 102, 'Mathematics', 'L.C. Edussuriya'),
(43, 'MS 1012', 'Mathematical Economics', 'semester 1', 2, 1, 40, 'Mathematics', 'H.C.Y. Jayathunga'),
(44, 'CH1008', 'General & Physical  Chemistry', 'semester 1', 2, 2, 80, 'Chemistry', 'K.M.N de Silva'),
(45, 'CH1010', 'Calculations in Chemistry', 'semester 1', 1, 1, 100, 'Chemistry', 'K.R.R Mahanama'),
(46, 'CH1011', 'Practical  Chemistry Level1', 'semester 1', 2, 2, 80, 'Chemistry', 'LHR Perera'),
(47, 'ST1006', 'Introduction to Probability  & Statistics', 'semester 1', 2, 2, 150, 'Statistics', 'G.A.C.N Priyadharshani'),
(48, 'ST 1008', 'Probability and Distributions', 'semester 1', 2, 2, 200, 'Statistics', 'E. R. A. D. Bandara'),
(49, 'ST1009', 'Exploratory Data Analysis', 'semester 1', 2, 2, 150, 'Statistics', 'M.D.T.Attygalle'),
(50, 'CS 1102', 'Introduction to Computing', 'semester 1', 3, 3, 350, 'UCSC', 'Dr.samantha'),
(51, 'NS1001', 'Fundamentals of Nuclear Science', 'semester 1', 3, 3, 10, 'Nuclear Science', 'Dr.abc'),
(52, 'BT1011', 'Genetics and Cell Biology', 'semester 1', 2, 2, 200, 'Plant Science', 'T. D. Silva'),
(53, 'BT1009', 'Genetics and Cell Biology Practicals', 'semester 1', 1, 1, 200, 'Plant Science', 'T. D. Silva'),
(54, 'BT1008', 'Plant Resources', 'semester 1', 1, 1, 100, 'Plant Science', 'IAJK Dissanayake'),
(55, 'BT 1013', 'Plant Structure', 'semester 1', 1, 1, 49, 'Plant Science', 'H.I.U. Caldera'),
(56, 'ZL1009', 'Evolution and Biogeography', 'semester 1', 2, 2, 120, 'Zoology and Environment Science', 'S. S. Seneviratne'),
(57, 'EN 1008', 'Introduction to Environmental sciences', 'semester 1', 3, 3, 150, 'Zoology and Environment Science', 'D. Halwathura'),
(58, 'IS 1006', 'Fundamentals of Statistics', 'semester 1', 3, 3, 155, 'Statistics', 'H.A.S.G. Dharmarathne'),
(59, 'IS1007', 'Introduction to Statistical  Computing', 'semester 1', 1, 1, 152, 'Statistics', 'Oshada Senaweera'),
(60, 'MS 1001', 'Principles of Management', 'semester 1', 2, 1, 45, 'Statistics', 'M.G.G Hemakumara'),
(70, 'PH 1022(G1)', 'Physics Laboratory I', 'semester 1', 4, 2, 60, 'Physics', 'WAM Madhavi'),
(71, 'PH 1022(G2)', 'Physics Laboratory I', 'semester 1', 4, 2, 65, 'Physics', 'WAM Madhavi'),
(72, 'PH 1006', 'Mechanics and Thermodynamics', 'semester 2', 2, 2, 150, 'Physics', 'IMK Fernando'),
(73, 'PH 1023', 'Physics Laboratory II', 'semester 2', 4, 2, 10, 'Physics', 'MS Gunewardene'),
(74, 'CH 1012', 'Organic Chemistry', 'semester 2', 3, 3, 50, 'Chemistry', 'DTU Abeytunga'),
(75, 'CH 1006', 'Impact of Chemistry on Society', 'semester 2', 2, 2, 100, 'Chemistry', 'SM Vithanarachchi'),
(76, 'AM 1013', 'Differential Equations I', 'semester 2', 2, 2, 150, 'Mathematics', 'H.C.Y. Jayathunga'),
(77, 'AM 1014', 'Applied Linear Algebra', 'semester 2', 2, 2, 200, 'Mathematics', 'D.R. Jayewardene'),
(78, 'AM 1015', 'Computational Mathematics I', 'semester 2', 2, 2, 200, 'Mathematics', 'K.K.W. Hasitha Erandi'),
(79, 'ST 1010', 'Statistical Theory', 'semester 2', 2, 2, 200, 'Statistics', 'R. A. B. Abeygunawardhana'),
(80, 'ST 1011', 'Introduction to Surveys', 'semester 2', 2, 2, 150, 'Statistics', 'G.H.S. Karunarathna'),
(81, 'ST 1012', 'Basic Statistical Computing', 'semester 2', 2, 2, 100, 'Statistics', 'G.A.C.N Priyadharshani'),
(82, 'PM 1013', 'Basic Analysis I', 'semester 2', 2, 2, 150, 'Mathematics', 'D.B. Dharmasena'),
(83, 'PM 1014', 'History as Motivation for Mathematics', 'semester 2', 2, 2, 150, 'Mathematics', 'B. L. Samarasekera'),
(84, 'CS 1101', 'Fundamentals of Programming', 'semester 2', 3, 3, 250, 'UCSC', 'Dr.samantha'),
(85, 'NS 1002', 'Nuclear Techniques', 'semester 2', 1, 1, 10, 'Nuclear Science', 'Dr.Def'),
(86, 'NS 1003', 'Computational Methods in Nuclear Science', 'semester 2', 2, 2, 14, 'Nuclear Science', 'Dr.GHI'),
(87, 'BT 1012', 'Variety of Plant and Microbial Life', 'semester 2', 2, 2, 50, 'Plant Science', 'HS Kathriarachchi'),
(88, 'BT 1010', 'Variety of Plant and Microbial Life Practicals', 'semester 2', 1, 1, 50, 'Plant Science', 'HS Kathriarachchi'),
(89, 'BT 1114', 'Flora of Sri Lanka', 'semester 2', 1, 1, 50, 'Plant Science', 'SMW Ranwala'),
(90, 'ZL 1008', 'Variety of Animal Life', 'semester 2', 3, 3, 150, 'Zoology and Environment Science', 'M. R. Wijesinghe'),
(91, 'ZL 1010', 'Animal Behavior', 'semester 2', 2, 2, 140, 'Zoology and Environment Science', 'S. .Premawansa'),
(92, 'IS 1008', 'Introduction to Probability and Distributions', 'semester 2', 3, 3, 140, 'Statistics', 'J.H.D.S.P. Tissera'),
(93, 'IS 1009', 'Introduction to Survey Design', 'semester 2', 2, 2, 130, 'Statistics', 'G.A.C.N Priyadharshani'),
(94, 'FM 1012', 'Mathematical Methods for Finance I', 'semester 2', 2, 2, 150, 'Mathematics', 'T.U. Hewage'),
(95, 'FM 1014', 'Computational Financial mathematics I', 'semester 2', 2, 2, 150, 'Mathematics', 'S.A.K.P. de Silva'),
(96, 'MS 1003', 'Operational Research I', 'semester 2', 2, 2, 130, 'Statistics', 'K.A.D. Deshani');

-- --------------------------------------------------------

--
-- Table structure for table `fourth_year`
--

CREATE TABLE `fourth_year` (
  `id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `semester` enum('semester 1','semester 2') NOT NULL,
  `no_of_hours` int(1) NOT NULL,
  `credits` int(11) DEFAULT NULL,
  `registered_students` int(10) NOT NULL,
  `department` varchar(50) NOT NULL,
  `Lecturer_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fourth_year`
--

INSERT INTO `fourth_year` (`id`, `course_id`, `course_name`, `semester`, `no_of_hours`, `credits`, `registered_students`, `department`, `Lecturer_name`) VALUES
(1, 'CS 4001', 'CS', 'semester 1', 1, NULL, 100, 'UCSC', 'aaa'),
(2, 'Item 4.2', 'E', 'semester 1', 1, NULL, 20, 'Chemistry', 'Dr.Kavindu'),
(3, 'Item 4.3', 'G', 'semester 1', 2, NULL, 0, 'Mathematics', '');

-- --------------------------------------------------------

--
-- Table structure for table `lecture`
--

CREATE TABLE `lecture` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` enum('Physics','Chemistry','Mathematics','Statistics','Zoology and Environment Science','Plant Science','UCSC','ITU','Nuclear Science') DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `expired_otp` datetime DEFAULT NULL,
  `create_otp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecture`
--

INSERT INTO `lecture` (`id`, `name`, `department`, `email`, `password`, `otp`, `expired_otp`, `create_otp`) VALUES
(6, 'Janaka Adassuriya', 'Physics', 'janaka@phys.cmb.ac.lk', '$2y$10$gpu9vZSHN/DjfL2tmBAZsePExajV..lK6.09HD8h2gUwW1MjMBwJe', NULL, NULL, NULL),
(7, 'KPSC Jayaratne', 'Physics', 'chandana@phys.cmb.ac.lk', '$2y$10$r83R5IbEvoZf79EtwcaaZeqBUkEH0BzMtR28w64OUqiR8EF7/ORLG', NULL, NULL, NULL),
(8, 'J.K. Wijerathna', 'Mathematics', 'jagath@maths.cmb.ac.lk', '$2y$10$dj/mES9sneD2O2Ox5XD9OOiVPCweTvzd7TuT8yJPxawXRNygWj4jG', NULL, NULL, NULL),
(9, 'P. D. D. Gallage', 'Mathematics', 'dilruk@sci.cmb.ac.lk', '$2y$10$01lnGsWkpR.dYcJ9nf3KweVtgcQo940xRHpCylVWOqjmcUNbg/k5C', NULL, NULL, NULL),
(10, 'C. J. Wijeratne', 'Mathematics', 'cjw@maths.cmb.ac.lk', '$2y$10$.pY7sEvIYh.2olJC4AuML.5dMpSc9Av4TcnmmnN0MIgD.ntSvdJGS', NULL, NULL, NULL),
(11, 'B. L. Samarasekera', 'Mathematics', 'lilanthi@maths.cmb.ac.lk', '$2y$10$ou2UnfBtyvcf9Vc5AnP20uR1PinyD8tTAZmG3dEGOOKskxWnU7Onm', NULL, NULL, NULL),
(12, 'S. S. N. Perera', 'Mathematics', 'ssnp@maths.cmb.ac.lk', '$2y$10$lZap5j/g4.Lrn2hGkUl5uOmF/o3nZfu9YxKR0TppHAudqmp5jU56a', NULL, NULL, NULL),
(13, 'S.A.K.P. de Silva', 'Mathematics', 'kdesilva@maths.cmb.ac.lk', '$2y$10$4Vkxi.somCNoRSSBM9MXzuyszN3JcVw9j7mgTPYEe1Ghep58F21E2', NULL, NULL, NULL),
(14, 'L.C. Edussuriya', 'Mathematics', 'lakshmie@maths.cmb.ac.lk', '$2y$10$9u4.fXe0Kop3G2Q1lDW.DupFNE2xkQOm3rIGUu/yJS4d4zL6uKNK6', NULL, NULL, NULL),
(15, 'H.C.Y. Jayathunga', 'Mathematics', 'yashika@maths.cmb.ac.lk', '$2y$10$84TVRCQC2GtMUum4WIxL9OpirSSRo3xGL3fY51DOakIw1jO1t8eWC', NULL, NULL, NULL),
(16, 'D.R. Jayewardene', 'Mathematics', 'romaine@maths.cmb.ac.lk', '$2y$10$NrdhNtlIzNSB/UicQnwDwu8EEvdrOS4HcelaRcHf5MdJP3cJLzgwy', NULL, NULL, NULL),
(17, 'K.K.W. Hasitha Erandi', 'Mathematics', 'erandi@maths.cmb.ac.lk', '$2y$10$Wu.GcDpZqZoUeRVbLVm9s.LPP8RnDRCOW3cwKqdDpJY1usZI3eFlu', NULL, NULL, NULL),
(18, 'D.B. Dharmasena', 'Mathematics', 'dayaldh@sci.cmb.ac.lk', '$2y$10$qRnQEp6i8Fxd1DFhNwELguJfT/qEr2i1KXfF88.G.MUKKGulp.JjG', NULL, NULL, NULL),
(19, 'H.C.Y. Jayathunga', 'Mathematics', 'yashika@maths.cmb.ac.lk', '$2y$10$Hb2YoJNcLBxD1wDnXccDq.VNRDVavEIdTUNZrJU6JstRWconoEkoG', NULL, NULL, NULL),
(20, 'K.M.N de Silva', 'Chemistry', 'kmnd@chem.cmb.ac.lk', '$2y$10$DWPacP.xaFQXOnrQ/czusO2u4af17Z.fWpyBMGYl1/zitAlL3mbre', NULL, NULL, NULL),
(21, 'K.R.R Mahanama', 'Chemistry', 'maha@chem.cmb.ac.lk', '$2y$10$U1q58IcsAttahdkRZSpo0eGuIO8/z8xTCbtSV5J19TQ2u5SuEMhHC', NULL, NULL, NULL),
(22, 'LHR Perera', 'Chemistry', 'hasini.perera@sci.cmb.ac.lk', '$2y$10$GlO.dN4XNgpksAHciDLE/uzD1ky0HIjU3K2owDENE5AePii3Dr5Y2', NULL, NULL, NULL),
(23, 'G.A.C.N Priyadharshani', 'Statistics', 'chandi@stat.cmb.ac.lk', '$2y$10$otK3.XQcxEBU5Zouilwlf.f156tuHCTfhakDpmunGtAJpaS96xnQi', NULL, NULL, NULL),
(24, 'E. R. A. D. Bandara', 'Statistics', 'anjana@stat.cmb.ac.lk', '$2y$10$sBU/obmb.av5t4UnTMudm.C7AAy.djKzc11lu40Re9VX7.hLs9ltW', NULL, NULL, NULL),
(25, 'M.D.T.Attygalle', 'Statistics', 'dilhari@stat.cmb.ac.lk', '$2y$10$yb4s72hSxasTqFju9J9.OeeVRfUTrGcM5fn3H9BbmIBrJg.GbLPge', NULL, NULL, NULL),
(26, 'Dr.samantha', 'UCSC', 'samantha@cmb.ac.lk', '$2y$10$SbCNfnArGqhhgknIoK8efOZmd8ZRnTMR9.niF21bDJwwNBOz22Z3.', NULL, NULL, NULL),
(27, 'T. D. Silva', 'Plant Science', 'tara@pts.cmb.ac.lk', '$2y$10$Yv7YiAXxs1TSxl0Kzl1XV.pOxGMCoAPn6GFIJQ3DQQ.97fuWgeUJa', NULL, NULL, NULL),
(28, 'IAJK Dissanayake', 'Plant Science', 'jkdissanayake@pts.cmb.ac.lk', '$2y$10$yxUPFXcu1FmO7tYCy.BVqu8UFdoHQ7cr3nRpwXLsb5srwAuTTPppK', NULL, NULL, NULL),
(29, 'H.I.U. Caldera', 'Plant Science', 'iroja@cmb.ac.lk', '$2y$10$RNtv2/K9KOMc6iKIiD8LeumrNYUxHfUPa0bUtOPpO0VOw5rfSX9XC', NULL, NULL, NULL),
(30, 'WAM Madhavi', 'Physics', 'monika.madhavi@phys.cmb.ac.lk', '$2y$10$oQ0RGq9JZLQuBBlnDqDS6OJdoyQgn2ZEgzbUxdUaVnMWLyQho.n4G', NULL, NULL, NULL),
(31, 'Dr.abc', 'Nuclear Science', 'abc@cmb.ac.lk', '$2y$10$iuY77INDYxmwTNS4W6m.POHJ8ZPfnpifLNLSa8p4xPboUh50xjqf.', NULL, NULL, NULL),
(32, 'S. S. Seneviratne', 'Zoology and Environment Science', 'sam@sci.cmb.ac.lk', '$2y$10$RPg44sU8UITV3WgyxVbWSuojxhbnzuPqhrgtw81KThLcbN9XXLzNK', NULL, NULL, NULL),
(33, 'D. Halwathura', 'Zoology and Environment Science', 'devan.halwatura@zoology.cmb.ac.lk', '$2y$10$VeELZB1SFjgwJ5pi36Yh6.7ZmNLBWwcdmhQO682o5URYRc0tVXtr2', NULL, NULL, NULL),
(34, 'H.A.S.G. Dharmarathne', 'Statistics', 'sameera@stat.cmb.ac.lk', '$2y$10$xAI0InotInsWaGIEyM9IgOl7IZS9B2nyfAVd9lyuGoonlGkVUuB22', NULL, NULL, NULL),
(35, 'Oshada Senaweera', 'Statistics', 'oshada@stat.cmb.ac.lk', '$2y$10$TeoQV8YvzDyXyujasLv9yupHsErPFMmFmWEaGskTizRtCVvPGb5r6', NULL, NULL, NULL),
(36, 'M.G.G Hemakumara', 'Statistics', 'hemakumara@cmb.ac.lk', '$2y$10$RZS0/saZsrmF4zJkE9TGpe93/8fqJtfQg1MuVxI4bHqNJ7Jo7sH26', NULL, NULL, NULL),
(37, 'WMKP Wijayaratna', 'Physics', 'kani@phys.cmb.ac.lk', '$2y$10$7jllAmTxDvOKq9kUoRVUiuE4KPAoX/ptZ2aJZb9moz4GD0XPr91La', NULL, NULL, NULL),
(38, 'HHE Jayaweera', 'Physics', 'hiran@phys.cmb.ac.lk', '$2y$10$h/IKM7wMI5ynMQSnBUZ3Z.Mu2i2bBLmlhoo.HaYub1dP/nBb77MWO', NULL, NULL, NULL),
(39, 'DL Weerawarne', 'Physics', 'dweerawa@phys.cmb.ac.lk', '$2y$10$GHZdpwCU/IHIKXxly7Mrlur6Jojz8q8PmrIk49RA1bkseaYJbazte', NULL, NULL, NULL),
(40, 'SHRT Sooriyagoda', 'Physics', 'thanuja@phys.cmb.ac.lk', '$2y$10$nNdxCdOiwYE8hMexBojRGuaxh.Fc0nF/2W/bmr0lJ8MhdTf4hGWzC', NULL, NULL, NULL),
(42, 'EMD Siriwardane', 'Physics', 'dilanga@phys.cmb.ac.lk', '$2y$10$vjbClzG4BQ5Stq6AGPLH/Osl.xKVTO9ocG2uo9qm0CZEwtwq0Iq6e', NULL, NULL, NULL),
(43, 'ERAD Bandara', 'Physics', 'anjana@stat.cmb.ac.lk', '$2y$10$TveEoRdzmPp0tDP4vx3ogOrOp5GTLrOey1D2T3W/BfOkvw.egihoW', NULL, NULL, NULL),
(44, 'KRR Mahanama', 'Chemistry', 'maha@chem.cmb.ac.lk', '$2y$10$KGosx8AswPaAve4dgkjYGucHIuu./q8OgAKHqbvdR9kYiHzh5x/rC', NULL, NULL, NULL),
(45, 'RD Wijesekera', 'Chemistry', 'ramanee@chem.cmb.ac.lk', '$2y$10$lbxTrHgoRwDGoaR4prFJS.a4j/RpYiOxiyPUc8AzMoqn35Jho9Q1e', NULL, NULL, NULL),
(46, 'MN Kaumal', 'Chemistry', 'mnkaumal@sci.cmb.ac.lk', '$2y$10$juNpuHHa0SzlJbXttb7wUuQtGuc9XfiEpAkq1uDZoygBAz5oKeD.a', NULL, NULL, NULL),
(47, 'A Tillekaratne', 'Chemistry', 'taashani@chem.cmb.ac.lk', '$2y$10$x1wr8RmUnVAx6JXNBchIJOy5ZGll5AtQxepTySRwY83eMXsG0Skda', NULL, NULL, NULL),
(48, 'S Samarasinghe', 'Chemistry', 'samarasinghe@cmb.ac.lk', '$2y$10$//W9FH5YWM86yFZUpVlRIODlciX5Om6EUdfk05pqTR4BbV9qDsatW', NULL, NULL, NULL),
(49, 'Nimal Punyasiri', 'Chemistry', 'nimal@cmb.ac.lk', '$2y$10$D33anQLLwzsK7jIzg3JF2eSlF5ug0/rCMeQvKlX8eWx0QL4k0kQba', NULL, NULL, NULL),
(50, 'HDSM Perera', 'Chemistry', 'sachindra.perera@chem.cmb.ac.lk', '$2y$10$A.SKEMRrS001qjmVM.78xuuswUxmiUgWXDcZpNDRrh7T8QNDSkm9.', NULL, NULL, NULL),
(51, 'WRM de Silva', 'Chemistry', 'rohini@chem.cmb.ac.lk', '$2y$10$TUNVixIAP3BgRmbJDMHszuwJW66oSCaPQv.QClRXn/B4ad8ejL/ly', NULL, NULL, NULL),
(52, 'KMN de Silva', 'Physics', 'kmnd@chem.cmb.ac.lk', '$2y$10$030.S08AFYRBUrzajuT40uAqApWMtKbpVvhz5.XWT6NstkZsmcgvW', NULL, NULL, NULL),
(53, 'C.J. Jayawardena', 'Mathematics', 'c_jayawardene@maths.cmb.ac.lk', '$2y$10$RZJkM9vaEMKFpRicWYNa4OHJ35MVvIEyK/O/m2OK1w32MM1aemLWW', NULL, NULL, NULL),
(54, 'D. R. Jayewardene', 'Mathematics', 'romaine.jayewardene@maths.cmb.ac.lk', '$2y$10$lDwfbIQ2CtdWlveXZE7MTe1n6DZg7N.kQgZ4mewIOwyOAUVtQbk/6', NULL, NULL, NULL),
(55, 'D.T. Tillkaratne', 'Mathematics', 'damith@maths.cmb.ac.lk', '$2y$10$ZVtC7wsTJ/hGGrxfsz3e6uDxN6tfrZQ9lwv4c8golrkuuk8qTX4Z6', NULL, NULL, NULL),
(56, 'A. C. Mahasinghe', 'Mathematics', 'anuradhamahasinghe@maths.cmb.ac.lk', '$2y$10$g.yZ4/K.lpxuPJYQF79IGO.DLrKS1nbYhyH2Sp2JvpM1ClS2rGTcG', NULL, NULL, NULL),
(57, 'U. Prabath Liyanage', 'Mathematics', 'prabath@cmb.ac.lk', '$2y$10$5XhW50Q6x/ACW2axrivEpujyXXBOn4Sk.W04PFshM1knF2bSwIcMi', NULL, NULL, NULL),
(58, 'T.U. Hewage', 'Mathematics', 'thilan@maths.cmb.ac.lk', '$2y$10$sSbq8XiiZ97gs434dSF70u1w7dYX1CIAbWiBzi2dLcIAkqLCOnfWW', NULL, NULL, NULL),
(59, 'R. A. B. Abeygunawardhana', 'Statistics', 'rab__abey@stat.cmb.ac.lk', '$2y$10$PJginEE3MhL1DJ60vuxu3OPuB63n/uVVNf3CSEaMnnzHUiUZin55.', NULL, NULL, NULL),
(60, 'C.H. Magalla', 'Statistics', 'champa@stat.cmb.ac.lk', '$2y$10$eALPMv4zCSTnri1VP5VfsecwQXiQR9sd7LJKU7qhAg5LO4XdwQg1G', NULL, NULL, NULL),
(61, 'C.D. Tilakaratne', 'Statistics', 'cdt@stat.cmb.ac.lk', '$2y$10$1YjbbwCnj.gpds0xcyljFui/1l0awW9nhEBtrrJZxmK7SddE7Rz4C', NULL, NULL, NULL),
(62, 'J.H.D.S.P. Tissera', 'Statistics', 'dilshani@stat.cmb.ac.lk', '$2y$10$gUzPKwl51ib2TAgma5SFf.skmFW/aL44QLIjRy1BvCe6Lk2JKO8Yu', NULL, NULL, NULL),
(63, 'S.D. Viswakula', 'Statistics', 'sam@stat.cmb.ac.lk', '$2y$10$7j9YymcEzm0xNCWsCIXYB.vcfranv9aYjPRvb9lqWXJcIRA2BRG36', NULL, NULL, NULL),
(64, 'R.V. Jayatillake', 'Statistics', 'rasika@stat.cmb.ac.lk', '$2y$10$k4HZms60Du3lWVMje.b84O.rZqScFzKXQGZaDGa.JVzzjaWoxtU.C', NULL, NULL, NULL),
(65, 'A.A.Sunethra', 'Statistics', 'sunethra@stat.cmb.ac.lk', '$2y$10$YUQ9qQ9VrjUxOU3q7P0yv.qdd6V7F3gxZD5Xh8Hqbmb.LG9TRvmBW', NULL, NULL, NULL),
(66, 'K.G.S.U. Ariyawansa', 'Plant Science', 'sameera@pts.cmb.ac.lk', '$2y$10$NUnBls65YFroiiljdckms.oYL8N4El6bipZqaH/YxlFVocKDzIRra', NULL, NULL, NULL),
(67, 'A Wickramasuriya', 'Plant Science', 'anushka@pts.cmb.ac.lk', '$2y$10$HZrQJjsDF/Rnl8zx6bFq5u6TY/MvOf/5h8MnYdg1t8oI36WAIMbPy', NULL, NULL, NULL),
(68, 'D. Bandupriya', 'Plant Science', 'dbandupriya@pts.cmb.ac.lk', '$2y$10$qJQYe23EqjliRfBhhKXg5evO15WTsitizwOBVGkx6hefrN8dy0GVq', NULL, NULL, NULL),
(69, 'R. Mayakaduwa', 'Plant Science', 'ruwani.mayakaduwa@pts.cmb.ac.lk', '$2y$10$oTm1Qv0DiRpc6KBci0lQOOQtprAsI4eikWh5lDIFN2zi.eToM2an6', NULL, NULL, NULL),
(70, 'HS Kathriarachchi', 'Plant Science', 'hashi@pts.cmb.ac.lk', '$2y$10$xyNtv0KYc4xtwECS4tfpHetX.pSOiCaIhFui.UQOw87K2m9.euNvK', NULL, NULL, NULL),
(71, 'C.M. Nanayakkara', 'Plant Science', 'chandi@pts.cmb.ac.lk', '$2y$10$QOHETxQsSzxyf1Ln4HnjnOHLu61lUirYerDlzNXXk/t51qqdQn3A.', NULL, NULL, NULL),
(72, 'D. K. Weerakoon', 'Zoology and Environment Science', 'devaka@sci.cmb.ac.lk', '$2y$10$YgPs.dkZxWwru//ZstSpA.DPCV.F0jsA/0lBQCzTPn.Ue3vzAKO.K', NULL, NULL, NULL),
(73, 'A. Witharana', 'Zoology and Environment Science', 'ayomi@zoology.cmb.ac.lk', '$2y$10$Sjyz9nlruEnsxHufCWZME.V30H6FdD4ionAuNSjYA8JHs41fEz3ze', NULL, NULL, NULL),
(74, 'I.C. Perera', 'Zoology and Environment Science', 'icperera@sci.cmb.ac.lk', '$2y$10$vY4jHyoiKBI6CHakb3FRAOvUZ8pnrLhgIHeTt/YRxCEw1MXaSM9MO', NULL, NULL, NULL),
(75, 'Maheshi Mapalagamage', 'Zoology and Environment Science', 'maheshi@zoology.cmb.ac.lk', '$2y$10$yC/xnwICMApGBZp2acmbb.ye/vIwe1Xmcr2RvRH8jDO/yk0qzgUsG', NULL, NULL, NULL),
(76, 'M. R. Wijesinghe', 'Zoology and Environment Science', 'mayuri@zoology.cmb.ac.lk', '$2y$10$rgWoj1TsmX4nKQv1fEVPF.MNteHM8XcniWAwcFMcS148Z59nboRGS', NULL, NULL, NULL),
(77, 'Kalpani Marasinghe', 'Zoology and Environment Science', 'kalpani@zoology.cmb.ac.lk', '$2y$10$DvSy/7LzfnY2o2GR/rF5l.ht6x9kGxlj1on9yjuEg.WawEb.rLxOy', NULL, NULL, NULL),
(78, 'S. Amarasekara', 'Zoology and Environment Science', 'sachini@zoology.cmb.ac.lk', '$2y$10$pwun1gMMge024bv1O5flvOlWcfBc/M7XeqVmXzKqpDiuw0b8fYw5i', NULL, NULL, NULL),
(79, 'G. H. Galhena', 'Zoology and Environment Science', 'gayani@zoology.cmb.ac.lk', '$2y$10$oJADsPwERcbNO.dOGLOWhe74ADjOVsQ2zvc./Iqq6yorOMA5Tq//e', NULL, NULL, NULL),
(80, 'I.T. Jayamanne', 'Statistics', 'imali@stat.cmb.ac.lk', '$2y$10$lbQAtD5Rcq4eCRNviY7SQubrCia3OfCcAbLGqxrpXTNTF7bKhnp1K', NULL, NULL, NULL),
(81, 'K.A.D. Deshani', 'Statistics', 'deshani@stat.cmb.ac.lk', '$2y$10$NLp..R0/PvuNLC4Ud9drfOKIrxOeFeTj.NjcURkhAQqnoEQqb6du2', NULL, NULL, NULL),
(82, 'G.H.S. Karunarathna', 'Statistics', 'hasani@stat.cmb.ac.lk', '$2y$10$dXbKvOHuA8ET3dINsKxcveIkiuPNwM9uU2TmpPKQnbnv0HDwZyK2.', NULL, NULL, NULL),
(83, 'Gayan Akmeemana', 'Physics', 'gayan@fos.cmb.ac.lk', '$2y$10$ESpjJo7AFsHLsGbdUJYJOuReasReIl2n6OVDzn1chR1UfiJJLuGga', NULL, NULL, NULL),
(84, 'MMGNK Abayaruwan', 'Plant Science', 'abayaruwan@cmb.ac.lk', '$2y$10$W234mQfr6TWOG7UnXu2MIOZHnyI.BVigZeiySy9JiG92c//UU.3v6', NULL, NULL, NULL),
(85, 'S.A.T.A. Perera', 'Plant Science', 'thilini@pts.cmb.ac.lk', '$2y$10$fyNcvqdYFkPup/8UJAzdGu9hh3OM4QNh634WulVO8kNRHsnCPrJ.a', NULL, NULL, NULL),
(86, 'G.N. Karunathunge', 'Mathematics', 'nilusha@maths.cmb.ac.lk', '$2y$10$2objsZbRkAuYuK077/toKuFlHXpyy1Ra1OCOTuFA2Y5ZTejD7xo92', NULL, NULL, NULL),
(87, 'ITU1', 'ITU', 'itu1@cmb.ac.lk', '$2y$10$yuhnzcddpCFy3d.whRknnOUwHuTZdpXck2SXbK3n/OKK86/dv1eou', NULL, NULL, NULL),
(88, 'ITM1', 'ITU', 'itm1@cmb.ac.lk', '$2y$10$Pkr0mcmzqo.zhKJtWeUYkepaTHUToE/zz5EAxaytqb15FbfxIzaGC', NULL, NULL, NULL),
(89, 'itm2@cmb.ac.lk', 'ITU', 'itm2@cmb.ac.lk', '$2y$10$J.InDwRXt4si8cA5aKvUjur.qm.kT5C6xz38ywZw3RVtXjObbugim', NULL, NULL, NULL),
(90, 'Dr.Def', 'Nuclear Science', 'def@cmb.ac.lk', '$2y$10$8Vv/Pq0A1xs8K/Gu3Hv23.GQtV9KuXCJcLjqflRBEI5JGInoDQVJa', NULL, NULL, NULL),
(91, 'Dr.GHI', 'Nuclear Science', 'ghi@cmb.ac.lk', '$2y$10$V6xFarosTHnsZsgPnnsKVeYDJM4yed8I22rJswRtMJokUIsfnaPGO', NULL, NULL, NULL),
(92, 'IMK Fernando', 'Physics', 'fernando@phys.cmb.ac.lk', '$2y$10$XPT.yqFmgGRSOMZfrO.5SedndoPBFCFDAMLlMU5jBnBXRfSkOj.T.', NULL, NULL, NULL),
(93, 'MS Gunewardene', 'Physics', 'gunewardene@cmb.ac.lk', '$2y$10$fhong2tP0Fbzgbix8YEfjuMAKCcbs4Y1BsOSgx4.T/i8URsombdee', NULL, NULL, NULL),
(94, 'DTU Abeytunga', 'Chemistry', 'abeytunga@cmb.ac.lk', '$2y$10$YD0csQk8Ecv0QjwhtXDi.uWqBG3l21oldwsrOIdUAkk73pxGCPWdq', NULL, NULL, NULL),
(95, 'SM Vithanarachchi', 'Chemistry', 'vithanarachchi@cmb.ac.lk', '$2y$10$CR6kKpcxmVrm0gxkBokxRefQDEIsHikGapjvqa6AV3chAiwVvSEY6', NULL, NULL, NULL),
(96, 'SMW Ranwala', 'Plant Science', 'ranwala@cmb.ac.lk', '$2y$10$x/OB.KE92YZhcLhfOgnD6ezZrHrBzgF3e8wJ3fNASNFUZXFfYJN4G', NULL, NULL, NULL),
(97, 'S. .Premawansa', 'Zoology and Environment Science', 'prem@cmb.ac.lk', '$2y$10$UTft0cDvPgGnXGB2lBQGHe/NKJxS6gOBHFG/3WlaiCxSkDouWYu0u', NULL, NULL, NULL),
(99, 'DR. Ucsc1', 'UCSC', 'ucsc@cmb.ac.lk', '$2y$10$gZ.HGgTGQe0xkJOjug2SR.CBc2NmPH96XIGa4UopUBhSuSee.Ohxy', NULL, NULL, NULL),
(100, 'P.V.D.G.N Silva', 'Chemistry', 'gaya111@cmb.ac.lk', '$2y$10$txzj17OGHPcbEvz1lMoMZ.ERHo3w/9E9ouPYP6xhg.UQ5bJxn0CU.', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lecture_halls`
--

CREATE TABLE `lecture_halls` (
  `hall_id` int(10) NOT NULL,
  `hall_name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `lecture_halls`
--

INSERT INTO `lecture_halls` (`hall_id`, `hall_name`, `capacity`, `category`) VALUES
(1, 'KGH', 250, 'Lecturehall'),
(2, 'IFS', 50, 'Lab'),
(3, 'PLR', 100, 'Lab'),
(4, 'NPLT', 100, 'Lecturehall'),
(5, 'NLT', 150, 'Lecturehall'),
(8, 'ILC', 300, 'Lecturehall'),
(9, 'CLT', 300, 'Lecturehall'),
(10, 'CLab', 100, 'Lab');

-- --------------------------------------------------------

--
-- Table structure for table `offer_courses`
--

CREATE TABLE `offer_courses` (
  `id` int(11) NOT NULL,
  `course_id` varchar(20) NOT NULL,
  `department` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offer_courses`
--

INSERT INTO `offer_courses` (`id`, `course_id`, `department`) VALUES
(29, 'Item 3.2', 'Physics'),
(30, 'Item 4.2', 'Mathematics'),
(31, 'Item 4.2', 'UCSC'),
(33, 'Item 3.1', 'UCSC'),
(34, 'CS 4001', 'Chemistry'),
(42, 'PH3037', 'ITU');

-- --------------------------------------------------------

--
-- Table structure for table `saved_courses`
--

CREATE TABLE `saved_courses` (
  `id` int(11) NOT NULL,
  `row` varchar(255) NOT NULL,
  `col` varchar(255) NOT NULL,
  `year` varchar(50) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `registered_students` int(11) DEFAULT NULL,
  `lecturer_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saved_courses`
--

INSERT INTO `saved_courses` (`id`, `row`, `col`, `year`, `semester`, `course_name`, `registered_students`, `lecturer_name`) VALUES
(460, 'PH 1005', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(462, 'PH 1005', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(463, 'PH 1005', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(464, 'PH 1005', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(465, 'PH 1005', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(466, 'PH 1007', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(468, 'PH 1007', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(469, 'PH 1007', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(470, 'PH 1007', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(471, 'PH 1007', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(479, 'AM1011', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(480, 'AM1011', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(482, 'AM1011', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(483, 'AM1011', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(484, 'AM1011', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(485, 'AM1011', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(486, 'AM1012', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(487, 'AM1012', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(489, 'AM1012', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(490, 'AM1012', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(491, 'AM1012', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(492, 'AM1012', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(493, 'PM 1011', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(494, 'PM 1011', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(496, 'PM 1011', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(497, 'PM 1011', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(498, 'PM 1011', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(499, 'PM 1011', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(500, 'PM 1011', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(501, 'PM 1011', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(502, 'PM 1011', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(503, 'PM 1012', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(504, 'PM 1012', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(506, 'PM 1012', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(507, 'PM 1012', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(508, 'PM 1012', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(509, 'PM 1012', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(510, 'FM 1011', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(511, 'FM 1011', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(512, 'FM 1011', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(513, 'FM 1011', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(514, 'FM 1013', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(515, 'FM 1013', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(516, 'FM 1013', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(517, 'FM 1013', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(518, 'MS 1011', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(519, 'MS 1011', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(520, 'MS 1011', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(521, 'MS 1011', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(522, 'AM 1108', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(530, 'CH1008', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(531, 'CH1008', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(533, 'CH1008', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(534, 'CH1008', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(535, 'CH1008', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(536, 'CH1008', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(537, 'CH1010', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(538, 'CH1010', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(540, 'CH1010', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(541, 'CH1010', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(542, 'CH1010', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(543, 'CH1010', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(544, 'CH1011', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(545, 'CH1011', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(547, 'CH1011', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(548, 'CH1011', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(549, 'CH1011', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(550, 'CH1011', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(551, 'ST1006', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(552, 'ST1006', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(554, 'ST1006', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(555, 'ST1006', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(556, 'ST1006', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(557, 'ST1006', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(558, 'ST 1008', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(559, 'ST 1008', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(561, 'ST 1008', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(562, 'ST 1008', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(563, 'ST 1008', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(564, 'ST 1008', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(565, 'ST1009', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(566, 'ST1009', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(568, 'ST1009', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(569, 'ST1009', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(570, 'ST1009', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(571, 'ST1009', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(576, 'CS 1102', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(577, 'CS 1102', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(579, 'CS 1102', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(580, 'CS 1102', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(581, 'CS 1102', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(582, 'CS 1102', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(583, 'NS1001', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(584, 'BT1011', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(585, 'BT1009', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(586, 'BT1008', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(587, 'BT 1013', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(588, 'ZL1009', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(589, 'CH1008', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(590, 'CH1010', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(591, 'CH1011', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(592, 'CS 1102', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(593, 'CS 1102', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(594, 'CS 1102', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(595, 'CS 1102', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(596, 'NS1001', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(597, 'NS1001', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(599, 'NS1001', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(600, 'NS1001', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(601, 'NS1001', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(602, 'NS1001', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(603, 'EN 1008', 'AM 1108', 'first_year', 'semester 1', 'Mathematics for Biological Sciences', 102, 'L.C. Edussuriya'),
(604, 'IS 1006', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(605, 'IS 1006', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(606, 'IS 1006', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(607, 'IS 1006', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(608, 'IS1007', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(609, 'IS1007', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(610, 'IS1007', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(611, 'IS1007', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(612, 'MS 1001', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(613, 'MS 1001', 'FM 1011', 'first_year', 'semester 1', 'Financial Mathematics I', 100, 'S. S. N. Perera'),
(614, 'MS 1001', 'FM 1013', 'first_year', 'semester 1', 'Linear Programming', 80, 'S.A.K.P. de Silva'),
(615, 'MS 1001', 'MS 1011', 'first_year', 'semester 1', 'Computing for Finance', 80, 'S. S. N. Perera'),
(616, 'PH 1005', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(617, 'PH 1005', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(618, 'PH 1005', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(619, 'PH 1005', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(620, 'PH 1005', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(621, 'PH 1005', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(622, 'PH 1005', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(623, 'PH 1005', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(624, 'PH 1007', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(625, 'PH 1007', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(626, 'PH 1007', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(627, 'PH 1007', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(628, 'PH 1007', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(629, 'PH 1007', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(630, 'PH 1007', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(631, 'PH 1007', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(640, 'AM1011', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(641, 'AM1011', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(642, 'AM1011', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(643, 'AM1011', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(644, 'AM1011', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(645, 'AM1011', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(646, 'AM1011', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(647, 'AM1011', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(648, 'AM1012', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(649, 'AM1012', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(650, 'AM1012', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(651, 'AM1012', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(652, 'AM1012', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(653, 'AM1012', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(654, 'AM1012', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(655, 'AM1012', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(656, 'PM 1011', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(657, 'PM 1011', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(658, 'PM 1011', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(659, 'PM 1011', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(660, 'PM 1011', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(661, 'PM 1011', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(662, 'PM 1011', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(663, 'PM 1011', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(664, 'PM 1011', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(665, 'PM 1011', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(666, 'PM 1011', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(667, 'PM 1012', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(668, 'PM 1012', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(669, 'PM 1012', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(670, 'PM 1012', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(671, 'PM 1012', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(672, 'PM 1012', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(673, 'PM 1012', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(674, 'PM 1012', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(675, 'FM 1011', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(676, 'FM 1011', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(677, 'FM 1011', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(678, 'FM 1011', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(679, 'FM 1013', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(680, 'FM 1013', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(681, 'FM 1013', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(682, 'FM 1013', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(683, 'MS 1011', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(684, 'MS 1011', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(685, 'MS 1011', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(686, 'MS 1011', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(687, 'AM 1108', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(688, 'AM 1108', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(689, 'AM 1108', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(690, 'AM 1108', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(691, 'AM 1108', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(692, 'AM 1108', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(693, 'AM 1108', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(694, 'AM 1108', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(695, 'AM 1108', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(696, 'AM 1108', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(697, 'CH1008', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(698, 'CH1008', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(699, 'CH1008', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(700, 'CH1008', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(701, 'CH1008', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(702, 'CH1008', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(703, 'CH1008', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(704, 'CH1008', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(705, 'CH1008', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(706, 'CH1008', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(707, 'CH1008', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(708, 'CH1008', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(709, 'CH1008', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(710, 'CH1008', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(711, 'CH1010', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(712, 'CH1010', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(713, 'CH1010', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(714, 'CH1010', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(715, 'CH1010', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(716, 'CH1010', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(717, 'CH1010', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(718, 'CH1010', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(719, 'CH1010', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(720, 'CH1010', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(721, 'CH1010', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(722, 'CH1010', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(723, 'CH1010', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(724, 'CH1010', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(725, 'CH1011', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(726, 'CH1011', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(727, 'CH1011', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(728, 'CH1011', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(729, 'CH1011', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(730, 'CH1011', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(731, 'CH1011', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(732, 'CH1011', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(733, 'CH1011', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(734, 'CH1011', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(735, 'CH1011', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(736, 'CH1011', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(737, 'CH1011', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(738, 'CH1011', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(739, 'ST1006', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(740, 'ST1006', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(741, 'ST1006', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(742, 'ST1006', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(743, 'ST1006', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(744, 'ST1006', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(745, 'ST 1008', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(746, 'ST 1008', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(747, 'ST 1008', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(748, 'ST 1008', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(749, 'ST 1008', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(750, 'ST 1008', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(751, 'ST 1008', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(752, 'ST 1008', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(753, 'ST1009', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(754, 'ST1009', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(755, 'ST1009', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(756, 'ST1009', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(757, 'ST1009', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(758, 'ST1009', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(759, 'ST1009', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(760, 'ST1009', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(761, 'CS 1102', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(762, 'CS 1102', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(763, 'CS 1102', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(764, 'CS 1102', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(765, 'CS 1102', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(766, 'CS 1102', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(767, 'CS 1102', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(768, 'CS 1102', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(769, 'CS 1102', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(770, 'CS 1102', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(771, 'CS 1102', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(772, 'CS 1102', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(773, 'CS 1102', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(774, 'CS 1102', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(775, 'CS 1102', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(776, 'CS 1102', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(777, 'CS 1102', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(778, 'NS1001', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(779, 'NS1001', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(780, 'NS1001', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(781, 'NS1001', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(782, 'NS1001', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(783, 'NS1001', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(784, 'NS1001', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(785, 'NS1001', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(786, 'NS1001', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(787, 'NS1001', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(788, 'NS1001', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(789, 'NS1001', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(790, 'NS1001', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(791, 'NS1001', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(792, 'NS1001', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(793, 'NS1001', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(794, 'BT1011', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(795, 'BT1011', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(796, 'BT1011', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(797, 'BT1011', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(798, 'BT1011', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(799, 'BT1011', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(800, 'BT1011', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(801, 'BT1011', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(802, 'BT1011', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(803, 'BT1011', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(804, 'BT1009', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(805, 'BT1009', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(806, 'BT1009', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(807, 'BT1009', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(808, 'BT1009', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(809, 'BT1009', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(810, 'BT1009', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(811, 'BT1009', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(812, 'BT1009', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(813, 'BT1009', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(814, 'BT1008', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(815, 'BT1008', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(816, 'BT1008', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(817, 'BT1008', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(818, 'BT1008', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(819, 'BT1008', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(820, 'BT1008', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(821, 'BT1008', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(822, 'BT1008', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(823, 'BT1008', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(824, 'BT 1013', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(825, 'BT 1013', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(826, 'BT 1013', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(827, 'BT 1013', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(828, 'BT 1013', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(829, 'BT 1013', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(830, 'BT 1013', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(831, 'BT 1013', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(832, 'BT 1013', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(833, 'BT 1013', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(834, 'ZL1009', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(835, 'ZL1009', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(836, 'ZL1009', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(837, 'ZL1009', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(838, 'ZL1009', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(839, 'ZL1009', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(840, 'ZL1009', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(841, 'ZL1009', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(842, 'ZL1009', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(843, 'ZL1009', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(844, 'EN 1008', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(845, 'EN 1008', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(846, 'EN 1008', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(847, 'EN 1008', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(848, 'EN 1008', 'BT1011', 'first_year', 'semester 1', 'Genetics and Cell Biology', 200, 'T. D. Silva'),
(849, 'EN 1008', 'BT1009', 'first_year', 'semester 1', 'Genetics and Cell Biology Practicals', 200, 'T. D. Silva'),
(850, 'EN 1008', 'BT1008', 'first_year', 'semester 1', 'Plant Resources', 100, 'IAJK Dissanayake'),
(851, 'EN 1008', 'BT 1013', 'first_year', 'semester 1', 'Plant Structure', 49, 'H.I.U. Caldera'),
(852, 'EN 1008', 'ZL1009', 'first_year', 'semester 1', 'Evolution and Biogeography', 120, 'S. S. Seneviratne'),
(853, 'EN 1008', 'EN 1008', 'first_year', 'semester 1', 'Introduction to Environmental sciences', 150, 'D. Halwathura'),
(854, 'IS 1006', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(855, 'IS 1006', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(856, 'IS 1006', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(857, 'IS 1006', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(858, 'IS1007', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(859, 'IS1007', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(860, 'IS1007', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(861, 'IS1007', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(862, 'MS 1001', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(863, 'MS 1001', 'IS 1006', 'first_year', 'semester 1', 'Fundamentals of Statistics', 155, 'H.A.S.G. Dharmarathne'),
(864, 'MS 1001', 'IS1007', 'first_year', 'semester 1', 'Introduction to Statistical  Computing', 152, 'Oshada Senaweera'),
(865, 'MS 1001', 'MS 1001', 'first_year', 'semester 1', 'Principles of Management', 45, 'M.G.G Hemakumara'),
(866, 'PH3001', 'PH3001', 'third_year', 'semester 1', 'Quantum Mechanics I', 34, 'WMKP Wijayaratna'),
(867, 'PH3001', 'PH3006', 'third_year', 'semester 1', 'Advanced Analogue and Digital Electronics', 30, 'DL Weerawarne'),
(868, 'PH3001', 'PH3032', 'third_year', 'semester 1', 'Embedded System Laboratory', 38, 'HHE Jayaweera'),
(869, 'PH3001', 'PH3052', 'third_year', 'semester 1', 'Electromagnetic Field 1', 28, 'SHRT Sooriyagoda'),
(870, 'PH3001', 'PH3057', 'third_year', 'semester 1', 'Mathematical Physics', 30, 'WAM Madhavi'),
(871, 'PH3001', 'PH3120', 'third_year', 'semester 1', 'Computational Physics Lab 1', 20, 'EMD Siriwardane'),
(872, 'PH3006', 'PH3001', 'third_year', 'semester 1', 'Quantum Mechanics I', 34, 'WMKP Wijayaratna'),
(873, 'PH3006', 'PH3006', 'third_year', 'semester 1', 'Advanced Analogue and Digital Electronics', 30, 'DL Weerawarne'),
(874, 'PH3006', 'PH3032', 'third_year', 'semester 1', 'Embedded System Laboratory', 38, 'HHE Jayaweera'),
(875, 'PH3006', 'PH3037', 'third_year', 'semester 1', 'Mobile Application Development', 100, 'Gayan Akmeemana'),
(876, 'PH3006', 'PH3052', 'third_year', 'semester 1', 'Electromagnetic Field 1', 28, 'SHRT Sooriyagoda'),
(877, 'PH3006', 'PH3057', 'third_year', 'semester 1', 'Mathematical Physics', 30, 'WAM Madhavi'),
(878, 'PH3006', 'PH3120', 'third_year', 'semester 1', 'Computational Physics Lab 1', 20, 'EMD Siriwardane'),
(879, 'PH3032', 'PH3001', 'third_year', 'semester 1', 'Quantum Mechanics I', 34, 'WMKP Wijayaratna'),
(880, 'PH3032', 'PH3006', 'third_year', 'semester 1', 'Advanced Analogue and Digital Electronics', 30, 'DL Weerawarne'),
(881, 'PH3032', 'PH3032', 'third_year', 'semester 1', 'Embedded System Laboratory', 38, 'HHE Jayaweera'),
(882, 'PH3032', 'PH3037', 'third_year', 'semester 1', 'Mobile Application Development', 100, 'Gayan Akmeemana'),
(883, 'PH3032', 'PH3052', 'third_year', 'semester 1', 'Electromagnetic Field 1', 28, 'SHRT Sooriyagoda'),
(884, 'PH3032', 'PH3057', 'third_year', 'semester 1', 'Mathematical Physics', 30, 'WAM Madhavi'),
(885, 'PH3032', 'PH3120', 'third_year', 'semester 1', 'Computational Physics Lab 1', 20, 'EMD Siriwardane'),
(886, 'PH3037', 'PH3001', 'third_year', 'semester 1', 'Quantum Mechanics I', 34, 'WMKP Wijayaratna'),
(887, 'PH3037', 'PH3006', 'third_year', 'semester 1', 'Advanced Analogue and Digital Electronics', 30, 'DL Weerawarne'),
(888, 'PH3037', 'PH3032', 'third_year', 'semester 1', 'Embedded System Laboratory', 38, 'HHE Jayaweera'),
(889, 'PH3037', 'PH3037', 'third_year', 'semester 1', 'Mobile Application Development', 100, 'Gayan Akmeemana'),
(890, 'PH3037', 'PH3120', 'third_year', 'semester 1', 'Computational Physics Lab 1', 20, 'EMD Siriwardane'),
(891, 'PH3052', 'PH3001', 'third_year', 'semester 1', 'Quantum Mechanics I', 34, 'WMKP Wijayaratna'),
(892, 'PH3052', 'PH3006', 'third_year', 'semester 1', 'Advanced Analogue and Digital Electronics', 30, 'DL Weerawarne'),
(893, 'PH3052', 'PH3052', 'third_year', 'semester 1', 'Electromagnetic Field 1', 28, 'SHRT Sooriyagoda'),
(894, 'PH3052', 'PH3057', 'third_year', 'semester 1', 'Mathematical Physics', 30, 'WAM Madhavi'),
(895, 'PH3057', 'PH3001', 'third_year', 'semester 1', 'Quantum Mechanics I', 34, 'WMKP Wijayaratna'),
(896, 'PH3057', 'PH3006', 'third_year', 'semester 1', 'Advanced Analogue and Digital Electronics', 30, 'DL Weerawarne'),
(897, 'PH3057', 'PH3032', 'third_year', 'semester 1', 'Embedded System Laboratory', 38, 'HHE Jayaweera'),
(898, 'PH3057', 'PH3052', 'third_year', 'semester 1', 'Electromagnetic Field 1', 28, 'SHRT Sooriyagoda'),
(899, 'PH3057', 'PH3057', 'third_year', 'semester 1', 'Mathematical Physics', 30, 'WAM Madhavi'),
(900, 'PH3120', 'PH3001', 'third_year', 'semester 1', 'Quantum Mechanics I', 34, 'WMKP Wijayaratna'),
(901, 'PH3120', 'PH3006', 'third_year', 'semester 1', 'Advanced Analogue and Digital Electronics', 30, 'DL Weerawarne'),
(902, 'PH3120', 'PH3032', 'third_year', 'semester 1', 'Embedded System Laboratory', 38, 'HHE Jayaweera'),
(903, 'PH3120', 'PH3037', 'third_year', 'semester 1', 'Mobile Application Development', 100, 'Gayan Akmeemana'),
(904, 'PH3120', 'PH3052', 'third_year', 'semester 1', 'Electromagnetic Field 1', 28, 'SHRT Sooriyagoda'),
(905, 'PH3120', 'PH3057', 'third_year', 'semester 1', 'Mathematical Physics', 30, 'WAM Madhavi'),
(906, 'PH3120', 'PH3120', 'third_year', 'semester 1', 'Computational Physics Lab 1', 20, 'EMD Siriwardane'),
(907, 'PH 1022(G1)', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(908, 'PH 1022(G1)', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(909, 'PH 1022(G1)', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(910, 'PH 1022(G1)', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(911, 'PH 1022(G1)', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(912, 'PH 1022(G1)', 'PM 1012', 'first_year', 'semester 1', 'Introduction to Number Theory', 100, 'B. L. Samarasekera'),
(913, 'PH 1022(G1)', 'CH1008', 'first_year', 'semester 1', 'General & Physical  Chemistry', 80, 'K.M.N de Silva'),
(914, 'PH 1022(G1)', 'CH1010', 'first_year', 'semester 1', 'Calculations in Chemistry', 100, 'K.R.R Mahanama'),
(915, 'PH 1022(G1)', 'CH1011', 'first_year', 'semester 1', 'Practical  Chemistry Level1', 80, 'LHR Perera'),
(916, 'PH 1022(G1)', 'ST1006', 'first_year', 'semester 1', 'Introduction to Probability  & Statistics', 150, 'G.A.C.N Priyadharshani'),
(917, 'PH 1022(G1)', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(918, 'PH 1022(G1)', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(919, 'PH 1022(G1)', 'PH 1022(G1)', 'first_year', 'semester 1', 'Physics Laboratory I', 60, 'WAM Madhavi'),
(920, 'PH 1022(G2)', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya'),
(921, 'PH 1022(G2)', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(922, 'PH 1022(G2)', 'AM1011', 'first_year', 'semester 1', 'Fundamental Applied Mathematics', 100, 'J.K. Wijerathna'),
(923, 'PH 1022(G2)', 'AM1012', 'first_year', 'semester 1', 'Vector Calculus', 200, 'P. D. D. Gallage'),
(924, 'PH 1022(G2)', 'PM 1011', 'first_year', 'semester 1', 'Foundations of Mathematics', 100, 'C. J. Wijeratne'),
(925, 'PH 1022(G2)', 'ST 1008', 'first_year', 'semester 1', 'Probability and Distributions', 200, 'E. R. A. D. Bandara'),
(926, 'PH 1022(G2)', 'ST1009', 'first_year', 'semester 1', 'Exploratory Data Analysis', 150, 'M.D.T.Attygalle'),
(927, 'PH 1022(G2)', 'CS 1102', 'first_year', 'semester 1', 'Introduction to Computing', 350, 'Dr.samantha'),
(928, 'PH 1022(G2)', 'NS1001', 'first_year', 'semester 1', 'Fundamentals of Nuclear Science', 10, 'Dr.abc'),
(929, 'PH 1022(G2)', 'PH 1022(G2)', 'first_year', 'semester 1', 'Physics Laboratory I', 65, 'WAM Madhavi'),
(930, 'PH 1005', 'PH 1007', 'first_year', 'semester 1', 'Astronomy 1', 180, 'KPSC Jayaratne'),
(931, 'PH 1007', 'PH 1005', 'first_year', 'semester 1', 'Modern Physics and Special Relativity', 100, 'Janaka Adassuriya');

-- --------------------------------------------------------

--
-- Table structure for table `saved_examtimetable`
--

CREATE TABLE `saved_examtimetable` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `hall_name` varchar(255) NOT NULL,
  `hour` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `clash` tinyint(1) NOT NULL DEFAULT 0,
  `clash_reason` varchar(255) DEFAULT NULL,
  `offered_to` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_timetable`
--

CREATE TABLE `saved_timetable` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` varchar(50) DEFAULT NULL,
  `hall_name` varchar(100) NOT NULL,
  `hour` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `clash` tinyint(1) DEFAULT 0,
  `offered_to` varchar(255) DEFAULT NULL,
  `clash_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `second_year`
--

CREATE TABLE `second_year` (
  `id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `semester` enum('semester 1','semester 2') NOT NULL,
  `no_of_hours` int(1) NOT NULL,
  `credits` int(11) DEFAULT NULL,
  `registered_students` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `Lecturer_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `second_year`
--

INSERT INTO `second_year` (`id`, `course_id`, `course_name`, `semester`, `no_of_hours`, `credits`, `registered_students`, `department`, `Lecturer_name`) VALUES
(12, 'aa', 'aa', '', 11, NULL, 100, 'Mathematics', 'Dr.Heerashigha');

-- --------------------------------------------------------

--
-- Table structure for table `third_year`
--

CREATE TABLE `third_year` (
  `id` int(11) NOT NULL,
  `course_id` varchar(255) NOT NULL,
  `course_name` varchar(50) NOT NULL,
  `semester` enum('semester 1','semester 2') NOT NULL,
  `no_of_hours` int(1) NOT NULL,
  `credits` int(11) DEFAULT NULL,
  `registered_students` int(10) NOT NULL,
  `department` varchar(50) NOT NULL,
  `Lecturer_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `third_year`
--

INSERT INTO `third_year` (`id`, `course_id`, `course_name`, `semester`, `no_of_hours`, `credits`, `registered_students`, `department`, `Lecturer_name`) VALUES
(8, 'PH3001', 'Quantum Mechanics I', 'semester 1', 3, 3, 34, 'Physics', 'WMKP Wijayaratna'),
(9, 'PH3006', 'Advanced Analogue and Digital Electronics', 'semester 1', 3, 3, 30, 'Physics', 'DL Weerawarne'),
(10, 'PH3030', 'Advanced Physics Laboratory 1', 'semester 1', 6, 6, 10, 'Physics', 'HHE Jayaweera'),
(11, 'PH3032', 'Embedded System Laboratory', 'semester 1', 7, 3, 38, 'Physics', 'HHE Jayaweera'),
(12, 'PH3037', 'Mobile Application Development', 'semester 1', 6, 3, 100, 'Physics', 'Gayan Akmeemana'),
(13, 'PH3052', 'Electromagnetic Field 1', 'semester 1', 3, 3, 28, 'Physics', 'SHRT Sooriyagoda'),
(14, 'PH3057', 'Mathematical Physics', 'semester 1', 3, 3, 30, 'Physics', 'WAM Madhavi'),
(15, 'PH3120', 'Computational Physics Lab 1', 'semester 1', 3, 3, 20, 'Physics', 'EMD Siriwardane'),
(21, 'CH3001', 'Topics in Analytical Chemistry I', 'semester 1', 2, 2, 150, 'Chemistry', 'K.R.R Mahanama'),
(22, 'CH3003', 'Industrial Chemistry', 'semester 1', 2, 2, 120, 'Chemistry', 'RD Wijesekera'),
(23, 'CH3004', 'Laboratory Management', 'semester 1', 1, 1, 50, 'Chemistry', 'MN Kaumal'),
(24, 'CH3006', 'Computational Chemistry', 'semester 1', 2, 2, 110, 'Chemistry', 'A Tillekaratne'),
(25, 'CH3008', 'Quality Management', 'semester 1', 1, 1, 150, 'Chemistry', 'MN Kaumal'),
(26, 'CH3021', 'Spectroscopy', 'semester 1', 3, 3, 100, 'Chemistry', 'S Samarasinghe'),
(27, 'CH3030', 'Advanced Practical Chemistry', 'semester 1', 6, 8, 200, 'Chemistry', 'A Tillekaratne'),
(28, 'CH3033', 'Chemistry of Biomolecules', 'semester 1', 6, 3, 200, 'Chemistry', 'Nimal Punyasiri'),
(29, 'CH3071', 'Pharmaceutics I', 'semester 1', 3, 3, 150, 'Chemistry', 'HDSM Perera'),
(30, 'CH3075', 'Practical Pharmacy', 'semester 1', 3, 8, 100, 'Chemistry', 'WRM de Silva'),
(31, 'CH3090', 'Practical Computational Chemistry', 'semester 1', 6, 8, 200, 'Chemistry', 'K.M.N de Silva'),
(32, 'CS3008', 'Introduction to Data Structures and Algorithms Mac', 'semester 1', 3, 3, 150, 'UCSC', 'Dr.samantha'),
(33, 'CS3120', 'Machine Learning and Neural Computing', 'semester 1', 3, 3, 200, 'UCSC', 'Dr.samantha'),
(34, 'AM 3031', 'Mathematical Methods I', 'semester 1', 3, 3, 200, 'Mathematics', 'B. L. Samarasekera'),
(35, 'AM 3033', 'Applied Dynamical Systems', 'semester 1', 3, 3, 150, 'Mathematics', 'P. D. D. Gallage'),
(36, 'AM 3035', 'Discrete Applied Mathematics', 'semester 1', 3, 3, 200, 'Mathematics', 'C.J. Jayawardena'),
(37, 'PM 3031', 'Linear Algebra', 'semester 1', 3, 3, 150, 'Mathematics', 'D.R. Jayewardene'),
(38, 'PM 3033', 'Real Analysis I', 'semester 1', 3, 3, 150, 'Mathematics', 'D.T. Tillkaratne'),
(39, 'PM 3036', 'Topology I', 'semester 1', 3, 3, 150, 'Mathematics', 'C. J. Wijeratne'),
(40, 'AM 3032', 'Numerical Methods and Scientific Computing', 'semester 1', 2, 2, 150, 'Mathematics', 'H.C.Y. Jayathunga'),
(41, 'AM 3081', 'Applied Analysis', 'semester 1', 3, 3, 150, 'Mathematics', 'K.K.W. Hasitha Erandi'),
(42, 'AM 3082', 'Theory of Computation', 'semester 1', 3, 3, 150, 'Mathematics', 'A. C. Mahasinghe'),
(43, 'AM 3083', 'Computational Methods and Scientific Computing I', 'semester 1', 2, 2, 50, 'Mathematics', 'H.C.Y. Jayathunga'),
(44, 'FM 3031', 'Mathematical Methods for Finance II', 'semester 1', 3, 3, 30, 'Mathematics', 'J.K. Wijerathna'),
(45, 'FM 3032', 'Quantitative Finance', 'semester 1', 3, 3, 20, 'Mathematics', 'U. Prabath Liyanage'),
(46, 'FM 3034', 'Financial Mathematics III', 'semester 1', 2, 2, 40, 'Mathematics', 'S. S. N. Perera'),
(47, 'FM 3012', 'Economics I for Finance and Insurance', 'semester 1', 3, 3, 40, 'Mathematics', 'H.C.Y. Jayathunga'),
(48, 'ST 3003', 'Marketing Research', 'semester 1', 2, 2, 40, 'Statistics', 'R. A. B. Abeygunawardhana'),
(49, 'ST 3007', 'Operational Research', 'semester 1', 3, 3, 200, 'Statistics', 'Oshada Senaweera'),
(50, 'ST 3051', 'Statistical Inference I', 'semester 1', 3, 3, 150, 'Statistics', 'E. R. A. D. Bandara'),
(51, 'ST 3072', 'Applied Regression Analysis', 'semester 1', 3, 3, 150, 'Statistics', 'C.H. Magalla'),
(52, 'ST 3074', 'Time Series Analysis', 'semester 1', 2, 2, 150, 'Statistics', 'C.D. Tilakaratne'),
(53, 'ST 3075', 'Design of Experiments', 'semester 1', 2, 2, 140, 'Statistics', 'J.H.D.S.P. Tissera'),
(55, 'ST 3008', 'Applied statistical models', 'semester 1', 3, 3, 39, 'Statistics', 'R.V. Jayatillake'),
(56, 'ST 3085', 'Computational Statistics', 'semester 1', 2, 2, 46, 'Statistics', 'S.D. Viswakula'),
(57, 'DS 3001', 'Data Visualization Techniques', 'semester 1', 1, 1, 50, 'Statistics', 'A.A.Sunethra'),
(58, 'DS 3002', 'Data Ethics and Data Protection', 'semester 1', 2, 2, 20, 'Statistics', 'R. A. B. Abeygunawardhana'),
(59, 'BT 3001', 'Plant Pathology', 'semester 1', 3, 3, 45, 'Plant Science', 'K.G.S.U. Ariyawansa'),
(60, 'BT 3003', 'Plant Molecular Biology', 'semester 1', 2, 2, 50, 'Plant Science', 'A Wickramasuriya'),
(61, 'BT 3006', 'Plant Tissue Culture Technology', 'semester 1', 3, 3, 50, 'Plant Science', 'D. Bandupriya'),
(62, 'BT 3009', 'Environment & Biodiversity Related Legislation I i', 'semester 1', 1, 1, 50, 'Plant Science', 'H.I.U. Caldera'),
(63, 'BT 3053', 'Introduction to Bioinformatics', 'semester 1', 2, 2, 60, 'Plant Science', 'A Wickramasuriya'),
(64, 'BT 3058', 'Bioprospecting', 'semester 1', 2, 2, 50, 'Plant Science', 'R. Mayakaduwa'),
(65, 'BT 3061', 'Taxonomic Field Survey', 'semester 1', 3, 3, 50, 'Plant Science', 'HS Kathriarachchi'),
(66, 'BT 3064', 'Experimental Plant Biotechnology', 'semester 1', 2, 2, 50, 'Plant Science', 'C.M. Nanayakkara'),
(67, 'BT 3066', 'Plant Systematics', 'semester 1', 3, 3, 50, 'Plant Science', 'HS Kathriarachchi'),
(68, 'CS 3101', 'Rapid Application Development & Visual Programming', 'semester 1', 3, 3, 40, 'UCSC', 'Dr.samantha'),
(69, 'IT 3003', 'Advanced Programming Techniques', 'semester 1', 3, 3, 45, 'UCSC', 'DR. Ucsc1'),
(70, 'EN 3001', 'Environmental Science Seminar', 'semester 1', 1, 1, 40, 'Zoology and Environment Science', 'D. K. Weerakoon'),
(71, 'EN 3002', 'Current Environmental Issues', 'semester 1', 1, 1, 50, 'Zoology and Environment Science', 'D. K. Weerakoon'),
(72, 'EN 3014', 'Natural Disaster Risk Reduction and Resilience', 'semester 1', 3, 3, 50, 'Zoology and Environment Science', 'D. Halwathura'),
(73, 'EN 3019', 'Climate Change', 'semester 1', 3, 3, 60, 'Zoology and Environment Science', 'A. Witharana'),
(74, 'EN 3050', 'Hydrology and Water Management', 'semester 1', 2, 2, 60, 'Zoology and Environment Science', 'A. Witharana'),
(75, 'EN 3061', 'Environmental Resource Management I', 'semester 1', 3, 3, 50, 'Zoology and Environment Science', 'M. R. Wijesinghe'),
(76, 'CH 3010', 'Environmental Chemistry', 'semester 1', 2, 2, 70, 'Chemistry', 'K.R.R Mahanama'),
(77, 'ZL 3050', 'Applications of Molecular Biology', 'semester 1', 2, 2, 40, 'Zoology and Environment Science', 'I.C. Perera'),
(78, 'ZL 3051', 'Introduction to Immunology', 'semester 1', 2, 2, 40, 'Zoology and Environment Science', 'Maheshi Mapalagamage'),
(79, 'ZL 3052', 'Mammalogy', 'semester 1', 2, 2, 40, 'Zoology and Environment Science', 'M. R. Wijesinghe'),
(80, 'ZL 3011', 'Fish Biology, Fisheries and Aquaculture', 'semester 1', 3, 3, 50, 'Zoology and Environment Science', 'Kalpani Marasinghe'),
(81, 'ZL 3053', 'Species vulnerability risk assessment', 'semester 1', 2, 2, 40, 'Zoology and Environment Science', 'M. R. Wijesinghe'),
(82, 'ZL 3054', 'Evolutionary Biology', 'semester 1', 2, 2, 40, 'Zoology and Environment Science', 'S. S. Seneviratne'),
(83, 'ZL 3055', 'Invertebrate Zoology', 'semester 1', 1, 1, 50, 'Zoology and Environment Science', 'Kalpani Marasinghe'),
(84, 'ZL 3017', 'Applied Biotechnology', 'semester 1', 1, 1, 40, 'Zoology and Environment Science', 'S. Amarasekara'),
(85, 'ZL 3059', 'Molecular Biology', 'semester 1', 2, 2, 30, 'Zoology and Environment Science', 'G. H. Galhena'),
(86, 'ZL 3058', 'Immunology', 'semester 1', 2, 2, 45, 'Zoology and Environment Science', 'S. Amarasekara'),
(87, 'ZL 3076', 'Cellular and Molecular Physiology', 'semester 1', 2, 2, 50, 'Zoology and Environment Science', 'S. Amarasekara'),
(88, 'ZL 3082', 'Foundations in Molecular Ecology', 'semester 1', 2, 2, 40, 'Zoology and Environment Science', 'S. S. Seneviratne'),
(89, 'ZL 3086', 'Population Genetics and Genomics', 'semester 1', 2, 2, 50, 'Zoology and Environment Science', 'G. H. Galhena'),
(90, 'ZL 3077', 'Practical Molecular Biology I', 'semester 1', 3, 3, 50, 'Zoology and Environment Science', 'I.C. Perera'),
(91, 'ZL 3083', 'Molecular Taxonomy', 'semester 1', 1, 1, 100, 'Zoology and Environment Science', 'G. H. Galhena'),
(92, 'NS 3017', 'Applied Nuclear Science', 'semester 1', 3, 3, 10, 'Nuclear Science', 'Dr.abc'),
(93, 'NS 3025', 'Radiobiology', 'semester 1', 4, 3, 10, 'Nuclear Science', 'Dr.abc'),
(94, 'NS 3027', 'Human Anatomy and Physiology I', 'semester 1', 2, 2, 10, 'Nuclear Science', 'Dr.Def'),
(95, 'NS 3029', 'Physics of Nuclear Medicine', 'semester 1', 2, 2, 10, 'Nuclear Science', 'Dr.Def'),
(96, 'NS 3028', 'Human Anatomy Practical', 'semester 1', 2, 3, 10, 'Nuclear Science', 'Dr.GHI'),
(97, 'BC 3022', 'Metabolism I', 'semester 1', 2, 2, 30, 'Chemistry', 'P.V.D.G.N Silva'),
(98, 'BC 3030', 'Practical Biochemistry and Molecular Biology', 'semester 1', 6, 8, 30, 'Chemistry', 'P.V.D.G.N Silva'),
(99, 'MB 3022', 'Gene Expression and Regulation', 'semester 1', 3, 3, 50, 'Chemistry', 'KRR Mahanama'),
(100, 'MB 3025', 'Recombinant DNA Technology and Applications', 'semester 1', 3, 3, 50, 'Chemistry', 'K.R.R Mahanama');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `all_students`
--
ALTER TABLE `all_students`
  ADD PRIMARY KEY (`registration_num`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `dean`
--
ALTER TABLE `dean`
  ADD PRIMARY KEY (`dean_id`);

--
-- Indexes for table `first_year`
--
ALTER TABLE `first_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fourth_year`
--
ALTER TABLE `fourth_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecture`
--
ALTER TABLE `lecture`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecture_halls`
--
ALTER TABLE `lecture_halls`
  ADD PRIMARY KEY (`hall_id`);

--
-- Indexes for table `offer_courses`
--
ALTER TABLE `offer_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_courses`
--
ALTER TABLE `saved_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_examtimetable`
--
ALTER TABLE `saved_examtimetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_timetable`
--
ALTER TABLE `saved_timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `second_year`
--
ALTER TABLE `second_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `third_year`
--
ALTER TABLE `third_year`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `first_year`
--
ALTER TABLE `first_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `fourth_year`
--
ALTER TABLE `fourth_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lecture`
--
ALTER TABLE `lecture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `lecture_halls`
--
ALTER TABLE `lecture_halls`
  MODIFY `hall_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `offer_courses`
--
ALTER TABLE `offer_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `saved_courses`
--
ALTER TABLE `saved_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=932;

--
-- AUTO_INCREMENT for table `saved_examtimetable`
--
ALTER TABLE `saved_examtimetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `saved_timetable`
--
ALTER TABLE `saved_timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1110;

--
-- AUTO_INCREMENT for table `second_year`
--
ALTER TABLE `second_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `third_year`
--
ALTER TABLE `third_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
