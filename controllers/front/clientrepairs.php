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

class RepairshopClientrepairsModuleFrontController extends ModuleFrontController {
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
        foreach ($my_repairs as $key=>$rep)
        {
            $customer = new Customer($rep['id_customer']);
            $cart = new Cart($rep['id_cart']);
            $my_repairs[$key]['customer']=$customer->firstname." ".$customer->lastname;
            $my_repairs[$key]['total']=$cart->getOrderTotal();
        }

        //echo "<pre>";print_r($my_repairs);exit();

        $this->context->smarty->assign(array("my_repairs"=>$my_repairs));


        if (_PS_VERSION_ >= '1.7') {
            $this->setTemplate('module:repairshop/views/templates/front/clientrepairs.tpl');
        } else {
            $this->setTemplate("clientrepairs.tpl");
        }
    }
}