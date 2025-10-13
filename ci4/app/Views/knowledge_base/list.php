<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<style>
    /* Override summary card styles to match sales_invoices exactly */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: var(--bg-primary, #ffffff) !important;
        border-radius: var(--radius-lg, 8px) !important;
        padding: 24px !important;
        box-shadow: var(--shadow-md, 0 4px 8px rgba(9, 30, 66, 0.15)) !important;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: none !important;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px !important;
        width: 100% !important;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }

    .summary-card.blue::before {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    }

    .summary-card.green::before {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    .summary-card.orange::before {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }

    .summary-card.purple::before {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg, 0 8px 16px rgba(9, 30, 66, 0.2)) !important;
    }

    .summary-card-title {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: var(--gray-600, #6b7280) !important;
        margin-bottom: 12px !important;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: none !important;
        letter-spacing: normal !important;
    }

    .summary-card-value {
        font-size: 2rem !important;
        font-weight: 700 !important;
        color: var(--gray-900, #111827) !important;
        line-height: 1 !important;
        margin-bottom: 8px !important;
    }

    .summary-card-subtitle {
        font-size: 0.75rem !important;
        color: var(--gray-500, #6b7280) !important;
        margin-top: 0 !important;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/knowledge_base/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Article
        </a>
    </div>
</div>

<!-- Summary Cards for Knowledge Base -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-book"></i> Total Articles</div>
            <div class="summary-card-value" id="totalArticles">0</div>
            <div class="summary-card-subtitle">all articles</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Published</div>
            <div class="summary-card-value" id="publishedArticles">0</div>
            <div class="summary-card-subtitle">live articles</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-eye"></i> Total Views</div>
            <div class="summary-card-value" id="totalViews">0</div>
            <div class="summary-card-subtitle">article views</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-thumbs-up"></i> Helpful</div>
            <div class="summary-card-value" id="totalHelpful">0</div>
            <div class="summary-card-subtitle">positive feedback</div>
        </div>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table" id="knowledgeBaseTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Article #', 'Title', 'Category', 'Status', 'Views', 'Helpful', 'Created'];
    let columnsMachineName = ['id', 'article_number', 'title', 'category', 'status', 'view_count', 'helpful_count', 'created_at'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "knowledge_base",
            apiPath: "knowledge_base/knowledgeBaseList",
            selector: "knowledgeBaseTable"
        }
    );

    // Update summary cards
    $(document).ready(function() {
        setTimeout(function() {
            updateKnowledgeBaseSummaryCards();
        }, 1000);
    });

    // Update summary cards with knowledge base metrics
    function updateKnowledgeBaseSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('jwt_token') ?? session('token') ?? ''; ?>';

        // Fetch knowledge base data and calculate summaries
        fetch('/knowledge_base/knowledgeBaseList?uuid_business_id=' + businessUuid, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateKnowledgeBaseMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching knowledge base summary data:', error);
            });
    }

    function calculateKnowledgeBaseMetrics(articles) {
        let totalArticles = articles.length;
        let publishedArticles = 0;
        let totalViews = 0;
        let totalHelpful = 0;

        articles.forEach(function(article) {
            // Count published articles
            const status = (article.status || '').toLowerCase();
            if (status === 'published' || status === 'active' || status == 1) {
                publishedArticles++;
            }

            // Sum up total views
            const views = parseInt(article.view_count || 0);
            totalViews += views;

            // Sum up helpful count
            const helpful = parseInt(article.helpful_count || 0);
            totalHelpful += helpful;
        });

        // Update summary cards
        $('#totalArticles').text(totalArticles);
        $('#publishedArticles').text(publishedArticles);
        $('#totalViews').text(totalViews.toLocaleString());
        $('#totalHelpful').text(totalHelpful.toLocaleString());

        console.log('Knowledge Base metrics updated:', {
            totalArticles: totalArticles,
            publishedArticles: publishedArticles,
            totalViews: totalViews,
            totalHelpful: totalHelpful
        });
    }
</script>
