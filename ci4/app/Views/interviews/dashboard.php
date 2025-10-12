<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    /* Modern Interview Dashboard Styling */
    .main_content_iner {
        background: #f4f5f7 !important;
    }

    .interview-dashboard {
        background: transparent;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        width: 100%;
        max-width: 100%;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .stat-icon.purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stat-icon.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .stat-icon.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin: 8px 0 4px 0;
    }

    .stat-label {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
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

    .btn-schedule {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-schedule:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .interviews-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .interview-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
    }

    .interview-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
    }

    .interview-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .interview-status {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-confirmed {
        background: #d1fae5;
        color: #065f46;
    }

    .status-completed {
        background: #e0e7ff;
        color: #4338ca;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .interview-type-badge {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 600;
        background: #f3f4f6;
        color: #4b5563;
    }

    .interview-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px 0;
    }

    .interview-job {
        font-size: 13px;
        color: #667eea;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .interview-datetime {
        display: flex;
        align-items: center;
        gap: 16px;
        margin: 12px 0;
        padding: 12px;
        background: #f8fafc;
        border-radius: 8px;
    }

    .datetime-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #475569;
    }

    .datetime-item i {
        color: #667eea;
    }

    .interview-meta {
        display: flex;
        gap: 16px;
        margin: 12px 0;
        font-size: 13px;
        color: #64748b;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .meta-item i {
        color: #667eea;
    }

    .interview-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
    }

    .btn-sm {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #334155;
        text-decoration: none;
    }

    .candidate-count-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
        color: white;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .empty-state-icon {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 16px;
    }

    .empty-state-title {
        font-size: 20px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    .empty-state-text {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 24px;
    }

    .platform-icon {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        background: #ede9fe;
        color: #6b21a8;
    }
</style>

<div class="interview-dashboard">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">📅 Interview Management</h1>
        <a href="/interviews/schedule" class="btn-schedule">
            <i class="fas fa-plus"></i>
            Schedule New Interview
        </a>
    </div>

            <!-- Statistics Cards -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value" id="totalInterviews"><?= $total_interviews ?></div>
                    <div class="stat-label">Total Interviews</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value" id="upcomingInterviews"><?= $upcoming_interviews ?></div>
                    <div class="stat-label">Upcoming Interviews</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="completedInterviews"><?= $completed_interviews ?></div>
                    <div class="stat-label">Completed This Month</div>
                </div>
            </div>

            <!-- Interviews Grid -->
            <div class="interviews-grid" id="interviewsGrid">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <div class="empty-state-icon">📅</div>
                <h3 class="empty-state-title">No Interviews Scheduled</h3>
                <p class="empty-state-text">Get started by scheduling your first interview with candidates.</p>
                <a href="/interviews/schedule" class="btn-schedule">
                    <i class="fas fa-plus"></i>
                    Schedule Interview
                </a>
            </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadInterviews();

    function loadInterviews() {
        $.ajax({
            url: '/interviews/getInterviews',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data.length > 0) {
                    renderInterviews(response.data);
                    $('#emptyState').hide();
                } else {
                    $('#interviewsGrid').hide();
                    $('#emptyState').show();
                }
            },
            error: function() {
                console.error('Failed to load interviews');
            }
        });
    }

    function renderInterviews(interviews) {
        const grid = $('#interviewsGrid');
        grid.empty();

        interviews.forEach(interview => {
            const card = createInterviewCard(interview);
            grid.append(card);
        });
    }

    function createInterviewCard(interview) {
        const statusClass = `status-${interview.status}`;
        const platformIcons = {
            'google-meet': '📹',
            'zoom': '💻',
            'microsoft-teams': '🎥',
            'in-person': '🏢',
            'phone': '📞'
        };

        const date = new Date(interview.scheduled_date + ' ' + interview.scheduled_time);
        const formattedDate = date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        const formattedTime = date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });

        return `
            <div class="interview-card">
                <div class="candidate-count-badge">
                    <i class="fas fa-users"></i> ${interview.total_candidates || 0} Candidates
                </div>

                <div class="interview-header">
                    <span class="interview-status ${statusClass}">${interview.status}</span>
                    <span class="interview-type-badge">${interview.interview_type}</span>
                </div>

                <h3 class="interview-title">${interview.interview_title}</h3>

                ${interview.job_title ? `<div class="interview-job">
                    <i class="fas fa-briefcase"></i> ${interview.job_title}
                    ${interview.reference_number ? ` (${interview.reference_number})` : ''}
                </div>` : ''}

                <div class="interview-datetime">
                    <div class="datetime-item">
                        <i class="fas fa-calendar"></i>
                        <span>${formattedDate}</span>
                    </div>
                    <div class="datetime-item">
                        <i class="fas fa-clock"></i>
                        <span>${formattedTime}</span>
                    </div>
                </div>

                <div class="interview-meta">
                    <div class="meta-item">
                        <span class="platform-icon">${platformIcons[interview.platform] || '💻'} ${interview.platform}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-hourglass-half"></i>
                        <span>${interview.duration_minutes} mins</span>
                    </div>
                </div>

                <div class="interview-actions">
                    <a href="/interviews/view/${interview.uuid}" class="btn-sm btn-primary">
                        <i class="fas fa-eye"></i>
                        View Details
                    </a>
                    <a href="/interviews/schedule?uuid=${interview.uuid}" class="btn-sm btn-secondary">
                        <i class="fas fa-edit"></i>
                        Edit
                    </a>
                </div>
            </div>
        `;
    }
});
</script>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
