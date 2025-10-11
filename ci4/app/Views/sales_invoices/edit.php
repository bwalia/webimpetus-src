<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<?php
$customers = getResultArray("customers");
$templates = getResultArray("templates", ["module_name" => $tableName]);
$items = getResultArray("sales_invoice_items", ["sales_invoices_uuid" => @$sales_invoice->uuid], false);
$notes = getResultArray("sales_invoice_notes", ["sales_invoices_uuid" => @$sales_invoice->uuid], false);
$business = getRowArray("businesses", ["uuid_business_id" => session('uuid_business')], false);
$taxes = getResultArray("taxes", ["uuid_business_id" => session('uuid_business')], false);
?>

<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <input type="hidden" value="<?= @$sales_invoice->uuid ?>" name="uuid" id="mainTableId">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><?php echo lang('Purchase_invoice.invoice_details');?></a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?php echo lang('Purchase_invoice.invoice_settings');?></a>
                            <a class="nav-item nav-link" id="nav-notes-tab" data-toggle="tab" href="#nav-notes" role="tab" aria-controls="nav-notes" aria-selected="false"><?php echo lang('Common.notes');?></a>


                        </div>
                    </nav>
                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.invoice_number');?>*</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input readonly type="text" class="form-control" value="<?= @$sales_invoice->invoice_number ?>" id="invoice_number" name="invoice_number" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.terms');?>* </label>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="terms" id="terms" class="form-control dashboard-dropdown">
                                                <option value="">--Please Select--</option>
                                                <option value="Net 15" <?= @$sales_invoice->terms == 'Net 15' ? 'selected' : '' ?>>Net 15</option>
                                                <option value="Net 20" <?= @$sales_invoice->terms == 'Net 20' ? 'selected' : '' ?>>Net 20</option>
                                                <option value="Net 30" <?= @$sales_invoice->terms == 'Net 30' ? 'selected' : '' ?>>Net 30</option>
                                                <option value="Upon receipt" <?= @$sales_invoice->terms == 'Upon receipt' ? 'selected' : '' ?>>Upon receipt</option>
                                                <option value="Due On Receipt" <?= @$sales_invoice->terms == 'Due On Receipt' ? 'selected' : '' ?>>Due On Receipt</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.client');?>* </label>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="client_id" name="client_id" class="form-control required dashboard-dropdown">
                                                <option value="" selected="">--Select--</option>
                                                <?php foreach ($customers as $row) : ?>
                                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$sales_invoice->client_id) {
                                                                                            echo "selected";
                                                                                        } ?>><?= $row['company_name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.bill_to');?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <textarea name="bill_to" class="form-control"><?= @$sales_invoice->bill_to ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.order_by');?> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control" value="<?= @$sales_invoice->order_by ?>" id="order_by" name="order_by" placeholder="">
                                        </div>
                                    </div>

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Common.notes');?> </label>
                                        </div>
                                        <div class="col-md-6">

                                            <textarea autocomplete="off" class="form-control" name="notes"><?= @$sales_invoice->notes ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="tags">Tags</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control" value="<?= @$sales_invoice->tags ?>" id="tags" name="tags" placeholder="e.g., urgent, project-x, quarterly">
                                            <small class="form-text text-muted">Enter tags separated by commas</small>
                                        </div>
                                    </div>

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.project_code');?>* </label>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="project_code" id="project_code" class=" required form-control">
                                                <option value="">--Please Select--</option>
                                                <option value="4D" <?= @$sales_invoice->project_code == '4D' ? 'selected' : '' ?>>4D</option>
                                                <option value="CatBase" <?= @$sales_invoice->project_code == 'CatBase' ? 'selected' : '' ?>> CatBase</option>
                                                <option value="Cloud Consultancy" <?= @$sales_invoice->project_code == 'Cloud Consultancy' ? 'selected' : '' ?>> Cloud Consultancy</option>
                                                <option value="Cloud Native Engineering" <?= @$sales_invoice->project_code == 'Cloud Native Engineering' ? 'selected' : '' ?>> Cloud Native Engineering</option>
                                                <option value="Database" <?= @$sales_invoice->project_code == 'Database' ? 'selected' : '' ?>> Database</option>
                                                <option value="Domains" <?= @$sales_invoice->project_code == 'Domains' ? 'selected' : '' ?>> Domains</option>
                                                <option value="IMG2D" <?= @$sales_invoice->project_code == 'IMG2D' ? 'selected' : '' ?>> IMG2D</option>
                                                <option value="IT Consulting" <?= @$sales_invoice->project_code == 'IT Consulting' ? 'selected' : '' ?>> IT Consulting</option>
                                                <option value="Jobshout" <?= @$sales_invoice->project_code == 'Jobshout' ? 'selected' : '' ?>> Jobshout</option>
                                                <option value="Mobile App" <?= @$sales_invoice->project_code == 'Mobile App' ? 'selected' : '' ?>> Mobile App</option>
                                                <option value="Mobile Friendly Website" <?= @$sales_invoice->project_code == 'Mobile Friendly Website' ? 'selected' : '' ?>> Mobile Friendly Website</option>
                                                <option value="Nginx" <?= @$sales_invoice->project_code == 'Nginx' ? 'selected' : '' ?>> Nginx</option>
                                                <option value="Time-Based" <?= @$sales_invoice->project_code == 'Time-Based' ? 'selected' : '' ?>> Time-Based</option>
                                                <option value="TIZO" <?= @$sales_invoice->project_code == 'TIZO' ? 'selected' : '' ?>> TIZO</option>
                                                <option value="WEBSITE" <?= @$sales_invoice->project_code == 'WEBSITE' ? 'selected' : '' ?>> WEBSITE</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>


                                <div class="col-md-6">

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="customInvoiceNumber"><?php echo lang('Purchase_invoice.custom_invoice_number');?>*</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control required" value="<?= empty(@$sales_invoice->custom_invoice_number) ? @$business->business_code .  @$sales_invoice->invoice_number : @$sales_invoice->custom_invoice_number ?>" id="custom_invoice_number" name="custom_invoice_number" placeholder="">
                                        </div>
                                    </div>

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Common.date');?>*</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control datepicker" value="<?= render_date(@$sales_invoice->date) ?>" id="date" name="date" placeholder="">
                                        </div>
                                    </div>

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputEmail4"><?php echo lang('Purchase_invoice.due_date');?>*</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control datepicker" id="due_date" name="due_date" placeholder="" value="<?= render_date(@$sales_invoice->due_date) ?>">
                                        </div>
                                    </div>

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.balance_outstanding');?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input readonly type="input" class="form-control" id="balance_due" name="balance_due" placeholder="" value="<?= @$sales_invoice->balance_due ?>">
                                        </div>
                                    </div>

                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4">Status</label>
                                        </div>
                                        <div class="col-md-6">
                                            <select name="status" id="status" class="form-control dashboard-dropdown">
                                                <option value="">--Please Select--</option>
                                                <option value="Invoiced" <?= @$sales_invoice->status == 'Invoiced' ? 'selected' : '' ?>>Invoiced</option>
                                                <option value="Paid" <?= @$sales_invoice->status == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                <option value="Bad debt" <?= @$sales_invoice->status == 'Bad debt' ? 'selected' : '' ?>>Bad debt</option>
                                                <option value="Needs chasing" <?= @$sales_invoice->status == 'Needs chasing' ? 'selected' : '' ?>>Needs chasing</option>
                                                <option value="Credit" <?= @$sales_invoice->status == 'Credit' ? 'selected' : '' ?>>Credit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.grand_total');?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input readonly type="total" class="form-control" id="total" name="total" placeholder="" value="<?= @$sales_invoice->total ?>">
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.total_paid');?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="total_paid" class="form-control" id="total_paid" name="total_paid" placeholder="" value="<?= @$sales_invoice->total_paid ?>">
                                        </div>
                                    </div>
                                    <div class="row form-group ">
                                        <div class="col-md-4">
                                            <label for="inputPassword4"><?php echo lang('Purchase_invoice.paid_date');?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" autocomplete="off" class="form-control datepicker" id="paid_date" name="paid_date" placeholder="" value="<?= render_date(@$sales_invoice->paid_date) ?>">
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
                                                        <th data-th="Item"><span class="bt-content"><?php echo lang('Common.item');?></span></th>
                                                        <th data-th="Description"><span class="bt-content"><?php echo lang('Common.description');?></span></th>
                                                        <th data-th="Rate"><span class="bt-content"><?php echo lang('Common.rate');?></span></th>
                                                        <th data-th="Hours"><span class="bt-content"><?php echo lang('Common.hours');?></span></th>
                                                        <th data-th="Amount"><span class="bt-content"><?php echo lang('Common.amount');?></span></th>
                                                        <th class="td_edit" data-th="Edit/Save"><span class="bt-content"><?php echo lang('Common.edit');?>/<?php echo lang('Common.save');?></span></th>
                                                        <th class="td_remove" data-th="Cancel/Remove"><span class="bt-content"><?php echo lang('Common.cancel');?>/<?php echo lang('Common.remove');?></span></th>
                                                    </tr>

                                                    <!-- <tr class="item-row"><td class="item-id"><span class="item_id"></span><input class="item_uuid" type="hidden"></td><td><span class="s_description" style="display:none"></span><textarea class="description form-control"></textarea></td><td><span class="s_rate" style="display:none"></span><input type="text" class="rate num form-control" value="0" style="width:50px"></td><td><span class="s_hours" style="display:none"></span><input type="text" class="hours num form-control" value="0" style="width:50px"></td><td><span class="price">0</span></td><td><a href="javascript:void(0)" class="editlink" style="display:none " title="Edit"><i class="fa fa-pencil"></i></a><a href="javascript:void(0)" class="savelink" title="Save"><i class="fa fa-save"></i></a></td><td><a href="javascript:void(0)" class="removelink" style="display:none" title="Remove"><i class="fa fa-trash"></i></a><a href="javascript:void(0)" class="cancellink" title="Cancel"><i class="ti-close"></i></a></td></tr> -->

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
                                                                    <input type="text" class="rate num form-control" style="display: none;" value="<?= $eachItems->rate ?>">
                                                                </span></td>
                                                            <td data-th="Hours"><span class="bt-content">
                                                                    <span class="s_hours" style="display: inline;"><?= $eachItems->hours ?></span>
                                                                    <input type="text" class="hours num form-control" style="display: none;" value="<?= $eachItems->hours ?>">
                                                                </span></td>
                                                            <td data-th="Amount"><span class="bt-content">
                                                                    <span class="price"><?= $eachItems->amount ?></span>
                                                                </span></td>

                                                            <td class="td_edit" data-th="Edit/Save"><span class="bt-content">

                                                                    <a href="javascript:void(0)" class="editlink btn btn-success" title="Edit" style=""><i class="fa fa-edit"></i></a>

                                                                    <a href="javascript:void(0)" class="savelink btn btn-success" style="display:none" title="" aria-describedby="ui-tooltip-1"><i class="fa fa-save"></i></a>
                                                                </span></td>
                                                            <td class="td_remove" data-th="Cancel/Remove"><span class="bt-content">
                                                                    <a href="javascript:void(0)" class="removelink btn btn-danger" title="Rmove" style=""><i class="fa fa-trash"></i></a>
                                                                    <a href="javascript:void(0)" class="cancellink btn btn-danger" style="" title="Cancel"><i class="ti-close"></i></a>
                                                                </span>
                                                            </td>

                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row form-group hidden-xs" style="margin-bottom:20px;">
                                        <button type="button" class="btn btn-primary btn-color margin-right-5 btn-sm" id="addrow" style="float:right; margin-left: 14px;">+ <?php echo lang('Purchase_invoice.add_invoice_item');?></button>
                                    </div>

                                    <div class="row form-group">
                                        <label class="col-md-4 control-label"><?php echo lang('Purchase_invoice.tax_code');?></label>
                                        <div class="col-md-6">
                                            <select id="inv_tax_code" name="inv_tax_code" class="form-control dashboard-dropdown">
                                                <?php foreach ($taxes as $tax) { ?>
                                                    <option data-val="<?= $tax->tax_rate ?>" value="<?= $tax->tax_code ?>" <?= @$sales_invoice->inv_tax_code == $tax->tax_code ? 'selected' : '' ?>><?= $tax->tax_code ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.total_hours');?></label>
                                        <div class="col-sm-6"><input name="total_hours" class="form-control" type="text" value="<?= @$sales_invoice->total_hours ?>" id="total_hours" readonly=""></div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.total_due');?></label>
                                        <div class="col-sm-6"><input class="form-control" type="text" value="<?= @$sales_invoice->total_due ?>" name="total_due" id="total_due" readonly=""></div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.total_tax');?></label>
                                        <div class="col-sm-6"><input name="total_tax" class="form-control" type="text" value="<?= @$sales_invoice->total_tax ?>" id="total_tax" readonly=""></div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.total_due_tax');?></label>
                                        <div class="col-sm-6"><input name="total_due_with_tax" class="form-control" type="text" value="<?= @$sales_invoice->total_due_with_tax ?>" id="total_due_with_tax" readonly=""></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row">


                                <div class="form-group col-md-6">
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.invoice_pin');?></label>
                                        <div class="col-sm-6">
                                            <input type="input" autocomplete="off" class="form-control" id="payment_pin_or_passcode" name="payment_pin_or_passcode" placeholder="" value="<?= @$sales_invoice->payment_pin_or_passcode ?>">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.tax_rate');?></label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control" id="invoice_tax_rate" name="invoice_tax_rate" placeholder="" value="<?= @$sales_invoice->invoice_tax_rate ?>">
                                        </div>
                                    </div>



                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.invoice_type');?></label>
                                        <div class="col-sm-6">
                                            <select name="inv_template" id="inv_template" class="form-control dashboard-dropdown">
                                                <option value="">--Please Select--</option>
                                                <option value="TimeBilling" <?= @$sales_invoice->inv_template == 'TimeBilling' ? 'selected' : '' ?>>TimeBilling</option>
                                                <option value="Monthly Contract" <?= @$sales_invoice->inv_template == 'Monthly Contract' ? 'selected' : '' ?>>Monthly Contract</option>
                                                <option value="Day Rate" <?= @$sales_invoice->inv_template == '"Day Rate' ? 'selected' : '' ?>>Day Rate</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.invoice_template');?></label>
                                        <div class="col-sm-6">
                                            <select id="print_template_code" name="print_template_code" class="form-control  dashboard-dropdown">
                                                <option value="" selected="">--Please Selected--</option>
                                                <?php foreach ($templates as $row) : ?>
                                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$sales_invoice->print_template_code) {
                                                                                            echo "selected";
                                                                                        } ?>><?= $row['code']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.internal_notes');?></label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="internal_notes"><?= @$sales_invoice->internal_notes ?></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group col-md-6">
                                    <div class="row form-group">

                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.transfer_no');?></label>
                                        <div class="col-sm-6">
                                            <input type="text" autocomplete="off" class="form-control" id="inv_customer_ref_po" name="inv_customer_ref_po" placeholder="" value="<?= @$sales_invoice->inv_customer_ref_po ?>">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.customer_currency_code');?></label>
                                        <div class="col-sm-6">
                                            <select id="currency_code" name="currency_code" class="form-control dashboard-dropdown">
                                                <option value="">--Please Select--</option>
                                                <option value="AUD" <?= @$sales_invoice->currency_code == 'AUD' ? 'selected' : '' ?>>AUD</option>
                                                <option value="EUR" <?= @$sales_invoice->currency_code == 'EUR' ? 'selected' : '' ?>>EUR</option>
                                                <option value="GBP" <?= @$sales_invoice->currency_code == 'GBP' ? 'selected' : '' ?>>GBP</option>
                                                <option value="INR" <?= @$sales_invoice->currency_code == 'INR' ? 'selected' : '' ?>>INR</option>
                                                <option value="USD" <?= @$sales_invoice->currency_code == 'USD' ? 'selected' : '' ?>>USD</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.base_currency_code');?></label>
                                        <div class="col-sm-6">
                                            <input type="text" autocomplete="off" class="form-control" id="base_currency_code" name="base_currency_code" placeholder="" value="<?= @$sales_invoice->base_currency_code ?>">

                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.exchange_base_currency');?></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="inv_exchange_rate" name="inv_exchange_rate" placeholder="" value="<?= @$sales_invoice->inv_exchange_rate ?>">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-4 control-label"><?php echo lang('Purchase_invoice.lock_invoice');?></label>
                                        <div class="col-sm-6">
                                            <input type="checkbox" value="1" name="is_locked" id="is_locked" <?= @$sales_invoice->is_locked ? 'checked' : '' ?> />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-notes" role="tabpanel" aria-labelledby="nav-notes-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="render-notes">
                                        <?php foreach ($notes as $eachNotes) { ?>
                                            <div class="form-group each-notes-div-<?php echo $eachNotes->id; ?>">
                                                <label for="inputEmail4" class="notes-lebel"><?php echo getUserInfo()->name . ' (' . $eachNotes->created_at . " )"; ?></label>
                                                <button type="button" data-id="<?php echo $eachNotes->id; ?>" class="btn btn-danger btn-color btn-sm float-right delete-note" style="" onclick="deleteNote(<?php echo $eachNotes->id; ?>)">Delete Note</button>
                                                <textarea name="" class="form-control each-notes" id="" data-id="<?php echo $eachNotes->id; ?>" cols="10" rows="5"><?= $eachNotes->notes; ?></textarea>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <button type="button" id="addNote" class="btn btn-primary btn-color btn-sm" style="margin-bottom:10px;margin-left: 5px" onclick="addCustomerNote()"><?php echo lang('Purchase_invoice.add_note');?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-12">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo lang('Common.submit');?></button>
        </form>
    </div>
</div>
<style>
    .row.form-group {
        margin-top: 30px;
    }
</style>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script src="/assets/js/sales_invoices.js"></script>
<!-- main content part end -->

<script>
    var baseUrl = "<?php echo base_url(); ?>";

    var is_locked = "<?= @$sales_invoice->is_locked ?>";
    var user_role = "<?= session('role') ?>";
    if (is_locked == "1" && (user_role != "1" && user_role != "2")) {
        $(".editlink").addClass("d-none");
        $(".removelink").addClass("d-none");
        $("#addrow").addClass("d-none");
        $("button[type='submit']").addClass("d-none");
        $(".delete-note").addClass("d-none");
        $("#addNote").addClass("d-none");
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
    $(document).on("change", "#terms", calculateDueDate);
</script>