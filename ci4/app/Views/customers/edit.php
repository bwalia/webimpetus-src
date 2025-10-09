<?php require_once (APPPATH . 'Views/common/edit-title.php');
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
                            <a class="nav-item nav-link" id="nav-map-tab" data-toggle="tab" href="#nav-map" role="tab"
                                aria-controls="nav-map" aria-selected="false">Google Map</a>
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
                                    <input autocomplete="off" type="text" class="form-control required" id="acc_no"
                                        name="acc_no" placeholder="" value="<?= @$customer->acc_no ?>">
                                </div>
                                <div class="form-check col-md-1">
                                </div>
                                <div class="form-check checkbox-section col-md-1">
                                    <div class="checkbox-label">

                                        <input class="form-check-input" type="checkbox" name="status" id="status" <?php if (@$customer->status == "1") {
                                            echo
                                                "checked";
                                        } ?>   value="<?php echo @$customer->status; ?>">
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
                                    <input autocomplete="off" type="text" class="form-control" id="contact_firstname"
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
                            <input type="hidden" name="id" value="<?= @$customer->id ?>" />

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="inputPassword4">Notes</label>
                                    <textarea class="form-control" name="notes"><?= @$customer->notes ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-12 mb-3 d-flex justify-content-between">
                                        <a href="/contacts/edit" target="_blank" class="btn btn-primary"
                                            id="addContact">+ Add</a>
                                        <button type="button" onclick="window.location.reload();"
                                            class="btn btn-primary" id="refreshPage">Refresh</button>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div id="contactWrapper"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-map" role="tabpanel" aria-labelledby="nav-map-tab">
                            <div class="form-group col-md-12">
                                <label for="contacts-option">Google Map</label>
                                <div id="company-address-google-map" style="height: 800px;">
                                    <iframe id="company-address-google-map-frame"
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.0000000000005!2d-73.9854286845947!3d40.74881797932569!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c2598f5f1b6f75%3A0x8e0b7e6f3f1b8b1d!2sEmpire%20State%20Building!5e0!3m2!1sen!2sbd!4v1632213660006!5m2!1sen!2sbd"
                                        width="100%" height="100%" style="border:0;" allowfullscreen=""
                                        loading="lazy"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tags Section -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="customer_tags">
                        <i class="fa fa-tags"></i> Tags
                        <a href="/tags/manage" target="_blank" style="font-size: 0.85rem; margin-left: 8px;">
                            <i class="fa fa-cog"></i> Manage Tags
                        </a>
                    </label>
                    <select id="customer_tags" name="customer_tags[]" class="form-control select2" multiple="multiple"
                            data-placeholder="Select tags for this customer...">
                        <!-- Populated by JavaScript -->
                    </select>
                    <small class="form-text text-muted">
                        Select multiple tags to categorize this customer. You can create new tags from the Manage Tags page.
                    </small>
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
<?php
$contactsValues = array_map(function ($v, $k) {
    $values = array_values($v);
    array_push($values, "");
    return $values;
}, $contacts, array_keys($contacts));
?>

<script>

    $(document).ready(function () {
        new gridjs.Grid({
            columns: [
                { name: "Unique ID" },
                { name: "First Name" },
                { name: "Surname" },
                { name: "Email" },
                { name: "Phone Number" },
                { name: "Mobile" },
                {
                    name: 'Actions',
                    formatter: (cell, row) => {
                        return gridjs.html(
                            `
                            <div class='action-button-wrapper'>
                                <button type='button' class='btn btn-primary' id='removeContact' data-contactId='${row.cells[0].data}'><i class="ti-trash"></i></button>
                            </div?
                        `
                        );
                    }
                },
            ],
            data: <?php echo json_encode($contactsValues) ?>
        }).render(document.getElementById("contactWrapper"));
    });

    $(document).on("click", "#removeContact", function () {
        const contactUuid = $(this).attr('data-contactId');
        $.ajax({
            url: baseUrl + "/customers/removeContact",
            data: {
                contactUuid
            },
            method: 'post',
            success: function (res) {
                if (res) {
                    window.location.reload();
                }
            }
        })
    });

    $(document).on("click", ".form-check-input", function () {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });

    $("#email").blur(function (e) {
        var email = $("#email").val();
        var rowUuid = '<?php echo @$customer->uuid ?? ""; ?>';
        if (!rowUuid) {
            $.ajax({
                url: baseUrl + "/customers/checkEmail",
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
    });

    // Load tags functionality
    $(document).ready(function() {
        loadCustomerTags();
    });

    function loadCustomerTags() {
        const customerId = '<?= @$customer->id ?>';

        // Load all tags
        $.ajax({
            url: '/tags/tagsList',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const tags = response.data;
                    const $select = $('#customer_tags');

                    // Populate select options
                    tags.forEach(function(tag) {
                        const option = new Option(tag.name, tag.id, false, false);
                        $(option).attr('data-color', tag.color);
                        $select.append(option);
                    });

                    // Initialize select2 with custom template
                    $select.select2({
                        placeholder: 'Select tags for this customer...',
                        allowClear: true,
                        templateResult: formatCustomerTag,
                        templateSelection: formatCustomerTagSelection
                    });

                    // Load currently assigned tags if editing
                    if (customerId) {
                        loadCurrentCustomerTags(customerId);
                    }
                }
            }
        });
    }

    function loadCurrentCustomerTags(customerId) {
        $.ajax({
            url: '/tags/getEntityTags/customer/' + customerId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const currentTagIds = response.data.map(function(tag) {
                        return tag.id.toString();
                    });
                    $('#customer_tags').val(currentTagIds).trigger('change');
                }
            }
        });
    }

    function formatCustomerTag(tag) {
        if (!tag.id) return tag.text;

        const color = $(tag.element).data('color') || '#667eea';
        const $tag = $(
            '<span style="display: flex; align-items: center; gap: 8px;">' +
                '<span style="width: 12px; height: 12px; border-radius: 50%; background-color: ' + color + ';"></span>' +
                '<span>' + tag.text + '</span>' +
            '</span>'
        );
        return $tag;
    }

    function formatCustomerTagSelection(tag) {
        if (!tag.id) return tag.text;
        return tag.text;
    }

    // Save tags when form is submitted
    $('#addcustomer').on('submit', function(e) {
        const customerId = '<?= @$customer->id ?>';

        if (customerId) {
            e.preventDefault();

            // Save tags first
            const selectedTags = $('#customer_tags').val() || [];

            $.ajax({
                url: '/tags/attach',
                method: 'POST',
                data: {
                    entity_type: 'customer',
                    entity_id: customerId,
                    tag_ids: selectedTags
                },
                dataType: 'json',
                success: function(response) {
                    // Now submit the main form
                    $('#addcustomer').off('submit').submit();
                },
                error: function() {
                    // Submit anyway if tag saving fails
                    $('#addcustomer').off('submit').submit();
                }
            });
        }
    });
</script>