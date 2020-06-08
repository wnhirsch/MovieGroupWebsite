CREATE DATABASE MOVIEGROUP CHARACTER SET utf8 COLLATE utf8_general_ci;
-- ALTER DATABASE `OcREbskFYD`CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE Viewer (
	nick VARCHAR(20) NOT NULL,
	password VARCHAR(100) NOT NULL,

	PRIMARY KEY (nick)
);

CREATE TABLE Token (
	token VARCHAR(10) NOT NULL,
	createdAt INT NOT NULL,

	PRIMARY KEY (token)
);

CREATE TABLE Movie (
	id VARCHAR(20) NOT NULL,
	title VARCHAR(200) NOT NULL,
	plot TEXT,
	poster VARCHAR(500),

	year INT,
	runtime VARCHAR(10),
	director VARCHAR(50),
	genre VARCHAR(100),
	actors VARCHAR(500),
	awards VARCHAR(200),

	choosedBy VARCHAR(20) NOT NULL,
	watchAt INT NOT NULL,
	
	PRIMARY KEY (id)
);

CREATE TABLE MovieRate (
	id VARCHAR(20) NOT NULL,
	nick VARCHAR(20) NOT NULL,
	rate FLOAT(2) NOT NULL,
	
	PRIMARY KEY (id, nick)
);

CREATE TABLE CriticRate (
	id VARCHAR(20) NOT NULL,
	source VARCHAR(50) NOT NULL,
	rate FLOAT(2) NOT NULL,
	max FLOAT(2) NOT NULL,
	
	PRIMARY KEY (id, source)
);