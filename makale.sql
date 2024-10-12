-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 12 Eki 2024, 19:40:02
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `makale`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(25, 42, 27, 'Güzel', '2024-08-22 23:59:03'),
(26, 42, 27, 'Beğendim', '2024-08-22 23:59:11'),
(27, 43, 27, 'Beğenmedim', '2024-08-22 23:59:31'),
(28, 44, 16, 'Sadece arkadaşız .', '2024-08-23 00:00:14'),
(29, 43, 17, 'Çok güzel beğendim yaralı oldu.', '2024-08-23 00:08:10'),
(31, 45, 16, 'hrghfg', '2024-08-26 13:07:05'),
(32, 45, 18, 'dsfsd', '2024-08-26 13:09:37');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `comment_likes`
--

INSERT INTO `comment_likes` (`id`, `user_id`, `comment_id`, `created_at`) VALUES
(8, 17, 27, '2024-08-23 00:08:13');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `response` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `evaluations`
--

INSERT INTO `evaluations` (`id`, `post_id`, `question_id`, `reviewer_id`, `response`) VALUES
(36, 43, 16, 17, 'İyi'),
(37, 43, 17, 17, 'Fenerbahçe'),
(38, 42, 18, 18, 'gdfgd'),
(39, 42, 19, 18, 'ghdfgdf'),
(40, 42, 20, 18, 'dgfgd'),
(41, 45, 21, 17, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'),
(42, 45, 22, 17, 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).'),
(43, 45, 23, 17, 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.\r\n\r\nThe standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.'),
(44, 45, 24, 17, 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `evaluation_questions`
--

CREATE TABLE `evaluation_questions` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `evaluation_questions`
--

INSERT INTO `evaluation_questions` (`id`, `post_id`, `question`) VALUES
(16, 43, 'Nasıl ?'),
(17, 43, 'Şampiyon ?'),
(18, 42, 'hrfhf'),
(19, 42, 'fhfhfh'),
(20, 42, 'fhghfh'),
(21, 45, 'Makale nasıldı ?'),
(22, 45, 'Makalenin eksikleri neler ? '),
(23, 45, 'Siz olsanız nasıl yapardınız ?\r\n'),
(24, 45, 'Hangi üniversite mezunusunuz ?'),
(25, 43, 'rthrfgh');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `user_id`, `created_date`) VALUES
(5, 20, 16, '2024-08-20 19:11:32'),
(6, 20, 17, '2024-08-20 19:12:10'),
(7, 19, 17, '2024-08-20 19:12:11'),
(8, 18, 17, '2024-08-20 19:12:12'),
(9, 17, 17, '2024-08-20 19:12:13'),
(11, 22, 16, '2024-08-20 19:39:25'),
(12, 21, 16, '2024-08-20 19:39:26'),
(14, 22, 17, '2024-08-20 19:39:35'),
(37, 22, 18, '2024-08-20 21:27:04'),
(38, 23, 18, '2024-08-20 21:27:09'),
(39, 29, 16, '2024-08-21 14:39:53'),
(40, 30, 16, '2024-08-21 14:47:01'),
(41, 33, 17, '2024-08-21 15:41:58'),
(42, 35, 17, '2024-08-21 15:42:58'),
(44, 34, 27, '2024-08-21 18:36:40'),
(45, 23, 27, '2024-08-21 18:36:41'),
(46, 21, 17, '2024-08-22 11:52:59'),
(92, 23, 17, '2024-08-22 23:00:53'),
(93, 34, 17, '2024-08-22 23:11:12'),
(96, 37, 16, '2024-08-22 23:42:28'),
(97, 38, 17, '2024-08-22 23:47:56'),
(98, 42, 27, '2024-08-22 23:58:55'),
(99, 43, 27, '2024-08-22 23:58:56'),
(101, 45, 16, '2024-08-22 23:59:52'),
(102, 44, 16, '2024-08-23 00:02:18'),
(104, 45, 17, '2024-08-23 00:07:40'),
(105, 44, 17, '2024-08-23 00:07:41'),
(106, 43, 17, '2024-08-23 00:07:42'),
(107, 42, 17, '2024-08-23 00:08:23'),
(108, 39, 17, '2024-08-23 00:08:26'),
(109, 43, 16, '2024-08-23 00:10:41'),
(110, 45, 18, '2024-08-26 13:09:35');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `editor_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `notifications`
--

INSERT INTO `notifications` (`id`, `post_id`, `editor_id`, `message`, `is_read`, `created_at`) VALUES
(72, 43, 17, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-23 00:13:08'),
(73, 43, 29, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-23 00:13:18'),
(74, 43, 30, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-23 00:13:18'),
(75, 43, 16, 'eren Değerlendirmesini tamamladı.', 0, '2024-08-23 00:14:36'),
(76, 43, 16, 'eren Değerlendirmesini tamamladı.', 0, '2024-08-23 00:14:36'),
(77, 42, 17, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-26 12:02:03'),
(78, 42, 18, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-26 12:02:03'),
(79, 42, 16, 'alperDeğerlendirmesini tamamladı.', 0, '2024-08-26 12:04:05'),
(80, 42, 16, 'alperDeğerlendirmesini tamamladı.', 0, '2024-08-26 12:04:05'),
(81, 42, 16, 'alperDeğerlendirmesini tamamladı.', 0, '2024-08-26 12:04:05'),
(82, 45, 17, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-26 12:05:13'),
(83, 45, 18, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-26 12:05:13'),
(84, 45, 27, 'eren Değerlendirmesini tamamladı.', 0, '2024-08-26 12:08:56'),
(85, 45, 27, 'eren Değerlendirmesini tamamladı.', 0, '2024-08-26 12:08:56'),
(86, 45, 27, 'eren Değerlendirmesini tamamladı.', 0, '2024-08-26 12:08:56'),
(87, 45, 27, 'eren Değerlendirmesini tamamladı.', 0, '2024-08-26 12:08:56'),
(88, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-26 12:55:55'),
(89, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-26 12:55:55'),
(90, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-26 12:55:55'),
(91, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-26 12:55:55'),
(92, 43, 32, 'Yeni bir makaleye hakem olarak atandınız.', 0, '2024-08-26 13:08:10'),
(93, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-31 13:03:28'),
(94, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-31 13:03:28'),
(95, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-31 13:03:28'),
(96, 45, 27, 'eren Değerlendirmesini güncelledi.', 0, '2024-08-31 13:03:28');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `file` varchar(255) NOT NULL,
  `createddate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `likes` int(255) NOT NULL,
  `shares` int(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `posts`
--

INSERT INTO `posts` (`id`, `kullanici_id`, `title`, `file`, `createddate`, `likes`, `shares`, `description`) VALUES
(39, 16, 'Sefiller ', 'Sefiller-Victor-Hugo.pdf', '2024-08-22 23:50:02', 0, 0, 'Kitap'),
(42, 16, 'Matlab', 'MATLAB.pdf', '2024-08-22 23:55:14', 0, 0, 'SAYISAL HESAPLAMA DİLİ'),
(43, 16, 'Lorem', 'Lorem.docx', '2024-08-22 23:56:24', 0, 0, 'Yazı'),
(44, 27, 'Tarih', 'Tarih.docx', '2024-08-22 23:57:47', 0, 0, 'Yazı'),
(45, 27, 'Verimlilik ve Etkinlik', 'Verimlilik-ve-etkinlik.pdf', '2024-08-22 23:58:50', 0, 0, 'Endüstri');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `post_reviewers`
--

CREATE TABLE `post_reviewers` (
  `post_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `post_reviewers`
--

INSERT INTO `post_reviewers` (`post_id`, `reviewer_id`) VALUES
(42, 17),
(42, 18),
(43, 17),
(43, 29),
(43, 30),
(43, 32),
(45, 17),
(45, 18);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(250) NOT NULL,
  `gender` int(1) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `yetki` int(1) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `fullname`, `gender`, `email`, `password`, `yetki`, `createdate`, `photo`) VALUES
(16, 'Eren Karabıyık', 'Eren Karabıyık', 0, 'mertluviana@gmail.com', '$2y$10$iroYw7Os8kbzBLtncWCuLekGwQQROF6/y4of7cK3TsycBZE3Y16W.', 0, '2024-08-20 17:41:21', '66c7aca33b97f.png'),
(17, 'eren ', 'Eren krbyk', 0, 'mertluviana@gmail.com', '$2y$10$SHoydvR3seej1KtyUmAMpO.NeQS9O2BHBIy5LbFFEN8Ar45UJPVJW', 1, '2024-08-20 19:11:59', '66c7268916d3c.png'),
(18, 'alper', 'alper bilen', 0, 'kral19057@hotmail.com', '$2y$10$0axKZsaITZgnO7.5KDpEsuT/hH.I4/HWyjE59cUOBxzxRuuxd2pK.', 1, '2024-08-20 21:17:33', ''),
(27, 'mehmet', 'mehmet arı', 0, 'erayjsx@gmail.com', '$2y$10$Zw0RBbamShjNwXEsM3Ccc.hUi/G1e5u5hr3KUifVeqTS7wrhObbBu', 0, '2024-08-21 18:23:43', '66c7b22bc683d.png'),
(28, 'ali', 'ali kara', 0, '220102068@ogrenci.karatekin.edu.tr', '$2y$10$9bP1FCezW9JBiIVI1WqkTuBH9LXrUeNLCHDMTYqtNMrCEy0u9HFDG', 0, '2024-08-21 18:34:06', '66c6331e4d0f5.png'),
(29, 'ayşe', 'ayşe kör', 1, 'krbykeren13@gmail.com', '$2y$10$YZdPRp2mOjipNDxufAnUd.Xmw5U4YKje7XIBoIsfNQkYjXgGMmWEW', 1, '2024-08-21 21:19:51', '66c659f7abedd.png'),
(30, 'Göktürk', 'hasan', 0, '220102068@ogrenci.karatekin.edu.tr', '$2y$10$fcpZ6EtH0X.R02LCA374VO03p0LtvytvqLFEG7IsrdC0.JQ11O6KS', 1, '2024-08-21 22:17:51', '66c6678f39382.png'),
(31, 'hasan', 'ali karaaa', 0, 'krbykeren13@gmail.com', '$2y$10$s9TBMAodNF3Gi/BSmD216.cPqq1.iIHf5me1mQHgPvRVRqz41Buly', 1, '2024-08-21 22:18:12', '66c667a45317b.png'),
(32, 'jale', 'mehmet arıww', 1, 'erayjsx@gmail.com', '$2y$10$gauL40Ofm8z2W62b2y5nTu9AXVy76ZbHQWUnxuu.DKuZdd2s8USi6', 1, '2024-08-21 22:18:33', '66c667b930567.png'),
(33, 'viale_store', 'hasanasd', 1, 'erayjsx@gmail.com', '$2y$10$0BSBvwvLMrUdWMR2z5gM7ejYpHokzDwaFkpxLGBE3l4Je.IopX99S', 1, '2024-08-21 22:19:01', '66c667d52adf8.png'),
(34, 'popo', 'popc', 1, 'erayjsx@gmail.com', '$2y$10$yXcj4u66FT6ClO8KyULOqOuJhLq1YCSxWcvfg/YMv1BiXgqw6mtjS', 1, '2024-08-21 22:19:30', '66c667f293868.png'),
(35, 'gakas', 'dsvf', 0, 'kral19057@hotmail.com', '$2y$10$H.w3vOkn8n.CNyK0X3BCEu.m0hiSGFziMgrbLPibBYVbTSC5.5d.a', 1, '2024-08-21 22:19:57', '66c6680d7ed4d.png'),
(36, 'kötü', 'kmsa', 0, 'krbykeren13@gmail.com', '$2y$10$.L/5KElz30bUytSSW5tTceMr1oYykK4h3cckDLfimrTJpLLv1Yl.C', 1, '2024-08-21 22:20:28', '66c6682c9ffa6.png'),
(37, 'baba', 'ali karaaaaaa', 0, 'krbykeren13@gmail.com', '$2y$10$n5NCwnd1APZeuQ8APkaq4OTdCasaAKxybNje6lOTuQzYsLD.4cS7S', 1, '2024-08-21 22:21:06', '66c668529ec09.png'),
(38, 'kel oğlan', 'hasan hs', 0, 'kral19057@hotmail.com', '$2y$10$2La0SI1gQ9KyhWNihY/S6etDup7iUEmglXB65PxdtWIARRfLzqBYm', 1, '2024-08-21 22:21:51', '66c6687f4dcc2.png'),
(39, 'muzo', 'azgın dayı', 0, 'krbykeren13@gmail.com', '$2y$10$UwRXOWOu/fu2SyxH8N35cuBf8p5q4lmVlnOHyAV1.T5ZlA21TWw62', 1, '2024-08-22 12:44:04', '66c73294a207e.png'),
(40, 'kral ', 'kralın ', 0, 'mertluviana@gmail.com', '$2y$10$jLD4XPLq30jyiIJgnL76eO4FvyX05eS0LBFMHdIC956cJrh6k55.u', 1, '2024-08-22 20:59:33', '66c7a6b5a7011.png');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Tablo için indeksler `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Tablo için indeksler `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Tablo için indeksler `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `editor_id` (`editor_id`);

--
-- Tablo için indeksler `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `post_reviewers`
--
ALTER TABLE `post_reviewers`
  ADD PRIMARY KEY (`post_id`,`reviewer_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Tablo için AUTO_INCREMENT değeri `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Tablo için AUTO_INCREMENT değeri `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Tablo için AUTO_INCREMENT değeri `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- Tablo için AUTO_INCREMENT değeri `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `comment_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_likes_ibfk_2` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `evaluation_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_3` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  ADD CONSTRAINT `evaluation_questions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `post_reviewers`
--
ALTER TABLE `post_reviewers`
  ADD CONSTRAINT `post_reviewers_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_reviewers_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
