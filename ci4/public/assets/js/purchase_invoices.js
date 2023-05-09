var mainTableId = $("#mainTableId").val();

$(document).on("click", ".savelink", function () {

    var current = $(this);
    var description = $(this).closest(".item-row").find('.description').val();
    var rate = $(this).closest(".item-row").find('.rate').val();
    var hours = $(this).closest(".item-row").find('.hours').val();
    var id = $(this).closest(".item-row").attr('id');
    var mainTableId = $("#mainTableId").val();

    $.ajax({
        url: baseUrl + "/purchase_invoices/addInvoiceItem",
        data: { id: id, description: description, rate: rate, hours: hours, mainTableId: mainTableId },
        method: 'post',
        success: function (res) {

            var obj = JSON.parse(res);
            if (obj.status) {

                current.closest(".item-row").attr("id", obj.data.id);
                current.closest(".item-row").find("[name='item_id[]']").val(obj.data.id);
                current.closest(".item-row").attr("id", obj.data.id);
                current.closest(".item-row").find(".item_id").text(obj.data.id);
                current.closest(".item-row").find(".s_description").text(obj.data.description);
                current.closest(".item-row").find(".s_description").show();
                current.closest(".item-row").find(".description").hide();

                current.closest(".item-row").find(".s_rate").show();
                current.closest(".item-row").find(".rate").hide();
                current.closest(".item-row").find(".s_rate").text(obj.data.rate);

                current.closest(".item-row").find(".s_hours").show();
                current.closest(".item-row").find(".hours").hide();
                current.closest(".item-row").find(".s_hours").text(obj.data.hours);

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
    var hours = $(this).closest(".item-row").find('.hours').val();
    var id = $(this).closest(".item-row").attr('id');
    var mainTableId = $("#mainTableId").val();

    current.closest(".item-row").find(".s_description").hide();
    current.closest(".item-row").find(".description").show();

    current.closest(".item-row").find(".s_rate").hide();
    current.closest(".item-row").find(".rate").show();

    current.closest(".item-row").find(".s_hours").hide();
    current.closest(".item-row").find(".hours").show();

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
        url: baseUrl + "/purchase_invoices/removeInvoiceItem",
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

$(document).on("change", "#inv_tax_code", function () {
    calculationAmount(false)
})

function calculationAmount(saveData = true) {

    var totalHour = 0;
    var mainTableId = $("#mainTableId").val();
    var totalAmount = 0;
    var tax = 0;

    $("#table-breakpoint .item-row").each(function () {

        var rate = parseFloat($(this).find(".rate").val());
        var hours = parseFloat($(this).find(".hours").val());

        totalHour += hours;
        var amount = rate * hours;
        totalAmount += amount;
        $(this).find(".price").val(amount);
    });

    let inv_tax_code_val = parseFloat($("#inv_tax_code").find(':selected').data('val'));
    var tax = (totalAmount / 100) * inv_tax_code_val;
    var totalAmountWithTax = totalAmount + tax;

    totalAmount = totalAmount.toFixed(2);
    totalHour = totalHour.toFixed(2);
    tax = tax.toFixed(2);
    totalAmountWithTax = totalAmountWithTax.toFixed(2);

    $("#total_due").val(totalAmount);
    $("#total_hours").val(totalHour);
    $("#total_tax").val(tax);
    $("#total_due_with_tax").val(totalAmountWithTax);
    $("#balance_due").val(totalAmountWithTax);
    $("#total").val(totalAmountWithTax);

    if (saveData) {

        $.ajax({
            url: baseUrl + "/purchase_invoices/updateInvoice",
            data: { totalAmount: totalAmount, mainTableId: mainTableId, totalHour: totalHour, totalAmountWithTax: totalAmountWithTax, total_tax: tax },
            method: 'post',
            success: function (res) {


            }
        })
    }



}
$(document).on("click", "#addrow", function () {
    var html = '<tr class="item-row"><td class="item-id"><span class="item_id"></span><input name="item_id[]" type="hidden"></td><td><span class="s_description" style="display:none"></span><textarea maxlength="1023" class="description form-control"></textarea></td><td><span class="s_rate" style="display:none"></span><input type="text" class="rate num form-control" value="0" style="width:50px"></td><td><span class="s_hours" style="display:none"></span><input type="text" class="hours num form-control" value="0" style="width:50px"></td><td><span class="price">0</span></td><td><a href="javascript:void(0)" class="editlink" style="display:none " title="Edit"><i class="fa fa-edit"></i></a><a href="javascript:void(0)" class="savelink" title="Save"><i class="fa fa-save"></i></a></td><td><a href="javascript:void(0)" class="removelink" style="display:none" title="Remove"><i class="fa fa-trash"></i></a><a href="javascript:void(0)" class="cancellink" title="Cancel"><i class="fa fa-remove"></i></a></td></tr>';

    $('#table-breakpoint tr:last').after(html);

});


function addCustomerNote() {
    var html = '<div class="form-group"><label class="notes-lebel" for="inputEmail4"></label><textarea name="" class="form-control each-notes" id="" cols="10" rows="5"></textarea></div>';

    $('.render-notes').append(html);
}
function deleteNote(id) {

    var current = $(this);

    $.ajax({
        url: baseUrl + "/purchase_invoices/deleteNote",
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
        url: baseUrl + "/purchase_invoices/saveNotes",
        data: { id: id, notes: notes, mainTableId: mainTableId },
        method: 'post',
        success: function (res) {

            var obj = JSON.parse(res);
            console.log(id)
            if (id === undefined) {

                var text = obj.name + " (" + obj.data.created_at + " )";
                current.closest(".render-notes").find(".notes-lebel").text(text);

                if ($(".each-notes-div-" + obj.data.id + " #add").length == 0) {
                    var deleteText = ' <button type="button" id="add" data-id="' + obj.data.id + '" class="btn btn-danger btn-color btn-sm float-right" style="" onclick="deleteNote(' + obj.data.id + ')">Delete Note</button>';

                    current.closest(".form-group").find(".notes-lebel").after(deleteText);
                }

                current.closest(".form-group").addClass("each-notes-div-" + obj.data.id);
                current.attr("data-id", obj.data.id);
            }
        }
    })
})


