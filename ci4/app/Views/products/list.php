<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Code</th>
                    <th scope="col">Description</th>
                    <th scope="col">SKU</th>
                    <th scope="col">Published</th>
                    <th scope="col">Availavle Stock</th>
                    <th scope="col">Unit Price</th>
                    <th scope="col">Sort Order</th>
                    <th scope="col">Created at</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($productsList as $row) : ?>
                    <tr data-link="/products/edit/<?= $row['uuid']; ?>">

                        <td class="f_s_12 f_w_400"><?= $row['id']; ?></td>
                        <td class="f_s_12 f_w_400"><?= $row['name']; ?>
                        <td class="f_s_12 f_w_400"><?= $row['code']; ?></td>
                        <td scope="col"><?= $row['description']; ?></td>

                        <td scope="col"><?= $row['sku']; ?></td>
                        <td class="f_s_12 f_w_400 <?= $row['is_published'] == 0 ? 'text_color_1' : 'text_color_2' ?> ">
                            <span class="stsSpan"> <?= $row['is_published'] == 0 ? 'inactive' : 'active' ?></span>
                        </td>
                        <td scope="col"><?= $row['stock_available']; ?></td>
                        <td scope="col"><?= $row['unit_price']; ?></td>
                        <td scope="col"><?= $row['sort_order']; ?></td>
                        <td scope="col"><?= $row['created_at']; ?></td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/products/delete/<?= $row['uuid']; ?>"> <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="/products/edit/<?= $row['uuid']; ?>"> <i class="fas fa-edit"></i> Edit</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>