{**
 * Module repairshop
 *
 * @category Prestashop
 * @category Module
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 **}
{*<!doctype html>*}
{*<html lang="{$language.iso_code}">*}

{*<head>*}
    {*{block name='head'}*}
        {*{include file=$headerr}*}
    {*{/block}*}
{*</head>*}

{*<body id="{$page.page_name}" class="{$page.body_classes|classnames}">*}

{*{hook h='displayAfterBodyOpeningTag'}*}

{*<main>*}

    {*<header id="header">*}
        {*{block name='header'}*}
            {*{include file='_partials/header.tpl'}*}
        {*{/block}*}
    {*</header>*}

    <section id="wrapper">
        <div class="container" style="min-height:300px!important;">
            {if $nb_repairs>0}
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">{l s='ID' mod='repairshop'}</th>
                    <th scope="col">{l s='Name' mod='repairshop'}</th>
                    <th scope="col">{l s='Customer' mod='repairshop'}</th>
                    <th scope="col">{l s='Device' mod='repairshop'}</th>
                    <th scope="col">{l s='Date' mod='repairshop'}</th>
                    <th scope="col">{l s='Total' mod='repairshop'}</th>
                    <th scope="col">{l s='Status' mod='repairshop'}</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                {foreach $my_repairs as $my_repair}
                    <tr>
                        <th scope="row">{$my_repair['id_repair']}</th>
                        <td>{$my_repair['name']}</td>
                        <td>{$my_repair['customer']}</td>
                        <td>{$my_repair['device']}</td>
                        <td>{$my_repair['date_add']}</td>
                        <td>{$my_repair['total']}</td>
                        <td>{$my_repair['statut']}</td>
                        <td><a href="{$my_repair['view']}" class="btn btn-success">{l s='View' mod='repairshop'}</a>
                        </td>
                    </tr>
                {/foreach}

                </tbody>
            </table>
            {/if}
            {if $nb_repairs==0}
                <div class="alert alert-warning" role="alert">
                    {l s="You don't have any repairs, if you think there's a mistake, please feel free to contact us" mod='repairshop'}
                </div>
            {/if}

        </div>
    </section>

    {*<footer id="footer">*}
        {*{block name="footer"}*}
            {*{include file="_partials/footer.tpl"}*}
        {*{/block}*}
    {*</footer>*}

{*</main>*}

{*{hook h='displayBeforeBodyClosingTag'}*}

{*{block name='javascript_bottom'}*}
    {*{include file="_partials/javascript.tpl" javascript=$javascript.bottom}*}
{*{/block}*}


{*</body>*}

{*</html>*}