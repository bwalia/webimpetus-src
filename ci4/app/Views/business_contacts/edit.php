<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>
<?php $customers = $additional_data["customers"]; ?>
    <div class="white_card_body">
        <div class="card-body">
            
            <form id="addcustomer" method="post" action="/business_contacts/update" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">Customer Name</label>
                        <select id="client_id" name="client_id" class="form-control required dashboard-dropdown">
                            <option value="" selected="">--Select--</option>
                            <?php foreach($customers as $row):?>
                            <option value="<?= $row['id'];?>" <?php if($row['id'] == @$business_contact->client_id){ echo "selected"; }?>><?= $row['company_name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>

                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">First Name</label>
                        <input type="text" class="form-control required only_alpha" id="first_name" name="first_name" placeholder=""  value="<?= @$business_contact->first_name ?>">
                    </div>

                </div>
                    
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Surname</label>
                        <input type="text" class="form-control only_alpha" id="surname" name="surname" placeholder=""  value="<?= @$business_contact->surname ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder=""  value="<?= @$business_contact->title ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputEmail4"> Salutation</label>
                        <input type="text" class="form-control" id="saludation" name="saludation" placeholder=""  value="<?= @$business_contact->saludation ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="inputEmail4">News Letter Status</label>
                        <input type="text" class="form-control" id="news_letter_status" name="news_letter_status" placeholder=""  value="<?= @$business_contact->news_letter_status ?>">
                    </div>
                </div>
                <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$business_contact->id ?>" />

                <div class="form-row">
                    <div class="form-group required col-md-6">
                        <label for="inputEmail4">Email</label>
                        <input type="text" class="form-control required email" id="email" name="email" placeholder=""  value="<?= @$business_contact->email ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Password</label>
                        <input autocomplete="new-password" type="password" class="form-control" id="password" name="password" placeholder=""  value="">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Direct Phone</label>
                        <input type="text" class="form-control number" id="direct_phone" name="direct_phone" placeholder=""  value="<?= @$business_contact->direct_phone ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Mobile</label>
                        <input type="text" class="form-control number" id="mobile" name="mobile" placeholder=""  value="<?= @$business_contact->mobile ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Direct Fax</label>
                        <input type="text" class="form-control" id="direct_fax" name="direct_fax" placeholder=""  value="<?= @$business_contact->direct_fax ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Type</label>
                        <select name="type" id="type" class="form-control select2">
                            <option value="1" <?php if( @$business_contact->type == 1)echo "selected" ?>>Shareholder</option>
                            <option value="2" <?php if( @$business_contact->type == 2)echo "selected" ?>>Director</option>
                            <option value="3" <?php if( @$business_contact->type == 3)echo "selected" ?>>Executive Director</option>
                            
                        </select>
                    </div>

                    <div class="form-check col-md-1">
                    </div>
                    <div class="form-check checkbox-section col-md-3">
                        <div class = "checkbox-label" >

                        <input class="form-check-input" name="allow_web_access" id="allow_web_access" value="<?php echo @$business_contact->allow_web_access; ?>" type="checkbox" <?php if(@$business_contact->allow_web_access == "1"){echo 
                            "checked"; }?>>
                        <label class="form-check-label" for="flexCheckIndeterminate">
                            Allow WebAccess
                        </label>
                        </div>
                    </div>
                </div>

                
                <div class="form-row">
                        <div class="form-group col-md-12">
                        <label for="inputPassword4">Comments</label>
                        <textarea class="form-control"  id="comments" name="comments" ><?= @$business_contact->comments ?></textarea> 
                    </div>
                    
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
</script>