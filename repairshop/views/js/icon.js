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
    var icon = $("#subtab-AdminRepairs").children().first().children().first();
    if (icon.length) {
        icon.html("phonelink_setup");
        icon.removeClass('mi-extension');
    }
    else {
        icon = $(".icon-AdminRepairs").first();
        icon.addClass("icon-medkit");
    }


});