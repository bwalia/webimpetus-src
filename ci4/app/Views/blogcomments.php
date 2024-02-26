<?php 
    include('common/header.php'); 
    $roles = getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false);
?>
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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Blog Comments </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Blog Comments</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                          <?php if ((@$_SESSION['role'] && isset($roles['role_name']) && $roles['role_name'] == "Administrator") || session('uuid') == 1) { ?>
                            <!--div class="header_more_tool setDropDownBlk">
                              
                            <a href="/jobapps/add" class="btn btn-primary"><i class="ti-plus"></i> Add job</a>
						  </div--> <?php } ?>

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
                              
                            </div>
                        </div>
                        <div class="white_card_body ">
                            <div class="QA_table ">
                                <!-- table-responsive -->
                                <table id="example"  class="table tableDocument table-bordered table-hover">
                                    <thead>
                                        <tr>
                                           
                                            <th scope="col">Id</th>
                                            <th scope="col">Name</th>
											<th scope="col">Email</th>
                                            <th scope="col">Message</th>
                                            
											<th scope="col">Created at</th>
                                          <th scope="col" width="50">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                  
                                    <?php foreach($content as $row):?>
                                    <tr>
                                       
                                        <td class="f_s_12 f_w_400"><?= $row['id'];?></td>
                                        <td class="f_s_12 f_w_400"><?= $row['name'];?>
										<td class="f_s_12 f_w_400"><?= $row['email'];?>
                                        </td>
                                        
                                       
                                        <td class="f_s_12 f_w_400  ">
                                             <p class="pd10"> <?= $row['message'];?></p>
                                        </td>
										
										<td class="f_s_12 f_w_400  ">
                                             <p class="pd10"> <?= $row['created'];?></p>
                                        </td>
                                        <td class="f_s_12 f_w_400 text-right">
                                            <div class="header_more_tool">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                      <i class="ti-more-alt"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                      
                                                      <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/jobapps/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                                      <!--a class="dropdown-item" href="/Enquiries/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a-->
                                                      
                                                      
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