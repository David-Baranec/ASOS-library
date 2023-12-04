-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: Út 28.Nov 2023, 13:26
-- Verzia serveru: 10.4.18-MariaDB
-- Verzia PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `library_db`
--

DELIMITER $$
--
-- Procedúry
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_due_list` ()  NO SQL
SELECT I.issue_id, M.email, B.isbn, B.title
FROM book_issue_log I INNER JOIN member M on I.member = M.username INNER JOIN book B ON I.book_isbn = B.isbn
WHERE DATEDIFF(CURRENT_DATE, I.due_date) >= 0 AND DATEDIFF(CURRENT_DATE, I.due_date) % 5 = 0 AND (I.last_reminded IS NULL OR DATEDIFF(I.last_reminded, CURRENT_DATE) <> 0)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `book`
--

CREATE TABLE `book` (
  `isbn` char(13) NOT NULL,
  `title` varchar(80) NOT NULL,
  `author` varchar(80) NOT NULL,
  `category` varchar(80) NOT NULL,
  `price` int(4) UNSIGNED NOT NULL,
  `copies` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Sťahujem dáta pre tabuľku `book`
--

INSERT INTO `book` (`isbn`, `title`, `author`, `category`, `price`, `copies`) VALUES
('0000545010225', 'Harry Potter and the Deathly Hallows', 'J. K. Rowling', 'Fiction', 55, 30),
('0000553103547', 'A Game of Thrones', 'George R. R. Martin', 'Fiction', 50, 10),
('0000553106635', 'A Storm of Swords', 'George R. R. Martin', 'Fiction', 55, 15),
('0000553108034', 'A Clash of Kings', 'George R. R. Martin', 'Fiction', 50, 0),
('0000553801503', 'A Feast for Crows', 'George R. R. Martin', 'Fiction', 60, 19),
('0000747532699', 'Harry Potter and the Philosopher\'s Stone', 'J. K. Rowling', 'Fiction', 30, 12),
('0000747538492', 'Harry Potter and the Chamber of Secrets', 'J. K. Rowling', 'Fiction', 30, 10),
('0000747542155', 'Harry Potter and the Prisoner of Azkaban', 'J. K. Rowling', 'Fiction', 35, 16),
('0000747546240', 'Harry Potter and the Goblet of Fire', 'J. K. Rowling', 'Fiction', 40, 15),
('0000747551006', 'Harry Potter and the Order of the Phoenix', 'J. K. Rowling', 'Fiction', 40, 20),
('0000747581088', 'Harry Potter and the Half-Blood Prince', 'J. K. Rowling', 'Fiction', 50, 25),
('9780066620992', 'Good to Great', 'Jim Collins', 'Non-fiction', 30, 10),
('9780241257555', 'The Pigeon Tunnel', 'John le CarrÃ©', 'Non-fiction', 20, 25),
('9780439023511', 'Mockingjay', 'Suzanne Collins', 'Fiction', 50, 20),
('9780439023528', 'The Hunger Games', 'Suzanne Collins', 'Fiction', 40, 10),
('9780545227247', 'Catching Fire', 'Suzanne Collins', 'Fiction', 40, 15),
('9780553801477', 'A Dance with Dragons', 'George R. R. Martin', 'Fiction', 60, 0),
('9780967752808', 'Sandbox Wisdom', 'Tom Asacker', 'Non-fiction', 25, 5),
('9781501141515', 'Born to Run', 'Bruce Springsteen', 'Non-fiction', 25, 20),
('9788183331630', 'Let Us C', 'Yashavant Kanetkar', 'Education', 20, 22),
('9789350776667', 'Computer Graphics and Virtual Reality', 'Sanjesh S. Pawale', 'Education', 10, 30),
('9789350776773', 'Microcontroller and Embedded Systems', 'Harish G. Narula', 'Education', 8, 15),
('9789350777077', 'Advanced Database Management Systems', 'Mahesh Mali', 'Education', 6, 30),
('9789350777121', 'Operating Systems', 'Rajesh Kadu', 'Education', 5, 24),
('9789351194545', 'Open Source Technologies', 'Dayanand Ambawade', 'Education', 10, 20),
('9789381626719', 'Stay Hungry Stay Foolish', 'Rashmi Bansal', 'Non-fiction', 10, 5);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `book_history_log`
--

CREATE TABLE `book_history_log` (
  `history_id` int(11) NOT NULL,
  `member` varchar(20) NOT NULL,
  `book_isbn` varchar(13) NOT NULL,
  `borrowing_date` date NOT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Sťahujem dáta pre tabuľku `book_history_log`
--

INSERT INTO `book_history_log` (`history_id`, `member`, `book_isbn`, `borrowing_date`, `return_date`) VALUES
(3, 'koo', '9789350776773', '2023-11-28', '2023-11-28'),
(4, 'koo', '0000553801503', '2023-11-28', NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `book_issue_log`
--

CREATE TABLE `book_issue_log` (
  `issue_id` int(11) NOT NULL,
  `member` varchar(20) NOT NULL,
  `book_isbn` varchar(13) NOT NULL,
  `due_date` date NOT NULL,
  `last_reminded` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Sťahujem dáta pre tabuľku `book_issue_log`
--

INSERT INTO `book_issue_log` (`issue_id`, `member`, `book_isbn`, `due_date`, `last_reminded`) VALUES
(14, 'koo', '0000553801503', '2023-12-05', NULL);

--
-- Spúšťače `book_issue_log`
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
-- Štruktúra tabuľky pre tabuľku `librarian`
--

CREATE TABLE `librarian` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Sťahujem dáta pre tabuľku `librarian`
--

INSERT INTO `librarian` (`id`, `username`, `password`) VALUES
(1, 'genesis', '93c768d0152f72bc8d5e782c0b585acc35fb0442');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `balance` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Sťahujem dáta pre tabuľku `member`
--

INSERT INTO `member` (`id`, `username`, `password`, `name`, `email`, `balance`) VALUES
(4, 'koo', '956154322bd4b43a2af03dca7a245c1a5eb83b39', 'koo ', 'koo@gmail.com', 9350),
(5, 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Test', 'test@test.com', 500),
(6, 'domi', '48f95adcdd2d1d9b1da95a0e676fea740d73adad', 'dominik', 'gggg@ssss', 50000);

--
-- Spúšťače `member`
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
-- Štruktúra tabuľky pre tabuľku `pending_book_requests`
--

CREATE TABLE `pending_book_requests` (
  `request_id` int(11) NOT NULL,
  `member` varchar(20) NOT NULL,
  `book_isbn` varchar(13) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `balance` int(4) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexy pre tabuľku `book_history_log`
--
ALTER TABLE `book_history_log`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexy pre tabuľku `book_issue_log`
--
ALTER TABLE `book_issue_log`
  ADD PRIMARY KEY (`issue_id`);

--
-- Indexy pre tabuľku `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexy pre tabuľku `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexy pre tabuľku `pending_book_requests`
--
ALTER TABLE `pending_book_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexy pre tabuľku `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `book_history_log`
--
ALTER TABLE `book_history_log`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pre tabuľku `book_issue_log`
--
ALTER TABLE `book_issue_log`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pre tabuľku `librarian`
--
ALTER TABLE `librarian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pre tabuľku `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pre tabuľku `pending_book_requests`
--
ALTER TABLE `pending_book_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
