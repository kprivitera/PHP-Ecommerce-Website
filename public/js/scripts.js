/*! Main */
jQuery(document).ready(function($) {
  
    // Fix a navbar ao ultrapassa-lo
    var navbar = $('#cat-bar'),
    		distance = navbar.offset().top,
        $window = $(window);

    $window.scroll(function() {
        if ($window.scrollTop() >= distance) {
            navbar.removeClass('navbar-fixed-top').addClass('navbar-fixed-top');
          	$("body").css("padding-top", "50px");
        } else {
            navbar.removeClass('navbar-fixed-top');
            $("body").css("padding-top", "0px");
        }
    });

    //mouseover product secondary image
    function productImageCycle(){

        var initialImage, secondaryImage, self;

        $('.thumbnail img').bind("mouseenter", function(){
            self = $(this);
            secondaryImage = self.data("mouseover");
            initialImage = self.attr("src");
            self.attr("src", secondaryImage);
        }); //end mouseenter


        $('.thumbnail img').bind("mouseleave", function(){
            self = $(this);
            self.attr("src", initialImage);
        }); //end mouseenter

    }   

    productImageCycle();

   function searchFunction(){
        $("#search").keyup(function(){
            if($(this).val().length > 0){
               $('#here').show(); 
            } else {
                $('#here').hide();
            };
            var x = $(this).val();

            $.ajax({
                type:'GET',
                url:'/ecom/resources/search.php?',
                data:'q=' + x,
                success:function(data){
                    $('#here').html(data);
                }
            })
        });
    }
    searchFunction();

});