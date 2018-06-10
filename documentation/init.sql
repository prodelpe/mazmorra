/* 
 * Base de datos inicial de la Mazmorra del Androide
 */
/**
 * Author:  Pau
 * Created: 20-may-2018
 */

CREATE DATABASE mazmorra;

CREATE TABLE mazmorra.books (
id INT(6) AUTO_INCREMENT PRIMARY KEY,
idAPI INT(20) NOT NULL,
title VARCHAR(100) NOT NULL,
authors VARCHAR(50) NOT NULL,
image VARCHAR(300),
textSnippet TEXT,
description TEXT
);

CREATE TABLE mazmorra.users (
id INT(6) AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(40) NOT NULL,
password VARCHAR(20) NOT NULL,
nickname VARCHAR(20),
bio TEXT,
image VARCHAR(300)
);

CREATE TABLE mazmorra.userbooks (
id INT(6) AUTO_INCREMENT PRIMARY KEY,
userId INT(6) ,
bookId INT(6) ,
FOREIGN KEY (userId) REFERENCES users(id),
FOREIGN KEY (bookId) REFERENCES books(id)
);

CREATE TABLE mazmorra.usermessages (
id INT(6) AUTO_INCREMENT PRIMARY KEY,
userId INT(6),
userTold INT(6),
title VARCHAR(255),
message TEXT,
dateSent TIMESTAMP,
messageRead TINYINT(1),
deleted TINYINT(1),
FOREIGN KEY (userId) REFERENCES users(id)
);

CREATE TABLE mazmorra.reviews (
id INT(6) AUTO_INCREMENT PRIMARY KEY, 
userId INT(6),
bookId INT(6),
title VARCHAR(255),
comment TEXT, 
rating  TINYINT(1),
FOREIGN KEY (userId) REFERENCES users(id),
FOREIGN KEY (bookId) REFERENCES books(id)
);