-- SQL File for tutorCS


-- DROP THE TABLE SCHEDULED_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS SCHEDULED_T;

-- DROP THE TABLE SESSION_T IF IT ALREADY EXISTS 
DROP TABLE IF EXISTS SESSION_T;


-- DROP THE TABLE MATERIALS_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS MATERIALS_T;

-- DROP THE TABLE TEACHES_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS TEACHES_T;

-- DROP THE TABLE TUTORS_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS TUTORS_T;

-- DROP THE TABLE ENROLLED_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS ENROLLED_T;

-- DROP THE TABLE COURSE_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS COURSE_T;

-- DROP THE TABLE TUTOR_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS TUTOR_T;

 -- DROP THE TABLE STUDENT_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS STUDENT_T;

-- DROP THE TABLE USER_T IF IT ALREADY EXISTS
DROP TABLE IF EXISTS USER_T;

-- CREATE A TABLE TO HOLD INFORMATION ON USERS
-- HAWKID IS THE PRIMARY KEY
-- FIRSTNAME, LASTNAME, EMAIL, AND ROLL CAN NOT BE NULL
-- BR: A USER MUST BE AT LEAST A STUDENT, PROFESSOR, TUTOR, OR ADMIN
-- BR: A USER MAY BE A TUTOR AND A STUDENT FOR DIFFERENT COURSES
-- BR: A USER MAY BE A PROFESSOR FOR A COURSE AND AN ADMIN FOR THE SYSTEM
-- BR: A USER CAN NOT BE A (STUDENT OR TUTOR) AND A (PROFESSOR OR ADMIN)
CREATE TABLE USER_T(
    HAWKID VARCHAR(255) NOT NULL,
	FIRSTNAME VARCHAR(255) NOT NULL,
	LASTNAME VARCHAR(255) NOT NULL,
	EMAIL VARCHAR(255) NOT NULL,
	STUDENT BIT NOT NULL,
	TUTOR BIT NOT NULL,
	PROFESSOR BIT NOT NULL,
	ADMIN BIT NOT NULL,
	NICK_NAME VARCHAR(255),
	PHONE_NUMBER INT(10),
PRIMARY KEY (HAWKID));
 
-- CREATE A TABLE TO HOLD INFORMATION ON STUDENTS
-- HAWKID IS THE PRIMARY KEY AND FORGEIN KEY TO USER_T
-- ALLOC_SESSIONS CAN NOT BE NULL
CREATE TABLE STUDENT_T(
    HAWKID VARCHAR(255) NOT NULL,
	ALLOC_SESSIONS INT(2) NOT NULL,
PRIMARY KEY (HAWKID),
FOREIGN KEY (HAWKID) REFERENCES USER_T(HAWKID));

-- CREATE A TABLE TO HOLD INFORMATION ON TUTORS
-- HAWKID IS THE PRIMARY KEY AND FORGEIN KEY TO USER_T
CREATE TABLE TUTOR_T(
    HAWKID VARCHAR(255) NOT NULL,
	AVAILABILITY INT(2) NOT NULL, -- NEED TO ASK SEGRE AT NEXT MEETING BEST WAY TO STORE THIS INFORMATION
PRIMARY KEY (HAWKID),
FOREIGN KEY (HAWKID) REFERENCES USER_T(HAWKID));

-- CREATE A TABLE TO HOLD INFORMATION ON COURSES
-- COURSE_ID IS THE PRIMARY KEY
-- COURSE_NAME AND SEMESTER CAN NOT BE NULL
-- BR: A COURSE CAN REPEAT IN MULTIPLE SEMESTERS
CREATE TABLE COURSE_T(
	COURSE_ID INT(8) NOT NULL AUTO_INCREMENT,
	COURSE_NAME VARCHAR(255) NOT NULL,
	SEMESTER VARCHAR(12) NOT NULL,
PRIMARY KEY (COURSE_ID));

-- CREATE A TABLE TO HOLD INFORMATION ON STUDENTS THAT ARE ENROLLED IN ONE OR MANY COURSES
-- COURSE_ID AND HAWKID ARE PRIMARY KEYS
-- BR: A STUDENT MUST BE ENROLLED IN AT LEAST ONE COURSE
CREATE TABLE ENROLLED_T(
	HAWKID VARCHAR(255) NOT NULL,
	COURSE_ID INT(8) NOT NULL,
PRIMARY KEY (HAWKID, COURSE_ID),
FOREIGN KEY (HAWKID) REFERENCES (STUDENT_T),
FOREIGN KEY (COURSE_ID) REFERENCES (COURSE_T));

