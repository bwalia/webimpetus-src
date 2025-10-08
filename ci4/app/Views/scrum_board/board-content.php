<div class="row no-gutters">
    <?php 
    $columnTitles = [
        'backlog' => '📦 Backlog',
        'sprint-ready' => '✅ Sprint Ready', 
        'in-sprint' => '🏃 In Sprint',
        'completed' => '🎉 Completed'
    ];
    
    foreach ($tasks as $key => $values) { 
        $taskCount = count($values);
    ?>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="scrum-column" data-category="<?= $key ?>">
                <div class="scrum-column-header">
                    <?= $columnTitles[$key] ?? ucfirst(str_replace('-', ' ', $key)) ?>
                    <span class="task-count"><?= $taskCount ?></span>
                </div>
                
                <div class="scrum-column-content">
                    <div class="items add-dropzone" data-category="<?= $key ?>">
                        <?php if (count($values)) { ?>
                            <?php foreach ($values as $row) { ?>
                                <div class="scrum-task draggable shadow-sm add-dropzone" 
                                     data-id="<?= $row['id'] ?>"
                                     id="cd<?= $row['id'] ?>" 
                                     draggable="true" 
                                     ondragstart="drag(event)">
                                    <div class="scrum-task-content" onclick="gotoTask('<?=$row['uuid']?>')">
                                        <div class="scrum-task-id">
                                            TSK-<?= $row['task_id'] ?>
                                        </div>
                                        
                                        <?php if (!empty($row['story_points'])) { ?>
                                            <div class="story-points">
                                                <?= $row['story_points'] ?> pts
                                            </div>
                                        <?php } ?>
                                        
                                        <div class="scrum-task-title">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </div>
                                        
                                        <div class="scrum-task-meta">
                                            <div class="scrum-priority <?= strtolower($row['priority']) ?>">
                                                <?= ucfirst($row['priority']) ?>
                                            </div>
                                            
                                            <?php if (!empty($row['sprint_name'])) { ?>
                                                <div class="scrum-sprint">
                                                    🎯 <?= htmlspecialchars($row['sprint_name']) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="dropzone" data-category="<?= $key ?>">
                                <p class="text-muted small">No tasks in this column</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
        ev.target.classList.add('dragging');
    }

    function allowDrop(ev) {
        ev.preventDefault();
        const dropzone = ev.target.closest('.add-dropzone');
        if (dropzone && !dropzone.classList.contains('droppable')) {
            dropzone.classList.add('droppable');
        }
    }

    function drop(ev) {
        ev.preventDefault();
        const dropzone = ev.target.closest('.add-dropzone');
        if (!dropzone) return;
        
        dropzone.classList.remove('droppable');
        
        var data = ev.dataTransfer.getData("text");
        const draggedElement = document.getElementById(data);
        
        if (!draggedElement) return;
        
        const data_category = dropzone.dataset.category;
        const task_id = draggedElement.dataset.id;
        
        // Animated move
        draggedElement.style.opacity = '0.5';
        
        // Update on server
        $.ajax({
            url: '/scrum_board/update_task',
            method: 'POST',
            data: {
                task_id: task_id,
                data_category: data_category
            },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status) {
                    // Animate and move element
                    setTimeout(() => {
                        dropzone.appendChild(draggedElement);
                        draggedElement.style.opacity = '1';
                        draggedElement.classList.remove('dragging');
                        
                        // Update task counts
                        updateTaskCounts();
                        
                        // Show success notification
                        showNotification('✅ Task moved successfully!', 'success');
                    }, 300);
                } else {
                    draggedElement.style.opacity = '1';
                    showNotification('❌ Error moving task', 'error');
                }
            },
            error: function() {
                draggedElement.style.opacity = '1';
                showNotification('❌ Network error', 'error');
            }
        });
    }

    function updateTaskCounts() {
        document.querySelectorAll('.scrum-column').forEach(column => {
            const count = column.querySelectorAll('.scrum-task').length;
            const badge = column.querySelector('.task-count');
            if (badge) {
                badge.textContent = count;
            }
        });
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} notification-toast`;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        `;
        notification.innerHTML = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Initialize drag and drop
    document.addEventListener('DOMContentLoaded', function() {
        const dropzones = document.querySelectorAll('.add-dropzone');
        dropzones.forEach(zone => {
            zone.addEventListener('dragover', allowDrop);
            zone.addEventListener('drop', drop);
            zone.addEventListener('dragleave', function(e) {
                if (e.target.classList.contains('add-dropzone')) {
                    e.target.classList.remove('droppable');
                }
            });
        });
    });

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        .dragging {
            opacity: 0.5;
            transform: rotate(5deg);
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
