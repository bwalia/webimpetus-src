<?php require_once (APPPATH . 'Views/common/edit-title.php'); ?>
<?php
//  $row = getRowArray("blocks_list", ["code" => "contact_types_list_json"]);
//  echo "<pre>";
// print_r($customers);
// print_r("hello");
// die();
// if (isset($row)) {
// $contact_type = json_decode(@$row->text);
// $customers = getResultArray("customers");
// } else {
//     $customers = getResultArray("customers");
// }
$previousUrl = $_SERVER['HTTP_REFERER'];
$companyUUID = null;
$pattern = '/\/companies\/edit\/[a-f0-9\-]{36}$/';
if (preg_match($pattern, $previousUrl)) {
    $companyUUID = substr($previousUrl, strrpos($previousUrl, '/') + 1);
}
$customerUUID = null;
$customerPatt = '/\/customers\/edit\/[a-f0-9\-]{36}$/';
if (preg_match($customerPatt, $previousUrl)) {
    $customerUUID = substr($previousUrl, strrpos($previousUrl, '/') + 1);
}
?>
<style>
    .radio-button-label.active {
        background-color: #a40032 !important;
    }
    .hidden {
        display: none;
    }
</style>
<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action="/contacts/update" enctype="multipart/form-data">
            <input type="hidden" name="companyUUID" value="<?php echo $companyUUID; ?>">
            <input type="hidden" name="customerUUID" value="<?php echo $customerUUID; ?>">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                                role="tab" aria-controls="nav-home" aria-selected="true">Customer Detail</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                                role="tab" aria-controls="nav-profile" aria-selected="false">Contact Addresses</a>
                        </div>
                    </nav>

                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="linked_module_types"
                                            id="customers" value="customers"
                                            <?= @$contact->linked_module_types == 'customers' ? 'checked="checked"' : '' ?> />
                                        <label class="form-check-label" for="customers">Customers</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="linked_module_types"
                                            id="companies" value="companies"
                                            <?= @$contact->linked_module_types == 'companies' ? 'checked="checked"' : '' ?> />
                                        <label class="form-check-label" for="companies">Companies</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div id="customersSelector" class="form-group col-md-6 <?= @$contact->linked_module_types == 'customers' ? 'show' : 'hidden' ?>">
                                    <label for="inputEmail4">Customer Name</label>
                                    <select id="customer_id" name="customer_id"
                                        class="form-control dashboard-dropdown select-customer-contacts-ajax">
                                        <option value="" selected="">--Select--</option>
                                        <?php
                                        if (isset($customers)) {
                                            foreach ($customers as $row): ?>
                                                <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$contact->customer_id) {
                                                      echo "selected";
                                                  } ?>>
                                                    <?= $row['company_name']; ?>
                                                </option>
                                            <?php endforeach;
                                        } ?>
                                    </select>
                                </div>

                                <div id="companiesSelector" class="form-group col-md-6 <?= @$contact->linked_module_types == 'companies' ? 'show' : 'hidden' ?>">
                                    <label for="inputEmail4">Company Name</label>
                                    <select id="company_id" name="company_id"
                                        class="form-control dashboard-dropdown select-company-contacts-ajax">
                                        <option value="" selected="">--Select--</option>
                                        <?php
                                        if (isset($companies)) {
                                            foreach ($companies as $row): ?>
                                                <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$contact->company_id) {
                                                      echo "selected";
                                                  } ?>>
                                                    <?= $row['company_name']; ?>
                                                </option>
                                            <?php endforeach;
                                        } ?>
                                    </select>
                                </div>

                                <div class="form-group required col-md-6">
                                    <label for="inputEmail4">First Name</label>
                                    <input type="text" autocomplete="off" autocomplete="off" autocomplete="off"
                                        class="form-control required" id="first_name" name="first_name" placeholder=""
                                        value="<?= @$contact->first_name ?>">
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Surname</label>
                                    <input type="text" autocomplete="off" autocomplete="off" class="form-control"
                                        id="surname" name="surname" placeholder="" value="<?= @$contact->surname ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Title</label>
                                    <input type="text" autocomplete="off" autocomplete="off" class="form-control"
                                        id="title" name="title" placeholder="" value="<?= @$contact->title ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4"> Salutation</label>
                                    <input type="text" autocomplete="off" class="form-control" id="saludation"
                                        name="saludation" placeholder="" value="<?= @$contact->saludation ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">News Letter Status</label>
                                    <input type="text" autocomplete="off" class="form-control" id="news_letter_status"
                                        name="news_letter_status" placeholder=""
                                        value="<?= @$contact->news_letter_status ?>">
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="id" placeholder="" id="contactId"
                                value="<?= @$contact->id ?>" />
                            <input type="hidden" class="form-control" name="uuid" placeholder="" id="uuid"
                                value="<?= @$contact->uuid ?>" />

                            <div class="form-row">
                                <div class="form-group required col-md-6">
                                    <label for="inputEmail4">Email</label>
                                    <input type="text" autocomplete="off" class="form-control required email" id="email"
                                        name="email" placeholder="" value="<?= @$contact->email ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Password</label>
                                    <input autocomplete="new-password" type="password" class="form-control"
                                        id="password" name="password" placeholder="" value="">
                                    <span class="passwordIcon psswrdIcon">
                                        <a href="javascript:void(0)" onclick="showPassword('password')">
                                            <i class="fa fa-eye"></i><i class="fa fa-eye-slash"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Direct Phone</label>
                                    <input type="text" autocomplete="off" class="form-control number" id="direct_phone"
                                        name="direct_phone" placeholder="" value="<?= @$contact->direct_phone ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Mobile</label>
                                    <input type="text" autocomplete="off" class="form-control number" id="mobile"
                                        name="mobile" placeholder="" value="<?= @$contact->mobile ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="inputEmail4">Direct Fax</label>
                                    <input type="text" autocomplete="off" class="form-control" id="direct_fax"
                                        name="direct_fax" placeholder="" value="<?= @$contact->direct_fax ?>">
                                </div>


                                <div class="form-group col-md-3">

                                    <!-- <label for="inputEmail4">Contact Type</label>
                                    <select id="contact_type" name="contact_type" class="form-control dashboard-dropdown">
                                        <option value="" selected="">--Select--</option>
                                        <?php if (isset($contact_type) && is_array($contact_type)) {
                                            foreach (@$contact_type as $key => $value): ?>
                                        <option value="<?= $value; ?>" <?php if ($value == @$contact->contact_type) {
                                              echo "selected";
                                          } ?>><?= $value; ?></option>
                                        <?php endforeach;
                                        } ?>
                                </select> -->



                                    <label for="inputEmail4">Category</label>
                                    <!-- previous data was $contact_type -->
                                    <select id="contact_type" name="contact_type"
                                        class="form-control dashboard-dropdown">
                                        <option value="" selected="">--Select--</option>
                                        <?php
                                        if (isset($categories) && is_array($categories)) {
                                            foreach (@$categories as $category): ?>
                                                <option <?php if ($category["id"] == @$contact->contact_type) {
                                                    echo "selected";
                                                } ?> value="<?php echo ($category["id"]) ?>">
                                                    <?php echo ($category["name"]) ?>
                                                </option>
                                            <?php endforeach;
                                        } ?>
                                    </select>


                                </div>
                                <div class="form-group col-md-1">
                                </div>

                                <div class="form-check checkbox-section col-md-3">
                                    <div class="checkbox-label">

                                        <input class="form-check-input" name="allow_web_access" id="allow_web_access"
                                            value="<?php echo @$contact->allow_web_access; ?>" type="checkbox" 
                                            <?php if (@$contact->allow_web_access == "1") {
                                                   echo "checked";
                                               } ?>>
                                        <label class="form-check-label" for="flexCheckIndeterminate">
                                            Allow WebAccess
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="inputPassword4">Comments</label>
                                    <textarea class="form-control" id="comments"
                                        name="comments"><?= @$contact->comments ?></textarea>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <button type="button" class="btn btn-primary" id="addContact">+ Add</button>
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

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true"></div>
<!-- main content part end -->

