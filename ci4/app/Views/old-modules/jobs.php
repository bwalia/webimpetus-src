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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Jobs </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Jobs List</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                         
                            <div class="header_more_tool setDropDownBlk">
                              
                            <a href="/jobs/add" class="btn btn-primary"><i class="ti-plus"></i> Add job</a>
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
                              
                            </div>
                        </div>
                        <div class="white_card_body ">
                            <div class="QA_table ">
                                <!-- table-responsive -->
                                <table id="example"  class="table table-listing-items tableDocument table-bordered table-hover">
                                    <thead>
                                        <tr>
                                           
                                            <th scope="col">Id</th>
                                            <th scope="col">Title</th>
											<th scope="col">Sub title</th>
                                            <th scope="col">Status</th>
                                            
											<th scope="col">Published at</th>
											<th scope="col">Created at</th>
                                            <th scope="col" width="50">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                  
                                    <?php foreach($content as $row):?>
                                    <tr data-href="/jobs/edit/<?= $row['id'];?>">
                                       
                                        <td class="f_s_12 f_w_400"><?= $row['id'];?></td>
                                        <td class="f_s_12 f_w_400"><?= $row['title'];?>
										<td class="f_s_12 f_w_400"><?= $row['sub_title'];?>
                                        </td>
                                        <td class="f_s_12 f_w_400 <?=$row['status']==0?'text_color_1':'text_color_2'?> "><?=$row['status']==0?'inactive':'active'?>
                                        </td>
                                        <?php /* ?><td class="f_s_12 f_w_400  ">
										<?php if(!empty($row['image_logo'])) { ?>
                                            <img src="<?='data:image/jpeg;base64,'.$row['image_logo']?>" width="200px">
										<?php } ?>
                                        </a>
                                        </td> */ ?>
                                        <td class="f_s_12 f_w_400 text_color_1 ">
                                             <p class="pd10"> <?= date('Y-m-d H:i:s',$row['publish_date']);?></p>
                                        </td>
										
										<td class="f_s_12 f_w_400 text_color_1 ">
                                             <p class="pd10"> <?= $row['created'];?></p>
                                        </td>
                                       <td class="f_s_12 f_w_400 text-right">
                                            <div class="header_more_tool">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                      <i class="ti-more-alt"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                      
                                                      <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/jobs/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                                      <a class="dropdown-item" href="/jobs/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>
                                                      
                                                      
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