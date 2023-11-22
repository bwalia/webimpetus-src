<?php require_once (APPPATH.'Views/common/list-title.php'); ?>

<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>

                    <th scope="col">Select</th>
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

                <?php
                
                foreach($webpages as $row):
                    if(isset($menuName)){
                        $link = "/webpages/edit/".$row['uuid']."?cat=strategies";
                    }else{
                       $link = "/webpages/edit/".$row['uuid'];
                    }
                    ?>
                    <tr data-link=<?=$link?>>
                        <td>
                            <input type="checkbox" class="row-select">
                        </td>
                        <td class="f_s_12 f_w_400" ><?= $row['id'];?></td>
                        <td class="f_s_12 f_w_400"><?= $row['title'];?></td>
                        <td class="f_s_12 f_w_400"><?= $row['sub_title'];?>      </td>
                        <td class="f_s_12 f_w_400 <?=$row['status']==0?'text_color_1':'text_color_2'?> ">
                           <span class="stsSpan"> <?=$row['status']==0?'inactive':'active'?></span>
                        </td>

                        <td class="f_s_12 f_w_400  ">
                            <p class="pd10"> <?= date('Y-m-d H:i:s',$row['publish_date']);?></p>
                        </td>

                        <td class="f_s_12 f_w_400 ">
                            <p class="pd10"> <?= $row['created'];?></p>
                        </td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/webpages/delete/<?= $row['id'];?>"> <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="/webpages/edit/<?= $row['uuid'];?>"> <i class="fas fa-edit"></i> Edit</a>


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