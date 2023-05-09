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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Services </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Services</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                           <a href="/services" class="btn btn-primary"><i class="fa fa-table"></i> Services List</a>
                        </div>
                      
                    </div>
                </div>
            </div>
            <div class="row ">

                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_30">
                       
                        <div class="white_card_body">
                            <div class="card-body">
                               
                                <form id="addservice" method="post" action="/services/save" enctype="multipart/form-data">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputEmail4">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputState">Choose User</label>
                                            <select id="uuid" name="uuid" class="form-control">
                                                <option value="" selected="">--Select--</option>
												<?php foreach($users as $row):?>
                                                <option value="<?= $row['uuid'];?>"><?= $row['name'];?></option>
                                               <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
									
									<div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputState">Choose Category</label>
                                            <select id="cid" name="cid" class="form-control">
                                                <option value="" selected="">--Select--</option>
												<?php foreach($category as $row):?>
                                                <option value="<?= $row['id'];?>"><?= $row['name'];?></option>
                                               <?php endforeach;?>
                                            </select>
                                        </div>
										<div class="form-group col-md-6">
                                            <label for="inputState">Choose Tenant</label>
                                            <select id="tid" name="tid" class="form-control">
                                                <option value="" selected="">--Select--</option>
												<?php foreach($tenants as $row):?>
                                                <option value="<?= $row['id'];?>"><?= $row['name'];?></option>
                                               <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
									
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputPassword4">Code</label>
                                            <input type="text" class="form-control" id="code" name="code" placeholder="">
                                        </div>
                                         <div class="form-group col-md-6">
                                            <label for="inputPassword4">Notes</label>
                                            <input type="text" class="form-control" id="notes" name="notes" placeholder="">
                                        </div>                                      
                                    </div>

                                     <div class="form-row">
                                          <div class="form-group col-md-6" style="display: none">
                                           <label for="inputAddress">Logo Upload</label>
                                            <div class="custom-file">
                                            <input type="file" name="file" class="custom-file-input" id="file">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            </div>
                                            <script>
                                                // Add the following code if you want the name of the file appear on select
                                                $(".custom-file-input").on("change", function() {
                                                  var fileName = $(this).val().split("\\").pop();
                                                  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                                                });
                                            </script>
                                        </div>
                                        <div class="form-group col-md-6" style="display: none">
                                           <label for="inputAddress">Brand Upload</label>
                                            <div class="custom-file">
                                            <input type="file" name="file2" class="custom-file-input" id="file2">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            </div>
                                            <script>
                                                // Add the following code if you want the name of the file appear on select
                                                $("#file2").on("change", function() {
                                                  var fileName = $(this).val().split("\\").pop();
                                                  $(this).siblings("#file2").addClass("selected").html(fileName);
                                                });
                                            </script>
                                        </div>
                                     </div>
									
									<div class="form-row" id="office_address_1" style="display : none">
										<div class="form-group col-md-6">
											<label for="inputEmail4">Secret Key</label>
											<input type="text" class="form-control" id="key_name_1" name="key_name[]" placeholder="" value="">
										</div>
										<div class="form-group col-md-5">
											<label for="inputEmail4">Secret Value</label>
											<input type="text" class="form-control" id="key_value_1" name="key_value[]" placeholder="" value="">
										</div>
										<div class="form-group col-md-1 change">
											<button class="btn btn-primary bootstrap-touchspin-up add" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">+</button>
										</div>
									</div>
									<div class="form-row addresscontainer">
									
									</div>
									<input type="hidden" value="1" id="total_secret_services" name="total_secret_services">
									
									<!-- 
									<div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputPassword4">nginx config</label>
                                            <textarea class="form-control" id="nginx_config" name="nginx_config" placeholder=""></textarea>
                                        </div>
                                         <div class="form-group col-md-6">
                                            <label for="inputPassword4">varnish config</label>
                                            <textarea type="text" class="form-control" id="varnish_config" name="varnish_config" placeholder=""></textarea>
                                        </div>                                      
                                    </div>
									-->

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
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

    <script>
		// Add the following code if you want the name of the file appear on select
		$(".custom-file-input").on("change", function() {
		  var fileName = $(this).val().split("\\").pop();
		  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});
	</script>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
 <script>
   if ($("#addservice").length > 0) {
      $("#addservice").validate({
    rules: {
      name: {
        required: true,
      }, 
      notes: {
        required: false,
      }, 
      code: {
        required: true,
      }, 
      uuid: {
        required: true,
      }/* , 
      file: {
        required: true,
      }, 
      file2: {
        required: true,
      }, 
      nginx_config: {
        required: true,
      } , 
      varnish_config: {
        required: true,
      }*/, 
      cid: {
        required: true,
      }, 
      tid: {
        required: true,
      },   
    },
    messages: {
      name: {
        required: "Please enter name",
      },      
     notes: {
        required: "Please enter notes",
      },      
     code: {
        required: "Please enter code",
      },      
     uuid: {
        required: "Please select user",
      },
        
    },
  })
}


	$(document).ready(function() {
	
        var max_fields_limit = 10; //set limit for maximum input fields
		var x = $('#total_secret_services').val(); //initialize counter for text box
		$('.add').click(function(e){ //click event on add more fields button having class add_more_button
			// e.preventDefault();
			if(x < max_fields_limit){ //check conditions
				x++; //counter increment
				
				$('.addresscontainer').append('<div class="form-row col-md-12" id="office_address_'+x+'"><div class="form-group col-md-6">'+
												'<label for="inputSecretKey">Secret Key</label>'+
												'<input type="text" class="form-control" id="key_name_'+x+'" name="key_name[]" placeholder="" value="">'+
											'</div>'+
											'<div class="form-group col-md-5">'+
												'<label for="inputSecretValue">Secret Value</label>'+
												'<input type="text" class="form-control" id="key_value_'+x+'" name="key_value[]" placeholder="" value="">'+
											'</div>'+
											'<div class="form-group col-md-1 change">'+
												'<button class="btn btn-info bootstrap-touchspin-up deleteaddress" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>'+
											'</div></div>'
											);
				
				
			}
			
			$('.deleteaddress').on("click", function(e){ //user click on remove text links
				e.preventDefault(); 
				$(this).parent().parent().remove();
				x--;
			})
		});   
	});
</script>