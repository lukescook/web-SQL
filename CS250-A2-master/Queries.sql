#Summary
SELECT Users.UserID, Users.Country, COUNT(Distinct Orders.OrderID) AS OrderAmount, SUM(Trans.Quantity) AS BookCount
FROM Orders
INNER JOIN Trans ON Orders.OrderID = Trans.OrderID
INNER JOIN Users ON Orders.UserID = Users.UserID
WHERE Users.UserID = {$user_id}
GROUP BY Orders.UserID;

#Orders By ID
SELECT Orders.OrderID 
FROM Orders
WHERE UserID = {$user_id};

#Orders summary by id
SELECT Orders.OrderID, Orders.Year, Orders.Totalpay,  SUM(Books.Unitprice * Trans.Quantity) - Orders.TotalPay AS Discount
FROM Trans
INNER JOIN Books ON Trans.ISBN = Books.ISBN
INNER JOIN Orders ON Trans.OrderID = Orders.OrderID
WHERE Orders.UserID = {$user_id}
GROUP BY Trans.OrderID
ORDER BY Orders.Year DESC;


#List of books in orders by id
SELECT  Trans.OrderID, Books.Title, Books.ISBN, Books.Unitprice, Books.Genre, Books.ImageURL, AVG(Bookratings.Rating) AS AvgRating, Trans.Quantity, Trans.Quantity * Books.Unitprice AS Total
FROM Trans
LEFT JOIN Books ON Trans.ISBN = Books.ISBN
LEFT JOIN Bookratings ON Books.ISBN = Bookratings.ISBN
WHERE Trans.OrderID = {$order_id}
GROUP BY Books.ISBN
ORDER BY Books.Genre ASC;
