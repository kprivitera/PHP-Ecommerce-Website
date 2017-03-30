<?php 

/*******************************************************************************
                                Helper Functions
********************************************************************************/

$upload_directory = "uploads";

//check database connection
if($connection){
	//echo "connected to database";
}

//grab the id of the last inserted record
function last_id(){
	global $connection;
	return mysqli_insert_id($connection);
}

//whatever string going into the function will be assigned to the message session superglobal
function set_message($msg){
	if(!empty($msg)){
		$_SESSION['message'] = $msg;
	} else {
		$msg = "";
	}
}

//check to see if the session is available
function display_message(){
	//check to see if the session is available
	if(isset($_SESSION['message'])){
		//if so echo it
		echo $_SESSION['message'];
		//at the same time we are going to unset it when we echo it so when we refresh it is not there again.
		unset($_SESSION['message']);
	}
	
}

//redirect to chosen location
function redirect($location){
	header("Location: $location ");
}


//function to quickly make an sql statement
function query($sql) {
	//let the function know that youre using a global variable
	global $connection;
	return mysqli_query($connection, $sql);
}

//check if query failed
function confirm($result){
	global $connection;
	if(!$result){
		die("QUERY FAILED ". mysqli_error($connection));
	}
}

//escape strings
function escape_string($string){
	global $connection;
	return mysqli_real_escape_string($connection, $string);
}

function fetch_array($result){
	return mysqli_fetch_array($result);
}

/*******************************************************************************
                                 Front End Functions
********************************************************************************/

//get products
function get_products(){
	//select all products from the products table
	$query = query("SELECT * FROM products");

	//make sure the query we sent worked
	confirm($query);

	//print out the results
	while ($row = fetch_array($query)) {
		
		//grab product directory
		$product_image = display_image($row['product_image']);
		$product_image_secondary = display_image($row['product_image_secondary']);

		//heredoc is used to make the html into a string, and you dont need to worry about changing the single quotes to double quotes
		$product = <<<DELIMITER

		 <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="thumbnail">
                <a href="item.php?id={$row['product_id']}"><img src="../resources/{$product_image}" data-mouseover="../resources/{$product_image_secondary}" alt=""></a>
                <div class="caption text-center">
                    <h4><a href="item.php?id={$row['product_id']}">{$row['product_title']}</a></h4>
                    <h4>&#36;{$row['product_price']}</h4>
                    <a class="btn btn-primary" target="_blank" href="../resources/cart.php?add={$row['product_id']}">Add to cart</a>
                </div>
                


            </div>
        </div>

DELIMITER;

		echo $product;
	}

}


//get the categories for the side nav on the index
function get_categories(){
	
	//grab all categories
	$query = query("SELECT * FROM categories");
	confirm($query);

	//post categories onto the page
	while($row = fetch_array($query)) {
		
$category_links = <<<DELIMITER

<li><a href='category.php?id={$row['cat_id']}' class=''>{$row['cat_title']}</a></li>

DELIMITER;

	echo $category_links;
	
	}

}

//when click on category name, in the category page list the products in that category
function get_products_in_cat_page(){
	//select all products from the products table where the the products category id equals the category id of the category name link
	$query = query("SELECT * FROM products WHERE product_category_id = " . escape_string($_GET['id']) . " ");

	//make sure the query we sent worked
	confirm($query);

	//print out the results
	while ($row = fetch_array($query)) {

		//grab product directory
		$product_image = display_image($row['product_image']);
		$product_image_secondary = display_image($row['product_image_secondary']);
		
		//heredoc is used to make the html into a string, and you dont need to worry about changing the single quotes to double quotes
		$product = <<<DELIMITER

		  <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <a href="item.php?id={$row['product_id']}"><img src="../resources/{$product_image}" data-mouseover="../resources/{$product_image_secondary}" alt=""></a>
                    <div class="caption">
                        <h3>{$row['product_title']}</h3>
                        <h4>&#36;{$row['product_price']}</h4>
                        
                    </div>
                </div>
            </div>
DELIMITER;

/*<p>{$row['short_desc']}
<p><a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a></p>
</p> put inside delimiter if want short desc*/

		echo $product;
	}

}


//on the shop page it will display all of the products
function get_products_in_shop_page(){
	//select all the products from the table
	$query = query("SELECT * FROM products");

	//make sure the query we sent worked
	confirm($query);

	//print out the results
	while ($row = fetch_array($query)) {
	
		$product_image = display_image($row['product_image']);
		$product_image_secondary = display_image($row['product_image_secondary']);

		//heredoc is used to make the html into a string, and you dont need to worry about changing the single quotes to double quotes
		//add this to delimiter if needed <p>{$row['short_desc']}.</p>
		$product = <<<DELIMITER

		  <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <img src="../resources/{$product_image}" data-mouseover="../resources/{$product_image_secondary}" alt="">
                    <div class="caption">
                        <h3>{$row['product_title']}</h3>
                        <p>
                            <a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                        </p>
                    </div>
                </div>
            </div>
DELIMITER;

		echo $product;
	}

}


