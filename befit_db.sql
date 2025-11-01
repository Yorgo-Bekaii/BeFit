-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2025 at 02:54 AM
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
-- Database: `befit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` enum('equipment','supplement') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category`, `created_at`) VALUES
(1, 'FitRx Smart Adjustable Dumbbells - 1kg to 20kg', 'Adjustable dumbbells with smart tracking', 124.99, 'photos/dumbell.jpeg', 'equipment', '2025-07-11 22:47:39'),
(2, 'Resistance Band Set 1-5kg', 'Set of resistance bands for full-body workouts', 24.99, 'photos/resistance.jpg', 'equipment', '2025-07-11 22:47:39'),
(3, '10 mm lever-action belt for weightlifting', 'Premium weightlifting belt for support', 59.99, 'photos/belt.jpg', 'equipment', '2025-07-11 22:47:39'),
(4, 'Portable Doorway Pull-up Bar', 'Portable pull-up bar for home workouts', 44.99, 'photos/pullup.jpg', 'equipment', '2025-07-11 22:47:39'),
(5, '2kg Kevin Levrone - Gold Whey Protein', 'Premium whey protein for muscle recovery', 64.99, 'photos/prot.webp', 'supplement', '2025-07-11 22:47:39'),
(6, '500g Kevin Levrone - Gold Creatine Monohydrate', 'Pure creatine monohydrate for strength gains', 34.99, 'photos/creatine.jpg', 'supplement', '2025-07-11 22:47:39'),
(7, '500g Kevin Levrone - Gold Preworkout', 'Pre-workout supplement for energy and focus', 34.99, 'photos/preworkout.jpeg', 'supplement', '2025-07-11 22:47:39'),
(8, '4kg Kevin Levrone - Mass Gainer', 'Mass gainer for weight and muscle gain', 84.99, 'photos/mass.jpeg', 'supplement', '2025-07-11 22:47:39');

-- --------------------------------------------------------

--
-- Table structure for table `recommended_supplements`
--

CREATE TABLE `recommended_supplements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `recommended_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `purchased` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `recommended_supplements`
--

INSERT INTO `recommended_supplements` (`id`, `user_id`, `product_id`, `reason`, `recommended_at`, `purchased`) VALUES
(27, 10, 6, 'May help increase muscle mass and strength gains, particularly beneficial for beginners.', '2025-07-17 18:26:41', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified` tinyint(1) DEFAULT 0,
  `verification_sent_at` datetime DEFAULT NULL,
  `verification_attempts` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `created_at`, `verified`, `verification_sent_at`, `verification_attempts`) VALUES
