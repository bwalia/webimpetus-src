<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<?php
$customers = getResultArray("customers", ["supplier" => 1]);
$templates = getResultArray("templates", ["module_name" => $tableName]);
$items = getWithOutUuidResultArray("purchase_order_items", ["purchase_orders_uuid" => @$purchase_order->uuid], false);
$business = getRowArray("businesses", ["uuid_business_id" => session('uuid_business')], false);
$taxes = getResultArray("taxes", ["uuid_business_id" => session('uuid_business')], false);
$status = ["Estimate", "Quote", "Ordered", "Acknowledged", "Authorised", "Delivered", "Completed", "Proforma Invoice"];
?>

<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <input type="hidden" value="<?= @$purchase_order->uuid ?>" name="uuid" id="mainTableId">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><?php echo lang('Purchase_invoice.order_details'); ?></a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"> <?php echo lang('Purchase_invoice.other_details'); ?></a>
                        </div>
                    </nav>
                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.order_number'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input readonly type="text" autocomplete="off" class="form-control" value="<?= @$purchase_order->order_number ?>" id="order_number" name="order_number" placeholder="">
                                        </div>
                                    </div>

                                    <div class="row form-group row-space required">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.supplier'); ?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="client_id" name="client_id" class="form-control required dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <?php foreach ($customers as $row) : ?>
                                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$purchase_order->client_id) {
                                                                                            echo "selected";
                                                                                        } ?>><?= $row['company_name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.bill_to'); ?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <textarea name="bill_to" class="form-control"><?= @$purchase_order->bill_to ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.order_by'); ?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control" value="<?= @$purchase_order->order_by ?>" id="order_by" name="order_by" placeholder="">
                                        </div>
                                    </div>

                                    <div class="row form-group row-space">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Common.comments'); ?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <textarea name="comments" id="comments" class="form-control" cols="15" rows="5"><?= @$purchase_order->comments ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.order_template'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="template" name="template" class="form-control  dashboard-dropdown">
                                                <option value="" selected="">--Please Selected--</option>
                                                <?php foreach ($templates as $row) : ?>
                                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$purchase_order->template) {
                                                                                            echo "selected";
                                                                                        } ?>><?= $row['code']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.project_code'); ?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="project_code" id="project_code" class="form-control dashboard-dropdown">
                                                <option value="">--Please Select--</option>
                                                <option value="4D" <?= @$purchase_order->project_code == '4D' ? 'selected' : '' ?>>4D</option>
                                                <option value="CatBase" <?= @$purchase_order->project_code == 'CatBase' ? 'selected' : '' ?>> CatBase</option>
                                                <option value="Cloud Consultancy" <?= @$purchase_order->project_code == 'Cloud Consultancy' ? 'selected' : '' ?>> Cloud Consultancy</option>
                                                <option value="Cloud Native Engineering" <?= @$purchase_order->project_code == 'Cloud Native Engineering' ? 'selected' : '' ?>> Cloud Native Engineering</option>
                                                <option value="Database" <?= @$purchase_order->project_code == 'Database' ? 'selected' : '' ?>> Database</option>
                                                <option value="Domains" <?= @$purchase_order->project_code == 'Domains' ? 'selected' : '' ?>> Domains</option>
                                                <option value="IMG2D" <?= @$purchase_order->project_code == 'IMG2D' ? 'selected' : '' ?>> IMG2D</option>
                                                <option value="IT Consulting" <?= @$purchase_order->project_code == 'IT Consulting' ? 'selected' : '' ?>> IT Consulting</option>
                                                <option value="Jobshout" <?= @$purchase_order->project_code == 'Jobshout' ? 'selected' : '' ?>> Jobshout</option>
                                                <option value="Mobile App" <?= @$purchase_order->project_code == 'Mobile App' ? 'selected' : '' ?>> Mobile App</option>
                                                <option value="Mobile Friendly Website" <?= @$purchase_order->project_code == 'Mobile Friendly Website' ? 'selected' : '' ?>> Mobile Friendly Website</option>
                                                <option value="Nginx" <?= @$purchase_order->project_code == 'Nginx' ? 'selected' : '' ?>> Nginx</option>
                                                <option value="Time-Based" <?= @$purchase_order->project_code == 'Time-Based' ? 'selected' : '' ?>> Time-Based</option>
                                                <option value="TIZO" <?= @$purchase_order->project_code == 'TIZO' ? 'selected' : '' ?>> TIZO</option>
                                                <option value="WEBSITE" <?= @$purchase_order->project_code == 'WEBSITE' ? 'selected' : '' ?>> WEBSITE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="customInvoiceNumber"><?php echo lang('Purchase_invoice.custom_order_number'); ?>*</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control required" value="<?= empty(@$purchase_order->custom_order_number) ? @$business->business_code .  @$purchase_order->order_number : @$purchase_order->custom_order_number ?>" id="custom_order_number" name="custom_order_number" placeholder="">
                                        </div>
                                    </div>

                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Common.date'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" autocomplete="off" class="form-control datepicker" value="<?= render_date(@$purchase_order->date) ?>" id="date" name="date" placeholder="">
                                        </div>
                                    </div>



                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.balance_outstanding'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input readonly type="input" class="form-control" id="balance_due" name="balance_due" placeholder="" value="<?= @$purchase_order->balance_due ?>">
                                        </div>
                                    </div>

                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Common.status'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="status" id="status" class="form-control dashboard-dropdown">
                                                <?php foreach ($status as $key => $value) : ?>
                                                    <option value="<?= $key; ?>" <?php if ($key == @$purchase_order->status) {
                                                                                        echo "selected";
                                                                                    } ?>><?= $value; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>



                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.grand_total'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input readonly type="total" class="form-control" id="total" name="total" placeholder="" value="<?= @$purchase_order->total ?>">
                                        </div>
                                    </div>

                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.paid_date'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control datepicker" id="paid_date" name="paid_date" placeholder="" value="<?= render_date(@$purchase_order->paid_date) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-12">

                                    <div class=" table-responsive table-full-width">
                                        <div class="table-responsive" style="border:none;">
                                            <table class="table table-striped  table-bordered table-hover custom-tbl-st" id="table-breakpoint" style="background-color: rgb(255, 255, 255); border-radius: 4px;">
                                                <tbody>
                                                    <tr class="item">
                                                        <th data-th="Item"><span class="bt-content"><?php echo lang('Common.item'); ?></span></th>
                                                        <th data-th="Description"><span class="bt-content"><?php echo lang('Common.description'); ?></span></th>
                                                        <th data-th="Rate"><span class="bt-content"><?php echo lang('Common.rate'); ?></span></th>
                                                        <th data-th="qty"><span class="bt-content"><?php echo lang('Common.qty'); ?></span></th>
                                                        <th data-th="discount"><span class="bt-content"><?php echo lang('Common.discount'); ?>(%)</span></th>
                                                        <th data-th="Amount"><span class="bt-content"><?php echo lang('Common.amount'); ?></span></th>
                                                        <th class="td_edit" data-th="Edit/Save"><span class="bt-content"><?php echo lang('Common.edit'); ?>/<?php echo lang('Common.save'); ?></span></th>
                                                        <th class="td_remove" data-th="Cancel/Remove"><span class="bt-content"><?php echo lang('Common.cancel'); ?>/<?php echo lang('Common.remove'); ?></span></th>
                                                    </tr>



                                                    <?php foreach ($items as $eachItems) { ?>
                                                        <tr class="item-row" id="<?= $eachItems->id ?>">
                                                            <td class="item-id" data-th="Item"><span class="bt-content">
                                                                    <div class="delete-wpr"><span class="item_id"><?= $eachItems->id ?></span>
                                                                        <input name="item_id[]" type="hidden" value="<?= $eachItems->id ?>">
                                                                    </div>
                                                                </span></td>
                                                            <td data-th="Description"><span class="bt-content">
                                                                    <span class="s_description" style="display: inline;"><?= $eachItems->description ?></span>
                                                                    <textarea maxlength="1023" class="description form-control" style="display: none;"><?= $eachItems->description ?></textarea>
                                                                </span></td>
                                                            <td data-th="Rate"><span class="bt-content">
                                                                    <span class="s_rate" style="display: inline;"><?= $eachItems->rate ?></span>
                                                                    <input type="text" autocomplete="off" class="rate num form-control" style="display: none;width:100%" value="<?= $eachItems->rate ?>">
                                                                </span></td>
                                                            <td data-th="Qty"><span class="bt-content">
                                                                    <span class="s_qty" style="display: inline;"><?= $eachItems->qty ?></span>
                                                                    <input type="number" autocomplete="off" class="qty num form-control" style="display: none;" value="<?= $eachItems->qty ?>">
                                                                </span></td>
                                                            <td data-th="Discount"><span class="bt-content">
                                                                    <span class="s_discount" style="display: inline;"><?= $eachItems->discount ?></span>
                                                                    <input type="text" autocomplete="off" class="discount num form-control" style="display: none;" value="<?= $eachItems->discount ?>">
                                                                </span></td>
                                                            <td data-th="Amount"><span class="bt-content">
                                                                    <span class="price"><?= $eachItems->amount ?></span>

                                                                </span></td>

                                                            <td class="td_edit" data-th="Edit/Save"><span class="bt-content">

                                                                    <a href="javascript:void(0)" class="editlink" title="Edit" style=""><i class="fa fa-edit"></i></a>

                                                                    <a href="javascript:void(0)" class="savelink" style="display:none" title="" aria-describedby="ui-tooltip-1"><i class="fa fa-save"></i></a>



                                                                </span></td>
                                                            <td class="td_remove" data-th="Cancel/Remove"><span class="bt-content">
                                                                    <a href="javascript:void(0)" class="removelink" title="Rmove" style=""><i class="fa fa-trash"></i></a>
                                                                    <a href="javascript:void(0)" class="cancellink" style="" title="Cancel"><i class="fa fa-remove"></i></a>
                                                                </span></td>

                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row form-group row-space hidden-xs" style="margin-bottom:5px;margin-left: 5px;">
                                        <button type="button" class="btn btn-primary btn-color margin-right-5 btn-sm" id="addrow" style="float:right;">+ <?php echo lang('Purchase_invoice.add_new_item'); ?></button>
                                    </div>

                                    <div class="row form-group row-space">
                                        <label class="col-sm-2 control-label"><?php echo lang('Purchase_invoice.total_qty'); ?></label>
                                        <div class="col-sm-3"><input name="total_qty" class="form-control" type="text" autocomplete="off" value="<?= @$purchase_order->total_qty ?>" id="total_qty" readonly=""></div>
                                    </div>
                                    <div class="row form-group row-space">
                                        <label class="col-sm-2 control-label"><?php echo lang('Purchase_invoice.order_subtotal'); ?></label>
                                        <div class="col-sm-3"><input class="form-control" type="text" autocomplete="off" value="<?= @$purchase_order->subtotal ?>" name="subtotal" id="subtotal" readonly=""></div>
                                    </div>
                                    <div class="row form-group row-space">
                                        <label class="col-sm-2 control-label"><?php echo lang('Purchase_invoice.discount_amount'); ?></label>
                                        <div class="col-sm-3"><input class="form-control" type="text" autocomplete="off" value="<?= @$purchase_order->discount ?>" name="discount" id="totalDiscount" readonly=""></div>
                                    </div>
                                    <div class="row form-group row-space">
                                        <label class="col-sm-2 control-label"><?php echo lang('Purchase_invoice.total_due'); ?></label>
                                        <div class="col-sm-3"><input class="form-control" type="text" autocomplete="off" value="<?= @$purchase_order->total_due ?>" name="total_due" id="total_due" readonly=""></div>
                                    </div>
                                    <div class="row form-group row-space">
                                        <label class="col-sm-2 control-label"><?php echo lang('Purchase_invoice.tax_code'); ?></label>
                                        <div class="col-sm-3">
                                            <select id="tax_code" name="tax_code" class="form-control">
                                                <?php foreach ($taxes as $tax) { ?>
                                                    <option data-val="<?= $tax->tax_rate ?>" value="<?= $tax->tax_code ?>" <?= @$sales_invoice->inv_tax_code == $tax->tax_code ? 'selected' : '' ?>><?= $tax->tax_code ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group row-space">
                                        <label class="col-sm-2 control-label"><?php echo lang('Purchase_invoice.total_tax'); ?></label>
                                        <div class="col-sm-3"><input name="total_tax" class="form-control" type="text" autocomplete="off" value="<?= @$purchase_order->total_tax ?>" id="total_tax" readonly=""></div>
                                    </div>
                                    <div class="row form-group row-space">
                                        <label class="col-sm-2 control-label"><?php echo lang('Purchase_invoice.total_due_tax'); ?></label>
                                        <div class="col-sm-3"><input name="total_due_with_tax" class="form-control" type="text" autocomplete="off" value="<?= @$purchase_order->total_due_with_tax ?>" id="total_due_with_tax" readonly=""></div>
                                    </div>



                                </div>


                            </div>

                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row">


                                <div class="form-group col-md-6">


                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.transfer_no'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control" id="customer_ref_po" name="customer_ref_po" placeholder="" value="<?= @$purchase_order->customer_ref_po ?>">
                                        </div>
                                    </div>
                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.tax_rate'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" placeholder="" value="<?= @$purchase_order->tax_rate ?>">
                                        </div>
                                    </div>

                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.customer_currency_code'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="currency_code" name="currency_code" class="form-control dashboard-dropdown">
                                                <option value="">--Please Select--</option>
                                                <option value="AUD" <?= @$purchase_order->currency_code == 'AUD' ? 'selected' : '' ?>>AUD</option>
                                                <option value="EUR" <?= @$purchase_order->currency_code == 'EUR' ? 'selected' : '' ?>>EUR</option>
                                                <option value="GBP" <?= @$purchase_order->currency_code == 'GBP' ? 'selected' : '' ?>>GBP</option>
                                                <option value="INR" <?= @$purchase_order->currency_code == 'INR' ? 'selected' : '' ?>>INR</option>
                                                <option value="USD" <?= @$purchase_order->currency_code == 'USD' ? 'selected' : '' ?>>USD</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                <div class="form-group col-md-6">


                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.base_currency_code'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control" id="base_currency_code" name="base_currency_code" placeholder="" value="<?= @$purchase_order->base_currency_code ?>">
                                        </div>
                                    </div>
                                    <div class="row form-group row-space ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.exchange_base_currency'); ?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control" id="exchange_rate" name="exchange_rate" placeholder="" value="<?= @$purchase_order->exchange_rate ?>">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.lock_order'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="checkbox" value="1" name="is_locked" id="is_locked" <?= @$purchase_order->is_locked ? 'checked' : '' ?> />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-12">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo lang('Common.submit'); ?></button>
        </form>
    </div>
</div>

<style>
    .row-space {
        margin-top: 25px;
        margin-bottom: 25px;
    }
</style>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script src="/assets/js/purchase_orders.js"></script>
<!-- main content part end -->

<script>
    var baseUrl = "<?php echo base_url(); ?>";

    var is_locked = "<?= @$purchase_order->is_locked ?>";
    var user_role = "<?= session('role') ?>";
    if (is_locked == "1" && (user_role != "1" && user_role != "2")) {
        $(".editlink").addClass("d-none");
        $(".removelink").addClass("d-none");
        $("#addrow").addClass("d-none");
        $("button[type='submit']").addClass("d-none");
        $(".cancellink").addClass("d-none");
        $("input").prop("disabled", true);
        $("textarea").prop("disabled", true);
        $("select.form-control").attr("disabled", true);
        $("select#uuidBusinessIdSwitcher").attr("disabled", false);
    }

    $(document).on("click", ".form-check-input", function() {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });

    $(document).on("change", "#client_id", fillupBillToAddress);
</script>