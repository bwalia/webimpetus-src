<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<style>
    .user-business-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .user-selection-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .user-selection-card h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-select-wrapper {
        position: relative;
    }

    .user-select-wrapper select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .user-select-wrapper select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .dual-panel-container {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 24px;
        margin-bottom: 24px;
        align-items: start;
    }

    .panel {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-height: 400px;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
    }

    .panel-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .panel-count {
        background: #667eea;
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .panel-search {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 12px;
        font-size: 0.875rem;
    }

    .panel-search:focus {
        outline: none;
        border-color: #667eea;
    }

    .business-list {
        max-height: 450px;
        overflow-y: auto;
    }

    .business-item {
        padding: 12px;
        margin-bottom: 8px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .business-item:hover {
        border-color: #667eea;
        background: #f9fafb;
        transform: translateX(4px);
    }

    .business-item.selected {
        border-color: #10b981;
        background: #d1fae5;
    }

    .business-item.primary {
        border-color: #f59e0b;
        background: #fef3c7;
    }

    .business-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
    }

    .business-info {
        flex-grow: 1;
    }

    .business-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
    }

    .business-meta {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 2px;
    }

    .business-badge {
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .badge-primary {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-assigned {
        background: #d1fae5;
        color: #065f46;
    }

    .transfer-controls {
        display: flex;
        flex-direction: column;
        gap: 12px;
        justify-content: center;
        padding: 20px 0;
    }

    .transfer-btn {
        padding: 12px 20px;
        border-radius: 8px;
        border: 2px solid #667eea;
        background: #667eea;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .transfer-btn:hover {
        background: #5568d3;
        transform: scale(1.05);
    }

    .transfer-btn:disabled {
        background: #e5e7eb;
        border-color: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .transfer-btn.secondary {
        background: white;
        color: #667eea;
    }

    .transfer-btn.secondary:hover {
        background: #f9fafb;
    }

    .primary-business-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .primary-business-section h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .primary-options {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }

    .primary-option {
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .primary-option:hover {
        border-color: #f59e0b;
    }

    .primary-option.selected {
        border-color: #f59e0b;
        background: #fef3c7;
    }

    .primary-option input[type="radio"] {
        accent-color: #f59e0b;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-save {
        padding: 12px 32px;
        border-radius: 8px;
        border: none;
        background: #10b981;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-save:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .btn-cancel {
        padding: 12px 32px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        background: white;
        color: #6b7280;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-cancel:hover {
        border-color: #9ca3af;
        background: #f9fafb;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dual-panel-container {
            grid-template-columns: 1fr;
        }

        .transfer-controls {
            flex-direction: row;
            order: -1;
        }

        .primary-options {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="white_card_body">
    <div class="user-business-container">
        <form action="/user_business/update" method="post" id="userBusinessForm">
            <?php
                $uri = service('uri');
                $uriSegment = $uri->getSegment(3);
            ?>

            <!-- User Selection -->
            <div class="user-selection-card">
                <h3><i class="fa fa-user"></i> Select User</h3>
                <div class="user-select-wrapper">
                    <select id="user_id" name="user_id" class="select2" required <?php if ($uriSegment && !empty($uriSegment) && isset($uriSegment)) { echo 'disabled=disabled'; } ?>>
                        <option value="">-- Select a user --</option>
                        <?php foreach ($allUsers as $row): ?>
                            <option value="<?= $row->id; ?>" data-uuid="<?= $row->uuid; ?>" <?php if ($row->uuid == @$selectedUser['uuid']) { echo 'selected'; } ?>>
                                <?= $row->name; ?> (<?= $row->email; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo @$result[0]->uuid; ?>" />
            <input type="hidden" name="selectedUserId" value="<?php echo @$selectedUser['uuid']; ?>" />
            <input type="hidden" id="user_business_id_hidden" name="user_business_id[]" value="" />

            <?php
            $assignedBusinesses = [];
            if (isset($result[0]->user_business_id) && !is_null($result[0]->user_business_id)) {
                $assignedBusinesses = json_decode(@$result[0]->user_business_id, true) ?: [];
            }
            ?>

            <!-- Dual Panel Business Assignment -->
            <div class="dual-panel-container">
                <!-- Available Businesses -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fa fa-building"></i>
                            Available Businesses
                            <span class="panel-count" id="availableCount">0</span>
                        </div>
                    </div>
                    <input type="text" class="panel-search" id="searchAvailable" placeholder="Search businesses...">
                    <div class="business-list" id="availableList">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>

                <!-- Transfer Controls -->
                <div class="transfer-controls">
                    <button type="button" class="transfer-btn" id="assignBtn" onclick="assignSelected()" disabled>
                        <i class="fa fa-arrow-right"></i> Assign
                    </button>
                    <button type="button" class="transfer-btn secondary" id="assignAllBtn" onclick="assignAll()">
                        <i class="fa fa-angle-double-right"></i> All
                    </button>
                    <button type="button" class="transfer-btn secondary" id="removeAllBtn" onclick="removeAll()">
                        <i class="fa fa-angle-double-left"></i> All
                    </button>
                    <button type="button" class="transfer-btn" id="removeBtn" onclick="removeSelected()" disabled>
                        <i class="fa fa-arrow-left"></i> Remove
                    </button>
                </div>

                <!-- Assigned Businesses -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <i class="fa fa-check-circle"></i>
                            Assigned Businesses
                            <span class="panel-count" id="assignedCount">0</span>
                        </div>
                    </div>
                    <input type="text" class="panel-search" id="searchAssigned" placeholder="Search assigned...">
                    <div class="business-list" id="assignedList">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Primary Business Selection -->
            <div class="primary-business-section" id="primarySection" style="display: none;">
                <h3><i class="fa fa-star"></i> Select Primary Business</h3>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 12px;">
                    Choose which business the user will see by default when they log in.
                </p>
                <div class="primary-options" id="primaryOptions">
                    <!-- Populated by JavaScript -->
                </div>
                <input type="hidden" name="primary_business_uuid" id="primary_business_uuid" value="<?= @$result[0]->primary_business_uuid; ?>">
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="/user_business" class="btn-cancel">
                    <i class="fa fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-save">
                    <i class="fa fa-save"></i> Save Assignment
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
// All businesses data
const allBusinesses = <?= json_encode($userBusiness); ?>;
const assignedBusinessUUIDs = <?= json_encode($assignedBusinesses); ?>;
const primaryBusinessUUID = '<?= @$result[0]->primary_business_uuid; ?>';

let availableBusinesses = [];
let assignedBusinesses = [];
let selectedAvailable = [];
let selectedAssigned = [];

// Initialize
$(document).ready(function() {
    initializeBusinessLists();
    updateCounts();
    updatePrimaryOptions();
    setupSearchFilters();

    // If editing existing, show primary section
    if (assignedBusinesses.length > 0) {
        $('#primarySection').show();
    }
});

function initializeBusinessLists() {
    availableBusinesses = allBusinesses.filter(b => !assignedBusinessUUIDs.includes(b.uuid));
    assignedBusinesses = allBusinesses.filter(b => assignedBusinessUUIDs.includes(b.uuid));

    renderAvailableList();
    renderAssignedList();
}

function renderAvailableList(filter = '') {
    const container = $('#availableList');
    container.empty();

    const filtered = availableBusinesses.filter(b =>
        b.name.toLowerCase().includes(filter.toLowerCase())
    );

    if (filtered.length === 0) {
        container.html('<div class="empty-state"><i class="fa fa-inbox"></i><p>No businesses available</p></div>');
        return;
    }

    filtered.forEach(business => {
        const initial = business.name.charAt(0).toUpperCase();
        const item = $(`
            <div class="business-item" data-uuid="${business.uuid}" onclick="toggleAvailableSelection('${business.uuid}')">
                <div class="business-icon">${initial}</div>
                <div class="business-info">
                    <div class="business-name">${business.name}</div>
                    <div class="business-meta">Click to select</div>
                </div>
            </div>
        `);
        container.append(item);
    });
}

function renderAssignedList(filter = '') {
    const container = $('#assignedList');
    container.empty();

    const filtered = assignedBusinesses.filter(b =>
        b.name.toLowerCase().includes(filter.toLowerCase())
    );

    if (filtered.length === 0) {
        container.html('<div class="empty-state"><i class="fa fa-inbox"></i><p>No businesses assigned</p></div>');
        $('#primarySection').hide();
        return;
    }

    $('#primarySection').show();

    filtered.forEach(business => {
        const initial = business.name.charAt(0).toUpperCase();
        const isPrimary = business.uuid === primaryBusinessUUID;
        const item = $(`
            <div class="business-item ${isPrimary ? 'primary' : ''}" data-uuid="${business.uuid}" onclick="toggleAssignedSelection('${business.uuid}')">
                <div class="business-icon">${initial}</div>
                <div class="business-info">
                    <div class="business-name">${business.name}</div>
                    <div class="business-meta">Click to select</div>
                </div>
                ${isPrimary ? '<span class="business-badge badge-primary"><i class="fa fa-star"></i> Primary</span>' : '<span class="business-badge badge-assigned"><i class="fa fa-check"></i> Assigned</span>'}
            </div>
        `);
        container.append(item);
    });
}

function toggleAvailableSelection(uuid) {
    const index = selectedAvailable.indexOf(uuid);
    if (index > -1) {
        selectedAvailable.splice(index, 1);
        $(`#availableList [data-uuid="${uuid}"]`).removeClass('selected');
    } else {
        selectedAvailable.push(uuid);
        $(`#availableList [data-uuid="${uuid}"]`).addClass('selected');
    }
    updateButtons();
}

function toggleAssignedSelection(uuid) {
    const index = selectedAssigned.indexOf(uuid);
    if (index > -1) {
        selectedAssigned.splice(index, 1);
        $(`#assignedList [data-uuid="${uuid}"]`).removeClass('selected');
    } else {
        selectedAssigned.push(uuid);
        $(`#assignedList [data-uuid="${uuid}"]`).addClass('selected');
    }
    updateButtons();
}

function assignSelected() {
    selectedAvailable.forEach(uuid => {
        const business = availableBusinesses.find(b => b.uuid === uuid);
        if (business) {
            assignedBusinesses.push(business);
            availableBusinesses = availableBusinesses.filter(b => b.uuid !== uuid);
        }
    });

    selectedAvailable = [];
    renderAvailableList($('#searchAvailable').val());
    renderAssignedList($('#searchAssigned').val());
    updateCounts();
    updatePrimaryOptions();
    updateButtons();
    updateHiddenInput();
}

function removeSelected() {
    selectedAssigned.forEach(uuid => {
        const business = assignedBusinesses.find(b => b.uuid === uuid);
        if (business) {
            availableBusinesses.push(business);
            assignedBusinesses = assignedBusinesses.filter(b => b.uuid !== uuid);

            // Clear primary if removed
            if ($('#primary_business_uuid').val() === uuid) {
                $('#primary_business_uuid').val('');
            }
        }
    });

    selectedAssigned = [];
    renderAvailableList($('#searchAvailable').val());
    renderAssignedList($('#searchAssigned').val());
    updateCounts();
    updatePrimaryOptions();
    updateButtons();
    updateHiddenInput();
}

function assignAll() {
    assignedBusinesses = [...assignedBusinesses, ...availableBusinesses];
    availableBusinesses = [];
    selectedAvailable = [];

    renderAvailableList($('#searchAvailable').val());
    renderAssignedList($('#searchAssigned').val());
    updateCounts();
    updatePrimaryOptions();
    updateButtons();
    updateHiddenInput();
}

function removeAll() {
    availableBusinesses = [...availableBusinesses, ...assignedBusinesses];
    assignedBusinesses = [];
    selectedAssigned = [];
    $('#primary_business_uuid').val('');

    renderAvailableList($('#searchAvailable').val());
    renderAssignedList($('#searchAssigned').val());
    updateCounts();
    updatePrimaryOptions();
    updateButtons();
    updateHiddenInput();
}

function updateCounts() {
    $('#availableCount').text(availableBusinesses.length);
    $('#assignedCount').text(assignedBusinesses.length);
}

function updateButtons() {
    $('#assignBtn').prop('disabled', selectedAvailable.length === 0);
    $('#removeBtn').prop('disabled', selectedAssigned.length === 0);
}

function updatePrimaryOptions() {
    const container = $('#primaryOptions');
    container.empty();

    if (assignedBusinesses.length === 0) {
        return;
    }

    assignedBusinesses.forEach(business => {
        const isSelected = business.uuid === $('#primary_business_uuid').val();
        const option = $(`
            <label class="primary-option ${isSelected ? 'selected' : ''}" onclick="selectPrimary('${business.uuid}')">
                <input type="radio" name="primary_radio" value="${business.uuid}" ${isSelected ? 'checked' : ''}>
                <div class="business-info">
                    <div class="business-name">${business.name}</div>
                </div>
            </label>
        `);
        container.append(option);
    });
}

function selectPrimary(uuid) {
    $('#primary_business_uuid').val(uuid);
    $('.primary-option').removeClass('selected');
    $(`.primary-option:has(input[value="${uuid}"])`).addClass('selected');

    // Update visual in assigned list
    renderAssignedList($('#searchAssigned').val());
}

function updateHiddenInput() {
    const uuids = assignedBusinesses.map(b => b.uuid);
    // Clear existing hidden inputs
    $('[name="user_business_id[]"]').remove();

    // Add new hidden inputs for each business
    uuids.forEach(uuid => {
        $('<input>').attr({
            type: 'hidden',
            name: 'user_business_id[]',
            value: uuid
        }).appendTo('#userBusinessForm');
    });
}

function setupSearchFilters() {
    $('#searchAvailable').on('input', function() {
        renderAvailableList($(this).val());
    });

    $('#searchAssigned').on('input', function() {
        renderAssignedList($(this).val());
    });
}

// Form submission
$('#userBusinessForm').on('submit', function(e) {
    if (assignedBusinesses.length === 0) {
        e.preventDefault();
        alert('Please assign at least one business to the user.');
        return false;
    }

    if (assignedBusinesses.length > 0 && !$('#primary_business_uuid').val()) {
        e.preventDefault();
        alert('Please select a primary business.');
        return false;
    }

    updateHiddenInput();
});
</script>
