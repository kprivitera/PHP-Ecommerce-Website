<?php require_once("../resources/config.php"); ?>

<?php include(TEMPLATE_FRONT . DS . "header.php"); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="row carousel-holder">

                <div class="col-md-12">
                    <?php include(TEMPLATE_FRONT . DS . "slider.php"); ?>
                </div>

            </div>
        </div>
    </div>
    <!-- Page Content -->
    
    <div class="container">   
            <div class="row">
                <div class="col-lg-12 col-sm-12 hp-heading">
                    <h2>Latest Products</h2>
                </div>
        </div>
                <div class="row">

                   <!-- list the products -->
                   <?php get_products(); ?>
                </div><!-- row ends here -->

            </div>

    <!-- /.container -->
 
<?php include(TEMPLATE_FRONT . DS . "footer.php"); ?>
   