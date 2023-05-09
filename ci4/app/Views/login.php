<!DOCTYPE html>
<html lang="zxx">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Panel</title>

    <link rel="icon" href="img/logo-icon.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.1/css/dataTables.bootstrap4.min.css" />
       <link rel="stylesheet" href="/assets/vendors/font_awesome/css/all.min.css" />
    <!-- Icons CSS -->
    <link rel="stylesheet" href="/assets/vendors/themefy_icon/themify-icons.css" />
    <link rel="stylesheet" href="/assets/css/metisMenu.css">
  <!-- sidebarmenu -->
    <!-- style CSS -->
    <link rel="stylesheet" href="/assets/css/style.css" />
    <link rel="stylesheet" href="/assets/css/select2.min.css" />
</head>
<body class="crm_body_bg">   


<!-- main content part here -->
 



<section class="loginBlkMain dashboard_part large_header_bg">
      
    <div class="main_content_iner ">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
              
                <div class="col-lg-12">
				
                    <div class="mb_30">
                        <div class="row justify-content-center">
                          
                            <div class="col-lg-4">
							
                              
                                <!-- sign_in  -->
                                <div class="modal-content cs_modal loginModel">
                                   
                                    <div class="modal-body">
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
                                        <div class="logo  text-center">
                                            <a class="large_logo" href="/"><img src="<?=$logo->meta_value?'data:image/jpeg;base64,'.$logo->meta_value:'/assets/img/logo.jpg'?>" style="width: 100%;" alt=""></a>
                                        </div>
                                        <br/><br/>
                                        <form id="loginform" method="post" action="/home/login">
                                            <!-- <div class="form-group">
												<label>Service</label>
                                                <select name="uuid_business_id" id="uuid_business_id" class="form-control">
                                                 <option>Please Select</option>
                                                 <?php foreach($uuid as $eachUuid):?>
                                                    <option value="<?php echo $eachUuid->id?>"> <?php echo $eachUuid->name?></option>
                                                
                                                 <?php endforeach;?>
                                                </select>
                                               
                                            </div> -->
                                            <div class="form-group">
												<label>Email</label>
                                                <input type="text" class="form-control" name="email" id="email" placeholder="">
                                                <span><i class="fa fa-user"></i></span>
                                            </div>
                                            <div class="form-group">
												<label>Password</label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="">
                                                <span class="psswrdIcon">
                                                    <a href="#" onclick="myFunction()">
                                                        <i class="fa fa-eye"></i><i class="fa fa-eye-slash"></i></span></a>

                                                <input type="hidden" class="form-control" id="redirectAfterLogin" name="redirectAfterLogin" placeholder="">
                                            </div>
                                            <button type="submit" class="btn_1 full_width text-center">Log in</button>
                                            <!--div class="text-center">
                                                <a href="/forgot" class="pass_forget_btn">Forget Password?</a>
                                            </div-->
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- footer part -->
<?php require_once (APPPATH.'Views/common/footer_copyright.php'); ?>
</section>


<!-- footer  -->
<script src="/assets/js/jquery-3.4.1.min.js"></script>
<script src="/assets/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
<script type="text/javascript">
    function myFunction() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
     $('.psswrdIcon').addClass('changeIcon');
  } else {
    x.type = "password";
    $('.psswrdIcon').removeClass('changeIcon');
  }
}
</script>
 <script>
   if ($("#loginform").length > 0) {
      $("#loginform").validate({
    rules: {
      email: {
        required: true,
        maxlength: 50,
        email: true,
      }, 
      password: {
        required: true,
      },   
    },
    messages: {
      email: {
        required: "Please enter valid email",
        email: "Please enter valid email",
        maxlength: "The email name should less than or equal to 50 characters",
        },      
     password: {
        required: "Please enter your password",
      },
        
    },
  })
}
$("#uuid_business_id").select2();



let menu_current = localStorage.getItem("menu-current")
console.log("menu_current",menu_current)
document.getElementById("redirectAfterLogin").value = menu_current;
</script>
<style>
.select2-container .select2-selection--single {
    height: 42px;
}
</style>
</body>
</html>