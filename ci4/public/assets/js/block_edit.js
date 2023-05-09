$(document).ready(function () {
    // Don't allow PHP code except role 'Admin with BLock'
    if (role != '2') {
        $("textarea.block-text").on("input", function () {
            let block_text = $(this).val();
            var php_start_tag = "<?";
            var php_end_tag = "?>";
            if (block_text.indexOf(php_start_tag) != -1) {
                alert("You are not allowed to enter PHP code here!");
                $("textarea.block-text").val(block_text.replace(php_start_tag, "<"));
                return false;
            }

            if (block_text.indexOf(php_end_tag) != -1) {
                alert("You are not allowed to enter PHP code here!");
                $("textarea.block-text").val(block_text.replace(php_end_tag, ">"));
                return false;
            }

        });
    }
});