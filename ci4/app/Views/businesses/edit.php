<?php require_once(APPPATH . 'Views/common/edit-title.php');

$businessContacts = getResultArray("business_contacts");
$str = file_get_contents(APPPATH . 'languages.json');
$json = json_decode($str, true);
?>

<div class="white_card_body">
    <div class="card-body">

        <form id="adddomain" method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <div class="form-row">

                <input type="hidden" name="uuid" value="<?= @$businesse->uuid ?>" />

                <div class=" col-md-6">
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Business No</label>
                        <input autocomplete="off" type="text" disabled="disabled" class="form-control" value="<?= @$businesse->uuid_business_id ?>" />
                    </div>
                    <div class="form-group required col-md-12">
                        <label for="inputEmail4">Name</label>
                        <input autocomplete="off" type="text" class="form-control required" name="name" placeholder="" value="<?= @$businesse->name ?>">
                    </div>
                    <div class="form-group  col-md-12 required">
                        <label for="businessCode">Business Code</label>
                        <input autocomplete="off" type="text" minlength="3" maxlength="7" class="form-control required" name="business_code" placeholder="" value="<?= @$businesse->business_code ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Email</label>
                        <input autocomplete="off" type="text" class="form-control " name="email" placeholder="" value="<?= @$businesse->email ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Company Address</label>
                        <input autocomplete="off" type="text" class="form-control " name="company_address" placeholder="" value="<?= @$businesse->company_address ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Company Number</label>
                        <input autocomplete="off" type="text" class="form-control " name="company_number" placeholder="" value="<?= @$businesse->company_number ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Vat Number</label>
                        <input autocomplete="off" type="text" class="form-control " name="vat_number" placeholder="" value="<?= @$businesse->vat_number ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">No Of Shares</label>
                        <input autocomplete="off" type="text" class="form-control " name="no_of_shares" placeholder="" value="<?= @$businesse->no_of_shares ?>">
                    </div>
                </div>

                <div class=" col-md-6">
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Web Site</label>
                        <input autocomplete="off" type="text" class="form-control " name="web_site" placeholder="" value="<?= @$businesse->web_site ?>">
                    </div>

                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Payment Page Url</label>
                        <input autocomplete="off" type="text" class="form-control " id="payment_page_url" name="payment_page_url" placeholder="" value="<?= @$businesse->payment_page_url ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Country Code</label>
                        <input autocomplete="off" type="text" class="form-control " id="country_code" name="country_code" placeholder="" value="<?= @$businesse->country_code ?>">
                    </div>

                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Language Code</label>
                        <select name="language_code" class="form-control">
                            <option value="">--Select--</option>
                            <?php foreach ($json as $key=>$row) : ?>
                                    <option value="<?= $key; ?>" <?=@$businesse->language_code == $key?'selected="selected"' : ''; ?>><?= $row; ?></option>
                            <?php endforeach; ?>
                            <!-- <option value="en" <?= @$businesse->language_code == "en" ? "selected" : "" ?>>English</option>
                            <option value="fr" <?= @$businesse->language_code == "fr" ? "selected" : "" ?>>French</option>
                            <option value="hi" <?= @$businesse->language_code == "hi" ? "selected" : "" ?>>Hindi</option> -->
                        </select>
                    </div>

                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Telephone No</label>
                        <input autocomplete="off" type="text" class="form-control " id="telephone_no" name="telephone_no" placeholder="" value="<?= @$businesse->telephone_no ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Trading As</label>
                        <input autocomplete="off" type="text" class="form-control " id="trading_as" name="trading_as" placeholder="" value="<?= @$businesse->trading_as ?>">
                    </div>
                    <div class="form-group  col-md-12">
                        <label for="inputEmail4">Business Contact</label>
                        <select id="business_contacts" name="business_contacts[]" multiple class="form-control select2">
                            <?php
                            if (isset($businesse) && (!empty($businesse->business_contacts))) {
                                $arr = json_decode(@$businesse->business_contacts);
                                foreach ($businessContacts as $row) : ?>
                                    <option value="<?= $row['id']; ?>" <?php if ($arr) {
                                                                            echo  in_array($row['id'], $arr) ? 'selected="selected"' : '';
                                                                        } ?>>
                                        <?= $row['surname']; ?>
                                    </option>
                            <?php endforeach;
                            } ?>
                        </select>
                    </div>

                    <?php if (in_array($role, [1, 2])) { ?>
                        <div class="form-group col-md-12">
                            <br><span class="help-block">Primary Business</span><br>
                            <span class="help-block">
                                <input type="checkbox" name="default_business" id="default_business" <?= !empty(@$businesse->default_business) ? "checked" : ''; ?>>
                            </span>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>


<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    $("#status").on("change", function() {
        <?php if (isset($secret) && (!empty($secret->key_value))) { ?>
            var vall = '<?= base64_encode(@$secret->key_value) ?>';
            if ($(this).is(":checked") === true) {
                $('#key_value').val(atob(vall))
            } else {
                $('#key_value').val("*************")
            }
        <?php } ?>
    })
</script>