<html>
<head>
<title>Layers Bakery</title>
</head>
<body>

<p> Customer Form </p>

<form action="confirmation.php" method="post">

<label for = "username" > Username: </label>
<input type = "text" name= "username"><br>

<label for = "cust_name" > Name: </label>
<input type = "text" name= "cust_name"><br>

<label for = "email" > Email Address: </label>
<input type = "text" name= "email"><br>

<label for = "address" > Home Address: </label>
<input type = "text" name= "address"><br>

<label for = "number" > Phone Number: </label>
<input type = "text" name= "number"><br>

<label for = "order_id" > Order id: </label>
<input type = "text" name= "order_id"><br>

<p><input type="submit" name="submit" class="button" value="Submit" /></p>

</form>

<?php

include('./my_connect.php');

$mysqli = get_mysqli_conn();


$sql = "INSERT INTO customers (username, name, email, address, phone_number) "
    ."VALUES (?,?,?,?,?,?)";

$stmt = $mysqli->prepare($sql);

$username = $POST_["username"];
$cust_name = $_POST["cust_name"];
$email = $_POST["email"];
$address = $_POST["address"];
$number = $_POST["number"];
$order_id = $_POST["order_id"];

$stmt->bind_param('ssssss',$username, $cust_name, $email, $address, $number, $order_id);
$stmt->execute();

?>

</html>
