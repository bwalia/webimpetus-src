<?php require_once(APPPATH . 'Views/common/edit-title.php');
$categories = getResultArray("categories");
?>

<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action="/customers/update" enctype="multipart/form-data">

            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                                role="tab" aria-controls="nav-home" aria-selected="true">Customer detail</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                                role="tab" aria-controls="nav-profile" aria-selected="false">Contacts</a>
                        </div>
                    </nav>

                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                            aria-labelledby="nav-home-tab">

                            <div class="form-row">
                                <div class="form-group required col-md-4">
                                    <label for="selectCustomer">Customer / Company Name</label>
                                    <input autocomplete="off" type="text" class="form-control required"
                                        id="company_name" name="company_name" placeholder=""
                                        value="<?= @$customer->company_name ?>">
                                </div>



                                <div class="form-group required col-md-4">
                                    <label for="inputEmail4">Customer Account No</label>
                                    <input autocomplete="off" type="text" class="form-control required acc_number"
                                        id="acc_no" name="acc_no" placeholder="" value="<?= @$customer->acc_no ?>">
                                </div>
                                <div class="form-check col-md-1">
                                </div>
                                <div class="form-check checkbox-section col-md-1">
                                    <div class="checkbox-label">

                                        <input class="form-check-input" type="checkbox" name="status" id="status" <?php if (@$customer->status == "1") {
                                            echo
                                                "checked";
                                        } ?> value="<?php echo @$customer->status; ?>">
                                        <label class="form-check-label" for="flexCheckIndeterminate">
                                            <?php /* if (@$customer->status == "1") {
                                           echo "Inactive";
                                       } else { */
                                            echo "Active";
                                            //} 
                                            ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check checkbox-section col-md-1">
                                    <div class="checkbox-label">

                                        <input class="form-check-input" name="supplier" id="supplier"
                                            value="<?php echo @$customer->supplier; ?>" type="checkbox" <?php if (@$customer->supplier == "1") {
                                                   echo
                                                       "checked";
                                               } ?>>
                                        <label class="form-check-label" for="flexCheckIndeterminate">
                                            Supplier
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4"> Contact First Name</label>
                                    <input autocomplete="off" type="text" class="form-control" id="company_name"
                                        name="contact_firstname" placeholder=""
                                        value="<?= @$customer->contact_firstname ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Contact Last Name</label>
                                    <input autocomplete="off" type="text" class="form-control" id="contact_lastname"
                                        name="contact_lastname" placeholder=""
                                        value="<?= @$customer->contact_lastname ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4"> Address 1</label>
                                    <input autocomplete="off" type="text" class="form-control" id="address1"
                                        name="address1" placeholder="" value="<?= @$customer->address1 ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Address 2</label>
                                    <input autocomplete="off" type="text" class="form-control" id="address2"
                                        name="address2" placeholder="" value="<?= @$customer->address2 ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">City</label>
                                    <input autocomplete="off" type="text" class="form-control" id="city" name="city"
                                        placeholder="" value="<?= @$customer->city ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Country</label>
                                    <input autocomplete="off" type="text" class="form-control" id="country"
                                        name="country" placeholder="" value="<?= @$customer->country ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4"> Postal Code</label>
                                    <input autocomplete="off" type="text" class="form-control" id="postal_code"
                                        name="postal_code" placeholder="" value="<?= @$customer->postal_code ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Phone</label>
                                    <input autocomplete="off" type="text" class="form-control phone" id="phone"
                                        name="phone" placeholder="" value="<?= @$customer->phone ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Website</label>
                                    <input autocomplete="off" type="text" class="form-control" id="website"
                                        name="website" placeholder="" value="<?= @$customer->website ?>">
                                </div>



                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Categories</label>
                                    <select id="categories" name="categories[]" multiple class="form-control select2">
                                        <?php
                                        if (isset($customer) && (!empty($customer->categories))) {
                                            $arr = json_decode(@$customer->categories);
                                            foreach ($categories as $row): ?>
                                                <option value="<?= $row['id']; ?>" <?php if ($arr)
                                                      echo
                                                          in_array($row['id'], $arr) ? 'selected="selected"' : '' ?>><?= $row['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group required col-md-12 px-0">
                                <label for="email">Email</label>
                                <input autocomplete="off" type="text" class="form-control email required" id="email"
                                    name="email" placeholder="" value="<?= @$customer->email ?>">
                            </div>
                            <input type="hidden" name="uuid" value="<?= @$customer->uuid ?>" />

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="inputPassword4">Notes</label>
                                    <textarea class="form-control" name="notes"><?= @$customer->notes ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php 
                            $contactUUIDs = [];
                            foreach ($selectedContacts as $key => $selectedContact) {
                                array_push($contactUUIDs, $selectedContact['contact_uuid']);
                            }
                        ?>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="form-group col-md-12">
                                <label for="contacts-option">Choose Contacts</label>
                                <select id="contacts-option" name="cnId[]" multiple class="form-control select2">
                                    <option value="">--Select--</option>
                                    <?php foreach (@$contacts as $row): ?>
                                        <option value="<?= $row['uuid']; ?>" <?= (in_array($row['uuid'], $contactUUIDs)) ? 'selected' : '' ?>>
                                            <?= $row['first_name'] . ' ' . $row['surname']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="form-row">
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>

            </div>

        </form>
        <input type="hidden" value="<?php echo @$totalContact; ?>" id="total_contacts" name="total_contacts">

    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->


<script>
    $(document).on("click", ".form-check-input", function () {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });
</script>

<script>
    $(document).ready(function () {

        var max_fields_limit = 10; //set limit for maximum input fields
        var x = $('#total_contacts').val(); //initialize counter for text box
        $('.add').click(function (e) { //click event on add more fields button having class add_more_button
            // e.preventDefault();
            if (x < max_fields_limit) { //check conditions
                x++; //counter increment

                $('.addresscontainer').append('<div class="form-row col-md-12" id="office_address_' + x + '"><div class="form-group col-md-3">' +
                    '<label for="inputSecretKey">Name</label>' +
                    '<input type="text" class="form-control" id="first_name' + x + '" name="first_name[]" placeholder="" value="">' +
                    '</div>' +
                    '<div class="form-group col-md-3">' +
                    '<label for="inputSecretValue">Surname</label>' +
                    '<input type="text" class="form-control" id="surname' + x + '" name="surname[]" placeholder="" value="">' +
                    '</div>' +
                    '<div class="form-group col-md-5">' +
                    '<label for="inputSecretValue">Email</label>' +
                    '<input type="text" class="form-control" id="contact_email' + x + '" name="contact_email[]" placeholder="" value="">' +
                    '</div> <input type="hidden" value="0" id="contact_id" name="contact_id[]">' +
                    '<div class="form-group col-md-1 change">' +
                    '<button class="btn btn-info bootstrap-touchspin-up deleteaddress" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>' +
                    '</div></div>'
                );


            }

            $('.deleteaddress').on("click", function (e) { //user click on remove text links

                $(this).parent().parent().remove();
                x--;
            })
        });
    });
    $('.deleteaddress').on("click", function (e) { //user click on remove text links

        var current = $(this);
        var customerId = current.closest(".each-row").find("#contact_id").val();
        $.ajax({
            url: baseUrl + "/customers/deleteCustomer",
            data: {
                customerId: customerId
            },
            method: 'post',
            success: function (res) {
                console.log(res)
                current.parent().parent().remove();

            }
        })

        x--;
    })
</script>