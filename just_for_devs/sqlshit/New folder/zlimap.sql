drop database if exists zliMap;
create database zliMap;
use zliMap;

create table tbladmin (
	id int(4) unsigned not null auto_increment,
	username varchar(30) not null,
	password varchar(30) not null,
    	primary key(id)
);

create table tblPlz (
	plz_id bigint(6) unsigned not null auto_increment,
	plz smallint(4) unsigned not null,
	ort varchar(40) not null,
	longitude double signed not null,
	latitude double signed not null,

	primary key(plz_id)
);

create table tblDude (
	id bigint(6) unsigned not null auto_increment,
	plz_id bigint(6) unsigned not null,
	nachname	varchar(50) not null,
	vorname		varchar(50) not null,

	primary key(id),
	foreign key(plz_id) references tblPlz(plz_id) on update cascade on delete cascade
);

CREATE TABLE tblsuperadmin (
	id INT(4) NOT NULL AUTO_INCREMENT, 
	email VARCHAR(50) NOT NULL, 
	name VARCHAR(30) NOT NULL, 
	nachname VARCHAR(30) NOT NULL, 
	benutzername VARCHAR(30) NOT NULL, 
	password VARCHAR(5000) NOT NULL, 
	PRIMARY KEY (`id`)
	) ENGINE = InnoDB;