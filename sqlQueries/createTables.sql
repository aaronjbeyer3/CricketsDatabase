/*------------+
|CREATE TABLES| 
*/------------+

CREATE TABLE Cricket
( ID integer NOT NULL,
name varchar(30) NOT NULL,
mother integer,
father integer,  
PRIMARY KEY (ID));

CREATE TABLE Observer
( ID integer NOT NULL,
oName varchar(20) NOT NULL,  
PRIMARY KEY (ID));

CREATE TABLE Project
( ID integer NOT NULL,
projName varchar(20) NOT NULL,  
PRIMARY KEY (ID));

CREATE TABLE Test
( ID integer NOT NULL,
tName varchar(20) NOT NULL,
projectID integer,  
PRIMARY KEY (ID),
FOREIGN KEY (projectID) REFERENCES Project(ID) ON DELETE CASCADE);

CREATE TABLE TestInstance
( ID integer NOT NULL,
testID integer NOT NULL,
recordingTime varchar(5),
mass varchar(10),
rep integer NOT NULL,
arena integer NOT NULL,
temp integer,
recordingDate date,
observerID integer,
status varchar(10),  
PRIMARY KEY (ID),
FOREIGN KEY (testID) REFERENCES Test(ID) ON DELETE CASCADE,
FOREIGN KEY (observerID) REFERENCES Observer(ID) ON DELETE CASCADE);

CREATE TABLE CricketTestInstance
( ID integer NOT NULL,
cricketID integer NOT NULL,
testInstanceID integer NOT NULL,  
PRIMARY KEY (ID),
FOREIGN KEY (cricketID) REFERENCES Cricket(ID) ON DELETE CASCADE,
FOREIGN KEY (testInstanceID) REFERENCES TestInstance(ID) ON DELETE CASCADE);
