
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `activeLoans` (
  `username` varchar(65) NOT NULL,
  `amount` int(65) NOT NULL,
  `date` date NOT NULL,
  `interest` int(65) NOT NULL,
  `emailed` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `activeLoans` (`username`, `amount`, `date`, `interest`, `emailed`) VALUES
('bank', 0, '9999-12-31', 0, 1);

CREATE TABLE `balance` (
  `username` varchar(65) NOT NULL,
  `userBalance` int(65) NOT NULL,
  `userLoans` int(65) NOT NULL,
  `pendingIn` int(65) NOT NULL DEFAULT '0',
  `pendingOut` int(65) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `balance` (`username`, `userBalance`, `userLoans`, `pendingIn`, `pendingOut`) VALUES
('bank', 0, 0, 0, 0);

CREATE TABLE `loginAttempts` (
  `IP` varchar(20) NOT NULL,
  `Attempts` int(11) NOT NULL,
  `LastLogin` datetime NOT NULL,
  `Username` varchar(65) DEFAULT NULL,
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `members` (
  `id` char(23) NOT NULL,
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(65) NOT NULL DEFAULT '',
  `email` varchar(65) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `mod_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `isAdmin` int(1) NOT NULL DEFAULT '0',
  `isBanned` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `userLog` (
  `id` int(10) NOT NULL,
  `action` varchar(65) CHARACTER SET utf8 NOT NULL,
  `username` varchar(65) CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `activeLoans`
  ADD PRIMARY KEY (`username`);

ALTER TABLE `balance`
  ADD PRIMARY KEY (`username`);

ALTER TABLE `loginAttempts`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

ALTER TABLE `userLog`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `loginAttempts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

ALTER TABLE `userLog`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=634;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
