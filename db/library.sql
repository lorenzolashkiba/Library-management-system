-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 15, 2022 at 09:18 PM
-- Server version: 5.7.34
-- PHP Version: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_due_list` ()  NO SQL
SELECT I.issue_id, M.email, B.isbn, B.title
FROM book_issue_log I INNER JOIN member M on I.member = M.username INNER JOIN book B ON I.book_isbn = B.isbn
WHERE DATEDIFF(CURRENT_DATE, I.due_date) >= 0 AND DATEDIFF(CURRENT_DATE, I.due_date) % 5 = 0 AND (I.last_reminded IS NULL OR DATEDIFF(I.last_reminded, CURRENT_DATE) <> 0)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `isbn` char(13) NOT NULL,
  `title` varchar(80) NOT NULL,
  `author` varchar(80) NOT NULL,
  `category` varchar(80) NOT NULL,
  `copies` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`isbn`, `title`, `author`, `category`, `copies`) VALUES
('0000545010225', 'Harry Potter and the Deathly Hallows', 'J. K. Rowling', 'Fiction', 30),
('0000553103547', 'A Game of Thrones', 'George R. R. Martin', 'Fiction', 10),
('0000553106635', 'A Storm of Swords', 'George R. R. Martin', 'Fiction', 15),
('0000553108034', 'A Clash of Kings', 'George R. R. Martin', 'Fiction', 9),
('0000553801503', 'A Feast for Crows', 'George R. R. Martin', 'Fiction', 20),
('0000747532699', 'Harry Potter and the Philosopher\'s Stone', 'J. K. Rowling', 'Fiction', 12),
('0000747538492', 'Harry Potter and the Chamber of Secrets', 'J. K. Rowling', 'Fiction', 10),
('0000747542155', 'Harry Potter and the Prisoner of Azkaban', 'J. K. Rowling', 'Fiction', 16),
('0000747546240', 'Harry Potter and the Goblet of Fire', 'J. K. Rowling', 'Fiction', 15),
('0000747551006', 'Harry Potter and the Order of the Phoenix', 'J. K. Rowling', 'Fiction', 20),
('0000747581088', 'Harry Potter and the Half-Blood Prince', 'J. K. Rowling', 'Fiction', 25),
('9780066620992', 'Good to Great', 'Jim Collins', 'Non-fiction', 10),
('9780241257555', 'The Pigeon Tunnel', 'John le CarrÃ©', 'Non-fiction', 25),
('9780439023511', 'Mockingjay', 'Suzanne Collins', 'Fiction', 20),
('9780439023528', 'The Hunger Games', 'Suzanne Collins', 'Fiction', 10),
('9780545227247', 'Catching Fire', 'Suzanne Collins', 'Fiction', 14),
('9780553801477', 'A Dance with Dragons', 'George R. R. Martin', 'Fiction', 30),
('9780967752808', 'Sandbox Wisdom', 'Tom Asacker', 'Non-fiction', 5),
('9781501141515', 'Born to Run', 'Bruce Springsteen', 'Non-fiction', 20),
('9788183331630', 'Let Us C', 'Yashavant Kanetkar', 'Education', 22),
('9789350776667', 'Computer Graphics and Virtual Reality', 'Sanjesh S. Pawale', 'Education', 30),
('9789350776773', 'Microcontroller and Embedded Systems', 'Harish G. Narula', 'Education', 15),
('9789350777077', 'Advanced Database Management Systems', 'Mahesh Mali', 'Education', 29),
('9789350777121', 'Operating Systems', 'Rajesh Kadu', 'Education', 24),
('9789351194545', 'Open Source Technologies', 'Dayanand Ambawade', 'Education', 20),
('9789381626719', 'Stay Hungry Stay Foolish', 'Rashmi Bansal', 'Non-fiction', 5);

-- --------------------------------------------------------

--
-- Table structure for table `book_issue_log`
--

CREATE TABLE `book_issue_log` (
  `issue_id` int(11) NOT NULL,
  `member` varchar(20) NOT NULL,
  `book_isbn` varchar(13) NOT NULL,
  `due_date` date NOT NULL,
  `last_reminded` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `book_issue_log`
--

INSERT INTO `book_issue_log` (`issue_id`, `member`, `book_isbn`, `due_date`, `last_reminded`) VALUES
(1, 'seph32', '9789350777077', '2016-10-17', '2022-11-15'),
(2, 'seph32', '9780545227247', '2016-10-17', '2022-11-15'),
(3, 'lkn', '0000553108034', '2022-11-21', NULL);

--
-- Triggers `book_issue_log`
--
DELIMITER $$
CREATE TRIGGER `issue_book` BEFORE INSERT ON `book_issue_log` FOR EACH ROW BEGIN
	SET NEW.due_date = DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY);
    UPDATE member SET balance = balance - (SELECT price FROM book WHERE isbn = NEW.book_isbn) WHERE username = NEW.member;
    UPDATE book SET copies = copies - 1 WHERE isbn = NEW.book_isbn;
    DELETE FROM pending_book_requests WHERE member = NEW.member AND book_isbn = NEW.book_isbn;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `return_book` BEFORE DELETE ON `book_issue_log` FOR EACH ROW BEGIN
    UPDATE member SET balance = balance + (SELECT price FROM book WHERE isbn = OLD.book_isbn) WHERE username = OLD.member;
    UPDATE book SET copies = copies + 1 WHERE isbn = OLD.book_isbn;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `librarian`
--

CREATE TABLE `librarian` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `librarian`
--

INSERT INTO `librarian` (`id`, `username`, `password`) VALUES
(1, 'genesis', '93c768d0152f72bc8d5e782c0b585acc35fb0442');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `username`, `password`, `name`, `email`) VALUES
(1, 'cloud9', 'c67adbca4bd9f7e583f05f4c7edbcb733c7c9233', 'Cloud Strife', 'cloud@shinra.com'),
(2, 'seph32', '75bf2b008d91258f56fc0d3a938ca64b8a631533', 'Sephiroth', 'seph@shinra.com'),
(3, 'zack_ff7', '52d849001964af394040dc48b673f748e55e1af7', 'Zack Fair', 'zack@shinra.com'),
(4, 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Test', 'test@test.com'),
(5, 'lkn', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'loren', 'lorenzoboiko@gmail.com'),
(6, 'lore', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '23456u', 'lorenzofgboiko@gmail.com'),
(7, 'sdad', 'fb2cd57838a759e660faccf15efb31dd94e11701', 'sdfghjw', 'romano3783@lance7.com');

--
-- Triggers `member`
--
DELIMITER $$
CREATE TRIGGER `add_member` AFTER INSERT ON `member` FOR EACH ROW DELETE FROM pending_registrations WHERE username = NEW.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_member` AFTER DELETE ON `member` FOR EACH ROW DELETE FROM pending_book_requests WHERE member = OLD.username
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pending_book_requests`
--

CREATE TABLE `pending_book_requests` (
  `request_id` int(11) NOT NULL,
  `member` varchar(20) NOT NULL,
  `book_isbn` varchar(13) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pending_book_requests`
--

INSERT INTO `pending_book_requests` (`request_id`, `member`, `book_isbn`, `time`) VALUES
(1, 'zack_ff7', '9780553801477', '2016-10-10 12:53:27');

-- --------------------------------------------------------

--
-- Table structure for table `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexes for table `book_issue_log`
--
ALTER TABLE `book_issue_log`
  ADD PRIMARY KEY (`issue_id`);

--
-- Indexes for table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pending_book_requests`
--
ALTER TABLE `pending_book_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book_issue_log`
--
ALTER TABLE `book_issue_log`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `librarian`
--
ALTER TABLE `librarian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pending_book_requests`
--
ALTER TABLE `pending_book_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
