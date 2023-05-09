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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Settings </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Settings</li>
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
                               
                                <form action="/dashboard/saveset" method="post" id="userform" enctype="multipart/form-data">
								
								  <div class="form-group col-md-6">
								  <?php if(!empty($data->meta_value)) { ?>
                                            <img src="<?='data:image/jpeg;base64,'.$data->meta_value?>" width="250px"><br><br>
										<?php } ?>
                                           <label for="inputAddress">Upload Logo</label>
                                            <div class="uplogInrDiv mb_30">
                                            <input type="file" name="file" class="custom-file-input" id="customFile">
                                             <div class="uploadBlkInr">
                            <div class="uplogImg">
                              <img src="/assets/img/fileupload.png" />
                            </div>
                            <div class="uploadFileCnt">
                              <p>
                                <a href="#">Upload a file </a> file chosen or drag
                                and drop
                              </p>
                              <p>
                                <span>Video, PNG, JPG, GIF up to 10MB</span>
                              </p>
                            </div>
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
      file: {
        required: true,
      }  
    },
    messages: {
      name: {
        required: "Please enter name",
      }
        
    },
  })
}
</script>