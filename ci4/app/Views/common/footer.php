</div>
</div>

</div>
</div>
</div>

<?php require_once(APPPATH . 'Views/common/scripts.php'); ?>
<?php require_once(APPPATH . 'Views/common/footer_copyright.php'); ?>

</section>
<script>
$(document).ready(function() {
    // Language Switcher Handler
    $('#languageSwitcher').on('change', function() {
        const selectedLanguage = $(this).val();

        // Save language preference via AJAX
        $.ajax({
            url: '/dashboard/setLanguage',
            method: 'POST',
            data: { language: selectedLanguage },
            success: function(response) {
                // Reload page to apply new language
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error switching language:', error);
            }
        });
    });

    // Font Size Controls
    let currentFontSize = localStorage.getItem('fontSize') ? parseInt(localStorage.getItem('fontSize')) : 100;

    // Apply saved font size on page load
    function applyFontSize(size) {
        document.documentElement.style.fontSize = size + '%';
        $('#fontSizeDisplay').text(size + '%');
        localStorage.setItem('fontSize', size);
    }

    // Initialize with saved size
    applyFontSize(currentFontSize);

    // Increase font size
    $('#increaseFontSize').on('click', function() {
        if (currentFontSize < 150) { // Max 150%
            currentFontSize += 10;
            applyFontSize(currentFontSize);
        }
    });

    // Decrease font size
    $('#decreaseFontSize').on('click', function() {
        if (currentFontSize > 70) { // Min 70%
            currentFontSize -= 10;
            applyFontSize(currentFontSize);
        }
    });

    // Reset font size on double-click of display
    $('#fontSizeDisplay').on('dblclick', function() {
        currentFontSize = 100;
        applyFontSize(currentFontSize);
    });

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
        var nameRegex = /^[a-zA-Z ]{2,64}$/;
        if (nameVal.length > 2) {
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
});
</script>