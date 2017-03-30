<?php require_once("config.php"); ?>

<?php 

//check if a buy now button has been clicked
if(isset($_GET['add'])){
  
  //get the row of the products table for the product selected
  $query = query("SELECT * FROM products WHERE product_id =" . escape_string($_GET['add']) . " ");
  confirm($query);

  while($row = fetch_array($query)) {
   //Check if the product quantity from the database isn’t equal to the amount saved in the SESSION of the product in the cart. If so add one to the session.
    if($row['product_quantity'] != $_SESSION['product_' . $_GET['add']]) {

      //add the product id to the session
      $_SESSION['product_' . $_GET['add']] +=1;
      redirect("../public/checkout.php");

    } else {
      set_message("We only have " . $row['product_quantity'] . "{$row['product_title']}'s" . " available ");
      redirect("../public/checkout.php");
    } //end if statement

  } //end while
} 


//this checks if the remove button is clicked, so 1 is taken away from the session 
if(isset($_GET['remove'])){
  //grab the session for the product and minus 1
  $_SESSION['product_' . $_GET['remove']]--;

  //if the session value is under 1 then redirect
  if($_SESSION['product_' . $_GET['remove']] < 1){
    unset($_SESSION['item_total']);
    unset($_SESSION['item_quantity']);
    redirect("../public/checkout.php");
  } else {
    redirect("../public/checkout.php");
  }
}


//this will delete a product from the cart
if(isset($_GET['delete'])){

  $_SESSION['product_' . $_GET['delete']] = "0";
  //unset them when deleted so they are removed from cart
  unset($_SESSION['item_total']);
  unset($_SESSION['item_quantity']);
  redirect("../public/checkout.php");
  
}


//responsible for displaying items
function cart(){

  //form variables
  $item_name = 1;
  $item_number = 1;
  $amount = 1;
  $quantity = 1;

  //total cart cost
  $total = 0;
  $item_quantity = 0;
  $sub = 0;

  foreach ($_SESSION as $name => $value) {

    //only show products we have added to the cart, so value would be over 0
    if($value > 0){
      if(substr($name, 0, 8) == "product_"){

        //check the legnth of the id of the session product property, -8 due to the 'product_' being 8 characters which we dont need
        $length = strlen($name - 8);
        //using the name variable, start at character 8 and grab the anount of characters the id is, using the $length variable
        $id = substr($name, 8, $length);

        //loop through database and display products
        $query = query("SELECT * FROM products WHERE product_id =" . escape_string($id) . " ");
        confirm($query);

        while($row = fetch_array($query)) {
          
          //work out sub total. So multiply the price by the amount in the cart
          $sub = $row['product_price'] * $value;

          //amount of items in cart
          $item_quantity += $value;

          $product_image = display_image($row['product_image']);

          $product = <<<DELIMITER
<tr>
  <td>{$row['product_title']}<br>
<img width="100" src="../resources/{$product_image}">
  </td>
  <td>&#36;{$row['product_price']}</td>
  <td>{$value}</td>
  <td>&#36;{$sub}</td>
  <td>
  <a href="../resources/cart.php?remove={$row['product_id']}" class="btn btn-warning"><span class="glyphicon glyphicon-minus"></span></a>    
  <a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></a>
  <a href="../resources/cart.php?delete={$row['product_id']}" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
  </td>
</tr>

<input type="hidden" name="item_name_{$item_name}" value="{$row['product_title']}">
<input type="hidden" name="item_number_{$item_number}" value="{$row['product_id']}">
<input type="hidden" name="amount_{$amount}" value="{$row['product_price']}">
<input type="hidden" name="quantity_{$quantity}" value="{$value}">

DELIMITER;

          echo $product;

          //form variables
          $item_name++;
          $item_number++;
          $amount++;
          $quantity++;

        } //end of while

      //save in a session the total price of cart
      $_SESSION['item_total'] = $total += $sub;
      //save in session the amount of items in cart
      $_SESSION['item_quantity'] = $item_quantity;
      } //end of if statement
    }
  } //end of foreach

}

function cartDropdown(){

    //form variables
  $item_name = 1;
  $item_number = 1;
  $amount = 1;
  $quantity = 1;

  //total cart cost
  $total = 0;
  $item_quantity = 0;
  $sub = 0;
  foreach ($_SESSION as $name => $value) {

    //only show products we have added to the cart, so value would be over 0
    if($value > 0){
      if(substr($name, 0, 8) == "product_"){

        //check the legnth of the id of the session product property, -8 due to the 'product_' being 8 characters which we dont need
        $length = strlen($name - 8);
        //using the name variable, start at character 8 and grab the anount of characters the id is, using the $length variable
        $id = substr($name, 8, $length);

        //loop through database and display products
        $query = query("SELECT * FROM products WHERE product_id =" . escape_string($id) . " ");
        confirm($query);

        while($row = fetch_array($query)) {
          
          //work out sub total. So multiply the price by the amount in the cart
          $sub = $row['product_price'] * $value;

          //amount of items in cart
          $item_quantity += $value;

          $product_image = display_image($row['product_image']);

          $product = <<<DELIMITER
<div class="dd-prod">

  <div class="dd-prod-title">{$row['product_title']}</div>
  <div class="dd-prod-image"><img width="100" src="../resources/{$product_image}"></div>
  <div class="dd-prod-price"><td>Price: &#36;{$row['product_price']}</div>
  <div class="dd-prod-amount">Amount: {$value}</div>
  <div class="dd-prod-price">&#36;{$sub}</div>
  <div class=""><a href="../resources/cart.php?delete={$row['product_id']}" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
  </div>

</div>

DELIMITER;

          echo $product;

        } //end of while

      } //end of if statement
    }
  } //end of foreach
}

function process_transaction(){

//if we have recieved the transaction id
if(isset($_GET['tx'])){
  //save the data in variables
  $amount = $_GET['amt'];
  $currency = $_GET['cc'];
  $transaction = $_GET['tx'];
  $status = $_GET['st'];

  $total = 0;
  $item_quantity = 0;

  foreach ($_SESSION as $name => $value) {

    if($value > 0){
      if(substr($name, 0, 8) == "product_"){

        $length = strlen($name - 8);
        $id = substr($name, 8, $length);

        //insert this data into the orders table
        $send_order = query("INSERT INTO orders (order_amount, order_transaction, order_currency, order_status ) VALUES('{$amount}','{$transaction}','{$currency}','{$status}')");

        //gives us the last inserted id, we can use the function from our functions page
        $last_id = last_id();

        confirm($send_order);

        $query = query("SELECT * FROM products WHERE product_id =" . escape_string($id) . " ");
        confirm($query);

        while($row = fetch_array($query)) {
          
          $sub = $row['product_price'] * $value;
          $item_quantity += $value;
          $product_price = $row['product_price'];
          $product_title = $row['product_title'];

        } //end of while

        $total += $sub;
        //echo $item_quantity;
      
        //insert this data into the reports table
        $insert_report = query("INSERT INTO reports (product_id, order_id, product_title, product_price, product_quantity) VALUES('{$id}','{$last_id}','{$product_title}','{$product_price}','{$value}')");
        confirm($insert_report);

        } 
      }
    } //end of foreac
    session_destroy();
  }  else {
    redirect("index.php");
  }
}

function show_paypal(){

//if there is an item quantity we can return the button
if(isset($_SESSION['item_quantity']) && $_SESSION['item_quantity'] >= 1 ){

$paypal_button = <<<DELIMITER

    <input type="image" name="upload"
    src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
    alt="PayPal - The safer, easier way to pay online">

DELIMITER;

return $paypal_button;

}







}


?>