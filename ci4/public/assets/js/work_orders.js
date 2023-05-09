var mainTableId = $("#mainTableId").val();

$(document).on("click", ".savelink", function () {

    var current = $(this);
    var description = $(this).closest(".item-row").find('.description').val();
    var rate = $(this).closest(".item-row").find('.rate').val();
    var qty = parseInt($(this).closest(".item-row").find('.qty').val());
    var discount = $(this).closest(".item-row").find('.discount').val();
    var id = $(this).closest(".item-row").attr('id');
    var mainTableId = $("#mainTableId").val();

    $.ajax({
        url: baseUrl + "/work_orders/addInvoiceItem",
        data: { id: id, description: description, rate: rate, qty: qty, discount: discount, mainTableId: mainTableId },
        method: 'post',
        success: function (res) {

            var obj = JSON.parse(res);
            if (obj.status) {

                current.closest(".item-row").attr("id", obj.data.id);
                current.closest(".item-row").find("[name='item_id[]']").val(obj.data.id);
                current.closest(".item-row").find(".item_id").text(obj.data.id);
                current.closest(".item-row").find(".s_description").text(obj.data.description);
                current.closest(".item-row").find(".s_description").show();
                current.closest(".item-row").find(".description").hide();

                current.closest(".item-row").find(".s_rate").show();
                current.closest(".item-row").find(".rate").hide();
                current.closest(".item-row").find(".s_rate").text(obj.data.rate);

                current.closest(".item-row").find(".s_qty").show();
                current.closest(".item-row").find(".qty").hide();
                current.closest(".item-row").find(".s_qty").text(obj.data.qty);

                current.closest(".item-row").find(".s_discount").show();
                current.closest(".item-row").find(".discount").hide();
                current.closest(".item-row").find(".s_discount").text(obj.data.discount);

                current.closest(".item-row").find(".price").show();
                current.closest(".item-row").find(".price").text(obj.data.amount);

                current.closest(".item-row").find(".savelink").hide();
                current.closest(".item-row").find(".editlink").show();

                current.closest(".item-row").find(".cancellink").hide();
                current.closest(".item-row").find(".removelink").show();

                calculationAmount(true);
            }

        }
    })
});

$(document).on("click", ".editlink", function () {

    var current = $(this);
    var description = $(this).closest(".item-row").find('.description').val();
    var rate = $(this).closest(".item-row").find('.rate').val();
    var discount = $(this).closest(".item-row").find('.discount').val();
    var qty = parseInt($(this).closest(".item-row").find('.qty').val());
    var id = $(this).closest(".item-row").attr('id');
    var mainTableId = $("#mainTableId").val();

    current.closest(".item-row").find(".s_description").hide();
    current.closest(".item-row").find(".description").show();

    current.closest(".item-row").find(".s_rate").hide();
    current.closest(".item-row").find(".rate").show();
    current.closest(".item-row").find(".s_qty").hide();
    current.closest(".item-row").find(".qty").show();

    current.closest(".item-row").find(".s_discount").hide();
    current.closest(".item-row").find(".discount").show();

    current.closest(".item-row").find(".savelink").show();
    current.closest(".item-row").find(".editlink").hide();

    current.closest(".item-row").find(".cancellink").show();
    current.closest(".item-row").find(".removelink").hide();
});

$(document).on("click", ".removelink", function () {

    var current = $(this);
    var id = $(this).closest(".item-row").attr('id');
    var mainTableId = $("#mainTableId").val();

    $.ajax({
        url: baseUrl + "/work_orders/removeInvoiceItem",
        data: { id: id, mainTableId: mainTableId },
        method: 'post',
        success: function (res) {

            var obj = JSON.parse(res);
            if (obj.status) {

                current.closest(".item-row").remove();

                calculationAmount(true);

            }

        }
    })
});

$(document).on("change", "#tax_code", function () {
    calculationAmount(false)
})

