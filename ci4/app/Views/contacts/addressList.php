<table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
    <thead>
        <tr>
            
            <th scope="col">Address line one</th>
            <th scope="col">Address line two</th>
            <th scope="col">City</th>
            <th scope="col">State</th>
            <th scope="col">Post code</th>
            <th scope="col">Country</th>
            <th scope="col">Address Type</th>
            <th scope="col" width="50">Action</th>
        </tr>
    </thead>
    <tbody>                                        
    
    <?php foreach($data as $row):?>
    <tr data-row-id="<?= $row['id'];?>">
        
        <td class="f_s_12 f_w_400"><?= $row['address_line_1'];?></td>
        <td class="f_s_12 f_w_400"><?= $row['address_line_2'];?> </td>
        <td class="f_s_12 f_w_400  "><?= $row['city'];?></td>
        <td class="f_s_12 f_w_400  "><?= $row['state'];?> </td>
        <td class="f_s_12 f_w_400  "><?= $row['post_code'];?> </td>
        <td class="f_s_12 f_w_400  "><?= $row['country'];?> </td>
        <td class="f_s_12 f_w_400  "><?= $row['address_type'];?> </td>
       
        <td class="f_s_12 f_w_400 text-right">
            <div class="header_more_tool">
                <div class="dropdown">
                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                        <i class="ti-more-alt"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        
                        <a class="dropdown-item delete-addresses" onclick="return confirm('Are you sure want to delete?');" data-id="<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                        <a class="dropdown-item edit-addresses"  data-id="<?= $row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>
                        
                        
                    </div>
                </div>
            </div>
        </td>   
                                            
    </tr>
    
    <?php endforeach;?>  

        
    </tbody>
</table>