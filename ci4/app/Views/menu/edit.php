<?php require_once (APPPATH.'Views/common/edit-title.php'); 

$str = file_get_contents(APPPATH . 'languages.json');
$json = json_decode($str, true);
//print_r($json); die;
?>
    <div class="white_card_body">
        <div class="card-body">
            
            <form id="addcustomer" method="post" action=<?php echo "/".$tableName."/update";?> enctype="multipart/form-data">
                <div class="form-row">
                   

                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">Name</label>
                        <input type="input" class="form-control required" id="name" name="name" placeholder=""  value="<?= @$data->name ?>">
                    </div>
                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">Link</label>
                        <input type="input" class="form-control required" id="link" name="link" placeholder=""  value="<?= @$data->link ?>">
                    </div>

                </div>
                <div class="form-row">
                   

                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">Icon</label>
                        <input type="input" class="form-control required" id="icon" name="icon" placeholder=""  value="<?= @$data->icon ?>">
                    </div>


                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Categories</label>
                        <select id="categories" name="categories[]" multiple class="form-control select2">
                            <?php 
                            if (isset($categories) && (!empty($categories))) {
                            foreach($categories as $row):?>
                            <option value="<?= $row['id'];?>" <?php  if(!empty($selected_cat)) echo 
                            in_array($row['id'],$selected_cat)?'selected="selected"':''?>><?= $row['name'];?></option>
                            <?php endforeach;?>
                            <?php } ?>
                    </select>
                    </div>
                   

                </div>


                <div class="form-row">
                   

                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">FTS Tags</label>
                        
                        <select id="my-select2" data-select2-tags="true" name="tags[]" multiple="multiple" class="form-control select2 required">
                        <?php 
                            if (!empty($data->menu_fts)) {
                            $arr = explode(',',$data->menu_fts);
                            foreach($arr as $row):?>
                            <option value="<?= $row;?>" selected="selected"><?= $row;?></option>
                            <?php endforeach;?>
                            <?php } ?>
                    </select>
                    </div>

                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">Language Code</label>
                        <select name="language_code" class="required form-control">
                            <?php foreach ($json as $key=>$row) : ?>
                                    <option value="<?= $key; ?>" <?=@$data->language_code == $key?'selected="selected"' : ''; ?>><?= $row; ?></option>
                            <?php endforeach; ?>
                           
                        </select>
                    </div>
                   

                </div>
                    
               
                <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$data->id ?>" />

                </div>

              

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
   
<?php require_once (APPPATH.'Views/common/footer.php'); ?>
<!-- main content part end -->

<script>
$(document).on("click", ".form-check-input", function(){
    if($(this).prop("checked") == false){
        $(this).val(0);
    }else{
        $(this).val(1);
    }
});

$('#my-select2').select2({
    tags: true
})
</script>
