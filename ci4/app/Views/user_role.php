<?php include('common/header.php'); ?>
<!-- main content part here -->
 

<?php include('common/sidebar.php'); ?>

<section class="main_content dashboard_part large_header_bg">
       <?php include('common/top-header.php'); ?> 
   
<div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Role Based Access Manager </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Role Based Access Manager</li>
                            </ol>
                        </div>
                                              
                    </div>
                </div>
            </div>
            <div class="row ">

                <div class="col-lg-12">
				<?php 
				// Display Response
				if(session()->has('message')){
				?>
				<div class="alert <?= session()->getFlashdata('alert-class') ?>">
				   <?= session()->getFlashdata('message') ?>
				</div>
				<?php
				}
				?>
                    <div class="white_card card_height_100 mb_30">
                       
                        <div class="white_card_body">
                            <div class="card-body">
                               
                                <form action="/dashboard/savepwd" method="post" id="userform">
								
								                    <div class="row">
                                         <div class="col-md-6 color_text_6">
                                            <h4>Select the user from the list</h4>                                           
                                        </div>  
                                        
                                        <div class="col-md-6 color_text_6">
                                            <h4>Select the Roles for selected user</h4>                                           
                                        </div>  
                                    </div>


                                    <div class="row-col">
                                  <div class="column">
                                  <?php foreach ($users as $user) { ?>
                                    <div class="col-md-6 product" data-uid="<?php echo $user['id'];?>">
                                    <?php echo $user['name']; ?>                                    
                                    </div>
                                  <?php } ?>  
                                  </div>
                                  <div class="column" id="menu_div">
                                  Select a user first
                                  </div>
                                </div>
									

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
              

             
               
            </div>
        </div>
    </div>
    </div>
<?php include('common/footer.php'); ?>
</section>
<!-- main content part end -->

<?php include('common/scripts.php'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
 <script>
   if ($("#userform").length > 0) {
      $("#userform").validate({
    rules: {
      opassword: {
        required: true,
      }, 
      npassword: {
        required: true,
      }, 
      cpassword: {
        required: true,
		equalTo : "#npassword"
      }  
    },
    messages: {
      name: {
        required: "Please enter name",
      },
      email: {
        required: "Please enter valid email",
        email: "Please enter valid email",
        maxlength: "The email name should less than or equal to 50 characters",
        },      
     password: {
        required: "Please enter password",
      },
        
    },
  })
}

var menu_items = <?php echo json_encode($menus);?>;
console.log(menu_items)
var selected_items = []
var current_user = '';


$("body .product").click(function () { 
        $(".product").not(this).removeClass("selected").addClass('unselected')
        $(this).removeClass("unselected").addClass("selected");
        current_user = $(this).data('uid');
        if($(this).data('uid')){
          $("#menu_div").html('Loading...');
				 $.ajax({
						url: baseURL + 'users/getuser/'+$(this).data('uid'),
            dataType: 'JSON',
						success: function(d) {
              d = JSON.parse(d)
							console.log(d)
              var div_html = '';
              selected_items = []
              menu_items.map((val)=>{
                val.id = val.id.toString();
                var selected = d && d.length>0 && d.filter(e => e ===val.id).length > 0?'selected':''
                console.log('selected',selected,val.id)
                if(d && d.length>0 && d.filter(e => e ===val.id).length > 0){
                  selected_items.push(val.id)
                }
                div_html += '<div class=" col-md-6 userole_menu '+selected+'" data-menuid="'+val.id+'">'+val.name+'</div>'
              })

              $("#menu_div").html(div_html);

              
						}
				});
        }
  });

  $( "body").on( "click", ".userole_menu",function () {
        console.log(current_user);
        var menuid = parseInt($(this).data('menuid'))
        $("#menu_div").addClass('disable_css')
        if($(this).hasClass("selected")){
          selected_items = selected_items.filter(function(item) {
              return parseInt(item) !== parseInt(menuid)
          })
        }else{
          selected_items.push(menuid);
        }
        console.log(menuid,selected_items);
        $(".userole_menu").not(this).addClass('unselected')
        $(this).toggleClass("unselected").toggleClass("selected");
        if(current_user!=="" && selected_items.length>0){
          var $parentDiv = $("body #menu_div");
          $.ajax({
              url: baseURL + 'users/update_permission',
              type: "post",
              dataType: 'JSON',
              data: {userid:current_user,items:selected_items},
              success: function(d) {
                $parentDiv.removeClass('disable_css')
                //console.log(d)              
              }
          });

        }
        

  });
</script>
<style>
  div.product:hover {
    border:1px solid #878787;
    -moz-border-radius:3px;
    border-radius:3px;
    cursor:pointer;
    }

    div.product.unselected {
    opacity:0.6;
    filter:alpha(opacity=60);
    }

    div.product.selected {
    border:1px solid #32a24e;
    -moz-border-radius:3px;
    border-radius:3px;
    }


    div.userole_menu:hover {
    border:1px solid #878787;
    -moz-border-radius:3px;
    border-radius:3px;
    cursor:pointer;
    }

    div.userole_menu.unselected {
    opacity:0.9;
    filter:alpha(opacity=100);
    }

    div.userole_menu.selected {
    border:1px solid #32a24e;
    -moz-border-radius:3px;
    border-radius:3px;
    }

    div.userole_menu.selected:before {
  content: '\2713';
  display: inline-block;
  color: red;
  padding: 0 6px 0 0;
}

.column {
  float: left;
  width: 50%;
  padding: 10px;
  height: 300px; /* Should be removed. Only for demonstration */
  overflow:scroll;
}

/* Clear floats after the columns */
.row-col:after {
  content: "";
  display: table;
  clear: both;
}

.disable_css{
  pointer-events: none;
    opacity: 0.4;
}

</style>