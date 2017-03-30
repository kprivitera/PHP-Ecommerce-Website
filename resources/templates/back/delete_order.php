<?php require_once("../../config.php"); ?>
<?php 

//check to see if we get an id from the get request
if(isset($_GET['id'])){
  $query = query("DELETE FROM orders WHERE order_id = " . escape_string($_GET['id']) ." ");
  confirm($query);
  set_message("Order Deleted");
  redirect("../../../public/admin/index.php?orders");
}else{
  redirect("../../../public/admin/index.php?orders");
}

?>

