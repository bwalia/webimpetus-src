<?php require_once(APPPATH . 'Views/kanban_board/list-title.php'); ?>

<div class="row no-gutters">
    <?php 
    $columnTitles = [
        'todo' => '📋 To Do',
        'in-progress' => '🚀 In Progress', 
        'review' => '👀 Review',
        'done' => '✅ Done'
    ];
    
    foreach ($tasks as $key => $values) { 
        $taskCount = count($values);
    ?>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="kanban-column" data-category="<?= $key ?>">
                <div class="kanban-column-header">
                    <?= $columnTitles[$key] ?? ucfirst(str_replace('-', ' ', $key)) ?>
                    <span class="task-count"><?= $taskCount ?></span>
                </div>
                
                <div class="kanban-column-content">
                    <div class="items add-dropzone" data-category="<?= $key ?>">
                        <?php if (count($values)) { ?>
                            <?php foreach ($values as $row) { ?>
                                <div class="kanban-task draggable shadow-sm add-dropzone" 
                                     data-id="<?= $row['id'] ?>"
                                     id="cd<?= $row['id'] ?>" 
                                     draggable="true" 
                                     ondragstart="drag(event)">
                                    <div class="kanban-task-content" onclick="gotoTask('<?=$row['uuid']?>')">
                                        <div class="kanban-task-id">
                                            TSK-<?= $row['task_id'] ?>
                                        </div>
                                        
                                        <div class="kanban-task-title">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </div>
                                        
                                        <div class="kanban-task-meta">
                                            <div class="kanban-priority <?= strtolower($row['priority']) ?>">
                                                <?= ucfirst($row['priority']) ?>
                                            </div>
                                            
                                            <?php if (!empty($row['project_name'])) { ?>
                                                <div class="kanban-project">
                                                    📁 <?= htmlspecialchars($row['project_name']) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropzone rounded" 
                                     ondrop="drop(event)" 
                                     ondragover="allowDrop(event)"
                                     ondragleave="clearDrop(event)"></div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center py-4" style="color: #adb5bd;">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No tasks yet</p>
                                <small>Drag tasks here to get started</small>
                            </div>
                        <?php } ?>
                        <div class="dropzone rounded" 
                             ondrop="drop(event)" 
                             ondragover="allowDrop(event)"
                             ondragleave="clearDrop(event)"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

</div>


<script>
    var task_id;
    var data_category;
    
    const drag = (event) => {
        event.dataTransfer.setData("text/plain", event.target.id);
        task_id = event.target.getAttribute("data-id");
        
        // Add visual feedback for dragging
        event.target.style.opacity = '0.5';
        event.target.style.transform = 'rotate(5deg) scale(1.05)';
        
        // Highlight all drop zones
        document.querySelectorAll('.dropzone').forEach(zone => {
            zone.style.borderColor = '#667eea';
            zone.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
        });
    }

    const allowDrop = (ev) => {
        ev.preventDefault();
        if (hasClass(ev.target, "dropzone")) {
            addClass(ev.target, "droppable");
            ev.target.style.borderColor = '#667eea';
            ev.target.style.backgroundColor = 'rgba(102, 126, 234, 0.2)';
            ev.target.style.transform = 'scale(1.02)';
        }
    }

    const clearDrop = (ev) => {
        removeClass(ev.target, "droppable");
        ev.target.style.borderColor = 'transparent';
        ev.target.style.backgroundColor = 'transparent';
        ev.target.style.transform = 'scale(1)';
    }

    const drop = (event) => {
        event.preventDefault();
        const data = event.dataTransfer.getData("text/plain");
        const element = document.querySelector(`#${data}`);
        data_category = $(event.target).parent().attr("data-category");
        
        // Reset dragged element styles
        element.style.opacity = '1';
        element.style.transform = '';
        
        // Reset all drop zones
        document.querySelectorAll('.dropzone').forEach(zone => {
            zone.style.borderColor = 'transparent';
            zone.style.backgroundColor = 'transparent';
            zone.style.transform = 'scale(1)';
        });
        
        try {
            // Add success animation
            element.style.animation = 'taskDrop 0.3s ease';
            
            // remove the spacer content from dropzone
            event.target.removeChild(event.target.firstChild);
            // add the draggable content
            event.target.appendChild(element);
            // remove the dropzone parent
            unwrap(event.target);
            
            // Update task count badges
            updateTaskCounts();
            
        } catch (error) {
            console.warn("can't move the item to the same place")
            // Reset styles on error
            element.style.opacity = '1';
            element.style.transform = '';
        }
        updateDropzones();
    }

    const updateTaskCounts = () => {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const category = column.getAttribute('data-category');
            const taskCount = column.querySelectorAll('.kanban-task').length;
            const countBadge = column.querySelector('.task-count');
            if (countBadge) {
                countBadge.textContent = taskCount;
                // Add a subtle animation
                countBadge.style.animation = 'pulse 0.3s ease';
            }
        });
    }

    const updateDropzones = () => {
        /* after dropping, refresh the drop target areas
          so there is a dropzone after each item
          using jQuery here for simplicity */

        var dz = $('<div class="dropzone rounded" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="clearDrop(event)"></div>');

        // delete old dropzones
        $('.dropzone').remove();

        // insert new dropdzone after each item
        dz.insertAfter('.add-dropzone');

        // send ajax request and update database value of task table
        var form = new FormData();
        form.append("task_id", task_id);
        form.append("data_category", data_category);
        
        // Show loading state
        showNotification('Updating task...', 'info');
        
        $.ajax({
            url: '/kanban_board/update_task',
            data: form,
            dataType: 'JSON',
            type: 'POST',
            success: function (result) {
                console.log(result);
                showNotification('Task updated successfully!', 'success');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                showNotification('Error updating task. Please try again.', 'error');
            },
            cache: false,
            contentType: false,
            processData: false
        });
    };

    const showNotification = (message, type) => {
        // Create a simple notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn 0.3s ease;';
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                ${message}
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
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

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes taskDrop {
            0% { transform: scale(1.1); }
            50% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
</script>


<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
    var base_url = '<?php echo base_url('/kanban_board') ?>';
    $(document).ready(function () {
        $("#kanban_sprint").on("change", function (e) {
            var redirect_to = base_url;
            if ($(this).val() != "") {
                redirect_to = base_url + "?sprint=" + $(this).val();
            }
            window.location.replace(redirect_to);
        });
    });

    function gotoTask(task_id) {
        window.location.href = '<?= "/" . $tableName . "/edit/" ?>'+task_id
    }
    
    // Ensure kanban container responds to sidebar toggle
    $(document).ready(function() {
        // Listen for sidebar toggle button clicks
        $(".open_miniSide").on("click", function() {
            console.log("Sidebar toggled - Kanban should respond");
            // Force refresh of kanban container classes
            setTimeout(function() {
                if ($(".sidebar").hasClass("mini_sidebar")) {
                    $(".kanban-container").addClass("full_main_content");
                    console.log("Sidebar collapsed - Kanban adjusted to 70px padding");
                } else {
                    $(".kanban-container").removeClass("full_main_content");
                    console.log("Sidebar expanded - Kanban adjusted to 270px padding");
                }
            }, 50);
        });
        
        // Check initial state on page load
        if ($(".sidebar").hasClass("mini_sidebar")) {
            $(".kanban-container").addClass("full_main_content");
            console.log("Initial state: Sidebar collapsed");
        } else {
            console.log("Initial state: Sidebar expanded");
        }
    });
</script>