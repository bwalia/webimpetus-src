                      
                    </div>
                </div>
               
            </div>
        </div>
    </div>
    
<?php require_once (APPPATH.'Views/common/scripts.php'); ?>
<?php require_once (APPPATH.'Views/common/footer_copyright.php'); ?>

</section>
<script>
    $('input[type=search]').on('search', function () {
        if($(this).val()==""){
            window.location.href = '/';
        }
    // search logic here
    // this function will be executed on click of X (clear button)
});

// $('#sidebar_menu li').on('click', function(e) {
//   $(this).children('ul').toggle();
//   $(this).children('ul').toggleClass("show");
//   $(this).siblings('li').find('ul').hide();
//   $(this).siblings('li ul').toggleClass("show");
//   e.stopPropagation();
// });

$('#sidebar_menu li a').click(function(){
    var id = $(this).attr('id');
    $('#sidebar_menu li ul.item-show-'+id).toggleClass("show");
    $('#sidebar_menu li #'+id+' span').toggleClass("rotate");
    
});

$('#sidebar_menu li').click(function(){
    $(this).addClass("active").siblings().removeClass("active");
});
</script>