<?php
/**
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
 /* Do not edit or add to this file if you wish to upgrade Prestashop to newer
 * versions in the future.
 * ****************************************************
 *
 *  @author     BEST-KIT.COM (contact@best-kit.com)
 *  @copyright  http://best-kit.com
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'bestkit_psubscription/classes/BestkitPsubscriptionProduct.php';
require_once _PS_MODULE_DIR_ . 'bestkit_psubscription/classes/BestkitPsubscriptionPeriod.php';

class bestkit_psubscription extends PaymentModule
{
	const PREFIX = 'bestkikPP_';

	protected $_config = array(
		'api_username' => '',
		'api_password' => '',
		'api_signature' => '',
		'terms_and_conditions' => '',
		'currency' => array('USD', 'GBP', 'EUR', 'JPY', 'CAD', 'AUD', 'RUB'),
		'payment_type' => array('Sale'),
		'environment' => array('sandbox', 'live'),
	);

	protected $_hooks = array(
		'payment',
		'paymentReturn',
		'displayHeader',
		'displayProductPriceBlock',
        'displayAdminProductsExtra',
        'displayProductButtons',
        'actionProductUpdate',
        'actionCartSave',
        'displayAdminOrderContentOrder',
        'actionObjectCartUpdateAfter',
        'actionObjectCartAddAfter',
	);

    protected $_tabs = array(
        array(
            'class_name' => 'AdminBestkitPsubscriptionPeriod',
            'parent' => 'AdminCatalog',
            'name' => 'Paypal billing periods'
        ),
    );

    public function __construct()
    {
        $this->name = 'bestkit_psubscription';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.3';
        $this->author = 'best-kit.com';
        $this->need_instance = 0;
        $this->module_key = '9d20d0bc6af11ae5e60420da677720de';

        parent::__construct();

        $this->displayName = $this->l('PayPal Recurring Payments');
        $this->description = $this->l('PayPal Recurring Payments & Subscriptions');
    }

	public static function useConfig($name, $value = null)
	{
		$name = self::PREFIX . $name;
		if (is_null($value)) {
			return Configuration::get($name);
		} else {
			return Configuration::updateValue($name, $value);
		}

		return FALSE;
	}

	public function install()
	{
		if (!function_exists('curl_version')) {
			return false;
		}

		$install = parent::install();
		
		foreach ($this->_hooks as $hook) {
			$this->registerHook($hook);
		}

		$this->updatePosition(Hook::getIdByName('displayProductButtons'), 0, 1);

		foreach ($this->_config as $name => $value) {
			if (is_array($value)) {
				self::useConfig($name, current($value));
			} else {
				self::useConfig($name, $value);
			}
		}

		$sql = array(
			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_psubscription_product` (
    		  `id_bestkit_psubscription_product` int(10) unsigned NOT NULL auto_increment,
    		  `id_product` int(10) NOT NULL,
    		  `id_shop` int(10) NOT NULL,
    		  `is_enabled` int(1) NOT NULL,
    		  `subscription_only` int(1) NOT NULL,
              `period_ids` text NOT NULL,
    		  PRIMARY KEY (`id_bestkit_psubscription_product`, `id_product`, `id_shop`)
    		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',

			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_psubscription_period` (
    		  `id_bestkit_psubscription_period` int(10) unsigned NOT NULL auto_increment,
    		  `billing_period` varchar(255) NOT NULL,
    		  `billing_freq` int(10) NOT NULL,
    		  `billing_cycles` int(10) NOT NULL,
    		  `allowed_start_days` varchar(255) NOT NULL,
    		  PRIMARY KEY (`id_bestkit_psubscription_period`)
    		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',

			'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_psubscription_period_lang` (
    		  `id_bestkit_psubscription_period` int(10) unsigned NOT NULL auto_increment,
    		  `id_lang` int(10) NOT NULL,
    		  `title` varchar(255) NOT NULL,
    		  PRIMARY KEY (`id_bestkit_psubscription_period`, `id_lang`)
    		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
		);

		$cart_product = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'cart_product`');
		if (!array_key_exists('bestkit_psubscription', $cart_product)) {
			$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'cart_product` ADD COLUMN `bestkit_psubscription` varchar(255) NOT NULL';
		}

		foreach ($sql as $query) {
			Db::getInstance()->Execute($query);
		}

        $languages = Language::getLanguages();
        foreach ($this->_tabs as $tab) {
            $_tab = new Tab();
            $_tab->class_name = $tab['class_name'];
            $_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
            if (empty($_tab->id_parent)) {
                $_tab->id_parent = 0;
            }
            
            $_tab->module = $this->name;
            foreach ($languages as $language) {
                $_tab->name[$language['id_lang']] = $this->l($tab['name']);
            }

            $_tab->add();
        }

		return $install;
	}

	public function uninstall()
	{
		foreach ($this->_hooks as $hook) {
			$this->unregisterHook($hook);
		}

		$sql = array("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "bestkit_psubscription_product`, 
    		    `" . _DB_PREFIX_ . "bestkit_psubscription_period`, 
                `" . _DB_PREFIX_ . "bestkit_psubscription_period_lang`;");

		foreach ($sql as $query) {
			Db::getInstance()->Execute($query);
		}

        foreach ($this->_tabs as $tab) {
            $idTab = Tab::getIdFromClassName($tab['class_name']);
            if ($idTab) {
                $_tab = new Tab($idTab);
                $_tab->delete();
            }
        }

		return parent::uninstall();
	}

	public static function prepareArrayConfig($array)
	{
		$new_array = array();
		foreach ($array as $value) {
			$new_array[] = array(
				'id' => $value,
				'label' => Tools::ucfirst($value),
			);
		}

		return $new_array;
	}

	public function getIpnToken()
	{
		return md5(_COOKIE_KEY_ . md5(_PS_CREATION_DATE_) . _PS_VERSION_);
	}

	public function hookDisplayAdminProductsExtra()
	{
        $id_product = (int)Tools::getValue('id_product');
        if ($id_product) {
            $stores = Shop::getContextListShopID();
            if (count($stores) > 1) {
                return $this->l('Please select a shop!');
            }

	        $helper = new HelperForm();
	        $helper->module = $this;
	        $helper->title = $this->displayName;
	        $helper->first_call = false;

	        $this->fields_form[0]['form'] = array(
	            'tinymce' => TRUE,
	            'legend' => array('title' => $this->displayName, 'image' => $this->_path . 'logo.gif'),
	            'input' => array(
	                array(
	                    'type' => 'hidden',
	                    'name' => 'bestkit_psubscription[id_bestkit_psubscription_product]',
	                ),
	                array(
	                    'type' => 'switch',
	                    'label' => $this->l('Enable PayPal Recurring Payments:'),
	                    'name' => 'bestkit_psubscription[is_enabled]',
	                    'class' => 't',
	                    'is_bool' => TRUE,
	                    'values' => array(array(
			                'id' => 'is_enabled_on',
			                'value' => 1), array(
			                'id' => 'is_enabled_off',
			                'value' => 0)
			            )
	                ),
	                array(
	                    'type' => 'switch',
	                    'label' => $this->l('Subscription Only:'),
	                    'name' => 'bestkit_psubscription[subscription_only]',
	                    'class' => 't',
	                    'is_bool' => TRUE,
	                    'values' => array(array(
			                'id' => 'is_enabled_on',
			                'value' => 1), array(
			                'id' => 'is_enabled_off',
			                'value' => 0)
			            )
	                ),
	                array(
	                    'type' => 'select',
	                    'label' => $this->l('Allowed Billing Periods:'),
	                    'name' => 'bestkit_psubscription[period_ids][]',
	                    'multiple' => true,
	                    'size' => 7,
	                    'style' => 'width:100px',
	                    'options' => array(
	                        'query' => BestkitPsubscriptionPeriod::getConfigCollection(),
	                        'id' => 'id_bestkit_psubscription_period',
	                        'name' => 'title',
	                    ),
	                    'required' => true,
	                ),
	            ),
	            'submit' => array(
	                'name' => 'submitAddproductAndStay',
	                'title' => $this->l('   Save And Stay  '),
				),
	        );

	        $paypalProduct = BestkitPsubscriptionProduct::getProductData($id_product, $stores[0]);
			$helper->fields_value['bestkit_psubscription[is_enabled]'] = $paypalProduct->is_enabled;
			$helper->fields_value['bestkit_psubscription[subscription_only]'] = $paypalProduct->subscription_only;
			$helper->fields_value['bestkit_psubscription[period_ids][]'] = $paypalProduct->getPeriodIds();
			$helper->fields_value['bestkit_psubscription[id_bestkit_psubscription_product]'] = $paypalProduct->id;

	        return $helper->generateForm($this->fields_form);
		}
	}

	public function hookActionProductUpdate($params)
	{
		if (Tools::getIsset('bestkit_psubscription')) {
			$data = Tools::getValue('bestkit_psubscription');
			$data['period_ids'] = implode(',', $data['period_ids']);
			$data['id_product'] = (int)Tools::getValue('id_product');
			$data['id_shop'] = current(Shop::getContextListShopID());
			BestkitPsubscriptionProduct::setProductData($data);
		}
	}

    public function hookDisplayHeader($params)
    {
    	$this->context->controller->addCss($this->_path . 'css/front.css', 'all');
    	$this->context->controller->addJs($this->_path . 'js/override.js');

    	$controller = $this->context->controller->php_self;
    	if ($controller == 'product') {
    		$this->context->controller->removeJS(_THEME_JS_DIR_.'product.js');
    		$this->context->controller->addJs($this->_path . 'js/product.js');
			$this->context->controller->addJs($this->_path . 'js/front.js');
			$this->context->controller->addJqueryUI('ui.datepicker');
		}

		if ($this->context->controller instanceof ParentOrderController) {
			$this->context->controller->addJs($this->_path . 'js/order.js');
		}
		
		$this->smarty->assign(array(
			'subscription_count' => $this->allowToOrder(),
			'id_cms_of_terms' => (int)$this->useConfig('terms_and_conditions'),
			'subscribe' =>'החזיר בתשלמים '
		));

		return $this->display(__FILE__, 'header.tpl');
    }

	public function hookDisplayProductPriceBlock($params)
	{
		$controller = $this->context->controller->php_self;
		$shop = current(Shop::getContextListShopID());
		if ($controller != 'product' && isset($params['type']) && $params['type'] == 'weight') {
			$paypalProduct = BestkitPsubscriptionProduct::getProductData($params['product']['id_product'], $shop);
			if ($paypalProduct->is_enabled && $paypalProduct->subscription_only) {
				return '<script>overrideAddToSubscribeBestkit('.$params['product']['id_product'].')</script>';
			}
		}

		return null;
	}

	public function hookDisplayProductButtons($params)
	{
		$shop = current(Shop::getContextListShopID());
		$paypalProduct = BestkitPsubscriptionProduct::getProductData($params['product']->id, $shop);
		if ($paypalProduct->is_enabled && count($paypalProduct->getPeriodIds())) {
			$periods = new Collection('BestkitPsubscriptionPeriod', $this->context->language->id);
			$periods->where('id_bestkit_psubscription_period', 'IN', $paypalProduct->getPeriodIds());

			$this->context->smarty->assign(array(
				'paypalProduct' => $paypalProduct,
				'periods' => $periods,
				'bestkitPath' => $this->_path,
				'Start_date' => date("Y-m-d"),
				'subscribe' =>'החזיר בתשלומים'
			));

			return $this->display(__FILE__, 'productButtons.tpl');
		}

		return null;
	}
	
	public function hookActionObjectCartAddAfter($params)
	{
		return $this->hookActionCartSave($params);
	}

	public function hookActionObjectCartUpdateAfter($params)
	{
		return $this->hookActionCartSave($params);
	}

	public function hookActionCartSave($params)
	{
		if (isset($params['cart'])) {
			$cart = $params['cart'];
		} else if (isset($params['object'])) {
			$cart = $params['object'];
		} else if (isset(Context::getContext()->cart->id)) {
			$cart = Context::getContext()->cart;
		} else {
			return;
		}

		if (Tools::getIsset('bestkit_psubscription')) {
			if ($period = (int)Tools::getValue('period')) {
				$start_date = Tools::getValue('start_date');

				$id_cart = (int)$cart->id;
				$id_product = (int)Tools::getValue('id_product', null);
				$id_product_attribute = (int)Tools::getValue('id_product_attribute', Tools::getValue('ipa'));

				$_period = new BestkitPsubscriptionPeriod($period);
				if (!$_period->id) {
					die($this->l('Invalid subscription period!'));
				}

				$today = strtotime(date('Y-m-d', time())); 
				$_start_date = strtotime(date('Y-m-d', (int)strtotime($start_date))); 

				if ($today > $_start_date) {
					die($this->l('Invalid subscription date!'));
				}

				$bestkit_psubscription = array('id_period' => $period, 'start_date' => $start_date);
				Db::getInstance()->execute('
					UPDATE `' . _DB_PREFIX_ . 'cart_product`
					SET `bestkit_psubscription` = "' . pSQL(serialize($bestkit_psubscription)) . '"
					WHERE `id_product` = ' . (int)$id_product .
					' AND `id_product_attribute` = '.(int)$id_product_attribute .
					' AND `id_cart` = '. $id_cart .
					' LIMIT 1'
				);
			}
		}
	}

	public function allowToOrder()
	{
		if (!$this->active) {
			return false;
		}

		/*$iso_code = $this->context->currency->iso_code;
		if (!in_array($iso_code, $this->_config['currency'])) {
			return false;
		}*/

		if (!$this->context->cart->id) {
			return false;
		}

		$subscriptionProducts = (int)Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'cart_product`
			WHERE `bestkit_psubscription` IS NOT NULL 
			AND `bestkit_psubscription` != ""
			AND `id_cart` = ' . $this->context->cart->id . '
		');

		return $subscriptionProducts;
	}

	public function hookPayment($params)
	{
		if (!$this->allowToOrder()) {
			return;
		}

		$this->smarty->assign(array(
			'this_path' => $this->_path,
		));

		$products = Db::getInstance()->executeS('
			SELECT * FROM `' . _DB_PREFIX_ . 'cart_product`
			WHERE `id_cart` = ' . $this->context->cart->id . '
		');

		$this->context->smarty->assign('totalCartCount', count($products));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active) {
			return;
		}

		$state = $params['objOrder']->getCurrentState();
		if ($state != Configuration::get('PS_OS_ERROR')) {
			$this->smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
				'id_order' => $params['objOrder']->id
			));

			if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference)) {
				$this->smarty->assign('reference', $params['objOrder']->reference);
			}
		} else {
			$this->smarty->assign('error', urldecode(Tools::getValue('error')));
		}

		return $this->display(__FILE__, 'payment_return.tpl');
	}

    private function initConfigForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->toolbar_scroll = TRUE;
        $helper->title = $this->displayName;
        $helper->submit_action = $this->name;

        $this->fields_form[0]['form'] = array(
            'tinymce' => TRUE,
            'legend' => array('title' => $this->displayName, 'image' => $this->_path . 'logo.gif'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('API Username:'),
                    'name' => 'api_username',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('API Password:'),
                    'name' => 'api_password',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('API Signature:'),
                    'name' => 'api_signature',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Environment:'),
                    'name' => 'environment',
                    'style' => 'width:100px',
                    'options' => array(
                        'query' => self::prepareArrayConfig($this->_config['environment']),
                        'id' => 'id',
                        'name' => 'label',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Terms And Conditions of the Subscriptions:'),
                    'name' => 'terms_and_conditions',
                    'style' => 'height:100px',
                    'options' => array(
                        'query' => CMS::listCms($this->context->language->id),
                        'id' => 'id_cms',
                        'name' => 'meta_title',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Allowed Payment Currencies:'),
                    'name' => 'currency',
                    'style' => 'width:100px',
                    'multiple' => true,
                    'size' => 7,
                    'disabled' => true,
                    'options' => array(
                        'query' => self::prepareArrayConfig($this->_config['currency']),
                        'id' => 'id',
                        'name' => 'label',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Payment Type:'),
                    'name' => 'payment_type',
                    'style' => 'width:100px',
                    'options' => array(
                        'query' => self::prepareArrayConfig($this->_config['payment_type']),
                        'id' => 'id',
                        'name' => 'label',
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('IPN handler URL:'),
                    'name' => 'ipn_url',
                ),
            ),
            'submit' => array(
                'name' => $this->name,
                'title' => $this->l('   Save  '),
			),
        );

        return $helper;
    }

    protected function postProcess()
    {
        if (Tools::getIsset($this->name)) {
           foreach ($this->_config as $config => $default) {
	           if ($value = Tools::getValue($config)) {
		           self::useConfig($config, $value);
	           } else {
		           self::useConfig($config, $default);
	           }
           }

		   Tools::redirectAdmin('index.php?tab=AdminModules&conf=4&configure=' . $this->name
		   . '&token=' . Tools::getAdminToken('AdminModules'
		   . (int)(Tab::getIdFromClassName('AdminModules'))
		   . (int)$this->context->employee->id));
        }
    }

    public function getContent()
    {
    	$this->bootstrap = true;
        $this->postProcess();
        $helper = $this->initConfigForm();
        foreach ($this->fields_form[0]['form']['input'] as $input) {
            $helper->fields_value[$input['name']] = self::useConfig($input['name']);
        }

		$helper->fields_value['ipn_url'] = $this->context->link->getModuleLink('bestkit_psubscription', 'ipn', array('token' => $this->getIpnToken()), true);

        return $helper->generateForm($this->fields_form) 
        . '<script>$("select#currency, select#payment_type").attr("disabled", true)</script>';
    }

    private function initToolbar()
    {
        $this->toolbar_btn['save'] = array('href' => '#', 'desc' => $this->l('Save'));
        return $this->toolbar_btn;
    }

	public function hookdisplayAdminOrderContentOrder($params)
	{
		if (isset($params['order']) && $params['order']->module == $this->name) {
			return '<style>#formAddPaymentPanel .alert-danger { display: none !important; }</style>';
		}
	}
}
