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

class Bestkit_psubscriptionfrontModuleFrontController extends
    ModuleFrontController
{
    public $ssl = true;
    //public $auth = true;
    public $display_column_left = false;
  
	protected function allowToOrder()
	{
		if (!$this->context->cart->id) {
			Tools::redirect('index.php?controller=order');
		}

		if ($this->module->allowToOrder()) {
			$products = Db::getInstance()->executeS('
				SELECT * FROM `' . _DB_PREFIX_ . 'cart_product`
				WHERE `id_cart` = ' . $this->context->cart->id . '
			');

			if (count($products) > 1) {
				Tools::redirect('index.php?controller=order');
			}
			
			return true;
		}
		
		return false;
	}

	public function initContent()
	{
        $this->response = false;
        if (Tools::isSubmit('action')) {
            $actionName = Tools::getValue('action', '') . 'Action';
            if (method_exists($this, $actionName)) {
                if ($this->response = $this->$actionName()) {
                	$result = (array)$this->response['result'];
                	if (isset($result['ACK'])) {
	                	if (in_array(Tools::strtoupper($result['ACK']), array('SUCCESS', 'SUCCESSWITHWARNING'))) {
	                		$obj = $this->response['object'];
							$id_order = urldecode($obj->id_order);
							$_order = new Order($id_order);
							
							//$_order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));

							Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)($_order->id_cart).'&id_module='.(int)($this->module->id).'&id_order='.$id_order.'&key='.$_order->getCustomer()->secure_key);

	                	} else if (isset($result['L_LONGMESSAGE0'])) {
		                	$error = $result['L_LONGMESSAGE0'];
		                	if (isset($result['order'])) {
			                	$_order = $result['order'];
		                	}
		                	
		                	if (isset($this->response['object']->order)) {
			                	$_order = $this->response['object']->order;
		                	}
		                	
		                	if (!isset($_order)) {
			                	die(urldecode($error));
		                	}

		                	if ($_order->getCurrentState() != Configuration::get('PS_OS_ERROR')) {
			                	$_order->setCurrentState(Configuration::get('PS_OS_ERROR'));
		                	}

		                	Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)($_order->id_cart).'&id_module='.(int)($this->module->id).'&id_order='.$_order->id.'&key='.$_order->getCustomer()->secure_key.'&error='.$error);
	                	}
                	} else {
                		$order_id = (int)Tools::getValue('id_order');
                		$_order = new Order($order_id);
                		if ($_order->id && $_order->id_customer == Context::getContext()->customer->id) {
                			if ($_order->getCurrentState() != Configuration::get('PS_OS_CANCELED')) {
	                			$_order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
	                		}
                		}

                		parent::initContent();
	                	$this->setTemplate('front.tpl');
                	}
                }
                
                return;
            }
        }

        Tools::redirect('index.php?controller=order');
	}

	protected function initPaypal()
	{
		require_once _PS_MODULE_DIR_ . 'bestkit_psubscription/lib/paypalapi.php';

		$obj = new paypal_recurring;

		$obj->environment = bestkit_psubscription::useConfig('environment');
		$obj->paymentType = urlencode(bestkit_psubscription::useConfig('payment_type'));

		/* PAYPAL API  DETAILS */
		$obj->API_UserName = urlencode(bestkit_psubscription::useConfig('api_username'));
		$obj->API_Password = urlencode(bestkit_psubscription::useConfig('api_password'));
		$obj->API_Signature = urlencode(bestkit_psubscription::useConfig('api_signature'));
		if ($obj->environment == 'sandbox') {
			$obj->API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
		} else {
			$obj->API_Endpoint = "https://api-3t.paypal.com/nvp";
		}

		/*SET SUCCESS AND FAIL URL*/
		//$link = Context::getContext()->link; 

		return $obj;
	}

	public function goAction()
	{
		if ($this->allowToOrder()) {
			$cart = $this->context->cart;
	
			if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
				Tools::redirect('index.php?controller=order&step=1');
			}

			$customer = new Customer($cart->id_customer);
			if (!Validate::isLoadedObject($customer)) {
				Tools::redirect('index.php?controller=order&step=1');
			}
        // ADDED       YGPC
     
      	$nb_payments = Db::getInstance()->executeS('
				SELECT billing_cycles FROM `' . _DB_PREFIX_ . 'bestkit_psubscription_period`
				WHERE `id_bestkit_psubscription_period` = "2" ' );
     var_dump(	$nb_payments  )  ;  
    ///////////////////////////////////////////////////////////////
			$currency = $this->context->currency;
			$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
      $total =  (int) ( $total / $nb_payments[0]['billing_cycles']  )  ;                                           // ADDED YGPC
			$message = '';
			$products = Db::getInstance()->executeS('
				SELECT * FROM `' . _DB_PREFIX_ . 'cart_product`
				WHERE `id_cart` = ' . $cart->id . '
			');

			foreach ($products as $product) {
				$data = unserialize($product['bestkit_psubscription']);
				$period = new BestkitPsubscriptionPeriod($data['id_period'], Context::getContext()->language->id);
				$message .= $this->module->l('Product ID: ') . $product['id_product'] . '; ' . chr(10);
				$message .= $this->module->l('Period name: ') . $period->title . '; ' . chr(10);
				$message .= $this->module->l('Start date: ') . $data['start_date'] . '; ' . chr(10);
				$message .= $this->module->l('Billing period: ') . $period->billing_period . '; ' . chr(10);
				$message .= $this->module->l('Billing freq: ') . $period->billing_freq . '; ' . chr(10);
			}

			if ($this->module->validateOrder((int)$cart->id, Configuration::get('PS_OS_PAYPAL'), $total, $this->module->displayName, $message, array(), (int)$currency->id, false, $customer->secure_key)) {
				$id_order = $this->module->currentOrder;
				$_order = new Order($id_order);

				$obj = $this->initPaypal();
				$link = Context::getContext()->link;
				$obj->order = $_order;
				$obj->currencyID = $currency->iso_code;
				$obj->paymentAmount = (int)( urlencode((float)$_order->getOrdersTotalPaid())  /  $nb_payments[0]['billing_cycles'] ) ;   // ADDED YGPC 
				$obj->description = urlencode( Tools::substr('PayPal Subscription. Order Ref. ' . $_order->reference, 0, 120) ).' ???????? ' ;
				$obj->cancelURL = urlencode($link->getModuleLink('bestkit_psubscription', 'front', 
					array(
						'action' => 'errorFromPaypal', 
						'id_order' => $id_order,
					)
				));

				$obj->returnURL = urlencode($link->getModuleLink('bestkit_psubscription', 'front',
					array(
						'action' => 'returnFromPaypal',
						'id_order' => $id_order,
					)
				));

				return array('result' => $obj->setExpressCheckout());
			}		
		}

		Tools::redirect('index.php?controller=order');
	}

	public function returnFromPaypalAction()
	{
		$obj = $this->initPaypal();
		$obj->id_order = (int)Tools::getValue('id_order');
		$_order = new Order($obj->id_order);
		$obj->order = $_order;
		$obj->currencyID = $this->context->currency->iso_code;
		$obj->paymentAmount = urlencode((float)$_order->getOrdersTotalPaid());
		$obj->description = urlencode(Tools::substr('PayPal Subscription. Order Ref. ' . $_order->reference, 0, 120)).' ???????? ' ;
		$obj->notify_url = urlencode($this->context->link->getModuleLink('bestkit_psubscription', 'ipn', array('token' => $this->module->getIpnToken()), true));

		return array('result' => $obj->getExpressCheckout(), 'object' => $obj);
	}

	public function errorFromPaypalAction()
	{
		return array('result' => $_REQUEST);
	}

	public function frontAction()
	{
		return array('result' => 'front');
	}

	public function cartDetailsAction()
	{
		$result = array();
		$items = Tools::getValue('items');
		foreach ($items as $item) {
			$_item = explode('_', $item);
			if (count($_item) == 4) {
				$sql = 'SELECT cp.`bestkit_psubscription` FROM `' . _DB_PREFIX_ . 'cart_product` cp
					WHERE cp.`id_product` = ' . (int)$_item[0] . '
					AND cp.`id_product_attribute` = ' . (int)$_item[1] . '
					AND cp.`id_cart` = ' . (int)Context::getContext()->cart->id . '
					AND cp.`id_address_delivery` = ' . (int)$_item[3];

				if ($row = Db::getInstance()->getRow($sql)) {				
					if ($row['bestkit_psubscription']) {
						$data = unserialize($row['bestkit_psubscription']);
						$paypalProduct = BestkitPsubscriptionProduct::getProductData((int)$_item[0], current(Shop::getContextListShopID()));
						$period = new BestkitPsubscriptionPeriod($data['id_period'], Context::getContext()->language->id);
						$data['row'] = 'product_' . $item;
						$data['period'] = $period->title;
						$data['only_subscription'] = (int)$paypalProduct->subscription_only;
						$result[] = $data;
					}
				}
			}
		}
		
		die(Tools::jsonEncode($result));
	}

	public function paymentAction()
	{
  
   
  
		if ($this->allowToOrder()) {
			$this->display_column_left = false;
			parent::initContent();
			$cart = Context::getContext()->cart;
      
       // ADDED       YGPC
     
      	$nb_payments = Db::getInstance()->executeS('
				SELECT billing_cycles FROM `' . _DB_PREFIX_ . 'bestkit_psubscription_period`
				WHERE `id_bestkit_psubscription_period` = "2" ' );
 
    ///////////////////////////////////////////////////////////////
      
			$this->context->smarty->assign(array(
				'nbProducts' => $cart->nbProducts(),
				'cust_currency' => $cart->id_currency,
				'currencies' => $this->module->getCurrency((int)$cart->id_currency),
        'total' => (float)$cart->getOrderTotal(true, Cart::BOTH) ,
				'sub_total' => (int)( (float)$cart->getOrderTotal(true, Cart::BOTH) /  $nb_payments [0]['billing_cycles'] ) ,
        'nb_payments' =>   $nb_payments [0]['billing_cycles'] ,
				'isoCode' => $this->context->language->iso_code,
				'this_path' => $this->module->getPathUri(),
				'this_path_cheque' => $this->module->getPathUri(),
				'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
			
			));   //  
	    
			$this->setTemplate('payment_execution.tpl');
		} else {
			Tools::redirect('index.php?controller=order');
		}
	}
}
