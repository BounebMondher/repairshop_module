<!doctype html>
<html lang="{$language.iso_code}">

<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
</head>

<body id="{$page.page_name}" class="{$page.body_classes|classnames}">

{hook h='displayAfterBodyOpeningTag'}

<main>

    <header id="header">
        {block name='header'}
            {include file='_partials/header.tpl'}
        {/block}
    </header>

    <section id="wrapper">
        <div class="container" style="min-height:300px!important;">

            <table class="table">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Device</th>
                    <th scope="col">Date</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
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
                    <td>View</td>
                </tr>
                {/foreach}

                </tbody>
            </table>

        </div>
    </section>

    <footer id="footer">
        {block name="footer"}
            {include file="_partials/footer.tpl"}
        {/block}
    </footer>

</main>

{hook h='displayBeforeBodyClosingTag'}

{block name='javascript_bottom'}
    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
{/block}

</body>

</html>