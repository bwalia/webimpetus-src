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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30" >Users </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Users List</li>
                            </ol>
                        </div>
                        <div class="page_title_right">

                           <!--p class="showingTotal">
                            Showing <?=count($users);?> out of <?=count($users);?></p-->
                           
                            <div class="header_more_tool setDropDownBlk">
                              
                            <a href="users/add" class="btn btn-primary"><i class="ti-plus"></i> Add User</a>
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
                                    <h3 class="m-0">Users List</h3>
                                </div>
                              
                            </div>
                        </div>
                        <div class="white_card_body ">
                            <div class="QA_table ">
                                <!-- table-responsive -->
                                <table id="example"  class="table table-listing-items tableDocument table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <!--th scope="col">
                                                <input type="checkbox" class="check_all" onclick="set_check_all(this);">
                                            </th-->
                                            <th scope="col">Id</th>
                                            <th scope="col">UUID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Password</th>
                                            <th scope="col">Address</th>
                                            
                                            <th scope="col">Note</th>
                                            <th scope="col">Status</th><th scope="col" width="50">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                  
                                    <?php foreach($users as $row):?>
									
									<tr data-link="/users/edit/<?= $row['id'];?>">
                                        
                                        <!--td class="checkDocument">
                                            <input type="checkbox" class="check_all" onclick="set_check_all(this);">
                                        </td-->
                                        <td class="f_s_12 f_w_400"><a href="/users/edit/<?= $row['id'];?>"><?= $row['id'];?></a></td>
                                        <td class="f_s_12 f_w_400"><a href="/users/edit/<?= $row['id'];?>"><?= $row['uuid'];?> </a></td>
                                        <td class="f_s_12 f_w_400  "><a href="/users/edit/<?= $row['id'];?>"><?= $row['name'];?></a></td>
                                         <td class="f_s_12 f_w_400  "><a href="/users/edit/<?= $row['id'];?>"><?= $row['email'];?></a></td>
                                         <td class="f_s_12 f_w_400  "><a href="/users/edit/<?= $row['id'];?>"><?= $row['password'];?></a></td>
                                         <td class="f_s_12 f_w_400  "><a href="/users/edit/<?= $row['id'];?>"><?= $row['address'];?></a></td>
                                          
                                            <td class="f_s_12 f_w_400  ">
                                             <p class="pd10"> <?= $row['notes'];?></p>
                                            </td>
                                       <td class="f_s_12 f_w_400  ">
                                              <div class="">
                                                <label class="switch2">
                                                  <input type="checkbox" class="checkb" data-url="users/status" name="checkb[]" value="<?= $row['id'];?>" <?=($row['status']==1)?'checked':''?>  />
                                                  <span class="slider round"></span>
                                                </label>
                                            </div>
                                            </td> <td class="f_s_12 f_w_400 text-right">
                                            <div class="header_more_tool">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                      <i class="ti-more-alt"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                      
                                                      <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/users/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                                      <a class="dropdown-item" href="/users/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>
                                                      
                                                      
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

<?php include('common/footer.php'); ?>
</section>
<!-- main content part end -->

<?php include('common/scripts.php'); ?>