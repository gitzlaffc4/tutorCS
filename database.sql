 
-- drop the table favmovires if it already exists
DROP TABLE IF EXISTS ________;


-- create a table to hold information on favorite movies
-- for all the column/variables that will be eventually sent to the web app,
CREATE TABLE _________(
    id INT NOT NULL AUTO_INCREMENT,
	title VARCHAR(250) NOT NULL,
	director VARCHAR(150) NOT NULL,
	relyear INT NOT NULL,
	trailer VARCHAR(500),
	PRIMARY KEY (id)
 );