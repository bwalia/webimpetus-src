<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<div class="main_content_iner">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Job Scheduler Calendar</h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">

                        <!-- Filter Controls -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="filter_project">Filter by Project</label>
                                <select id="filter_project" class="form-control">
                                    <option value="">All Projects</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_job">Filter by Job</label>
                                <select id="filter_job" class="form-control">
                                    <option value="">All Jobs</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_user">Filter by User</label>
                                <select id="filter_user" class="form-control">
                                    <option value="">All Users</option>
                                    <?php if (isset($users)): foreach ($users as $user): ?>
                                        <option value="<?= is_object($user) ? $user->id : $user['id'] ?>">
                                            <?= is_object($user) ? $user->name : $user['name'] ?>
                                        </option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_employee">Filter by Employee</label>
                                <select id="filter_employee" class="form-control">
                                    <option value="">All Employees</option>
                                    <?php if (isset($employees)): foreach ($employees as $employee): ?>
                                        <option value="<?= is_object($employee) ? $employee->id : $employee['id'] ?>">
                                            <?= is_object($employee) ? ($employee->first_name . ' ' . $employee->surname) : ($employee['first_name'] . ' ' . $employee['surname']) ?>
                                        </option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mb-3">
                            <button id="btn_new_event" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Schedule New Event
                            </button>
                            <a href="/project_jobs" class="btn btn-secondary">
                                <i class="fa fa-list"></i> View Jobs List
                            </a>
                        </div>

                        <!-- Calendar Container -->
                        <div id="calendar"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Schedule Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="event_uuid" name="uuid">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="event_job">Job *</label>
                            <select id="event_job" name="uuid_project_job_id" class="form-control" required>
                                <option value="">--Select Job--</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="event_phase">Phase (Optional)</label>
                            <select id="event_phase" name="uuid_phase_id" class="form-control">
                                <option value="">--Select Phase--</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="event_title">Title *</label>
                            <input type="text" id="event_title" name="title" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="event_date">Date *</label>
                            <input type="date" id="event_date" name="schedule_date" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="event_all_day">
                                <input type="checkbox" id="event_all_day" name="all_day" value="1"> All Day Event
                            </label>
                        </div>
                    </div>

                    <div class="form-row" id="time_fields">
                        <div class="form-group col-md-6">
                            <label for="event_start_time">Start Time</label>
                            <input type="time" id="event_start_time" name="start_time" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="event_end_time">End Time</label>
                            <input type="time" id="event_end_time" name="end_time" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="event_assigned_user">Assign to User</label>
                            <select id="event_assigned_user" name="assigned_to_user_id" class="form-control">
                                <option value="">--Unassigned--</option>
                                <?php if (isset($users)): foreach ($users as $user): ?>
                                    <option value="<?= is_object($user) ? $user->id : $user['id'] ?>">
                                        <?= is_object($user) ? $user->name : $user['name'] ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="event_assigned_employee">Assign to Employee</label>
                            <select id="event_assigned_employee" name="assigned_to_employee_id" class="form-control">
                                <option value="">--Unassigned--</option>
                                <?php if (isset($employees)): foreach ($employees as $employee): ?>
                                    <option value="<?= is_object($employee) ? $employee->id : $employee['id'] ?>">
                                        <?= is_object($employee) ? ($employee->first_name . ' ' . $employee->surname) : ($employee['first_name'] . ' ' . $employee['surname']) ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="event_color">Color</label>
                            <input type="color" id="event_color" name="color" class="form-control" value="#667eea">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="event_status">Status</label>
                            <select id="event_status" name="status" class="form-control">
                                <option value="Scheduled">Scheduled</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="event_notes">Notes</label>
                            <textarea id="event_notes" name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="btn_delete_event" class="btn btn-danger" style="display:none;">Delete</button>
                <button type="button" id="btn_save_event" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<!-- FullCalendar CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
let calendar;
let currentFilters = {
    project_uuid: null,
    job_uuid: null,
    user_id: null,
    employee_id: null
};

