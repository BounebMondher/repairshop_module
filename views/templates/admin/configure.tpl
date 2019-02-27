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

            <label for="rsvalue">{l s='config value' mod='repairshop'}</label>
            <input type="text" name="rsvalue" id="rsvalue" class="form-control" value="{$REPAIRSHOP_VALUE}"/>

        </div>
        <div class="panel-footer">
            <button type="submit" name="saversvalue" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save' mod='repairshop'}
            </button>
        </div>
    </form>
</div>