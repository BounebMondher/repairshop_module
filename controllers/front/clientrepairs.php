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

class RepairshopClientrepairsModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();


        $my_repairs = Repair::get_client_repairs($this->context->cookie->id_customer);
        $nameArray[1] = $this->l('waiting for repair');
        $nameArray[2] = $this->l('waiting for hardware');
        $nameArray[3] = $this->l('repair in progress');
        $nameArray[4] = $this->l('repaired');
        $nameArray[5] = $this->l('unrepairable');
        $nameArray[6] = $this->l('returned to client');
//        $link = Context::getContext()->link->getModuleLink('repairshop', 'AdminRepairs', array('id_repair' => 1));
//        echo "<pre>";var_dump($link);exit();
        foreach ($my_repairs as $key => $rep) {
            $customer = new Customer($rep['id_customer']);
            $cart = new Cart($rep['id_cart']);
            $my_repairs[$key]['customer'] = $customer->firstname . " " . $customer->lastname;
            $my_repairs[$key]['total'] = $cart->getOrderTotal();
            $my_repairs[$key]["statut"] = $nameArray[$my_repairs[$key]["statut"]];
            //$my_repairs[$key]["view"] = Context::getContext()->link->getModuleLink('repairshop', 'Clientrepairs', array('id_repair' => $rep['id_repair']));
            $my_repairs[$key]["view"] = "?id_repair=" . $rep['id_repair'];
        }


        //echo "<pre>";print_r($my_repairs);exit();
        if (isset($this->context->cookie->success))
            $sss = $this->context->cookie->success;
        else
            $sss = "nope, still fucked up";

        $this->context->smarty->assign(array("my_repairs" => $my_repairs, "success" => $sss));


        if (_PS_VERSION_ >= '1.7') {
            $this->setTemplate('module:repairshop/views/templates/front/clientrepairs.tpl');
        } else {
            $this->setTemplate("clientrepairs.tpl");
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('id_repair')) {

            $this->context->cookie->__set('success', "suckcess");
            $id_repair = Tools::getValue('id_repair');
            $link = new Link;
            $redirect_link = $link->getModuleLink(
                'repairshop',
                'showpdf',
                array(
                    'id_repair' => $id_repair,
                    'admin_key' => Configuration::get('PS_REPAIR_SHOP_SECURE_KEY')
                )
            );

            Tools::redirect($redirect_link);

        }

        if (Tools::isSubmit('sendbymail')) {
            $id_repair = Tools::getValue('id_repair');
            $link = new Link;
            $redirect_link = $link->getModuleLink(
                'repairshop',
                'showpdf',
                array(
                    'id_repair' => $id_repair,
                    'admin_key' => Configuration::get('PS_REPAIR_SHOP_SECURE_KEY'),
                    'sendMailToCustomer' => true
                )
            );

            Tools::redirect($redirect_link);
        }
    }
}