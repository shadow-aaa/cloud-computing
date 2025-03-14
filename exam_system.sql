-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2025-03-14 11:49:12
-- 服务器版本： 5.7.26
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `exam_system`
--

-- --------------------------------------------------------

--
-- 表的结构 `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `classes`
--

INSERT INTO `classes` (`id`, `name`, `teacher_id`) VALUES
(11, '计算机3班', 21),
(10, '数学1班', 4);

-- --------------------------------------------------------

--
-- 表的结构 `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `exams`
--

INSERT INTO `exams` (`id`, `name`, `class_id`, `start_time`, `end_time`) VALUES
(53, '期末模拟1', 11, '2024-12-25 14:54:00', '2024-12-25 14:54:00'),
(52, '期末考试', 10, '2024-12-23 13:08:00', '2024-12-23 14:08:00'),
(51, '第二次测验', 10, '2024-12-22 16:13:00', '2024-12-22 17:13:00'),
(50, '第一次测验', 10, '2024-12-22 15:58:00', '2024-12-22 16:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `exam_classes`
--

CREATE TABLE `exam_classes` (
  `exam_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `exam_classes`
--

INSERT INTO `exam_classes` (`exam_id`, `class_id`) VALUES
(0, 1),
(0, 4),
(0, 5),
(0, 1),
(0, 4),
(0, 5);

-- --------------------------------------------------------

--
-- 表的结构 `exam_questions`
--

CREATE TABLE `exam_questions` (
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `exam_questions`
--

INSERT INTO `exam_questions` (`exam_id`, `question_id`) VALUES
(1, 2),
(2, 3),
(3, 2),
(3, 3),
(4, 5),
(5, 5),
(6, 2),
(6, 3),
(6, 5),
(7, 2),
(7, 3),
(7, 5),
(8, 2),
(8, 3),
(8, 5),
(9, 2),
(9, 3),
(9, 5),
(10, 2),
(10, 3),
(10, 5),
(11, 2),
(11, 3),
(11, 5),
(12, 2),
(12, 3),
(12, 5),
(13, 2),
(13, 3),
(13, 5),
(14, 2),
(14, 3),
(14, 5),
(15, 2),
(15, 3),
(15, 5),
(16, 2),
(16, 3),
(16, 5),
(16, 6),
(16, 7),
(16, 8),
(17, 2),
(17, 3),
(18, 2),
(18, 3),
(19, 2),
(19, 3),
(19, 5),
(19, 6),
(19, 7),
(20, 2),
(20, 3),
(20, 5),
(20, 6),
(20, 7),
(21, 2),
(21, 3),
(21, 5),
(21, 6),
(21, 7),
(22, 2),
(22, 3),
(22, 5),
(22, 6),
(22, 7),
(23, 2),
(24, 11),
(25, 11),
(26, 11),
(27, 11),
(28, 11),
(29, 11),
(30, 11),
(31, 2),
(32, 5),
(33, 2),
(33, 3),
(33, 5),
(33, 6),
(33, 10),
(34, 2),
(34, 3),
(34, 5),
(34, 6),
(34, 10),
(35, 2),
(36, 5),
(37, 5),
(38, 2),
(38, 3),
(38, 5),
(38, 6),
(38, 10),
(39, 2),
(39, 3),
(39, 5),
(39, 6),
(39, 10),
(40, 2),
(40, 3),
(40, 5),
(40, 6),
(40, 10),
(41, 2),
(41, 3),
(41, 5),
(41, 6),
(41, 10),
(42, 2),
(42, 3),
(43, 12),
(44, 2),
(44, 12),
(45, 12),
(45, 13),
(46, 5),
(46, 6),
(47, 12),
(48, 2),
(48, 6),
(49, 2),
(49, 10),
(50, 2),
(50, 3),
(51, 2),
(51, 3),
(51, 5),
(51, 6),
(51, 10),
(52, 5),
(52, 6),
(52, 14),
(52, 15),
(52, 16),
(53, 2),
(53, 3),
(53, 5),
(53, 6),
(53, 10),
(53, 12);

-- --------------------------------------------------------

--
-- 表的结构 `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('single','multiple','blank','true_false','short_answer') COLLATE utf8_unicode_ci NOT NULL,
  `answer` text COLLATE utf8_unicode_ci,
  `points` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `questions`
--

INSERT INTO `questions` (`id`, `text`, `type`, `answer`, `points`) VALUES
(2, '单选题', 'single', 'A', 10),
(3, '填空题2+2 = ？', 'blank', '4', 10),
(5, '简答题', 'short_answer', '', 10),
(6, '判断题', 'true_false', 'true', 10),
(10, '多选题', 'multiple', 'A,B', 10),
(12, '如果a=1,那么b=?', 'short_answer', '', 100),
(14, '新中国成立时间是', 'single', 'D', 3),
(15, '30 * 5 =', 'blank', '150', 3),
(16, '若x^2=1 ，则x =', 'multiple', 'A,B', 4);

-- --------------------------------------------------------

--
-- 表的结构 `student_classes`
--

CREATE TABLE `student_classes` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `student_classes`
--

INSERT INTO `student_classes` (`id`, `student_id`, `class_id`) VALUES
(1, 3, 1),
(2, 3, 2),
(3, 5, 1),
(4, 6, 1),
(5, 7, 1),
(6, 8, 1),
(11, 6, 10),
(12, 5, 10),
(10, 5, 2),
(13, 22, 11);

-- --------------------------------------------------------

--
-- 表的结构 `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text COLLATE utf8_unicode_ci,
  `score` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `submissions`
--

INSERT INTO `submissions` (`id`, `student_id`, `exam_id`, `question_id`, `answer`, `score`) VALUES
(49, 5, 52, 6, 'true', 10),
(50, 5, 52, 14, 'D', 3),
(51, 5, 52, 15, '150', 3),
(46, 5, 50, 2, 'A', 10),
(47, 5, 50, 3, '4', 10),
(48, 5, 52, 5, '123', 10),
(52, 5, 52, 16, 'A,B', 4);

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` enum('student','teacher') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(5, '666', '$2y$10$Pdrcv277AkkHKnU4JJsCb.w8N5UO7Ae4GDUMibLIbsMDOWgNzX5Wi', 'student'),
(4, '123', '$2y$10$yLJLzIuC32labCS0RwpkQO288kQBac.Bc6.RLl.Nf7T9OW6Tc4vVW', 'teacher'),
(6, '111', '$2y$10$jlPQ9iYIO/jCJXLDxfgdE.cI6bwpLsdDz1GuRGcYu/MHBZ6MT8JdC', 'student'),
(21, '2333', '$2y$10$mssTKMqr3svVCZ.I9XfGZeJFe3wMcsYhShAm3hxg0UdfQepGu6oAC', 'teacher'),
(22, '1111', '$2y$10$ovmRef3LmzRiUabx/gDKCOfKEbLqrWDg.edAGHFlxB4t57q3Y4Lg2', 'student');

--
-- 转储表的索引
--

--
-- 表的索引 `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- 表的索引 `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- 表的索引 `exam_classes`
--
ALTER TABLE `exam_classes`
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `class_id` (`class_id`);

--
-- 表的索引 `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `question_id` (`question_id`);

--
-- 表的索引 `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `student_classes`
--
ALTER TABLE `student_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- 表的索引 `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `question_id` (`question_id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用表AUTO_INCREMENT `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- 使用表AUTO_INCREMENT `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- 使用表AUTO_INCREMENT `student_classes`
--
ALTER TABLE `student_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
