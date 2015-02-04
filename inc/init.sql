-- Create syntax for TABLE 'v2_badge'
CREATE TABLE `v2_badge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `team` int(11) unsigned NOT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `color` varchar(12) DEFAULT NULL,
  `color2` varchar(12) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'v2_event'
CREATE TABLE `v2_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL DEFAULT '',
  `location` varchar(512) DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'v2_session'
CREATE TABLE `v2_session` (
  `session_id` varchar(256) NOT NULL DEFAULT '',
  `session_data` longtext NOT NULL,
  `session_lastaccesstime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'v2_shift'
CREATE TABLE `v2_shift` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` int(11) DEFAULT NULL,
  `team` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event` (`event`,`team`,`end`,`start`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'v2_team'
CREATE TABLE `v2_team` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `lead` int(11) DEFAULT NULL,
  `openjoin` tinyint(1) DEFAULT '0',
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'v2_user'
CREATE TABLE `v2_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `salt` varchar(256) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `firstname` varchar(64) DEFAULT NULL,
  `lastname` varchar(64) DEFAULT NULL,
  `burnername` varchar(64) DEFAULT NULL,
  `callsign` varchar(64) DEFAULT NULL,
  `rank` enum('U','A') DEFAULT 'U',
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `callsign` (`callsign`),
  UNIQUE KEY `burnername` (`burnername`),
  UNIQUE KEY `firstname` (`firstname`,`lastname`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'v2_userbadges'
CREATE TABLE `v2_userbadges` (
  `user` int(11) unsigned NOT NULL,
  `badge` int(11) unsigned NOT NULL,
  `status` enum('R','G') NOT NULL DEFAULT 'R' COMMENT 'Requested, Granted',
  UNIQUE KEY `user` (`user`,`badge`),
  KEY `FK2_badge` (`badge`),
  CONSTRAINT `FK2_badge` FOREIGN KEY (`badge`) REFERENCES `v2_badge` (`id`),
  CONSTRAINT `FK2_userbadge` FOREIGN KEY (`user`) REFERENCES `v2_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'v2_userteams'
CREATE TABLE `v2_userteams` (
  `user` int(11) unsigned NOT NULL,
  `team` int(11) unsigned NOT NULL,
  `status` enum('A','M') NOT NULL DEFAULT 'M',
  UNIQUE KEY `user` (`user`,`team`),
  KEY `FK2_teams` (`team`),
  CONSTRAINT `FK2_teams` FOREIGN KEY (`team`) REFERENCES `v2_team` (`id`),
  CONSTRAINT `FK2_user` FOREIGN KEY (`user`) REFERENCES `v2_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;