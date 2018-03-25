CREATE TABLE `guild` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `color` char(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guild_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path_UNIQUE` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `killed` (
  `name` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `value` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`name`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `x` tinyint(3) unsigned DEFAULT NULL,
  `y` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `x_y_UNIQUE` (`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `nain` (
  `id` int(10) unsigned NOT NULL,
  `current_update` int(10) unsigned DEFAULT NULL,
  `last_date` datetime DEFAULT NULL,
  `last_image` int(10) unsigned DEFAULT NULL,
  `last_name` int(10) unsigned DEFAULT NULL,
  `last_tag` int(10) unsigned DEFAULT NULL,
  `last_guild` int(10) unsigned DEFAULT NULL,
  `last_level` int(10) unsigned DEFAULT NULL,
  `last_side` tinyint(3) unsigned DEFAULT NULL,
  `last_map` int(10) unsigned DEFAULT NULL,
  `last_x` tinyint(3) unsigned DEFAULT NULL,
  `last_y` tinyint(3) unsigned DEFAULT NULL,
  `last_death` date DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `hp_min` smallint(5) unsigned DEFAULT NULL,
  `hp_max` smallint(5) unsigned DEFAULT NULL,
  `bourrin` decimal(4,2) unsigned DEFAULT NULL,
  `sniper` decimal(4,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `objet` (
  `id` bigint(20) unsigned NOT NULL,
  `current_update` int(10) unsigned NOT NULL,
  `image` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL,
  `name` int(10) unsigned NOT NULL,
  `map` int(10) unsigned NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `dust` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `objet_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `objet_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `square` (
  `map` int(10) unsigned NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `current_update` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`map`,`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_UNIQUE` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `update` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `map` int(10) unsigned NOT NULL,
  `x` tinyint(3) NOT NULL,
  `y` tinyint(3) NOT NULL,
  `range` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `update_nain` (
  `nain` int(10) unsigned NOT NULL,
  `update` int(10) unsigned NOT NULL,
  `image` int(10) unsigned NOT NULL,
  `name` int(10) unsigned NOT NULL,
  `tag` int(10) unsigned DEFAULT NULL,
  `guild` int(10) unsigned DEFAULT NULL,
  `level` int(10) unsigned NOT NULL,
  `side` tinyint(3) unsigned NOT NULL,
  `x` tinyint(3) NOT NULL,
  `y` tinyint(3) NOT NULL,
  PRIMARY KEY (`nain`,`update`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `password` varchar(40) NOT NULL,
  `is_active` tinyint(3) unsigned NOT NULL,
  `is_admin` tinyint(3) unsigned NOT NULL,
  `last_connection` datetime DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `nain` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