-- CREATE A TABLE TO HOLD INFORMATION ON WHICH COURSES A TUTOR TUTORS FOR
-- COURSE_ID AND HAWKID ARE PRIMARY KEYS
-- BR: A TUTOR MUST TUTOR AT LEAST ONE COURSE
CREATE TABLE TUTORS_T(
	HAWKID VARCHAR(255) NOT NULL,
	COURSE_ID INT(8) NOT NULL,
PRIMARY KEY (HAWKID, COURSE_ID),
FOREIGN KEY (HAWKID) REFERENCES (TUTOR_T),
FOREIGN KEY (COURSE_ID) REFERENCES (COURSE_T));

-- CREATE A TABLE TO HOLD INFORMATION ON WHICH COURSES A PROFESSOR TEACHES
-- COURSE_ID AND HAWKID ARE PRIMARY KEYS
-- BR: A PROFESSOR MUST TEACH AT LEAST ONE COURSE
CREATE TABLE TEACHES_T(
	HAWKID VARCHAR(255) NOT NULL,
	COURSE_ID INT(8) NOT NULL,
PRIMARY KEY (HAWKID, COURSE_ID),
FOREIGN KEY (HAWKID) REFERENCES (USER_T),
FOREIGN KEY (COURSE_ID) REFERENCES (COURSE_T));

-- CREATE A TABLE THAT HOLDS MATERIALS THAT ARE USED FOR A COURSE
-- MATERIAL_ID IS THE PRIMARY KEY
-- COURSE_ID FOREIGN KEY TO COURSE_T
-- MATERIAL_NAME IS NOT NULL
-- BR: A MATERIAL ITEM MUST BE ASSIGNED WITH ONE COURSE
CREATE TABLE MATERIALS_T(
	MATERIAL_ID INT(10) NOT NULL AUTO_INCREMENT,
	COURSE_ID INT(8) NOT NULL,
	MATERIAL_NAME VARCHAR(255) NOT NULL,
	MATERIAL_DESC TEXT,
	FILE VARBINARY,
PRIMARY KEY (MATERIAL_ID),
FOREIGN KEY (COURSE_ID) REFERENCES (COURSE_T));


-- CREATE A TABLE THAT HOLDS INFORMATION OF AVAILABLE TUTORING SESSIONS THAT AVAILABLE
-- SESSION_ID IS THE PRIMARY KEY
-- TUTOR_HAWKID IS FORGEIN KEY FROM TUTOR_T
-- COURSE_ID IS A FORGEIN KEY FROM COURSE_T
-- ALL ATTRIBUTES ARE NOT NULL AS THEY ARE ALL REQUIRED TO CREATE A SESSION
-- BR: A SESSION MUST BE FOR ONE COURSE
-- BR: A SESSION MUST HAVE ONE TUTOR
-- BR: A TUTOR MUST CREATE AT LEAST ONE OPEN SESSION
-- BR: A SESSION END TIME MUST BE 20 MINUTES AFTER START
CREATE TABLE SESSION_T(
	SESSION_ID INT(6) NOT NULL AUTO_INCREMENT,
	TUTOR_HAWKID VARCHAR(255) NOT NULL,
	COURSE_ID INT(8) NOT NULL,
	SESSION_DATE DATE NOT NULL,
	SESSION_START_TIME TIME NOT NULL,
	SESSION_END_TIME TIME NOT NULL,
	SCHEDULED BIT NOT NULL,
PRIMARY KEY (SESSION_ID),
FOREIGN KEY (TUTOR_HAWKID) REFERENCES (TUTOR_T),
FOREIGN KEY (COURSE_ID) REFERENCES (COURSE_T));

-- CREATE A TABLE THAT HOLDS SCHEDULED TUTORING SESSIONS AND KEEPS TRACK OF IF THEY WHERE COMPLETED OR CANCELED AND BY WHOM
-- BR: A SCHEDULED SESSION MUST BE WITH ONE STUDENT
-- BR: A STUDENT CAN HAVE MANY SCHEDULED SESSIONS
-- BR: A SESSION MUST HAVE A MATERIAL_ID OR A COMMENT SUBMITTED BY STUDENT
--
CREATE TABLE SCHEDULED_T(
	SESSION_ID INT(6) NOT NULL,
	STUD_HAWKID VARCHAR(255) NOT NULL,
	MATERIAL_ID INT(10),
	COMPLETED BIT NOT NULL,
	CANC_BY_TUT BIT NOT NULL,
	CANC_BY_STUD BIT NOT NULL,
	COMMENTS TEXT,
PRIMARY KEY (SESSION_ID),
FOREIGN KEY (SESSION_ID) REFERENCES (SESSION_T),
FOREIGN KEY (STUD_HAWKID) REFERENCES (STUDENT_T),
FOREIGN KEY (MATERIAL_ID) REFERENCES (MATERIAL_T));