function calculationAmount(saveData = true) {
    var totalQty = 0;
    var mainTableId = $("#mainTableId").val();
    var totalAmount = 0;
    var tax = 0;
    var totalDiscount = 0;
    var subTotal = 0;

    $("#table-breakpoint .item-row").each(function () {

        var rate = parseFloat($(this).find(".rate").val());
        var qty = parseInt($(this).find(".qty").val());
        var discount = parseFloat($(this).find(".discount").val());
        var discount_amount = (rate * qty) / 100 * discount;
        var amount = (rate * qty) - discount_amount;
        subTotal += rate * qty;

        totalQty += qty;
        totalAmount += amount;
        totalDiscount += discount_amount;
        $(this).find(".price").val(amount);
    });

    let inv_tax_code_val = parseFloat($("#tax_code").find(':selected').data('val'));
    var tax = (totalAmount / 100) * inv_tax_code_val;
    var totalAmountWithTax = totalAmount + tax;

    totalAmount = totalAmount.toFixed(2);
    tax = tax.toFixed(2);
    totalAmountWithTax = totalAmountWithTax.toFixed(2);
    totalDiscount = totalDiscount.toFixed(2);
    subTotal = subTotal.toFixed(2);

    $("#total_due").val(totalAmount);
    $("#total_qty").val(totalQty);
    $("#total_tax").val(tax);
    $("#total_due_with_tax").val(totalAmountWithTax);
    $("#balance_due").val(totalAmountWithTax);
    $("#total").val(totalAmountWithTax);
    $("#totalDiscount").val(totalDiscount);
    $("#subtotal").val(subTotal);

    if (saveData) {
        $.ajax({
            url: baseUrl + "/work_orders/updateInvoice",
            data: { totalAmount: totalAmount, mainTableId: mainTableId, totalQty: totalQty, totalAmountWithTax: totalAmountWithTax, total_tax: tax, subtotal: subTotal, discount: totalDiscount },
            method: 'post',
            success: function (res) {


            }
        })
    }
}

$(document).on("click", "#addrow", function () {
    var html = '<tr class="item-row"><td class="item-id"><span class="item_id"></span><input name="item_id[]" type="hidden"></td><td><span class="s_description" style="display:none"></span><textarea maxlength="1023" class="description form-control"></textarea></td><td><span class="s_rate" style="display:none;width:100%"></span><input type="text" class="rate num form-control" value="0" style="width:100%"></td><td><span class="s_qty" style="display:none;width:100%"></span><input type="text" class="qty num form-control" value="0" style="width:100%"></td><td><span class="s_discount" style="display:none"></span><input type="text" class="discount num form-control" value="0" style="width:100%"></td><td><span class="price">0</span></td><td><a href="javascript:void(0)" class="editlink" style="display:none " title="Edit"><i class="fa fa-edit"></i></a><a href="javascript:void(0)" class="savelink" title="Save"><i class="fa fa-save"></i></a></td><td><a href="javascript:void(0)" class="removelink" style="display:none" title="Remove"><i class="fa fa-trash"></i></a><a href="javascript:void(0)" class="cancellink" title="Cancel"><i class="fa fa-remove"></i></a></td></tr>';

    $('#table-breakpoint tr:last').after(html);
    $('#table-breakpoint .rate').css("width", "100%");
    $('#table-breakpoint .qty').css("width", "100%");
    $('#table-breakpoint .discunt').css("width", "100%");

});


function addCustomerNote() {
    var html = '<div class="form-group"><label class="notes-lebel" for="inputEmail4"></label><textarea name="" class="form-control each-notes" id="" cols="10" rows="5"></textarea></div>';

    $('.render-notes').append(html);
}
function deleteNote(id) {

    var current = $(this);

    $.ajax({
        url: baseUrl + "/work_orders/deleteNote",
        data: { id: id },
        method: 'post',
        success: function (res) {

            var obj = JSON.parse(res);
            if (obj.status) {

                $(".each-notes-div-" + id).remove();
            }
        }
    })
}
$(document).on("change", ".each-notes", function () {
    var notes = $(this).val();
    var id = $(this).attr('data-id');
    var current = $(this);

    $.ajax({
        url: baseUrl + "/work_orders/saveNotes",
        data: { id: id, notes: notes, mainTableId: mainTableId },
        method: 'post',
        success: function (res) {

            var obj = JSON.parse(res);
            console.log(id)
            if (id === undefined) {

                var text = obj.name + " (" + obj.data.created_at + " )";
                current.closest(".render-notes").find(".notes-lebel").text(text);

                var deleteText = ' <button type="button" id="add" data-id="' + obj.data.id + '" class="btn btn-danger btn-color btn-sm float-right" style="" onclick="deleteNote(' + obj.data.id + ')">Delete Note</button>';

                current.closest(".render-notes").find(".notes-lebel").after(deleteText);
                current.closest(".form-group").addClass("each-notes-div-" + obj.data.id);
                current.attr("data-id", obj.data.id);
            }
        }
    })
})


