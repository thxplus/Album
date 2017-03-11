
CREATE TABLE `typecho_album` (
  `id` 					int(10) 			unsigned 			NOT NULL 			auto_increment,
  `name`				text,								
  `mime`				varchar(32)									default NULL,
  `pixel`				text,												
  `size`				int(12)				unsigned			default '0',
  `created`			int(10) 			unsigned 			default '0',
  `description`	text,
  `url`					text,
  `thumb`				text,
  `public`			TINYINT(1) 		unsigned 			default '1',
  `from`				varchar(10)		  						default NULL,
  `category`		int(4)				unsigned			default '1',
  `server`			varchar(20)		  						default 'local',
  PRIMARY KEY  (`id`),
  INDEX `ccf`(`created`,`category`,`from`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

CREATE TABLE `typecho_album_local` (
  `id` 					int(10) 			unsigned 			NOT NULL 			auto_increment,
  `iid` 				int(10) 			unsigned 			NOT NULL,
  `tid` 				int(10) 			unsigned 			default NULL,
  `pid` 				int(10) 			unsigned 			default NULL,
  `title`				text,
  `category`		int(4)				unsigned			default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `iid` (`iid`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

CREATE TABLE `typecho_album_shoot` (
  `id` 						int(10) 			unsigned 			NOT NULL 			auto_increment,
  `iid` 					int(10) 			unsigned 			NOT NULL,
  `pid` 					int(10) 			unsigned 			default NULL,
  `camera`				text,
  `lens`					text,
  `aperture`			varchar(32)									default NULL,
  `shutterSpeed`	varchar(32)									default NULL,
  `focalLength`		varchar(32)									default NULL,
  `focalLength35mmFilm`	varchar(32)						default NULL,
  `ISO`						varchar(32)									default NULL,
  `time`					text,
  `category`			int(4)				unsigned			default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `iid` (`iid`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

CREATE TABLE `typecho_album_network` (
  `id` 					int(10) 			unsigned 			NOT NULL 			auto_increment,
  `iid` 				int(10) 			unsigned 			NOT NULL,
	`description`	text,
  `category`		int(4)				unsigned			default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `iid` (`iid`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

CREATE TABLE `typecho_album_category` (
  `id` 					int(10) 			unsigned 			NOT NULL 			auto_increment,
  `name`				varchar(50)									default '默认分类',
  `public`			TINYINT(1) 		unsigned 			default '1',
  `description`	text,
  `count`				int(10) 			unsigned 			default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

CREATE TABLE `typecho_album_count` (
  `id` 					int(10) 			unsigned 			NOT NULL 			auto_increment,
  `total`				int(10) 			unsigned 			default '0',
  `local`				int(10) 			unsigned 			default '0',
  `shoot`				int(10) 			unsigned 			default '0',
  `network`			int(10) 			unsigned 			default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;