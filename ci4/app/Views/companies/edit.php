<?php require_once (APPPATH . 'Views/common/edit-title.php');
$allContacts = getResultArray("contacts");
?>

<div class="white_card_body">
    <div class="card-body">

        <form id="addCompany" method="post" action="/companies/update" enctype="multipart/form-data">

            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                                role="tab" aria-controls="nav-home" aria-selected="true">Company detail</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                                role="tab" aria-controls="nav-profile" aria-selected="false">Contacts</a>
                        </div>
                    </nav>

                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                            aria-labelledby="nav-home-tab">

                            <div class="form-row">
                                <div class="form-group required col-md-4">
                                    <label for="selectCompany">Company Name</label>
                                    <input autocomplete="off" type="text" class="form-control required"
                                        id="company_name" name="company_name" placeholder=""
                                        value="<?= @$company->company_name ?>">
                                </div>

                                <div class="form-group required col-md-4">
                                    <label for="inputEmail4">Company Number</label>
                                    <input autocomplete="off" type="text" class="form-control required"
                                        id="company_number" name="company_number" placeholder=""
                                        value="<?= @$company->company_number ?>">
                                </div>
                                <div class="form-check col-md-1">
                                </div>
                                <div class="form-check checkbox-section col-md-1">
                                    <div class="checkbox-label">

                                        <input class="form-check-input" type="checkbox" name="status" id="status" <?php if (@$company->status == "1") {
                                            echo
                                                "checked";
                                        } ?>   value="<?php echo @$company->status; ?>">
                                        <label class="form-check-label" for="status">
                                            <?php
                                            echo "Active";
                                            ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="address_1"> Address 1</label>
                                    <input autocomplete="off" type="text" class="form-control" id="address_1"
                                        name="address_1" placeholder="" value="<?= @$company->address_1 ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="address_2">Address 2</label>
                                    <input autocomplete="off" type="text" class="form-control" id="address_2"
                                        name="address_2" placeholder="" value="<?= @$company->address_2 ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="address_3"> Address 3</label>
                                    <input autocomplete="off" type="text" class="form-control" id="address_3"
                                        name="address_3" placeholder=""
                                        value="<?= @$company->address_3 ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="town_or_city">Town or City</label>
                                    <input autocomplete="off" type="text" class="form-control" id="town_or_city"
                                        name="town_or_city" placeholder=""
                                        value="<?= @$company->town_or_city ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="state_or_county">State or Country</label>
                                    <input autocomplete="off" type="text" class="form-control" id="state_or_county" name="state_or_county"
                                        placeholder="" value="<?= @$company->state_or_county ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="post_zip_code">Post Zip Code</label>
                                    <input autocomplete="off" type="text" class="form-control" id="post_zip_code"
                                        name="post_zip_code" placeholder="" value="<?= @$company->post_zip_code ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="region_area">Region Area</label>
                                    <input autocomplete="off" type="text" class="form-control" id="region_area"
                                        name="region_area" placeholder="" value="<?= @$company->region_area ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="website">Website</label>
                                    <input autocomplete="off" type="text" class="form-control" id="website"
                                        name="website" placeholder="" value="<?= @$company->website ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input autocomplete="off" type="text" class="form-control" id="email"
                                        name="email" placeholder="" value="<?= @$company->email ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="premises_type">Premises Type</label>
                                    <input autocomplete="off" type="text" class="form-control" id="premises_type"
                                        name="premises_type" placeholder="" value="<?= @$company->premises_type ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="no_of_employees">No. of Employees</label>
                                    <input autocomplete="off" type="text" class="form-control" id="no_of_employees"
                                        name="no_of_employees" placeholder="" value="<?= @$company->no_of_employees ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="company_type">Company Type</label>
                                    <input autocomplete="off" type="text" class="form-control" id="company_type"
                                        name="company_type" placeholder="" value="<?= @$company->company_type ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="sic_code">SIC Code</label>
                                    <input autocomplete="off" type="text" class="form-control" id="sic_code"
                                        name="sic_code" placeholder="" value="<?= @$company->sic_code ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="turnover">Turnover</label>
                                    <input autocomplete="off" type="text" class="form-control" id="turnover"
                                        name="turnover" placeholder="" value="<?= @$company->turnover ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="company_telephone">Company Telephone</label>
                                    <input autocomplete="off" type="text" class="form-control" id="company_telephone"
                                        name="company_telephone" placeholder="" value="<?= @$company->company_telephone ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="company_fax">Company Fax</label>
                                    <input autocomplete="off" type="text" class="form-control" id="company_fax"
                                        name="company_fax" placeholder="" value="<?= @$company->company_fax ?>">
                                </div>
                            </div>
                            <input type="hidden" name="uuid" value="<?= @$company->uuid ?>" />
                            <input type="hidden" name="id" value="<?= @$company->id ?>" />
                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="form-group col-md-12">
                                <label for="contacts-option">Choose Contacts</label>
                                <select id="contacts-option" name="contactID" class="form-control select2 w-100">
                                    <option value="">--Select--</option>
                                    <?php foreach (@$allContacts as $allContact): ?>
                                        <option value="<?= $allContact['uuid']; ?>" <?= ($allContact['uuid'] == @$contacts['contact_uuid']) ? 'selected' : '' ?>>
                                            <?= $allContact['first_name'] . ' ' . $allContact['surname']; ?>
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

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->


<script>
    $(document).on("click", ".form-check-input", function () {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });

    $("#email").blur(function (e) {
        var email = $("#email").val();
        var rowUuid = '<?php echo @$company->uuid ?? ""; ?>';
        if (!rowUuid) {
            $.ajax({
                url: baseUrl + "/companies/checkEmail",
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
        validateName($("#company_name"), e);
        validateName($("#contact_firstname"), e);

        if ($("#emailError").length) {
            e.preventDefault();
            return false;
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
        var companyId = current.closest(".each-row").find("#contact_id").val();
        $.ajax({
            url: baseUrl + "/companies/deleteCompany",
            data: {
                companyId: companyId
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