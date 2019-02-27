{**
 * Module repairshop
 *
 * @category Prestashop
 * @category Module
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 **}
<table>
    <tr>
        <td style="text-align: center; font-size: 6pt; color: #444">
            {if $available_in_your_account}
                {l s='An electronic version of this report is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).' mod='repairshop'}
                <br/>
            {/if}
            {$shop_address|escape:'htmlall':'UTF-8':'UTF-8'}<br/>

            {if !empty($shop_phone) OR !empty($shop_fax)}
                {l s='For more assistance, contact Support:' mod='repairshop'}
                <br/>
                {if !empty($shop_phone)}
                    Tel: {$shop_phone|escape:'htmlall':'UTF-8':'UTF-8'}
                {/if}

                {if !empty($shop_fax)}
                    Fax: {$shop_fax|escape:'htmlall':'UTF-8':'UTF-8'}
                {/if}
                <br/>
            {/if}

            {if isset($shop_details)}
                {$shop_details|escape:'htmlall':'UTF-8':'UTF-8'}
                <br/>
            {/if}

        </td>
    </tr>
</table>