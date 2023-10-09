<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="addcat" method="post" action="/domains/update" enctype="multipart/form-data"
            onsubmit="return frmValidate();">
            <div class="form-row">

                <div class="form-group required col-md-12">
                    <label for="inputState">Choose Customer</label>
                    <select id="uuid" name="uuid" class="form-control required select2">
                        <option value="" selected="">--Select--</option>
                        <?php foreach ($customers as $row): ?>
                            <option value="<?= $row['uuid']; ?>" <?= (is_object($domain) && property_exists($domain, 'customer_uuid') && $row['uuid'] == $domain->customer_uuid) ? 'selected' : '' ?>>
                                <?= $row['company_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php 
                    $serviceUuids = [];
                    foreach ($serviceDomains as $key => $serviceDomain) {
                        array_push($serviceUuids, $serviceDomain['service_uuid']);
                    }
                ?>
                <div class="form-group col-md-12">
                    <label for="inputState">Choose Service</label>
                    <select id="sid" name="sid[]" multiple class="form-control select2">
                        <option value="">--Select--</option>
                        <?php foreach ($services as $row): ?>
                            <option value="<?= $row['uuid']; ?>" <?= (in_array($row['uuid'], $serviceUuids)) ? 'selected' : '' ?>>
                                <?= $row['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group required col-md-12">
                    <label for="inputEmail4">Domain Name</label>
                    <input type="text" class="form-control required" id="name" name="name" placeholder=""
                        value="<?= @$domain->name ?>">

                    <p id="domain_error"></p>

                    <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$domain->uuid ?>" />
                </div>

                <div class="form-group col-md-12">
                    <label for="inputAddress">Upload</label>
                    <span class="all-media-image-files">
                        <?php if (!empty(@$domain->image_logo)) { ?>
                            <img src="<?= @$domain->image_logo; ?>" width="100px">
                            <a href="/domains/deleteImage/<?= @$domain->uuid ?>" onclick="return confirm('Are you sure?')"
                                class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </span>
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

            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputPassword4">Notes</label>
                    <textarea class="form-control" name="notes"><?= @$domain->notes ?></textarea>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<!-- main content part end -->

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    var id = "<?= @$domain->uuid ?>";
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
            url: '/domains/uploadMediaFiles',
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

    $("#delete_file").on("click", function (e) {
        e.preventDefault();
        $(".all-media-image-files").html("");
    })

    function frmValidate() {
        var val = document.getElementById("name");
        if (/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/.test(val.value)) {
            $('#domain_error').html("");
        } else {
            $('#domain_error').html("Enter Valid Domain Name");
            document.getElementById("name").focus();
            return false;
        }
    }
</script>
<style>
    #domain_error {
        color: red;
        font-size: 14px;
    }