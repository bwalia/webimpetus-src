<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table" id="customersTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/scripts.php'); ?>

<script>
    const grid = new gridjs.Grid({
        columns: [
            {
                name: "uuid",
                hidden: true
            },
            'Id', 'Customer Name',
            'Account Number',
            'Status', 'Email',
            {
                name: 'Actions',
                sort: false,
                formatter: (cell, row) => {
                    return gridjs.html(
                        `<div class="header_more_tool">
                            <div class="dropdown">
                                <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                    <i class="ti-more-alt"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');"
                                        href="/customers/deleterow/${row.cells[0].data}"> <i class="ti-trash"></i>
                                        Delete
                                    </a>
                                    <a class="dropdown-item" href="/customers/edit/${row.cells[0].data}"> <i
                                        class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>`
                    );
                }
            },
        ],
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
        sort: {
            multiColumn: false,
            server: {
                url: (prev, columns) => {
                    if (!columns.length) return prev;
                    const col = columns[0];
                    const dir = col.direction === 1 ? 'asc' : 'desc';
                    let colNames = ['uuid', 'id', 'company_name', 'acc_no', 'status', 'email'];
                    let colName = colNames[col.index];
                    
                    return `${prev}${prev.includes("?") ? "&" : "?"}order=${colName}&dir=${dir}`;
                }
            }
        },
        server: {
            url: 'customers/customersList',
            then: data => data.data.map(customer =>
                [
                    customer.uuid,
                    customer.id,
                    customer.company_name,
                    customer.acc_no,
                    customer.status === 1 ? "Active" : "Inactive",
                    customer.email,
                    null
                ]
            ),
            total: data => data.recordsTotal
        }
    }).render(document.getElementById("customersTable"));
    grid.on('cellClick',
        (...args) =>
            console.log({args})
            // args[2].id !== "actions" &&
            // (window.location.href = `/customers/edit/${args[3]._cells[0].data}`)
    );
</script>