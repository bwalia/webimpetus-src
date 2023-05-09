<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">
        
        <form id="tenform" method="post" action="<?php echo $actionUrl; ?>" enctype="multipart/form-data">
            <div class="form-row">

                <div class="form-group  col-md-4">
                    <label for="inputState">Choose User</label>
                    <select id="uuid" name="uuid" class="form-control  dashboard-dropdown">
                        <option value="" selected="">--Select--</option>
                      <?php foreach($users as $row):?>
                        <option value="<?= $row['uuid'];?>" <?=($row['uuid']== @$tenant->uuid)?'selected':'' ?>><?= $row['name'];?></option>
                        <?php endforeach;?>
                    </select>
                </div>


                <div class="form-group col-md-4">
                    <label for="inputState">Choose Service</label>
                    <select id="sid" name="sid[]" multiple class="form-control js-example-basic-multiple">                                            
                <?php foreach($services as $row):?>
                        <option value="<?= $row['id'];?>" <?=(in_array($row['id'],$tservices))?'selected':'' ?>><?= $row['name'];?></option>
                        <?php endforeach;?>
                    </select>
                </div>


                <div class="form-group  col-md-4">
                    <label for="inputEmail4">Name</label>
                    <input type="text" class="form-control " id="name" name="name" placeholder="" value="<?=@$tenant->name ?>">
                  </div>
                  <input type="hidden" class="form-control" name="id" placeholder="" value="<?=@$tenant->id ?>" />
             
                
            </div>
            
            <div class="form-row">
                   <div class="form-group   col-md-4">
                    <label for="inputPassword4">Address</label>
                    <input type="address" class="form-control " id="address" name="address" placeholder=""  value="<?=@$tenant->address ?>">
                </div>
                  <div class="form-group  col-md-4">
                    <label for="inputPassword4">Contact Name</label>
                    <input type="text" class="form-control " id="contact_name" name="contact_name" placeholder=""  value="<?=@$tenant->contact_name ?>">
                </div>
                <div class="form-group  col-md-4">
                    <label for="inputAddress">Contact Email</label>
                    <input type="email" class="form-control  email" id="contact_email" name="contact_email" placeholder=""  value="<?=@$tenant->contact_email ?>">                                       
                </div>
            </div>

              <div class="form-row">
                  <div class="form-group col-md-12">
                    <label for="inputPassword4">Notes</label>
                  <textarea class="form-control" name="notes" ><?=@$tenant->notes ?></textarea> 
                </div>
                
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>


 <?php require_once (APPPATH.'Views/common/footer.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
 <script>
   if ($("#tenform").length > 0) {
    $.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Only alphabetical characters");
      $("#tenform").validate({
    rules: {
      name: {
        required: true,
		minlength: 5,
		lettersonly: true 
      }, 
      contact_email: {
        required: true,
		email: true
      },
      address: {
        required: true,
		minlength: 5 
      },
      contact_name: {
        required: true,
		minlength: 5,
		lettersonly: true 
      },
      uuid: {
        required: true,
      }
    },
    messages: {
      name: {
        required: "Please enter name",
      },
      email: {
        required: "Please enter valid email",
        email: "Please enter valid email",
        maxlength: "The email name should less than or equal to 50 characters",
        },      
     password: {
        required: "Please enter password",
      },
        
    },
  })
}
</script>