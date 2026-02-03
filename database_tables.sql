-- Table structure for event_config
CREATE TABLE IF NOT EXISTS `event_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_start_date` varchar(20) NOT NULL,
  `reg_end_date` varchar(20) NOT NULL,
  `event_date` varchar(20) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for event_registrations
CREATE TABLE IF NOT EXISTS `event_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `college` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `event_date` varchar(20) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `created` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;