<?php require_once (APPPATH.'Views/common/list-title.php'); 
$status = ["Estimate", "Quote","Ordered","Acknowledged","Authorised","Delivered","Completed","Proforma Invoice"];
?>
    <div class="white_card_body ">
        <div class="QA_table ">
            <!-- table-responsive -->
            <table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
            <tr>
                    <th scope="col"><?php echo lang('Common.ID');?></th>
                    <th scope="col"><?php echo lang('Purchase_invoice.order_number');?></th>
                    <th scope="col"><?php echo lang('Purchase_invoice.due_date');?></th>
                    <th scope="col"><?php echo lang('Purchase_invoice.client');?></th>
                    <th scope="col"><?php echo lang('Purchase_invoice.project_code');?></th>
                    <th scope="col"><?php echo lang('Purchase_invoice.total_paid');?></th>
                    <th scope="col"><?php echo lang('Purchase_invoice.balance_outstanding');?></th>
                    <th scope="col"><?php echo lang('Purchase_invoice.paid_date');?></th>
                    <th scope="col"><?php echo lang('Common.status');?></th>
                    <th scope="col" width="50"><?php echo lang('Common.action');?></th>
                </tr>
                </thead>
                <tbody>                                                       
                <?php foreach($work_orders as $row):?>
                <tr data-link=<?= "/".$tableName."/edit/".$row['uuid'];?> >                  
                    <td class="f_s_12 f_w_400"><?= $row['id'];?>
                    </td>
                    <td class="f_s_12 f_w_400"><?= $row['order_number'];?>
                    </td>
                    <td class="f_s_12 f_w_400  "><?= render_date($row['date']);?> </td>
                    <td class="f_s_12 f_w_400  "><?= $row['company_name'];?> </td>
                    <td class="f_s_12 f_w_400  "><?= $row['project_code'];?> </td>
                    <td class="f_s_12 f_w_400  "><?= $row['total'];?> </td>
                    <td class="f_s_12 f_w_400  "><?= $row['balance_due'];?> </td>
                    <td class="f_s_12 f_w_400  "><?= render_date($row['paid_date']);?> </td>
                    <td class="f_s_12 f_w_400  "><?= $status[$row['status']];?> </td>
                    <td class="f_s_12 f_w_400 text-right">
                        <div class="header_more_tool">
                            <div class="dropdown">
                                <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                    <i class="ti-more-alt"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href=<?= "/".$tableName."/deleterow/".$row['uuid'];?>>
                                    <i class="ti-trash"></i> Delete</a>
                                    <a class="dropdown-item" href="<?= "/".$tableName."/edit/".$row['uuid'];?>"> <i class="fas fa-edit"></i> Edit</a>
                                    <a class="dropdown-item" href="<?= "/" . $tableName . "/exportPDF/" . $row['uuid'] . "?" . rand(0,999999); ?>"> <i class="ti-printer"></i> Print PDF</a>                             
                                    <a class="dropdown-item" href="/<?php echo $tableName; ?>/clone/<?= $row['uuid']; ?>"> <i class="fas fa-copy"></i> Clone</a>
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