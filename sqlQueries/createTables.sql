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
mass integer,
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


--===============================================================
--!!!The rest of the code below is for testing the tables!!!
--===============================================================


/*---------------+
|INSERT TEST ROWS| 
*/---------------+

--I inserted the mother and father values with a ton of substrings because something similar will have to be done upon csv upload.
-- So it was just to practice the concept and get it working.
INSERT INTO Cricket VALUES 
(1,
'AD_CS2_134_132_23',
CAST(SUBSTR('AD_CS2_134_132_23', INSTR('AD_CS2_134_132_23', '_', 1, 2) + 1, INSTR('AD_CS2_134_132_23', '_', 1, 3) - INSTR('AD_CS2_134_132_23', '_', 1, 2) - 1) AS INTEGER), CAST(SUBSTR('AD_CS2_134_132_23', INSTR('AD_CS2_134_132_23', '_', 1, 3) + 1, INSTR('AD_CS2_134_132_23', '_', 1, 4) - INSTR('AD_CS2_134_132_23', '_', 1, 3) - 1) AS INTEGER));
INSERT INTO Cricket VALUES 
(2,
'AD_CS2_102_114_81',
CAST(SUBSTR('AD_CS2_102_114_81', INSTR('AD_CS2_102_114_81', '_', 1, 2) + 1, INSTR('AD_CS2_102_114_81', '_', 1, 3) - INSTR('AD_CS2_102_114_81', '_', 1, 2) - 1) AS INTEGER), CAST(SUBSTR('AD_CS2_102_114_81', INSTR('AD_CS2_102_114_81', '_', 1, 3) + 1, INSTR('AD_CS2_102_114_81', '_', 1, 4) - INSTR('AD_CS2_102_114_81', '_', 1, 3) - 1) AS INTEGER));

INSERT INTO Observer VALUES (1, 'JD');
INSERT INTO Observer VALUES (2, 'AW');

INSERT INTO Project VALUES (1, 'AD');
INSERT INTO Project VALUES (2, 'PROJ2');

INSERT INTO Test VALUES (1, 'OF', 1);
INSERT INTO Test VALUES (2, 'AP', 1);

INSERT INTO TestInstance VALUES 
(1, 2, '9:52', null, 1, 1, 27, TO_DATE('8/4/2016', 'MM/DD/YYYY'), 1, '');
INSERT INTO TestInstance VALUES 
(2, 2, '9:52', null, 1, 2, 27, TO_DATE('8/4/2016', 'MM/DD/YYYY'), 1, '');

INSERT INTO CricketTestInstance VALUES (1, 1, 1);
INSERT INTO CricketTestInstance VALUES (2, 2, 2);

/*------+
|QUERIES| 
*/------+

-- SELECT * from all tables to test
SELECT * FROM Cricket;
SELECT * FROM Observer;
SELECT * FROM Project;
SELECT * FROM Test;
SELECT * FROM TestInstance;
SELECT * FROM CricketTestInstance;

-- Do a join that makes it all look like the example csv
SELECT c.name AS Id, t.tName AS Test, c.mother AS Mom, c.father AS Dad, ti.recordingTime AS testTime, ti.mass, ti.rep, ti.arena, ti.temp, ti.recordingDate AS testDate, o.oName AS Observer, ti.status
FROM TestInstance ti
LEFT JOIN CricketTestInstance cti ON ti.ID = cti.testInstanceID
INNER JOIN Cricket c ON cti.cricketID = c.ID
LEFT JOIN Test t ON ti.testID = t.ID
INNER JOIN Project p ON t.projectID = p.ID
LEFT JOIN Observer o ON ti.observerID = o.ID;
