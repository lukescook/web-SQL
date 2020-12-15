<html>
<head>
    <title>Better Amazon</title>
    <style>

        .html,body {
            width: 710px;
        }

        .header {
            background-color: lightgray;
        }

        #orderTable {
            width: 100%;
            border:0;
        }

        #idPrompt {
            margin-top: 15px;
            width: 100%;
        }
        
    </style>
</head>
<body>
    <header>
        <meta charset="UTF-8" />
        <img src="Data/amazon_topbanner.png" />
    </header>  


    <table id="orderTable">
        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $user_id = $_GET['user_id'];
            $conn = null;

            try
            {
                //User Summary
                $conn = new PDO("mysql:host={$servername}; dbname=CS250", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT Users.UserID, Users.Country, COUNT(Trans.OrderID) AS OrderAmount, SUM(Trans.Quantity) AS BookCount
							        FROM Orders
							        INNER JOIN Trans ON Orders.OrderID = Trans.OrderID
							        INNER JOIN Users ON Orders.UserID = Users.UserID
							        WHERE Users.UserID = {$user_id}
							        GROUP BY Orders.UserID;");
                if($stmt->execute()){
                    $details = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo format_user_details($details);
                }


                //Get order headers
                $stmt = $conn->prepare("SELECT Orders.OrderID, Orders.Year, Orders.Totalpay,  SUM(Books.Unitprice * Trans.Quantity) - Orders.TotalPay AS Discount
                                FROM Trans
                                INNER JOIN Books ON Trans.ISBN = Books.ISBN
                                INNER JOIN Orders ON Trans.OrderID = Orders.OrderID
                                WHERE Orders.UserID = {$user_id}
                                GROUP BY Trans.OrderID
                                ORDER BY Orders.Year DESC");
                if($stmt->execute()){
                    while($header_row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        echo format_header_row($header_row);
                        //Get orders by ID
                        $order_id = $header_row["OrderID"];
                        $inner_stmt = $conn->prepare("SELECT  Trans.OrderID, Books.Title, Books.ISBN, Books.Unitprice, Books.Genre, Books.ImageURL, AVG(Bookratings.Rating) AS AvgRating, Trans.Quantity, Trans.Quantity * Books.Unitprice AS Total
                                             FROM Trans
                                             LEFT JOIN Books ON Trans.ISBN = Books.ISBN
                                             LEFT JOIN Bookratings ON Books.ISBN = Bookratings.ISBN
                                             WHERE Trans.OrderID = {$order_id}
                                             GROUP BY Books.ISBN
                                             ORDER BY Books.Genre ASC;");
                        if($inner_stmt->execute()){
                            $formatted = "";
                            while($order_row = $inner_stmt->fetch(PDO::FETCH_ASSOC)){
                                //echo var_dump($order_row);
                                $formatted .= format_row($order_row);
                            }

                            echo $formatted;
                        }

                    }
                }

            }
            catch(PDOException $e)
            {
                echo "Connection failed: " . $e->getMessage();
            }

        ?>
    </table>
    <footer>
        <img src="Data/amazon_bottombanner.png" />
    </footer>

    <form action="index.php" method="get" id="idPrompt">
        <b>Query</b> Userid:
        <input type="text" name="user_id" value="164288" />
        <input type="submit" />
    </form>
</body>
</html>

<?php

function format_user_details($data){

    $details_row = "<tr>
                        <td colspan=\"2\">
                            User Details: <br>
                            Userid: {$data["UserID"]} <br />
                            Country: {$data["Country"]}
                        </td>
                        <td colspan=\"2\">
                            Orders Summary: <br />
                            {$data["OrderAmount"]} orders placed <br />
                            {$data["BookCount"]} books bought
                        </td>
                    </tr>";
    return $details_row;
}

function format_header_row($data){
    $header_row = "<tr class=\"header\">";
    $header_row .= "<td>Order placed <br> {$data["Year"]}</td>";
    $header_row .= "<td>Total <br> &pound;{$data["Totalpay"]}</td>";
    if(intval($data["Discount"]) > 0){
        $header_row .= "<td>Discount <br> &pound;{$data["Discount"]}</td>";
    } else {
        $header_row .= "<td> </td>";
    }
    $header_row .= "<td>Order ID <br> {$data["OrderID"]}</td>";
    $header_row .= "</tr>";

    return $header_row;
}

function format_row($data){
    $order_row = "<tr><td><img src=\"{$data['ImageURL']}\"></td>";
    $quantity = "";
    if (intval($data['Quantity']) > 1){
        $quantity .= "{$data['Quantity']} of ";
    }
    $order_row .= "<td>{$quantity}{$data['Title']}<br>";
    $order_row .= "{$data['ISBN']}<br>";
    $order_row .= "&pound;{$data['Unitprice']} <br>";
    $order_row .= stars($data["AvgRating"])."</td>";
    $order_row .= "<td>{$data['Genre']}</td>";
    $order_row .= "<td>&pound;{$data['Total']}</td>";
    $order_row .= "</tr>";

    return $order_row;
}

function stars($ammount){
    $stars = "";
    for($i = 0; $i < intval($ammount); $i++){
        $stars .= "*";
    }
    return $stars;
}
?>