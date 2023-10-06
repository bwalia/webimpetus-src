<?php require_once(APPPATH . 'Views/kanban_board/list-title.php'); ?>

<div class="row flex-row flex-sm-nowrap py-3">



    <?php foreach ($tasks as $key => $values) { ?>
        <div class="col-sm-6 col-md-4 col-xl-3">


            <div class="card bg-light">
                <div class="card-body card-body-custom" data-category="<?= $key ?>">
                    <h6 class="card-title text-uppercase text-truncate py-2"><?= $key ?></h6>
                    <div class="items add-dropzone" data-category="<?= $key ?>">
                        <?php if (count($values)) { ?>
                            <?php foreach ($values as $row) { ?>
                                <div class="card draggable shadow-sm add-dropzone" data-id="<?= $row['id'] ?>" id="cd<?= $row['id'] ?>" draggable="true" ondragstart="drag(event)">
                                    <div class="card-body card-body-custom p-2">
                                        <div class="card-title">
                                            <a href="<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>" class="lead font-weight-light">TSK-<?= $row['task_id'] ?></a>
                                        </div>
                                        <p><?= $row['name'] ?></p>
                                        <div class="mt-3 pl-2 text-white <?= $row['priority'] == 'high' ? 'bg-danger' : ($row['priority'] == 'medium' ? 'bg-warning' : 'bg-info') ?>"><?= ucfirst($row['priority']) ?></div>
                                        <a href="<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>" class="btn btn-success btn-sm mt-3">View</a>
                                    </div>
                                </div>
                                <div class="dropzone rounded" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="clearDrop(event)"> &nbsp; </div>
                            <?php } ?>
                        <?php }  ?>
                        <div class="dropzone rounded" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="clearDrop(event)"> &nbsp; </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

</div>


<script>
    var task_id;
    var data_category;
    const drag = (event) => {
        event.dataTransfer.setData("text/plain", event.target.id);
        task_id = event.target.getAttribute("data-id");
    }

    const allowDrop = (ev) => {
        ev.preventDefault();
        if (hasClass(ev.target, "dropzone")) {
            addClass(ev.target, "droppable");
        }
    }

    const clearDrop = (ev) => {
        removeClass(ev.target, "droppable");
    }

    const drop = (event) => {
        event.preventDefault();
        const data = event.dataTransfer.getData("text/plain");
        const element = document.querySelector(`#${data}`);
        data_category = $(event.target).parent().attr("data-category");
        try {
            // remove the spacer content from dropzone
            event.target.removeChild(event.target.firstChild);
            // add the draggable content
            event.target.appendChild(element);
            // remove the dropzone parent
            unwrap(event.target);
        } catch (error) {
            console.warn("can't move the item to the same place")
        }
        updateDropzones();
    }

    const updateDropzones = () => {
        /* after dropping, refresh the drop target areas
          so there is a dropzone after each item
          using jQuery here for simplicity */

        var dz = $('<div class="dropzone rounded" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="clearDrop(event)"> &nbsp; </div>');

        // delete old dropzones
        $('.dropzone').remove();

        // insert new dropdzone after each item
        //dz.insertAfter('.card.draggable');
        dz.insertAfter('.add-dropzone');
        //$(".add-dropzone").prepend(dz);

        // insert new dropzone in any empty swimlanes
        //$(".items:not(:has(.card.draggable))").append(dz);

        // send ajax request and update database value of task table
        var form = new FormData();
        form.append("task_id", task_id);
        form.append("data_category", data_category);
        $.ajax({
            url: '/kanban_board/update_task',
            data: form,
            dataType: 'JSON',
            type: 'POST',
            success: function(result) {
                console.log(result);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    };

    function hasClass(target, className) {
        return new RegExp('(\\s|^)' + className + '(\\s|$)').test(target.className);
    }

    function addClass(ele, cls) {
        if (!hasClass(ele, cls)) ele.className += " " + cls;
    }

    function removeClass(ele, cls) {
        if (hasClass(ele, cls)) {
            var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
            ele.className = ele.className.replace(reg, ' ');
        }
    }

    function unwrap(node) {
        node.replaceWith(...node.childNodes);
    }
</script>


<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
    var base_url = '<?php echo base_url('/kanban_board') ?>';
    $(document).ready(function() {
        $("#kanban_sprint").on("change", function(e) {
            var redirect_to = base_url;
            if ($(this).val() != "") {
                redirect_to = base_url + "?sprint=" + $(this).val();
            }
            window.location.replace(redirect_to);
        });
    });
</script>