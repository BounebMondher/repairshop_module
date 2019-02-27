/**
 * Module repairshop
 *
 * @category Prestashop
 * @category Module
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

$(document).ready(function () {
    var payment_button = $("#payment-confirmation button").html();
    $("#checkout-payment-step input").click(function (e) {
        setTimeout(function () {
            if ($("#repair-payment").parent().css('display') == "block") {
                $("#payment-confirmation button").html($("#repair-payment").html());
            } else {
                $("#payment-confirmation button").html(payment_button);
            }
        }, 100);
    })
});

function repairLoadCarrierList() {
    var form = $('#repairForm');
    data = form.serialize();
    //ajax call
    $.ajax({
        type: 'POST',
        url: repairControllerUrl + '&ajax_carrier_list',
        data: data,
        success: function (data) {
            //update id_cart field
            var d = $.parseJSON(data);
            //console.log(data);
            $('#repair_id_cart').val(d.id_cart);
            RepairPopulateSelectCarrier(data);
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('Une erreur est survenue !');
        }
    });
}
function RepairPopulateSelectCarrier(data) {
    //decode jsoon;
    data = $.parseJSON(data);

    if (data['prefered_order']) {
        // get prefered carrier order
        var order = data['prefered_order'].split(',');

        var carrierSelect = $('#repair_carrier_input');
        carrierSelect.html('');

        for (var k = 0; k <= order.length; k++) {
            if ($('#selected_carrier').val() == order[k]) {
                var selected = 'selected';
            } else {
                var selected = '';
            }

            carrierSelect.append('<option value="' + order[k] + '" ' + selected + '>' + data[order[k]]['name'] + ' - ' + data[order[k]]['price'] + ' ' + currency_sign + ' (' + data[order[k]]['taxOrnot'] + ')</option>');
        }

        RepairChangeCarrier();
    }
}
function RepairChangeCarrier() {
    data = $('#repairForm').serialize();
    $.ajax({
        type: 'POST',
        url: repairControllerUrl + '&change_carrier_cart',
        data: data,
        success: function (data) {
            if (data != '') {
                var data = $.parseJSON(data);
                $('#repairTotalRepairWithTax').html(formatCurrency(data.total_price, currency_format, currency_sign, currency_blank));
                $('#repairTotalRepair').html(formatCurrency(data.total_price_without_tax, currency_format, currency_sign, currency_blank));
                $('#repairTotalTax').html(formatCurrency(data.total_tax, currency_format, currency_sign, currency_blank));
                $('#repairTotalDiscounts').html(formatCurrency(data.total_discounts, currency_format, currency_sign, currency_blank));
                if (priceDisplay == 1)
                    $('#repairTotalShipping').html(formatCurrency(data.total_shipping_tax_exc, currency_format, currency_sign, currency_blank));
                else
                    $('#repairTotalShipping').html(formatCurrency(data.total_shipping, currency_format, currency_sign, currency_blank));
            }
        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert('Une erreur est survenue !');
        }
    });
}
