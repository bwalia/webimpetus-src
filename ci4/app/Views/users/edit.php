<?php
require_once(APPPATH . 'Views/common/edit-title.php');
$str = file_get_contents(APPPATH . 'languages.json');
$json = json_decode($str, true);
$roles = getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false);
?>

<div class="white_card_body">
    <div class="card-body">
        <form action="/users/update" method="post" id="userform">
            <div class="form-row">
                <div class="form-group required  col-md-4">
                    <label for="inputName">Name</label>
                    <input type="text" class="form-control required" id="inputName" name="name" placeholder=""
                        value="<?= @$user->name ?>" />
                        <span class="form-control-feedback" id="nameError"></span>
                </div>
                <input type="hidden" class="form-control " name="id" placeholder="" value="<?= @$user->id ?>" />
                <div class="form-group required col-md-4">
                    <label for="inputEmail">Email</label>
                    <input type="email" id="inputEmail" class="form-control required" name="email" placeholder=""
                        value="<?= @$user->email ?>">
                        <span class="form-control-feedback" id="emailError"></span>
                </div>
                <?php if (empty($user->id)) { ?>
                    <div class="form-group required col-md-4">
                        <label for="inputPassword4">Password</label>
                        <input type="password" id="inputPassword4" class="form-control required" name="password"
                            placeholder="">
                            <span class="passwordIcon psswrdIcon">
                                <a href="#" onclick="showPassword('inputPassword4')">
                                    <i class="fa fa-eye"></i><i class="fa fa-eye-slash"></i>
                                </a>
                            </span>
                            <span class="form-control-feedback" id="passwordError"></span>
                    </div>
                <?php } ?>
                <div class="form-group col-md-12">
                    <label for="inputAddress">Address</label>
                    <textarea type="text" class="form-control" name="address" placeholder=""
                        value=""><?= @$user->address ?></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputNotes">Notes</label>
                    <textarea class="form-control" id="inputNotes" name="notes"><?= @$user->notes ?></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group  col-md-12">
                    <label for="inputLanCode">Language Code</label>
                    <select name="language_code" id="inputLanCode" class="form-control">
                        <option value="">--Select--</option>
                        <?php foreach ($json as $key => $row): ?>
                            <option value="<?= $key; ?>" <?= @$user->language_code == $key ? 'selected="selected"' : ''; ?>>
                                <?= $row; ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>
            </div>
            
            <?php if ((isset($_SESSION['role']) && isset($roles['role_name']) && $roles['role_name'] == "Administrator") || session('uuid') == 1) { ?>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="userRole">Set User Role</label>
                        <select name="role" id="userRole" class="form-control">
                        <option value="">--Select--</option>
                        <?php if (!empty($user_roles) && $user_roles) { ?>
                            <?php foreach ($user_roles as $key => $row): ?>
                                <option value="<?= $row['uuid']; ?>" <?= @$user->role == $row['uuid'] ? 'selected="selected"' : ''; ?>>
                                    <?= $row['role_name']; ?>
                                </option>
                            <?php endforeach; ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputState">
                        <i class="fa fa-shield"></i> Set User Module Permissions
                    </label>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllModules">
                            <i class="fa fa-check-square"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllModules">
                            <i class="fa fa-square-o"></i> Clear All
                        </button>
                    </div>
                    <select id="sid" name="sid[]" multiple class="form-control select2-permissions" data-placeholder="Select modules to grant access...">
                        <?php
                            $arr = (isset($user) && (!empty($user->permissions))) ? json_decode(@$user->permissions, true) : false;
                            foreach ($menu as $row): ?>
                                <option value="<?= $row['id']; ?>" <?php if ($arr && is_array($arr))
                                      echo
                                          in_array($row['id'], $arr) ? 'selected="selected"' : '' ?>><?= $row['name']; ?>
                                </option>
                            <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-info-circle"></i> Select all modules this user should have access to. Changes take effect on next login.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <?php if (!empty($user->id)) { ?>
            <h3 class="f_s_25 f_w_700 dark_text mr_30 mt_30">Change Password </h3>

            <form action="/users/savepwd" method="post" id="chngpwd">

                <div class="form-row">
                    <div class="form-group  col-md-6">
                        <label for="npassword">New Password</label>
                        <input type="password" id="npassword" class="form-control" name="npassword" placeholder="">
                        <span class="passwordIcon psswrdIcon">
                            <a href="javascript:void(0)" onclick="showPassword('npassword')">
                                <i class="fa fa-eye"></i><i class="fa fa-eye-slash"></i>
                            </a>
                        </span>
                        <span class="form-control-feedback" id="newpasswordError"></span>
                    </div>
                    <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$user->id ?>" />
                    <div class="form-group  npassword col-md-6">
                        <label for="inputPassword4">Confirm Password</label>
                        <input type="password" class="form-control" name="cpassword" placeholder="">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php } ?>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
<script>
    if ($("#chngpwd").length > 0) {
        $("#chngpwd").validate({
            rules: {
                npassword: {
                    required: true,
                },
                cpassword: {
                    required: true,
                    equalTo: "#npassword"
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                }

            },
        })
    }

    $("#userform").submit(function (evt) {
        const password = $("#inputPassword4").val();
        validatePassword(password, evt, "passwordError");
        const inputEmail = $("#inputEmail").val();
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        const nameRegex = /^[A-Za-z\s'\-\.]+$/;
        if (emailRegex.test(inputEmail)) {
            $("#emailError").text("");
        } else {
            $("#emailError").text("The email should be valid.");
            evt.preventDefault();
            return false;
        }
        const inputName = $("#inputName").val(); 
        if (inputName.length > 1 && inputName.trim() !== '' && nameRegex.test(inputName)) {
            $("#nameError").text("");
        } else {
            $("#nameError").text("Please enter a valid name.");
            evt.preventDefault();
            return false;
        }
    });

    $("#chngpwd").submit(function(evt) {
        const nPassword = $("#npassword").val();
        validatePassword(nPassword, evt, "newpasswordError");
    })
    function validatePassword(password, evt, ele) {
        if (!password || password == "") return false;
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?!.*\s).{8,}$/;
        if (passwordRegex.test(password)) {
            $("#" + ele).text("");
        } else {
            $("#" + ele).text("The password must have at least 8 characters, at least 1 number, at least 1 capital letter, and does not contains whitespace.");
            evt.preventDefault();
            return false;
        }
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
</script>

<style>
    /* Enhanced Select2 styling for permissions */
    .select2-permissions .select2-selection__choice {
        background-color: #667eea !important;
        color: white !important;
        border: none !important;
        padding: 4px 8px !important;
        border-radius: 12px !important;
        margin: 3px !important;
        font-size: 13px !important;
    }

    .select2-permissions .select2-selection__choice__remove {
        color: white !important;
        margin-right: 5px !important;
        font-weight: bold !important;
    }

    .select2-permissions .select2-selection__choice__remove:hover {
        color: #ffcccc !important;
    }

    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e3e6f0 !important;
        border-radius: 0.35rem !important;
        min-height: 45px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
    }

    .select2-search__field {
        min-height: 35px !important;
    }

    /* Role select styling */
    #userRole {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        height: 45px;
    }

    #userRole:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>

