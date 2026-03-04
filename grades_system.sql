-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 03:51 PM
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
-- Database: `grades_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `action_time`) VALUES
(1, 8, 'User logged in', '2026-02-25 12:06:16'),
(2, 8, 'User logged in', '2026-02-25 12:34:38'),
(3, 8, 'Submitted enrollment request for subject 2', '2026-02-25 12:36:45'),
(4, 8, 'Submitted enrollment request for subject 5', '2026-02-25 12:36:49'),
(5, 8, 'Submitted enrollment request for subject 4', '2026-02-25 12:36:54'),
(6, 8, 'Submitted enrollment request for subject 6', '2026-02-25 12:36:56'),
(7, 8, 'Submitted enrollment request for subject 1', '2026-02-25 12:36:59'),
(8, 8, 'Submitted enrollment request for subject 3', '2026-02-25 12:37:01'),
(9, 1, 'User logged in', '2026-02-25 12:41:03'),
(10, 7, 'User logged in', '2026-02-25 12:41:29'),
(11, 8, 'User logged in', '2026-02-25 12:44:11'),
(12, 1, 'User logged in', '2026-02-25 12:53:45'),
(13, 1, 'User logged in', '2026-02-25 12:54:40'),
(14, 7, 'User logged in', '2026-02-25 12:55:06'),
(15, 7, 'User logged in', '2026-02-25 13:07:52'),
(16, 7, 'User logged in', '2026-02-25 13:14:45'),
(17, 8, 'User logged in', '2026-02-25 13:28:22'),
(18, 7, 'User logged in', '2026-02-25 13:46:32'),
(19, 7, 'User logged in', '2026-02-26 09:20:39'),
(20, 7, 'User logged in', '2026-02-26 09:39:23'),
(21, 7, 'User logged in', '2026-02-26 09:54:26'),
(22, 7, 'Enrollment Approved: request 1 student 8 subject 2', '2026-02-26 09:54:28'),
(23, 7, 'Enrollment Approved: request 3 student 8 subject 4', '2026-02-26 09:54:59'),
(24, 7, 'Enrollment Approved: request 4 student 8 subject 6', '2026-02-26 09:55:01'),
(25, 7, 'Enrollment Approved: request 6 student 8 subject 3', '2026-02-26 09:55:04'),
(26, 7, 'User logged in', '2026-02-26 09:55:21'),
(27, 7, 'Enrollment Approved: request 2 student 8 subject 5', '2026-02-26 09:55:22'),
(28, 7, 'Enrollment Approved: request 5 student 8 subject 1', '2026-02-26 09:55:24'),
(29, 8, 'User logged in', '2026-02-26 09:55:40'),
(30, 8, 'User logged in', '2026-02-26 09:57:22'),
(31, 8, 'Viewed Student Report Card', '2026-02-26 09:57:44'),
(32, 7, 'User logged in', '2026-02-26 09:58:22'),
(33, 1, 'User logged in', '2026-02-26 09:58:55'),
(34, 1, 'Encoded grade', '2026-02-26 09:59:18'),
(35, 2, 'User logged in', '2026-02-26 09:59:54'),
(36, 2, 'Encoded grade', '2026-02-26 10:00:01'),
(37, 3, 'User logged in', '2026-02-26 10:00:14'),
(38, 3, 'Encoded grade', '2026-02-26 10:00:19'),
(39, 4, 'User logged in', '2026-02-26 10:00:30'),
(40, 4, 'Encoded grade', '2026-02-26 10:00:36'),
(41, 5, 'User logged in', '2026-02-26 10:00:46'),
(42, 5, 'Encoded grade', '2026-02-26 10:00:51'),
(43, 6, 'User logged in', '2026-02-26 10:01:00'),
(44, 6, 'Encoded grade', '2026-02-26 10:01:05'),
(45, 7, 'User logged in', '2026-02-26 10:01:32'),
(46, 7, 'Approved grade ID 1', '2026-02-26 10:07:34'),
(47, 7, 'Approved grade ID 2', '2026-02-26 10:07:40'),
(48, 8, 'User logged in', '2026-02-26 10:07:51'),
(49, 8, 'User logged in', '2026-02-26 10:12:28'),
(50, 1, 'User logged in', '2026-02-26 10:13:03'),
(51, 1, 'Viewed Faculty Grade Sheets', '2026-02-26 10:13:26'),
(52, 7, 'User logged in', '2026-02-26 10:14:35'),
(53, 7, 'Viewed Registrar Student Record for 1', '2026-02-26 10:15:38'),
(54, 7, 'Viewed Registrar Student Record for 2', '2026-02-26 10:15:49'),
(55, 7, 'Viewed Registrar Student Record for 8', '2026-02-26 10:15:53'),
(56, 7, 'Registrar exported student record PDF for student 8', '2026-02-26 10:16:01'),
(57, 7, 'User logged in', '2026-02-26 10:23:48'),
(58, 7, 'Viewed Master Enrollment & Grade List', '2026-02-26 10:23:59'),
(59, 7, 'Viewed Master Enrollment & Grade List', '2026-02-26 10:24:06'),
(60, 7, 'Viewed Master Enrollment & Grade List', '2026-02-26 10:24:41'),
(61, 7, 'Viewed Master Enrollment & Grade List', '2026-02-26 10:25:01'),
(62, 7, 'Viewed Master Enrollment & Grade List', '2026-02-26 10:25:44'),
(63, 7, 'User logged in', '2026-03-02 12:22:37'),
(64, 7, 'User logged in', '2026-03-02 13:37:02'),
(65, 1, 'User logged in', '2026-03-02 13:39:09'),
(66, 8, 'User logged in', '2026-03-02 13:40:05'),
(67, 8, 'User logged in', '2026-03-02 13:59:28'),
(68, 8, 'User logged in', '2026-03-02 14:43:55'),
(69, 2, 'User logged in', '2026-03-02 14:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `subject_id`, `semester_id`) VALUES
(2, 8, 2, 2),
(3, 8, 4, 2),
(4, 8, 6, 2),
(5, 8, 3, 2),
(6, 8, 5, 2),
(7, 8, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_requests`
--

