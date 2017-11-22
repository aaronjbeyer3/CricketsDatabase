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
(1, 2, '9:52', '', 1, 1, 27, TO_DATE('8/4/2016', 'MM/DD/YYYY'), 1, '');
INSERT INTO TestInstance VALUES 
(2, 2, '9:52', '', 1, 2, 27, TO_DATE('8/4/2016', 'MM/DD/YYYY'), 1, '');

INSERT INTO CricketTestInstance VALUES (1, 1, 1);
INSERT INTO CricketTestInstance VALUES (2, 2, 2);

-- SELECT * from all tables to test
SELECT * FROM Cricket;
SELECT * FROM Observer;
SELECT * FROM Project;
SELECT * FROM Test;
SELECT * FROM TestInstance;
SELECT * FROM CricketTestInstance;