SELECT Orders.OrderID, Orders.UserID, SUM(Books.Unitprice * Trans.Quantity) - Orders.TotalPay AS Discount
FROM Trans
INNER JOIN Books ON Trans.ISBN = Books.ISBN
INNER JOIN Orders ON Trans.OrderID = Orders.OrderID
GROUP BY Trans.OrderID 
HAVING Discount > 0
ORDER BY Discount DESC;
