<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<style>
    .column-wrap {
        width: 1%;
        white-space: nowrap;
    }

    .quick-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-decoration: none;
        display: inline-block;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .quick-action-btn.primary {
        background-color: #667eea;
        color: white;
    }

    .quick-action-btn.success {
        background-color: #10b981;
        color: white;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/documents/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Document
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-calendar-week"></i> This Week</div>
            <div class="summary-card-value" id="weekDocs">0</div>
            <div class="summary-card-subtitle">documents uploaded</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-calendar"></i> This Month</div>
            <div class="summary-card-value" id="monthDocs">0</div>
            <div class="summary-card-subtitle">documents uploaded</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-hdd"></i> Storage Used</div>
            <div class="summary-card-value" id="storageUsed">0 MB</div>
            <div class="summary-card-subtitle">total file size</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-folder"></i> Top Category</div>
            <div class="summary-card-value" id="topCategory">-</div>
            <div class="summary-card-subtitle">most used</div>
        </div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">File</th>
                    <th scope="col">Client</th>
                    <th scope="col">Category</th>
                    <th scope="col">Created</th>
                    <th scope="col">Modified</th>
                    <th scope="col" width="50">Action</th>
                    <th scope="col" width="500">Preview</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $row):
                    $html = "";
                    $url = base_url() . "/document/view/" . $row['uuid'];
                    $html = '<a href="' . $url . '" target="_blank">Document Link</a>';
                ?>
                    <tr data-link=<?= "/" . $tableName . "/edit/" . $row['id']; ?>>

                        <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id'] ?>"><?= !empty($row['file']) ? basename($row['file']) : ''; ?></td>
                        <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id'] ?>"><?= $row['company_name']; ?></td>
                        <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id'] ?>" data-category="<?= $row['category_id'] ?? '' ?>"><?= $row['category_name'] ?? 'N/A'; ?></td>

                        <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id'] ?>" data-created="<?= $row['created_at'] ?? '' ?>">
                            <?php if (isset($row['created_at']) && (!empty($row['created_at']))) {
                                echo render_date(strtotime($row['created_at']));
                            } ?>
                        </td>
                        <td class="f_s_12 f_w_400 open-file" data-id="<?= $row['id'] ?>" data-document-date="<?= $row['document_date'] ?? '' ?>">
                            <?php if (isset($row['modified_at']) && (!empty($row['modified_at']))) {
                                echo render_date(strtotime($row['modified_at']));
                            } ?>
                        </td>

                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href=<?= "/" . $tableName . "/delete/" . $row['id']; ?>>
                                            <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="<?= "/" . $tableName . "/edit/" . $row['id']; ?>"> <i class="fas fa-edit"></i> Edit</a>
                                        <a class="dropdown-item" id="copyToClipBoard" link='<?= !empty($row['file']) ? str_replace("https://webimpetus-images.s3.eu-west-2.amazonaws.com", $front_domain, $row['file']) : ''; ?>'> <i class="fas fa-copy"></i> Copy Link</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="f_s_12 f_w_400 preview-file"></td>

                    </tr>

                <?php endforeach; ?>


            </tbody>
        </table>
    </div>
</div>

<?php require_once (APPPATH . 'Views/documents/footer.php'); ?>

<script>
    $('.table-listing-items  tr  td').on('click', function(e) {

        var dataClickable = $(this).parent().attr('data-link');
        if ($(this).is(':last-child') || $(this).is(':first-child')) {}

    });

    $(document).on("click", ".open-file", function(e) {

        e.preventDefault();

        var el = $(this);
        var rowid = el.data('id');

        $.ajax({
            url: baseURL + "documents/getfile",
            data: {
                rowid: rowid
            },
            method: 'POST',
            dataType: "JSON",
        }).done(function(response) {
            var html = '';
            if (response.file.length > 0) {

                html = '<iframe  src="https://docs.google.com/gview?embedded=true&url=' + response.file + '" width="512" height="500"> </iframe>';
            }

            $('.preview-file').html('');
            setTimeout(function() {
                el.closest('tr').find('.preview-file').html(html);
            }, 500);
        });


    })

    $(document).on("click", "#copyToClipBoard", function(e) {
        e.preventDefault();
        var val = $(this).attr("link");
        copyToClipboard(val);
    });

    function copyToClipboard(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);
    }

    // Update summary cards with document metrics
    function updateDocumentSummaryCards() {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay()); // Sunday of current week
        const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);

        let weekCount = 0;
        let monthCount = 0;
        let totalStorage = 0;
        let categoryMap = {};

        // Iterate through table rows to calculate metrics
        $('#example tbody tr').each(function() {
            const $row = $(this);

            // Get document date from data attribute or created date
            const documentDateStr = $row.find('td[data-document-date]').data('document-date');
            const createdDateStr = $row.find('td[data-created]').data('created');
            const dateStr = documentDateStr || createdDateStr;

            if (dateStr) {
                const docDate = new Date(dateStr);

                // Count this week
                if (docDate >= weekStart) {
                    weekCount++;
                }

                // Count this month
                if (docDate >= monthStart) {
                    monthCount++;
                }
            }

            // Track categories
            const categoryId = $row.find('td[data-category]').data('category');
            const categoryName = $row.find('td[data-category]').text().trim();

            if (categoryId && categoryName && categoryName !== 'N/A') {
                if (!categoryMap[categoryName]) {
                    categoryMap[categoryName] = 0;
                }
                categoryMap[categoryName]++;
            }

            // Note: Storage calculation would require actual file sizes from metadata
            // For now, we'll estimate or you can add data-size attributes to rows
        });

        // Find top category
        let topCategoryName = '-';
        let topCategoryCount = 0;

        for (const [categoryName, count] of Object.entries(categoryMap)) {
            if (count > topCategoryCount) {
                topCategoryCount = count;
                topCategoryName = categoryName;
            }
        }

        // Update summary cards
        $('#weekDocs').text(weekCount);
        $('#monthDocs').text(monthCount);

        // For storage, you would need to fetch this from the backend
        // or include it in the row data attributes
        $('#storageUsed').text('N/A');

        if (topCategoryCount > 0) {
            $('#topCategory').text(topCategoryName);
            $('#topCategory').next('.summary-card-subtitle').text(topCategoryCount + ' documents');
        } else {
            $('#topCategory').text('-');
            $('#topCategory').next('.summary-card-subtitle').text('no documents');
        }

        console.log('Document summary updated:', {
            week: weekCount,
            month: monthCount,
            topCategory: topCategoryName,
            topCount: topCategoryCount
        });
    }

    // Update summary cards on page load
    $(document).ready(function() {
        // Wait for DataTable to initialize if being used
        setTimeout(function() {
            updateDocumentSummaryCards();
        }, 500);
    });
</script>
