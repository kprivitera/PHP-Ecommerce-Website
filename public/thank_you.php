<?php require_once("../resources/config.php"); ?>
<?php include(TEMPLATE_FRONT . DS . "header.php"); ?>

<?php 
//this page is the redirect after the buyer purchases an item.

  process_transaction();

?>


<div class="container">

  <h1 class="text-center">Thankyou</h1>

</div>



<?php require_once("../resources/templates/front/footer.php"); ?>