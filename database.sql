 
-- drop the table USER_T if it already exists
DROP TABLE IF EXISTS USER_T;


-- create a table to hold information on users
-- hawkid is the primary key
-- firstname, lastname, email, and roll can not be null
CREATE TABLE USER_T(
    hawkid VARCHAR(255) NOT NULL,
	firstname VARCHAR(255) NOT NULL,
	lastname VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	student BIT NOT NULL,
	tutor BIT NOT NULL),
	professor BIT NOT NULL,
	admin BIT NOT NULL,
	nick_name VARCHAR(255),
	phone_number INT(10),
PRIMARY KEY (hawkid));
 
 
 
 
 -- AUTO_INCREMENT 