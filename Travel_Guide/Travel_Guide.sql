-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 22, 2025 at 04:38 AM
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
-- Database: `Travel_Guide`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `roll_number` varchar(20) DEFAULT NULL,
  `query_type` enum('travel_suggestion','bug_report','feature_request','general') NOT NULL,
  `subject` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `newsletter` tinyint(1) DEFAULT 0,
  `submission_date` datetime NOT NULL,
  `status` enum('pending','in_progress','resolved','closed') DEFAULT 'pending',
  `response` text DEFAULT NULL,
  `response_date` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `roll_number`, `query_type`, `subject`, `email`, `message`, `newsletter`, `submission_date`, `status`, `response`, `response_date`, `admin_notes`) VALUES
(1, 'Abhiram', '230150015', 'bug_report', 'Bug', 'a.madam@iitg.ac.in', 'bug', 1, '2025-04-22 04:19:43', 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `destination_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image_link` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `stay_cost` decimal(10,2) NOT NULL,
  `food_cost` decimal(10,2) NOT NULL,
  `stay_time` int(11) NOT NULL,
  `best_time_to_visit` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_id`, `name`, `description`, `image_link`, `latitude`, `longitude`, `stay_cost`, `food_cost`, `stay_time`, `best_time_to_visit`) VALUES
(29, 'Kamakhya Temple', 'One of the most revered Shakti Peethas, Kamakhya Temple is located atop Nilachal Hill and is dedicated to Goddess Kamakhya. The temple is famous for its unique architecture, spiritual significance, and the annual Ambubachi Mela, drawing pilgrims and tourists from across India.', 'https://c9admin.cottage9.com/uploads/5663/kamakhya-temple-history.jpg', 26.16660000, 91.70560000, 0.00, 300.00, 3, 'October to March'),
(30, 'Umananda Island', 'Umananda Island is the world’s smallest inhabited river island, situated in the Brahmaputra River. It hosts the ancient Umananda Temple dedicated to Lord Shiva and is known for its peaceful environment and occasional sightings of rare Golden Langurs.', 'https://upload.wikimedia.org/wikipedia/commons/4/4b/Umananda_Island%2C_Guwahati_%284%29.jpg', 26.19030000, 91.73920000, 0.00, 200.00, 2, 'October to March'),
(31, 'Pobitora Wildlife Sanctuary', 'Pobitora Wildlife Sanctuary is renowned for its dense population of the Indian one-horned rhinoceros. The sanctuary also shelters wild buffaloes, leopards, and over 200 bird species, making it a haven for wildlife enthusiasts and birdwatchers.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR-vqNNGsmhy3uvpPb7Ho39ebtcwXXoy9_V_A&s', 26.26210000, 91.98320000, 0.00, 400.00, 4, 'November to February'),
(32, 'Assam State Zoo', 'The Assam State Zoo cum Botanical Garden is the largest zoo in Northeast India, home to over 900 animals, birds, and reptiles. The lush gardens and diverse wildlife make it a popular spot for families and nature lovers.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3W8olLq_4ur45R1bQg3ZX3kc3HkNzS4MRmg&s', 26.14450000, 91.75380000, 0.00, 250.00, 3, 'October to March'),
(33, 'Saraighat Bridge', 'Saraighat Bridge is the first rail-cum-road bridge across the Brahmaputra River, connecting Assam with the rest of India. The bridge is an engineering marvel and offers breathtaking views of the river, especially at sunrise and sunset.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTutZ-4_5d5YudZSnI6aCJEXe96ocXMuWdQg&s', 26.19430000, 91.69180000, 0.00, 0.00, 1, 'Year-round'),
(34, 'Hajo', 'Hajo is an ancient pilgrimage town revered by Hindus, Buddhists, and Muslims. It is famous for the Hayagriva Madhava Temple, Powa Mecca mosque, and its peaceful rural landscapes, reflecting Assam’s religious harmony.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQgnBwOzcyVcNYX_ikdfENFG7wO49CfgokHtg&s', 26.24470000, 91.52530000, 0.00, 300.00, 3, 'October to March'),
(35, 'Dipor Bil', 'Dipor Bil is a freshwater lake and Ramsar site on the outskirts of Guwahati. It is a birdwatcher’s paradise, hosting numerous migratory and resident birds, and offers tranquil views amid lush greenery.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0t01tpBUK3Ls74EAWAAsi8G-Wsr9Zfg-Jzw&s', 26.10450000, 91.63320000, 0.00, 200.00, 2, 'November to March'),
(36, 'Nehru Park', 'Nehru Park is a beautifully landscaped park in the heart of Guwahati, featuring sculptures of Assamese dance forms, musical fountains, and an open-air theater. It is ideal for leisurely walks and family outings.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQC6mE2yLxPkYNZrpmGXhsh0VCWKt_WRWZWYA&s', 26.18610000, 91.75320000, 0.00, 100.00, 2, 'Year-round'),
(37, 'Purva Tirupati Balaji Temple', 'This temple is modeled after the famous Tirupati Balaji Temple in South India. It features striking Dravidian architecture and a peaceful atmosphere, making it a serene spiritual retreat in Guwahati.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJPsf40RWYSHoFjiDpmwEys9xw-Iggxa_rEg&s', 26.11560000, 91.79650000, 0.00, 150.00, 2, 'Year-round'),
(38, 'Basistha Ashram', 'Basistha Ashram is an ancient hermitage associated with sage Basistha, located at the confluence of the Basistha and Garbhanga rivers. The site features a historic temple, scenic waterfalls, and lush surroundings.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQF0WJ5jqaNNdAIuoVRwLPzdghXJ22wnJsoKQ&s', 26.10420000, 91.84130000, 0.00, 150.00, 2, 'October to March'),
(39, 'Fancy Bazar', 'Fancy Bazar is Guwahati’s bustling commercial hub, renowned for its traditional markets, vibrant street food, and lively atmosphere. It is a great place to shop for local handicrafts and experience Assamese culture.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQNEmpEcbTWmOElNIWH_oZhij9KtR_C47bq0Q&s', 26.18330000, 91.74000000, 0.00, 300.00, 2, 'Year-round'),
(40, 'Srimanta Sankardev Kalakshetra', 'Srimanta Sankardev Kalakshetra is a premier cultural institution in Assam, showcasing the region’s art, culture, and heritage through museums, open-air theaters, and exhibitions. It is both educational and entertaining for visitors.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR0uj74zcwMar5dFgywXNeX7FJL_EJTtMPiKg&s', 26.14370000, 91.78920000, 0.00, 200.00, 3, 'Year-round'),
(41, 'Kaziranga National Park', 'Famous for one-horned rhinoceros and UNESCO World Heritage Site. Offers jeep and elephant safaris.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ471E-c9ENE_mU0Moy8OT59jiM_TqPTrJZbQ&s', 26.57260000, 93.17340000, 4500.00, 800.00, 48, 'November to April'),
(43, 'Cherrapunji', 'Known as the \"wettest place on Earth,\" featuring waterfalls like Nohkalikai and mystical caves.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRx2HM-XO4YYkqEKFdL0FFeJJm-1mmBRE2eQ&s', 25.27110000, 91.73160000, 5000.00, 700.00, 24, 'October to May'),
(44, 'Dawki & Mawlynnong', 'Dawki’s crystal-clear Umngot River and Mawlynnong, Asia’s cleanest village.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5jWU5IYVSzUIxfznGj91uQMbg9_xvbByFGw&s', 25.20600000, 91.61300000, 2000.00, 500.00, 24, 'November to March'),
(45, 'Manas National Park', 'UNESCO site with tigers, elephants, and river rafting. Jeep safaris available.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4ETppzrlmVBR4GZiwBlwgEu-fav1j5rY76Q&s', 26.71500000, 91.01690000, 2000.00, 600.00, 24, 'October to April'),
(46, 'Bhalukpong', 'Gateway to Arunachal Pradesh, known for Kameng River and Nameri National Park.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSW7iwjay2_hImg_wVjQg4UNNizBfuL_YWqGQ&s', 27.03750000, 92.60030000, 3000.00, 500.00, 24, 'November to April'),
(47, 'Nameri National Park', 'Birdwatcher’s paradise with river rafting and eco-camps.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRP5vMSIuVDbwwdHceiYgaJomoGMg1MBmChxQ&s', 26.95000000, 92.83330000, 1800.00, 400.00, 24, 'November to April'),
(48, 'Tezpur', 'Historic town with mythological sites like Agnigarh Hill and scenic Brahmaputra views.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTcMz7rr9w_cz8V2jE4sqA4ShWuCFNdFjemPXN6S3Gbcd1p3zA_KicoarjFloBDdyHb6nY&usqp=CAU', 26.63380000, 92.79250000, 1600.00, 300.00, 24, 'October to March'),
(49, 'Haflong', 'Assam’s only hill station with lakes, trekking trails, and orchids.', 'https://live.staticflickr.com/5338/7154791222_3c17609e3e_b.jpg', 25.16480000, 93.01760000, 3500.00, 600.00, 48, 'Year-round'),
(50, 'Diphu', 'Cultural heart of Karbi Anglong, known for traditional festivals and handicrafts.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRtJ3w1l3QP7FkrKR-eHXj4kQtUFu1e77EWg&s', 25.84310000, 93.43100000, 2500.00, 400.00, 24, ''),
(51, 'Mayong', 'Village of black magic and sorcery, near Pobitora Wildlife Sanctuary.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiKJnT4vwIVtq-mk2JRusLMOFY95kC2t1rRA&s', 26.15750000, 92.32560000, 1500.00, 300.00, 12, 'November to March'),
(52, 'Krem Chympe', 'India’s 5th longest cave with underground rivers and waterfalls.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTKURo06qdTZHaxYs-qM-yRCila6aTE4lDN1w&s', 25.28330000, 92.36670000, 1000.00, 300.00, 12, 'November to March'),
(54, 'Tawang Monastery', 'Largest Buddhist monastery in India, perched at 10,000 ft with views of the Tawang Chu valley. Known for its spiritual significance and Tibetan architecture.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpfJ39bVbJrQApG1sTTkar1nsWsoSwukrFjw&s', 27.58610000, 91.86190000, 3500.00, 700.00, 48, 'March to October'),
(55, 'Ziro Valley', 'UNESCO-tagged cultural landscape of the Apatani tribe, famous for pine-clad hills, rice-fish farming, and the Ziro Music Festival.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ6UbbS1NbFNgdA5kEVvFhZ-NWT2QW4oevCbA&s', 27.63330000, 93.83330000, 2800.00, 600.00, 48, 'September to April'),
(56, 'Majuli Island', 'World’s largest river island and Vaishnavite cultural hub with 22 satras (monasteries). Stay in bamboo cottages or homestays.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRZJcHnkS8ELlORzqlib8SA-qbb6rnNHDtAvA&s', 26.95000000, 94.16670000, 1500.00, 400.00, 24, 'October to March'),
(58, 'Sivasagar', 'Historic capital of the Ahom Kingdom. Explore Rang Ghar (Asia’s oldest amphitheater) and Talatal Ghar.', 'https://www.mytrip-to-india.com/wp-content/uploads/2022/09/Sivasagar.png', 26.98330000, 94.63330000, 1800.00, 350.00, 12, 'October to March'),
(59, 'Bomdila-Dirang-Sangti Circuit', 'Scenic route through Buddhist monasteries, apple orchards, and hot springs. Stay at Sangti Velley homestay.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSeb4Afu_EGqBuNSGd7CNc4wvnWKVj1n7s1mXfZrSaqR-3U2p3A2reXWNChRGDrK3oGeIE&usqp=CAU', 27.26450000, 92.42440000, 2200.00, 450.00, 24, 'November to April'),
(60, 'Dzükou Valley', 'Alpine valley with seasonal flower carpets and trekking trails. Base at Kohima’s Dzükou Guest House.', 'https://unconventionalandvivid.com/wp-content/uploads/2019/08/imgonline-com-ua-compressed-Hy5kkpTophXmRAo.jpg', 25.56670000, 94.08330000, 2000.00, 400.00, 24, 'June to September'),
(61, 'Loktak Lake', 'Largest freshwater lake in NE India, famous for floating phumdis and Keibul Lamjao National Park (home to sangai deer).', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT75ZCdG6egNmSnWxFSAe2o9AqnmD9ZpcZV1_Oa8MDTPFRs3s3_4y1by130mfjuY4JOE78&usqp=CAU', 24.50000000, 93.76670000, 1800.00, 300.00, 12, 'October to March'),
(62, 'Laitlum Canyon', 'Dramatic canyon with cliffside trails and panoramic views. Free entry, 45 km from Shillong.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ1jAWd3fzvOxotOJIKkWH9MPdN72A64BdErgWIRwTB2TZgxG4jFplTnIpidaHJw0xNIig&usqp=CAU', 25.44170000, 91.75330000, 0.00, 200.00, 6, 'Year-round'),
(63, 'Nongriat Root Bridges', 'Iconic living root bridges accessible via a 3,000-step trek. Entry fee: ₹50.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQy2ZaazlEimiKGwtSlK_td34Lw7Jh5rzcMAcRV8D-qucdSWVg2SN4sFi-qFA5DbfaMe70&usqp=CAU', 25.27110000, 91.73160000, 1200.00, 300.00, 12, 'October to May'),
(64, 'Anini', 'Remote town in Dibang Valley with riverside camps and treks to Mehao Lake.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSSk3NpEQLAqkkeIjLE3Zy0TZpuw47Bupr77VdLG4sC4bGBADmTLDb6OWnV0EQNqWRmIk&usqp=CAU', 28.80000000, 95.90000000, 3000.00, 500.00, 48, 'November to April'),
(65, 'Dong Village', 'First inhabited place in India to witness sunrise. Trek 8 km for Himalayan views.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTd3TU2GFvWU5cEg5mIATfs3i58aCOnU5jSOUFwpU5WinhVDyg-LlzMgZpbAVJlTkBPKxw&usqp=CAU', 28.11530000, 97.02170000, 1500.00, 250.00, 12, 'November to February'),
(66, 'Mechuka Valley', '“Mini Tibet” with Samten Yongcha Monastery and Siyom River rafting. Homestays available.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS34N9gz0UxaA7tkEui6dbAfH5cRlGlpzIpEsbmKmgHuNX1iDS_a1lklWJa6ZWIDEslKhI&usqp=CAU', 28.60420000, 94.78610000, 2500.00, 450.00, 48, 'October to April'),
(67, 'Pasighat', 'Oldest town in Arunachal, gateway to Siang River adventures and Daying Ering Wildlife Sanctuary.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSnCvQsBRa6ZljNJeJhuVWXRw-icB6l1cdnb3DhzPZp_waYpxn48e7u9RZFdmzkOPkLGw&usqp=CAU', 28.06670000, 95.33330000, 2000.00, 400.00, 24, 'Year-round'),
(68, 'Krem Liat Prah', '34-km-long limestone cave system in Jaintia Hills. Guided tours available.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSsTTOQQixUUeVrj0UAs65vj7GrDj36PMqrVM7FzezI_zX5a1-773euDwBChfxEAjETXJ4&usqp=CAU', 25.46670000, 92.36670000, 1800.00, 300.00, 12, 'November to March');

-- --------------------------------------------------------

--
-- Table structure for table `planned_destinations`
--

CREATE TABLE `planned_destinations` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `visit_duration` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `sequence_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `planned_destinations`
--

INSERT INTO `planned_destinations` (`id`, `plan_id`, `destination_id`, `visit_duration`, `notes`, `sequence_order`) VALUES
(97, 14, 29, 4, 'Visit during morning for less crowd', 1),
(98, 14, 30, 3, 'Take the first ferry in the morning', 2),
(99, 14, 36, 2, 'Evening visit for musical fountains', 3),
(100, 14, 39, 4, 'Shopping for souvenirs', 4),
(101, 15, 54, 12, 'Spend a night near the monastery', 1),
(102, 15, 64, 24, 'Book homestay in advance', 2),
(103, 15, 65, 6, 'Wake up early for sunrise', 3),
(104, 15, 66, 24, 'Try local cuisine', 4),
(105, 15, 67, 10, 'River rafting if weather permits', 5),
(106, 16, 62, 6, 'Carry trekking gear', 1),
(107, 16, 63, 10, 'Start trek early morning', 2),
(108, 16, 68, 8, 'Book cave guide in advance', 3),
(110, 16, 43, 10, 'Waterproof equipment for photography', 5),
(111, 17, 31, 8, 'Morning safari for best wildlife viewing', 1),
(112, 17, 32, 6, 'Focus on the rare species section', 2),
(113, 17, 41, 12, 'Book elephant safari in advance', 3),
(114, 17, 47, 10, 'Try river rafting if time permits', 4),
(115, 18, 33, 2, 'Visit during sunset', 1),
(116, 18, 34, 6, 'Visit all three religious sites', 2),
(117, 18, 40, 6, 'Attend cultural performance if available', 3),
(119, 18, 58, 8, 'Hire a knowledgeable local guide', 5),
(120, 19, 30, 6, 'Early morning for wildlife spotting', 1),
(121, 19, 35, 4, 'Bring binoculars for birdwatching', 2),
(122, 19, 56, 24, 'Stay in traditional bamboo cottage', 3),
(123, 19, 61, 12, 'Boat tour of the phumdis', 4),
(124, 20, 29, 6, 'Participate in morning rituals if possible', 1),
(125, 20, 34, 8, 'Visit all major religious sites', 2),
(126, 20, 37, 4, 'Morning visit for peaceful experience', 3),
(127, 20, 38, 4, 'Explore the nearby waterfall too', 4),
(128, 21, 59, 24, 'Acclimatize properly before high altitude', 1),
(129, 21, 60, 24, 'Camp overnight in the valley', 2),
(130, 21, 63, 12, 'Start trek early for root bridges', 3),
(131, 21, 68, 10, 'Advanced booking for cave exploration', 4),
(132, 21, 47, 8, 'Try river rafting adventure', 5),
(133, 22, 40, 6, 'Attend cultural show if available', 1),
(134, 22, 55, 12, 'Try to coincide with local festival', 2),
(135, 22, 56, 24, 'Visit at least 3 different satras', 3),
(136, 22, 58, 8, 'Learn about Ahom architecture', 4),
(138, 23, 54, 12, 'Photography during golden hours', 1),
(139, 23, 60, 24, 'Best in summer for flower blooms', 2),
(140, 23, 62, 6, 'Sunrise view from the canyon', 3),
(141, 23, 66, 24, 'River valley landscapes', 4),
(142, 23, 43, 12, 'Visit all major waterfalls', 5),
(143, 24, 64, 24, 'Remote location, carry essentials', 1),
(144, 24, 65, 8, 'Special permit required, apply early', 2),
(145, 24, 51, 6, 'Visit Mayong Folklore Museum', 3),
(147, 24, 49, 24, 'Plan ahead as area is remote', 5);

-- --------------------------------------------------------

--
-- Table structure for table `planned_trips`
--

CREATE TABLE `planned_trips` (
  `plan_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `trip_name` varchar(100) NOT NULL,
  `mode_of_transport` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `planned_trips`
--

INSERT INTO `planned_trips` (`plan_id`, `user_id`, `trip_name`, `mode_of_transport`, `start_date`, `end_date`, `budget`, `created_at`) VALUES
(14, '230110015', 'Guwahati Weekend Getaway', 'Bus', '2024-11-15', '2024-11-17', 5000.00, '2024-10-01 04:45:00'),
(15, '230110069', 'Arunachal Adventure', 'Car', '2024-12-20', '2024-12-27', 25000.00, '2024-09-15 09:00:00'),
(16, '230150003', 'Meghalaya Exploration', 'Bike', '2025-01-10', '2025-01-15', 15000.00, '2024-10-05 04:15:00'),
(17, '230150007', 'Wildlife Tour', 'Car', '2024-11-25', '2024-11-30', 20000.00, '2024-10-10 10:50:00'),
(18, '230150015', 'Historical Northeast', 'Train', '2024-12-05', '2024-12-12', 18000.00, '2024-09-25 06:00:00'),
(19, '230150037', 'Island and River Expedition', 'Boat', '2025-02-10', '2025-02-15', 12000.00, '2024-10-15 08:15:00'),
(20, '230150045', 'Spiritual Journey', 'Car', '2024-11-20', '2024-11-25', 10000.00, '2024-10-02 09:30:00'),
(21, '230150063', 'Adventure Trek', 'Mixed', '2025-03-01', '2025-03-10', 30000.00, '2024-10-20 03:30:00'),
(22, '230150069', 'Cultural Northeast', 'Bus', '2024-12-15', '2024-12-22', 16000.00, '2024-09-30 08:45:00'),
(23, '230150073', 'Scenic Northeast', 'Car', '2025-01-20', '2025-01-28', 22000.00, '2024-10-12 05:00:00'),
(24, '230150077', 'Off-Beat Destinations', 'Bike', '2025-02-15', '2025-02-22', 18000.00, '2024-10-18 05:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `review_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `destination_id`, `user_id`, `rating`, `comment`, `is_approved`, `review_at`) VALUES
(30, 29, '230110015', 5, 'Kamakhya Temple has amazing spiritual energy. The architecture is breathtaking and the views from Nilachal Hill are spectacular!', 1, '2024-10-15 04:00:00'),
(31, 30, '230110069', 4, 'Umananda Island is peaceful and serene. The boat ride to the island was enjoyable. Didn\'t see any Golden Langurs though.', 1, '2024-09-20 06:15:00'),
(32, 31, '230150003', 5, 'Pobitora Wildlife Sanctuary is a must-visit! Saw so many rhinos up close. Much less crowded than Kaziranga.', 1, '2024-08-05 10:50:00'),
(33, 32, '230150007', 3, 'Assam State Zoo is decent but needs better maintenance. The botanical garden section was nice though.', 1, '2024-10-10 08:45:00'),
(34, 33, '230150015', 4, 'Saraighat Bridge offers stunning sunset views. Great place for photography enthusiasts!', 1, '2024-09-25 12:00:00'),
(35, 34, '230150037', 5, 'Hajo is a hidden gem. The religious harmony and historical sites make it a unique experience.', 1, '2024-07-15 06:30:00'),
(36, 35, '230150045', 4, 'Dipor Bil is perfect for birdwatching. Spotted several migratory species. Take binoculars!', 1, '2024-10-05 02:30:00'),
(37, 36, '230150063', 3, 'Nehru Park is good for a quick evening visit. The musical fountains were not working when I went.', 1, '2024-09-01 13:00:00'),
(38, 37, '230150069', 5, 'Purva Tirupati Balaji Temple is beautiful and serene. The architecture is magnificent.', 1, '2024-08-20 04:30:00'),
(39, 38, '230150073', 4, 'Basistha Ashram has a calming atmosphere. The waterfalls nearby are worth visiting too.', 1, '2024-09-10 06:00:00'),
(40, 39, '230150077', 4, 'Fancy Bazar is bustling with energy! Great place to buy local handicrafts and try street food.', 1, '2024-10-12 10:30:00'),
(41, 40, '230110015', 5, 'Srimanta Sankardev Kalakshetra beautifully showcases Assamese culture. The exhibitions are informative and engaging.', 1, '2024-09-30 08:15:00'),
(42, 54, '230110069', 5, 'Tawang Monastery is breathtaking! The journey is challenging but completely worth it.', 1, '2024-07-25 08:30:00'),
(43, 55, '230150003', 4, 'Ziro Valley has incredible landscapes and the Apatani culture is fascinating. Attended the music festival and loved it!', 1, '2024-09-15 11:50:00'),
(44, 56, '230150007', 5, 'Majuli Island is like stepping back in time. The satras and local culture are amazing. Stayed in a bamboo cottage which was a unique experience.', 1, '2024-08-10 05:00:00'),
(46, 58, '230150037', 5, 'Sivasagar\'s historical monuments are impressive. Rang Ghar and Talatal Ghar showcase amazing Ahom architecture.', 1, '2024-09-05 05:45:00'),
(47, 59, '230150045', 4, 'The Bomdila-Dirang-Sangti Circuit offers diverse experiences. The hot springs in Dirang were especially relaxing.', 1, '2024-10-01 10:00:00'),
(48, 60, '230150063', 5, 'Dzükou Valley is paradise! The flower blooms in summer are unbelievable. Tough trek but worth every step.', 1, '2024-07-20 04:15:00'),
(49, 61, '230150069', 5, 'Loktak Lake\'s floating phumdis are a unique sight. Saw the endangered sangai deer too!', 1, '2024-08-15 07:00:00'),
(50, 62, '230150073', 4, 'Laitlum Canyon offers breathtaking views. The hiking trails are challenging but rewarding.', 1, '2024-09-20 08:50:00'),
(51, 63, '230150077', 5, 'Nongriat Root Bridges are engineering marvels! The trek is exhausting but the bridges are incredible.', 1, '2024-10-15 05:30:00'),
(52, 64, '230110015', 4, 'Anini is remote but beautiful. Perfect for those seeking solitude and pristine nature.', 1, '2024-08-25 10:45:00'),
(53, 65, '230110069', 5, 'Dong Village sunrise is magical! Get there early and bring warm clothes.', 1, '2024-11-05 01:30:00'),
(54, 66, '230150003', 5, 'Mechuka Valley truly feels like mini Tibet. The monastery and landscapes are stunning.', 1, '2024-10-18 08:00:00'),
(55, 67, '230150007', 4, 'Pasighat is a great base for exploring Eastern Arunachal. The Siang River rafting was thrilling!', 1, '2024-09-15 10:15:00'),
(56, 68, '230150015', 5, 'Krem Liat Prah caves are mind-blowing! Hire a good guide as it\'s easy to get lost.', 1, '2024-08-30 05:50:00'),
(57, 41, '230150037', 5, 'Kaziranga safari was the highlight of my Northeast trip! Saw numerous rhinos, elephants, and even tigers from a distance.', 1, '2024-11-10 03:30:00'),
(59, 43, '230150063', 5, 'Cherrapunji\'s waterfalls are spectacular, especially after monsoon. Nohkalikai Falls is simply majestic!', 1, '2024-09-30 07:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default_profile.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `profile_image`) VALUES
('230110015', 'Eshanth', 'eshanth@iitg.ac.in', '$2y$10$OMFJ8I17rGzIqUh8lx8avOjfxIc8011ZwFeolyzygVws6sN8sQk4.', 'default_profile.png'),
('230110069', 'pavan', 'pavan@iitg.ac.in', '$2y$10$01saNGOfuDzMNuZVTW4hWOA5osYTduim6EtpmLiFCbT2foih1XTue', 'default_profile.png'),
('230150003', 'Aftab', 'aftab@iitg.ac.in', '$2y$10$jcMtoaUBtqlVXZS/XvLo5.lXXno7KdKGanIfL6q5vuypIOndFAG7e', 'default_profile.png'),
('230150007', 'Rishi', 'rishi@iitg.ac.in', '$2y$10$1cHLTJT0cygyDFZVk7H8rOWhhT2Qarx2.QPNscVBt6ifHw1CuZifm', 'default_profile.png'),
('230150015', 'Abhiram', 'a.madam@iitg.ac.in', '$2y$10$jRFEDWElzxkE7FfvBUPe2efdNMoLsTFvZdVL7HxfWQd53q.KI/dHe', '230150015_1745262154.jpg'),
('230150037', 'Amritha', 'amritha@iitg.ac.in', '$2y$10$bNoqLajOYm2r9J3jiQvdm.EfqgE4KFUZmec0PJxSzCyt/4kdrLe.O', 'default_profile.png'),
('230150045', 'vivek', 'vivek@iitg.ac.in', '$2y$10$8o5r97bnCPZDodqmVzD0YeE/DxEoHEaICR1WvTIQfHi1yrOucuceu', 'default_profile.png'),
('230150063', 'Mihika', 'mihika@iitg.ac.in', '$2y$10$eAMqCO.QJ209SDxexYkw0eZtLPDgzBk77UBPMm8t/6ZvXbqXrLRyK', 'default_profile.png'),
('230150069', 'sonu', 'sonu@iitg.ac.in', '$2y$10$hom.sYfW5tSCs5nZ37wOiOgqYyOq0DRXNAcJGjLyHmOQ5Sk0B9.v2', 'default_profile.png'),
('230150073', 'Manaswini', 'manaswini@iitg.ac.in', '$2y$10$tMpvg59aPlRBo3IFPHDl6On9rf9IHAj/5MRY9v9.MHdHzrgtcPWDe', 'default_profile.png'),
('230150077', 'Rhea', 'rhea@iitg.ac.in', '$2y$10$Ui7aTwLIFTPLpOPgrZcCd.0V1PVQCvcW0ysjugD5aQw9DDHtNnGHq', 'default_profile.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`destination_id`);

--
-- Indexes for table `planned_destinations`
--
ALTER TABLE `planned_destinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `planned_trips`
--
ALTER TABLE `planned_trips`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `destination_id` (`destination_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `planned_destinations`
--
ALTER TABLE `planned_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `planned_trips`
--
ALTER TABLE `planned_trips`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `planned_destinations`
--
ALTER TABLE `planned_destinations`
  ADD CONSTRAINT `planned_destinations_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `planned_trips` (`plan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `planned_destinations_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`) ON DELETE CASCADE;

--
-- Constraints for table `planned_trips`
--
ALTER TABLE `planned_trips`
  ADD CONSTRAINT `planned_trips_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
