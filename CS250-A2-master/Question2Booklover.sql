SELECT Bookratings.UserID, COUNT(Bookratings.Rating) AS Rated, AVG(Bookratings.Rating) AS AvgRating
FROM Bookratings
GROUP BY Bookratings.UserID
HAVING Rated > 100
ORDER BY AvgRating DESC;


SELECT UserID, AvgRating
FROM (
SELECT UserID, COUNT(Rating) AS Rated, AVG(Rating) AS AvgRating
FROM Bookratings
GROUP BY UserID
HAVING Rated > 100
ORDER BY AvgRating DESC) as P
WHERE NOT EXISTS (
SELECT UserID, AvgRating
FROM P as A
WHERE AvgRating > P.AvgRating);