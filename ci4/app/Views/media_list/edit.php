<?php require_once(APPPATH . 'Views/media_list/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">

        <form id="adddomain" method="post" action="/gallery/update" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputEmail4">Code</label>
                    <input type="text" class="form-control" id="title" name="code" value="<?= @$media_list->code ?>"
                        placeholder="">
                    <input type="hidden" class="form-control" name="id" placeholder=""
                        value="<?= @$media_list->id ?>" />
                    <input type="hidden" class="form-control" name="uuid" placeholder=""
                        value="<?= @$media_list->uuid ?>" />
                </div>

                <div class="form-group col-md-12">
                    <span class="all-media-image-files">
                        <?php if (!empty(@$media_list->name)) {

                            $tokens = explode('.', $media_list->name);
                            $extension = $tokens[count($tokens) - 1];

                            $varray = ['webm', 'wmv', 'ogg', 'mp4', 'mov', 'flv', 'avi', 'mkv'];

                            if (in_array(trim($extension), $varray)) {
                                ?>

                                <video width="320" height="240" controls>
                                    <?php foreach ($varray as $val) { ?>
                                        <source src="<?= @$media_list->name ?>" type=video/<?= $val ?>>
                                    <?php } ?>
                                </video><br>

                            <?php } else { ?>


                                <img src="<?= @$media_list->name ?>" width="140px"><br>
                            <?php }
                        } ?>
                    </span>
                </div>

                <div class="form-group col-md-12">
                    <label for="inputAddress">Upload Image</label>
                    <div class="uplogInrDiv" id="drop_file_doc_zone">

                        <input type="file" name="file" class="fileUpload" id="customFile">
                        <div class="uploadBlkInr">
                            <div class="uplogImg">
                                <img src="/assets/img/fileupload.png" />
                            </div>
                            <div class="uploadFileCnt">
                                <p>
                                    <a href="#">Upload a file </a> file chosen or drag
                                    and drop
                                </p>
                                <p>
                                    <span>Video, PNG, JPG, GIF up to 10MB</span>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="form-group col-md-12">
                    <label for="inputEmail4">Status</label>
                </div>
                <div class="form-group col-md-12">

                    <label for="inputEmail4"><input type="radio" value="1" class="form-control" id="status"
                            name="status" <?= @$media_list->status == 1 ? 'checked' : '' ?> placeholder=""> Yes</label>

                    <label for="inputEmail4"><input type="radio" <?= @$media_list->status == 0 ? 'checked' : '' ?>
                            value="0" class="form-control" id="status" name="status" placeholder=""> No</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>


<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->

<script>
    var id = "<?= @$media_list->id ?>";

    $(document).on('drop', '#drop_file_doc_zone', function (e) {

        // $("#ajax_load").show();
        console.log(e.originalEvent.dataTransfer);
        if (e.originalEvent.dataTransfer) {
            if (e.originalEvent.dataTransfer.files.length) {
                e.preventDefault();
                e.stopPropagation();
                var i = 0;
                while (i < e.originalEvent.dataTransfer.files.length) {
                    newUploadDocFiles(e.originalEvent.dataTransfer.files[i], id);
                    i++;
                }
            }
        }
    });


    $(document).on("change", ".fileUpload", function () {

        for (var count = 0; count < $(this)[0].files.length; count++) {

            newUploadDocFiles($(this)[0].files[count], id);
        }

    });



    function newUploadDocFiles(fileobj, id) {

        $("#ajax_load").hide();

        var form = new FormData();

        form.append("file", fileobj);
        form.append("mainTable", class_name);
        form.append("id", id);

        $.ajax({
            url: '/gallery/uploadMediaFiles',
            type: 'post',
            dataType: 'json',
            maxNumberOfFiles: 1,
            autoUpload: false,
            success: function (result) {

                $("#ajax_load").hide();
                if (result.status == '1') {
                    $(".all-media-image-files").html(result.file_path);
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#ajax_load").hide();
                console.log(textStatus, errorThrown);
            },
            data: form,
            cache: false,
            contentType: false,
            processData: false
        });

    }

    $("#delete_image_logo").on("click", function (e) {
        e.preventDefault();
        $(".all-media-image-files").html("");
    })
</script>