function login_user(){

	//check if the user has pressed the submit button
	if (isset($_POST['submit'])){
		//escape the username and password to prevent sql injection
		$username = escape_string($_POST['username']);
		$password = escape_string($_POST['password']);

		//request the row of the user with the same username and password as what has been submitted by the form.
		$query = query("SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}' ");
		confirm($query);

		//check to see if anything is found. a row count of more than 0. Returns the count of rows
		if (mysqli_num_rows($query) == 0){
			set_message("Your password or username are wrong");
			redirect("login.php");
		} else {
			//set a session to have the value of the username while logged in
			$_SESSION['username'] = $username;
			redirect("admin/index.php");
		}
	}
}


//code for the contact form
function send_message(){
	if (isset($_POST['submit'])){
		
		//save the information inputted from the form
		$to        =  "someEmailaddress@gmail.com";
		$from_name =  $_POST['name'];
		$subject   =  $_POST['subject'];
		$email     =  $_POST['email'];
		$message   =  $_POST['message'];

		$headers = "From: {$from_name} {$email}";

		//mail function isnt reliable because filters stop it, third party plugins might be better. Returns true or false
		$result = mail($to, $subject, $message, $headers);


		//check if the mail fucntion worked
		if(!$result){
			set_message("Sorry we could not send your message");
			//refresh the page when it sends
			redirect("contact.php");
		} else {
			set_message("Your message has been sent");
			//refresh the page when it sends
			redirect("contact.php");
		}
	}
}

function liveSearch(){
    global $connection;
    if (!empty($_GET['q'])){
        $q = $_GET['q'];
        $query = "SELECT * FROM products WHERE product_title LIKE '%$q%'";
        $result = mysqli_query($connection, $query);
        //confirm($result);
        $test = mysqli_num_rows($result);
        while($output = mysqli_fetch_assoc($result)){
            echo '<a href="/ecom/public/item.php?id='. $output['product_id'] .'">' . $output['product_title'] . '</a>';
        }
    }

}

/*******************************************************************************
                                 Back End Functions
********************************************************************************/

//display orders on the orders page
function display_orders(){
	$query = query("SELECT * FROM orders");
	confirm($query);

	while($row = fetch_array($query)){
		$orders = <<<DELIMITER
<tr>
<td>{$row['order_id']}</td>
<td>{$row['order_amount']}</td>
<td>{$row['order_transaction']}</td>
<td>{$row['order_currency']}</td>
<td>{$row['order_status']}</td>
<td><a class="btn btn-danger" href="../../resources/templates/back/delete_order.php?id={$row['order_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
</tr>

DELIMITER;
	
	echo $orders;
	}
}

function display_image($picture){
	global $upload_directory;
	return  $upload_directory . DS . $picture;
}

function get_products_in_admin(){
		//select all products from the products table
	$query = query("SELECT * FROM products");

	//make sure the query we sent worked
	confirm($query);

	//print out the results
	while ($row = fetch_array($query)) {
		
		$category = show_product_category_title($row['product_category_id']);

		//grab product directory
		$product_image = display_image($row['product_image']);

		//heredoc is used to make the html into a string, and you dont need to worry about changing the single quotes to double quotes
		$product = <<<DELIMITER

      	<tr>
            <td>{$row['product_id']}</td>
            <td>{$row['product_title']}<br>
              <a href="index.php?edit_product&id={$row['product_id']}"><img src="../../resources/{$product_image}"  width="100" alt=""></a>
            </td>
            <td>{$category}</td>
            <td>{$row['product_price']}</td>
            <td>{$row['product_quantity']}</td>
            <td><a class="btn btn-danger" href="../../resources/templates/back/delete_product.php?id={$row['product_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
DELIMITER;

		echo $product;
	}
}

//show the name of the product category in the database
function show_product_category_title($product_category_id){
	$category_query = query("SELECT * FROM categories WHERE cat_id = '{$product_category_id}'");
	confirm($category_query);

	while($category_row = fetch_array($category_query)){
		return $category_row['cat_title'];
	}
}

/************** Add products in admin *******************/
function add_product(){
	
	//check if the public button has been clicked
	if(isset($_POST['publish'])){
		//if so save the post data into variables
		$product_title       = escape_string($_POST['product_title']);
		$product_category_id = escape_string($_POST['product_category_id']);
		$product_price       = escape_string($_POST['product_price']);
		$product_description = escape_string($_POST['product_description']);
		$short_desc          = escape_string($_POST['short_desc']);
		$product_quantity    = escape_string($_POST['product_quantity']);

		$product_image       = escape_string($_FILES['file']['name']);
		$image_temp_location = escape_string($_FILES['file']['tmp_name']);

		$product_image_secondary       = escape_string($_FILES['file2']['name']);
		$image_temp_location_secondary = escape_string($_FILES['file2']['tmp_name']);

		move_uploaded_file($image_temp_location, UPLOAD_DIRECTORY . DS . $product_image);
		move_uploaded_file($image_temp_location, UPLOAD_DIRECTORY . DS . $product_image_secondary);

		//make a query to insert the data into the database.
		$query = query("INSERT INTO products(product_title, product_category_id, product_price, product_description, short_desc, product_quantity, product_image, product_image_secondary) VALUES('{$product_title}', '{$product_category_id}', '{$product_price}', '{$product_description}', '{$short_desc}', '{$product_quantity}', '{$product_image}', '{$product_image_secondary}')");
		confirm($query);
		$last_id = last_id();
		set_message("New Product with id {$last_id} was created");
		redirect("index.php?products");

	}
}

