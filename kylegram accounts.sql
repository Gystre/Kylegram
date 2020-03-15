create database if not exists `kylegram`;
use `kylegram`;

drop table if exists accounts;
create table accounts (
`id` int(11) not null auto_increment,
`name` varchar(15) NOT NULL DEFAULT '',
`password` varchar(255) DEFAULT NULL,
`loggedin` tinyint(1) unsigned NOT NULL DEFAULT '0',
`created` timestamp,
`ip` text,
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
);

drop table if exists images;
create table images (
`id` int(11) not null auto_increment,
`author` varchar(15) not null,
`name` varchar(255) not null,
`description` varchar(255) not null default '',
`imgdir` varchar(255) not null,
`created` timestamp,
PRIMARY KEY (`id`)
)