CREATE TABLE `enrollment_requests` (
  `request_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `decision_date` timestamp NULL DEFAULT NULL,
  `registrar_id` int(11) DEFAULT NULL,
  `decision_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment_requests`
--

INSERT INTO `enrollment_requests` (`request_id`, `student_id`, `subject_id`, `status`, `request_date`, `decision_date`, `registrar_id`, `decision_notes`) VALUES
(1, 8, 2, 'Approved', '2026-02-25 12:36:45', '2026-02-26 09:54:28', 7, ''),
(2, 8, 5, 'Approved', '2026-02-25 12:36:49', '2026-02-26 09:55:22', 7, ''),
(3, 8, 4, 'Approved', '2026-02-25 12:36:54', '2026-02-26 09:54:58', 7, ''),
(4, 8, 6, 'Approved', '2026-02-25 12:36:56', '2026-02-26 09:55:01', 7, ''),
(5, 8, 1, 'Approved', '2026-02-25 12:36:57', '2026-02-26 09:55:24', 7, ''),
(6, 8, 3, 'Approved', '2026-02-25 12:37:01', '2026-02-26 09:55:04', 7, '');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `numeric_grade` decimal(3,2) NOT NULL,
  `remarks` varchar(20) NOT NULL,
  `status` enum('Pending','Returned','Approved') DEFAULT 'Pending',
  `is_locked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `enrollment_id`, `period_id`, `percentage`, `numeric_grade`, `remarks`, `status`, `is_locked`) VALUES
(1, 7, 1, 85.00, 2.25, 'Good', 'Approved', 1),
(2, 2, 1, 85.00, 2.25, 'Good', 'Approved', 1),
(3, 5, 1, 83.00, 2.25, 'Good', 'Pending', 0),
(4, 3, 1, 87.00, 2.00, 'Good', 'Pending', 0),
(5, 6, 1, 88.00, 2.00, 'Good', 'Pending', 0),
(6, 4, 1, 85.00, 2.25, 'Good', 'Pending', 0);

-- --------------------------------------------------------

--
-- Table structure for table `grade_corrections`
--

CREATE TABLE `grade_corrections` (
  `request_id` int(11) NOT NULL,
  `grade_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `registrar_id` int(11) DEFAULT NULL,
  `decision_notes` text DEFAULT NULL,
  `decision_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grading_periods`
--

CREATE TABLE `grading_periods` (
  `period_id` int(11) NOT NULL,
  `period_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grading_periods`
--

INSERT INTO `grading_periods` (`period_id`, `period_name`) VALUES
(1, 'Prelim'),
(2, 'Midterm'),
(3, 'Finals');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'New enrollment approved: student 8 for subject 2', 0, '2026-02-26 09:54:28'),
(2, 8, 'Your enrollment request #1 was approved.', 0, '2026-02-26 09:54:28'),
(3, 4, 'New enrollment approved: student 8 for subject 4', 0, '2026-02-26 09:54:59'),
(4, 8, 'Your enrollment request #3 was approved.', 0, '2026-02-26 09:54:59'),
(5, 6, 'New enrollment approved: student 8 for subject 6', 0, '2026-02-26 09:55:01'),
(6, 8, 'Your enrollment request #4 was approved.', 0, '2026-02-26 09:55:01'),
(7, 3, 'New enrollment approved: student 8 for subject 3', 0, '2026-02-26 09:55:04'),
(8, 8, 'Your enrollment request #6 was approved.', 0, '2026-02-26 09:55:04'),
(9, 5, 'New enrollment approved: student 8 for subject 5', 0, '2026-02-26 09:55:22'),
(10, 8, 'Your enrollment request #2 was approved.', 0, '2026-02-26 09:55:22'),
(11, 1, 'New enrollment approved: student 8 for subject 1', 0, '2026-02-26 09:55:24'),
(12, 8, 'Your enrollment request #5 was approved.', 0, '2026-02-26 09:55:24'),
(13, 1, 'Grade ID 1 was approved and locked.', 0, '2026-02-26 10:07:34'),
(14, 8, 'A grade for you was approved by the Registrar.', 0, '2026-02-26 10:07:35'),
(15, 2, 'Grade ID 2 was approved and locked.', 0, '2026-02-26 10:07:40'),
(16, 8, 'A grade for you was approved by the Registrar.', 0, '2026-02-26 10:07:41');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(4, 'Admin'),
(1, 'Faculty'),
(2, 'Registrar'),
(3, 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL,
  `semester_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`semester_id`, `semester_name`) VALUES
