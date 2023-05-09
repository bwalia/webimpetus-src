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
                                <li class="breadcrumb-item active">Services List</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                        
                            <div class="header_more_tool setDropDownBlk">
                              
                            <a href="/services/add" class="btn btn-primary"><i class="ti-plus"></i> Add Services</a>
                        </div>
						
						
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
				
				
                    <div class="white_card card_height_100 mb_20 ">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Services List</h3>
                                </div>
                              
                            </div>
                        </div>
                        <div class="white_card_body ">
                            <div class="QA_table ">
                                <!-- table-responsive -->
                                <table id="example"  class="table table-listing-items tableDocument table-bordered table-hover">
                                    <thead>
                                        <tr>
                                           
                                            <th scope="col">Id</th>
                                            <th scope="col">UUID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Category</th>     
											<th scope="col">Tenant</th> 											
                                            <th scope="col">Code</th>
                                            <th scope="col">Service Logo</th>
                                            <th scope="col">Brand Image</th>
                                          
                                            <th scope="col">Notes</th>
                                            
                                              <th scope="col">Status</th>
                                             <th scope="col" width="50">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                  
                                   <?php foreach($services as $row):?>  
                                    <tr data-href="/services/edit/<?= $row['id'];?>">                                     
                                        <td class="f_s_12 f_w_400"><a href="/services/edit/<?= $row['id'];?>"><?= $row['id'];?></a>
                                        </td>
                                        <td class="f_s_12 f_w_400"><a href="/services/edit/<?= $row['id'];?>"><?= $row['uuid'];?> </a>
                                        </td>
                                        <td class="f_s_12 f_w_400 text_color_1 "><a href="/services/edit/<?= $row['id'];?>"><?= $row['name'];?></a>
                                        </td>
                                        <td class="f_s_12 f_w_400 text_color_1 "><a href="/services/edit/<?= $row['id'];?>"><?= $row['category'];?></a>
                                        </td>
										
										<td class="f_s_12 f_w_400"><a href="/services/edit/<?= $row['id'];?>"><?= $row['tenant'];?> </a>
                                        </td>
                                        <td class="f_s_12 f_w_400 text_color_1 "><a href="/services/edit/<?= $row['id'];?>"><?= $row['code'];?></a>
                                        </td>
                                          <td class="f_s_12 f_w_400  "><a href="/services/edit/<?= $row['id'];?>">
                                           <?php if(!empty($row['image_logo'])) { ?>
                                            <img src="<?='data:image/jpeg;base64,'.$row['image_logo'];?>" width="150px">
										<?php } ?>
                                        </a>
                                        </td>

                                          <td class="f_s_12 f_w_400  "><a href="/services/edit/<?= $row['id'];?>">
                                           <?php if(!empty($row['image_brand'])) { ?>
                                            <img src="<?='data:image/jpeg;base64,'.$row['image_brand'];?>" width="150px">
										<?php } ?>
                                        </a>
                                        </td>
                                        <td class="f_s_12 f_w_400 text_color_1 ">
                                             <p class="pd10"> <?= $row['notes'];?> </p>
                                        </td>
                                        
                                          <td class="f_s_12 f_w_400 text_color_1 "><a href="/services/edit/<?= $row['id'];?>"> <div class="">
                                                <label class="switch2">
                                                  <input type="checkbox" class="checkb" data-url="services/status" name="checkb[]" value="<?= $row['id'];?>" <?=($row['status']==1)?'checked':''?>  />
                                                  <span class="slider round"></span>
                                                </label>
                                            </div></a>
                                        </td>
                                        <td class="f_s_12 f_w_400 text-right">
                                            <div class="header_more_tool">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                      <i class="ti-more-alt"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                      
                                                      <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/services/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                                      <a class="dropdown-item" href="/services/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>
                                                      
                                                      
                                                    </div>
                                                </div>
                                            </div>
                                        </td> 
									 									
                                    </tr>
                                  <?php endforeach;?>  
                                    

                                       
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>


<?php include('common/scripts.php'); ?>