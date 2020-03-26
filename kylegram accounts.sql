create database if not exists `kylegram`;
use `kylegram`;

drop table if exists accounts;
create table accounts (
`id` int(11) not null auto_increment,
`name` varchar(15) not null default '',
`password` varchar(255) default null,
`permission` tinyint(1) unsigned not null default '0',
`loggedin` tinyint(1) unsigned not null default '0',
`created` timestamp,
`ip` text,
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
);

drop table if exists images;
create table images (
`author` varchar(15) not null,
`name` varchar(255) not null,
`encodedname` varchar(255) not null,
`description` varchar(255) not null default '',
`likes` int unsigned not null default '0',
`imgdir` varchar(255) not null,
`created` timestamp
)