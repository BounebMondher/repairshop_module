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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Repairshop extends Module
{
    public function __construct()
    {
        $this->name = 'repairshop';
        $this->author = 'Mondher Bouneb';
        $this->version = '1.0.0';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Repair shop management');
        $this->description = $this->l('Keep track of your repairs status, and keep your clients informed in real-time (back office & front office display)');
        $this->ps_version_compliancy = array('min' => '1.6.0.0', 'max' => '1.7.99.99');
    }

    public function install()
    {
        $sql = array();
        include(dirname(__FILE__) . '/sql/install.php');
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        $rand_key = Tools::substr(md5(rand(0, 1000000)), 0, 7);
        Configuration::updateValue('PS_REPAIR_SHOP_SECURE_KEY', $rand_key);
        Configuration::updateValue('REPAIRSHOP_EXPIRETIME', 0);
        Configuration::updateValue('REPAIRSHOP_IMAGESIZE', "");
        Configuration::updateValue('REPAIRSHOP_MAXPRODFIRSTPAGE', 7);
        Configuration::updateValue('REPAIRSHOP_MAXPRODPAGE', 10);
        Configuration::updateValue('REPAIRSHOP_SHOWFREEFORM', 1);
        Configuration::updateValue('REPAIRSHOP_SHOWACCOUNTBTN', 1);
        return parent::install() && $this->registerHook('displayNav2') && $this->registerHook('header') && $this->registerHook('displayBackOfficeFooter') && $this->createTabLink();
    }

    public function uninstall()
    {
        $sql = array();
        include(dirname(__FILE__) . '/sql/uninstall.php');
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return parent::uninstall();
    }

    public function hookDisplayNav2()
    {
        if(Configuration::get('REPAIRSHOP_SHOWFRONT')==1)
        return $this->display(__FILE__, 'views/templates/hook/nav.tpl');
    }

    public function hookDisplayBackOfficeFooter()
    {
        echo '<script type="text/javascript" src="' . (__PS_BASE_URI__) . 'modules/' . $this->name . '/views/js/icon.js"></script>';
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(array(
            $this->_path . 'views/css/repairshop.css'
        ));
        $this->context->controller->addJS(array(
            $this->_path . 'views/js/repairshop.js'
        ));
    }

    public function getContent()
    {
        if (Tools::isSubmit('saversvalue'))
        {
            $rsautosend = (int) Tools::getValue('rsautosend');
            Configuration::updateValue('REPAIRSHOP_AUTOSEND', $rsautosend);
            $rsshowfront = (int) Tools::getValue('rsshowfront');
            Configuration::updateValue('REPAIRSHOP_SHOWFRONT', $rsshowfront);
        }

        $this->context->smarty->assign(array(
            'REPAIRSHOP_AUTOSEND' => Configuration::get('REPAIRSHOP_AUTOSEND'),
            'REPAIRSHOP_SHOWFRONT' => Configuration::get('REPAIRSHOP_SHOWFRONT')
        ));

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function createTabLink()
    {
        $tab = new Tab;
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Repairs');
        }
        $tab->class_name = 'AdminRepairs';
        $tab->module = $this->name;
        if (_PS_VERSION_ >= '1.7')
            $tab->id_parent = (int)Tab::getIdFromClassName('SELL');
        else
            $tab->id_parent = 0;
        $tab->add();
        return true;
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if (_PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }

}