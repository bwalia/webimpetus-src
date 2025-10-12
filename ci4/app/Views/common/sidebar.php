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

    <ul id="sidebar_menu">
        <!-- Dashboard - Always shown at the top -->
        <?php
        $currentPath = $_SERVER['REQUEST_URI'] ?? '';
        $isDashboard = (strpos($currentPath, '/dashboard') === 0);
        ?>
        <li>
            <a href="/dashboard" class="<?= $isDashboard ? 'active' : '' ?>">
                <i class="fa fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if (!empty($_SESSION['permissions'])) { ?>
            <?php
            $menu = MenuByCategory(); //getWithOutUuidResultArray("menu", [], true, "sort_order");
            $permissions = $_SESSION['permissions'];
            $userMenus = array_map(function ($menu, $mKey) {
                return $menu['id'];
            }, $permissions, array_keys($permissions));

            if (isset($_SESSION["menucode"])) {

                $menucode = $_SESSION["menucode"];
            } else {
                $menucode = 1;
            }
            // prd($menu);
            $catname = '';
            $inc = 1;
            foreach ($menu as $val) {

                if (in_array($val['id'], $userMenus)) {
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
                            <a href="#" id="<?= $inc ?>" class="cat-caret-wrapper">
                                <?php echo $val['catname']; ?>
                                <span class="fas fa-caret-down"></span>
                            </a>
                            <ul class="item-show-1">
                                <li><a href="<?php echo $val['link']; ?>" class="<?php if (@$menucode == $val['id'])
                                       echo "active"; ?>"><i class="<?php echo $val['icon']; ?>"></i> <span>
                                            <?php echo $val['name']; ?>
                                        </span></a></li>

                            <?php } else { ?>
                                <li><a href="<?php echo $val['link']; ?>" class="<?php if (@$menucode == $val['id'])
                                       echo "active"; ?>"><i class="<?php echo $val['icon']; ?>"></i> <span>
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

    <!-- Search Menu at Bottom -->
    <div class="sidebar-search-wrapper">
        <input type="text" class="form-control sidebar-search search-placeholder" id="myInput" onkeyup="search_menu()"
            placeholder="Search Menu" />
    </div>

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

    /* JIRA-Style Sidebar Layout */
    .sidebar {
        display: flex;
        flex-direction: column;
        height: 100vh;
        overflow: hidden;
        background: #ffffff;
        border-right: 1px solid #dfe1e6;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    /* JIRA-Style Menu Container */
    #sidebar_menu {
        padding: 8px 0;
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* JIRA-Style Category Headers */
    #sidebar_menu li.cat-listing > a.cat-caret-wrapper {
        padding: 8px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #5e6c84;
        background: transparent;
        border-left: none;
        transition: all 0.15s ease;
        margin: 8px 8px 4px 8px;
        border-radius: 3px;
    }

    #sidebar_menu li.cat-listing > a.cat-caret-wrapper:hover {
        background: #ebecf0;
        color: #172b4d;
    }

    #sidebar_menu li.cat-listing > a.cat-caret-wrapper .fas {
        float: right;
        font-size: 10px;
        margin-top: 2px;
        transition: transform 0.2s ease;
        color: #5e6c84;
    }

    /* JIRA-Style Menu Items */
    #sidebar_menu li a {
        padding: 8px 16px;
        margin: 2px 8px;
        font-size: 13px;
        font-weight: 400;
        color: #172b4d;
        display: flex;
        align-items: center;
        border-left: none;
        border-radius: 3px;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    #sidebar_menu li a:hover {
        background: #ebecf0;
        color: #172b4d;
        border-left: none;
    }

    #sidebar_menu li a.active {
        background: #deebff;
        color: #0052cc;
        border-left: none;
        font-weight: 500;
    }

    /* JIRA-Style Menu Icons */
    #sidebar_menu li a i {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #5e6c84;
        transition: color 0.15s ease;
    }

    #sidebar_menu li a:hover i {
        color: #172b4d;
    }

    #sidebar_menu li a.active i {
        color: #0052cc;
    }

    /* Menu Text */
    #sidebar_menu li a span {
        display: contents;
        transform: translateY(-50%);
        font-size: 13px;
        transition: transform 0.4s;
        line-height: 1.4;
    }

    #sidebar_menu li a span.rotate {
        transform: translateY(-50%) rotate(-180deg);
    }

    /* JIRA-Style Submenu */
    #sidebar_menu li.cat-listing ul {
        background: transparent;
        padding: 4px 0;
        margin: 0 0 8px 0;
    }

    #sidebar_menu li.cat-listing ul li {
        list-style: none;
    }

    #sidebar_menu li.cat-listing ul li a {
        padding: 6px 16px 6px 46px;
        font-size: 13px;
        font-weight: 400;
        margin: 1px 8px;
    }

    #sidebar_menu li.cat-listing ul li a:hover {
        background: #ebecf0;
    }

    #sidebar_menu li.cat-listing ul li a.active {
        background: #deebff;
        color: #0052cc;
        font-weight: 500;
    }

    /* JIRA-Style Search Box at Bottom */
    .sidebar-search-wrapper {
        padding: 12px 16px;
        border-top: 1px solid #dfe1e6;
        background: #fafbfc;
        position: sticky;
        bottom: 0;
        z-index: 100;
        flex-shrink: 0;
    }

    .sidebar-search {
        background: #ffffff;
        border: 2px solid #dfe1e6;
        border-radius: 3px;
        padding: 8px 12px 8px 36px;
        font-size: 13px;
        font-weight: 400;
        color: #172b4d;
        transition: all 0.15s ease;
        width: 100%;
        box-shadow: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235e6c84' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cpath d='m21 21-4.35-4.35'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 10px center;
        background-size: 16px;
    }

    .sidebar-search:focus {
        background-color: white;
        border-color: #4c9aff;
        box-shadow: 0 0 0 1px #4c9aff;
        outline: none;
    }

    .sidebar-search::placeholder {
        color: #5e6c84;
        font-size: 13px;
        font-weight: 400;
    }

    /* JIRA-Style Logo Section */
    .sidebar .logo {
        padding: 16px 16px;
        border-bottom: 1px solid #dfe1e6;
        margin-bottom: 8px;
        background: #ffffff;
    }

    /* JIRA-Style Scrollbar */
    #sidebar_menu::-webkit-scrollbar {
        width: 8px;
    }

    #sidebar_menu::-webkit-scrollbar-track {
        background: transparent;
    }

    #sidebar_menu::-webkit-scrollbar-thumb {
        background: #c1c7d0;
        border-radius: 4px;
        border: 2px solid #ffffff;
    }

    #sidebar_menu::-webkit-scrollbar-thumb:hover {
        background: #a5adba;
    }

    /* JIRA-Style Badge/Counter */
    .menu-badge {
        background: #0052cc;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        margin-left: auto;
        min-width: 20px;
        text-align: center;
    }

    /* JIRA-Style Nav Icon & Title */
    .nav_icon_small {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .nav_icon_small img {
        width: 18px;
        height: 18px;
        opacity: 0.7;
        transition: opacity 0.15s ease;
    }

    #sidebar_menu li a:hover .nav_icon_small img,
    #sidebar_menu li a.active .nav_icon_small img {
        opacity: 1;
    }

    .nav_title {
        flex: 1;
        display: flex;
        align-items: center;
    }

    .nav_title span {
        font-size: 13px;
        font-weight: 400;
        color: inherit;
        line-height: 1.4;
    }

    /* Collapsed/Mini Sidebar Adjustments */
    .mini_sidebar #sidebar_menu li a span {
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 991px) {
        #sidebar_menu li a {
            padding: 10px 15px;
            font-size: 13px;
        }

        #sidebar_menu li.cat-listing > a.cat-caret-wrapper {
            padding: 10px 15px;
            font-size: 10px;
        }
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

    // Optional: Add keyboard shortcut to focus search (but don't auto-focus)
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById("myInput");

        // Focus search when "/" is pressed (common search shortcut)
        document.addEventListener('keydown', function(e) {
            // Focus search when "/" is pressed (unless already in an input field)
            if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }

            // Also allow Ctrl+K or Cmd+K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });
    });
</script>