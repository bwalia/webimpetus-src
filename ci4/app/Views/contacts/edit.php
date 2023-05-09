<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>
<?php 
$row = getRowArray("blocks_list", ["code" => "contact_types_list_json"]);
if (isset($row)) {
$contact_type = json_decode(@$row->text);
$customers = getResultArray("customers");
} else {
    $customers = getResultArray("customers");
}
?>
    <div class="white_card_body">
        <div class="card-body">
            
            <form id="addcustomer" method="post" action="/contacts/update" enctype="multipart/form-data">

            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Customer Detail</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Contact Addresses</a>
                        </div>
                    </nav>
            
                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="form-row">
                            <div class="form-group required col-md-6">
                                <label for="inputEmail4">Customer Name</label>
                                <select id="client_id" name="client_id" class="form-control required dashboard-dropdown">
                                    <option value="" selected="">--Select--</option>
                                    <?php if(isset($customers)) { foreach($customers as $row):?>
                                    <option value="<?= $row['id'];?>" <?php if($row['id'] == @$contact->client_id){ echo "selected"; }?>><?= $row['company_name'];?></option>
                                    <?php endforeach; } ?>
                                </select>
                            </div>

                            <div class="form-group required col-md-6">
                                <label for="inputEmail4">First Name</label>
                                <input type="text" autocomplete="off" autocomplete="off" autocomplete="off" class="form-control required" id="first_name" name="first_name" placeholder=""  value="<?= @$contact->first_name ?>">
                            </div>

                        </div>
                            
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Surname</label>
                                <input type="text" autocomplete="off" autocomplete="off" class="form-control" id="surname" name="surname" placeholder=""  value="<?= @$contact->surname ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Title</label>
                                <input type="text" autocomplete="off" autocomplete="off" class="form-control" id="title" name="title" placeholder=""  value="<?= @$contact->title ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4"> Salutation</label>
                                <input type="text" autocomplete="off" class="form-control" id="saludation" name="saludation" placeholder=""  value="<?= @$contact->saludation ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputEmail4">News Letter Status</label>
                                <input type="text" autocomplete="off" class="form-control" id="news_letter_status" name="news_letter_status" placeholder=""  value="<?= @$contact->news_letter_status ?>">
                            </div>
                        </div>
                        <input type="hidden" class="form-control" name="id" placeholder="" id="contactId" value="<?= @$contact->id ?>" />
                        <input type="hidden" class="form-control" name="uuid" placeholder="" id="uuid" value="<?= @$contact->uuid ?>" />

                        <div class="form-row">
                            <div class="form-group required col-md-6">
                                <label for="inputEmail4">Email</label>
                                <input type="text" autocomplete="off" class="form-control required email" id="email" name="email" placeholder=""  value="<?= @$contact->email ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Password</label>
                                <input autocomplete="new-password" type="password" class="form-control" id="password" name="password" placeholder=""  value="">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Direct Phone</label>
                                <input type="text" autocomplete="off" class="form-control number" id="direct_phone" name="direct_phone" placeholder=""  value="<?= @$contact->direct_phone ?>">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Mobile</label>
                                <input type="text" autocomplete="off" class="form-control number" id="mobile" name="mobile" placeholder=""  value="<?= @$contact->mobile ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="inputEmail4">Direct Fax</label>
                                <input type="text" autocomplete="off" class="form-control" id="direct_fax" name="direct_fax" placeholder=""  value="<?= @$contact->direct_fax ?>">
                            </div>

                            
                                <div class="form-group col-md-3">
                                    
                                    <!-- <label for="inputEmail4">Contact Type</label>
                                    <select id="contact_type" name="contact_type" class="form-control dashboard-dropdown">
                                        <option value="" selected="">--Select--</option>
                                        <?php if(isset($contact_type) && is_array($contact_type)){ foreach(@$contact_type as $key => $value):?>
                                        <option value="<?= $value;?>" <?php if($value == @$contact->contact_type){ echo "selected"; }?>><?= $value;?></option>
                                        <?php endforeach; }?>
                                </select> -->

                                

                                <label for="inputEmail4">Category</label>
                                <!-- previous data was $contact_type -->
                                    <select id="contact_type" name="contact_type" class="form-control dashboard-dropdown">
                                        <option value="" selected="">--Select--</option>
                                        <?php 
                                        if(isset($categories) && is_array($categories)){ 
                                            foreach(@$categories as $category):?>
                                        <option <?php if($category["id"] == @$contact->contact_type){ echo "selected"; }?> value="<?php echo($category["id"])?>" ><?php echo($category["name"])?></option>
                                        <?php endforeach; }?>
                                </select>


                            </div>
                            <div class="form-group col-md-1">
                            </div>
                        
                            <div class="form-check checkbox-section col-md-3">
                                <div class = "checkbox-label" >

                                <input class="form-check-input" name="allow_web_access" id="allow_web_access" value="<?php echo @$contact->allow_web_access; ?>" type="checkbox" <?php if(@$contact->allow_web_access == "1"){echo 
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
                                <textarea class="form-control"  id="comments" name="comments" ><?= @$contact->comments ?></textarea> 
                            </div>
                            
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <button type="button" class="btn btn-primary" id="addContact">add</button>
                            <br>
                            <br>
                            <div id="addressList"></div>
                        </div>
                    </div>
                </div>
            </div>

                
            </form>
        </div>
    </div>

<?php require_once (APPPATH.'Views/common/footer.php'); ?>
<div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
<!-- main content part end -->

<script>

var uuid =  $('#uuid').val();

$(document).on("click", ".form-check-input", function(){
    if($(this).prop("checked") == false){
        $(this).val(0);
    }else{
        $(this).val(1);
    }
});

$(document).on("click", '#addContact', function () {

    var contactId = $("#contactId").val();

    $.ajax({
        data: {contactId: contactId},
        url: baseURL+ 'contacts/addAddress',
        type: 'POST',
        success: function(response) {

            var obj = JSON.parse(response);

            $("#addAddressModal").html(obj.html);
            $("#addAddressModal").modal('show');
            $("#addAddressModal #address_type").select2();
        }
    });    
});
$(document).on("click", '.edit-addresses', function () {

    var addressId = $(this).attr("data-id");

    $.ajax({
        data: {addressId: addressId},
        url: baseURL+ 'contacts/editAddress',
        type: 'POST',
        success: function(response) {

            var obj = JSON.parse(response);

            $("#addAddressModal").html(obj.html);
            $("#addAddressModal").modal('show');
        }
    });    
});
$(document).on("click", '.delete-addresses', function () {

    var addressId = $(this).attr("data-id");

    $.ajax({
        data: {addressId: addressId},
        url: baseURL+ 'contacts/deleteAddress',
        type: 'POST',
        success: function(response) {

            renderAddress(uuid);
        }
    });    
});


$(document).on("click", "#saveOrUpdateAddress", function() {

    var address_line_1 =  $('#addAddressModal #address_line_1').val();
    var address_line_2 =  $('#addAddressModal #address_line_2').val();
    var address_line_3 =  $('#addAddressModal #address_line_3').val();
    var address_line_4 =  $('#addAddressModal #address_line_4').val();
    var city =  $('#addAddressModal #city').val();
    var state =  $('#addAddressModal #state').val();
    var post_code =  $('#addAddressModal #post_code').val();
    var country =  $('#addAddressModal #country').val();
    var addressId =  $('#addAddressModal #addressId').val();
    var address_type =  $('#addAddressModal #address_type').val();
    

    $.ajax({
        url: baseURL+ 'contacts/saveAddress',
        type: 'POST',
        data: {addressId:addressId, uuid_contact:uuid, address_line_1:address_line_1, address_line_2:address_line_2, address_line_3:address_line_3, address_line_4:address_line_4,city:city, state:state, post_code:post_code, country:country,address_type:address_type},
        success: function(response) {
        
            var obj = JSON.parse(response);
            if(obj.status){
                $('#addAddressModal').modal('hide');
            
                renderAddress(uuid);
            } else{
                // alert(obj.msg);
            }
        },
    });
});

function renderAddress(uuid){
        $.ajax({
            data: {uuid: uuid},
            url: baseURL+ 'contacts/renderAddress',
            type: 'POST',
            success: function(response) {
    
                var obj = JSON.parse(response);

                $("#addressList").html(obj.html);
            }
        });  
    }

$(document).ready(function(){

    renderAddress(uuid);


})


</script>