<?php
/**
 * Module repairshop
 *
 * @category Prestashop
 * @category Module
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

require_once _PS_MODULE_DIR_ . 'repairshop/models/repair.php';

class RepairShopShowPdfModuleFrontController extends ModuleFrontController
{

    public function init()
    {
        $this->display_column_left = false;
        parent::init();
    }

    public function initContent()
    {
        if (Tools::getValue('sendMailToCustomer') && Tools::getValue('sendMailToCustomer') == true) {
            $new_repair = new Repair(Tools::getValue('id_repair'));
            if (!$new_repair->isAllowed())
                return false;

            if ($new_repair->sendMailToCustommer($this->context) == true)
                $text = '<span style="color:green">'.$this->l("Mail to customer has been sent successfully").'</a>';
            else
                $text = '<span style="color:red">'.$this->l("An error occurred during sending mail").'</a>';

            $text .= '<br /><a href="#" onClick="history.back(-1); return false;">back to repair list</a>';
            echo $text;
            die();
        }


        if (Tools::getValue('idCart'))
            $repair = Repair::getByIdCart((int)Tools::getValue('idCart'));

        if (Tools::getValue('id_repair'))
            $repair = new Repair((int)Tools::getValue('id_repair'));

        if ($repair == false)
            die('no repair found');

        if (!$repair->isAllowed())
            return false;
        $repair->renderPdf(Context::getContext()->smarty, true, Context::getContext());
    }

}