(1, 'anthony.imad@isae.edu.lb', '$2y$10$Q65vpgrmm/TrWWGMoVXfb.DbpRnoT6g/6DdpzVXHaORuxTqQ9E6Yy', 'anthony', '2025-07-12 00:49:50', 0, NULL, 0),
(10, 'yorgobekaiiprofessional@gmail.com', '$2y$10$YtDfn.Sh3tzCWa6iOCfWOOb0AOU9Q3yOiT11uBTwTuLWmuuBmuwjq', 'Yorgo', '2025-07-16 22:00:10', 1, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_workout_history`
--

CREATE TABLE `user_workout_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `workout_date` date NOT NULL,
  `workout_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`workout_data`)),
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_workout_history`
--

INSERT INTO `user_workout_history` (`id`, `user_id`, `workout_date`, `workout_data`, `completed`, `notes`) VALUES
(21, 10, '2025-07-17', '{\"weekly_plan\":[{\"day\":\"Monday\",\"focus\":\"Upper Body (Push)\",\"exercises\":[{\"name\":\"Dumbbell Bench Press\",\"sets\":3,\"reps\":8,\"rest\":\"60-90 seconds\",\"notes\":\"Focus on controlled movement, lowering the dumbbells slowly. If 8 reps are too easy, increase the weight slightly next week.\"},{\"name\":\"Dumbbell Shoulder Press\",\"sets\":3,\"reps\":8,\"rest\":\"60-90 seconds\",\"notes\":\"Maintain good posture, avoid arching your back. Start with lighter dumbbells if needed.\"},{\"name\":\"Dumbbell Flyes\",\"sets\":3,\"reps\":10,\"rest\":\"60 seconds\",\"notes\":\"Keep a slight bend in your elbows. Focus on squeezing your chest muscles at the top of the movement.\"},{\"name\":\"Resistance Band Triceps Pushdowns\",\"sets\":3,\"reps\":12,\"rest\":\"60 seconds\",\"notes\":\"Keep your elbows close to your body. Control the resistance as you extend your arms.\"}]},{\"day\":\"Wednesday\",\"focus\":\"Lower Body\",\"exercises\":[{\"name\":\"Dumbbell Squats\",\"sets\":3,\"reps\":10,\"rest\":\"60-90 seconds\",\"notes\":\"Focus on proper form â€“ chest up, back straight, squat down as if sitting in a chair.  Consider bodyweight squats until form is solid.\"},{\"name\":\"Dumbbell Lunges\",\"sets\":3,\"reps\":10,\"rest\":\"60-90 seconds\",\"notes\":\"Alternate legs each set. Keep your front knee behind your toes. Start with bodyweight only if needed.\"},{\"name\":\"Resistance Band Glute Bridges\",\"sets\":3,\"reps\":15,\"rest\":\"60 seconds\",\"notes\":\"Place the resistance band around your thighs, just above your knees. Squeeze your glutes at the top of the movement.\"},{\"name\":\"Standing Calf Raises (Dumbbells optional)\",\"sets\":3,\"reps\":15,\"rest\":\"60 seconds\",\"notes\":\"Can be done with or without dumbbells. Focus on a full range of motion.\"}]},{\"day\":\"Friday\",\"focus\":\"Upper Body (Pull)\",\"exercises\":[{\"name\":\"Dumbbell Rows\",\"sets\":3,\"reps\":8,\"rest\":\"60-90 seconds\",\"notes\":\"Maintain a flat back, pull the dumbbell towards your chest. Keep your elbow close to your body.\"},{\"name\":\"Resistance Band Pull-Aparts\",\"sets\":3,\"reps\":15,\"rest\":\"60 seconds\",\"notes\":\"Focus on squeezing your shoulder blades together. Use a band with appropriate resistance.\"},{\"name\":\"Dumbbell Bicep Curls\",\"sets\":3,\"reps\":10,\"rest\":\"60 seconds\",\"notes\":\"Keep your elbows close to your body. Avoid using momentum to swing the dumbbells.\"},{\"name\":\"Dumbbell Hammer Curls\",\"sets\":3,\"reps\":10,\"rest\":\"60 seconds\",\"notes\":\"Similar to bicep curls, but your palms face each other throughout the movement.\"}]}],\"supplement_recommendations\":[{\"name\":\"Creatine Monohydrate\",\"reason\":\"May help increase muscle mass and strength gains, particularly beneficial for beginners.\"},{\"name\":\"Whey Protein Powder\",\"reason\":\"Can help meet daily protein needs, supporting muscle recovery and growth, especially if dietary protein intake is insufficient. Consume post-workout or between meals.\"}],\"general_advice\":\"Consistency is key! Stick to this plan for at least 4-6 weeks and track your progress. Focus on proper form over lifting heavy weights. Gradually increase the weight or resistance as you get stronger. Ensure you\'re eating a balanced diet with enough protein (around 0.8-1 gram per pound of bodyweight). Get enough sleep (7-9 hours) for optimal recovery. Listen to your body and take rest days when needed. Don\'t be afraid to adjust the plan as you progress and discover what works best for you. If you have any pre-existing medical conditions, consult with a doctor or qualified healthcare professional before starting any new exercise program.\"}', 0, 'Workout plan generated');

-- --------------------------------------------------------

--
-- Table structure for table `workout_plans`
--

CREATE TABLE `workout_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `goal` varchar(255) DEFAULT NULL,
  `training_days` int(11) DEFAULT NULL,
  `equipment` varchar(255) DEFAULT NULL,
  `workout_plan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`workout_plan`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fitness_level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `gender` enum('male','female','other') DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `preferences` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_plans`
--

INSERT INTO `workout_plans` (`id`, `user_id`, `weight`, `height`, `age`, `goal`, `training_days`, `equipment`, `workout_plan`, `created_at`, `fitness_level`, `gender`, `medical_conditions`, `preferences`, `last_updated`) VALUES
(4, 10, 54.00, 174, 18, 'build_muscle', 3, 'dumbbells,resistance_bands', '{\"weekly_plan\":[{\"day\":\"Monday\",\"focus\":\"Upper Body (Push)\",\"exercises\":[{\"name\":\"Dumbbell Bench Press\",\"sets\":3,\"reps\":8,\"rest\":\"60-90 seconds\",\"notes\":\"Focus on controlled movement, lowering the dumbbells slowly. If 8 reps are too easy, increase the weight slightly next week.\"},{\"name\":\"Dumbbell Shoulder Press\",\"sets\":3,\"reps\":8,\"rest\":\"60-90 seconds\",\"notes\":\"Maintain good posture, avoid arching your back. Start with lighter dumbbells if needed.\"},{\"name\":\"Dumbbell Flyes\",\"sets\":3,\"reps\":10,\"rest\":\"60 seconds\",\"notes\":\"Keep a slight bend in your elbows. Focus on squeezing your chest muscles at the top of the movement.\"},{\"name\":\"Resistance Band Triceps Pushdowns\",\"sets\":3,\"reps\":12,\"rest\":\"60 seconds\",\"notes\":\"Keep your elbows close to your body. Control the resistance as you extend your arms.\"}]},{\"day\":\"Wednesday\",\"focus\":\"Lower Body\",\"exercises\":[{\"name\":\"Dumbbell Squats\",\"sets\":3,\"reps\":10,\"rest\":\"60-90 seconds\",\"notes\":\"Focus on proper form â€“ chest up, back straight, squat down as if sitting in a chair.  Consider bodyweight squats until form is solid.\"},{\"name\":\"Dumbbell Lunges\",\"sets\":3,\"reps\":10,\"rest\":\"60-90 seconds\",\"notes\":\"Alternate legs each set. Keep your front knee behind your toes. Start with bodyweight only if needed.\"},{\"name\":\"Resistance Band Glute Bridges\",\"sets\":3,\"reps\":15,\"rest\":\"60 seconds\",\"notes\":\"Place the resistance band around your thighs, just above your knees. Squeeze your glutes at the top of the movement.\"},{\"name\":\"Standing Calf Raises (Dumbbells optional)\",\"sets\":3,\"reps\":15,\"rest\":\"60 seconds\",\"notes\":\"Can be done with or without dumbbells. Focus on a full range of motion.\"}]},{\"day\":\"Friday\",\"focus\":\"Upper Body (Pull)\",\"exercises\":[{\"name\":\"Dumbbell Rows\",\"sets\":3,\"reps\":8,\"rest\":\"60-90 seconds\",\"notes\":\"Maintain a flat back, pull the dumbbell towards your chest. Keep your elbow close to your body.\"},{\"name\":\"Resistance Band Pull-Aparts\",\"sets\":3,\"reps\":15,\"rest\":\"60 seconds\",\"notes\":\"Focus on squeezing your shoulder blades together. Use a band with appropriate resistance.\"},{\"name\":\"Dumbbell Bicep Curls\",\"sets\":3,\"reps\":10,\"rest\":\"60 seconds\",\"notes\":\"Keep your elbows close to your body. Avoid using momentum to swing the dumbbells.\"},{\"name\":\"Dumbbell Hammer Curls\",\"sets\":3,\"reps\":10,\"rest\":\"60 seconds\",\"notes\":\"Similar to bicep curls, but your palms face each other throughout the movement.\"}]}],\"supplement_recommendations\":[{\"name\":\"Creatine Monohydrate\",\"reason\":\"May help increase muscle mass and strength gains, particularly beneficial for beginners.\"},{\"name\":\"Whey Protein Powder\",\"reason\":\"Can help meet daily protein needs, supporting muscle recovery and growth, especially if dietary protein intake is insufficient. Consume post-workout or between meals.\"}],\"general_advice\":\"Consistency is key! Stick to this plan for at least 4-6 weeks and track your progress. Focus on proper form over lifting heavy weights. Gradually increase the weight or resistance as you get stronger. Ensure you\'re eating a balanced diet with enough protein (around 0.8-1 gram per pound of bodyweight). Get enough sleep (7-9 hours) for optimal recovery. Listen to your body and take rest days when needed. Don\'t be afraid to adjust the plan as you progress and discover what works best for you. If you have any pre-existing medical conditions, consult with a doctor or qualified healthcare professional before starting any new exercise program.\"}', '2025-07-17 18:26:30', 'beginner', 'male', '', '', '2025-07-17 18:26:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recommended_supplements`
--
ALTER TABLE `recommended_supplements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_workout_history`
--
ALTER TABLE `user_workout_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `recommended_supplements`
--
ALTER TABLE `recommended_supplements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_workout_history`
--
ALTER TABLE `user_workout_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `workout_plans`
--
ALTER TABLE `workout_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `recommended_supplements`
--
ALTER TABLE `recommended_supplements`
  ADD CONSTRAINT `recommended_supplements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recommended_supplements_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `user_workout_history`
--
ALTER TABLE `user_workout_history`
  ADD CONSTRAINT `user_workout_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
