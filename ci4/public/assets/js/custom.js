(function ($) {
    "use strict";

    // metisMenu 
    $("#sidebar_menu").metisMenu();
    // metisMenu 
    $("#admin_profile_active").metisMenu();


    // console.log($(".sidebar").hasClass("mini_sidebar"))

    // if($(".sidebar").hasClass("mini_sidebar")){
    //     localStorage.setItem("menu-class","close")
    // }
    // else{
    //     localStorage.setItem("menu-class","open")
    // }
    // const pathname = window.location.pathname
    // console.log("pathname",pathname)
    // const vsl =$( `a[href*="${pathname}"]` );
    // console.log(vsl.hasClass('active'))



    let menu_current = localStorage.getItem("menu-current")
    localStorage.setItem("menu-current", window.location.pathname)


    let menu_status = localStorage.getItem("menu-class")
    if (menu_status == "open") {
        $(".sidebar").removeClass("mini_sidebar");
        $(".main_content").removeClass("full_main_content");
    }
    else {
        $(".sidebar").addClass("mini_sidebar");
        $(".main_content").addClass("full_main_content");

    }

    $(".open_miniSide").click(function () {
        $(".sidebar").toggleClass("mini_sidebar");
        if ($(".sidebar").hasClass("mini_sidebar")) {
            localStorage.setItem("menu-class", "close");
            $(".sidebar-search-wrapper").hide();
        }
        else {
            localStorage.setItem("menu-class", "open")
            $(".sidebar-search-wrapper").show();
        }
        $(".main_content ").toggleClass("full_main_content");
        $(".footer_part ").toggleClass("full_footer");
    });



    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop();
        if (scroll < 400) {
            $('#back-top').fadeOut(500);
        } else {
            $('#back-top').fadeIn(500);
        }
    });

    // back to top 
    $('#back-top a').on("click", function () {
        $('body,html').animate({
            scrollTop: 0
        }, 1000);
        return false;
    });





    // PAGE ACTIVE 
    /*$( "#sidebar_menu" ).find( "a" ).removeClass("active");
    $( "#sidebar_menu" ).find( "li" ).removeClass("mm-active");
    $( "#sidebar_menu" ).find( "li ul" ).removeClass("mm-show");*/

    /*var current = window.location.pathname
    $("#sidebar_menu >li a").filter(function() {
    
        var link = $(this).attr("href");
        if(link){
            if (current.indexOf(link) != -1) {
                $(this).parents().parents().children('ul.mm-collapse').addClass('mm-show').closest('li').addClass('mm-active');
                $(this).addClass('active');
                return false;
            }
        }
    });*/

    // #NOTIFICATION_ 
    // for MENU notification
    $('.bell_notification_clicker').on('click', function () {
        $('.Menu_NOtification_Wrap').toggleClass('active');
    });

    $(document).click(function (event) {
        if (!$(event.target).closest(".bell_notification_clicker ,.Menu_NOtification_Wrap").length) {
            $("body").find(".Menu_NOtification_Wrap").removeClass("active");
        }
    });

    //notification section js
    $(".close_icon").click(function () {
        $(this).parents(".hide_content").slideToggle("0");
    });

    //active sidebar
    $('.sidebar_icon').on('click', function () {
        $('.sidebar').toggleClass('active_sidebar');
    });
    $('.sidebar_close_icon i').on('click', function () {
        $('.sidebar').removeClass('active_sidebar');
    });

    //active menu
    $('.troggle_icon').on('click', function () {
        $('.setting_navbar_bar').toggleClass('active_menu');
    });

    $('.custom_select').click(function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $('.custom_select.active').removeClass('active');
            $(this).addClass('active');
        }
    });


    $(document).click(function (event) {
        if (!$(event.target).closest(".custom_select").length) {
            $("body").find(".custom_select").removeClass("active");
        }
    });
    //remove sidebar
    $(document).click(function (event) {
        if (!$(event.target).closest(".sidebar_icon, .sidebar").length) {
            $("body").find(".sidebar").removeClass("active_sidebar");
        }
    });

    // check all
    $("#checkAll").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });


    //custom file
    $('.input-file').each(function () {
        var $input = $(this),
            $label = $input.next('.js-labelFile'),
            labelVal = $label.html();

        $input.on('change', function (element) {
            var fileName = '';
            if (element.target.value) fileName = element.target.value.split('\\').pop();
            fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
        });
    });

    //custom file
    $('.input-file2').each(function () {
        var $input = $(this),
            $label = $input.next('.js-labelFile1'),
            labelVal = $label.html();

        $input.on('change', function (element) {
            var fileName = '';
            if (element.target.value) fileName = element.target.value.split('\\').pop();
            fileName ? $label.addClass('has-file').find('.js-fileName1').html(fileName) : $label.removeClass('has-file').html(labelVal);
        });
    });

    //add class in radio buttons
    window.onload = function () {
        if (!$('[type="radio"]').hasClass("radio-button")) {
            $('[type="radio"]').addClass('radioInput');
            $('[type="radio"]').parent().addClass('radioLabel');
            $('[type="radio"]').parent().parent().addClass('radioGroup');
            $('[type="radio"]:checked').parent().addClass('radioCheckedLabel');
        }
    };

    $('input:radio').change(function () {
        $('.radioInput').parent().removeClass('radioCheckedLabel');

        if ($('.radioInput').is(":checked")) {
            $(this).parent().addClass('radioCheckedLabel');
        } else {
            //$(this).parent().removeClass('radioCheckedLabel');
        }
    });

    $(function () {
        $('.dragtoken').draggable({
            helper: 'clone'
        });
        $("#template_content").droppable({
            drop: function (event, ui) {
                let template_content = $("#template_content").val().trim();
                let current_content = '<*--' + ui.draggable.text().trim() + '--*>';
                $("#template_content").val(template_content + current_content);
            }
        });
    });

    $(".search-token-button").click(function () {
        let search_token = $('input[name="search-token"]').val();
        $.ajax({
            url: '/templates/getBlockListBySearch/' + search_token.trim(),
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                $("#ajax_load").hide();
                $('.token-list').empty();
                $.each(result.blocks_lists, function (i, item) {
                    let href = '<a href="' + baseURL + 'blocks/edit/' + item.id + '" target="_blank">' + item.code + '</a>';
                    $('.token-list').append('<li class="list-group-item list-group-item-action dragtoken" aria-current="true">' + href + '</li>');
                });
                $('.dragtoken').draggable({
                    helper: 'clone'
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#ajax_load").hide();
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    $(document).on("click", ".search-icon #header-search-icon", () => {
        $(".header_search .header_search_form").toggle('slow', () => {
            if ($(".header_search .header_search_form").is(':visible')) {
                $(".search-icon #header-search-icon").removeClass('fa-search').addClass('fa-times');
            }
            else {
                $(".search-icon #header-search-icon").removeClass('fa-times').addClass('fa-search');
            }
        });        
    })

}(jQuery));



