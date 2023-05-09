<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">

        <form id="adddomain" method="post" action="/gallery/update" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputEmail4">Code</label>
                    <input type="text" class="form-control" id="title" name="code" value="<?=@$content->code?>" placeholder="">
                    <input type="hidden" class="form-control" name="id" placeholder="" value="<?=@$content->id ?>" />
                </div>

                <div class="form-group col-md-12">
                    <span class="all-media-image-files">
                    <?php if(!empty(@$content->name)) { ?>
                        <img src="<?=@$content->name?>" width="140px">
                    <?php } ?>
                    </span>
                    <label for="inputAddress">Upload Image</label>
                    <div class="uplogInrDiv" id="drop_file_doc_zone">
                        <input type="file" name="file" class="fileUpload" id="customFile">
                        <div class="uploadBlkInr">
                            <div class="uplogImg">
                              <img src="/assets/img/fileupload.png" />
                            </div>
                            <div class="uploadFileCnt">
                              <p>
                                <a href="#">Upload a file </a> file chosen or drag
                                and drop
                              </p>
                              <p>
                                <span>Video, PNG, JPG, GIF up to 10MB</span>
                              </p>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="form-group col-md-12">
                    <label for="inputEmail4">Status</label>
                </div>
                <div class="form-group col-md-12">

                    <label for="inputEmail4"><input type="radio" value="1" class="form-control" id="status" name="status" <?=@$content->status==1?'checked':''?> placeholder=""> Yes</label>

                    <label for="inputEmail4"><input type="radio" <?=@$content->status==0?'checked':''?> value="0" class="form-control" id="status" name="status" placeholder=""> No</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>


<?php require_once (APPPATH.'Views/common/footer.php'); ?>
<script>



   var id = "<?=@$content->id ?>";
   
   $(document).on('drop', '#drop_file_doc_zone', function(e){
       alert(2)
       // $("#ajax_load").show();
       console.log(e.originalEvent.dataTransfer);
           if(e.originalEvent.dataTransfer){
               if(e.originalEvent.dataTransfer.files.length) {
                   e.preventDefault();
                   e.stopPropagation();
                   var i = 0;
                   while ( i < e.originalEvent.dataTransfer.files.length ){
                       newUploadDocFiles(e.originalEvent.dataTransfer.files[i], id);
                       i++;
                   }
               }   
           }
       }
   );
   
           
   $(document).on("change", ".fileUpload", function() {
   
       for (var count = 0; count < $(this)[0].files.length; count++) {
   
           newUploadDocFiles($(this)[0].files[count], id);
       }
   
   });
   
   
   
   function newUploadDocFiles(fileobj, id) {
   
       $("#ajax_load").hide();
   
       var form = new FormData();
   
       form.append("file", fileobj);
       form.append("mainTable", class_name);
       form.append("id", id);

        $.ajax({
            url: '/gallery/uploadMediaFiles',
            type: 'post',
            dataType: 'json',
            maxNumberOfFiles: 1,
            autoUpload: false,
            success: function(result) {

                $("#ajax_load").hide();
                if (result.status == '1') {
                    $(".all-media-image-files").append(result.file_path);
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#ajax_load").hide();
                console.log(textStatus, errorThrown);
            },
            data: form,
            cache: false,
            contentType: false,
            processData: false
       });

   }

   $("#delete_image_logo").on("click", function(e){
      e.preventDefault();
      $(".all-media-image-files").html("");
   })

</script>