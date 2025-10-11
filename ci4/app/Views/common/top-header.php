<?php
$business = getAllBusiness();
//print_r($_SESSION); die;
if (empty($_SESSION['uuid'])) { ?>
    <script>
        window.location.href = "/";
    </script>
    <?php
}

$roles = isset($_SESSION['role']) ? getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false) : "";
?><!-- menu  -->
<div class="container-fluid no-gutters">
    <div class="row">
        <div class="col-lg-12 p-0 ">
            <div class="header_iner d-flex justify-content-between align-items-center">
                <div class="sidebar_icon d-lg-none">
                    <i class="ti-menu"></i>
                </div>
                <div class="line_icon open_miniSide d-none d-lg-block">
                    <i class="ti-menu"></i>
                </div>

                <div class="header_search">
                    <form action="/dashboard" class="header_search_form" style="margin: 0;">
                        <div class="business-uuid-selector ml-3 mr-3" style="max-width: 600px;">
                            <input type="search" class="form-control" name="search"
                                value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>"
                                placeholder="Search..."
                                style="width: 100%; max-width: 600px;" />
                        </div>
                    </form>
                    <div class="search-icon">
                        <i class="fas fa-times" id="header-search-icon"></i>
                    </div>
                </div>


                <div class="header_right d-flex justify-content-between align-items-center">



                    <div class="business-uuid-selector mr-3" style="min-width: 400px; max-width: 600px;">
                        <select name="uuid_business_id" id="uuidBusinessIdSwitcher"
                            class="form-control dashboard-dropdown" style="width: 100%;">
                            <option value="">-- Choose Business --</option>
                            <?php foreach ($business as $eachUuid) { ?>
                                <option value="<?php echo $eachUuid['uuid'] ?>" <?php if (@$_SESSION['uuid_business'] == $eachUuid['uuid']) {
                                       echo "selected";
                                   } ?>> <?php echo $eachUuid['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Language Switcher -->
                    <div class="language-switcher mr-3">
                        <style>
                            .language-switcher select {
                                border: 2px solid #667eea;
                                border-radius: 8px;
                                padding: 8px 12px;
                                font-weight: 500;
                                color: #495057;
                                transition: all 0.3s ease;
                                min-width: 120px;
                                background: white;
                            }
                            .language-switcher select:focus {
                                outline: none;
                                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
                                border-color: #5a67d8;
                            }
                            .language-switcher select option {
                                padding: 10px;
                            }
                        </style>
                        <select name="app_language" id="languageSwitcher" class="form-control dashboard-dropdown">
                            <option value="en" <?= (session('app_language') ?? 'en') == 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                            <option value="fr" <?= (session('app_language') ?? 'en') == 'fr' ? 'selected' : '' ?>>🇫🇷 Français</option>
                            <option value="nl" <?= (session('app_language') ?? 'en') == 'nl' ? 'selected' : '' ?>>🇳🇱 Nederlands</option>
                            <option value="hi" <?= (session('app_language') ?? 'en') == 'hi' ? 'selected' : '' ?>>🇮🇳 हिन्दी</option>
                        </select>
                    </div>

                    <div class="profile_info">
                        <img src="<?= (!empty($_SESSION['profile_img'])) ? 'data:image/jpeg;base64,' . $_SESSION['profile_img'] : '/assets/img/1.jpg' ?>"
                            alt="#">

                        <!--empty($_SESSION['role']) && $_SESSION['role']==1?'':''-->

                        <?php 
                            $roleName = "";
                            if (isset($_SESSION['role']) && isUUID($_SESSION['role'])) {
                                $role = $_SESSION['role'];
                                $roleName = getRoleNameByUUID($role);
                                $roleName = isset($roleName->role_name) ? $roleName->role_name : "";
                            }
                        ?>
                        <div class="profile_info_iner">
                            <div class="profile_author_name">
                                <h5>
                                    <?= !empty($_SESSION['uname']) ? $_SESSION['uname'] : '' ?>
                                </h5>
                            </div>
                            <div class="profile_info_details">
                                <span><i class="fa fa-envelope"></i>
                                    <?= !empty($_SESSION['uemail']) ? $_SESSION['uemail'] : '' ?>
                                </span>
                                <?php if ((isset($_SESSION['role']) && isset($roles['role_name']) && $roles['role_name'] == "Administrator") || session('uuid') == 1) { ?>
                                    <a href="/dashboard/user_role"><i class="fa fa-eye"></i>Role Based Access Manager</a>
                                <?php } ?>
                                <a href="/dashboard/chgpwd"><i class="fa fa-eye"></i>My Profile</a>
                                <?php if ((isset($_SESSION['role']) && isset($roles['role_name']) && $roles['role_name'] == "Administrator") || session('uuid') == 1) { ?><a
                                        href="/dashboard/settings"><i class="fa fa-cog"></i>Settings</a>
                                <?php } ?>
                                <a href="/home/logout"><i class="fa fa-sign-out-alt"></i>Log Out </a>
                                <div class="text-right p-2 text-info font-weight-bolder">
                                    You are logged in as: <strong><?= $roleName ? $roleName :''?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ menu  -->
<style>
    input[type=search]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }
</style>

<style>
    /* Make business selector dropdown wider */
    .business-uuid-selector .select2-container {
        min-width: 300px !important;
        max-width: 400px !important;
    }

    .business-uuid-selector .select2-container .select2-selection--single {
        height: auto !important;
        min-height: 38px !important;
        border: 2px solid #667eea !important;
        border-radius: 8px !important;
    }

    .business-uuid-selector .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        padding-left: 12px !important;
        font-weight: 500 !important;
    }

    .business-uuid-selector .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }
</style>