-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2023 at 05:50 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignment_course_upload`
--

CREATE TABLE `assignment_course_upload` (
  `assignment_course_upload_id` int(11) NOT NULL,
  `link` varchar(2000) NOT NULL,
  `file` varchar(2000) NOT NULL,
  `class_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_course_upload`
--

INSERT INTO `assignment_course_upload` (`assignment_course_upload_id`, `link`, `file`, `class_id`, `user_id`, `assignment_id`, `status`) VALUES
(96, 'https://byjus.com/biology/cell-biology/', '', 45, 28, 28, 'submitted'),
(97, 'https://scopegurdoninstitute.co.uk/digital-toolkit-1-investigate-the-cell-3d-model', '', 45, 28, 27, 'submitted');

-- --------------------------------------------------------

--
-- Table structure for table `classwork_assignment`
--

CREATE TABLE `classwork_assignment` (
  `assignment_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `instruction` varchar(1000) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `student` varchar(100) NOT NULL,
  `point` int(11) NOT NULL,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `time` varchar(50) NOT NULL,
  `class_topic` varchar(64) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `link` varchar(2000) DEFAULT NULL,
  `file` varchar(2000) DEFAULT NULL,
  `youtube` varchar(2000) DEFAULT NULL,
  `assignment_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classwork_assignment`
--

INSERT INTO `classwork_assignment` (`assignment_id`, `title`, `instruction`, `class_name`, `student`, `point`, `date`, `due_date`, `time`, `class_topic`, `class_id`, `teacher_id`, `link`, `file`, `youtube`, `assignment_status`) VALUES
(27, 'Building a Cell', 'Build a digital model of a Cell.', 'STEM-Einstein - General Biology', 'R.J Liwag,John Renzo Linsangan', 20, '2023-10-03', '2023-10-09', '11:59 PM', 'Cell Biology', 39, 33, '', '', NULL, 'turned-in late'),
(28, 'Cell Division', 'Define Cell Biology', 'STEM-Einstein - General Biology', 'R.J Liwag,John Renzo Linsangan', 10, '2023-10-11', '2023-10-02', '11:59 PM', 'Cell Biology', 39, 33, NULL, NULL, NULL, 'turned-in late'),
(30, 'Cytoskeleton', 'Define Cytoskeleton and give examples.', 'STEM-Einstein - General Biology', 'R.J Liwag,John Renzo Linsangan', 20, '2023-10-12', '2023-10-13', '11:59 PM', 'Cell Biology', 39, 33, '', '', '', 'assigned');

-- --------------------------------------------------------

--
-- Table structure for table `classwork_assignment_upload`
--