<script>
    // Initialize Select2 for permissions with enhanced features
    $(document).ready(function() {
        $('#sid').select2({
            placeholder: 'Select modules to grant access...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false, // Keep dropdown open for multiple selections
            templateResult: formatModule,
            templateSelection: formatModuleSelection
        });

        // Format module options with icons
        function formatModule(module) {
            if (!module.id) {
                return module.text;
            }

            var $module = $(
                '<span><i class="fa fa-cube" style="margin-right: 8px; color: #667eea;"></i>' +
                module.text +
                '</span>'
            );
            return $module;
        }

        // Format selected modules
        function formatModuleSelection(module) {
            return module.text;
        }

        // Show count of selected modules
        $('#sid').on('change', function() {
            var count = $(this).val() ? $(this).val().length : 0;
            var label = count === 1 ? 'module' : 'modules';
            if (count > 0) {
                $(this).next('.select2').find('.select2-selection__rendered')
                    .attr('title', count + ' ' + label + ' selected');
            }
        });

        // Select All Modules button
        $('#selectAllModules').on('click', function() {
            $('#sid option').prop('selected', true);
            $('#sid').trigger('change');
        });

        // Deselect All Modules button
        $('#deselectAllModules').on('click', function() {
            $('#sid option').prop('selected', false);
            $('#sid').trigger('change');
        });
    });
</script>