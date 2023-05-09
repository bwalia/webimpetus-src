<?php require_once (APPPATH.'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <?php foreach ($fields as $field) { ?>
                    <th scope="col"><?php echo readableFieldName($field); ?></th>
                    <?php } ?>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>                                        
            
                <?php foreach(${$tableName} as $row) { ?>
                    <tr  data-link="/<?php echo $tableName; ?>/edit/<?= $row[$identifierKey];?>">

                        <?php foreach ($fields as $field) { ?>
                        <td class="f_s_12 f_w_400"><?= $row[ $field ];?></td>
                        <?php } ?>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        
                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/<?php echo $tableName; ?>/delete/<?= $row[ $identifierKey ];?>"> <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="/<?php echo $tableName; ?>/edit/<?= $row[ $identifierKey ];?>"> <i class="fas fa-edit"></i> Edit</a>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </td>                                       
                    </tr>
                <?php } ?>  
                
            </tbody>
        </table>
    </div>
</div>
<?php require_once (APPPATH.'Views/common/footer.php'); ?>