$(document).ready(function() {
    // Initialize Select2 for filters
    $("#filter_project").select2({
        ajax: {
            url: "/common/searchProjects",
            dataType: 'json',
            delay: 250,
            data: function(params) { return { q: params.term || '' }; },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return { id: item.uuid, text: item.name }
                    })
                };
            }
        },
        placeholder: 'All Projects',
        allowClear: true
    });

    $("#filter_job").select2({
        ajax: {
            url: "/searchProjectJobs",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || '',
                    project_uuid: currentFilters.project_uuid
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return { id: item.uuid, text: item.job_number + ' - ' + item.job_name }
                    })
                };
            }
        },
        placeholder: 'All Jobs',
        allowClear: true
    });

    // Initialize Select2 for event modal
    $("#event_job").select2({
        dropdownParent: $('#eventModal'),
        ajax: {
            url: "/searchProjectJobs",
            dataType: 'json',
            delay: 250,
            data: function(params) { return { q: params.term || '' }; },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return { id: item.uuid, text: item.job_number + ' - ' + item.job_name }
                    })
                };
            }
        },
        placeholder: '--Select Job--'
    });

    // When job is selected, load its phases
    $("#event_job").on('change', function() {
        const jobUuid = $(this).val();
        if (jobUuid) {
            loadPhases(jobUuid);
        } else {
            $("#event_phase").empty().append('<option value="">--Select Phase--</option>');
        }
    });

    // Toggle time fields based on all-day checkbox
    $("#event_all_day").on('change', function() {
        if ($(this).is(':checked')) {
            $("#time_fields").hide();
            $("#event_start_time, #event_end_time").prop('required', false);
        } else {
            $("#time_fields").show();
            $("#event_start_time, #event_end_time").prop('required', true);
        }
    });

    // Filter change handlers
    $("#filter_project, #filter_job, #filter_user, #filter_employee").on('change', function() {
        currentFilters.project_uuid = $("#filter_project").val();
        currentFilters.job_uuid = $("#filter_job").val();
        currentFilters.user_id = $("#filter_user").val();
        currentFilters.employee_id = $("#filter_employee").val();
        calendar.refetchEvents();
    });

    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        editable: true,
        droppable: true,
        events: function(info, successCallback, failureCallback) {
            $.ajax({
                url: '/project_job_scheduler/getEvents',
                data: {
                    start: info.startStr,
                    end: info.endStr,
                    ...currentFilters
                },
                success: function(data) {
                    successCallback(data.data || []);
                },
                error: function() {
                    failureCallback();
                }
            });
        },
        eventClick: function(info) {
            openEventModal(info.event);
        },
        dateClick: function(info) {
            openEventModal(null, info.dateStr);
        },
        eventDrop: function(info) {
            handleEventDrop(info.event, info.delta);
        }
    });

    calendar.render();

    // New event button
    $("#btn_new_event").click(function() {
        openEventModal();
    });

    // Save event button
    $("#btn_save_event").click(function() {
        saveEvent();
    });

    // Delete event button
    $("#btn_delete_event").click(function() {
        if (confirm('Delete this scheduled event?')) {
            deleteEvent();
        }
    });
});

function loadPhases(jobUuid) {
    $.ajax({
        url: '/searchProjectJobPhases',
        data: { job_uuid: jobUuid },
        success: function(data) {
            const $select = $("#event_phase");
            $select.empty().append('<option value="">--Select Phase--</option>');
            data.forEach(function(phase) {
                $select.append(`<option value="${phase.uuid}">${phase.phase_number} - ${phase.phase_name}</option>`);
            });
        }
    });
}

function openEventModal(event = null, dateStr = null) {
    $("#eventForm")[0].reset();
    $("#event_phase").empty().append('<option value="">--Select Phase--</option>');

    if (event) {
        // Edit existing event
        $("#eventModalTitle").text('Edit Scheduled Event');
        $("#btn_delete_event").show();
        $("#event_uuid").val(event.id);
        $("#event_title").val(event.title);
        $("#event_date").val(event.startStr.split('T')[0]);
        $("#event_color").val(event.backgroundColor);
        $("#event_status").val(event.extendedProps.status || 'Scheduled');
        $("#event_notes").val(event.extendedProps.notes || '');
        $("#event_all_day").prop('checked', event.allDay);

        if (!event.allDay && event.startStr.includes('T')) {
            const startTime = event.startStr.split('T')[1].substring(0, 5);
            const endTime = event.endStr ? event.endStr.split('T')[1].substring(0, 5) : '';
            $("#event_start_time").val(startTime);
            $("#event_end_time").val(endTime);
            $("#time_fields").show();
        } else {
            $("#time_fields").hide();
        }

        // Set job and trigger phase load
        if (event.extendedProps.uuid_project_job_id) {
            const jobOption = new Option(event.extendedProps.job_name, event.extendedProps.uuid_project_job_id, true, true);
            $("#event_job").append(jobOption).trigger('change');
        }
    } else {
        // New event
        $("#eventModalTitle").text('Schedule New Event');
        $("#btn_delete_event").hide();
        if (dateStr) {
            $("#event_date").val(dateStr);
        }
    }

    $("#eventModal").modal('show');
}

function saveEvent() {
    const formData = new FormData(document.getElementById('eventForm'));
    const data = Object.fromEntries(formData.entries());

    $.ajax({
        url: '/project_job_scheduler/createEvent',
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.status) {
                $("#eventModal").modal('hide');
                calendar.refetchEvents();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to save event');
        }
    });
}

function deleteEvent() {
    const uuid = $("#event_uuid").val();
    $.ajax({
        url: '/project_job_scheduler/deleteEvent/' + uuid,
        method: 'POST',
        success: function(response) {
            if (response.status) {
                $("#eventModal").modal('hide');
                calendar.refetchEvents();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function handleEventDrop(event, delta) {
    const newDate = event.startStr.split('T')[0];
    $.ajax({
        url: '/project_job_scheduler/dragDrop',
        method: 'POST',
        data: {
            uuid: event.id,
            new_date: newDate
        },
        success: function(response) {
            if (!response.status) {
                alert('Failed to update event');
                calendar.refetchEvents();
            }
        },
        error: function() {
            alert('Error updating event');
            calendar.refetchEvents();
        }
    });
}
</script>
