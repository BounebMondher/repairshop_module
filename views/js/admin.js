/**
 * Module repairshop
 *
 * @category Prestashop
 * @category Module
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

$(document).ready(function(){
	//autocomplete product
	$('#repair_product_autocomplete_input').autocomplete(
                'index.php?controller=AdminRepairs&ajax_product_list',
                {
		minChars: 1,
		autoFill: true,
		max:200,
		matchContains: true,
		scroll:false,
		cacheLength:0,
		formatItem: function(item) {
			return item[0]+' - '+item[1]+' - '+item[2];
		},
                extraParams: {
                    token: token,
                    id_customer: function() {return $('#repair_customer_id').val()}
		}
	}).result(function(e,i){
               console.log(i);
		if(i != undefined)
                    RepairAddProductToRepair(i[0],i[1],i[2],1,0,i[3]);
		$(this).val('');
	});

	//autocomplete customer
	$('#repair_customer_autocomplete_input').autocomplete(
                'index.php?controller=AdminRepairs&ajax_customer_list&token='+token,
                {
                    minChars: 1,
                    autoFill: true,
                    max:200,
                    matchContains: true,
                    scroll:false,
                    dataType: 'json',
                    cacheLength:0,
                    formatItem: function(data, i, max, value, term) {
                            return value;
                    },
                    parse: function(data) {
                        //console.log(data);
                            var mytab = new Array();
                            for (var i = 0; i < data.length; i++)
                                    mytab[mytab.length] = { data: data[i], value: (data[i].id_customer + ' - ' + data[i].lastname + ' - ' + data[i].firstname).trim() };
                            return mytab;
                    }
                }).result(function(e,i){
		if(i != undefined)
                    RepairAddCustomerToRepair(i['id_customer'], i['lastname'],i['firstname']);
		$(this).val('');
	});

	$('#repair_refresh_carrier_list').click(function(e) {
		repairLoadCarrierList();
		e.preventDefault();
	});

	$('#repair_refresh_total_repair').click(function(e) {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $(this).addClass('disabled');
		RepairCalcTotalRepair();
		e.preventDefault();
	});

	$('#repair_select_cart_rules').change(function(e) {

                $('#repairCartRulesMsgError').hide('fast');
		if($(this).val()!="-1") {
			if($('#trCartRule_'+$(this).val()).length>0) {
				alert('Cart rule already added');
				return false;
			}
			$.ajax({
			type : 'POST',
			url : 'index.php?controller=AdminRepairs&ajax_load_cart_rule&token='+token,
                        data: $('#repairForm').serialize()+'&id_cart_rule='+$(this).val(),
			success : function(data){
				data=$.parseJSON(data);
                                if(!data.id) {
                                    repairShowCartRuleErrors(data);
                                }
                                else {
                                    //console.log(id_lang_default);
                                    RepairAddRuleToQuotation(data.id,data.name[id_lang_default],data.description,data.code,data.free_shipping,data.reduction_percent,data.reduction_amount,'0',data.gift_product);
                                }
                            }, error : function(XMLHttpRequest, textStatus, errorThrown) {
			   alert('Une erreur est survenue !');
			}
		});
		}
		repairLoadCarrierList();
		e.preventDefault();
	});

        
        $('.upload_attachement').on('click', function(e) {
            repairdeleteupload(this);
            e.preventDefault();
	});

})

function repairShowCartRuleErrors(msg) {
    $('#repairCartRulesMsgError').html(msg);
    $('#repairCartRulesMsgError').show('slow');
}

function repairAutoChangePrice(currentInput,inputClass) {
    $('.'+inputClass).each(function() {
        $(this).val(currentInput.value);
    })
}

var boolForLine=1;
function RepairAddProductToRepair(prodId,prodName,prodPrice,qty,idAttribute,specificPrice,yourPrice,customization_datas_json) {
        RepairToggleSubmitBtn(0);
        var id_attribute = (idAttribute == null)?idAttribute:null;
        var specificPrice = (specificPrice != undefined)?specificPrice:'';
        var specificQty = (specificQty != undefined)?specificQty:'';
        var yourPrice = (yourPrice != undefined)?yourPrice:'';

	randomId=new Date().getTime();
        boolForLine = (boolForLine == 1)?0:1;
        var customization_datas = $.parseJSON(customization_datas_json);
        //console.log(customization_datas);
        var displayedCustomizationDatas = '';
        var qtyInputType = 'text';
        var onChangeCustomizationPrice ='';
        var customPriceClass=''
        if(customization_datas) {
            for (var i = 0; i < customization_datas.length; i++){
                displayedCustomizationDatas+='<tr class="trAdminCustomizationData"><td colspan="6" class="tdAdminCustomizationDataValue">';
                var customization_datas_array = customization_datas[i]['datas'][1];
                for (var j = 0; j < customization_datas_array.length; j++){
                    var addBr = (j>0)?'<br />':'';
                    displayedCustomizationDatas+=addBr+customization_datas_array[j]['name']+ ' : '+customization_datas_array[j]['value'];
                }
                displayedCustomizationDatas+='<td class="tdAdminCustomizationDataQty"><input type="text" value="'+customization_datas[i]['quantity']+'" name="add_customization['+randomId+']['+customization_datas[i]['datas']['1']['0']['id_customization']+'][newQty]" /></td></td><td></td></tr>';
            }
            qtyInputType = 'hidden';
            customPriceClass = 'customprice_'+prodId+'_'+idAttribute;
            onChangeCustomizationPrice = 'onchange="repairAutoChangePrice(this,\''+customPriceClass+'\')"';
        }
    var newTr='<tr class="line_'+boolForLine+'" id="trProd_'+randomId+'" style="display:none;">';
	newTr+='<td id="tdIdprod_'+randomId+'">'+prodId+'<input type="hidden" name="whoIs['+randomId+']" value="'+prodId+'" id="whoIs_'+randomId+'"/></td>';
	newTr+='<td>'+prodName+'</td>';
	newTr+='<td id="declinaisonsProd_'+randomId+'"></td>';
	newTr+='<td class="prodPrice" id="prodPrice_'+randomId+'">'+prodPrice+'</td>';
	newTr+='<td class="prodPrice" id="prodReducedPrice_'+randomId+'">'+specificPrice+'</td>';
        newTr+='<td><input '+onChangeCustomizationPrice+' name="specific_price['+randomId+']" id="specificPriceInput_'+randomId+'" type="text" value="'+yourPrice+'" class="calcTotalOnChange '+customPriceClass+'"/></td>';
	newTr+='<td class="productPrice">';
        newTr+='<input id="inputQty_'+randomId+'" type="'+qtyInputType+'" value="'+qty+'" name="add_prod['+randomId+']" class="opartDevisAddProdInput calcTotalOnChange"/>';
        if(customization_datas) {
            newTr+='<span></span>';
        }
        newTr+='</td>';
        newTr+='<td>';
        if(!customization_datas) {
            newTr+='<a href="#" onclick="repairDeleteProd(\''+randomId+'\'); return false;"><i class="icon-trash"></i></a>';
        }
        newTr+='</td>';
        newTr+='</tr>';
        newTr+=displayedCustomizationDatas;
	$('#repairProdList').append(newTr);
	$('#trProd_'+randomId).show('slow');
	
    RepairLoadDeclinaisons(randomId,idAttribute);
    RepairBindOnChange();
}
function RepairBindOnChange() {
    $('.calcTotalOnChange').unbind( "change" );
    $('.calcTotalOnChange').change(function() {
        //var randomId = $(this).attr('id').replace('select_attribute_','');
        var randomId = $(this).attr('id').substring($(this).attr('id').lastIndexOf('_')+1);
        if($('#specificPriceInput_' + randomId).val()=='') {
            if($('#last_selected_attribute_'+randomId).length)
                var id_attribute = $('#last_selected_attribute_'+randomId).val();
            else
                var id_attribute = 0;

            deleteOldSpecificPrice(randomId,id_attribute);
            var current_id_attribute = $('#select_attribute_' + randomId).val()
            $('#last_selected_attribute_' + randomId).val(current_id_attribute);
        }
        repairCalcReducedPrice();
     });
}

function RepairAddRuleToRepair(ruleId,name,description,code,free_shipping,reduction_percent,reduction_amount,reduction_type,gift_product) {

        var gift_product_link=(gift_product==0)?'':gift_product;
	var newTr='<tr id="trCartRule_'+ruleId+'" style="display:none;">';
	newTr+='<td>'+ruleId+'<input type="hidden" name="add_rule[]" value="'+ruleId+'" /></td>';
	newTr+='<td>'+name+'</td>';
	newTr+='<td>'+description+'</td>';
	newTr+='<td>'+code+'</td>';
	newTr+='<td>'+((free_shipping==1)?'<i class="icon-check"></i>':'')+'</td>';
	newTr+='<td>'+reduction_percent+'</td>';
	newTr+='<td>'+reduction_amount+'</td>';
	newTr+='<td>'+reduction_type+'</td>';
	newTr+='<td>'+gift_product_link+'</td>';
	newTr+='<td><a href="#" onclick="repairDeleteRule(\''+ruleId+'\'); return false;"><i class="icon-trash"></i></a></td>';
	newTr+='</tr>';
	$('#repairCartRuleList').append(newTr);
	$('#trCartRule_'+ruleId).show('slow');
}

function RepairLoadDeclinaisons(randomId,idAttribute) {
    RepairToggleSubmitBtn(0);
    var prodId=$('#whoIs_'+randomId).val();
    $.ajax({
    	type : 'POST',
        url : 'index.php?controller=AdminRepairs&ajax_load_declinaisons&token='+token,
        data: 'id_prod='+prodId,
        success : function(data,prodId){
            RepairPopulateDeclinaisons(data,randomId,idAttribute);
        }, error : function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Une erreur est survenue !');
        }
    });
}

function RepairPopulateDeclinaisons(data,randomId,idAttribute) {
    if(data.length==0)
        return false;
    data=$.parseJSON(data);
    //select soit defaut soit selected
    var s = $('<select id="select_attribute_'+randomId+'" name="add_attribute['+randomId+']" class="calcTotalOnChange calcTotalOnChangeDec" />');
    for (var key in data) {
       var selected="";
       if(idAttribute!=0 && key==idAttribute)
           selected="selected";
       else if(idAttribute==0 && data['default_on']==1)
            selected="selected";
       s.append('<option '+selected+' value="' + key + '" title="'+data[key]['price']+'">'+ data[key]['attribute_designation']+' ['+data[key]['reference']+'] ('+data[key]['price']+')</option>');
    }

    $('#declinaisonsProd_'+randomId).append(s);
    //add hidden field last id attribute
    var hidden_field_value = $('#select_attribute_'+randomId).val();
    var hidden_field = '<input type="hidden" value="'+hidden_field_value+'" id="last_selected_attribute_'+randomId+'" />';
    $('#declinaisonsProd_'+randomId).append(hidden_field);

    RepairBindOnChange();
    RepairToggleSubmitBtn(1);
}
function RepairCalcTotalRepair() {
        //console.log('total');
        RepairToggleSubmitBtn(0);
	$.ajax({
    	type : 'POST',
        url : 'index.php?controller=AdminRepairs&ajax_get_total_cart&token='+token,
        data: $('#repairForm').serialize(),
        success : function(data){
                        console.log(data);
			var data = $.parseJSON(data);
			$('#totalProductHt').html(data.total_products.toFixed(2));
			$('#totalDiscountsHt').html(data.total_discounts_tax_exc.toFixed(2));
			$('#totalShippingHt').html(data.total_shipping_tax_exc.toFixed(2));
			$('#totalTax').html(data.total_tax.toFixed(2));
            if (data.group_tax_method) {
                $('#totalTax').html('<strike>'+(data.total_tax.toFixed(2))+'</strike>');
                $('#totalRepairWithTax').html((data.total_price-data.total_tax).toFixed(2));
            } else {
                $('#totalTax').html(data.total_tax.toFixed(2));
                $('#totalRepairWithTax').html(data.total_price.toFixed(2));
            }
			$('#repair_id_cart').val(data.id_cart);
			//calc reduced price
			repairCalcReducedPrice();
            $('#repair_refresh_total_repair').removeClass('disabled');

        }, error : function(XMLHttpRequest, textStatus, errorThrown) {
           console.log(XMLHttpRequest);
           console.log(XMLHttpRequest.responseText);
           alert('Une erreur est survenue !');
           $('#repair_refresh_total_repair').removeClass('disabled');
        }
    });
    if($("input[name=id_repair]").length>0)
        RepairShowAjaxMsg(repairMsgRepairSaved,'repairMsg');


}

function RepairAddCustomerToRepair(customerId,firstname,lastname) {
	var newHtml='('+customerId+') '+lastname+' '+firstname;
	$('#repair_customer_info').html(newHtml);
	$('#repair_customer_id').val(customerId);
 	$.ajax({
    	type : 'POST',
        url : 'index.php?controller=AdminRepairs&ajax_address_list&token='+token,
        data: 'id_customer='+customerId,
        success : function(data){
        	//console.log(data);
        	RepairPopulateSelectAddress(data);
        }, error : function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Une erreur est survenue !');
        }
    });
}

function RepairPopulateSelectAddress(data) {
	//decode jsoon;
	data=$.parseJSON(data);
	if(typeof data['erreur']!='undefined') {
            console.log(data['erreur']);
            return false;
        }
	//console.log(data);
	var invoiceSelect=$('#repair_invoice_address_input');
	var deliverySelect=$('#repair_delivery_address_input');
	invoiceSelect.html('');
	deliverySelect.html('');
	for (var key in data) {
            if(data[key]['address2']!="")
                var address2=data[key]['address2']+" - "
            else
                var address2="";
            if($('#selected_invoice').val()==key)
                var selectedInvoice="selected";
            else
                var selectedInvoice="";
            if($('#selected_delivery').val()==key)
                var selectedDelivery="selected";
            else
                var selectedDelivery="";
            invoiceSelect.append('<option '+selectedInvoice+' value="' + key + '">['+ data[key]['alias']+'] - '+ data[key]['company']+' - '+ data[key]['lastname']+' '+data[key]['firstname']+' - '+data[key]['address1']+' - '+address2+data[key]['postcode']+' - '+data[key]['city']+' - '+data[key]['country_name']+'</option>');
            deliverySelect.append('<option '+selectedDelivery+' value="' + key + '">['+ data[key]['alias']+'] - '+ data[key]['company']+' - '+ data[key]['lastname']+' '+data[key]['firstname']+' - '+data[key]['address1']+' - '+address2+data[key]['postcode']+' - '+data[key]['city']+' - '+data[key]['country_name']+'</option>');
	}
}

function deleteOldSpecificPrice(idRandom,id_attribute) {
    var id_cart = $('#repair_id_cart').val();
    var id_prod = $('#whoIs_'+idRandom).val();

    $.ajax({
        type : 'POST',
        url : 'index.php?controller=AdminRepairs&ajax_delete_specific_price&token='+token,
        data: 'id_cart='+id_cart+'&id_prod='+id_prod+'&id_attribute='+id_attribute,
        success : function(data){
        }, error : function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Une erreur est survenue !');
        }
    });
}

function repairDeleteProd(idRandom) {
    $('#trProd_'+idRandom).hide("slow", function() {
        if($('#select_attribute_'+idRandom).length)
            var id_attribute = $('#select_attribute_'+idRandom).val();
        else
            var id_attribute = null;
        deleteOldSpecificPrice(idRandom,id_attribute);
        $('#trProd_'+idRandom).remove();

    });
}

function repairDeleteRule(ruleId) {
    $('#trCartRule_'+ruleId).hide("slow", function() {
        $('#trCartRule_'+ruleId).remove();
    });
}

function repairCalcReducedPrice() {
    RepairToggleSubmitBtn(0);
    $.ajax({
    	type : 'POST',
        url : 'index.php?controller=AdminRepairs&ajax_get_reduced_price&token='+token,
        data: $('#repairForm').serialize(),
        success : function(data){
               console.log(data);
			var data = $.parseJSON(data);
			for(i = 0; i < data.length; i++) {
                            $('#prodPrice_'+data[i]['random_id']).html(data[i]['real_price']);
                            $('#prodReducedPrice_'+data[i]['random_id']).html(data[i]['reduced_price']);
                            $('#specificPriceInput_'+data[i]['random_id']).val(data[i]['your_price']);
			}
                         RepairToggleSubmitBtn(1);
                        
        }, error : function(XMLHttpRequest, textStatus, errorThrown) {
           alert('Une erreur est survenue !');
        }
    });
}
function repairdeleteupload(elt){
    $.ajax({
    	type : 'POST',
        url : 'index.php?controller=AdminRepairs&ajax_delete_upload_file&token='+token,
        data:{upload_name: $(elt).attr('data-name'),upload_id:$(elt).attr('data-id'),},
        //success : console.log('ok'),
    });
}

function RepairShowAjaxMsg(msg,className) {
    $('#repairMsgAlwaysTop').html(msg);
    $('#repairMsgAlwaysTop').removeClass('repairMsg','repairError');
    $('#repairMsgAlwaysTop').addClass(className);
    $('#repairMsgAlwaysTop').show(300).delay(2000).hide(300);
}

function RepairToggleSubmitBtn(showMe) {
    if(showMe == 0)
        $('#repairBtnSubmit').prop('disabled',true);
    else
        $('#repairBtnSubmit').prop('disabled',false);
}
