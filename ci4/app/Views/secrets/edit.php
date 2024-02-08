<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>
                       
<div class="white_card_body">
    <div class="card-body">
        
        <form id="adddomain" method="post" action="/secrets/update" enctype="multipart/form-data">
            <div class="form-row">
            
                <div class="form-group required col-md-12">
                    <label for="inputEmail4">Secret Key</label>
                    <input type="text" class="form-control required" id="title" name="key_name" placeholder=""  value="<?=@$secret->key_name?>">
                </div>
                <input type="hidden" class="form-control" name="id" placeholder="" value="<?=@$secret->id ?>" />
                <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?=@$secret->uuid ?>" />

                    
                    <div class="form-group col-md-12">
                    <label for="inputPassword4">Secret Value</label>
                    <textarea class="form-control" name="key_value" style="width:100%!important;height:250px" id="key_value"><?php if((@$secret->id) > 0 ){ ?>*****************<?php } ?></textarea> 
                </div>
            
                <?php if($sservices){?>
                <div class="form-group col-md-12">
                    <label for="inputState">Service Name</label><br>
                    <?php foreach($sservices as $row):?>
                    <strong>   <?php   echo $row['name']; ?></strong>
                    <?php endforeach;?>
         
                </div>
                <?php }?>
                
            <!-- <div class="form-group col-md-12">
                    <label for="inputState">Choose Service</label>
                    <select id="sid" name="sid[]" multiple class="form-control js-example-basic-multiple">                                            
                        <?php foreach($services as $row):?>
                        <option <?=in_array($row['id'],$sservices)?'selected':''?> value="<?=$row['id'];?>"><?= $row['name'];?></option>
                        <?php endforeach;?>
                    </select>
                </div> -->
                
                <?php if(@$secret->id && !empty($_SESSION['role']) && $_SESSION['role']==1){ ?>
                <div class="form-group col-md-12">
                    <label for="inputEmail4"><input type="checkbox" value="1" class="form-control" id="status" name="status" placeholder="" /> Show secret value </label>
                </div>
                <?php } ?>
                
            

            
            </div>

            
            


            
            

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

     
<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>

    $("#status").on("change", function(){
        var vall = '<?=!empty($secret->key_value)?base64_encode(@$secret->key_value):''?>';
        if($(this).is(":checked")===true){
            $('#key_value').val(atob(vall))
        }else{
            $('#key_value').val("*************")
        }
        //alert($(this).is(":checked"))
    })
</script>