//we need to get the category names for the add product page, otherwise they will just be numbers
function show_categories_add_product_page(){
	
	//grab all categories
	$query = query("SELECT * FROM categories");
	confirm($query);

	//post categories onto the page
	while($row = fetch_array($query)) {
		
$categories_options = <<<DELIMITER

<option value="{$row['cat_id']}">{$row['cat_title']}</option>


DELIMITER;

	echo $categories_options;
	
	}

}

//update product code
function update_product(){
	
	//check if the public button has been clicked
	if(isset($_POST['update'])){
		//if so save the post data into variables
		$product_title       = escape_string($_POST['product_title']);
		$product_category_id = escape_string($_POST['product_category_id']);
		$product_price       = escape_string($_POST['product_price']);
		$product_description = escape_string($_POST['product_description']);
		$short_desc          = escape_string($_POST['short_desc']);
		$product_quantity    = escape_string($_POST['product_quantity']);	
		$product_image       = escape_string($_FILES['file']['name']);
		$image_temp_location = escape_string($_FILES['file']['tmp_name']);

		$product_image_secondary       = escape_string($_FILES['file2']['name']);
		$image_temp_location_secondary = escape_string($_FILES['file2']['tmp_name']);

		if(empty($product_image)){
			$get_pic = query("SELECT product_image FROM products WHERE product_id = " . escape_string($_GET['id']) ."");
			confirm($get_pic);

			while($pic = fetch_array($get_pic)){
				$product_image = $pic['product_image'];
			}
		}

		if(empty($product_image_secondary)){
			$get_pic_secondary = query("SELECT product_image_secondary FROM products WHERE product_id = " . escape_string($_GET['id']) ."");
			confirm($get_pic_secondary);

			while($pic = fetch_array($get_pic)){
				$product_image_secondary = $pic['product_image_secondary'];
			}
		}

		move_uploaded_file($image_temp_location, UPLOAD_DIRECTORY . DS . $product_image);

		//make a query to insert the data into the database.
		$query = "UPDATE products SET "; 
		$query .= "product_title           = '{$product_title}'      , ";
		$query .= "product_category_id     = '{$product_category_id}', ";
		$query .= "product_price           = '{$product_price}'      , ";
		$query .= "product_description     = '{$product_description}', ";
		$query .= "short_desc              = '{$short_desc}'         , ";
		$query .= "product_quantity        = '{$product_quantity}'   , ";
		$query .= "product_image           = '{$product_image}'      ,  ";
		$query .= "product_image_secondary = '{$product_image_secondary}'";
		$query .= "WHERE product_id = " . escape_string($_GET['id']) ." ";

		$send_update_query = query($query);
		confirm($send_update_query);
		set_message("Product has been updated.");
		redirect("index.php?products");

	}
}


/************** categories in admin *******************/

function show_categories_in_admin(){
	$category_query = query("SELECT * FROM categories");
	confirm($category_query);

	while($row = fetch_array($category_query)){
		$cat_id = $row['cat_id'];
		$cat_title = $row['cat_title'];

		$category = <<<DELIMITER

  <tr>
  	<td>{$cat_id}</td>
    <td>{$cat_title}</td>
    <td><a class="btn btn-danger" href="../../resources/templates/back/delete_category.php?id={$row['cat_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
  </tr>

DELIMITER;

		echo $category;
	}
}

function add_category(){
	if(isset($_POST['add_category'])){
		$cat_title = escape_string($_POST['cat_title']);

		if(empty($cat_title) || $cat_title == " "){
			echo "<p class='bg-danger'>THIS CANNOT BE EMPTY</p>";
		} else {
			$insert_cat = query("INSERT INTO categories(cat_title) VALUES('{$cat_title}')");
			confirm($insert_cat);
			set_message("CATEGORY CREATED");
		}
	}
}


/************** users in admin *******************/

function display_users(){
	$users_query = query("SELECT * FROM users");
	confirm($users_query);

	while($row = fetch_array($users_query)){
		$user_id = $row['user_id'];
		$username = $row['username'];
		$email = $row['email'];
		$username = $row['password'];

		$user = <<<DELIMITER

  <tr>
  	<td>{$user_id}</td>
    <td>{$username}</td>
    <td>{$email}</td>
    <td><a class="btn btn-danger" href="../../resources/templates/back/delete_user.php?id={$row['user_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
  </tr>

DELIMITER;

		echo $user;
	}
}

?>

