{**
 * Module repairshop
 *
 * @category Prestashop
 * @category Module
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 **}
<div class="panel">
    <div class="panel-heading">
        {l s='Configuration' mod='repairshop'}
    </div>
    <form method="POST">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="rsvalue">{l s='Automatically send repair pdf via email to client upon creation' mod='repairshop'}</label>
                </div>
                <div class="col-md-2">
                    <input type="checkbox" name="rsautosend" id="rsautosend" value="1" class="form-control" {if $REPAIRSHOP_AUTOSEND==1}checked{/if} "/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="rsvalue">{l s='Show "My repairs" page link in the front office ' mod='repairshop'}</label>
                </div>
                <div class="col-md-2">
                    <input type="checkbox" name="rsshowfront" id="rsshowfront" value="1" class="form-control" {if $REPAIRSHOP_SHOWFRONT==1}checked{/if} "/>
                </div>
            </div>



        </div>
        <div class="panel-footer">
            <button type="submit" name="saversvalue" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save' mod='repairshop'}
            </button>
        </div>
    </form>
</div>