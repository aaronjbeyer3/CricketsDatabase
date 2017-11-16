-- Do a join that makes it all look like the example csv
SELECT c.name AS Id, t.tName AS Test, ti.recordingTime AS testTime, ti.mass, ti.rep, ti.arena, ti.temp, ti.recordingDate, o.oName AS Observer, ti.status
FROM TestInstance ti
LEFT JOIN CricketTestInstance cti ON ti.ID = cti.testInstanceID
INNER JOIN Cricket c ON cti.cricketID = c.ID
LEFT JOIN Test t ON ti.testID = t.ID
INNER JOIN Project p ON t.projectID = p.ID
LEFT JOIN Observer o ON ti.observerID = o.ID;
--WHERE ...

--Using different criteria in the WHERE will be the different download options.