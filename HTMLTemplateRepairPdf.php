<?php
/**
 * Module repairshop
 *
 * @author    Mondher Bouneb <bounebmondher@gmail.com>
 * @copyright Mondher Bouneb
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @category Prestashop
 * @category Module
 */

class HTMLTemplateRepairPdf extends HTMLTemplate
{
    public $cart_object;
    public $repair;

    public function __construct($repair, $smarty)
    {
        $this->repair = $repair;
        $this->cart_object = new Cart($repair->id_cart);
        $this->smarty = $smarty;
        $this->message = $repair->message;

        $this->shop = new Shop(Context::getContext()->shop->id);
    }

    public function getContent()
    {
        $context = Context::getContext();
        $max_prod_page = 13;
        $max_prod_first_page = 8;

        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $pdf_shopping_cart_dir = 'module:repairshop/views/templates/front/pdf/shopping-cart-product-line.tpl';
        } else {
            $pdf_shopping_cart_dir = _PS_MODULE_DIR_ . 'repairshop/views/templates/front/pdf/shopping-cart-product-line.tpl';
        }

        $priceDisplay = ((int)Configuration::get('PS_TAX') == 0) ? 1 : Product::getTaxCalculationMethod((int)$this->cart_object->id_customer);
        $this->smarty->assign(array(
            'message' => nl2br($this->message),
            'cart_obj' => $this->cart_object,
            'priceDisplay' => $priceDisplay,
            'use_taxes' => (int)Configuration::get('PS_TAX'),
            'maxProdFirstPage' => $max_prod_first_page,
            'maxProdPage' => $max_prod_page,
            'pdf_shopping_cart_dir' => $pdf_shopping_cart_dir,
            'tax_details' => $this->repair->getDetailsTax($this->cart_object),
            'repair_name' => $this->repair->name,
            'repair_object' => $this->repair
        ));
        // echo "dir=".$pdf_shopping_cart_dir;
        // die();
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            return $this->smarty->fetch('module:repairshop/views/templates/front/pdf/ps17/repair.tpl');
        } else {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'repairshop/views/templates/front/pdf/repair.tpl');
        }
    }

    public function getHeader()
    {
        $shop_name = Configuration::get('PS_SHOP_NAME', null, null, (int)$this->cart_object->id_shop);
        $path_logo = $this->getLogo();
        $width = 0;
        $height = 0;
        if (!empty($path_logo)) {
            list($width, $height) = getimagesize($path_logo);
        }

        //Limit the height of the logo for the PDF render
        $maximum_height = 100;
        if ($height > $maximum_height) {
            $ratio = $maximum_height / $height;
            $height *= $ratio;
            $width *= $ratio;
        }

        $this->smarty->assign(array(
            'logo_path' => $path_logo,
            'img_ps_dir' => 'http://' . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
            'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
            'title' => $this->l('Repair number') . ':' . $this->repair->id,
            'date' => Tools::displayDate($this->cart_object->date_upd),
            'shop_name' => $shop_name,
            'width_logo' => $width,
            'height_logo' => $height
        ));
        return $this->smarty->fetch($this->getTemplate('header'));
    }

    protected static function l($string)
    {
        return Translate::getModuleTranslation('repairshop', $string, 'HTMLTemplateRepairPdf');
    }

    public function getFooter()
    {
        $shop_address = $this->getShopAddress();
        $this->smarty->assign(array(
            'available_in_your_account' => $this->available_in_your_account,
            'shop_address' => $shop_address,
            'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, (int)$this->cart_object->id_shop),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, (int)$this->cart_object->id_shop),
            'shop_details' => Configuration::get('PS_SHOP_DETAILS', null, null, (int)$this->cart_object->id_shop),
            'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->cart_object->id_shop)
        ));
        return $this->smarty->fetch(_PS_MODULE_DIR_ . 'repairshop/views/templates/front/pdf/footer.tpl');
    }

    /*
     * Returns the template filename
     * @return string filename
     */

    public function getFilename()
    {
        return self::l('Repair') . '_' . $this->repair->id . '.pdf';
    }

    /*
     * Returns the template filename when using bulk rendering
     * @return string filename
     */

    public function getBulkFilename()
    {
        return self::l('repair') . '.pdf';
    }

    protected function getLogo()
    {
        $logo = '';
        if (Configuration::get('PS_LOGO_INVOICE', null, null, (int)$this->cart_object->id_shop) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int)$this->cart_object->id_shop))
        ) {
            $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int)$this->cart_object->id_shop);
        } elseif (Configuration::get('PS_LOGO', null, null, (int)$this->cart_object->id_shop) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int)$this->cart_object->id_shop))
        ) {
            $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int)$this->cart_object->id_shop);
        }
        return $logo;
    }

    /** since 1.6.1.5 **/
    public function getPagination()
    {
        return false;
    }
}
