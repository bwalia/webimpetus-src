
<?php require_once (APPPATH.'Views/common/list-title.php'); ?>

<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example"  class="table table-listing-items tableDocument table-bordered table-hover">
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
                    <tr >

                        <td class="f_s_12 f_w_400"><?= $row['id'];?></td>
                        <td class="f_s_12 f_w_400"><?= $row['name'];?>
                        <td class="f_s_12 f_w_400"><?= $row['email'];?></td>


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

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/blog/delete/<?= $row['id'];?>/blogcomments"> <i class="ti-trash"></i> Delete</a>
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
<?php require_once (APPPATH.'Views/common/footer.php'); ?>