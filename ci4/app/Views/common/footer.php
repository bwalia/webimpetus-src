</div>
</div>

</div>
</div>
</div>

<?php require_once(APPPATH . 'Views/common/scripts.php'); ?>
<?php require_once(APPPATH . 'Views/common/footer_copyright.php'); ?>

</section>
<script>
    $('input[type=search]').on('search', function () {
        if ($(this).val() == "") {
            window.location.href = '/';
        }
        // search logic here
        // this function will be executed on click of X (clear button)
    });

    // $('#sidebar_menu li').on('click', function(e) {
    //   $(this).children('ul').toggle();
    //   $(this).children('ul').toggleClass("show");
    //   $(this).siblings('li').find('ul').hide();
    //   $(this).siblings('li ul').toggleClass("show");
    //   e.stopPropagation();
    // });

    $('#sidebar_menu li a').click(function () {
        var id = $(this).attr('id');
        $('#sidebar_menu li ul.item-show-' + id).toggleClass("show");
        $('#sidebar_menu li #' + id + ' span').toggleClass("rotate");

    });

    $('#sidebar_menu li').click(function () {
        $(this).addClass("active").siblings().removeClass("active");
    });

    $("input[type=email]").focusout(function (event) {
        validateEmail($(this).val(), $(this), event);
    })
    $("input[name=telephone_no]").focusout(function (event) {
        validatePhoneNo($(this).val(), event);
    })

    function validatePhoneNo(phoneNo, event) {
        var phonePattern = /^\d{10}$/;
        if (phonePattern.test(phoneNo)) {
            $('#phoneError').text('');
            $('#phoneError').remove();
        } else {
            if ($("#phoneError").length === 0) {
                $("<span class='form-control-feedback' id='phoneError'>The Tele/Phone Number should be valid</span>").insertAfter($("input[name=telephone_no]"));
            }
            event.preventDefault();
            return false;
        }
    }

    function validateEmail(email, errorEle, event) {
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (emailRegex.test(email)) {
            $("#emailError").text("");
            $("#emailError").remove();
        } else {
            if ($("#emailError").length === 0) {
                $("<span class='form-control-feedback' id='emailError'>The email should be valid.</span>").insertAfter($(errorEle));
            }
            event.preventDefault();
            return false;
        }
    }

    function validateName(name, event) {
        let nameVal = name.val();
        var nameRegex = /^[a-zA-Z ]{2,30}$/;
        if (nameRegex.test(nameVal)) {
            $("#nameError").text("");
            $("#nameError").remove();
        } else {
            if ($("#nameError").length === 0) {
                $("<span class='form-control-feedback' id='nameError'>Invalid name! Please enter at least 2 letters, using only alphabetic characters.</span>").insertAfter(name);
            }
            event.preventDefault();
            return false;
        }
    }
</script>