

  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Addresses</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="addressId" value="<?php echo $addressId;?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Address line one</label>
                    <input type="text" autocomplete="off" class="form-control" id="address_line_1" name="address_line_1" placeholder=""  value="<?= @$data->address_line_1 ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Address line two</label>
                    <input type="text" autocomplete="off" class="form-control" id="address_line_2" name="address_line_2" placeholder=""  value="<?= @$data->address_line_2 ?>">
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Address line three</label>
                    <input type="text" autocomplete="off" class="form-control" id="address_line_3" name="address_line_3" placeholder=""  value="<?= @$data->address_line_3 ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Address line four</label>
                    <input type="text" autocomplete="off" class="form-control" id="address_line_4" name="address_line_4" placeholder=""  value="<?= @$data->address_line_4 ?>">
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">City</label>
                    <input type="text" autocomplete="off" class="form-control" id="city" name="city" placeholder=""  value="<?= @$data->city ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputEmail4">state</label>
                    <input type="text" autocomplete="off" class="form-control" id="state" name="state" placeholder=""  value="<?= @$data->state ?>">
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">country</label>
                    <input type="text" autocomplete="off" class="form-control" id="country" name="country" placeholder=""  value="<?= @$data->country ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Post Code</label>
                    <input type="text" autocomplete="off" class="form-control" id="post_code" name="post_code" placeholder=""  value="<?= @$data->post_code ?>">
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputEmail4">Address Type</label><br>
                    <select id="address_type" name="address_type"  class="form-control select2">                                            
                    
                        <option value="home" <?php if(@$data->address_type == "home")echo "selected";  ?> >Home</option>
                        <option value="correspondence" <?php if(@$data->address_type == "correspondence")echo "selected";  ?> >Correspondence</option>
                        <option value="business" <?php if(@$data->address_type == "business")echo "selected";  ?> >Business</option>
                       
                    </select> 
                </div>
               

            </div>
        </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveOrUpdateAddress">Save</button>
      </div>
    </div>
  </div>

  <style>
      span.select2.select2-container.select2-container--default {
          width:227px !important;
        }
  </style>
