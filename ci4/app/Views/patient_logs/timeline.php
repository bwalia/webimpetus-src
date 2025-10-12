<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<style>
    .patient-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .timeline-container {
        position: relative;
        padding-left: 30px;
    }

    .timeline-container::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 2px solid white;
    }

    .timeline-date {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 5px;
    }

    .timeline-category {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .vital-signs-chart {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .metric-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }

    .metric-label {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 5px;
    }
</style>

<div class="white_card_body">
    <!-- Patient Header -->
    <div class="patient-header">
        <h3><?= $patient->name ?></h3>
        <p class="mb-0">
            <i class="fa fa-phone"></i> <?= $patient->phone ?? 'N/A' ?> |
            <i class="fa fa-envelope"></i> <?= $patient->email ?? 'N/A' ?>
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mb-3">
        <div>
            <a href="/patient_logs" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to Logs
            </a>
        </div>
        <div>
            <a href="/patient_logs/edit?patient_contact_id=<?= $patient_contact_id ?>" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Log
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#timeline-tab">
                <i class="fa fa-stream"></i> Timeline
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#vitals-tab">
                <i class="fa fa-heartbeat"></i> Vital Signs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#medications-tab">
                <i class="fa fa-pills"></i> Medications
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#labs-tab">
                <i class="fa fa-flask"></i> Lab Results
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Timeline Tab -->
        <div id="timeline-tab" class="tab-pane fade show active">
            <div class="timeline-container">
                <?php if (empty($timeline)): ?>
                    <p class="text-muted">No logs recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($timeline as $log): ?>
                        <div class="timeline-item">
                            <div class="timeline-date">
                                <i class="fa fa-clock"></i>
                                <?= date('d M Y H:i', strtotime($log['performed_datetime'])) ?>
                            </div>

                            <span class="timeline-category" style="background-color: #e0e7ff; color: #3730a3;">
                                <?= $log['log_category'] ?>
                            </span>

                            <?php if ($log['is_flagged']): ?>
                                <span class="badge badge-danger ml-2">
                                    <i class="fa fa-flag"></i> Flagged
                                </span>
                            <?php endif; ?>

                            <h6 class="mt-2"><?= $log['title'] ?? $log['log_type'] ?? 'Log Entry' ?></h6>

                            <?php if (!empty($log['description'])): ?>
                                <p class="mb-2"><?= nl2br(htmlspecialchars($log['description'])) ?></p>
                            <?php endif; ?>

                            <?php if ($log['log_category'] == 'Medication'): ?>
                                <div class="row">
                                    <div class="col-md-3"><strong>Medication:</strong> <?= $log['medication_name'] ?></div>
                                    <div class="col-md-2"><strong>Dosage:</strong> <?= $log['dosage'] ?></div>
                                    <div class="col-md-2"><strong>Route:</strong> <?= $log['route'] ?></div>
                                    <div class="col-md-3"><strong>Frequency:</strong> <?= $log['frequency'] ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if ($log['log_category'] == 'Vital Signs'): ?>
                                <div class="row">
                                    <?php if ($log['blood_pressure_systolic']): ?>
                                        <div class="col-md-3">
                                            <strong>BP:</strong> <?= $log['blood_pressure_systolic'] ?>/<?= $log['blood_pressure_diastolic'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($log['heart_rate']): ?>
                                        <div class="col-md-2"><strong>HR:</strong> <?= $log['heart_rate'] ?> bpm</div>
                                    <?php endif; ?>
                                    <?php if ($log['temperature']): ?>
                                        <div class="col-md-2"><strong>Temp:</strong> <?= $log['temperature'] ?>°C</div>
                                    <?php endif; ?>
                                    <?php if ($log['oxygen_saturation']): ?>
                                        <div class="col-md-2"><strong>SpO2:</strong> <?= $log['oxygen_saturation'] ?>%</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2 text-muted" style="font-size: 0.85rem;">
                                <i class="fa fa-user"></i> <?= $log['staff_name'] ?? 'Unknown' ?>
                                <?php if (!empty($log['job_title'])): ?>
                                    (<?= $log['job_title'] ?>)
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Vital Signs Tab -->
        <div id="vitals-tab" class="tab-pane fade">
            <div class="vital-signs-chart">
                <h5 class="mb-3">Recent Vital Signs (Last 7 Days)</h5>

                <?php if (empty($vital_signs)): ?>
                    <p class="text-muted">No vital signs recorded in the last 7 days.</p>
                <?php else: ?>
                    <div class="row">
                        <?php
                        $latest = $vital_signs[count($vital_signs) - 1];
                        ?>
                        <?php if (!empty($latest['blood_pressure_systolic'])): ?>
                            <div class="col-md-3 mb-3">
                                <div class="metric-box">
                                    <div class="metric-value"><?= $latest['blood_pressure_systolic'] ?>/<?= $latest['blood_pressure_diastolic'] ?></div>
                                    <div class="metric-label">Blood Pressure</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($latest['heart_rate'])): ?>
                            <div class="col-md-3 mb-3">
                                <div class="metric-box">
                                    <div class="metric-value"><?= $latest['heart_rate'] ?></div>
                                    <div class="metric-label">Heart Rate (bpm)</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($latest['temperature'])): ?>
                            <div class="col-md-3 mb-3">
                                <div class="metric-box">
                                    <div class="metric-value"><?= $latest['temperature'] ?>°C</div>
                                    <div class="metric-label">Temperature</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($latest['oxygen_saturation'])): ?>
                            <div class="col-md-3 mb-3">
                                <div class="metric-box">
                                    <div class="metric-value"><?= $latest['oxygen_saturation'] ?>%</div>
                                    <div class="metric-label">SpO2</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h6 class="mt-4 mb-3">History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>BP</th>
                                    <th>HR</th>
                                    <th>Temp</th>
                                    <th>SpO2</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse($vital_signs) as $vs): ?>
                                    <tr>
                                        <td><?= date('d M H:i', strtotime($vs['performed_datetime'])) ?></td>
                                        <td><?= $vs['blood_pressure_systolic'] ? $vs['blood_pressure_systolic'] . '/' . $vs['blood_pressure_diastolic'] : '-' ?></td>
                                        <td><?= $vs['heart_rate'] ?? '-' ?></td>
                                        <td><?= $vs['temperature'] ? $vs['temperature'] . '°C' : '-' ?></td>
                                        <td><?= $vs['oxygen_saturation'] ? $vs['oxygen_saturation'] . '%' : '-' ?></td>
                                        <td><?= $vs['staff_name'] ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Medications Tab -->
        <div id="medications-tab" class="tab-pane fade">
            <h5 class="mb-3">Medication History</h5>

            <?php if (empty($medications)): ?>
                <p class="text-muted">No medications recorded.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Route</th>
                                <th>Frequency</th>
                                <th>Status</th>
                                <th>Administered By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medications as $med): ?>
                                <tr>
                                    <td><?= date('d M Y H:i', strtotime($med['administered_at'] ?? $med['performed_datetime'])) ?></td>
                                    <td><strong><?= $med['medication_name'] ?></strong></td>
                                    <td><?= $med['dosage'] ?></td>
                                    <td><?= $med['route'] ?></td>
                                    <td><?= $med['frequency'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $med['medication_status'] == 'Administered' ? 'success' : 'warning' ?>">
                                            <?= $med['medication_status'] ?? 'Pending' ?>
                                        </span>
                                    </td>
                                    <td><?= $med['staff_name'] ?? '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Lab Results Tab -->
        <div id="labs-tab" class="tab-pane fade">
            <h5 class="mb-3">Laboratory Results</h5>

            <?php if (empty($lab_results)): ?>
                <p class="text-muted">No lab results recorded.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Test Name</th>
                                <th>Result</th>
                                <th>Reference Range</th>
                                <th>Flag</th>
                                <th>Ordered By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lab_results as $lab): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($lab['performed_datetime'])) ?></td>
                                    <td><strong><?= $lab['test_name'] ?></strong></td>
                                    <td><?= $lab['test_result'] ?></td>
                                    <td><?= $lab['reference_range'] ?? '-' ?></td>
                                    <td>
                                        <?php if (!empty($lab['abnormal_flag'])): ?>
                                            <span class="badge badge-danger"><?= $lab['abnormal_flag'] ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $lab['staff_name'] ?? '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
