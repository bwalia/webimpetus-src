<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<?php
$customers = $additional_data["customers"];
$businesses = getWithOutUuidResultArray("businesses");

?>
<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?>
            enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group  col-md-6">
                    <label for="inputEmail4">Employee Business Access List</label>
                    <select id="businesses" name="businesses[]" multiple class="form-control select2">
                        <?php
                        if (isset($employee) && (!empty($employee->businesses))) {
                            $arr = json_decode(@$employee->businesses);
                            foreach ($businesses as $row): ?>
                                <option value="<?= $row['id']; ?>" <?php if ($arr)
                                     echo
                                         in_array($row['id'], $arr) ? 'selected="selected"' : '' ?>>
                                    <?= $row['name']; ?>
                                </option>
                            <?php endforeach;
                        } ?>
                    </select>

                </div>

                <div class="form-group required col-md-6">
                    <label for="inputEmail4">First Name</label>
                    <input type="text" class="form-control required" id="first_name" name="first_name" placeholder=""
                        value="<?= @$employee->first_name ?>">
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Surname</label>
                    <input type="text" class="form-control" id="surname" name="surname" placeholder=""
                        value="<?= @$employee->surname ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="inputEmail4">Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder=""
                        value="<?= @$employee->title ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4"> Salutation</label>
                    <input type="text" class="form-control" id="saludation" name="saludation" placeholder=""
                        value="<?= @$employee->saludation ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="inputEmail4">News Letter Status</label>
                    <input type="text" class="form-control" id="news_letter_status" name="news_letter_status"
                        placeholder="" value="<?= @$employee->news_letter_status ?>">
                </div>
            </div>
            <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$employee->uuid ?>" />

            <div class="form-row">
                <div class="form-group required col-md-6">
                    <label for="inputEmail4">Email</label>
                    <input type="text" class="form-control required email" id="email" name="email" placeholder=""
                        value="<?= @$employee->email ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="inputEmail4">Password</label>
                    <input autocomplete="new-password" type="password" class="form-control" id="password"
                        name="password" placeholder="" value="">
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
                    <input type="text" class="form-control" id="direct_phone" name="direct_phone" placeholder=""
                        value="<?= @$employee->direct_phone ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="inputEmail4">Mobile</label>
                    <input type="text" class="form-control phone" id="mobile" name="mobile" placeholder=""
                        value="<?= @$employee->mobile ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputEmail4">Direct Fax</label>
                    <input type="text" class="form-control" id="direct_fax" name="direct_fax" placeholder=""
                        value="<?= @$employee->direct_fax ?>">
                </div>

                <div class="form-check col-md-1">
                </div>
                <div class="form-check checkbox-section col-md-3">
                    <div class="checkbox-label">

                        <input class="form-check-input" name="allow_web_access" id="allow_web_access"
                            value="<?php echo @$employee->allow_web_access; ?>" type="checkbox" <?php if (@$employee->allow_web_access == "1") {
                                   echo
                                       "checked";
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
                    <textarea class="form-control" id="comments" name="comments"><?= @$employee->comments ?></textarea>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
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

    $("#email").blur(function (e) {
        var email = $("#email").val();
        $.ajax({
            url: baseUrl + "/employees/checkEmail",
            data: {
                email: email
            },
            method: 'post',
            success: function (res) {
                console.log(res);
                e.preventDefault();
                return false
            }
        })
    })
    $(":submit").click( function ( e ) {
        validateName($("#first_name"), e);
        var email = $("#email").val();
        $.ajax({
            url: baseUrl + "/employees/checkEmail",
            data: {
                email: email
            },
            method: 'post',
            success: function (res) {
                console.log(res);
                e.preventDefault();
                return false
            }
        })
    })
</script>