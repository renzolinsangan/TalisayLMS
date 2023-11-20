-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2023 at 06:13 PM
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
-- Table structure for table `assignmentgrade`
--

CREATE TABLE `assignmentgrade` (
  `assignmentGrade_id` int(11) NOT NULL,
  `assignmentTitle` varchar(100) NOT NULL,
  `studentFirstName` varchar(64) NOT NULL,
  `studentLastName` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `gradeType` varchar(64) NOT NULL,
  `score` int(11) NOT NULL,
  `assignmentPoint` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignmentgrade`
--

INSERT INTO `assignmentgrade` (`assignmentGrade_id`, `assignmentTitle`, `studentFirstName`, `studentLastName`, `date`, `gradeType`, `score`, `assignmentPoint`, `student_id`, `teacher_id`, `class_id`, `assignment_id`) VALUES
(10, 'Cell Energetics', 'John Renzo', 'Linsangan', '2023-11-15', 'written', 10, 20, 28, 33, 39, 33),
(11, 'Cell Model', 'Marco Luis', 'Hernandez', '2023-11-15', 'performance', 20, 20, 37, 33, 39, 32),
(12, 'Cell Energetics', 'Marco Luis', 'Hernandez', '2023-11-15', 'written', 20, 20, 37, 33, 39, 33),
(15, 'Cell Model', 'Carl Justine', 'Aala', '2023-11-15', 'performance', 8, 20, 29, 33, 39, 32),
(16, 'Cell Energetics', 'Martin Clarence', 'Guantes', '2023-11-16', 'written', 12, 20, 38, 33, 39, 33),
(17, 'Cell Model', 'John Renzo', 'Linsangan', '2023-11-16', 'performance', 20, 20, 28, 33, 39, 32),
(18, 'Cell Model', 'R.J.', 'Liwag', '2023-11-16', 'performance', 15, 20, 36, 33, 39, 32),
(19, 'Cell Energetics', 'R.J.', 'Liwag', '2023-11-16', 'written', 10, 20, 36, 33, 39, 33);

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
(96, 'https://byjus.com/biology/cell-biology/', '', 52, 28, 28, 'submitted'),
(97, 'https://scopegurdoninstitute.co.uk/digital-toolkit-1-investigate-the-cell-3d-model', '', 52, 28, 27, 'submitted'),
(99, 'https://www.khanacademy.org/science/ap-biology/cellular-energetics', '', 49, 38, 33, 'submitted'),
(100, 'https://www.khanacademy.org/science/ap-biology/cellular-energetics', '', 52, 28, 33, 'submitted'),
(101, 'https://getbootstrap.com/docs/5.3/components/modal/', '', 45, 28, 32, ''),
(111, 'https://www.facebook.com/', '', 58, 28, 33, 'submitted');

-- --------------------------------------------------------

--
-- Table structure for table `classwork_assignment`
--

CREATE TABLE `classwork_assignment` (
  `assignment_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `instruction` varchar(1000) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `student` varchar(1000) NOT NULL,
  `type` varchar(64) NOT NULL,
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

INSERT INTO `classwork_assignment` (`assignment_id`, `title`, `instruction`, `class_name`, `student`, `type`, `point`, `date`, `due_date`, `time`, `class_topic`, `class_id`, `teacher_id`, `link`, `file`, `youtube`, `assignment_status`) VALUES
(32, 'Cell Model', 'Create a cell model in a one whole sheet of short bond paper, put it in Microsoft Docx, and submit it before due date.', 'STEM-Einstein - General Biology', 'R.J. Liwag,Marco Luis Hernandez,Carl Justine Aala,Martin Clarence Guantes,John Renzo Linsangan', 'performance', 20, '2023-10-16', '2023-10-17', '11:59 PM', 'Cell Biology', 39, 33, NULL, NULL, NULL, 'missing'),
(33, 'Cell Energetics', 'Create a story based on the information that is related about Cell Energetics. Be creative and submit it before the due date.', 'STEM-Einstein - General Biology', 'R.J. Liwag,Marco Luis Hernandez,Carl Justine Aala,Martin Clarence Guantes,John Renzo Linsangan', 'written', 20, '2023-10-15', '2023-10-17', '11:59 PM', 'Cell Biology', 39, 33, NULL, NULL, 'https://www.youtube.com/watch?v=eJ9Zjc-jdys', 'missing');

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
-- Table structure for table `classwork_exam`
--

CREATE TABLE `classwork_exam` (
  `exam_id` int(11) NOT NULL,
  `examTitle` varchar(64) NOT NULL,
  `examInstruction` varchar(1000) NOT NULL,
  `class_name` varchar(64) NOT NULL,
  `student` varchar(1000) NOT NULL,
  `examPoint` int(11) NOT NULL,
  `date` date NOT NULL,
  `dueDate` date NOT NULL,
  `time` varchar(64) NOT NULL,
  `classTopic` varchar(64) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `examStatus` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classwork_exam`
--

INSERT INTO `classwork_exam` (`exam_id`, `examTitle`, `examInstruction`, `class_name`, `student`, `examPoint`, `date`, `dueDate`, `time`, `classTopic`, `class_id`, `teacher_id`, `examStatus`) VALUES
(1, 'Final Exam in General Biology', 'No cheating, please answer honestly. Good Luck on your exam class, I wish you the best!', 'STEM-Einstein - General Biology', 'John Renzo Linsangan,R.J. Liwag,Martin Clarence Guantes,Marco Luis Hernandez,Carl Justine Aala', 50, '2023-11-20', '2023-11-20', '11:59 PM', 'Exams', 39, 33, 'missing');

-- --------------------------------------------------------

--
-- Table structure for table `classwork_material`
--

CREATE TABLE `classwork_material` (
  `material_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `class_name` varchar(64) NOT NULL,
  `student` varchar(1000) NOT NULL,
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
(114, 'Video About Cell Biology.', 'Please watch this video about Cell Biology so that for tomorrow, we will have a short recitation about the video. Thank you, have a nice day!', 'STEM-Einstein - General Biology', 'R.J. Liwag,Marco Luis Hernandez,Carl Justine Aala,Martin Clarence Guantes,John Renzo Linsangan', 'Cell Biology', 39, 33, '2023-11-15', NULL, '', 'https://www.youtube.com/watch?v=URUJD5NEXC8');

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
(156, 'https://www.pinterest.ph/pin/631348441494356252/', '', '', 1),
(183, 'https://www.pinterest.ph/pin/631348441494356252/', '', '', 1);

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
  `student` varchar(1000) NOT NULL,
  `type` varchar(64) NOT NULL,
  `point` int(11) NOT NULL,
  `point_received` int(11) NOT NULL,
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

INSERT INTO `classwork_question` (`question_id`, `title`, `question`, `instruction`, `class_name`, `student`, `type`, `point`, `point_received`, `date`, `due_date`, `time`, `class_topic`, `class_id`, `teacher_id`, `link`, `file`, `youtube`, `question_status`) VALUES
(21, 'Cell Biology', 'What is Cell Biology? ', 'Explain it one paragraph with a minimum of 5 sentence. Do not use any AI Tools in generating answers, if you are caught, your grade will be equivalent to 0. Do it before the due date, have a nice day.', 'STEM-Einstein - General Biology', 'R.J. Liwag,Marco Luis Hernandez,Carl Justine Aala,Martin Clarence Guantes,John Renzo Linsangan', 'written', 40, 0, '2023-10-15', '2023-10-17', '11:59 PM', 'Cell Biology', 39, 33, 'https://www.nature.com/scitable/topic/cell-biology-13906536/', NULL, NULL, 'missing'),
(23, 'Cell Evolution', 'What happens in a cell evolution?', 'Explain every process that happens in a cell evolution.', 'STEM-Einstein - General Biology', 'R.J. Liwag,Marco Luis Hernandez,Carl Justine Aala,Martin Clarence Guantes,John Renzo Linsangan', 'written', 20, 0, '2023-10-24', '2023-10-27', '11:59 PM', 'Cell Biology', 39, 33, NULL, NULL, NULL, 'missing');

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
-- Table structure for table `classwork_quiz`
--

CREATE TABLE `classwork_quiz` (
  `quiz_id` int(11) NOT NULL,
  `quizTitle` varchar(64) NOT NULL,
  `quizInstruction` varchar(1000) NOT NULL,
  `quizLink` varchar(1000) NOT NULL,
  `class_name` varchar(64) NOT NULL,
  `student` varchar(1000) NOT NULL,
  `type` varchar(64) NOT NULL,
  `quizPoint` int(11) NOT NULL,
  `date` date NOT NULL,
  `dueDate` date NOT NULL,
  `time` varchar(50) NOT NULL,
  `classTopic` varchar(64) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `quizStatus` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classwork_quiz`
--

INSERT INTO `classwork_quiz` (`quiz_id`, `quizTitle`, `quizInstruction`, `quizLink`, `class_name`, `student`, `type`, `quizPoint`, `date`, `dueDate`, `time`, `classTopic`, `class_id`, `teacher_id`, `quizStatus`) VALUES
(15, 'Cell Biology Quiz', 'Answer this quiz which is a true or false question. Goodluck on the quiz and make sure to answer it on time.', 'https://forms.gle/Ln2Mf75r5EU62BDM6', 'STEM-Einstein - General Biology', 'R.J. Liwag,Marco Luis Hernandez,Carl Justine Aala,Martin Clarence Guantes,John Renzo Linsangan', 'written', 10, '2023-11-10', '2023-11-15', '11:59 PM', 'Cell Biology', 39, 33, 'assigned');

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
  `student_lastname` varchar(50) NOT NULL,
  `date` date DEFAULT NULL,
  `archive_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_enrolled`
--

INSERT INTO `class_enrolled` (`class_id`, `tc_id`, `class_name`, `section`, `subject`, `grade_level`, `strand`, `teacher_id`, `class_code`, `first_name`, `last_name`, `student_id`, `student_firstname`, `student_lastname`, `date`, `archive_status`) VALUES
(43, 40, 'STEM-Einstein - Basic Calculus', 'Einstein', 'Basic Calculus', 'Grade 11', 'STEM', 33, 'D6XDFLx', 'Patrick', 'Star', 28, 'John Renzo', 'Linsangan', '2023-09-07', 'archive'),
(44, 41, 'STEM-Einstein - Art Appreciation', 'Einstein', 'Art Apprecitiation', 'Grade 11', 'STEM', 33, '3YzS3aC', 'Patrick', 'Star', 28, 'John Renzo', 'Linsangan', '2023-09-07', 'archive'),
(54, 45, 'STEM-Einstein - Oral Communication', 'Einstein', 'Oral Communication', 'Grade 11', 'STEM', 33, 'RchAh06', 'Patrick', 'Star', 28, 'John Renzo', 'Linsangan', '2023-11-08', 'archive'),
(58, 39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'Grade 11', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star', 28, 'John Renzo', 'Linsangan', '2023-11-19', ''),
(60, 39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'Grade 11', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star', 36, 'R.J.', 'Liwag', '2023-11-19', ''),
(61, 39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'Grade 11', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star', 38, 'Martin Clarence', 'Guantes', '2023-11-19', ''),
(62, 39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'Grade 11', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star', 37, 'Marco Luis', 'Hernandez', '2023-11-19', ''),
(64, 39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'Grade 11', 'STEM', 33, 'ke1IPj7', 'Patrick', 'Star', 29, 'Carl Justine', 'Aala', '2023-11-19', ''),
(65, 48, 'TVL-ShangriLa - Information and Communications Tec', 'ShangriLa', 'Information and Communications Technology', 'Grade 11', 'TVL', 32, 'KNBFQcc', 'Spongebob', 'Squarepants', 31, 'Marissa Margarette', 'Garcia', '2023-11-20', ''),
(66, 48, 'TVL-ShangriLa - Information and Communications Tec', 'ShangriLa', 'Information and Communications Technology', 'Grade 11', 'TVL', 32, 'KNBFQcc', 'Spongebob', 'Squarepants', 69, 'Precious Alliyah', 'Ramos', '2023-11-20', ''),
(67, 48, 'TVL-ShangriLa - Information and Communications Tec', 'ShangriLa', 'Information and Communications Technology', 'Grade 11', 'TVL', 32, 'KNBFQcc', 'Spongebob', 'Squarepants', 71, 'Mnica Kate', 'Diego', '2023-11-20', ''),
(68, 48, 'TVL-ShangriLa - Information and Communications Tec', 'ShangriLa', 'Information and Communications Technology', 'Grade 11', 'TVL', 32, 'KNBFQcc', 'Spongebob', 'Squarepants', 72, 'Marianne Mae ', 'Alvarez', '2023-11-20', ''),
(69, 48, 'TVL-ShangriLa - Information and Communications Tec', 'ShangriLa', 'Information and Communications Technology', 'Grade 11', 'TVL', 32, 'KNBFQcc', 'Spongebob', 'Squarepants', 70, 'Leila', 'Macasinag', '2023-11-20', ''),
(70, 48, 'TVL-ShangriLa - Information and Communications Tec', 'ShangriLa', 'Information and Communications Technology', 'Grade 11', 'TVL', 32, 'KNBFQcc', 'Spongebob', 'Squarepants', 68, 'Irene', 'Llona', '2023-11-20', ''),
(71, 49, 'TVL-Macchiato - Information and Communications Tec', 'Macchiato', 'Information and Communications Technology', 'Grade 12', 'TVL', 32, 'dUTZrWs', 'Spongebob', 'Squarepants', 77, 'Ron James', 'Barrion', '2023-11-20', ''),
(72, 49, 'TVL-Macchiato - Information and Communications Tec', 'Macchiato', 'Information and Communications Technology', 'Grade 12', 'TVL', 32, 'dUTZrWs', 'Spongebob', 'Squarepants', 74, 'Justine', 'Mendoza', '2023-11-20', ''),
(73, 49, 'TVL-Macchiato - Information and Communications Tec', 'Macchiato', 'Information and Communications Technology', 'Grade 12', 'TVL', 32, 'dUTZrWs', 'Spongebob', 'Squarepants', 76, 'Josh', 'Centeno', '2023-11-20', ''),
(74, 49, 'TVL-Macchiato - Information and Communications Tec', 'Macchiato', 'Information and Communications Technology', 'Grade 12', 'TVL', 32, 'dUTZrWs', 'Spongebob', 'Squarepants', 75, 'Johnrey', 'Ramillo', '2023-11-20', ''),
(75, 49, 'TVL-Macchiato - Information and Communications Tec', 'Macchiato', 'Information and Communications Technology', 'Grade 12', 'TVL', 32, 'dUTZrWs', 'Spongebob', 'Squarepants', 73, 'Jhonlloyd', 'Casaul', '2023-11-20', '');

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
(23, 'theme4.jpg', 39, 33, 'STEM-Einstein - General Biology', 'old'),
(24, 'theme7.jpg', 43, 32, 'TVL-ShangriLa - Mechanics', 'recent'),
(25, 'theme9.jpg', 40, 33, 'STEM-Einstein - Basic Calculus', 'recent'),
(26, 'theme8.jpg', 45, 33, 'STEM-D - Oral Communication', 'old'),
(27, 'theme8.jpg', 45, 33, 'STEM-Einstein - Oral Communication', 'recent'),
(28, 'theme5.jpg', 46, 33, 'STEM-Newton - General Chemistry', 'old'),
(29, 'theme8.jpg', 47, 33, 'STEM Einstein - Science', 'recent'),
(30, 'theme8.jpg', 48, 32, 'TVL-ShangriLa - Information and Communications Tec', 'recent'),
(31, 'theme5.jpg', 49, 32, 'TVL-Macchiato - Information and Communications Tec', 'recent'),
(32, 'theme7.jpg', 39, 33, 'STEM-Einstein - General Biology', 'old'),
(33, 'theme5.jpg', 39, 33, 'STEM-Einstein - General Biology', 'old'),
(34, 'theme6.jpg', 39, 33, 'STEM-Einstein - General Biology', 'old'),
(35, 'theme5.jpg', 39, 33, 'STEM-Einstein - General Biology', 'old'),
(36, 'theme8.jpg', 39, 33, 'STEM-Einstein - General Biology', 'recent'),
(37, 'theme7.jpg', 46, 33, 'STEM-Newton - General Chemistry', 'recent');

-- --------------------------------------------------------

--
-- Table structure for table `examgrade`
--

CREATE TABLE `examgrade` (
  `examGrade_id` int(11) NOT NULL,
  `examTitle` varchar(64) NOT NULL,
  `studentFirstName` varchar(64) NOT NULL,
  `studentLastName` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `score` int(11) NOT NULL,
  `examPoint` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examgrade`
--

INSERT INTO `examgrade` (`examGrade_id`, `examTitle`, `studentFirstName`, `studentLastName`, `date`, `score`, `examPoint`, `student_id`, `teacher_id`, `class_id`, `exam_id`) VALUES
(2, 'Final Exam in General Biology', 'Marco Luis', 'Hernandez', '2023-11-15', 50, 50, 37, 33, 39, 1),
(3, 'Final Exam in General Biology', 'R.J.', 'Liwag', '2023-11-15', 48, 50, 36, 33, 39, 1),
(5, 'Final Exam in General Biology', 'John Renzo', 'Linsangan', '2023-11-17', 50, 50, 28, 33, 39, 1);

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
  `date` date NOT NULL,
  `attachment` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `firstname`, `lastname`, `report_title`, `details`, `date`, `attachment`) VALUES
(21, 'John Renzo', 'Linsangan', 'Push notification', 'You should enable the functionality of the push-notification.', '2023-11-15', '');

-- --------------------------------------------------------

--
-- Table structure for table `friend`
--

CREATE TABLE `friend` (
  `primary_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friend`
--

INSERT INTO `friend` (`primary_id`, `user_id`, `friend_id`, `name`, `date`) VALUES
(7, 28, 29, 'Carl Justine C. Aala', '2023-11-15'),
(8, 36, 28, 'John Renzo G. Linsangan', '2023-11-15'),
(9, 28, 36, 'R.J. C. Liwag', '2023-11-15');

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
(7, 'Smog O Vog', 'news', 'DOST_PHIVOLCS', '2023-11-12', 'all', '2023-11-12', '2023-11-21', 'Narito ang mga kaalaman tungkol sa Volcanic Smog o Vog at mga pamamaraan upang makaiwas sa panganib na dulot nito. \r\nAng vog ay isang uri ng polusyon sa hangin na sanhi ng mga bulkan. Binubo ito ng mga pinong patak na naglalaman ng volcanic gas tulad ng Sulfur Dioxide (SO2).\r\n• Ano ang Sulfur Dioxide o SO2?\r\nAng sulfur dioxide ay isang nakakalasong usok na maa-aring maka-apekto sa kalusugan ng tao at hayop, pati na rin sa mga halaman. Maaring ma expose ang isang indibidwal kung ito ay malalanghap o sa pamamagitan ng skin contact\r\n• Ano ang epekto ng vog?\r\nMaaring magdulot ito ng iritasyon sa mga mata, lalamunan at respiratory tract na maaaring maging malubha depende sa kosentrasyon o tagal ng pagkalanghap nito.\r\n• Sinu-sino ang mga sensitibo sa masamang epekto ng vog? \r\nMga may kondisyon sa kalusugan tulad ng hika, sakit sa baga at sakit sa puso. Matatanda, mga buntis at mga bata.\r\n• Ano ang dapat gawin?\r\nLimitahan ang pagkakalantad o exposure sa vog. Iwasan ang mga aktibidad sa labas o manatili na lamang sa loob ng bahay at isara ang mga bintana at pintuan upang maiwasang makapasok ang vog sa loob ng bahay.\r\n• Paano pu-protektahan ang sarili?\r\nMagsuot ng facemask o mas mabuti kung N95 facemasks o gas mask. Uminom ng maraming tubig upang maibsan ang iritasyon o paninikip ng daluyan ng paghinga. Kung kabilang sa mga sensitibong grupo, siguraduhing subaybayan ang inyong klagayan at magpatingin agad sa doktor o sa barangay health unit kung kinakailangan. Kung makakaranas ng matinding epekto, magpatingin agad sa doktor o sa barangay health unit.', 'picture_652681a2d64fd.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `questiongrade`
--

CREATE TABLE `questiongrade` (
  `questionGrade_id` int(11) NOT NULL,
  `questionTitle` varchar(100) NOT NULL,
  `studentFirstName` varchar(64) NOT NULL,
  `studentLastName` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `gradeType` varchar(64) NOT NULL,
  `score` int(11) NOT NULL,
  `questionPoint` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questiongrade`
--

INSERT INTO `questiongrade` (`questionGrade_id`, `questionTitle`, `studentFirstName`, `studentLastName`, `date`, `gradeType`, `score`, `questionPoint`, `student_id`, `teacher_id`, `class_id`, `question_id`) VALUES
(12, 'Cell Biology', 'Martin Clarence', 'Guantes', '2023-10-15', 'written', 25, 40, 38, 33, 39, 21),
(13, 'Cell Biology', 'John Renzo', 'Linsangan', '2023-11-15', 'written', 25, 40, 28, 33, 39, 21),
(14, 'Cell Evolution', 'John Renzo', 'Linsangan', '2023-11-15', 'written', 20, 20, 28, 33, 39, 23),
(15, 'Cell Biology', 'Marco Luis', 'Hernandez', '2023-11-15', 'written', 40, 40, 37, 33, 39, 21),
(16, 'Cell Evolution', 'Marco Luis', 'Hernandez', '2023-11-15', 'written', 20, 20, 37, 33, 39, 23),
(19, 'Cell Evolution', 'Martin Clarence', 'Guantes', '2023-11-16', 'written', 15, 20, 38, 33, 39, 23),
(20, 'Cell Biology', 'R.J.', 'Liwag', '2023-11-16', 'written', 10, 40, 36, 33, 39, 21),
(21, 'Cell Evolution', 'R.J.', 'Liwag', '2023-11-16', 'written', 18, 20, 36, 33, 39, 23);

-- --------------------------------------------------------

--
-- Table structure for table `quizgrade`
--

CREATE TABLE `quizgrade` (
  `quizGrade_id` int(11) NOT NULL,
  `quizTitle` varchar(64) NOT NULL,
  `studentFirstName` varchar(64) NOT NULL,
  `studentLastName` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `gradeType` varchar(64) NOT NULL,
  `score` int(11) NOT NULL,
  `quizPoint` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizgrade`
--

INSERT INTO `quizgrade` (`quizGrade_id`, `quizTitle`, `studentFirstName`, `studentLastName`, `date`, `gradeType`, `score`, `quizPoint`, `student_id`, `teacher_id`, `class_id`, `quiz_id`) VALUES
(10, 'Cell Biology Quiz', 'Marco Luis', 'Hernandez', '2023-11-15', 'written', 10, 10, 37, 33, 39, 15),
(12, 'Cell Biology Quiz', 'Martin Clarence', 'Guantes', '2023-11-16', 'written', 10, 10, 38, 33, 39, 15),
(13, 'Cell Biology Quiz', 'R.J.', 'Liwag', '2023-11-16', 'written', 10, 10, 36, 33, 39, 15),
(14, 'Cell Biology Quiz', 'John Renzo', 'Linsangan', '2023-11-17', 'written', 10, 10, 28, 33, 39, 15);

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
  `written` int(11) NOT NULL,
  `performance` int(11) NOT NULL,
  `exam` int(11) NOT NULL,
  `basegrade` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_code` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `archive_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`class_id`, `class_name`, `section`, `subject`, `strand`, `written`, `performance`, `exam`, `basegrade`, `teacher_id`, `class_code`, `first_name`, `last_name`, `archive_status`) VALUES
(39, 'STEM-Einstein - General Biology', 'Einstein', 'General Biology', 'STEM', 25, 50, 25, 60, 33, 'ke1IPj7', 'Patrick', 'Star', ''),
(40, 'STEM-Einstein - Basic Calculus', 'Einstein', 'Basic Calculus', 'STEM', 25, 50, 25, 60, 33, 'D6XDFLx', 'Patrick', 'Star', 'archive'),
(41, 'STEM-Einstein - Art Appreciation', 'Einstein', 'Art Apprecitiation', 'STEM', 25, 50, 25, 60, 33, '3YzS3aC', 'Patrick', 'Star', 'archive'),
(45, 'STEM-Einstein - Oral Communication', 'Einstein', 'Oral Communication', 'STEM', 25, 50, 25, 60, 33, 'RchAh06', 'Patrick', 'Star', 'archive'),
(46, 'STEM-Newton - General Chemistry', 'Newton', 'General Chemistry', 'STEM', 25, 50, 25, 60, 33, 'yAY1qt9', 'Patrick', 'Star', ''),
(47, 'STEM Einstein - Science', 'Einstein', 'Science', 'STEM', 25, 50, 25, 60, 33, 'mDnlUBK', 'Patrick', 'Star', ''),
(48, 'TVL-ShangriLa - Information and Communications Tec', 'ShangriLa', 'Information and Communications Technology', 'TVL', 20, 50, 30, 60, 32, 'KNBFQcc', 'Spongebob', 'Squarepants', ''),
(49, 'TVL-Macchiato - Information and Communications Tec', 'Macchiato', 'Information and Communications Technology', 'TVL', 20, 50, 30, 60, 32, 'dUTZrWs', 'Spongebob', 'Squarepants', '');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `primary_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`primary_id`, `user_id`, `student_id`, `name`, `date`) VALUES
(5, 33, 36, 'R.J. C. Liwag', '2023-11-16'),
(6, 33, 37, 'Marco Luis S. Hernandez', '2023-11-16');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignment_course_answer`
--

CREATE TABLE `student_assignment_course_answer` (
  `answer_assignment_id` int(11) NOT NULL,
  `assignment_course_upload_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `assignment_link` varchar(2000) DEFAULT NULL,
  `assignment_file` varchar(2000) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `assignment_course_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assignment_course_answer`
--

INSERT INTO `student_assignment_course_answer` (`answer_assignment_id`, `assignment_course_upload_id`, `assignment_id`, `title`, `date`, `assignment_link`, `assignment_file`, `user_id`, `class_id`, `teacher_id`, `assignment_course_status`) VALUES
(154, 99, 33, 'Cell Energetics', '2023-10-15', 'https://www.khanacademy.org/science/ap-biology/cellular-energetics', '', 38, 49, 33, 'turned-in late'),
(155, 100, 33, 'Cell Energetics', '2023-10-15', 'https://www.khanacademy.org/science/ap-biology/cellular-energetics', '', 28, 52, 33, 'turned-in late'),
(163, 0, 32, 'Cell Model', '2023-10-16', NULL, NULL, 28, 58, 33, 'turned-in late');

-- --------------------------------------------------------

--
-- Table structure for table `student_exam_course_answer`
--

CREATE TABLE `student_exam_course_answer` (
  `answer_exam_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `examTitle` varchar(100) NOT NULL,
  `examLink` varchar(100) NOT NULL,
  `examPoint` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `exam_course_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_exam_course_answer`
--

INSERT INTO `student_exam_course_answer` (`answer_exam_id`, `exam_id`, `examTitle`, `examLink`, `examPoint`, `date`, `user_id`, `class_id`, `teacher_id`, `exam_course_status`) VALUES
(4, 1, 'Final Exam in General Biology', 'https://docs.google.com/forms/d/e/1FAIpQLSc0jl5ypYrj5NU8E05cAnb5QFZv3IxCd6nMyIg-s850bTbSVA/viewform?', 50, '2023-11-10', 28, 56, 33, 'turned-in late');

-- --------------------------------------------------------

--
-- Table structure for table `student_question_course_answer`
--

CREATE TABLE `student_question_course_answer` (
  `answer_question_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `point` int(64) NOT NULL,
  `title` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `question_answer` varchar(2000) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `question_course_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_question_course_answer`
--

INSERT INTO `student_question_course_answer` (`answer_question_id`, `question_id`, `point`, `title`, `date`, `question_answer`, `user_id`, `class_id`, `teacher_id`, `question_course_status`) VALUES
(25, 21, 30, 'Cell Biology', '2023-10-15', 'Cell biology is the scientific discipline that investigates the structure, function, and behavior of cells, which are the fundamental units of life. It encompasses the study of various cellular processes, interactions, and the molecular mechanisms that underlie the functions of cells within living organisms. Cell biology is integral to understanding how life is organized and how organisms function at the cellular level.', 28, 52, 33, 'turned-in late'),
(27, 21, 40, 'Cell Biology', '2023-10-15', 'Cell biology is a branch of biology that focuses on the study of cells, which are the basic structural and functional units of living organisms. It encompasses a wide range of topics related to cells, including their structure, function, physiology, and interactions. Cell biology explores how cells are organized, how they communicate with each other, and how they carry out various biological processes.', 38, 49, 33, 'turned-in late'),
(31, 23, 20, 'Cell Evolution', '2023-10-24', 'What happens from cell evolution is that, cell evolution traces the development and diversification of cells, the fundamental units of life, over billions of years. All life shares a common ancestry, and cells have evolved from simple, early forms to more complex prokaryotic and eukaryotic structures. Genetic variation, driven by mutations, has played a central role, with natural selection favoring advantageous traits that enhance an organism\'s fitness and adaptation to its environment. This ongoing process has led to the emergence of new species and the diverse cellular structures and functions seen today. Molecular biology has shed light on the genetic and molecular changes that underlie cell evolution, and the endosymbiotic theory explains the origins of some cellular structures. Cell evolution is an integral part of the broader theory of biological evolution, illustrating the interconnectedness of all life on Earth.', 28, 58, 33, 'turned-in late');

-- --------------------------------------------------------

--
-- Table structure for table `student_quiz_course_answer`
--

CREATE TABLE `student_quiz_course_answer` (
  `answer_quiz_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `quizTitle` varchar(64) NOT NULL,
  `quizLink` varchar(100) NOT NULL,
  `quizPoint` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `quiz_course_status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_quiz_course_answer`
--

INSERT INTO `student_quiz_course_answer` (`answer_quiz_id`, `quiz_id`, `quizTitle`, `quizLink`, `quizPoint`, `date`, `user_id`, `class_id`, `teacher_id`, `quiz_course_status`) VALUES
(10, 15, 'Cell Biology Quiz', 'https://forms.gle/Ln2Mf75r5EU62BDM6', 10, '2023-11-20', 28, 58, 33, 'turned in');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `primary_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`primary_id`, `user_id`, `teacher_id`, `name`, `date`) VALUES
(8, 28, 33, 'Patrick H. Star', '2023-11-18');

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
(79, 'Cell Biology', 39, 33, 'Einstein', 'General Biology', 'STEM', 'STEM-Einstein - General Biology'),
(82, 'Exams', 39, 33, 'Einstein', 'General Biology', 'STEM', 'STEM-Einstein - General Biology'),
(83, 'Cell', 39, 33, 'Einstein', 'General Biology', 'STEM', 'STEM-Einstein - General Biology');

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
  `children` varchar(500) NOT NULL,
  `usertype` varchar(50) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `username`, `password`, `email`, `address`, `contact`, `firstname`, `middlename`, `lastname`, `grade_level`, `department`, `section`, `children`, `usertype`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(28, 'renzolinsangan', '$2y$10$vdNYpMxHSGPQ8URNzheJ.uAW.fC3pOhyfevUKTtEA20POmytl30Ma', 'renzolinsangan11@gmail.com', 'Block 3 Lot 44, Croatia St. Lynville Subdv. Purok 3, Latag, Lipa City, Batangas', '09657008926', 'John Renzo', 'Goyena', 'Linsangan', 'Grade 11', 'stem', 'Einstein', '', 'student', '3ccfc0c01335fa7ed19449e28d9bc689e1bf0181875a8992a9614fced3e012d2', '2023-11-12 14:26:46'),
(29, 'carljustine', '$2y$10$ceE9/MKm90xp3P6srqIJ6udFR2NgLpkg3gifIVQwJ4F5ChjjiD5u.', 'carljustine@gmail.com', 'Talisay, Batangas', '09223043043', 'Carl Justine', 'C ', 'Aala', 'Grade 11', 'stem', 'Einstein', '', 'student', '4685009adc6793cf52bd8c25e9c55114c3a3b92a98aa3a6cb25ba3a47fcdc487', '2023-10-06 07:36:21'),
(30, 'patriciaperez', '$2y$10$BXK56vTkmz.DALWwU/595.i3DhIWmw0zz8rnq7Nt6pEAynUzIq5/q', 'patriciaperez@gmail.com', 'Talisay, Batangas', '09232424244', 'Patricia', 'C', 'Perez', 'Grade 11', 'abm', 'Pacioli', '', 'student', NULL, NULL),
(31, 'marissamargarette', '$2y$10$uqnc8ZHIbxVmixLveq5SmOkGhsdrEB/gTa6TTBc.yxvRIam1MtC7e', 'marissagrc4@gmail.com', 'Purok 6, Brgy. Natatas, Tanauan City, Batangas', '09497920130', 'Marissa Margarette', 'Ocampo', 'Garcia', 'Grade 11', 'tvl', 'ShangriLa', '', 'student', 'b977467780a392b9124d4340aeab522e03adc312bd5711ef355f652d7a9e2ac4', '2023-10-13 12:54:16'),
(32, 'spongebob', '$2y$10$Xw3bTrw2rYk.p4RFKQF3ieS1bClb1b.gMHuwmCDx6aq7/zKALI1q.', 'spongebobsquarepants12@gmail.com', 'Bikini Bottom, Sa may Pinya', '09523232323', 'Spongebob', 'hindi', 'Squarepants', '', 'tvl', '', '', 'teacher', NULL, NULL),
(33, 'patrick', '$2y$10$J9gOlxnbm99eUvHa.UwgVOdXfXhYcMXvzEdUkSUjsGfc.vKK8to7K', 'patrickstar@gmail.com', 'Bikini Bottom, Stone House', '09232325556', 'Patrick', 'hindi', 'Star', '', 'stem', '', '', 'teacher', NULL, NULL),
(36, 'rjliwag', '$2y$10$NA18/Bn3ehbXsmw/Hmuo6uoIUGWBk8j/BDWSEbyUnqPa5zbSnOfm.', 'rjliwag@gmail.com', 'Talisay, Batangas', '09451213212', 'R.J.', 'Caguicla', 'Liwag', 'Grade 11', 'stem', 'Einstein', '', 'student', NULL, NULL),
(37, 'marcoluis', '$2y$10$yXO0x1CyK/zWzgUU4XFr/.6KJcVqHQ8Vs.CGxcSMmxZVAh6vDkQnW', 'marcoluishernandez@gmail.com', 'San Luis, Batangas', '09543221548', 'Marco Luis', 'S', 'Hernandez', 'Grade 11', 'stem', 'Einstein', '', 'student', NULL, NULL),
(38, 'martinclarence', '$2y$10$h2.Fn78znTIT.Vp.833hn.adR7e/reV.8rkpBa9GcIAxGm/rl.s56', 'martinclarensyo@gmail.com', 'San Miguel, Sto. Tomas, Batangas', '09485377283', 'Martin Clarence', 'O', 'Guantes', 'Grade 11', 'stem', 'Einstein', '', 'student', NULL, NULL),
(39, 'rickylinsangan', '$2y$10$Iipff.OyvD2UR.BgjNpiWO0BCOyxAXW.ygsn7aqbxZ0zS.AhrsEZa', 'rickylinsangan18@yahoo.com', 'Block 3 Lot 44, Croatia St. Lynville Subdv. Purok 3, Latag, Lipa City, Batangas', '09352162476', 'Ricardo', 'Espino', 'Linsangan', '', '', '', 'John Renzo Linsangan', 'parent', NULL, NULL),
(41, 'JMcacao', '$2y$10$4iUxyefCljlEuLA4NTpjZ.mI4XMw0LjPsxnLHClGoFWY0c7bpP3YC', 'jmcacao@gmail.com', 'Aya Talisay, Batangas', '09519226984', 'Jm Kerby', 'Doctora', 'Cacao', 'Grade 11', 'humss', 'Hemingway', '', 'student', NULL, NULL),
(42, 'liwagarchiee', '$2y$10$b0gXV9ayfSmLE4Rc2AJ/EOKIq9L5uc0g6PjYoAlt3nrh3uKo1Y4eW', 'archieliwag09@gmail.com', 'Aya Talisay, Batangas', '09093251053', 'Archie', 'Caguicla', 'Liwag', 'Grade 12', 'humss', 'Herodotus', '', 'student', NULL, NULL),
(43, 'arahbella', '$2y$10$oggP5TO2ccdlM.OAUAgJGOhHUq00IwMUJ6VemYHpOiiAZPsHjwtpy', 'arahbella_17@gmail.com', 'Aya Talisay, Batangas', '09485734003', 'Arah Bella', 'Castillo', 'Landicho', 'Grade 11', 'humss', 'Hemingway', '', 'student', NULL, NULL),
(44, 'Rosemarie', '$2y$10$Chnqj5nmiEzU3mOKpPlQeu5RQJMfRZqtrwLHRjuGWjw0dkwd7S0tm', 'mendoza.rosemarie@gmail.com', 'Zone 7 Talisay, Batangas', '09094826732', 'Rosemarie', 'Santillan', 'Mendoza', 'Grade 11', 'humss', 'Hemingway', '', 'student', NULL, NULL),
(45, 'johnjoshua', '$2y$10$RshMb84U.gtEaxOs3/noZu0Mf7dEnyuR22VAmDoj8Ri5O4.S7pHUS', 'joshua_lapade@gmail.com', 'Ambulong Tanauan City, Batangas', '09750023348', 'John Joshua', 'Arante', 'Lapade', 'Grade 11', 'humss', 'Hemingway', '', 'student', NULL, NULL),
(46, 'nicoleanillo', '$2y$10$oC.f4xxlJZvnOeSyohVc7.VQ01KKsPTYf11zsQ/ukl.l.J0Trswq.', 'Trixnicole.anillo@gmail.com', 'Zone 3 Talisay, Batangas', '09097456633', 'Trixie Nicole', 'Anillo', 'Malabanan', 'Grade 11', 'humss', 'Hemingway', '', 'student', NULL, NULL),
(47, 'Edward0', '$2y$10$BDDBrTEdA18Ygpzwz5DyauAfjJ5Rr.OwA8jdmscwoNVvtbVOCCDje', 'edwardjames@gmail.com', 'Aya Talisay, Batangas', '09245505823', 'James Edward', 'Dia', 'Anillo', 'Grade 12', 'humss', 'Herodotus', '', 'student', NULL, NULL),
(48, 'kristelaaa', '$2y$10$fuiPjwPL9H3tRLaYUQH2iO/Z/4t1nQ4uMRZV1XrYrukiq9qCMEZPy', 'imkristel@gmail.com', 'Tumaway Talisay, Batangas', '09495280023', 'Kristel', 'Arante', 'Reyes', 'Grade 12', 'humss', 'Herodotus', '', 'student', NULL, NULL),
(49, 'crizeldame', '$2y$10$SHAmqBNO7Bomxf8uDuSlfu7VONVXNjqVzP2FSp1iqyO82JdvJzFSW', 'criselda_atienza@gmail.com', 'Tumaway Talisay, Batangas', '09852745502', 'Criselda', 'Ishia', 'Atienza', 'Grade 12', 'humss', 'Herodotus', '', 'student', NULL, NULL),
(50, 'sofieee', '$2y$10$EDtPMp6VHdAVWl2ve94hFehLwl/xYqSiXHE66wdqS3nb7./SHoTm2', 'sofia_smith18@gmail.com', 'Tumaway Talisay, Batangas', '09058452231', 'Sofia', 'Quiones', 'Smith', 'Grade 12', 'humss', 'Herodotus', '', 'student', NULL, NULL),
(51, 'rhea.17', '$2y$10$Cdm9qzGxdGOkT824DRfsz.27Yd9Vf6W2pzq/GgcywmLIlnjZymSXK', 'panganibanrhea@gmail.com', 'Purok 4, Ambuong Tanauan City, Batangas', '09289500781', 'Rhea', 'Luciano', 'Panganiban', 'Grade 11', 'abm', 'Pacioli', '', 'student', NULL, NULL),
(52, 'johnpaul', '$2y$10$YmaeQlu3vfPlvZa9uzmw0emaGoVrMU12nVejdSF.ammHWoFK5Hzyy', 'pauljohn@gmail.com', 'Aya Talisay, Batangas', '09054332502', 'JohnPaul', 'Aala', 'Carolino', 'Grade 12', 'abm', 'Fayol', '', 'student', NULL, NULL),
(53, 'judyann', '$2y$10$nl6LICfI8TlFMdgf2dGtn.7IZb.noi1yzv9iSCa/L4PlX.yKcePfy', 'judy_ann@gmail.com', 'Tumaway Talisay, Batangas', '09858900471', 'Judyann', 'Castillo', 'Samiano', 'Grade 12', 'abm', 'Fayol', '', 'student', NULL, NULL),
(54, 'Jamescarlo13', '$2y$10$DG0rqbGqHSjif8zZxOBjWO6fbNl80R0j4hXXDJYrR5tiBGfO3VvdG', 'jamescarlo_13@gmail.com', 'Aya Talisay, Batangas', '09106452235', 'James Carlo', 'Samiano', 'Velanzuela', 'Grade 11', 'abm', 'Pacioli', '', 'student', NULL, NULL),
(55, 'Apriljoy', '$2y$10$xo0vUcEl3TYQg4.a.qLiMO5EVp6bz3OJ4llMhxPk6KuAtStkmXdH.', 'apriljoy_alvarez@gmail.com', 'Ambulong Tanauan City, Batangas', '09454334207', 'April Joy', 'Panal', 'Alvarez', 'Grade 12', 'abm', 'Fayol', '', 'student', NULL, NULL),
(56, 'Ericamae16', '$2y$10$Fl3ZfKmb5Ek1Va4ArNQBhewCSrtCY9Ni9mFXSyX9mcEg4Pkd0QlSi', 'erica.mae16@gmail.com', 'Aya Talisay, Batangas', '09324875661', 'Erica Mae', 'Arante', 'Salioman', 'Grade 12', 'abm', 'Fayol', '', 'student', NULL, NULL),
(57, 'Maryjane24', '$2y$10$7S1yyjo5RNUUCtwP7PrhROoJbi67EgjDX1yglCYu7glMW1tsnKkaO', 'maryjane_brazil@gmail.com', 'Tumaway Talisay, Batangas', '09076054169', 'Maryjane', 'Panganiban', 'Brazil', 'Grade 11', 'abm', 'Pacioli', '', 'student', NULL, NULL),
(58, 'princessjasmine', '$2y$10$kuzYjxIu.JL5PqAf7o0h9eU3Zez71qaHBiMkolV4PFqpAnHzqf1di', 'princessjas.narvaez@gmail.com', 'Ambulong Tanauan City, Batangas', '09825440452', 'Princess Jasmine', 'Alonzo', 'Narvaez', 'Grade 12', 'abm', 'Fayol', '', 'student', NULL, NULL),
(59, 'jelyanne', '$2y$10$UuWElaQPKs1LCXZ9tqkAAO4yOWtE1lyoKGBMXTy0JV2a/voGsQOkS', 'jelyanne@gmail.com', 'Zone 4 Talisay, Batangas', '09056524338', 'Jelly Anne', 'Salvador', 'Anciado', 'Grade 11', 'abm', 'Pacioli', '', 'student', NULL, NULL),
(60, 'ayeishaaaa', '$2y$10$imNNKRbSOpRdLpnVhOYcLuTgkCgxt0qqvnOPbAQ7b4UPnRhtYOu4O', 'ayeisha.jane@gmail.com', 'Ambulong Tanauan City, Batangas', '09498255056', 'Ayeisha Jane', 'Genes', 'Perez', 'Grade 11', 'abm', 'Pacioli', '', 'student', NULL, NULL),
(61, 'carlodizon', '$2y$10$1LYsA.a9oaVai.yfOoNAse1DEYkunvx/zsGu5uopY3ZLUXY32eTsy', 'Carlo_dizon@gmail.com', 'Tumaway Talisay, Batangas', '09185641284', 'John Carlo', 'Caguicla', 'Dizon', 'Grade 11', 'stem', 'Einstein', '', 'student', NULL, NULL),
(62, 'jedrickliwanag', '$2y$10$UCeXyFzo.qvObvin.4SifuzGE4LxMKN4IPchfBqorLmm8/ZAOD1zS', 'Jeds.vil@gmail.com', 'Sta. Maria Talisay, Batangas', '09163365224', 'Jedrick', 'Liwanag', 'Villanueva', 'Grade 12', 'stem', 'Edison', '', 'student', NULL, NULL),
(63, 'markgerochii', '$2y$10$gyYvv.CvKymk6mXM.gKvWenJU2wiVPZoquioz0KokrLfO0bgInATO', 'Markgerochii@gmail.com', 'Zone 4 Talisay, Batangas', '09504450122', 'Mark', 'Gil', 'Gerochi', 'Grade 12', 'stem', 'Edison', '', 'student', NULL, NULL),
(64, 'angelaclan', '$2y$10$FRsb0mpGdLze2hhJ8n7Sqe9aOYZUMCSn6UlpTrllsjg7R/n94bNq6', 'Angelaclan33@gmail.com', 'Zone 6 Talisay, Batangas', '09173365289', 'Angel Jeremiah', 'Perez', 'Aclan', 'Grade 12', 'stem', 'Edison', '', 'student', NULL, NULL),
(65, 'sherinemendoza', '$2y$10$o5GvkAqfGJIRhyVfqvQpWecikuBzvRcUn9CJmdEJWqQydZJjTTlqu', 'Sherine.Mendoza22@gmail.com', 'Banga Talisay, Batangas', '09196631258', 'Sherine', 'Laurel', 'Mendoza', 'Grade 12', 'stem', 'Edison', '', 'student', NULL, NULL),
(66, 'jilmatibag', '$2y$10$k/ROclOPFdevL5KI3PTbq.Sco8Eu8j0.VPE2YydCRcX107giT1Uku', 'jilmatibag14@gmail.com', 'Zone 1 Talisay, Batangas', '09105647899', 'Jillian', 'Mutoc', 'MatibagHuerto', 'Grade 12', 'stem', 'Edison', '', 'student', NULL, NULL),
(67, 'jusmendoza', '$2y$10$3Tzrs9ys0FUJLBSR58YuW.NwWQM4TsIuUlC/LsAlwgDnZSxtGv5N2', 'jusmendoza43@gmail.com', 'Quiling Talisay, Batangas', '09324416875', 'Eugine', 'Huerto', 'Marquez', 'Grade 12', 'stem', 'Edison', '', 'student', NULL, NULL),
(68, 'irenellon', '$2y$10$L8sxnPKwNx29e3XttzS3bemfB.JSi9yFL6XJhc.n7FMobBtx7T4NS', 'Irenellon03@gmail.com', 'Tumaway Talisay, Batangas', '09395562254', 'Irene', 'Mendoza', 'Llona', 'Grade 11', 'tvl', 'ShangriLa', '', 'student', NULL, NULL),
(69, 'alliramos', '$2y$10$opxISbDp8G75vUd550p5TuXpLr.UlGqVevpt1MTvd9qLA2PKqY7Qm', 'AlliRamos@gmail.com', 'Tanauan City, Batangas', '09201254458', 'Precious Alliyah', 'Marasigan', 'Ramos', 'Grade 11', 'tvl', 'ShangriLa', '', 'student', NULL, NULL),
(70, 'leilei12', '$2y$10$ekwGsASjNRCUIHipql4q7Oo53uJnwd0rcA4N2pPz8pLfkcQqdsxRy', 'leilei12@gmail.com', 'Aya Talisay, Batangas', '09504450122', 'Leila', 'Enriquez', 'Macasinag', 'Grade 11', 'tvl', 'ShangriLa', '', 'student', NULL, NULL),
(71, 'zebmonica', '$2y$10$9dAX.dbN4pc8d3WLZi1gbepL3jsiT6UJ4WJN7Jx.OOF/x50xxMYPW', 'zebmonica14@gmail.com', 'Zone 6 Talisay, Batangas', '09995546385', 'Mnica Kate', 'Amigo', 'Diego', 'Grade 11', 'tvl', 'ShangriLa', '', 'student', NULL, NULL),
(72, 'maemae554', '$2y$10$gca9a0lEXZP1LdftxWzNjeu741vgTSoD053qnkZAGCRozcE1X9/xO', 'Maemae554@gmail.com', 'Banga Talisay, Batangas', '09195936421', 'Marianne Mae ', 'Marasigan', 'Alvarez', 'Grade 11', 'tvl', 'ShangriLa', '', 'student', NULL, NULL),
(73, 'jhonlloydcasaul1', '$2y$10$EUWX2UAceGrkrxs4Jk18Bunocgh1ONDAYKGvM1k0p1GH0BLq8LDQO', 'jhonlloydcasaul1@gmail.com', 'Zone 1 Talisay, Batangas', '09103384215', 'Jhonlloyd', 'Encina', 'Casaul', 'Grade 12', 'tvl', 'Macchiato', '', 'student', NULL, NULL),
(74, 'jusmendoza43', '$2y$10$gYni0Q2T0OFjTBiCwruQrO.7j2JTxiUA3TR56hNrvGB0.NRqS0AXi', 'jusmendoza42@gmail.com', 'Quiling Talisay, Batangas', '09324416875', 'Justine', 'Cacao', 'Mendoza', 'Grade 12', 'tvl', 'Macchiato', '', 'student', NULL, NULL),
(75, 'jramillo11', '$2y$10$UALnXx4fXQBf6dAFJQfr9eV9mxJ9r8HRrJ5dybQc6Hltk8pXPKlGy', 'jramillo11@gmail.com', 'Quiling Talisay, Batangas', '09301886423', 'Johnrey', 'Tenorio', 'Ramillo', 'Grade 12', 'tvl', 'Macchiato', '', 'student', NULL, NULL),
(76, 'joshmvp123', '$2y$10$izKOh4EZoEixu2bOc32DeupdcdQnXLBqnJ877kb19d9lIWSXmCoKu', 'joshmvp123@gmail.com', 'Aya Talisay, Batangas', '09296397215', 'Josh', 'Barrion', 'Centeno', 'Grade 12', 'tvl', 'Macchiato', '', 'student', NULL, NULL),
(77, 'RonJames44', '$2y$10$WaldwfnvqqDn2TMmGQuMou22slBrdAQqVRhaytWOFpEHrTaQPh8oy', 'RonJames44@gmail.com', 'Sta. Maria Talisay, Batangas', '09294458775', 'Ron James', 'Gil', 'Barrion', 'Grade 12', 'tvl', 'Macchiato', '', 'student', NULL, NULL),
(78, 'squidward', '$2y$10$xr3uSShC32ioWbiSq8y65.5fFXdeIeIaci99qwvejQ7Z6jDGiGTza', 'squidwardtentacles@gmail.com', 'Bikini Bottom', '09452325481', 'Squidward', 'Many', 'Tentacles', '', 'abm', '', '', 'teacher', NULL, NULL),
(79, 'eugenekrabs', '$2y$10$KRDvnmvgp8ipXOJ78DRAzuLHX9S7j9FXooBFcZXBh.EnV9C6jiMfK', 'eugenekrabs@gmail.com', 'Bikini Bottom', '09884512575', 'Eugene', 'Mr', 'Krabs', '', 'humss', '', '', 'teacher', NULL, NULL);

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
(11, 31, '380293503_304768402179479_6674034346742619703_n.jpg', 'recent'),
(12, 30, 'pat.jpg', 'recent'),
(13, 29, 'carl.jpg', 'recent'),
(14, 33, 'patrick.jpg', 'recent'),
(15, 32, 'spongebob.jpg', 'recent'),
(16, 37, 'marco.jpg', 'recent'),
(17, 38, 'martino.jpg', 'recent'),
(18, 36, 'rj.jpg', 'recent'),
(19, 39, '301910_286560318029842_1109225643_n.jpg', 'old'),
(20, 39, '20257948_1604707099548971_5136766947339151974_n.jpg', 'recent');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignmentgrade`
--
ALTER TABLE `assignmentgrade`
  ADD PRIMARY KEY (`assignmentGrade_id`);

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
-- Indexes for table `classwork_exam`
--
ALTER TABLE `classwork_exam`
  ADD PRIMARY KEY (`exam_id`);

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
-- Indexes for table `classwork_quiz`
--
ALTER TABLE `classwork_quiz`
  ADD PRIMARY KEY (`quiz_id`);

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
-- Indexes for table `examgrade`
--
ALTER TABLE `examgrade`
  ADD PRIMARY KEY (`examGrade_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `friend`
--
ALTER TABLE `friend`
  ADD PRIMARY KEY (`primary_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `questiongrade`
--
ALTER TABLE `questiongrade`
  ADD PRIMARY KEY (`questionGrade_id`);

--
-- Indexes for table `quizgrade`
--
ALTER TABLE `quizgrade`
  ADD PRIMARY KEY (`quizGrade_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`primary_id`);

--
-- Indexes for table `student_assignment_course_answer`
--
ALTER TABLE `student_assignment_course_answer`
  ADD PRIMARY KEY (`answer_assignment_id`);

--
-- Indexes for table `student_exam_course_answer`
--
ALTER TABLE `student_exam_course_answer`
  ADD PRIMARY KEY (`answer_exam_id`);

--
-- Indexes for table `student_question_course_answer`
--
ALTER TABLE `student_question_course_answer`
  ADD PRIMARY KEY (`answer_question_id`);

--
-- Indexes for table `student_quiz_course_answer`
--
ALTER TABLE `student_quiz_course_answer`
  ADD PRIMARY KEY (`answer_quiz_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`primary_id`);

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
-- AUTO_INCREMENT for table `assignmentgrade`
--
ALTER TABLE `assignmentgrade`
  MODIFY `assignmentGrade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `assignment_course_upload`
--
ALTER TABLE `assignment_course_upload`
  MODIFY `assignment_course_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `classwork_assignment`
--
ALTER TABLE `classwork_assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `classwork_assignment_upload`
--
ALTER TABLE `classwork_assignment_upload`
  MODIFY `assignment_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `classwork_exam`
--
ALTER TABLE `classwork_exam`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classwork_material`
--
ALTER TABLE `classwork_material`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `classwork_material_upload`
--
ALTER TABLE `classwork_material_upload`
  MODIFY `material_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `classwork_question`
--
ALTER TABLE `classwork_question`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `classwork_question_upload`
--
ALTER TABLE `classwork_question_upload`
  MODIFY `question_upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `classwork_quiz`
--
ALTER TABLE `classwork_quiz`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `class_enrolled`
--
ALTER TABLE `class_enrolled`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `class_theme`
--
ALTER TABLE `class_theme`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `examgrade`
--
ALTER TABLE `examgrade`
  MODIFY `examGrade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `friend`
--
ALTER TABLE `friend`
  MODIFY `primary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `questiongrade`
--
ALTER TABLE `questiongrade`
  MODIFY `questionGrade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `quizgrade`
--
ALTER TABLE `quizgrade`
  MODIFY `quizGrade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `primary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_assignment_course_answer`
--
ALTER TABLE `student_assignment_course_answer`
  MODIFY `answer_assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `student_exam_course_answer`
--
ALTER TABLE `student_exam_course_answer`
  MODIFY `answer_exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_question_course_answer`
--
ALTER TABLE `student_question_course_answer`
  MODIFY `answer_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `student_quiz_course_answer`
--
ALTER TABLE `student_quiz_course_answer`
  MODIFY `answer_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `primary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `topic`
--
ALTER TABLE `topic`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
