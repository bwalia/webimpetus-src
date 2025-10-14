<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<style>
    /* Timer Display */
    .timer-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 30px;
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }

    .timer-display .timer-value {
        font-size: 64px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: 3px;
        margin: 20px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .timer-display .timer-label {
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 0.9;
    }

    .timer-controls {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 25px;
    }

    .timer-btn {
        padding: 15px 35px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .timer-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .timer-btn:active {
        transform: translateY(0);
    }

    .timer-btn.start {
        background-color: #10b981;
        color: white;
    }

    .timer-btn.stop {
        background-color: #ef4444;
        color: white;
    }

    .timer-btn.reset {
        background-color: #6b7280;
        color: white;
    }

    .timer-btn i {
        font-size: 20px;
    }

    /* Running indicator */
    .running-indicator {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 20px;
        margin-top: 15px;
    }

    .running-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #10b981;
        animation: pulse-dot 1.5s ease-in-out infinite;
    }

    @keyframes pulse-dot {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.2);
        }
    }

    /* Form sections */
    .form-section {
        background: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
    }

    .form-section-title {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f3f4f6;
    }

    .quick-time-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .quick-time-btn {
        padding: 8px 16px;
        border-radius: 6px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .quick-time-btn:hover {
        background: #667eea;
        color: white;
    }

    .quick-time-btn.active {
        background: #667eea;
        color: white;
    }

    /* Status indicator */
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-indicator.draft {
        background: #f3f4f6;
        color: #6b7280;
    }

    .status-indicator.running {
        background: #dbeafe;
        color: #1e40af;
    }

    /* Calculation summary */
    .calculation-summary {
        background: #f9fafb;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }

    .calculation-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .calculation-row:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 18px;
        color: #667eea;
        padding-top: 15px;
    }

    .calculation-label {
        color: #6b7280;
    }

    .calculation-value {
        font-weight: 600;
        color: #374151;
        font-family: 'Courier New', monospace;
    }
</style>

<div class="white_card_body">
    <div class="card-body">
        <form id="timesheetForm" method="post" action="<?= "/" . $tableName . "/update" ?>" enctype="multipart/form-data">
            <input type="hidden" name="uuid" value="<?= @$timesheet->uuid ?>" />

            <!-- Timer Display (only show if new or running) -->
            <?php if (empty(@$timesheet->uuid) || @$timesheet->is_running == 1): ?>
            <div class="timer-display">
                <div class="timer-label">
                    <?= @$timesheet->is_running == 1 ? 'Timer Running' : 'Ready to Start' ?>
                </div>
                <div class="timer-value" id="timerDisplay">00:00:00</div>

                <?php if (@$timesheet->is_running == 1): ?>
                    <div class="running-indicator">
                        <div class="running-dot"></div>
                        <span>Started <?= date('g:i A', strtotime(@$timesheet->start_time)) ?></span>
                    </div>
                    <div class="timer-controls">
                        <button type="button" class="timer-btn stop" onclick="stopTimer()">
                            <i class="fa fa-stop-circle"></i> Stop Timer
                        </button>
                    </div>
                <?php else: ?>
                    <div class="timer-controls">
                        <button type="button" class="timer-btn start" onclick="startQuickTimer()">
                            <i class="fa fa-play-circle"></i> Start Timer
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fa fa-info-circle"></i> Timesheet Details</h3>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-control required select-employee-ajax" required>
                                <option value="">-- Type to search employees --</option>
                                <?php if (!empty($selected_employee)): ?>
                                    <option value="<?= $selected_employee['id'] ?>" selected>
                                        <?= $selected_employee['first_name'] ?> <?= $selected_employee['surname'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control select-customer-ajax">
                                <option value="">-- Type to search customers --</option>
                                <?php if (!empty($selected_customer)): ?>
                                    <option value="<?= $selected_customer['id'] ?>" selected>
                                        <?= $selected_customer['company_name'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Project</label>
                            <select name="project_id" id="project_id" class="form-control select-project-ajax">
                                <option value="">-- Type to search projects --</option>
                                <?php if (!empty($selected_project)): ?>
                                    <option value="<?= $selected_project['id'] ?>" selected>
                                        <?= $selected_project['name'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Task</label>
                            <select name="task_id" id="task_id" class="form-control select-task-ajax">
                                <option value="">-- Type to search tasks --</option>
                                <?php if (!empty($selected_task)): ?>
                                    <option value="<?= $selected_task['id'] ?>" selected>
                                        <?= $selected_task['name'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="What are you working on?"><?= @$timesheet->description ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Tracking -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fa fa-clock"></i> Time Tracking</h3>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label>Start Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control required"
                                   value="<?= !empty(@$timesheet->start_time) ? date('Y-m-d\TH:i', strtotime(@$timesheet->start_time)) : date('Y-m-d\TH:i') ?>"
                                   onchange="calculateDuration()" required />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                                   value="<?= !empty(@$timesheet->end_time) ? date('Y-m-d\TH:i', strtotime(@$timesheet->end_time)) : '' ?>"
                                   onchange="calculateDuration()" />
                        </div>
                    </div>
                </div>

                <!-- Quick Time Buttons -->
                <div class="row">
                    <div class="col-md-12">
                        <label>Quick Time Entry</label>
                        <div class="quick-time-buttons">
                            <button type="button" class="quick-time-btn" onclick="addQuickTime(15)">+15 min</button>
                            <button type="button" class="quick-time-btn" onclick="addQuickTime(30)">+30 min</button>
                            <button type="button" class="quick-time-btn" onclick="addQuickTime(60)">+1 hour</button>
                            <button type="button" class="quick-time-btn" onclick="addQuickTime(120)">+2 hours</button>
                            <button type="button" class="quick-time-btn" onclick="addQuickTime(240)">+4 hours</button>
                            <button type="button" class="quick-time-btn" onclick="setFullDay()">Full Day (8h)</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Information -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fa fa-pound-sign"></i> Billing Information</h3>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Hourly Rate (£)</label>
                            <input type="number" name="hourly_rate" id="hourly_rate" class="form-control" step="0.01"
                                   value="<?= @$timesheet->hourly_rate ?? '0.00' ?>" onchange="calculateTotal()" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_billable" id="is_billable" value="1"
                                       <?= (@$timesheet->is_billable ?? 1) == 1 ? 'checked' : '' ?> onchange="calculateTotal()" />
                                Billable
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="draft" <?= @$timesheet->status == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="running" <?= @$timesheet->status == 'running' ? 'selected' : '' ?>>Running</option>
                                <option value="stopped" <?= @$timesheet->status == 'stopped' ? 'selected' : '' ?>>Stopped</option>
                                <option value="completed" <?= @$timesheet->status == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Calculation Summary -->
                <div class="calculation-summary" id="calculationSummary">
                    <div class="calculation-row">
                        <span class="calculation-label">Duration:</span>
                        <span class="calculation-value" id="displayDuration">0h 0m</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Billable Hours:</span>
                        <span class="calculation-value" id="displayHours">0.00</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Hourly Rate:</span>
                        <span class="calculation-value">£<span id="displayRate">0.00</span></span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Total Amount:</span>
                        <span class="calculation-value">£<span id="displayTotal">0.00</span></span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="form-section">
                <h3 class="form-section-title"><i class="fa fa-sticky-note"></i> Additional Notes</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"><?= @$timesheet->notes ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <?php
                $submitButtonText = 'Save Timesheet';
                $submitButtonIcon = 'fa-save';
                $showCancelButton = true;
                $cancelUrl = '/timesheets';
                include(APPPATH . 'Views/common/submit-button.php');
            ?>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let timerInterval;
    let timerStartTime;
    let isRunning = <?= @$timesheet->is_running == 1 ? 'true' : 'false' ?>;

    // Initialize timer if running
    if (isRunning) {
        timerStartTime = new Date('<?= @$timesheet->start_time ?>').getTime();
        startTimerDisplay();
    }

    function startTimerDisplay() {
        timerInterval = setInterval(function() {
            const now = new Date().getTime();
            const elapsed = now - timerStartTime;

            const hours = Math.floor(elapsed / (1000 * 60 * 60));
            const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);

            document.getElementById('timerDisplay').textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }, 1000);
    }

    function stopTimer() {
        if (confirm('Stop the timer and save this timesheet?')) {
            const uuid = '<?= @$timesheet->uuid ?>';
            if (uuid) {
                fetch('/timesheets/stopTimer/' + uuid, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status) {
                        alert('Timer stopped successfully!');
                        window.location.href = '/timesheets';
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error stopping timer:', error);
                    alert('An error occurred while stopping the timer.');
                });
            }
        }
    }

    function startQuickTimer() {
        // Validate required fields
        const employee = document.getElementById('employee_id').value;
        if (!employee) {
            alert('Please select an employee first.');
            return;
        }

        // Set start time to now
        const now = new Date();
        document.getElementById('start_time').value = now.toISOString().slice(0, 16);
        document.getElementById('status').value = 'running';

        // Submit form
        document.getElementById('timesheetForm').submit();
    }

    function calculateDuration() {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;

        if (startTime && endTime) {
            const start = new Date(startTime);
            const end = new Date(endTime);
            const diff = end - start;

            if (diff > 0) {
                const minutes = Math.floor(diff / 60000);
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;

                document.getElementById('displayDuration').textContent = hours + 'h ' + mins + 'm';
                document.getElementById('displayHours').textContent = (minutes / 60).toFixed(2);

                calculateTotal();
            }
        }
    }

    function calculateTotal() {
        const hours = parseFloat(document.getElementById('displayHours').textContent) || 0;
        const rate = parseFloat(document.getElementById('hourly_rate').value) || 0;
        const isBillable = document.getElementById('is_billable').checked;

        document.getElementById('displayRate').textContent = rate.toFixed(2);

        if (isBillable) {
            const total = hours * rate;
            document.getElementById('displayTotal').textContent = total.toFixed(2);
        } else {
            document.getElementById('displayTotal').textContent = '0.00';
        }
    }

    function addQuickTime(minutes) {
        const startTime = document.getElementById('start_time').value;
        if (!startTime) {
            const now = new Date();
            document.getElementById('start_time').value = now.toISOString().slice(0, 16);
        }

        const start = new Date(document.getElementById('start_time').value);
        const end = new Date(start.getTime() + minutes * 60000);
        document.getElementById('end_time').value = end.toISOString().slice(0, 16);

        calculateDuration();
    }

    function setFullDay() {
        const startTime = document.getElementById('start_time').value;
        if (!startTime) {
            const now = new Date();
            now.setHours(9, 0, 0, 0);
            document.getElementById('start_time').value = now.toISOString().slice(0, 16);
        }

        const start = new Date(document.getElementById('start_time').value);
        const end = new Date(start);
        end.setHours(start.getHours() + 8);
        document.getElementById('end_time').value = end.toISOString().slice(0, 16);

        calculateDuration();
    }

    // Initialize calculations on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateDuration();
        calculateTotal();
    });

    // Initialize Select2 with AJAX for large datasets
    $(document).ready(function() {
        // Employee search
        $(".select-employee-ajax").select2({
            ajax: {
                url: "/common/searchEmployees",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.first_name + ' ' + item.surname + (item.job_title ? ' (' + item.job_title + ')' : ''),
                                id: item.id
                            }
                        })
                    };
                }
            },
            minimumInputLength: 2,
            placeholder: "Type to search employees",
            allowClear: false
        });

        // Customer search
        $(".select-customer-ajax").select2({
            ajax: {
                url: "/common/searchCustomers",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.company_name,
                                id: item.id
                            }
                        })
                    };
                }
            },
            minimumInputLength: 2,
            placeholder: "Type to search customers",
            allowClear: true
        });

        // Project search (filtered by customer when selected)
        $(".select-project-ajax").select2({
            ajax: {
                url: "/common/searchProjects",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        customer_id: $('#customer_id').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: (item.project_code ? '[' + item.project_code + '] ' : '') + item.name,
                                id: item.id
                            }
                        })
                    };
                }
            },
            minimumInputLength: 2,
            placeholder: "Type to search projects",
            allowClear: true
        });

        // Task search (filtered by project when selected)
        $(".select-task-ajax").select2({
            ajax: {
                url: "/common/searchTasks",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        project_id: $('#project_id').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name + (item.status ? ' [' + item.status + ']' : ''),
                                id: item.id
                            }
                        })
                    };
                }
            },
            minimumInputLength: 2,
            placeholder: "Type to search tasks",
            allowClear: true
        });

        // When customer changes, reset and refresh project dropdown
        $('#customer_id').on('change', function() {
            $('#project_id').val(null).trigger('change');
        });

        // When project changes, reset and refresh task dropdown
        $('#project_id').on('change', function() {
            $('#task_id').val(null).trigger('change');
        });
    });
</script>