(1, 'First Semester'),
(2, 'Second Semester');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `faculty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `faculty_id`) VALUES
(1, 'SP101', 'Social and Professional Issues', 1),
(2, 'IAS102', 'Information Assurance and Security 2', 2),
(3, 'TEC101', 'Technopreneurship', 3),
(4, 'PM101', 'Business Process Management in IT', 4),
(5, 'ITSP2A', 'Mobile Application and Development', 5),
(6, 'SA101', 'System Administration And Maintenance', 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `role_id`, `created_at`) VALUES
(1, 'Jacqueline De Guzman', 'jacqueline.faculty@gmail.com', '$2y$10$oF94e4rJ.3I35Y0y7AubM.j8rXDVk/eDWi0Dr5y23imGK8TXWg4BO', 1, '2026-02-25 11:55:09'),
(2, 'Andrew Delacruz', 'andrew.faculty@gmail.com', '$2y$10$UI9UGiAMDZ/KKGinvalEh.MW7T.XP5quI1CIMxTyEa.O.S54w90QO', 1, '2026-02-25 11:56:58'),
(3, 'Marimel Loya', 'marimel.faculty@gmail.com', '$2y$10$NGfJOjXlAB1kIXLiLbwHO.sOF4aoilu5swLolOhlSbxUL7owBn6gG', 1, '2026-02-25 11:57:51'),
(4, 'Jorge Lucero', 'jorge.faculty@gmail.com', '$2y$10$NeesYvAJWn3mCPJLswzN5uMzgYFF9RnUSJeq2knesEOSkLXJagnr6', 1, '2026-02-25 11:58:57'),
(5, 'Jessa Brogada', 'jessa.faculty@gmail.com', '$2y$10$8Z.9dD4ioG5u0mgqkJ4D8.lIk5LQDZCcKAd7MXOClufqs8Zz8hMTq', 1, '2026-02-25 11:59:35'),
(6, 'Regane Macahibag', 'regane.faculty@gmail.com', '$2y$10$AZJAx4d6CpMdg5/ynSpcPONnxKotoR1Ju6k.1ECwayLHHw33r0x7m', 1, '2026-02-25 12:00:20'),
(7, 'Eva Arce', 'eva.registrar@gmail.com', '$2y$10$1.G8TngCS/DesxJ1C001t.RQaQ/33zfuKF590Act5U0imcIYyh64i', 2, '2026-02-25 12:01:50'),
(8, 'Yuan Amboy', 'yuan.student@gmail.com', '$2y$10$RDwalh4Be87BFTC3TFnvD.ChJVPmBnIecdzqSrSlDYnLjcbKpUPza', 3, '2026-02-25 12:05:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `enrollment_requests`
--
ALTER TABLE `enrollment_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `registrar_id` (`registrar_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `grade_corrections`
--
ALTER TABLE `grade_corrections`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `grade_id` (`grade_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `registrar_id` (`registrar_id`);

--
-- Indexes for table `grading_periods`
--
ALTER TABLE `grading_periods`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`semester_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `enrollment_requests`
--
ALTER TABLE `enrollment_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grade_corrections`
--
ALTER TABLE `grade_corrections`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grading_periods`
--
ALTER TABLE `grading_periods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`);

--
-- Constraints for table `enrollment_requests`
--
ALTER TABLE `enrollment_requests`
  ADD CONSTRAINT `enrollment_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollment_requests_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `enrollment_requests_ibfk_3` FOREIGN KEY (`registrar_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `grading_periods` (`period_id`);

--
-- Constraints for table `grade_corrections`
--
ALTER TABLE `grade_corrections`
  ADD CONSTRAINT `grade_corrections_ibfk_1` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`grade_id`),
  ADD CONSTRAINT `grade_corrections_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `grade_corrections_ibfk_3` FOREIGN KEY (`registrar_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
