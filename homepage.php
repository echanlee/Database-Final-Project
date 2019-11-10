<html>
<head>
<title>Layers Bakery</title>
</head>
<body>

<h1>Welcome to Layers!</h1>
<p>We strive to bake a difference one baked good at a time!</p>
<p>hellow are you working</p>


<p> Our products: </p>

<?php

$category = "unfiltered";
echo '<form action = "product.php" type = "submit" method = "post">';
echo '<label for="product_name">Search for your product</label>';
echo '<input type="text" name="product_name">';
echo '<input type="submit" value="Continue">';
echo '</form>';

include('./my_connect.php');
$mysqli = get_mysqli_conn();

function dessertAmount() {
  global $mysqli;
  $sql = "SELECT dessert_category, COUNT(*) "
      ."FROM product "
      ."GROUP BY dessert_category";

  $stmt = $mysqli->prepare($sql);
  $stmt->execute();
  $stmt->bind_result($categoryName,$itemsInEach);
  while ($stmt->fetch())
  {
      echo '<form action = "" type = "submit" method = "post">
          <button name = "category" value = '.$categoryName.'>'.$categoryName.'('.$itemsInEach.')</button>
          </form>';
  }
  echo '<form action = "" type = "submit">
          <button name = "noFilter" value = "">No Filter</button>
          </form>';
}

function categoryChange() {
  global $mysqli;
  echo "hhere";
  $sql = "SELECT product_id, product_name, price "
      ."FROM product "
      ."WHERE dessert_category = ?";
  echo "after query cat";
  // (4a) Prepared statement, stage 1: prepare

  $stmt = $mysqli->prepare($sql);


  $selectedCategory = $_POST["category"];

  $stmt->bind_param("s",$selectedCategory);

  // (5) Execute prepared statement
  $stmt->execute();

  // (6) Bind selected columns to PHP variables
  $stmt->bind_result($product_id, $product_name, $price);

  while ($stmt->fetch())
  {
      echo '<ul>
      <form action = "product.php" type = "submit" method = "post">
      <button name = "product" value = '.$product_id.'>'.$product_name.'</button>
      </form>
      </ul>';
  }
}

dessertAmount();

if (array_key_exists('category',$_POST)){
        echo "hello";
        categoryChange();
}

if (array_key_exists('noFilter',$_POST)){
    header('Refresh:0');
}
}
$stmt->close();
$mysqli->close();
?>

<br>
</form>
</body>
</div>
</div>

</body>
</html>
