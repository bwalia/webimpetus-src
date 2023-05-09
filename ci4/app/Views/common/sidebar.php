<!-- sidebar  -->
<nav class="sidebar mini_sidebar">
    <div class="logo d-flex justify-content-between">
        <?php if (!empty($_SESSION['logo'])) { ?>
            <a class="large_logo" href="/"><img src="<?= 'data:image/jpeg;base64,' . $_SESSION['logo'] ?>" alt=""></a>
            <!--<a class="small_logo" href="/"><img src="<?= 'data:image/jpeg;base64,' . $_SESSION['logo'] ?>" alt=""></a>-->
            <a class="small_logo" href="/"><img src="/assets/img/smallLogo.png" alt=""></a>
        <?php } ?>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>

    <div class="sidebar-search-wrapper">
        <input type="text" class="form-control sidebar-search search-placeholder" id="myInput" onkeyup="search_menu()" placeholder="Search by name" />
        <span class="sidebar-search-label sidebar-search-front-label">Search</span>
        <span class="sidebar-search-label sidebar-search-back-label">Navigation</span>
    </div>



    <ul id="sidebar_menu">
        <!--<li class="active"><a href="#">Dashboard</a></li>
            <li>
               <a href="#" id="1">Pages
               <span class="fas fa-caret-down"></span>
               </a>
               <ul class="item-show-1">
                  <li><a href="#">Contact us</a></li>
                  <li><a href="#">Our Team</a></li>
               </ul>
            </li>
            <li>
               <a href="#" id="2">Services
               <span class="fas fa-caret-down"></span>
               </a>
               <ul class="item-show-2">
                  <li><a href="#">App Design</a></li>
                  <li><a href="#">Web Design</a></li>
               </ul>
            </li>
            <li><a href="#">Users</a></li>
            <li><a href="#">Message</a></li>
            <li><a href="#">Bookmark</a></li>
            <li><a href="#">Files</a></li>
          </ul>



        <ul id="sidebar_menu"> -->




        <?php if (empty($_SESSION['permissions'])) { ?>
            <li class="mm-active">
                <a class="has-arrow" href="/dashboard" aria-expanded="true">
                    <div class="nav_icon_small">
                        <img src="/assets/img/menu-icon/dashboard.svg" alt="">
                    </div>
                    <div class="nav_title">
                        <span>Dashboard</span>
                    </div>
                </a>

            </li>
        <?php } else {

            ?>


            <?php

            $menu = MenuByCategory(); //getWithOutUuidResultArray("menu", [], true, "sort_order");
            //print_r($menu); die;
            $rowArray = getRowArray("users", ["id" => $_SESSION['uuid']]);
            $userMenus = $rowArray->permissions;
            $role = $rowArray->role;
            if ($userMenus) {
                $userMenus = json_decode($userMenus);
            }

            if (isset($_SESSION["menucode"])) {

                $menucode = $_SESSION["menucode"];
            } else {
                $menucode = 1;
            }
            // prd($menu);
            $catname = '';
            $inc = 1;
            foreach ($menu as $val) {

                if ($role == 1 || in_array($val['id'], $userMenus)) {
                    $activeIcon = $val['icon'];
                    //echo $catname.$val['catname'];
        
                    ?>

                    <?php if ($catname != $val['catname']) {

                        if ($catname !== '') {
                            echo '</ul></li>';
                        }

                        $catname = $val['catname'];
                        ?>

                        <li class="cat-listing">
                            <a href="#" id="<?= $inc ?>" class="cat-caret-wrapper"><?php echo $val['catname']; ?>
                                <span class="fas fa-caret-down"></span>
                            </a>
                            <ul class="item-show-1">
                                <li><a href="<?php echo $val['link']; ?>" class="<?php if (@$menucode == $val['id'])
                                       echo "active"; ?>"><i
                                            class="<?php echo $val['icon']; ?>"></i> <span>
                                            <?php echo $val['name']; ?>
                                        </span></a></li>

                            <?php } else { ?>
                                <li><a href="<?php echo $val['link']; ?>" class="<?php if (@$menucode == $val['id'])
                                       echo "active"; ?>"><i
                                            class="<?php echo $val['icon']; ?>"></i> <span>
                                            <?php echo $val['name']; ?>
                                        </span></a></li>
                            <?php } ?>


                            <?php /* <li><a href="<?php echo $val['link']; ?>" class="<?php if(@$menucode == $val['id'])echo "active";?>"><i class="<?php echo $val['icon']; ?>"></i> <span><?php echo $val['name']; ?> </span></a></li>	*/?>

                            <?php $inc++;
                }
            }

        }
        $_SESSION["menucode"] = 0;
        $menu = getWithOutUuidResultArray("menu", [], true, "sort_order");
        ?>
            </ul>



</nav>

<!--/ sidebar  -->

<style type="text/css">
    .footer_iner.text-center {
        display: block;
    }

    .f_s_12.f_w_400.text_color_1 img {
        width: 130px;
        padding: 15px;
        height: auto;
    }

    .sub-menu {
        display: none;
    }

    #sidebar_menu li a span {
        /* position: absolute;
  top: 50%;
  right: 20px; */
        display: contents;
        transform: translateY(-50%);
        font-size: 18px;
        transition: transform 0.4s;
    }

    #sidebar_menu li a span.rotate {
        transform: translateY(-50%) rotate(-180deg);
    }
</style>

<script>
    function search_menu() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        ul = document.getElementById("sidebar_menu");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
    const searchElement = document.getElementsByClassName('sidebar-search');
    searchElement[0].onfocus = () => {
        document.getElementsByClassName('sidebar-search-front-label')[0].style.display = 'none'
    }

    searchElement[0].onfocusout = () => {
        document.getElementsByClassName('sidebar-search-front-label')[0].style.display = 'flex'
    }
    searchText = document.getElementsByClassName("sidebar-search-label");
    for (const searchEle of searchText) {
        searchEle.onclick = () => {
            document.getElementById('myInput').focus();
        }
    }

</script>