<body>
<h1>Your Cart</h1>
<!-- TO DO: make sure this returns to store  -->
<a href="index.php">Back to store</a>
<br></br>
<table style="width:100%">
  <tr>
    <td height="55"><b>Product</b></td>
    <td><b>Amount</b></td>
    <td><b>Cost</b></td>
    <td></td>
  </tr>

  <?php
    //initialize connection with database
    include ('./my_connect.php');
    $mysqli = get_mysqli_conn();

    //initialize variables
    $total = 0;
    $shipping = 5.99;

    //displays cart and totals of users
    function displayCart() {
      global $mysqli;
      global $total;
      global $shipping;

      //query that collects and outputs items from cart
      $sql = "SELECT p.product_name, p.price, c.product_amount, c.c_price, c.product_id "
              . "FROM cart c "
              . "NATURAL JOIN product p";

      // Prepared statement\
      $stmt = $mysqli->prepare($sql);

      // execute statement
      $stmt -> execute();

      // Bind result variables
      $stmt -> bind_result($prod_name, $prod_price, $prod_amount, $cart_price, $product_id);

      //outputs all the results from the cart into table
      //adds delete button that can update cart
      while ($stmt->fetch())
      {
        echo '<tr><td height = "45">'.$prod_name.'<br>'.$prod_price.'</td>';
        echo '<td>';
        selectMenu($product_id, $prod_amount);
        echo '</td>';
        echo '<td>$'.$cart_price.'</td>';
        echo '<td>
          <form = "" method = "post">
                <button name = "delete" type = "submit" value = '.$product_id.'>X</button>
                <br/>
          </form>
          </td</tr>';
      }

      echo '</table><br><br>';

      //checks if user earned free shipping by spending over $30 on cakes OR cookies
      freeShippingCakeOrCookies();

      //calculates total;
      total();

      //if total is over $50 gives customer free shipping
      if($total>50)
      {
        $shipping = 0;
        echo '<p>You qualify for free shipping because you spent over $50!</p>';
      }

      echo '<p style = "font-size:110%;"><b>Subtotal: </b>'.$total.'</p>';

      //adds shipping to total
      $total += $shipping;

      echo '<p style = "font-size:110%;"><b>Shipping: </b>'.$shipping.'</p>';
      echo '<p style = "font-size:130%;"><strong>Total: </strong>'.$total.'</p>';

      //TO DO: link to checkout page
      echo '<form action = "other.php">
                 <button type = "submit">Checkout</button>
           </form>';
    }

    //menu that generates drop down menu for cart amount
    function selectMenu($prod_id, $amount)
    {
      echo '<form method="post">
            <select name="amount" onchange="this.form.submit()">';
      for ($i=1; $i<=30; $i++)
        {
          if($i != $amount)
          {
            echo '<option value='.$i.'|'.$prod_id.'>'.$i.'</option>';
          }
          else
          {
            echo '<option selected="selected" value='.$i.'>'.$i.'</option>';
          }
        }
        echo '</select>
              </form>';
    }

    //checks database to get total cost
    function total()
    {
      global $total;
      global $mysqli;

      $sql = "SELECT SUM(c_price) FROM cart";

      $stmt = $mysqli->prepare($sql);

      $stmt -> execute();

      $stmt -> bind_result($sum);
      while ($stmt->fetch())
      {
        $total += $sum;
      }
    }

    //checks if user gets free shipping by grouping total cost in database by type
    //if it is not null, user will receive free shipping
    function freeShippingCakeOrCookies()
    {
      global $shipping;
      global $mysqli;

      //returns dessert category where customer has spent over $30
      $sql = "SELECT p.dessert_category, SUM(c_price) "
             . "FROM cart c JOIN product p USING(product_id) "
             . "GROUP BY p.dessert_category "
             . "HAVING SUM(c_price) >= 30";

      $stmt = $mysqli->prepare($sql);

      $stmt -> execute();

      $stmt -> bind_result($dessert_cat, $total);
      while ($stmt->fetch())
      {
        if($shipping > 0){
          echo '<p>You qualify for free shipping because you spent over $30 on '.$dessert_cat.'s!</p>';
          $shipping = 0;
        }
        else {
           echo '<p>You also spent over $30 on '.$dessert_cat.'s!</p>';
        }
      }

    }

    //deletes an item from the cart
    function deleteItem()
    {
      global $mysqli;
      $sql = "DELETE FROM cart "
            ."WHERE product_id = ?";
      // Prepared statement, stage 1: prepare
      $stmt = $mysqli->prepare($sql);

      $prod_id = $_POST["delete"];

      $stmt-> bind_param('s', $prod_id);
      // Prepared statement, stage 2: execute

      $stmt -> execute();
      header("Refresh:0");
    }

    //updates the amount of a product in a cart
    function updateAmount($product_id, $newAmount)
    {
      global $mysqli;

      //updates amount by setting new amount and calculating the price
      $sql = "UPDATE cart "
              ."SET product_amount =?, "
              ."c_price = (SELECT price "
                  ."FROM product "
                  ."WHERE product_id = ?) * ? "
                  ."WHERE product_id = ?";
      // Prepared statement, stage 1: prepare
      $stmt = $mysqli->prepare($sql);

      $stmt-> bind_param('ssss', $newAmount,$product_id, $newAmount, $product_id);

      // Prepared statement, stage 2: execute

      $stmt -> execute();
      parent.window.location.reload();
    }

    displayCart();

    //checks if delete button is pressed
    if(array_key_exists('delete',$_POST)){
        deleteItem();
    }

    //checks if amount selection is changed
    if(isset($_POST['amount'])){
         $result = $_POST['amount'];
         $result_explode = explode('|', $result);
         updateAmount($result_explode[1], $result_explode[0]);
         // header("Refresh: 1");
      }
  ?>
</body>
