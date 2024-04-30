<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table" id="customersTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/scripts.php'); ?>

<script>
    $ = jQuery;
    function updateURL(searchQuery) {
        // Get the current URL
        var currentURL = window.location.href;

        // Remove existing search query parameter, if any
        var updatedURL = currentURL.split('?')[0];

        // If search query is not empty, add it to the URL
        if (searchQuery.trim() !== "") {
            updatedURL += "?query=" + encodeURIComponent(searchQuery);
        }

        // Replace the current URL with the updated one
        history.replaceState(null, null, updatedURL);
    }

    function resetSearch() {
        history.replaceState(null, null, "/customers");
        window.location.reload();
    }

    new gridjs.Grid({
        columns: ['Id', 'Customer Name'],
        pagination: {
            limit: 2,
            server: {
                url: (prev, page, limit) => `${prev}${prev.includes("?") ? "&" : "?"}limit=${limit}&offset=${page * limit}`
            }
        },
        search: {
            server: {
                url: (prev, keyword) => `${prev}?query=${keyword}`
            }
        },
        server: {
            url: 'customers/customersList',
            then: data => data.data.map(customer =>
                [customer.id, customer.company_name]
            ),
            total: data => data.recordsTotal
        }
    }).render(document.getElementById("customersTable"));
</script>