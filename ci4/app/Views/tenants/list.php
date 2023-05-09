<?php require_once (APPPATH.'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    
                    <th scope="col">Id</th>
                    <th scope="col">Services</th>
                    <th scope="col">Name</th>
                    <th scope="col">Address</th>
                    <th scope="col">Contact Name</th>
                    <th scope="col">Contact Email</th>
                    
                    <th scope="col">Note</th>
                <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>                                        
            
                <?php foreach($tenants as $row):?>
            <tr  data-link="/tenants/edit/<?= $row['id'];?>">
                
                <td class="f_s_12 f_w_400"><?= $row['id'];?>
                </td>
                <td class="f_s_12 f_w_400"><?= $row['service_name'];?> 
                </td>
                <td class="f_s_12 f_w_400  "><?= $row['name'];?>
                </td>
                <td class="f_s_12 f_w_400  "><?= $row['address'];?>
                </td>
                <td class="f_s_12 f_w_400  "><?= $row['contact_name'];?>
                </td>
                <td class="f_s_12 f_w_400  "><?= $row['contact_email'];?>
                </td>
                
                <td class="f_s_12 f_w_400  ">
                        <p class="pd10"> <?= $row['notes'];?> </p>
                </td>
                <td class="f_s_12 f_w_400 text-right">
                    <div class="header_more_tool">
                        <div class="dropdown">
                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                <i class="ti-more-alt"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                
                                <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/tenants/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                <a class="dropdown-item" href="/tenants/edit/<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>
                                
                                
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
<?php require_once (APPPATH.'Views/common/footer.php'); ?>