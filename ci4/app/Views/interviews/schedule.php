<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    .main_content_iner {
        background: #f4f5f7 !important;
    }

    .schedule-container {
        max-width: 1200px;
        margin: 0 auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .form-section {
        margin-bottom: 32px;
        padding-bottom: 32px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #667eea;
        font-size: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-label.required::after {
        content: ' *';
        color: #ef4444;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control:disabled {
        background: #f1f5f9;
        cursor: not-allowed;
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 20px;
        padding-right: 40px;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .platform-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
    }

    .platform-option {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }

    .platform-option:hover {
        border-color: #667eea;
        background: #f8f9fe;
    }

    .platform-option.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }

    .platform-option input[type="radio"] {
        display: none;
    }

    .platform-icon {
        font-size: 32px;
        margin-bottom: 8px;
    }

    .platform-name {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 14px 32px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        text-decoration: none;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 32px;
    }

    .help-text {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }

    .conditional-field {
        display: none;
    }

    .conditional-field.show {
        display: block;
    }
</style>

<div class="schedule-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">📅 <?= isset($interview) ? 'Edit Interview' : 'Schedule New Interview' ?></h1>
        <a href="/interviews" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Interviews
        </a>
    </div>

            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-<?= session()->getFlashdata('alert-class') ?>">
                    <?= session()->getFlashdata('message') ?>
                </div>
            <?php endif; ?>

            <!-- Schedule Form -->
            <form action="/interviews/save" method="POST" id="scheduleForm">
                <input type="hidden" name="uuid" value="<?= $interview->uuid ?? '' ?>">

                <div class="form-card">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Interview Title</label>
                                <input type="text" name="interview_title" class="form-control"
                                       value="<?= $interview->interview_title ?? '' ?>"
                                       placeholder="e.g., Technical Interview - Round 1" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Job Position</label>
                                <select name="job_id" class="form-control form-select">
                                    <option value="">Select Job (Optional)</option>
                                    <?php foreach ($jobs as $job): ?>
                                        <option value="<?= $job['id'] ?>"
                                                <?= (isset($interview) && $interview->job_id == $job['id']) ? 'selected' : '' ?>>
                                            <?= $job['title'] ?> (<?= $job['reference_number'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Interview Type</label>
                                <select name="interview_type" class="form-control form-select" required>
                                    <option value="video" <?= (isset($interview) && $interview->interview_type == 'video') ? 'selected' : '' ?>>Video Interview</option>
                                    <option value="phone-screening" <?= (isset($interview) && $interview->interview_type == 'phone-screening') ? 'selected' : '' ?>>Phone Screening</option>
                                    <option value="in-person" <?= (isset($interview) && $interview->interview_type == 'in-person') ? 'selected' : '' ?>>In-Person</option>
                                    <option value="technical" <?= (isset($interview) && $interview->interview_type == 'technical') ? 'selected' : '' ?>>Technical Assessment</option>
                                    <option value="panel" <?= (isset($interview) && $interview->interview_type == 'panel') ? 'selected' : '' ?>>Panel Interview</option>
                                    <option value="final" <?= (isset($interview) && $interview->interview_type == 'final') ? 'selected' : '' ?>>Final Interview</option>
                                    <option value="group" <?= (isset($interview) && $interview->interview_type == 'group') ? 'selected' : '' ?>>Group Interview</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Interview Round</label>
                                <input type="number" name="interview_round" class="form-control"
                                       value="<?= $interview->interview_round ?? '1' ?>" min="1" max="10">
                                <div class="help-text">1st round, 2nd round, final round, etc.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduling -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Date & Time
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Date</label>
                                <input type="date" name="scheduled_date" class="form-control"
                                       value="<?= $interview->scheduled_date ?? '' ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Time</label>
                                <input type="time" name="scheduled_time" class="form-control"
                                       value="<?= $interview->scheduled_time ?? '' ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control"
                                       value="<?= $interview->duration_minutes ?? '60' ?>" min="15" step="15">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-control form-select">
                                    <option value="Europe/London" <?= (isset($interview) && $interview->timezone == 'Europe/London') ? 'selected' : '' ?>>London (GMT)</option>
                                    <option value="America/New_York" <?= (isset($interview) && $interview->timezone == 'America/New_York') ? 'selected' : '' ?>>New York (EST)</option>
                                    <option value="America/Los_Angeles" <?= (isset($interview) && $interview->timezone == 'America/Los_Angeles') ? 'selected' : '' ?>>Los Angeles (PST)</option>
                                    <option value="Asia/Dubai" <?= (isset($interview) && $interview->timezone == 'Asia/Dubai') ? 'selected' : '' ?>>Dubai (GST)</option>
                                    <option value="Asia/Kolkata" <?= (isset($interview) && $interview->timezone == 'Asia/Kolkata') ? 'selected' : '' ?>>India (IST)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Meeting Platform -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-video"></i>
                            Meeting Platform
                        </h3>

                        <div class="platform-selector">
                            <label class="platform-option <?= (isset($interview) && $interview->platform == 'google-meet') || !isset($interview) ? 'selected' : '' ?>">
                                <input type="radio" name="platform" value="google-meet"
                                       <?= (isset($interview) && $interview->platform == 'google-meet') || !isset($interview) ? 'checked' : '' ?>>
                                <div class="platform-icon">📹</div>
                                <div class="platform-name">Google Meet</div>
                            </label>

                            <label class="platform-option <?= (isset($interview) && $interview->platform == 'zoom') ? 'selected' : '' ?>">
                                <input type="radio" name="platform" value="zoom"
                                       <?= (isset($interview) && $interview->platform == 'zoom') ? 'checked' : '' ?>>
                                <div class="platform-icon">💻</div>
                                <div class="platform-name">Zoom</div>
                            </label>

                            <label class="platform-option <?= (isset($interview) && $interview->platform == 'microsoft-teams') ? 'selected' : '' ?>">
                                <input type="radio" name="platform" value="microsoft-teams"
                                       <?= (isset($interview) && $interview->platform == 'microsoft-teams') ? 'checked' : '' ?>>
                                <div class="platform-icon">🎥</div>
                                <div class="platform-name">MS Teams</div>
                            </label>

                            <label class="platform-option <?= (isset($interview) && $interview->platform == 'in-person') ? 'selected' : '' ?>">
                                <input type="radio" name="platform" value="in-person"
                                       <?= (isset($interview) && $interview->platform == 'in-person') ? 'checked' : '' ?>>
                                <div class="platform-icon">🏢</div>
                                <div class="platform-name">In-Person</div>
                            </label>

                            <label class="platform-option <?= (isset($interview) && $interview->platform == 'phone') ? 'selected' : '' ?>">
                                <input type="radio" name="platform" value="phone"
                                       <?= (isset($interview) && $interview->platform == 'phone') ? 'checked' : '' ?>>
                                <div class="platform-icon">📞</div>
                                <div class="platform-name">Phone</div>
                            </label>
                        </div>

                        <div class="form-row" style="margin-top: 20px;" id="onlineMeetingFields">
                            <div class="form-group">
                                <label class="form-label">Meeting Link</label>
                                <input type="url" name="meeting_link" class="form-control"
                                       value="<?= $interview->meeting_link ?? '' ?>"
                                       placeholder="https://meet.google.com/xxx-xxxx-xxx">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Meeting ID (Optional)</label>
                                <input type="text" name="meeting_id" class="form-control"
                                       value="<?= $interview->meeting_id ?? '' ?>"
                                       placeholder="123 456 7890">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Meeting Password (Optional)</label>
                                <input type="text" name="meeting_password" class="form-control"
                                       value="<?= $interview->meeting_password ?? '' ?>">
                            </div>
                        </div>

                        <div class="form-row" style="margin-top: 20px; display: none;" id="inPersonFields">
                            <div class="form-group">
                                <label class="form-label">Address</label>
                                <input type="text" name="location_address" class="form-control"
                                       value="<?= $interview->location_address ?? '' ?>"
                                       placeholder="123 Main Street, London">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Room/Building</label>
                                <input type="text" name="location_room" class="form-control"
                                       value="<?= $interview->location_room ?? '' ?>"
                                       placeholder="Conference Room A, 3rd Floor">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-file-alt"></i>
                            Instructions & Notes
                        </h3>

                        <div class="form-group">
                            <label class="form-label">Instructions for Candidates</label>
                            <textarea name="instructions" class="form-control"
                                      placeholder="What should candidates prepare? Any specific requirements?"><?= $interview->instructions ?? '' ?></textarea>
                            <div class="help-text">This will be sent to candidates in the invitation email</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="internal_notes" class="form-control"
                                      placeholder="Notes for interviewers (not visible to candidates)"><?= $interview->internal_notes ?? '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Interview Agenda</label>
                            <textarea name="agenda" class="form-control"
                                      placeholder="Topics to cover during the interview"><?= $interview->agenda ?? '' ?></textarea>
                        </div>
                    </div>

                    <!-- Reminders -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-bell"></i>
                            Reminders
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <input type="checkbox" name="send_reminders" value="1"
                                           <?= (isset($interview) && $interview->send_reminders) || !isset($interview) ? 'checked' : '' ?>>
                                    Send automated reminders
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Reminder Time (hours before)</label>
                                <input type="number" name="reminder_hours_before" class="form-control"
                                       value="<?= $interview->reminder_hours_before ?? '24' ?>" min="1">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="/interviews" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Interview
                        </button>
                    </div>
                </div>
            </form>
</div>

<script>
$(document).ready(function() {
    // Platform selection styling
    $('input[name="platform"]').on('change', function() {
        $('.platform-option').removeClass('selected');
        $(this).closest('.platform-option').addClass('selected');

        // Show/hide relevant fields
        const platform = $(this).val();
        if (platform === 'in-person') {
            $('#onlineMeetingFields').hide();
            $('#inPersonFields').show();
        } else if (platform === 'phone') {
            $('#onlineMeetingFields').hide();
            $('#inPersonFields').hide();
        } else {
            $('#onlineMeetingFields').show();
            $('#inPersonFields').hide();
        }
    });

    // Initialize on page load
    $('input[name="platform"]:checked').trigger('change');

    // Form validation
    $('#scheduleForm').on('submit', function(e) {
        const date = new Date($('input[name="scheduled_date"]').val() + ' ' + $('input[name="scheduled_time"]').val());
        const now = new Date();

        if (date < now) {
            alert('Please select a future date and time for the interview.');
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
