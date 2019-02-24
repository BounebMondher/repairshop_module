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

require_once _PS_MODULE_DIR_.'repairshop/HTMLTemplateRepairPdf.php';



class Repair extends ObjectModel {
    /* @var string Name */

    public $id_repair;
    public $id_cart;
    public $id_customer;
    public $name;
    public $device;
    public $message;
    public $date_add;
    public $statut; //1 waiting for repair, 2 waiting for hardware, 3 repair in progress , 4 repaired, 5 unrepairable, 6 returned to client
    public $date_repaired;
    public $date_returned;
    public $id_creator;
    public $id_updater;
    public $id_order;
    public $smarty;

    /*
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'repair',
        'primary' => 'id_repair',
        'multilang' => false,
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'device' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'message' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'valide' => 'isDate', 'required' => true),
            'statut' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_repaired' => array('type' => self::TYPE_DATE, 'valide' => 'isDate', 'required' => false),
            'date_returned' => array('type' => self::TYPE_DATE, 'valide' => 'isDate', 'required' => false),
            'id_creator' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_updater' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),

        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function isAllowed()
    {
        $cookie = new Cookie('psAdmin');
        $context = Context::getContext();

        if ($cookie->id_employee) {
            return true;
        } elseif ($this->id_customer == $context->customer->id) {
            return true;
        }

        return false;
    }

    public static function createRepair(
        $cart,
        $customer,
        $statut = 1,
        $device = '',
        $id_repair = null,
        $repair_name = null,
        $message = '',
        $duplicate_cart = true

    ) {
        if ($duplicate_cart == true) {
            $duplicate = $cart->duplicate();
            $id_cart = $duplicate['cart']->id;
            $new_cart = new Cart($id_cart);
            if (count($cart->getCartRules()) > 0) {
                foreach ($cart->getCartRules() as $rule) {
                    $new_cart->addCartRule($rule['id_cart_rule']);
                }
            }
        } else {
            $new_cart = $cart;
        }

        $customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
                SELECT *
                FROM '._DB_PREFIX_.'customization c
                LEFT JOIN '._DB_PREFIX_.'customized_data cd ON cd.id_customization = c.id_customization
                WHERE c.id_cart = '.(int)$new_cart->id
        );

        foreach ($customs as $custom) {
            $prod_array = $new_cart->getProducts($custom['id_product']);
            $sql = 'UPDATE '._DB_PREFIX_.'customization SET id_address_delivery='.(int)$prod_array[0]['id_address_delivery'].
                ' WHERE id_customization='.(int)$custom['id_customization'];

            db::getInstance()->execute($sql);
        }

        $date_time = date('Y-m-d H:i:s');

        //save it
        if ($id_repair != null) {
            $new_repair = new Repair($id_repair);
        } else {
            $new_repair = new Repair();
        }
        $new_repair->statut = $statut;

        if ($repair_name == null) {
            $repair_name = $new_repair->l('Repair').' '.$date_time;
        }

        $new_repair->name = $repair_name;
        $new_repair->device = $device;
        $new_repair->id_cart = $new_cart->id;
        $new_repair->id_customer = (int)$customer->id;
        $new_repair->date_add = $date_time;
        $new_repair->message = $message;



        $new_repair->save();

        return $new_repair;
    }

    public static function createCart($id_cart = null)
    {
        $context = Context::getContext();
        if ($id_cart == null) {
            $cart = new Cart();
        } else {
            $cart = new Cart($id_cart);
        }

        Context::getContext()->cart = $cart;
        $id_customer = (int)Tools::getValue('repair_customer_id');
        $cart->id_customer = $id_customer;
        $customer_obj = new Customer($id_customer);
        $context->customer = $customer_obj;
        //empty cart
        $old_prod = $cart->getProducts();
        //p($cart->getProducts());

        foreach ($old_prod as $prod) {
            $customizations = $cart->getProductCustomization($prod['id_product']);
            /*p($customizations);
            if(count($customizations)>0)
            foreach($customizations as $customization) {
            //save old customization
            $cart->deleteProduct($prod['id_product'],$prod['id_product_attribute'],$customization['id_customization'],$prod['id_address_delivery']);
            }
            else*/
            //do not delete custom product here
            if (!count($customizations)>0) {
                $cart->deleteProduct($prod['id_product'], $prod['id_product_attribute']);
            }
        }

        $cart->id_currency = $context->currency->id;
        $cart->id_lang = $context->language->id;

        $cart->save();

        $add_prod_list = Tools::getValue('add_prod');
        $add_attribute_list = Tools::getValue('add_attribute');
        $add_customization_list = Tools::getValue('add_customization');
        $who_is_list = Tools::getValue('whoIs');
        $list_prod = array();
        $specific_price_list = Tools::getValue('specific_price');
        $specific_qty_list = Tools::getValue('specific_qty');
        if (!empty($who_is_list)) {
            foreach ($who_is_list as $random_id => $prod_id) {
                $list_prod[$random_id]['id'] = $prod_id;
                $list_prod[$random_id]['qty'] = $add_prod_list[$random_id];

                if (isset($add_attribute_list[$random_id])) {
                    $list_prod[$random_id]['id_attribute'] = $add_attribute_list[$random_id];
                } else {
                    $list_prod[$random_id]['id_attribute'] = 0;
                }

                /* customization */
                if (isset($add_customization_list[$random_id])) {
                    //get old qty
                    $oldCustoms=$cart->getProductCustomization($prod_id);
                    $list_prod[$random_id]['qty']=0;
                    foreach ($add_customization_list[$random_id] as $id_customization => $qtyArray) {
                        foreach ($oldCustoms as $oldCustom) {
                            if ($oldCustom['id_customization']==$id_customization) {
                                $oldQty = $oldCustom['quantity'];
                            }
                        }
                        //$qtyToAdd = $qtyArray['newQty'] - $qtyArray['oldQty'];
                        $qtyToAdd = $qtyArray['newQty'] - $oldQty;
                        $list_prod[$random_id]['id_customization'][$id_customization]['operator'] = ($qtyToAdd>0)?'up':'down';
                        $list_prod[$random_id]['id_customization'][$id_customization]['qty'] = abs($qtyToAdd);
                        $list_prod[$random_id]['qty']+=$qtyArray['newQty'];
                    }
                }

                /** specific price * */
                if (isset($specific_price_list[$random_id]) && $specific_price_list[$random_id] != '') {
                    $list_prod[$random_id]['specific_price'] = str_replace(',', '.', $specific_price_list[$random_id]);
                    $list_prod[$random_id]['specific_qty'] = $list_prod[$random_id]['qty'];
                    //$list_prod[$random_id]['specific_qty'] = ($specific_qty_list[$random_id] == '') ? 1 : $specific_qty_list[$random_id];
                } else {//si pas de prix specifique indique alors on enregistre le prix du produit en tant que prix specifique
                    $specific_price_output = null;
                    $price = Product::getPriceStatic($list_prod[$random_id]['id'], false, $list_prod[$random_id]['id_attribute'], 6, null, false, true, (int)$list_prod[$random_id]['qty'], false, $id_customer, 0, $cart->id_address_delivery, $specific_price_output, false, true, $context, true);

                    $list_prod[$random_id]['specific_price'] = $price;
                    $list_prod[$random_id]['specific_qty'] = $list_prod[$random_id]['qty'];
                }
            }
        }

        if (!empty($list_prod)) {
            foreach ($list_prod as $prod) {
                if (isset($prod['id_attribute']) && isset($prod['id_customization'])) {
                    foreach ($prod['id_customization'] as $id_customization=>$customization_array) {
                        if ($customization_array['qty']!=0) {
                            $cart->updateQty($customization_array['qty'], $prod['id'], $prod['id_attribute'], $id_customization, $customization_array['operator']);
                        }
                    }
                } elseif (isset($prod['id_customization'])>0) {
                    foreach ($prod['id_customization'] as $id_customization=>$qty_customization) {
                        if ($customization_array['qty']!=0) {
                            $cart->updateQty($customization_array['qty'], $prod['id'], $prod['id_attribute'], $id_customization, $customization_array['operator']);
                        }
                    }
                } elseif (isset($prod['id_attribute'])) {
                    $cart->updateQty($prod['qty'], $prod['id'], $prod['id_attribute']);
                } else {
                    $cart->updateQty($prod['qty'], $prod['id']);
                }
            }
        }

        //delete old rules
        $old_rules = $cart->getCartRules();
        if (count($old_rules) > 0) {
            foreach ($old_rules as $old_rule) {
                $cart->removeCartRule($old_rule['id_cart_rule']);
            }
        }

        $add_rule = Tools::getValue('add_rule');
        if (!empty($add_rule)) {
            foreach ($add_rule as $id_rule) {
                $cart->addCartRule($id_rule);
            }
        }

        $cart->setDeliveryOption(array($cart->id_address_delivery => (int)$cart->id_carrier.','));
        $cart->update();

        /** add specific price into table * */
        Repair::addSpecificPrice($list_prod, $cart, $id_customer);

        return $cart;
    }

    public static function addSpecificPrice($list_prod, $cart, $id_customer)
    {
        if (!empty($list_prod)) {
            foreach ($list_prod as $prod) {
                if (isset($prod['specific_price']) && $prod['specific_price'] != '' && $cart->id!=0) {
                    SpecificPrice::deleteByIdCart($cart->id, $prod['id'], $prod['id_attribute']);
                    $specific_price = new SpecificPrice();
                    $specific_price->id_cart = (int)$cart->id;
                    $specific_price->id_specific_price_rule = 0;
                    $specific_price->id_product = (int)$prod['id'];
                    $specific_price->id_product_attribute = (int)$prod['id_attribute'];
                    $specific_price->id_customer = $id_customer;
                    $specific_price->id_shop = (int)$cart->id_shop;
                    $specific_price->id_country = 0;
                    $specific_price->id_currency = 0;
                    $specific_price->id_group = 0;
                    $specific_price->from_quantity = (int)$prod['specific_qty'];
                    $specific_price->price = (float)$prod['specific_price'];
                    $specific_price->reduction_type = 'amount';
                    $specific_price->reduction_tax = 0;
                    $specific_price->reduction = 0;
                    $specific_price->from = 0;
                    $specific_price->to = 0;
                    $specific_price->add();
                }
            }
        }
    }

    public static function deleteSpecificPrice($id_cart, $id_product = null, $id_attribute = null)
    {
        $id_attribute = ($id_attribute == null)?0:$id_attribute;

        if (!isset($id_cart) || $id_cart == 0) {
            return false;
        }

        if ($id_product == null) {
            $sql = 'DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_cart` = '.(int)$id_cart;
        } else {
            $sql ='DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_cart` = '.(int)$id_cart.' AND id_product='.(int)$id_product.' AND id_attribute='.(int)$id_attribute;
        }
        Db::getInstance()->execute($sql);
    }

    public function sendMailToCustommer($context)
    {
        $data = $this->getDataArray($context);

        $customer_obj = new Customer($this->id_customer);
        $filename = $this->l('repair_').$this->id.'.pdf';
        $file_attachement = array();


            $file_attachement['content'] = $this->renderPDf($context->smarty, false);
            $file_attachement['name'] = $filename;
            $file_attachement['mime'] = 'application/pdf';

        //send mail to customer
        if (Mail::Send(
            (int)$context->language->id,
            'repairshop_customer',
            $this->l('Your repair'),
            $data,
            $customer_obj->email,
            $customer_obj->firstname.' '.$customer_obj->lastname,
            null,
            null,
            $file_attachement,
            null,
            _PS_MODULE_DIR_.'repairshop/mails/',
            false,
            (int)$context->shop->id
        )) {
            return true;
        } else {
            return false;
        }
    }


    public function renderPdf($smarty, $render = true)
    {
        $this->smarty = $smarty;
        $cart_obj = new Cart($this->id_cart);
        $this->assignSummaryInformations($cart_obj);


        $this->smarty->assign('repair_number', $this->id_repair);

        $pdf = new PDF($this, 'RepairPdf', $this->smarty);

        if ($render == false) {
            return $pdf->render(false);
        } else {
            $pdf->render();
        }

        die(); //evite les erreur 500 dans certain cas ?
    }

    public function getDetailsTax($cart_obj)
    {
        /*p($cart_obj);
        die();*/
        $products = $cart_obj->getProducts();
        $tax_details = array();
        $cart_rules = $cart_obj->getCartRules();

        if (isset($cart_rules) && count($cart_rules)>0) {
            foreach ($cart_rules as $cart_rule) {
                $tax_details['discount']['total_ttc'] =
                    (!isset($tax_details['discount']['total_ttc']) ? 0 : $tax_details['discount']['total_ttc']) + $cart_rule['value_real'];
                $tax_details['discount']['total_ht'] =
                    (!isset($tax_details['discount']['total_ht']) ? 0 : $tax_details['discount']['total_ht']) + $cart_rule['value_tax_exc'];
            }

            $tax_details['discount']['total_tax'] = $tax_details['discount']['total_ttc'] - $tax_details['discount']['total_ht'];
            //$tax_details['discount']['name'] = sprintf($this->l('Discount'), $shipping_tax_rate);
            $tax_details['discount']['name'] = $this->l('Discount');
        }

        $tax_details['ecotax']['total_tax'] = 0;
        $tax_details['ecotax']['total_ht'] = '--';

        foreach ($products as $product) {
            $rate = number_format($product['rate'], 3);
            $tax_details[$rate]['total_tax'] =
                (!isset($tax_details[$rate]['total_tax']) ? 0 : $tax_details[$rate]['total_tax']) + $product['total_wt'] - $product['total'];

            $tax_details[$rate]['total_ht'] =
                (!isset($tax_details[$rate]['total_ht']) ? 0 : $tax_details[$rate]['total_ht']) + $product['total'];

            if ($product['ecotax']!=0) {
                $tax_details['ecotax']['total_tax'] += $product['ecotax'] * $product['quantity'];
            }

            $tax_details['ecotax']['name']=$this->l('Ecotax');
            $tax_details[$rate]['name'] = $product['tax_name'];
        }

        /*get carrier tax rate*/
        $shipping_tax_rate = Tax::getCarrierTaxRate($cart_obj->id_carrier, $cart_obj->id_address_delivery);
        $shipping_cost_ht = $cart_obj->getTotalShippingCost(null, false);
        $shipping_tax = $cart_obj->getTotalShippingCost(null, true) - $shipping_cost_ht;

        $tax_details['shipping']['total_tax'] = $shipping_tax;
        $tax_details['shipping']['total_ht'] = $shipping_cost_ht;
        $tax_details['shipping']['name'] = sprintf($this->l('shipping tax (%d%%)'), $shipping_tax_rate);

        return $tax_details;
    }

    protected function assignSummaryInformations($cart_obj)
    {
        Context::getContext()->customer = new Customer($cart_obj->id_customer);
        Context::getContext()->cart = $cart_obj;
        $context = Context::getContext();

        $summary = $cart_obj->getSummaryDetails();
        $customized_datas = Product::getAllCustomizedDatas($cart_obj->id);

        if ($customized_datas) {
            foreach ($summary['products'] as &$product_update) {
                $product_id = (isset($product_update['id_product']) ? $product_update['id_product'] : $product_update['product_id']);
                $product_attribute_id = (isset($product_update['id_product_attribute']) ?
                    $product_update['id_product_attribute'] : $product_update['product_attribute_id']);

                if (isset($customized_datas[$product_id][$product_attribute_id])) {
                    $product_update['tax_rate'] = Tax::getProductTaxRate($product_id, $cart_obj->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                }
            }

            Product::addCustomizationPrice($summary['products'], $customized_datas);
        }

        $cart_product_context = Context::getContext()->cloneContext();
        $link = new Link();

        foreach ($summary['products'] as $key => &$product) {
            $product['quantity'] = $product['cart_quantity']; // for compatibility with 1.2 themes

            if ($cart_product_context->shop->id != $product['id_shop']) {
                $cart_product_context->shop = new Shop((int)$product['id_shop']);
            }
            $null = null;
            $product['price_without_specific_price'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                2,
                null,
                false,
                false,
                1,
                false,
                $this->id_customer,
                null,
                null,
                $null,
                true,
                true,
                $cart_product_context
            );

            if (Product::getTaxCalculationMethod()) {
                $product['is_discounted'] = $product['price_without_specific_price'] != $product['price'];
            } else {
                $product['is_discounted'] = $product['price_without_specific_price'] != $product['price_wt'];
            }

            $product['image'] = $this->setProductImageInformations($product['id_product'], $product['id_product_attribute']);

            if (is_object($product['image'])) {
                $img_src = _PS_IMG_DIR_.'p/'.$product['image']->getImgPath().'.jpg';
                if (file_exists($img_src)) {
                    $imgSizes = $this->getFinalImgSize($img_src);
                    $product['image_tag'] = '<img src="'.$img_src.'" alt="" width="'.$imgSizes[0].'px" height="'.$imgSizes[1].'px"/>';
                } else {
                    $product['image_tag'] = '';
                }
            } else {
                $product['image_tag'] = '';
            }
        }

        foreach ($summary['gift_products'] as $key => &$gift_product) {
            $gift_product['image'] = $this->setProductImageInformations($gift_product['id_product'], $gift_product['id_product_attribute']);
            if (is_object($gift_product['image'])) {
                $img_src = _PS_IMG_DIR_.'p/'.$gift_product['image']->getImgPath().'.jpg';
                $imgSizes = $this->getFinalImgSize($img_src);
                $gift_product['image_tag'] = '<img src="'.$img_src.'" alt=""  width="'.$imgSizes[0].'px" height="'.$imgSizes[1].'px" />';
            } else {
                $gift_product['image_tag'] = '';
            }
        }

        // Get available cart rules and unset the cart rules already in the cart
        $available_cart_rules = CartRule::getCustomerCartRules(
            $context->language->id,
            (isset($context->customer->id) ? $context->customer->id : 0),
            true,
            true,
            true,
            $cart_obj
        );

        $cart_cart_rules = $cart_obj->getCartRules();

        foreach ($available_cart_rules as $key => $available_cart_rule) {
            if (!$available_cart_rule['highlight'] || strpos($available_cart_rule['code'], 'BO_ORDER_') === 0) {
                unset($available_cart_rules[$key]);
                continue;
            }

            foreach ($cart_cart_rules as $cart_cart_rule) {
                if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule']) {
                    unset($available_cart_rules[$key]);
                    continue 2;
                }
            }
        }

        $show_option_allow_separate_package = (!$cart_obj->isAllProductsInStock(true) && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));
        //fix for ps 1.5.2.0 and minor
        $configSize = Configuration::get('REPAIRSHOP_IMAGESIZE');
        $smallSize = (method_exists('ImageType', 'getFormatedName'))?Image::getSize(ImageType::getFormatedName('small')):Image::getSize($configSize);


        $context->smarty->assign($summary);
        $context->smarty->assign(array(
            //'token_cart' => Tools::getToken(false),
            //'isLogged' => $this->isLogged,
            'isVirtualCart' => $cart_obj->isVirtualCart(),
            'productNumber' => $cart_obj->nbProducts(),
            'voucherAllowed' => CartRule::isFeatureActive(),
            'shippingCost' => $cart_obj->getOrderTotal(true, Cart::ONLY_SHIPPING),
            'shippingCostTaxExc' => $cart_obj->getOrderTotal(false, Cart::ONLY_SHIPPING),
            'customizedDatas' => $customized_datas,
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'lastProductAdded' => $cart_obj->getLastProduct(),
            'currencySign' => $context->currency->sign,
            'currencyRate' => $context->currency->conversion_rate,
            'currencyFormat' => $context->currency->format,
            'currencyBlank' => $context->currency->blank,
            'show_option_allow_separate_package' => $show_option_allow_separate_package,
            'PS_UPLOAD_DIR' => _PS_UPLOAD_DIR_,
            'smallSize' => $smallSize
        ));
    }

    private function setProductImageInformations($product_id, $product_attribute_id)
    {
        if (isset($product_attribute_id) && $product_attribute_id) {
            $id_image = Db::getInstance()->getValue('
                SELECT image_shop.id_image
                FROM '._DB_PREFIX_.'product_attribute_image pai'.
                Shop::addSqlAssociation('image', 'pai', true).'
                WHERE id_product_attribute = '.(int)$product_attribute_id
            );
        }

        if (!isset($id_image) || !$id_image) {
            $id_image = Db::getInstance()->getValue('
                SELECT image_shop.id_image
                FROM '._DB_PREFIX_.'image i'.
                Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1').'
                WHERE i.id_product = '.(int)($product_id)
            );
        }

        if ($id_image) {
            return new Image($id_image);
        }

        return false;
    }

    private function getFinalImgSize($src_file)
    {
        $max_height = 80;
        $max_width = 100;
        list($src_width, $src_height, $type) = getimagesize($src_file);
        $width_diff = $max_width / $src_width;
        $height_diff = $max_height / $src_height;

        if ($width_diff > 1 && $height_diff > 1) {
            $final_width = $src_width;
            $final_height = $src_height;
        } elseif ($width_diff > $height_diff) {
            $final_height = $max_height;
            $final_width = round(($src_width * $max_height) / $src_height);
        } else {
            $final_width = $max_width;
            $final_height = round($src_height * $max_width / $src_width);
        }

        return array($final_width,$final_height);
    }

    public static function l($string)
    {
        return Translate::getModuleTranslation('repairshop', $string, 'repair');
    }

    private function getDataArray($context)
    {
        $customer_obj = new Customer($this->id_customer);


            $customer_message = '';
        

        $data = array(
            '{firstname}' => $customer_obj->firstname,
            '{lastname}' => $customer_obj->lastname,
            '{customerMail}' => $customer_obj->email,
            '{shopName}' => Configuration::get('PS_SHOP_NAME'),
            '{shopUrl}' => $context->shop->domain.$context->shop->physical_uri,
            '{shopMail}' => Configuration::get('PS_SHOP_EMAIL'),
            '{shopTel}' => Configuration::get('PS_SHOP_PHONE'),
            '{customerMessage}' => nl2br($customer_message)
        );

        return $data;
    }

    public static function get_client_repairs($id_customer)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
                SELECT *
                FROM '._DB_PREFIX_.'repair r
                WHERE r.id_customer = '.$id_customer
        );
    }


}