<script>

    $(document).on('click', 'input[type=radio][name=linked_module_types]', function () {
        var originalValue = $(this).data('original-value');
        
        if (originalValue === undefined) {
          originalValue = $(this).val();
          $(this).data('original-value', originalValue);
        }
        setTimeout(() => {
          $(this).val(originalValue);
            if (originalValue == "customers") {
                $("#customersSelector").removeClass('hidden');
                $("#companiesSelector").addClass('hidden');
            }
            if (originalValue == "companies") {
                $("#companiesSelector").removeClass('hidden');
                $("#customersSelector").addClass('hidden');
            }
        }, 0);
    });

    var uuid = $('#uuid').val();
    $("#email").blur(function (e) {
        var email = $("#email").val();
        var rowUuid = '<?php echo @$contact->uuid ?? ""; ?>';
        if (!rowUuid) {
            $.ajax({
                url: baseUrl + "/contacts/checkEmail",
                data: {
                    email: email
                },
                method: 'post',
                success: function (res) {
                    var result = JSON.parse(res);
                    if (result.status == 409) {
                        e.preventDefault();
                        if ($("#emailError").length === 0) {
                            $("<span class='form-control-feedback' id='emailError'>Email already exists.</span>").insertAfter(e.target);
                        }
                        return false
                    } else {
                        $("#emailError").text("");
                        $("#emailError").remove();
                    }
                }
            })
        }
    });

    $(":submit").click(function (e) {
        if ($("#emailError").length) {
            e.preventDefault();
            return false;
        }
    });



    $(document).on("click", ".form-check-input", function () {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });

    $(document).on("click", '#addContact', function () {

        var contactId = $("#contactId").val();

        $.ajax({
            data: { contactId: contactId },
            url: baseURL + 'contacts/addAddress',
            type: 'POST',
            success: function (response) {

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
            data: { addressId: addressId },
            url: baseURL + 'contacts/editAddress',
            type: 'POST',
            success: function (response) {

                var obj = JSON.parse(response);

                $("#addAddressModal").html(obj.html);
                $("#addAddressModal").modal('show');
            }
        });
    });
    $(document).on("click", '.delete-addresses', function () {

        var addressId = $(this).attr("data-id");

        $.ajax({
            data: { addressId: addressId },
            url: baseURL + 'contacts/deleteAddress',
            type: 'POST',
            success: function (response) {

                renderAddress(uuid);
            }
        });
    });


    $(document).on("click", "#saveOrUpdateAddress", function () {

        var address_line_1 = $('#addAddressModal #address_line_1').val();
        var address_line_2 = $('#addAddressModal #address_line_2').val();
        var address_line_3 = $('#addAddressModal #address_line_3').val();
        var address_line_4 = $('#addAddressModal #address_line_4').val();
        var city = $('#addAddressModal #city').val();
        var state = $('#addAddressModal #state').val();
        var post_code = $('#addAddressModal #post_code').val();
        var country = $('#addAddressModal #country').val();
        var addressId = $('#addAddressModal #addressId').val();
        var address_type = $('#addAddressModal #address_type').val();


        $.ajax({
            url: baseURL + 'contacts/saveAddress',
            type: 'POST',
            data: { addressId: addressId, uuid_contact: uuid, address_line_1: address_line_1, address_line_2: address_line_2, address_line_3: address_line_3, address_line_4: address_line_4, city: city, state: state, post_code: post_code, country: country, address_type: address_type },
            success: function (response) {

                var obj = JSON.parse(response);
                if (obj.status) {
                    $('#addAddressModal').modal('hide');

                    renderAddress(uuid);
                } else {
                    // alert(obj.msg);
                }
            },
        });
    });

    function renderAddress(uuid) {
        $.ajax({
            data: { uuid: uuid },
            url: baseURL + 'contacts/renderAddress',
            type: 'POST',
            success: function (response) {

                var obj = JSON.parse(response);

                $("#addressList").html(obj.html);
            }
        });
    }

    function showPassword(id) {
        var x = document.getElementById(id);
        if (x.type === "password") {
            x.type = "text";
            $('.psswrdIcon').addClass('changeIcon');
        } else {
            x.type = "password";
            $('.psswrdIcon').removeClass('changeIcon');
        }
    }
    $(document).ready(function () {
        renderAddress(uuid);
    })

    $(document).ready(function () {
        $(".select-customer-contacts-ajax").select2({
            ajax: {
                url: "/contacts/contactsCustomerAjax",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.company_name,
                                id: item.id
                            }
                        })
                    };
                },
            },
            minimumInputLength: 2
        })
    });

    $(document).ready(function () {
        $(".select-company-contacts-ajax").select2({
            ajax: {
                url: "/contacts/contactsCompanyAjax",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.company_name,
                                id: item.id
                            }
                        })
                    };
                },
            },
            minimumInputLength: 2
        })
    });

</script>