CREATE TABLE `classwork_assignment_upload` (
  `assignment_upload_id` int(11) NOT NULL,
  `link` varchar(2000) DEFAULT NULL,
  `file` varchar(2000) DEFAULT NULL,
  `youtube` varchar(2000) DEFAULT NULL,
  `used` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classwork_material`
--

CREATE TABLE `classwork_material` (
  `material_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `class_name` varchar(64) NOT NULL,
  `student` varchar(64) NOT NULL,
  `class_topic` varchar(64) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `link` varchar(2000) DEFAULT NULL,
  `file` varchar(2000) DEFAULT NULL,
  `youtube` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classwork_material`
--

INSERT INTO `classwork_material` (`material_id`, `title`, `description`, `class_name`, `student`, `class_topic`, `class_id`, `teacher_id`, `date`, `link`, `file`, `youtube`) VALUES
(114, 'Video About Cell Biology.', 'Please watch this video about Cell Biology so that for tomorrow, we will have a short recitation about the video.', 'STEM-Einstein - General Biology', 'R.J Liwag,John Renzo Linsangan', 'Cell Biology', 39, 33, '2023-10-04', NULL, NULL, 'https://www.youtube.com/watch?v=URUJD5NEXC8');

-- --------------------------------------------------------

--
-- Table structure for table `classwork_material_upload`
--

CREATE TABLE `classwork_material_upload` (
  `material_upload_id` int(11) NOT NULL,
  `link` varchar(2000) NOT NULL,
  `file` varchar(2000) NOT NULL,
  `youtube` varchar(2000) NOT NULL,
  `used` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classwork_material_upload`
--

INSERT INTO `classwork_material_upload` (`material_upload_id`, `link`, `file`, `youtube`, `used`) VALUES
(89, '', '', 'https://www.youtube.com/watch?v=URUJD5NEXC8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `classwork_question`
--

CREATE TABLE `classwork_question` (
  `question_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `question` varchar(500) NOT NULL,
  `instruction` varchar(500) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `student` varchar(100) NOT NULL,
  `point` int(11) NOT NULL,
  `date` date NOT NULL,
  `due_date` date NOT NULL,
  `time` varchar(50) NOT NULL,
  `class_topic` varchar(64) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `link` varchar(2000) DEFAULT NULL,
  `file` varchar(2000) DEFAULT NULL,
  `youtube` varchar(2000) DEFAULT NULL,
  `question_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classwork_question`
--

INSERT INTO `classwork_question` (`question_id`, `title`, `question`, `instruction`, `class_name`, `student`, `point`, `date`, `due_date`, `time`, `class_topic`, `class_id`, `teacher_id`, `link`, `file`, `youtube`, `question_status`) VALUES
(21, 'Cell Biology', 'What is Cell Biology? ', 'Explain it one paragraph with a minimum of 5 sentence. Do not use any AI Tools in generating answers, if you are caught, your grade will be equivalent to 0.', 'STEM-Einstein - General Biology', 'John Renzo Linsangan', 30, '2023-10-01', '2023-10-14', '11:59 PM', 'Cell Biology', 39, 33, NULL, NULL, NULL, 'turned in');

-- --------------------------------------------------------

--
-- Table structure for table `classwork_question_upload`
--

CREATE TABLE `classwork_question_upload` (
  `question_upload_id` int(11) NOT NULL,
  `link` varchar(2000) NOT NULL,
  `file` varchar(2000) NOT NULL,
  `youtube` varchar(2000) NOT NULL,
  `used` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_enrolled`
--

CREATE TABLE `class_enrolled` (
  `class_id` int(11) NOT NULL,
  `tc_id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `grade_level` varchar(50) NOT NULL,
  `strand` varchar(50) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_code` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_firstname` varchar(50) NOT NULL,
  `student_lastname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_enrolled`
--

INSERT INTO `class_enrolled` (`class_id`, `tc_id`, `class_name`, `section`, `subject`, `grade_level`, `strand`, `teacher_id`, `class_code`, `first_name`, `last_name`, `student_id`, `student_firstname`, `student_lastname`) VALUES
(42, 39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'Grade 11', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star', 36, 'R.J', 'Liwag'),
(43, 40, 'STEM-Einstein - Basic Calculus', 'Einstein', 'Basic Calculus', 'Grade 11', 'STEM', 33, 'D6XDFLx', 'Patrick', 'Star', 28, 'John Renzo', 'Linsangan'),
(44, 41, 'STEM-Einstein - Art Appreciation', 'Einstein', 'Art Apprecitiation', 'Grade 11', 'STEM', 33, '3YzS3aC', 'Patrick', 'Star', 28, 'John Renzo', 'Linsangan'),
(45, 39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'Grade 11', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star', 28, 'John Renzo', 'Linsangan'),
(46, 43, 'TVL-ShangriLa - Mechanics', 'ShangriLa', 'Mechanics', 'Grade 11', 'TVL', 32, 'C1Qjhqd', 'Spongebob', 'Squarepants', 31, 'Marissa Margarette', 'Garcia');

-- --------------------------------------------------------

--
-- Table structure for table `class_theme`
--

CREATE TABLE `class_theme` (
  `theme_id` int(11) NOT NULL,
  `theme` varchar(1000) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_name` varchar(64) NOT NULL,
  `theme_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_theme`
--

INSERT INTO `class_theme` (`theme_id`, `theme`, `class_id`, `teacher_id`, `class_name`, `theme_status`) VALUES
(18, 'theme4.jpg', 39, 33, 'STEM-Einstein - General Biology\r\n', 'old'),
(20, 'theme8.jpg', 40, 33, 'STEM-Einstein - Basic Calculus', 'old'),
(21, 'theme7.jpg', 41, 33, 'STEM-Einstein - Art Appreciation', 'recent'),
(22, 'theme7.jpg', 39, 33, 'STEM-Einstein - General Biology', 'old'),
(23, 'theme4.jpg', 39, 33, 'STEM-Einstein - General Biology', 'recent'),
(24, 'theme7.jpg', 43, 32, 'TVL-ShangriLa - Mechanics', 'recent'),
(25, 'theme9.jpg', 40, 33, 'STEM-Einstein - Basic Calculus', 'recent');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `report_title` varchar(100) NOT NULL,
  `details` varchar(500) NOT NULL,
  `attachment` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `firstname`, `lastname`, `report_title`, `details`, `attachment`) VALUES
(19, 'Renzo', 'Linsangan', 'gege', 'gege talon', ''),
(20, 'Renzo', 'Linsangan', 'Talon una Ulo', 'gege', '');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `track` varchar(50) NOT NULL,
  `start_date` varchar(50) NOT NULL,
  `end_date` varchar(50) NOT NULL,
  `detail` varchar(2000) NOT NULL,
  `attachment` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`news_id`, `title`, `type`, `name`, `date`, `track`, `start_date`, `end_date`, `detail`, `attachment`) VALUES
(7, 'Smog O Vog', 'news', 'DOST_PHIVOLCS', '2023-10-13', 'all', '2023-10-13', '2023-10-16', 'Narito ang mga kaalaman tungkol sa Volcanic Smog o Vog at mga pamamaraan upang makaiwas sa panganib na dulot nito. \r\nAng vog ay isang uri ng polusyon sa hangin na sanhi ng mga bulkan. Binubo ito ng mga pinong patak na naglalaman ng volcanic gas tulad ng Sulfur Dioxide (SO2).\r\n• Ano ang Sulfur Dioxide o SO2?\r\nAng sulfur dioxide ay isang nakakalasong usok na maa-aring maka-apekto sa kalusugan ng tao at hayop, pati na rin sa mga halaman. Maaring ma expose ang isang indibidwal kung ito ay malalanghap o sa pamamagitan ng skin contact\r\n• Ano ang epekto ng vog?\r\nMaaring magdulot ito ng iritasyon sa mga mata, lalamunan at respiratory tract na maaaring maging malubha depende sa kosentrasyon o tagal ng pagkalanghap nito.\r\n• Sinu-sino ang mga sensitibo sa masamang epekto ng vog? \r\nMga may kondisyon sa kalusugan tulad ng hika, sakit sa baga at sakit sa puso. Matatanda, mga buntis at mga bata.\r\n• Ano ang dapat gawin?\r\nLimitahan ang pagkakalantad o exposure sa vog. Iwasan ang mga aktibidad sa labas o manatili na lamang sa loob ng bahay at isara ang mga bintana at pintuan upang maiwasang makapasok ang vog sa loob ng bahay.\r\n• Paano pu-protektahan ang sarili?\r\nMagsuot ng facemask o mas mabuti kung N95 facemasks o gas mask. Uminom ng maraming tubig upang maibsan ang iritasyon o paninikip ng daluyan ng paghinga. Kung kabilang sa mga sensitibong grupo, siguraduhing subaybayan ang inyong klagayan at magpatingin agad sa doktor o sa barangay health unit kung kinakailangan. Kung makakaranas ng matinding epekto, magpatingin agad sa doktor o sa barangay health unit.', 'picture_652681a2d64fd.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `strand` varchar(50) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_code` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`class_id`, `class_name`, `section`, `subject`, `strand`, `teacher_id`, `class_code`, `first_name`, `last_name`) VALUES
(39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star'),
(40, 'STEM-Einstein - Basic Calculus', 'Einstein', 'Basic Calculus', 'STEM', 33, 'D6XDFLx', 'Patrick', 'Star'),
(41, 'STEM-Einstein - Art Appreciation', 'Einstein', 'Art Apprecitiation', 'STEM', 33, '3YzS3aC', 'Patrick', 'Star'),
(43, 'TVL-ShangriLa - Mechanics', 'ShangriLa', 'Mechanics', 'TVL', 32, 'C1Qjhqd', 'Spongebob', 'Squarepants'),
(44, 'STEM-Newton - General Biology', 'Newton', 'General Biology', 'STEM', 32, 'rV6YJDt', 'Spongebob', 'Squarepants');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignment_course_answer`
--

CREATE TABLE `student_assignment_course_answer` (
  `answer_assignment_id` int(11) NOT NULL,
  `assignment_course_upload_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `assignment_link` varchar(2000) DEFAULT NULL,
  `assignment_file` varchar(2000) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assignment_course_answer`
--

INSERT INTO `student_assignment_course_answer` (`answer_assignment_id`, `assignment_course_upload_id`, `assignment_id`, `assignment_link`, `assignment_file`, `user_id`, `class_id`, `teacher_id`) VALUES
(151, 96, 28, 'https://byjus.com/biology/cell-biology/', '', 28, 45, 33),
(153, 97, 27, 'https://scopegurdoninstitute.co.uk/digital-toolkit-1-investigate-the-cell-3d-model', '', 28, 45, 33);

-- --------------------------------------------------------

--
-- Table structure for table `student_question_course_answer`
--

CREATE TABLE `student_question_course_answer` (
  `answer_question_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `point` int(64) NOT NULL,
  `question_answer` varchar(2000) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_question_course_answer`
--

INSERT INTO `student_question_course_answer` (`answer_question_id`, `question_id`, `point`, `question_answer`, `user_id`, `class_id`, `teacher_id`) VALUES
(25, 21, 30, 'cell biology ay mali', 28, 45, 33);

-- --------------------------------------------------------

--
-- Table structure for table `topic`
--

CREATE TABLE `topic` (
  `topic_id` int(11) NOT NULL,
  `class_topic` varchar(64) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `section` varchar(64) NOT NULL,
  `subject` varchar(64) NOT NULL,
  `strand` varchar(64) NOT NULL,
  `class_name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topic`
--

INSERT INTO `topic` (`topic_id`, `class_topic`, `class_id`, `teacher_id`, `section`, `subject`, `strand`, `class_name`) VALUES
(64, 'Derivative', 35, 33, 'STEM-B', 'Basic Calculus', 'STEM', 'STEM-B - Basic Calculus'),
(65, 'Functions', 35, 33, 'STEM-B', 'Basic Calculus', 'STEM', 'STEM-B - Basic Calculus'),
(66, 'Integral', 35, 33, 'STEM-B', 'Basic Calculus', 'STEM', 'STEM-B - Basic Calculus'),
(67, 'Free Fall', 36, 33, 'STEM-A', 'General Physics', 'STEM', 'STEM-A - General Physics'),
(68, 'Velocity', 36, 33, 'STEM-A', 'General Physics', 'STEM', 'STEM-A - General Physics'),
(69, 'Law of Motion', 36, 33, 'STEM-A', 'General Physics', 'STEM', 'STEM-A - General Physics'),
(70, 'Ecology', 37, 33, 'STEM-F', 'General Biology', 'STEM', 'STEM-F - General Biology'),
(71, 'Biology', 37, 33, 'STEM-F', 'General Biology', 'STEM', 'STEM-F - General Biology'),
(72, 'Cell Biology', 37, 33, 'STEM-F', 'General Biology', 'STEM', 'STEM-F - General Biology'),
(73, 'Architecture', 34, 32, 'STEM-C', 'Art Appreciation', 'STEM', 'STEM-C - Art Appreciation'),
(74, 'Painting', 34, 32, 'STEM-C', 'Art Appreciation', 'STEM', 'STEM-C - Art Appreciation'),
(75, 'Sculpture', 34, 32, 'STEM-C', 'Art Appreciation', 'STEM', 'STEM-C - Art Appreciation'),
(76, 'Derivative', 38, 32, 'STEM-B', 'Basic Calculus', 'STEM', 'STEM-B - Basic Calculus'),
(77, 'Functions', 38, 32, 'STEM-B', 'Basic Calculus', 'STEM', 'STEM-B - Basic Calculus'),
(78, 'Integral', 38, 32, 'STEM-B', 'Basic Calculus', 'STEM', 'STEM-B - Basic Calculus'),
(79, 'Cell Biology', 39, 33, 'Einstein', 'General Biology', 'STEM', 'STEM-Einstein - General Biology');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(500) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`admin_id`, `user_name`, `password`, `email`) VALUES
(6, 'talisayprincipal', '$2y$10$VHwROMtZCvtvR9qGwXTr1uNFaNPjXxPNNkxIbOKMeGLx.qLrIpNWm', 'talisayprincipal@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `grade_level` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `section` varchar(64) NOT NULL,
  `usertype` varchar(50) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `username`, `password`, `email`, `address`, `contact`, `firstname`, `middlename`, `lastname`, `grade_level`, `department`, `section`, `usertype`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(28, 'renzolinsangan', '$2y$10$vdNYpMxHSGPQ8URNzheJ.uAW.fC3pOhyfevUKTtEA20POmytl30Ma', 'renzolinsangan11@gmail.com', 'Block 3 Lot 44, Croatia St. Lynville Subdv. Purok 3, Latag, Lipa City, Batangas', '09657008926', 'John Renzo', 'Goyena', 'Linsangan', 'Grade 11', 'stem', 'Einstein', 'student', NULL, NULL),
(29, 'carljustine', '$2y$10$8jbVM6Wy6bb.DR6YHhRnV.BLMxZj5faVlfPQuw//NwrvaNW0qTA16', 'carljustine@gmail.com', 'Talisay, Batangas', '09223043043', 'Carl Justine', 'C ', 'Aala', 'Grade 12', 'humss', 'Freud', 'student', '4685009adc6793cf52bd8c25e9c55114c3a3b92a98aa3a6cb25ba3a47fcdc487', '2023-10-06 07:36:21'),
(30, 'patriciaperez', '$2y$10$BXK56vTkmz.DALWwU/595.i3DhIWmw0zz8rnq7Nt6pEAynUzIq5/q', 'patriciaperez@gmail.com', 'Talisay, Batangas', '09232424244', 'Patricia', 'C', 'Perez', 'Grade 11', 'abm', 'Pacioli', 'student', NULL, NULL),
(31, 'marissamargarette', '$2y$10$uqnc8ZHIbxVmixLveq5SmOkGhsdrEB/gTa6TTBc.yxvRIam1MtC7e', 'marissagrc4@gmail.com', 'Purok 6, Brgy. Natatas, Tanauan City, Batangas', '09497920130', 'Marissa Margarette', 'Ocampo', 'Garcia', 'Grade 11', 'tvl', 'ShangriLa', 'student', 'b977467780a392b9124d4340aeab522e03adc312bd5711ef355f652d7a9e2ac4', '2023-10-13 12:54:16'),
(32, 'spongebob', '$2y$10$Xw3bTrw2rYk.p4RFKQF3ieS1bClb1b.gMHuwmCDx6aq7/zKALI1q.', 'spongebobsquarepants12@gmail.com', 'Bikini Bottom, Sa may Pinya', '09523232323', 'Spongebob', 'hindi', 'Squarepants', '', 'tvl', '', 'teacher', NULL, NULL),
(33, 'patrick', '$2y$10$J9gOlxnbm99eUvHa.UwgVOdXfXhYcMXvzEdUkSUjsGfc.vKK8to7K', 'patrickstar@gmail.com', 'Bikini Bottom, Stone House', '09232325556', 'Patrick', 'hindi', 'Star', '', 'stem', '', 'teacher', NULL, NULL),
(36, 'rjliwag', '$2y$10$NA18/Bn3ehbXsmw/Hmuo6uoIUGWBk8j/BDWSEbyUnqPa5zbSnOfm.', 'rjliwag@gmail.com', 'Talisay, Batangas', '09451213212', 'Ronald John', 'Caguicla', 'Liwag', 'Grade 11', 'stem', 'Einstein', 'student', NULL, NULL),
(37, 'marcoluis', '$2y$10$yXO0x1CyK/zWzgUU4XFr/.6KJcVqHQ8Vs.CGxcSMmxZVAh6vDkQnW', 'marcoluishernandez@gmail.com', 'San Luis, Batangas', '09543221548', 'Marco Luis', 'S', 'Hernandez', 'Grade 11', 'stem', 'Einstein', 'student', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile` varchar(1000) NOT NULL,
  `profile_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`profile_id`, `user_id`, `profile`, `profile_status`) VALUES
(6, 28, '384487562_278785975040200_1993586305900934592_n.jpg', 'old'),
(7, 28, 'WIN_20230930_10_36_37_Pro.jpg', 'old'),
(8, 28, 'WIN_20230925_19_14_23_Pro.jpg', 'old'),
(9, 28, '384487562_278785975040200_1993586305900934592_n.jpg', 'recent'),
(10, 36, 'rj.jpg', 'recent'),
(11, 31, '380293503_304768402179479_6674034346742619703_n.jpg', 'recent'),
(12, 30, 'pat.jpg', 'recent'),
(13, 29, 'carl.jpg', 'recent'),
(14, 33, 'patrick.jpg', 'recent'),
(15, 32, 'spongebob.jpg', 'recent');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignment_course_upload`
--
ALTER TABLE `assignment_course_upload`
  ADD PRIMARY KEY (`assignment_course_upload_id`);

--
-- Indexes for table `classwork_assignment`
--
ALTER TABLE `classwork_assignment`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `classwork_assignment_upload`
--
ALTER TABLE `classwork_assignment_upload`
  ADD PRIMARY KEY (`assignment_upload_id`);

--
-- Indexes for table `classwork_material`
--
ALTER TABLE `classwork_material`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `classwork_material_upload`
--
ALTER TABLE `classwork_material_upload`
  ADD PRIMARY KEY (`material_upload_id`);

--
-- Indexes for table `classwork_question`
--
ALTER TABLE `classwork_question`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `classwork_question_upload`
--
ALTER TABLE `classwork_question_upload`
  ADD PRIMARY KEY (`question_upload_id`);

--
-- Indexes for table `class_enrolled`
--
ALTER TABLE `class_enrolled`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_theme`
--
ALTER TABLE `class_theme`
  ADD PRIMARY KEY (`theme_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `student_assignment_course_answer`
--
ALTER TABLE `student_assignment_course_answer`
  ADD PRIMARY KEY (`answer_assignment_id`);

--
-- Indexes for table `student_question_course_answer`
--
ALTER TABLE `student_question_course_answer`
  ADD PRIMARY KEY (`answer_question_id`);

--
-- Indexes for table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`topic_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`profile_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignment_course_upload`
--
ALTER TABLE `assignment_course_upload`
  MODIFY `assignment_course_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `classwork_assignment`
--
ALTER TABLE `classwork_assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `classwork_assignment_upload`
--
ALTER TABLE `classwork_assignment_upload`
  MODIFY `assignment_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `classwork_material`
--
ALTER TABLE `classwork_material`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `classwork_material_upload`
--
ALTER TABLE `classwork_material_upload`
  MODIFY `material_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `classwork_question`
--
ALTER TABLE `classwork_question`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `classwork_question_upload`
--
ALTER TABLE `classwork_question_upload`
  MODIFY `question_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `class_enrolled`
--
ALTER TABLE `class_enrolled`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `class_theme`
--
ALTER TABLE `class_theme`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `student_assignment_course_answer`
--
ALTER TABLE `student_assignment_course_answer`
  MODIFY `answer_assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `student_question_course_answer`
--
ALTER TABLE `student_question_course_answer`
  MODIFY `answer_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `topic`
--
ALTER TABLE `topic`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
