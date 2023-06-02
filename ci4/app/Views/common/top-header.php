<?php
$business = getAllBusiness();
//print_r($_SESSION); die;
if(empty($_SESSION['uuid'])){?>
<script>
window.location.href="/";
</script>
<?php
}
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
                            <div class="business-uuid-selector ml-3 mr-3">
                                <input type="search"  class="form-control" name="search" value="<?php echo isset($_GET['search'])?$_GET['search']:''?>" placeholder="Search..." />
                            </div>
                        </form>
                        <div class="search-icon">
                            <i class="fas fa-times" id="header-search-icon"></i>
                        </div>
                    </div>


                    <div class="header_right d-flex justify-content-between align-items-center">

                    
                        
                        <div class="business-uuid-selector mr-3">
                            <select name="uuid_business_id" id="uuidBusinessIdSwitcher" class="form-control dashboard-dropdown">
                                <?php foreach($business as $eachUuid) { ?>
                                <option value="<?php echo $eachUuid['uuid']?>"<?php if(@$_SESSION['uuid_business'] == $eachUuid['uuid']) { echo "selected"; } ?>>  <?php echo $eachUuid['name']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="profile_info">
                            <img src="<?=(!empty($_SESSION['profile_img']))?'data:image/jpeg;base64,'.$_SESSION['profile_img']:'/assets/img/1.jpg'?>" alt="#">

                            <!--empty($_SESSION['role']) && $_SESSION['role']==1?'':''-->

                            <div class="profile_info_iner">
                                <div class="profile_author_name">                                    
                                    <h5><?=!empty($_SESSION['uname'])?$_SESSION['uname']:''?></h5>
                                </div>
                                <div class="profile_info_details">
                                     <span><i class="fa fa-envelope"></i><?=!empty($_SESSION['uemail'])?$_SESSION['uemail']:''?></span>
                                     <?php if(!empty($_SESSION['role']) && $_SESSION['role']==1){?>
                                    <a href="/dashboard/user_role"><i class="fa fa-eye"></i>Role Based Access Manager</a><?php }?>
                                    <a href="/dashboard/chgpwd"><i class="fa fa-eye"></i>My Profile</a>
                                    <?php if(!empty($_SESSION['role']) && $_SESSION['role']==1){?><a href="/dashboard/settings"><i class="fa fa-cog"></i>Settings</a><?php }?>
                                    <a href="/home/logout"><i class="fa fa-sign-out-alt"></i>Log Out </a>
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