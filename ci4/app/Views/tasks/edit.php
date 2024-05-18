<?php

use App\Models\Customers_model;
use CodeIgniter\Database\BaseBuilder;

require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<?php
// print_r($customers);
// die();
// $projects = getResultArray("projects");
// $customers = (new Customers_model())
//     ->whereIn("id", function (BaseBuilder $subqueryBuilder) {
//         return $subqueryBuilder->select("customers_id")->from("projects")->groupBy("customers_id");
//     })
//     ->where("uuid_business_id", session('uuid_business'))
//     ->get()
//     ->getResultArray();
// $users = getResultArray("users");
// $contacts = getResultArray("contacts");
// $employees = getResultArray("employees");
// $sprints = getResultArray("sprints");
?>
<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?>
            enctype="multipart/form-data">
            <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$task->uuid ?>" />
            <div class="row">
                <div class="col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                                role="tab" aria-controls="nav-home" aria-selected="true">Main</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                                role="tab" aria-controls="nav-profile" aria-selected="false">Additional Info</a>
                        </div>
                    </nav>
                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="form-group required col-md-12">
                                    <label for="inputEmail4">Task Title </label>
                                    <input autocomplete="off" type="input" class="form-control required" id="name" name="name"
                                        placeholder="" value="<?= @$task->name ?>">
                                </div>
                                <div class="form-group   required col-md-12">
                                    <label for="description">Task Description</label>

                                    <textarea class="form-control" required name="description"
                                        id="content"><?= @$task->description ?></textarea>

                                </div>
                                <hr/>
                                <div class="row">
                                        <div class="form-group   required col-md-6">
                                            <label for="inputEmail4">Project Name </label>
                                            <select id="projects_id" name="projects_id" class="form-control required dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <?php foreach ($projects as $row): ?>
                                                    <option customer_id="<?= $row['customers_id']; ?>" value="<?= $row['id']; ?>" <?php if ($row['id'] == @$task->projects_id) {
                                                            echo "selected";
                                                        } ?>><?= $row['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group  required col-md-6">
                                            <label for="inputEmail4">Customer Name </label>
                                            <select id="customers_id" name="customers_id" class="form-control required select-customer-ajax">
                                                <option value="" selected="">--Select--</option>
                                                <?php foreach ($customers as $row): ?>
                                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$task->customers_id) {
                                                        echo "selected";
                                                    } ?>><?= $row['company_name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group  required col-md-6">
                                            <label for="inputEmail4">Contacts </label>
                                            <select id="contacts_id" name="contacts_id" class="form-control required dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <?php foreach ($contacts as $row): ?>
                                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$task->contacts_id) {
                                                        echo "selected";
                                                    } ?>><?= $row['first_name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group  required col-md-6">
                                            <label for="inputEmail4">Reported By </label>
                                            <select id="reported_by" name="reported_by" class="form-control required dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <?php foreach ($users as $row): ?>
                                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$task->reported_by) {
                                                        echo "selected";
                                                    } ?>><?= $row['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group required col-md-6 ">
                                            <label for="inputEmail4">Task Start Date</label>
                                            <input type="text" autocomplete="off" class="form-control required datepicker" id="start_date"
                                                name="start_date" placeholder="" value="<?= render_date(@$task->start_date) ?>">
                                        </div>
                                        <div class="form-group required col-md-6 ">
                                            <label for="inputEmail4">Task End Date</label>
                                            <input type="text" autocomplete="off" class="form-control required datepicker" id="end_date"
                                                name="end_date" placeholder="" value="<?= render_date(@$task->end_date) ?>">
                                            <span id="deadlineError" class="form-control-feedback"></span>
                                        </div>
                                        <div class="form-group required col-md-6 ">
                                            <label for="category">Category</label>
                                            <select name="category" class="form-control required dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <option value="todo" <?= ("todo" == @$task->category ? 'selected' : '') ?>>
                                                    Todo
                                                </option>
                                                <option value="in-progress" <?= ("in-progress" == @$task->category ? 'selected' : '') ?>>
                                                    In-progress
                                                </option>
                                                <option value="review" <?= ("review" == @$task->category ? 'selected' : '') ?>>
                                                    Review
                                                </option>
                                                <option value="done" <?= ("done" == @$task->category ? 'selected' : '') ?>>
                                                    Done
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group required  col-md-6 ">
                                            <label for="priority">Priority</label>
                                            <select name="priority" class="form-control required  dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <option value="low" <?= ("low" == @$task->priority ? 'selected' : '') ?>>
                                                    Low
                                                </option>
                                                <option value="medium" <?= ("medium" == @$task->priority ? 'selected' : '') ?>>
                                                    Medium
                                                </option>
                                                <option value="high" <?= ("high" == @$task->priority ? 'selected' : '') ?>>
                                                    High
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group  required col-md-6">
                                            <label for="sprint_id">Sprint </label>
                                            <select name="sprint_id" class="form-control required dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <?php foreach ($sprints as $row): ?>
                                                    <option value="<?= $row['id'] ?>" <?= ($row['id'] == @$task->sprint_id ? 'selected' : '') ?>>
                                                        <?= $row['sprint_name'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                </div>
                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row">
                                <div class="form-group  col-md-6">
                                    <label for="inputEmail4">Task ID </label>
                                    <input readonly autocomplete="off" type="input" class="form-control " id="task_id"
                                        name="task_id" placeholder="" value="<?= @$task->task_id ?>">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="inputEmail4"> Task Estimated Hour</label>
                                    <input type="number" class="form-control" id="estimated_hour" name="estimated_hour"
                                        placeholder="" value="<?= @$task->estimated_hour ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Assigned To </label>
                                    <select id="assigned_to" name="assigned_to" class="form-control dashboard-dropdown">
                                        <option value="" selected="">--Select--</option>
                                        <?php foreach ($employees as $row): ?>
                                            <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$task->assigned_to) {
                                                echo "selected";
                                            } ?>><?= $row['first_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group  col-md-6">
                                    <label for="inputEmail4"> Rate</label>
                                    <input type="number" class="form-control" id="rate" name="rate" placeholder=""
                                        value="<?= @$task->rate ?>">
                                </div>

                                <div class="form-group col-md-6 ">
                                    <label for="inputEmail4">Task Active</label>
                                    <select name="active" id="active" class="form-control dashboard-dropdown">
                                        <option value="1" <?php if (@$task->active == 1)
                                            echo "selected" ?>>Active</option>
                                            <option value="2" <?php if (@$task->active == 2)
                                            echo "selected" ?>>Completed</option>
                                    </select>
                                </div>

                                <div class="form-group  col-md-6 ">
                                    <label for="inputEmail4">Status</label>
                                    <select id="status" name="status" class="form-control  dashboard-dropdown">
                                        <option value="" selected="">--Select--</option>
                                        <option value="assigned" <?= ("assigned" == @$task->status ? 'selected' : '') ?>>
                                            Assigned
                                        </option>
                                        <option value="open" <?= ("open" == @$task->status ? 'selected' : '') ?>>
                                            Open
                                        </option>
                                        <option value="inReview" <?= ("inReview" == @$task->status ? 'selected' : '') ?>>
                                            In Review
                                        </option>
                                        <option value="completed" <?= ("completed" == @$task->status ? 'selected' : '') ?>>
                                            Completed
                                        </option>
                                        <option value="blocked" <?= ("blocked" == @$task->status ? 'selected' : '') ?>>
                                            Blocked
                                        </option>
                                    </select>
                                </div>
                                <?php if (!empty($task->id)) { ?>
                                    <div class="col-1212">

                                        <div class="form-group col-1212">
                                            <span class="">
                                                <?php if (!empty(@$media_list)) {

                                                    foreach ($media_list as $name) {

                                                        $tokens = explode('.', $name['name']);
                                                        $extension = $tokens[count($tokens) - 1];

                                                        $varray = ['webm', 'wmv', 'ogg', 'mp4', 'mov', 'flv', 'avi', 'mkv'];

                                                        if (in_array(trim($extension), $varray)) {
                                                            ?>
                                                            <video width="320" height="240" controls>
                                                                <?php foreach ($varray as $val) { ?>
                                                                    <source src="<?= @$name['name'] ?>" type=video/<?= $val ?>>
                                                                <?php } ?>
                                                            </video>

                                                            <a href="/gallery/delete_task/<?= $name['id'] ?>/<?= base64_encode('/tasks/editrow/' . @$task->uuid) ?>"
                                                                onclick="return confirm('Are you sure?')" id="" class="btn btn-danger"><i
                                                                    class="fa fa-trash"></i></a>

                                                            <br>

                                                        <?php } else { ?>
                                                            <img src="<?= @$name['name'] ?>" width="140px">
                                                            <a href="/gallery/delete_task/<?= $name['id'] ?>/<?= base64_encode('/tasks/editrow/' . @$task->uuid) ?>"
                                                                onclick="return confirm('Are you sure?')" id="" class="btn btn-danger"><i
                                                                    class="fa fa-trash"></i></a>

                                                            <br>
                                                        <?php }
                                                    }
                                                } ?>
                                            </span>
                                        </div>


                                        <div class="form-group col-1212">
                                            <span class="">
                                                <?php if (!empty(@$documents)) {

                                                    foreach ($documents as $doc) { ?>
                                                        <iframe
                                                            src="https://drive.google.com/viewerng/viewer?embedded=true&url=<?= @$doc['file'] ?>"
                                                            width="560" height="700"></iframe>
                                                        <a href="/documents/delete_task/<?= $doc['id'] ?>/<?= base64_encode('/tasks/editrow/' . @$task->uuid) ?>"
                                                            onclick="return confirm('Are you sure?')" id="" class="btn btn-danger"><i
                                                                class="fa fa-trash"></i></a>
                                                        <br>
                                                        <?php
                                                    }
                                                } ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-12">
                                    <div class="form-group col-12 imageUploadForm">
                                        <label for="inputAddress">Upload</label>
                                        <span class="all-media-image-files">

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
                                                    <p class="image-name"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->

<script>
    var id = "<?= @$task->id ?>";
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

    $(":submit").click(function (event) {
        const startDate = $("#start_date").val();
        const deadLineDate = $("#end_date").val();
        validateEndDate(startDate, deadLineDate, event)
        validateName($("#name"), event);
    });

    $(document).on("change", ".fileUpload", function () {
        console.log({
            "heerr": $(this)[0].files
        });
        $(".image-name").text($(this)[0].files[0].name)
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
            url: '/tasks/uploadMediaFiles',
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

    $(document).on("change", "#projects_id", function () {
        var customerId = $('option:selected', this).attr('customer_id');
        $("#customers_id").val(customerId);
        $("#customers_id").trigger("change");
    })
    $(document).on("click", ".form-check-input", function () {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });

    $("#end_date").change(function () {
        const startDate = $("#start_date").val();
        const deadLineDate = $(this).val();
        
        validateEndDate(startDate, deadLineDate, null)
    })

    function validateEndDate(slipStartDate, slipEndDate, evt) {
        const endDate = new Date(slipEndDate);
        const startDate = new Date(slipStartDate);
        const timeDifference = endDate - startDate;
        const daysDifference = Math.round(timeDifference / (1000 * 60 * 60 * 24));
        console.log({timeDifference, daysDifference});
        if (daysDifference < 0) {
            $("#deadlineError").text("Deadline date should be greater than the start date.");
            if (evt !== null) {
                evt.preventDefault();
            }
            return false;
        } else {
            $("#deadlineError").text("");
        }
    }

    $(document).ready(function() {
        $(".select-customer-ajax").select2({
            ajax: {
                url: "/tasks/companyCustomerAjax",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.company_name,
                                id: item.id
                            }
                        })
                    };
                },
            },
            minimumInputLength: 2
        })
    });
</script>