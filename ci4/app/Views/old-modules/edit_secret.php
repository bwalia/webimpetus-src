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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Secrets </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Secrets</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                           <a href="/secrets" class="btn btn-primary"><i class="fa fa-table"></i> Secrets List</a>
                        </div>
                      
                    </div>
                </div>
            </div>
            <div class="row ">

                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_30">
                       
                        <div class="white_card_body">
                            <div class="card-body">
                               
                                <form id="adddomain" method="post" action="/secrets/update" enctype="multipart/form-data">
                                   <div class="form-row">
									
									
										
										
                                        <div class="form-group col-md-12">
                                            <label for="inputEmail4">Secret Key</label>
                                            <input type="text" class="form-control" id="title" name="key_name" placeholder=""  value="<?=$content->key_name?>">
											<input type="hidden" class="form-control" name="id" placeholder="" value="<?=$content->id ?>" />
                                        </div>

                                          
                                         <div class="form-group col-md-12">
                                            <label for="inputPassword4">Secret Value</label>
                                          <textarea class="form-control" name="key_value" style="width:100%!important;height:250px" id="key_value">*****************</textarea> 
                                        </div>
                                    
                                   <div class="form-group col-md-12">
                                            <label for="inputState">Choose Service</label>
                                            <select id="sid" name="sid[]" multiple class="form-control js-example-basic-multiple">                                            
												<?php foreach($services as $row):?>
                                                <option <?=in_array($row['id'],$sservices)?'selected':''?> value="<?=$row['id'];?>"><?= $row['name'];?></option>
                                               <?php endforeach;?>
                                            </select>
                                        </div>
                                       
											<div class="form-group col-md-12">
                                            <label for="inputEmail4"></label>
											</div>
											<div class="form-group col-md-12">
											
                                             <label for="inputEmail4"><input type="checkbox" value="1" class="form-control" id="status" name="status" placeholder="" /> Show secret value </label>
                                        </div>
                                      
                                   

                                    
                                    </div>

                                    
                                   


                                    
                                  

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
		
		$("#status").on("change", function(){
			var vall = '<?=base64_encode($content->key_value)?>';
			if($(this).is(":checked")===true){
				$('#key_value').val(atob(vall))
			}else{
				$('#key_value').val("*************")
			}
			//alert($(this).is(":checked"))
		})
	</script>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
 <script>
   if ($("#adddomain").length > 0) {
      $("#adddomain").validate({
    rules: {
      code: {
        required: true,
      }, 
      text: {
        required: true,
      }  
    },
    messages: {
      code: {
        required: "Please enter code",
      }
        
    },
  })
}
</script>