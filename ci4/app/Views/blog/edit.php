<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>
<div class="white_card_body">
   <div class="card-body">
      <form id="addcat" method="post" action="/blog/update" enctype="multipart/form-data">
         <input type="hidden" class="form-control" name="id" placeholder="" value="<?=@$content->id ?>" />
         <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?=@$content->uuid ?>" />
         <input type="hidden" class="form-control" name="type" placeholder="" value="2" />
         <div class="row">
            <div class="col-xs-12 col-md-12">
               <nav>
                  <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                     <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Page Editor</a>
                     <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Search Optimisation</a>
                     <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Pictures</a>
                     <a class="nav-item nav-link" id="nav-about-tab" data-toggle="tab" href="#nav-about" role="tab" aria-controls="nav-about" aria-selected="false">Page Setup</a>					  
                  </div>
               </nav>
               <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                     <div class="form-row">
                        <div class="form-group col-md-12">
                           <label for="inputEmail4">Title*</label>
                           <input type="text" class="form-control" value="<?=@$content->title?>" id="title" name="title" placeholder="">
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputEmail4">Sub Title</label>
                           <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="" value="<?=@$content->sub_title?>">
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputPassword4">Body*</label>
                           <textarea class="form-control" name="content" id="content" ><?=@$content->content?></textarea> 
                        </div>
                        <div class="">
                           <label for="inputEmail4">Status</label>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputEmail4" class="pr_10"><input type="radio" value="1" class="form-control" id="status" name="status" <?=@$content->status==1?'checked':''?> placeholder=""> Yes</label>
                           <label for="inputEmail4"><input type="radio" <?=@$content->status==0?'checked':''?> value="0" class="form-control" id="status" name="status" placeholder=""> No</label>
                        </div>
                        <!-- <div class="">
                           <label>Blog Type</label>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="blog_type" class="pr_10">
                              <input 
                                 type="radio" 
                                 value="1" 
                                 class="form-control" 
                                 id="blog_type" 
                                 name="blog_type" 
                                 <?=@$content->blog_type == 1 ? 'checked' : ''?> 
                                 placeholder=""
                              >
                              Public
                           </label>
                           <label for="blog_type">
                              <input 
                                 type="radio" 
                                 <?=@$content->blog_type == 0 ? 'checked' : ''?> 
                                 value="0" 
                                 class="form-control" 
                                 id="blog_type" 
                                 name="blog_type" 
                                 placeholder=""
                              > 
                              Private
                           </label>
                        </div> -->
                        <div class="form-group col-md-4">
                           <label for="blog_type">Blog Type</label>
                           <select class="custom-select" id="blog_type" name="blog_type">
                              <option selected>Please Select the Blog Type</option>
                              <option value="1" <?=@$content->blog_type == 1 ? 'selected' : ''?> >Public</option>
                              <option value="0" <?=@$content->blog_type == 0 ? 'selected' : ''?>>Private</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                     <div class="form-row">
                        <div class="form-group col-md-12">
                           <label for="inputEmail4">URL Code*</label>
                           <input type="text" class="form-control" id="code" name="code" placeholder="" readonly="readonly" value="<?=@$content->code?>" onchange="format_manual_code('Code')">
                           <span class="help-block">URL (SEO friendly)</span><br>
                           <div class="mt_10"><span class="help-block ">
                           <input type="checkbox" name="chk_manual" id="chk_manual">
                           I want to manually enter code</span>
                        </div>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputEmail4">Meta keywords</label>
                           <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="" value="<?=@$content->meta_keywords?>">
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputEmail4">Meta Title</label>
                           <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="" value="<?=@$content->meta_title?>">
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputPassword4">Meta Description</label>
                           <textarea class="form-control" name="meta_description"><?=@$content->meta_description?></textarea> 
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                     <div class="form-row">
                        
                     <span class="all-media-image-files">
                        <?php 
                           $json = @$content->custom_assets?json_decode(@$content->custom_assets):[]; ?>
                        <?php foreach($images as $image){
                           if(!empty(@$image)) { ?>
                        <img class="img-rounded" src="<?= $image['image'];?>" width="100px">
                        <a href="/blog/rmimg/<?=@$image['id'].'/'.@$content->id; ?>" onclick="return confirm('Are you sure?')" class=""><i class="fa fa-trash"></i></a>
                        <?php 
                           } 
                           
                           }
                           ?>
                        </span>
                        <div class="form-group col-md-12" id="divfile">
                           <label for="inputAddress">Upload</label>
                           <div class="uplogInrDiv" id="drop_file_doc_zone">
                              <input type="file" name="file[]" class=" fileUpload" id="">
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
                     </div>
                  </div>
                  <div class=" fade" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                     <div class="form-row">
                        <div class="form-group col-md-12">
                           <label for="inputState">Choose User</label>
                           <select id="user_uuid" name="user_uuid" class="form-control">
                              <option value="0" selected="">--Select--</option>
                              <?php foreach($users as $row):?>
                              <option value="<?= $row['uuid'];?>"  <?=($row['uuid']==@$content->uuid)?'selected':''?>><?= $row['name'];?></option>
                              <?php endforeach;?>
                           </select>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputState">Choose Category</label>
                           <select id="catid" name="catid[]" multiple class="form-control select2">
                              <?php foreach($cats as $row):?>
                              <option value="<?= $row['id'];?>" <?=in_array($row['id'],$selected_cats)?'selected':''?>><?= $row['name'];?></option>
                              <?php endforeach;?>
                           </select>
                        </div>
                        <div class="form-group col-md-12">
                           <label for="inputState">Publish Date</label>
                           <input id="publish_date" class="form-control datepicker" name="publish_date" width="250" type="datepicker"  value="<?=render_date(@$content->publish_date)?>" />
                        </div>
                     </div>
                  </div>
               </div>
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
           url: '/blog/uploadMediaFiles',
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
<style>
   .custom-file{
   margin:30px;
   }
</style>