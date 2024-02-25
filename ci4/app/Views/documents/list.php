<?php require_once (APPPATH.'Views/common/list-title.php'); ?>
    <div class="white_card_body ">
        <div class="QA_table ">
            <!-- table-responsive -->
            <table id="example"  class="table table-listing-items tableDocument table-striped table-bordered">
                <thead>
                    <tr>
                        
                        <th scope="col">File</th>
                        <th scope="col">Client</th>
                        <th scope="col">Created</th>
                        <th scope="col">Modified</th>

                        <th scope="col" width="50">Action</th>
                        <th scope="col" width="500">Preview</th>
                    </tr>
                </thead>
                <tbody>                                        

                <?php foreach($documents as $row):
                    $html = "";
                    $url = base_url()."/document/view/".$row['uuid'];
                    $html = '<a href="'.$url.'" target="_blank">Document Link</a>';
                    ?>
                <tr data-link=<?= "/".$tableName."/edit/".$row['id'];?> >

                    <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id']?>"><?= !empty($row['file'])?basename($row['file']):'';?></td>
                    <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id']?>"><?= $row['company_name'];?></td>

                    <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id']?>"><?php if (isset($row['created_at']) && (!empty($row['created_at']))) { echo render_date(strtotime($row['created_at'])); } ?></td>
                    <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id']?>"><?php if (isset($row['modified_at']) && (!empty($row['modified_at']))) { echo render_date(strtotime($row['modified_at'])); } ?></td>

                    <td class="f_s_12 f_w_400 text-right">
                        <div class="header_more_tool">
                            <div class="dropdown">
                                <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                    <i class="ti-more-alt"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href=<?= "/".$tableName."/delete/".$row['id'];?>>
                                    <i class="ti-trash"></i> Delete</a>
                                    <a class="dropdown-item" href="<?= "/".$tableName."/edit/".$row['id'];?>"> <i class="fas fa-edit"></i> Edit</a>
                                    <a class="dropdown-item" id="copyToClipBoard" link='<?=!empty($row['file'])?str_replace("https://webimpetus-images.s3.eu-west-2.amazonaws.com", $front_domain, $row['file']):'';?>'> <i class="fas fa-copy"></i> Copy Link</a>
                                </div>
                            </div>
                        </div>
                    </td>   
                    <td class="f_s_12 f_w_400 preview-file" ></td>
                                                        
                </tr>
                
                <?php endforeach;?>  
   
                    
                </tbody>
            </table>
        </div>
    </div>

<?php require_once (APPPATH.'Views/documents/footer.php'); ?>

<script>

$('.table-listing-items  tr  td').on('click' , function(e) {
	  
      var dataClickable = $(this).parent().attr('data-link');
      if($(this).is(':last-child') || $(this).is(':first-child')){
      }
          
  });
  
$(document).on("click", ".open-file", function(e){ 

    e.preventDefault();

    var el = $(this);
    var rowid = el.data('id');

    $.ajax({
            url: baseURL + "documents/getfile",
            data:{ rowid:rowid },
            method: 'POST',
            dataType: "JSON",
    }).done(function(response){
        var html = '';
        if (response.file.length > 0) {

            html = '<iframe  src="https://docs.google.com/gview?embedded=true&url='+ response.file +'" width="512" height="500"> </iframe>';
        }

        $('.preview-file').html('');
        setTimeout(function(){
            el.closest('tr').find('.preview-file').html(html);
        }, 500);
    });


})

$(document).on("click", "#copyToClipBoard", function(e){
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