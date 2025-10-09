<?php require_once (APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="domainForm" method="post" action="/domains/update" enctype="multipart/form-data">
            <nav>
                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                        aria-controls="nav-home" aria-selected="true">
                        <i class="ti-world"></i> Domain Details
                    </a>
                    <a class="nav-item nav-link" id="nav-paths-tab" data-toggle="tab" href="#nav-paths" role="tab"
                        aria-controls="nav-paths" aria-selected="false">
                        <i class="ti-settings"></i> Configuration
                    </a>
                </div>
            </nav>

            <div class="tab-content py-4" id="nav-tabContent">
                <!-- Domain Details Tab -->
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group required">
                                <label for="name">Domain Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="name"
                                       name="name"
                                       placeholder="example.com"
                                       value="<?= esc($domain->name) ?>"
                                       required>
                                <small class="form-text text-muted">Enter a valid domain name (e.g., example.com)</small>
                                <div id="domain_error" class="invalid-feedback" style="display:none;"></div>
                                <input type="hidden" name="id" value="<?= esc($domain->uuid) ?>">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group required">
                                <label for="uuid">Customer <span class="text-danger">*</span></label>
                                <select id="uuid" name="uuid" class="form-control select2" required>
                                    <option value="">-- Select Customer --</option>
                                    <?php foreach ($customers as $row): ?>
                                        <option value="<?= esc($row['uuid']) ?>"
                                                <?= ($row['uuid'] == $domain->customer_uuid) ? 'selected' : '' ?>>
                                            <?= esc($row['company_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php
                        $serviceUuids = [];
                        foreach ($serviceDomains as $serviceDomain) {
                            $serviceUuids[] = $serviceDomain['service_uuid'];
                        }
                        ?>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sid">Associated Services</label>
                                <select id="sid" name="sid[]" multiple class="form-control select2">
                                    <?php foreach ($services as $row): ?>
                                        <option value="<?= esc($row['uuid']) ?>"
                                                <?= (in_array($row['uuid'], $serviceUuids)) ? 'selected' : '' ?>>
                                            <?= esc($row['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Select one or more services for this domain</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control"
                                          id="notes"
                                          name="notes"
                                          rows="4"
                                          placeholder="Add any notes or comments about this domain"><?= esc($domain->notes) ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Domain Logo/Image</label>

                                <div class="all-media-image-files mb-3">
                                    <?php if (!empty($domain->image_logo)): ?>
                                        <div class="d-flex align-items-center">
                                            <img src="/<?= esc($domain->image_logo) ?>"
                                                 width="120px"
                                                 class="img-thumbnail mr-2">
                                            <a href="/domains/deleteImage/<?= esc($domain->uuid) ?>"
                                               onclick="return confirm('Are you sure you want to delete this image?')"
                                               class="btn btn-danger btn-sm">
                                                <i class="ti-trash"></i> Delete Image
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="custom-file">
                                    <input type="file"
                                           class="custom-file-input"
                                           id="customFile"
                                           name="file"
                                           accept="image/*">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                                <small class="form-text text-muted">Supported formats: PNG, JPG, GIF (Max 10MB)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuration Tab -->
                <div class="tab-pane fade" id="nav-paths" role="tabpanel" aria-labelledby="nav-paths-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="domain_path">Path</label>
                                <input type="text"
                                       class="form-control"
                                       id="domain_path"
                                       name="domain_path"
                                       placeholder="/path/to/resource"
                                       value="<?= esc($domain->domain_path) ?>">
                                <small class="form-text text-muted">URL path for this domain</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="domain_path_type">Path Type</label>
                                <select class="form-control" id="domain_path_type" name="domain_path_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="prefix" <?= ($domain->domain_path_type == 'prefix') ? 'selected' : '' ?>>Prefix</option>
                                    <option value="exact" <?= ($domain->domain_path_type == 'exact') ? 'selected' : '' ?>>Exact Match</option>
                                    <option value="regex" <?= ($domain->domain_path_type == 'regex') ? 'selected' : '' ?>>Regex</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="domain_service_name">Service Name</label>
                                <input type="text"
                                       class="form-control"
                                       id="domain_service_name"
                                       name="domain_service_name"
                                       placeholder="my-service"
                                       value="<?= esc($domain->domain_service_name) ?>">
                                <small class="form-text text-muted">Backend service name</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="domain_service_port">Service Port</label>
                                <input type="number"
                                       class="form-control"
                                       id="domain_service_port"
                                       name="domain_service_port"
                                       placeholder="8080"
                                       min="1"
                                       max="65535"
                                       value="<?= esc($domain->domain_service_port) ?>">
                                <small class="form-text text-muted">Port number (1-65535)</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="ti-info-alt"></i>
                                <strong>Configuration Settings:</strong> These settings control how the domain routes to backend services.
                                Leave empty if not using service routing.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="ti-save"></i> Save Domain
                </button>
                <a href="/domains" class="btn btn-secondary">
                    <i class="ti-close"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
$(document).ready(function() {
    var domainId = "<?= $domain->uuid ?>";

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: function(){
            $(this).data('placeholder');
        }
    });

    // Custom file input label update
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
    });

    // Domain name validation
    $('#name').on('blur', function() {
        var domainName = $(this).val().trim();
        var domainRegex = /^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/;

        if (domainName && !domainRegex.test(domainName)) {
            $('#domain_error').show().text('Please enter a valid domain name (e.g., example.com)');
            $(this).addClass('is-invalid');
        } else {
            $('#domain_error').hide();
            $(this).removeClass('is-invalid');
        }
    });

    // Form validation
    $('#domainForm').on('submit', function(e) {
        var domainName = $('#name').val().trim();
        var customer = $('#uuid').val();
        var domainRegex = /^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/;

        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');

        var isValid = true;

        if (!domainName) {
            $('#name').addClass('is-invalid');
            $('#domain_error').show().text('Domain name is required');
            isValid = false;
        } else if (!domainRegex.test(domainName)) {
            $('#name').addClass('is-invalid');
            $('#domain_error').show().text('Please enter a valid domain name');
            isValid = false;
        }

        if (!customer) {
            $('#uuid').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            $('html, body').animate({
                scrollTop: $('.is-invalid:first').offset().top - 100
            }, 500);
            return false;
        }

        return true;
    });

    // AJAX file upload
    $('#customFile').on('change', function() {
        var file = this.files[0];
        if (file && domainId) {
            var formData = new FormData();
            formData.append('file', file);
            formData.append('id', domainId);

            $.ajax({
                url: '/domains/uploadMediaFiles',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status == '1') {
                        $('.all-media-image-files').html(response.file_path);
                        toastr.success('Image uploaded successfully');
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function() {
                    toastr.error('Error uploading file');
                }
            });
        }
    });
});
</script>

<style>
    #domain_error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .nav-tabs .nav-link {
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        color: #007bff;
        font-weight: 600;
    }

    .nav-tabs .nav-link i {
        margin-right: 5px;
    }

    .form-group.required > label:after {
        content: "";
        margin-left: 0;
    }

    .img-thumbnail {
        border: 2px solid #dee2e6;
    }

    .custom-file-label::after {
        content: "Browse";
    }

    .select2-container {
        width: 100% !important;
    }
</style>