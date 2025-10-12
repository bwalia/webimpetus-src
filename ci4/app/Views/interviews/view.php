<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    .main_content_iner {
        background: #f4f5f7 !important;
    }

    .interview-view {
        background: transparent;
        padding: 0;
        width: 100%;
        max-width: 100%;
    }

    .interview-header {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .interview-header h2 {
        margin: 0 0 15px 0;
        color: #1a1a2e;
        font-size: 24px;
        font-weight: 700;
    }

    .interview-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #64748b;
        font-size: 14px;
    }

    .meta-item strong {
        color: #1a1a2e;
        font-weight: 600;
    }

    .meeting-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }

    .meeting-info h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        font-weight: 600;
    }

    .meeting-link {
        background: rgba(255, 255, 255, 0.2);
        padding: 12px 15px;
        border-radius: 8px;
        margin-top: 10px;
        word-break: break-all;
    }

    .meeting-link a {
        color: white;
        text-decoration: none;
        font-weight: 500;
    }

    .meeting-link a:hover {
        text-decoration: underline;
    }

    .candidates-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .candidates-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .candidates-header h3 {
        margin: 0;
        color: #1a1a2e;
        font-size: 20px;
        font-weight: 700;
    }

    .bulk-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-bulk {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-reminders {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-reminders:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-add-candidates {
        background: #10b981;
        color: white;
    }

    .btn-add-candidates:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .candidate-card {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .candidate-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .candidate-card.fit {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .candidate-card.not-fit {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .candidate-card.strong-fit {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .candidate-card.maybe {
        border-color: #eab308;
        background: #fefce8;
    }

    .candidate-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .candidate-info h4 {
        margin: 0 0 5px 0;
        color: #1a1a2e;
        font-size: 18px;
        font-weight: 700;
    }

    .candidate-email {
        color: #64748b;
        font-size: 14px;
    }

    .status-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.attended {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.confirmed {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-badge.invited {
        background: #e0e7ff;
        color: #4338ca;
    }

    .status-badge.no-show {
        background: #fee2e2;
        color: #991b1b;
    }

    .evaluation-section {
        margin-top: 15px;
    }

    .evaluation-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-bottom: 15px;
    }

    .eval-btn {
        padding: 12px 16px;
        border: 2px solid;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-align: center;
        background: white;
    }

    .eval-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .eval-btn.fit {
        border-color: #10b981;
        color: #10b981;
    }

    .eval-btn.fit.active {
        background: #10b981;
        color: white;
    }

    .eval-btn.not-fit {
        border-color: #ef4444;
        color: #ef4444;
    }

    .eval-btn.not-fit.active {
        background: #ef4444;
        color: white;
    }

    .eval-btn.maybe {
        border-color: #eab308;
        color: #eab308;
    }

    .eval-btn.maybe.active {
        background: #eab308;
        color: white;
    }

    .eval-btn.strong-fit {
        border-color: #f59e0b;
        color: #f59e0b;
    }

    .eval-btn.strong-fit.active {
        background: #f59e0b;
        color: white;
    }

    .star-rating {
        display: flex;
        gap: 5px;
        margin: 15px 0;
    }

    .star {
        font-size: 24px;
        cursor: pointer;
        color: #cbd5e1;
        transition: all 0.2s ease;
    }

    .star:hover,
    .star.active {
        color: #f59e0b;
    }

    .tags-section {
        margin: 15px 0;
    }

    .tags-section label {
        display: block;
        margin-bottom: 8px;
        color: #1a1a2e;
        font-weight: 600;
        font-size: 14px;
    }

    .tag-options {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tag-option {
        padding: 8px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 20px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s ease;
        background: white;
    }

    .tag-option:hover {
        border-color: #667eea;
    }

    .tag-option.selected {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
    }

    .feedback-section {
        margin: 15px 0;
    }

    .feedback-section label {
        display: block;
        margin-bottom: 8px;
        color: #1a1a2e;
        font-weight: 600;
        font-size: 14px;
    }

    .feedback-section textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
    }

    .feedback-section textarea:focus {
        outline: none;
        border-color: #667eea;
    }

    .decision-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 8px;
        color: #1a1a2e;
        font-weight: 600;
        font-size: 14px;
    }

    .form-group select,
    .form-group input {
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
    }

    .form-group select:focus,
    .form-group input:focus {
        outline: none;
        border-color: #667eea;
    }

    .save-evaluation-btn {
        margin-top: 15px;
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .save-evaluation-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .cv-download {
        margin-top: 10px;
    }

    .cv-download a {
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .cv-download a:hover {
        text-decoration: underline;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        margin: 0 0 10px 0;
        color: #1a1a2e;
        font-size: 20px;
    }

    .add-candidates-section {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }

    .add-candidates-section h4 {
        margin: 0 0 15px 0;
        color: #1a1a2e;
        font-size: 16px;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .decision-section {
            grid-template-columns: 1fr;
        }

        .evaluation-buttons {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="interview-view">
    <!-- Interview Header -->
    <div class="interview-header">
        <h2><?= esc($interview['interview_title']) ?></h2>

        <?php if (!empty($interview['job_title'])): ?>
            <div style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                📋 <?= esc($interview['job_title']) ?>
            </div>
        <?php endif; ?>

        <div class="interview-meta">
            <div class="meta-item">
                <strong>Type:</strong> <?= ucfirst(str_replace('-', ' ', $interview['interview_type'])) ?>
            </div>
            <div class="meta-item">
                <strong>Round:</strong> <?= $interview['interview_round'] ?>
            </div>
            <div class="meta-item">
                <strong>Date:</strong> <?= date('M d, Y', strtotime($interview['scheduled_date'])) ?>
            </div>
            <div class="meta-item">
                <strong>Time:</strong> <?= date('g:i A', strtotime($interview['scheduled_time'])) ?>
            </div>
            <div class="meta-item">
                <strong>Duration:</strong> <?= $interview['duration_minutes'] ?> minutes
            </div>
            <div class="meta-item">
                <strong>Status:</strong>
                <span class="status-badge <?= $interview['status'] ?>">
                    <?= ucfirst($interview['status']) ?>
                </span>
            </div>
        </div>

        <!-- Meeting Information -->
        <?php if ($interview['platform'] !== 'phone'): ?>
        <div class="meeting-info">
            <h3>
                <?php
                $platformIcons = [
                    'google-meet' => '📹 Google Meet',
                    'zoom' => '💻 Zoom',
                    'microsoft-teams' => '🎥 Microsoft Teams',
                    'in-person' => '🏢 In-Person Meeting',
                    'other' => '🔗 Online Meeting'
                ];
                echo $platformIcons[$interview['platform']] ?? $interview['platform'];
                ?>
            </h3>

            <?php if ($interview['platform'] === 'in-person'): ?>
                <div><strong>Location:</strong> <?= esc($interview['location_address']) ?></div>
                <?php if (!empty($interview['location_room'])): ?>
                    <div><strong>Room:</strong> <?= esc($interview['location_room']) ?></div>
                <?php endif; ?>
            <?php else: ?>
                <?php if (!empty($interview['meeting_link'])): ?>
                    <div class="meeting-link">
                        <strong>Meeting Link:</strong><br>
                        <a href="<?= esc($interview['meeting_link']) ?>" target="_blank">
                            <?= esc($interview['meeting_link']) ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (!empty($interview['meeting_id'])): ?>
                    <div style="margin-top: 10px;"><strong>Meeting ID:</strong> <?= esc($interview['meeting_id']) ?></div>
                <?php endif; ?>
                <?php if (!empty($interview['meeting_password'])): ?>
                    <div><strong>Password:</strong> <?= esc($interview['meeting_password']) ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($interview['candidate_instructions'])): ?>
            <div style="margin-top: 20px; padding: 15px; background: #f8fafc; border-left: 4px solid #667eea; border-radius: 5px;">
                <strong>Instructions for Candidates:</strong>
                <p style="margin: 10px 0 0 0; color: #64748b;"><?= nl2br(esc($interview['candidate_instructions'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Candidates Section -->
    <div class="candidates-section">
        <div class="candidates-header">
            <h3>Interview Candidates (<?= count($candidates) ?>)</h3>
            <div class="bulk-actions">
                <button class="btn-bulk btn-reminders" onclick="sendBulkReminders()">
                    📨 Send Reminders to All
                </button>
                <button class="btn-bulk btn-add-candidates" onclick="toggleAddCandidates()">
                    ➕ Add Candidates
                </button>
            </div>
        </div>

        <!-- Add Candidates Section (Hidden by default) -->
        <div class="add-candidates-section" id="addCandidatesSection" style="display: none;">
            <h4>Add Candidates from Job Applications</h4>
            <select id="availableCandidates" class="form-control" multiple style="height: 150px;">
                <?php if (!empty($availableCandidates)): ?>
                    <?php foreach ($availableCandidates as $candidate): ?>
                        <option value="<?= $candidate['id'] ?>">
                            <?= esc($candidate['applicant_name']) ?> - <?= esc($candidate['email']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option disabled>No available candidates from this job</option>
                <?php endif; ?>
            </select>
            <button onclick="addSelectedCandidates()" class="btn-bulk btn-add-candidates" style="margin-top: 10px; width: 100%;">
                Add Selected Candidates
            </button>
        </div>

        <!-- Candidates List -->
        <div id="candidatesList">
            <?php if (empty($candidates)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">👥</div>
                    <h3>No Candidates Yet</h3>
                    <p>Add candidates from job applications to start evaluating them.</p>
                </div>
            <?php else: ?>
                <?php foreach ($candidates as $candidate): ?>
                    <div class="candidate-card <?= $candidate['evaluation_status'] !== 'pending' ? $candidate['evaluation_status'] : '' ?>"
                         data-candidate-id="<?= $candidate['id'] ?>">
                        <div class="candidate-header">
                            <div class="candidate-info">
                                <h4><?= esc($candidate['applicant_name']) ?></h4>
                                <div class="candidate-email"><?= esc($candidate['email']) ?></div>
                                <?php if (!empty($candidate['cv_file_path'])): ?>
                                    <div class="cv-download">
                                        <a href="/jobs/downloadCV/<?= $candidate['application_id'] ?>" target="_blank">
                                            📄 Download CV
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="status-badges">
                                <span class="status-badge <?= $candidate['attendance_status'] ?>">
                                    <?= ucfirst($candidate['attendance_status']) ?>
                                </span>
                                <?php if ($candidate['evaluation_status'] !== 'pending'): ?>
                                    <span class="status-badge" style="background: <?=
                                        $candidate['evaluation_status'] === 'fit' ? '#d1fae5' :
                                        ($candidate['evaluation_status'] === 'not-fit' ? '#fee2e2' :
                                        ($candidate['evaluation_status'] === 'strong-fit' ? '#fef3c7' : '#fef9c3'))
                                    ?>; color: <?=
                                        $candidate['evaluation_status'] === 'fit' ? '#065f46' :
                                        ($candidate['evaluation_status'] === 'not-fit' ? '#991b1b' :
                                        ($candidate['evaluation_status'] === 'strong-fit' ? '#92400e' : '#854d0e'))
                                    ?>;">
                                        <?= ucfirst($candidate['evaluation_status']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="evaluation-section">
                            <!-- Evaluation Buttons -->
                            <div class="evaluation-buttons">
                                <button class="eval-btn fit <?= $candidate['evaluation_status'] === 'fit' ? 'active' : '' ?>"
                                        onclick="setEvaluation(<?= $candidate['id'] ?>, 'fit')">
                                    ✅ Fit
                                </button>
                                <button class="eval-btn strong-fit <?= $candidate['evaluation_status'] === 'strong-fit' ? 'active' : '' ?>"
                                        onclick="setEvaluation(<?= $candidate['id'] ?>, 'strong-fit')">
                                    ⭐ Strong Fit
                                </button>
                                <button class="eval-btn maybe <?= $candidate['evaluation_status'] === 'maybe' ? 'active' : '' ?>"
                                        onclick="setEvaluation(<?= $candidate['id'] ?>, 'maybe')">
                                    🤔 Maybe
                                </button>
                                <button class="eval-btn not-fit <?= $candidate['evaluation_status'] === 'not-fit' ? 'active' : '' ?>"
                                        onclick="setEvaluation(<?= $candidate['id'] ?>, 'not-fit')">
                                    ❌ Not Fit
                                </button>
                            </div>

                            <!-- Star Rating -->
                            <div class="star-rating" data-candidate-id="<?= $candidate['id'] ?>">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= ($candidate['rating'] ?? 0) ? 'active' : '' ?>"
                                          data-rating="<?= $i ?>"
                                          onclick="setRating(<?= $candidate['id'] ?>, <?= $i ?>)">
                                        ⭐
                                    </span>
                                <?php endfor; ?>
                            </div>

                            <!-- Tags -->
                            <div class="tags-section">
                                <label>Selection Tags:</label>
                                <div class="tag-options">
                                    <?php
                                    $availableTags = ['technical-skills', 'communication', 'culture-fit', 'problem-solving', 'leadership', 'teamwork', 'creativity', 'experience'];
                                    $selectedTags = !empty($candidate['selection_tags']) ? json_decode($candidate['selection_tags'], true) : [];
                                    foreach ($availableTags as $tag):
                                    ?>
                                        <span class="tag-option <?= in_array($tag, $selectedTags) ? 'selected' : '' ?>"
                                              data-candidate-id="<?= $candidate['id'] ?>"
                                              data-tag="<?= $tag ?>"
                                              onclick="toggleTag(this)">
                                            <?= ucfirst(str_replace('-', ' ', $tag)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Feedback -->
                            <div class="feedback-section">
                                <label>Interview Feedback:</label>
                                <textarea class="feedback-textarea"
                                          data-candidate-id="<?= $candidate['id'] ?>"
                                          placeholder="Add your feedback about this candidate's performance..."><?= esc($candidate['feedback'] ?? '') ?></textarea>
                            </div>

                            <!-- Decision & Next Steps -->
                            <div class="decision-section">
                                <div class="form-group">
                                    <label>Decision:</label>
                                    <select class="decision-select" data-candidate-id="<?= $candidate['id'] ?>">
                                        <option value="pending" <?= ($candidate['decision'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="proceed" <?= ($candidate['decision'] ?? '') === 'proceed' ? 'selected' : '' ?>>Proceed to Next Round</option>
                                        <option value="reject" <?= ($candidate['decision'] ?? '') === 'reject' ? 'selected' : '' ?>>Reject</option>
                                        <option value="hold" <?= ($candidate['decision'] ?? '') === 'hold' ? 'selected' : '' ?>>Hold</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Next Steps:</label>
                                    <input type="text"
                                           class="next-steps-input"
                                           data-candidate-id="<?= $candidate['id'] ?>"
                                           placeholder="e.g., Schedule final round"
                                           value="<?= esc($candidate['next_steps'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Save Button -->
                            <button class="save-evaluation-btn" onclick="saveEvaluation(<?= $candidate['id'] ?>)">
                                💾 Save Evaluation
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const interviewUuid = '<?= $interview['uuid'] ?>';

function setEvaluation(candidateId, status) {
    // Update UI
    const card = document.querySelector(`[data-candidate-id="${candidateId}"]`);
    card.className = 'candidate-card ' + status;

    // Update button states
    card.querySelectorAll('.eval-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    card.querySelector(`.eval-btn.${status}`).classList.add('active');
}

function setRating(candidateId, rating) {
    const container = document.querySelector(`.star-rating[data-candidate-id="${candidateId}"]`);
    container.querySelectorAll('.star').forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

function toggleTag(element) {
    element.classList.toggle('selected');
}

function saveEvaluation(candidateId) {
    const card = document.querySelector(`[data-candidate-id="${candidateId}"]`);

    // Get evaluation status
    const activeEvalBtn = card.querySelector('.eval-btn.active');
    const evaluationStatus = activeEvalBtn ?
        activeEvalBtn.classList.contains('fit') ? 'fit' :
        activeEvalBtn.classList.contains('not-fit') ? 'not-fit' :
        activeEvalBtn.classList.contains('maybe') ? 'maybe' :
        activeEvalBtn.classList.contains('strong-fit') ? 'strong-fit' : 'pending'
        : 'pending';

    // Get rating
    const rating = card.querySelectorAll('.star.active').length;

    // Get selected tags
    const selectedTags = Array.from(card.querySelectorAll('.tag-option.selected'))
        .map(tag => tag.dataset.tag);

    // Get feedback
    const feedback = card.querySelector('.feedback-textarea').value;

    // Get decision and next steps
    const decision = card.querySelector('.decision-select').value;
    const nextSteps = card.querySelector('.next-steps-input').value;

    const data = {
        candidate_id: candidateId,
        evaluation_status: evaluationStatus,
        rating: rating,
        selection_tags: JSON.stringify(selectedTags),
        feedback: feedback,
        decision: decision,
        next_steps: nextSteps
    };

    $.ajax({
        url: '/interviews/updateEvaluation',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                // Show success message
                const btn = card.querySelector('.save-evaluation-btn');
                const originalText = btn.textContent;
                btn.textContent = '✓ Saved!';
                btn.style.background = '#10b981';

                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                }, 2000);
            } else {
                alert('Error saving evaluation: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            alert('Error saving evaluation. Please try again.');
            console.error('Error:', error);
        }
    });
}

function sendBulkReminders() {
    if (!confirm('Send interview reminders to all candidates via Email and WhatsApp?')) {
        return;
    }

    $.ajax({
        url: '/interviews/sendReminders/' + interviewUuid,
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                alert('Reminders sent successfully to all candidates!');
            } else {
                alert('Error sending reminders: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            alert('Error sending reminders. Please try again.');
            console.error('Error:', error);
        }
    });
}

function toggleAddCandidates() {
    $('#addCandidatesSection').slideToggle();
}

function addSelectedCandidates() {
    const selectedOptions = $('#availableCandidates').find(':selected');

    if (selectedOptions.length === 0) {
        alert('Please select at least one candidate');
        return;
    }

    const candidateIds = selectedOptions.map(function() {
        return $(this).val();
    }).get();

    $.ajax({
        url: '/interviews/addCandidates',
        method: 'POST',
        data: {
            interview_uuid: interviewUuid,
            candidate_ids: candidateIds
        },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                alert('Candidates added successfully!');
                location.reload();
            } else {
                alert('Error adding candidates: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            alert('Error adding candidates. Please try again.');
            console.error('Error:', error);
        }
    });
}
</script>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
