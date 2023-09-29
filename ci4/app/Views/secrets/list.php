<?php require_once (APPPATH.'Views/common/list-title.php'); ?>

<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" cellpadding="5" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    
                    <th scope="col">Id</th>
                    <th scope="col">Key name</th>
                    
                    <th scope="col">Services</th>
                    
                    <th scope="col">Created at</th>
                    <?php if(!empty($_SESSION['role'])){ ?><th scope="col" width="50">Action</th><?php } ?>
                </tr>
            </thead>
            <tbody>                                        
            
            <?php foreach($content as $row): ?>
            <tr data-link="/secrets/edit/<?= $row['uuid'];?>">
                
                <td class="f_s_12 f_w_100"> <?= $row['id'];?> </td>
                <td class="f_s_12 f_w_200"><?= $row['key_name'];?></td>
                <td class="f_s_12 f_w_100"><?= $row['name'];?></td>
                
                <?php /* ?><td class="f_s_12 f_w_400 <!--?=$row['status']==0?'text_color_1':'text_color_2'?--> "><?=$row['status']==0?'XXXXXXXXX':$row['key_value']?>
                </td>
                <td class="f_s_12 f_w_400  ">
                <?php if(!empty($row['image_logo'])) { ?>
                    <img src="<?='data:image/jpeg;base64,'.$row['image_logo']?>" width="200px">
                <?php } ?>
                
                </td> */ ?>
                                                        
                <td class="f_s_12 f_w_400  ">
                <p class="pd10"> <?= $row['created'];?></p>
                </td>
                <?php if(!empty($_SESSION['role'])){ ?> <td class="f_s_12 f_w_400 text-right">
                    <div class="header_more_tool">
                        <div class="dropdown">
                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                <i class="ti-more-alt"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                
                                <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/secrets/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                <a class="dropdown-item" href="/secrets/edit/<?= $row['uuid'];?>"> <i class="fas fa-edit"></i> Edit</a>
                                
                                
                            </div>
                        </div>
                    </div>
                </td>   
                <?php } ?>                                        
            </tr>
            
            <?php endforeach;?>  

            </tbody>
        </table>
    </div>
</div>

<?php require_once (APPPATH.'Views/common/footer.php'); ?>