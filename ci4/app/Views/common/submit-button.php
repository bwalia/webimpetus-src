<?php
/**
 * Standard Submit Button Component
 *
 * Usage:
 * <?php include(APPPATH . 'Views/common/submit-button.php'); ?>
 *
 * Or with custom text:
 * <?php $submitButtonText = 'Save Payment'; include(APPPATH . 'Views/common/submit-button.php'); ?>
 *
 * Or with custom icon:
 * <?php $submitButtonIcon = 'fa-check'; include(APPPATH . 'Views/common/submit-button.php'); ?>
 */

// Default values
$submitButtonText = $submitButtonText ?? 'Save';
$submitButtonIcon = $submitButtonIcon ?? 'fa-save';
$submitButtonClass = $submitButtonClass ?? 'btn btn-primary btn-lg';
$submitButtonId = $submitButtonId ?? '';
$showCancelButton = $showCancelButton ?? true;
$cancelUrl = $cancelUrl ?? (isset($tableName) ? '/' . $tableName : '/dashboard');

// Check permissions to conditionally disable button
$canSubmit = true;
if (isset($can_create) && isset($can_update)) {
    $canSubmit = $can_create || $can_update;
} elseif (isset($can_create)) {
    $canSubmit = $can_create;
} elseif (isset($can_update)) {
    $canSubmit = $can_update;
}
?>

<div class="form-actions mt-4 mb-3">
    <hr class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <?php if ($canSubmit): ?>
                <button type="submit" class="<?= $submitButtonClass ?>" <?= $submitButtonId ? 'id="' . $submitButtonId . '"' : '' ?>>
                    <i class="fa <?= $submitButtonIcon ?>"></i> <?= $submitButtonText ?>
                </button>
            <?php else: ?>
                <button type="button" class="<?= $submitButtonClass ?>" disabled title="You do not have permission to save">
                    <i class="fa fa-lock"></i> <?= $submitButtonText ?> (No Permission)
                </button>
            <?php endif; ?>

            <?php if ($showCancelButton): ?>
                <a href="<?= $cancelUrl ?>" class="btn btn-secondary btn-lg ml-2">
                    <i class="fa fa-times"></i> Cancel
                </a>
            <?php endif; ?>
        </div>

        <?php if (!$canSubmit): ?>
            <div class="text-muted">
                <i class="fa fa-info-circle"></i> <small>You have view-only access to this module</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .form-actions {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 30px;
    }
    .form-actions .btn-lg {
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    .form-actions .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
    }
    .form-actions .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
    }
    .form-actions .btn-primary:active {
        transform: translateY(0);
    }
    .form-actions .btn-primary:disabled,
    .form-actions .btn-primary[disabled] {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .form-actions .btn-secondary {
        background: #6c757d;
        border: none;
    }
    .form-actions .btn-secondary:hover {
        background: #5a6268;
    }
</style>
