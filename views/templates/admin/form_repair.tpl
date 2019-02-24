{**
 * Module repairshop
 *
 * @category Prestashop
 * @category Module
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 **}
<script type="text/javascript">
    var id_lang_default = {$id_lang_default|escape:'htmlall':'UTF-8'};
    var repairshop_module_dir = "{$ps_base_url|escape:'htmlall':'UTF-8'}{$repairshop_module_dir|escape:'htmlall':'UTF-8'}";
    var token = '{$repair_token|escape:'htmlall':'UTF-8'}';
</script>
<div id="repairShopMsgAlwaysTop"></div>
<form action="{$href|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" id="repairForm">
    <input type="hidden" name="submitAddRepair" value="1">
    {if isset($obj->id_repair) && $obj->id_repair!=""}
        <input type="hidden" value="{$obj->id_repair|escape:'htmlall':'UTF-8'}" name="id_repair" />
    {/if}
    
    <input type="hidden" value="{if isset($obj->id_repair) && $obj->id_repair!=""}{$obj->id_cart|escape:'htmlall':'UTF-8'}{/if}" name="idCart" id="repair_id_cart" />
    <!-- name -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Repair name' mod='repairshop'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-lg-1"><span class="pull-right"></span></div>	
                <label class="control-label col-lg-2">
                    {l s='Add a name to this repair:' mod='repairshop'}
                </label>
                <div class="col-lg-7">
                    <input type="text" value="{if isset($obj)}{$obj->name|escape:'htmlall':'UTF-8'}{/if}" name="repair_name" />
                </div>
            </div>
        </div>
    </div>
    <!-- device -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Device' mod='repairshop'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-lg-1"><span class="pull-right"></span></div>
                <label class="control-label col-lg-2">
                    {l s='Enter device name:' mod='repairshop'}
                </label>
                <div class="col-lg-7">
                    <input placeholder="iPhone X, Galaxy S9, Mac book Pro, Smart TV, iPad air ..." type="text" value="{if isset($obj)}{$obj->device|escape:'htmlall':'UTF-8'}{/if}" name="repair_device" />
                </div>
            </div>
        </div>
    </div>
    <!-- Status -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Status' mod='repairshop'}</h3>
        <div class="form-horizontal">
            <div class="form-group">
            <div class="col-lg-1"><span class="pull-right"></span></div>
            <label class="control-label col-lg-2" for="repair_status">
                {l s='select status:' mod='repairshop'}
            </label>
            <div class="col-lg-7">

                    <select id="repair_status" name="repair_status">
                        <option {if isset($obj) && $obj->statut == 1}selected{/if} value="1">{l s='waiting for repair' mod='repairshop'}</option>
                        <option {if isset($obj) && $obj->statut == 2}selected{/if} value="2">{l s='waiting for hardware' mod='repairshop'}</option>
                        <option {if isset($obj) && $obj->statut == 3}selected{/if} value="3">{l s='repair in progress' mod='repairshop'}</option>
                        <option {if isset($obj) && $obj->statut == 4}selected{/if} value="4">{l s='repaired' mod='repairshop'}</option>
                        <option {if isset($obj) && $obj->statut == 5}selected{/if} value="5">{l s='unrepairable' mod='repairshop'}</option>
                        <option {if isset($obj) && $obj->statut == 6}selected{/if} value="6">{l s='returned to client' mod='repairshop'}</option>
                    </select>

                {l s='you can add further details in the message section' mod='repairshop'}
            </div>
            </div>
        </div>
    </div>

    <!-- user -->
    <div class="panel">
        <h3><i class="icon-user"></i> {l s='Customer' mod='repairshop'}</h3>
        <div class="form-horizontal">
            <div class="form-group redirect_product_options redirect_product_options_product_choise">	
                <div class="col-lg-1"><span class="pull-right"></span></div>	
                <label class="control-label col-lg-2" for="repair_customer_autocomplete_input">
                    {l s='choose customer:' mod='repairshop'}
                </label>
                <div class="col-lg-7">
                    <input type="hidden" value="" name="id_product_redirected" />
                    <div class="input-group">
                        <input type="text" id="repair_customer_autocomplete_input" name="repair_customer_autocomplete_input" autocomplete="off" class="ac_input" />
                        <span class="input-group-addon"><i class="icon-search"></i></span>
                    </div>
                    <p class="help-block">{l s='Start by typing the first letters of the customer\'s firstname or lastname, then select the customer from the drop-down list.' mod='repairshop'}</p>
                    <h2 style="clear:both;">
                        <i class="icon-male"></i> 
                        <span href="" id="repair_customer_info"><span style="color:red">{l s='Please choose a customer' mod='repairshop'}</span></span>
                    </h2>			
                </div>
                <input type="hidden" name="repair_customer_id" id="repair_customer_id" value=""/>
            </div>
        </div>
    </div>

    <!-- products and/or services -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Products and/or services' mod='repairshop'}</h3>
        
        <div class="form-horizontal">
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" for="repair_product_autocomplete_input">
                {l s='add product:' mod='repairshop'}
            </label>
            <div class="col-lg-7">
                <input type="hidden" value="" name="id_product_redirected" />
                <div class="input-group">
                    <input type="text" id="repair_product_autocomplete_input" name="repair_product_autocomplete_input" autocomplete="off" class="ac_input" />
                    <span class="input-group-addon"><i class="icon-search"></i></span>					
                </div>
                <p class="help-block">{l s='Start by typing the first letters of the products\'s name, then select the product from the drop-down list.' mod='repairshop'}</p>
            </div>
            <div style="clear:both; height:20px;"></div>	
            <div class="col-lg-1"><span class="pull-right"></span></div>			
            <label class="control-label col-lg-2" for="repair_product_autocomplete_input">
                {l s='products in quotation:' mod='repairshop'}
            </label>
            <div class="col-lg-7">
                <!--<div id="waitProductLoad">{l s='loading' mod='repairshop'}</div>-->
                <table class="table" id="repairProdList">
                    <tr>
                        <th style="width:5%">{l s='id' mod='repairshop'}</th>
                        <th>{l s='name' mod='repairshop'}</th>
                        <th>{l s='Attributes' mod='repairshop'}</th>
                        <th style="width:10%">{l s='Catalog price without tax' mod='repairshop'}</th>
                        <th style="width:10%">{l s='Reduced price without tax' mod='repairshop'}</th>
                        <!--<th style="width:10%">{l s='real price' mod='repairshop'}</th>-->
                        <th style="width:10%">{l s='Your price' mod='repairshop'}</th>
                        <th style="width:10%">{l s='Quantity' mod='repairshop'}</th>
                        <th style="width:5%">&nbsp;</th>
                    </tr>	
                </table>	
            </div>
            <div style="clear:both;"></div>			
        </div>
    </div>
	<!-- discounts -->
	<div class="panel">
        <h3><i class="icon-archive"></i> {l s='Reductions' mod='repairshop'}</h3>
		<div class="form-horizontal">
			<div class="col-lg-1"><span class="pull-right"></span></div>
			<label class="control-label col-lg-2" for="repair_product_autocomplete_input">
                {l s='add reduction:' mod='repairshop'}
            </label>
            <div class="col-lg-7">
                <div class="input-group">
                    <select id="repair_select_cart_rules">
			{if count($cart_rules)>0}
			<option value="-1">--- {l s='cart rules' mod='repairshop'} ---</option>
			{foreach $cart_rules as $rule}
                            <option value="{$rule.id_cart_rule|escape:'htmlall':'UTF-8'}">{$rule.name|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
			{else}						
                            <option value="-1">--- {l s='no cart rules avaibles' mod='repairshop'} ---</option>
			{/if}
                    </select>
		</div>
                <div id="repairCartRulesMsgError" style="display:none;"></div>
            </div>
			<div style="clear:both; height:20px;"></div>	
            <div class="col-lg-1"><span class="pull-right"></span></div>			
            <label class="control-label col-lg-2" for="repair_product_autocomplete_input">
                {l s='discount in quotation:' mod='repairshop'}
            </label>
            <div class="col-lg-7">
                <table class="table" id="repairCartRuleList">
                    <tr>
                        <th style="width:5%">{l s='id' mod='repairshop'}</th>
                        <th>{l s='name' mod='repairshop'}</th>
						<th>{l s='description' mod='repairshop'}</th>
                        <th>{l s='code' mod='repairshop'}</th>
						<th>{l s='free shipping' mod='repairshop'}</th>
						<th>{l s='reduction percent' mod='repairshop'}</th>
						<th>{l s='reduction amount' mod='repairshop'}</th>
						<th>{l s='reduction type' mod='repairshop'}</th>
						<th>{l s='gift product' mod='repairshop'}</th>
						<th>&nbsp;</th>
                    </tr>	
                </table>	
            </div>
            <div style="clear:both;"></div>	
		</div>
	</div>

    <!-- additional information -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Additional informations' mod='repairshop'}</h3>
        <div class="form-horizontal">
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" for="repair_product_autocomplete_input">
                {l s='Message:' mod='repairshop'}
            </label>
            <div class="col-lg-7">			
                <textarea name="message">{if isset($obj->message)}{$obj->message|escape:'htmlall':'UTF-8'}{/if}</textarea>
                <p class="help-block">{l s='Visible on repair report.' mod='repairshop'}</p>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <!-- TOTAL -->
    <div class="panel">
        <h3><i class="icon-archive"></i> {l s='Total' mod='repairshop'}</h3>
        <div class="form-horizontal">
		
            <!-- total product ht -->
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total product without tax:' mod='repairshop'} = 
            </label>
            <div class="col-lg-7"><span id="totalProductHt"></span></div>            
            <div style="clear:both;"></div>
	
            <!-- total discounts ht-->
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total discounts without tax' mod='repairshop'} = 
            </label>
            <div class="col-lg-7"><span id="totalDiscountsHt"></span></div>            
            <div style="clear:both;"></div>
            
            <!-- total shipping ht-->
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total shipping with out tax' mod='repairshop'} = 
            </label>
            <div class="col-lg-7"><span id="totalShippingHt"></span></div>            
            <div style="clear:both;"></div>
            
            <!-- total tax -->
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0">
                {l s='Total tax' mod='repairshop'} = 
            </label>
            <div class="col-lg-7"><span id="totalTax"></span></div>            
            <div style="clear:both;"></div>
			
			
            <!-- total quotation with tax -->
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2" style="padding-top:0; font-size:1.5em;">
                {l s='Total repair with tax:' mod='repairshop'} =
            </label>
            <span id="totalRepairWithTax" style="color:red; font-weight:bold; font-size:1.5em;"></span>
            <div style="clear:both;"></div>
            <div class="col-lg-1"><span class="pull-right"></span></div>	
            <label class="control-label col-lg-2">
                <a href="#" id="repair_refresh_total_repair" style="display:inline-block; vertical-align:middle;"><i class="process-icon-refresh"></i></a>{l s='Refesh total' mod='repairshop'}
            </label>
            <div style="clear:both;"></div>
        </div>
    </div>
    <div style="clear:both";></div>
    <div class="panel">
        <div class="panel-footer">
            <a href="{$hrefCancel|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='cancel' mod='repairshop'}</a>
            <button id="repairBtnSubmit" disable="true" type="submit" name="submitAddrepair" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='save' mod='repairshop'}</button>
        </div>
    </div>
