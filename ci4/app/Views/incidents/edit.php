<?php require_once (APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="addincident" method="post" action="/incidents/update" enctype="multipart/form-data">
            <input type="hidden" class="form-control" name="id" value="<?= @$incident->id ?>" />
            <input type="hidden" class="form-control" name="uuid" value="<?= @$incident->uuid ?>" />

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="incident_number">Incident Number</label>
                    <input type="text" class="form-control" id="incident_number" name="incident_number"
                           value="<?= @$incident->incident_number ?>" readonly>
                </div>

                <div class="form-group required col-md-6">
                    <label for="title">Title</label>
                    <input type="text" class="form-control required" id="title" name="title"
                           value="<?= @$incident->title ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= @$incident->description ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="priority">Priority</label>
                    <select class="form-control select2" id="priority" name="priority">
                        <option value="low" <?= @$incident->priority == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= @$incident->priority == 'medium' || empty($incident) ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= @$incident->priority == 'high' ? 'selected' : '' ?>>High</option>
                        <option value="critical" <?= @$incident->priority == 'critical' ? 'selected' : '' ?>>Critical</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="impact">Impact</label>
                    <select class="form-control select2" id="impact" name="impact">
                        <option value="low" <?= @$incident->impact == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= @$incident->impact == 'medium' || empty($incident) ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= @$incident->impact == 'high' ? 'selected' : '' ?>>High</option>
                        <option value="critical" <?= @$incident->impact == 'critical' ? 'selected' : '' ?>>Critical</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="urgency">Urgency</label>
                    <select class="form-control select2" id="urgency" name="urgency">
                        <option value="low" <?= @$incident->urgency == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= @$incident->urgency == 'medium' || empty($incident) ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= @$incident->urgency == 'high' ? 'selected' : '' ?>>High</option>
                        <option value="critical" <?= @$incident->urgency == 'critical' ? 'selected' : '' ?>>Critical</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <select class="form-control select2" id="status" name="status">
                        <option value="new" <?= @$incident->status == 'new' || empty($incident) ? 'selected' : '' ?>>New</option>
                        <option value="assigned" <?= @$incident->status == 'assigned' ? 'selected' : '' ?>>Assigned</option>
                        <option value="in_progress" <?= @$incident->status == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="pending" <?= @$incident->status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="resolved" <?= @$incident->status == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="closed" <?= @$incident->status == 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category"
                           value="<?= @$incident->category ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="tags">Tags</label>
                    <input type="text" class="form-control" id="tags" name="tags"
                           value="<?= @$incident->tags ?>" placeholder="Comma separated">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="assigned_to">Assigned To</label>
                    <select class="form-control select2" id="assigned_to" name="assigned_to">
                        <option value="">--Select User--</option>
                        <?php if (isset($users)) {
                            foreach ($users as $user): ?>
                                <option value="<?= $user['id']; ?>" <?= $user['id'] == @$incident->assigned_to ? 'selected' : '' ?>>
                                    <?= $user['name']; ?>
                                </option>
                            <?php endforeach;
                        } ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="reporter_id">Reporter</label>
                    <select class="form-control select2" id="reporter_id" name="reporter_id">
                        <option value="">--Select Reporter--</option>
                        <?php if (isset($users)) {
                            foreach ($users as $user): ?>
                                <option value="<?= $user['id']; ?>" <?= $user['id'] == @$incident->reporter_id ? 'selected' : '' ?>>
                                    <?= $user['name']; ?>
                                </option>
                            <?php endforeach;
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="customer_id">Customer</label>
                    <select class="form-control select2" id="customer_id" name="customer_id">
                        <option value="">--Select Customer--</option>
                        <?php if (isset($customers)) {
                            foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id']; ?>" <?= $customer['id'] == @$incident->customer_id ? 'selected' : '' ?>>
                                    <?= $customer['company_name']; ?>
                                </option>
                            <?php endforeach;
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="reported_date">Reported Date</label>
                    <input type="text" class="form-control datetimepicker" id="reported_date" name="reported_date"
                           value="<?= @$incident->reported_date ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="due_date">Due Date</label>
                    <input type="text" class="form-control datetimepicker" id="due_date" name="due_date"
                           value="<?= @$incident->due_date ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="resolved_date">Resolved Date</label>
                    <input type="text" class="form-control datetimepicker" id="resolved_date" name="resolved_date"
                           value="<?= @$incident->resolved_date ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="related_kb_id">Related Knowledge Base Article</label>
                    <select class="form-control select2" id="related_kb_id" name="related_kb_id">
                        <option value="">--Select Article--</option>
                        <?php if (isset($knowledge_base)) {
                            foreach ($knowledge_base as $kb): ?>
                                <option value="<?= $kb['id']; ?>" <?= $kb['id'] == @$incident->related_kb_id ? 'selected' : '' ?>>
                                    <?= $kb['article_number']; ?> - <?= $kb['title']; ?>
                                </option>
                            <?php endforeach;
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="resolution_notes">Resolution Notes</label>
                    <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="4"><?= @$incident->resolution_notes ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="/incidents" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
