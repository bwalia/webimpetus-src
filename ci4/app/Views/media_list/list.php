 <?php require_once(APPPATH . 'Views/media_list/list-title.php'); ?>
 <div class="white_card_body ">
     <div class="QA_table ">
         <!-- table-responsive -->
         <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
             <thead>
                 <tr>
                     <th scope="col">Id</th>
                     <th scope="col">Code</th>
                     <th scope="col">Image</th>
                     <th scope="col">Status</th>
                     <th scope="col">Created at</th>
                     <th scope="col" width="50">Action</th>
                 </tr>
             </thead>
             <tbody>

                 <?php foreach ($media_list as $row) : ?>
                     <tr data-link="/gallery/edit/<?= $row['id']; ?>">

                         <td class="f_s_12 f_w_400"><?= $row['id']; ?></td>
                         <td class="f_s_12 f_w_400"><?= $row['code']; ?>
                         <td class="f_s_12 f_w_400"><?php if (!empty($row['name'])) {
$tokens = explode('.', $row['name']);
$extension = $tokens[count($tokens)-1];

$varray = ['webm', 'wmv', 'ogg', 'mp4', 'mov', 'flv', 'avi', 'mkv'];
                        
                    if(in_array(trim($extension),$varray)){  
                        ?>

    <video width="320" height="240" controls>
        <?php foreach($varray as $val){ ?>
            <source src="<?=@$row['name']?>" type=video/<?=$val?>>
        <?php } ?>
    </video><br>


    <?php } else { 
                                                        echo render_image($row['name']);
                                                  }  } ?>
                         </td>
                         <td class="f_s_12 f_w_400 <?= $row['status'] == 0 ? 'text_color_1' : 'text_color_2' ?> ">
                             <span class="stsSpan"><?= $row['status'] == 0 ? 'inactive' : 'active' ?></span>
                         </td>


                         <td class="f_s_12 f_w_400  ">
                             <p class="pd10"> <?= $row['created']; ?></p>
                         </td>
                         <td class="f_s_12 f_w_400 text-right">
                             <div class="header_more_tool">
                                 <div class="dropdown">
                                     <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                         <i class="ti-more-alt"></i>
                                     </span>
                                     <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                         <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/gallery/delete/<?= $row['id']; ?>"> <i class="ti-trash"></i> Delete</a>
                                         <a class="dropdown-item" href="/gallery/edit/<?= $row['id']; ?>"> <i class="fas fa-edit"></i> Edit</a>
                                         <a class="dropdown-item" id="copyToClipBoard" link='<?= str_replace("https://webimpetus-images.s3.eu-west-2.amazonaws.com", base_url(), $row['name']); ?>'> <i class="fas fa-copy"></i> Copy Link</a>
                                     </div>
                                 </div>
                             </div>
                         </td>
                     </tr>
                 <?php endforeach; ?>
             </tbody>
         </table>
     </div>
 </div>
 <?php require_once(APPPATH . 'Views/common/footer.php'); ?>

 <script>
     $(document).on("click", "#copyToClipBoard", function(e) {
         e.preventDefault();
         var val = $(this).attr("link");
         copyToClipboard(val);
     });

     function copyToClipboard(text) {
         var sampleTextarea = document.createElement("textarea");
         document.body.appendChild(sampleTextarea);
         sampleTextarea.value = text; //save main text in it
         sampleTextarea.select(); //select textarea contenrs
         document.execCommand("copy");
         document.body.removeChild(sampleTextarea);
     }
 </script>