</form>
       {*<pre>
            {$products|@print_r}
        </pre>*}
<script type="text/javascript">
    id_lang_default = {$id_lang_default|escape:'htmlall':'UTF-8'};
    specific_price_txt = "{l s='Specific price'  mod='repairshop'}";
    from_qty_text = "{l s='from'  mod='repairshop'}";
    qty_text = "{l s='quantity'  mod='repairshop'}";
    repairControllerUrl = 'index.php?controller=AdminRepairs&token={$repair_token|escape:'htmlall':'UTF-8'}';
    repairMsgRepairSaved = "{l s='Your repair has been saved' mod='repairshop'}";
    currency_sign = "{$currency_sign|escape:'htmlall':'UTF-8'}";
    nbProductToLoad = 0;
    {if $customer!=null}
        setTimeout(function(){
            RepairAddCustomerToRepair({$customer->id|escape:'htmlall':'UTF-8'},'{$customer->firstname|escape:'htmlall':'UTF-8'}','{$customer->lastname|escape:'htmlall':'UTF-8'}');
        }, 300); 
    {/if}
    {if $cart!=null}
        {foreach $products AS $product}
            nbProductToLoad++;
            RepairAddProductToRepair({$product.id_product|escape:'htmlall':'UTF-8'},'{$product.name|escape:'htmlall':'UTF-8'}','{$product.catalogue_price|escape:'htmlall':'UTF-8'}',{$product.cart_quantity|escape:'htmlall':'UTF-8'},{$product.id_product_attribute|escape:'htmlall':'UTF-8'},'{$product.specific_price|escape:'htmlall':'UTF-8'}','{$product.your_price|escape:'htmlall':'UTF-8'}','{$product.customization_datas_json}'); {*can't escape this value*}
        {/foreach}
    {/if}
	{if $cart!=null && !empty($summary.discounts)}		
		{foreach $summary.discounts AS $rule}
			{if $rule.reduction_product==-2}
				reduction_type = "{l s='selected product' mod='repairshop'}"
			{else if $rule.reduction_product==-1}
				reduction_type = "{l s='cheapest product' mod='repairshop'}"
			{else if $rule.reduction_product==0}
				reduction_type = "{l s='order' mod='repairshop'}"	
			{else}
				reduction_type = "{l s='specific product' mod='repairshop'} ({$rule.reduction_product})"{/if}
					
			RepairAddRuleToRepair({$rule.id_cart_rule|escape:'htmlall':'UTF-8'},'{$rule.name|escape:'htmlall':'UTF-8'}','{$rule.description|escape:'htmlall':'UTF-8'}','{$rule.code|escape:'htmlall':'UTF-8'}',{$rule.free_shipping|escape:'htmlall':'UTF-8'},'{$rule.reduction_percent|escape:'htmlall':'UTF-8'}','{$rule.reduction_amount|escape:'htmlall':'UTF-8'}',reduction_type,{$rule.gift_product|escape:'htmlall':'UTF-8'});
		{/foreach}
	{/if}

</script>