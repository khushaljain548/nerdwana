-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2024 at 06:38 PM
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
-- Database: `comun`
--

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE `forums` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forums`
--

INSERT INTO `forums` (`id`, `name`) VALUES
(1, 'Mobile Tech'),
(2, 'AI Development'),
(3, 'Everything Else');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `forum_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `postTitle` varchar(255) NOT NULL,
  `reply_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `username`, `content`, `created_at`, `forum_id`, `topic_id`, `postTitle`, `reply_count`) VALUES
(98, 'Khushal548', '<pre class=\"ql-syntax\" spellcheck=\"false\">&nbsp; .delete-account-button {\n&nbsp; &nbsp; font-family: \'Nothingnormal\', sans-serif;\n&nbsp; &nbsp; font-size: 18px;\n&nbsp; &nbsp; letter-spacing: 1px;\n&nbsp; &nbsp; background-color: #d71a21;\n&nbsp; &nbsp; color: white;\n&nbsp; &nbsp; border: 1px dotted #d71a21 !important;\n&nbsp; &nbsp; padding: 10px 20px;\n&nbsp; &nbsp; border-radius: 50px;\n&nbsp; &nbsp; cursor: pointer;\n&nbsp; &nbsp; transition: background-color 0.3s, color 0.3s;\n&nbsp; &nbsp; margin-top: 20px; /* Add some space above the button */\n}\n</pre><p><br></p><p><span style=\"color: rgb(212, 212, 212);\">this is how it should work</span></p>', '2024-07-05 17:23:06', 1, 1, 'Mt bew pist', 0),
(101, 'Test548', '<h1>My ‘Golden Hour’ with the Samsung Galaxy S24 Ultra</h1><h2>The Auriferous Marvel</h2><p>When Samsung invited me to explore their new Galaxy S24 lineup, one particular phone instantly caught my eye: the&nbsp;<strong>Titanium Yellow</strong>&nbsp;Samsung Galaxy S24 Ultra.&nbsp;<a href=\"https://mashable.com/article/samsung-galaxy-s24-ultra-hands-on-review\" target=\"_blank\" style=\"color: inherit;\">Its golden-esque hue reminded me of Tchaikovsky’s “Romeo and Juliet,” and I couldn’t take my eyes off it</a><a href=\"https://mashable.com/article/samsung-galaxy-s24-ultra-hands-on-review\" target=\"_blank\" style=\"color: var(--cib-color-foreground-accent-primary); background-color: transparent;\">1</a>.</p><h2>A Game-Changer in Feel and Features</h2><p>Having used the iPhone 15 Pro Max for months, I didn’t realize how unergonomic it was until I held the Galaxy S24 Ultra. The Titanium Yellow model felt like it was made for my hand.&nbsp;<a href=\"https://mashable.com/article/samsung-galaxy-s24-ultra-hands-on-review\" target=\"_blank\" style=\"color: inherit;\">Its design not only looks stunning but also feels comfortable, making it a potential daily driver for me</a><a href=\"https://mashable.com/article/samsung-galaxy-s24-ultra-hands-on-review\" target=\"_blank\" style=\"color: var(--cib-color-foreground-accent-primary); background-color: transparent;\">1</a>.</p><h2>Titanium Magic</h2><p>Aside from its attractive colorways (including Titanium Black, Titanium Violet, and Titanium Gray), the S24 Ultra boasts impressive features. It has a quadruple-camera layout, a hole-punch display, and even an S Pen for note-taking.&nbsp;<a href=\"https://mashable.com/article/samsung-galaxy-s24-ultra-hands-on-review\" target=\"_blank\" style=\"color: inherit;\">Plus, the AI enhancements keep getting better, making this phone a true powerhouse</a><a href=\"https://www.techradar.com/phones/samsung-galaxy-phones/samsung-galaxy-s24-ultra-review\" target=\"_blank\" style=\"color: var(--cib-color-foreground-accent-primary); background-color: transparent;\">2</a><a href=\"https://www.howtogeek.com/samsung-galaxy-s24-ultra-review/\" target=\"_blank\" style=\"color: var(--cib-color-foreground-accent-primary); background-color: transparent;\">3</a>.</p><h2>Conclusion</h2><p>The Galaxy S24 Ultra transcends the smartphone category. It offers better battery life, faster performance, and top-notch cameras. Yes, the menus could use some improvement, but when you witness the Ultra in action, it’s worth every penny. As for me, I’m already planning my next trip with this golden marvel in hand! 🌟📱✨</p><p><em>Have you tried the Galaxy S24 Ultra? Share your thoughts below!</em></p>', '2024-07-29 15:28:32', 3, 5, 'My trip with Samsung  S24 Ultra', 0);

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `reply_content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `forum_id`, `name`) VALUES
(1, 1, 'Android Devices'),
(2, 1, 'iOS Devices'),
(3, 2, 'Machine Learning'),
(4, 2, 'AI Applications'),
(5, 3, 'Blogs'),
(6, 3, 'Co-Creation');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(3, 'Khushal548', 'khushaljain618@gmail.com', 'Tata@548'),
(4, 'pushpa548', 'pushpa618@gmail.com', 'Tata@548'),
(6, 'khushaltata', 'tata@docomo548.com', 'Tata@548'),
(7, 'phakir', 'phakir@phakir.com', 'Phair@548'),
(10, 'Test548', 'Test@gmail.com', 'Tata@548'),
(11, 'KarnikaAwasthi', 'reshu@gmail.com', 'Tata@548');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_id` (`forum_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `forums`
--
ALTER TABLE `forums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `fk_replies_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
