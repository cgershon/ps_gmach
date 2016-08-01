<?php @session_start();
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderControllerCore extends ParentOrderController
{
    public $step;
    const STEP_SUMMARY_EMPTY_CART = -1;
    const STEP_ADDRESSES = 1;
    const STEP_DELIVERY = 2;
    const STEP_PAYMENT = 3;

    /**
     * Initialize order controller
     * @see FrontController::init()
     */
     

    public function init()
    {
        global $orderTotal;

        parent::init();

        $this->step = (int)Tools::getValue('step');
        if (!$this->nbProducts) {
            $this->step = -1;
        }
        $product = $this->context->cart->checkQuantities(true);

        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            $this->step = 0;
            $this->errors[] = sprintf(Tools::displayError('An item in your cart is no longer available (%1s). You cannot proceed with your order.'), Product::getProductName((int)$id_product));
        }

        // If some products have disappear
        if (is_array($product)) {
            $this->step = 0;
            $this->errors[] = sprintf(Tools::displayError('An item (%1s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']);
        }

        // Check minimal amount
        $currency = Currency::getCurrency((int)$this->context->cart->id_currency);

        $orderTotal = $this->context->cart->getOrderTotal();
        $minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase && $this->step > 0) {
            $_GET['step'] = $this->step = 0;
            $this->errors[] = sprintf(
                Tools::displayError('A minimum purchase total of %1s (tax excl.) is required to validate your order, current purchase total is %2s (tax excl.).'),
                Tools::displayPrice($minimal_purchase, $currency), Tools::displayPrice($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS), $currency)
            );
        }
        if (!$this->context->customer->isLogged(true) && in_array($this->step, array(1, 2, 3))) {
            $params = array();
            if ($this->step) {
                $params['step'] = (int)$this->step;
            }
            if ($multi = (int)Tools::getValue('multi-shipping')) {
                $params['multi-shipping'] = $multi;
            }

            $back_url = $this->context->link->getPageLink('order', true, (int)$this->context->language->id, $params);

            $params = array('back' => $back_url);
            if ($multi) {
                $params['multi-shipping'] = $multi;
            }
            if ($guest = (int)Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                $params['display_guest_checkout'] = $guest;
            }

            Tools::redirect($this->context->link->getPageLink('authentication', true, (int)$this->context->language->id, $params));
        }

        if (Tools::getValue('multi-shipping') == 1) {
            $this->context->smarty->assign('multi_shipping', true);
        } else {
            $this->context->smarty->assign('multi_shipping', false);
        }

        if ($this->context->customer->id) {
            $this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
        } else {
            $this->context->smarty->assign('address_list', array());
        }
    }

    public function postProcess()
    {
        // Update carrier selected on preProccess in order to fix a bug of
        // block cart when it's hooked on leftcolumn
        if ($this->step == 3 && Tools::isSubmit('processCarrier')) {
            $this->processCarrier();
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('ajax') && Tools::getValue('method') == 'updateExtraCarrier') {
            // Change virtualy the currents delivery options
            $delivery_option = $this->context->cart->getDeliveryOption();
            $delivery_option[(int)Tools::getValue('id_address')] = Tools::getValue('id_delivery_option');
            $this->context->cart->setDeliveryOption($delivery_option);
            $this->context->cart->save();
            $return = array(
                'content' => Hook::exec(
                    'displayCarrierList',
                    array(
                        'address' => new Address((int)Tools::getValue('id_address'))
                    )
                )
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }

        if ($this->nbProducts) {
            $this->context->smarty->assign('virtual_cart', $this->context->cart->isVirtualCart());
        }

        if (!Tools::getValue('multi-shipping')) {
            $this->context->cart->setNoMultishipping();
        }
     
	  $this->context->smarty->assign('nb_payments', $_SESSION['billing_cycles'] ); // ADDED YGPC

        // Check for alternative payment api
        $is_advanced_payment_api = (bool)Configuration::get('PS_ADVANCED_PAYMENT_API');
//var_dump (  '<br/> STEP: ',  (int)$this->step ); //exit;
        // 4 steps to the order
        switch ((int)$this->step) {

            case OrderController::STEP_SUMMARY_EMPTY_CART:
                $this->context->smarty->assign('empty', 1);
                $this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
            break;

            case OrderController::STEP_ADDRESSES:
                $this->_assignAddress();
                $this->processAddressFormat();
                if (Tools::getValue('multi-shipping') == 1) {
                    $this->_assignSummaryInformations();
                    $this->context->smarty->assign('product_list', $this->context->cart->getProducts());
                    $this->setTemplate(_PS_THEME_DIR_.'order-address-multishipping.tpl');
                } else {
                    $this->setTemplate(_PS_THEME_DIR_.'order-address.tpl');
                }
            break;

            case OrderController::STEP_DELIVERY:
                if (Tools::isSubmit('processAddress')) {
                    $this->processAddress();
                }
                 /* save identity IMG  Added YGPC */
       	 
	          	$img_exists  =	$this-> _saveIdentityImg() ; 
	         
	           if(   !$img_exists   )  // upload or change image
	          	{    //exit ('img') ;
	          		
	           	}
             //   $this->autoStep();
         	   $this-> _saveBankAccount() ; 
                $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
            break;

            case OrderController::STEP_PAYMENT:
                // Check that the conditions (so active) were accepted by the customer
                $cgv = Tools::getValue('cgv') || $this->context->cookie->check_cgv;
                
                
                	$img_exists  =	$this-> _saveIdentityImg() ; 
	          	//var_dump($img_exists  );//exit;
	           if(   !$img_exists   )  // upload or change image
	          	{ 	
	          		 Tools::redirect('index.php?controller=order&step=2');
	           	}
              
                if ($is_advanced_payment_api === false && Configuration::get('PS_CONDITIONS')
                    && (!Validate::isBool($cgv) || $cgv == false)) {
                    Tools::redirect('index.php?controller=order&step=2');
                }

                if ($is_advanced_payment_api === false) {
                    Context::getContext()->cookie->check_cgv = true;
                }

                // Check the delivery option is set
         
                if ($this->context->cart->isVirtualCart() === false) {
                    if (!Tools::getValue('delivery_option') && !Tools::getValue('id_carrier') && !$this->context->cart->delivery_option && !$this->context->cart->id_carrier) {
                        Tools::redirect('index.php?controller=order&step=2');
                    } elseif (!Tools::getValue('id_carrier') && !$this->context->cart->id_carrier) {
                        $deliveries_options = Tools::getValue('delivery_option');
                        if (!$deliveries_options) {
                            $deliveries_options = $this->context->cart->delivery_option;
                        }

                        foreach ($deliveries_options as $delivery_option) {
                            if (empty($delivery_option)) {
                                Tools::redirect('index.php?controller=order&step=2');
                            
                            }
                        }
                    }
                }
                
                /* save datas of bank account Added YGPC */
                
           	  $this-> _saveBankAccount() ; 
                   
                $this->autoStep();

                // Bypass payment step if total is 0
                if (($id_order = $this->_checkFreeOrder()) && $id_order) {
                    if ($this->context->customer->is_guest) {
                        $order = new Order((int)$id_order);
                        $email = $this->context->customer->email;
                        $this->context->customer->mylogout(); // If guest we clear the cookie for security reason
                        Tools::redirect('index.php?controller=guest-tracking&id_order='.urlencode($order->reference).'&email='.urlencode($email));
                    } else {
                        Tools::redirect('index.php?controller=history');
                    }
                }
                $this->_assignPayment();

                if ($is_advanced_payment_api === true) {
                    $this->_assignAddress();
                }
                // assign some informations to display cart
                $this->_assignSummaryInformations();
                $available_tz = FALSE;
                $available_tz = $this->_testTz();	// 
        
	        if ( $available_tz )
		        {
			//	$this->context->smarty->assign('nb_payments', $_SESSION['billing_cycles'] ); // ADDED YGPC

				$this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
      	      }	 
              else
              	{
              		  echo ' נא להכניס תמונה של תעודת זאות בבקשה !';
             	 	  $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
             	 }
             break;

            default:
                $this->_assignSummaryInformations();
                $this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
            break;
        }
    }

    protected function processAddressFormat()
    {
        $addressDelivery = new Address((int)$this->context->cart->id_address_delivery);
        $addressInvoice = new Address((int)$this->context->cart->id_address_invoice);

        $invoiceAddressFields = AddressFormat::getOrderedAddressFields($addressInvoice->id_country, false, true);
        $deliveryAddressFields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country, false, true);

        $this->context->smarty->assign(array(
            'inv_adr_fields' => $invoiceAddressFields,
            'dlv_adr_fields' => $deliveryAddressFields));
    }

    /**
     * Order process controller
     */
    public function autoStep()
    {
        if ($this->step >= 2 && (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice)) 
        {
           
           	  
            Tools::redirect('index.php?controller=order&step=1');
          
        }

        if ($this->step > 2 && !$this->context->cart->isVirtualCart()) {
            $redirect = false;
            if (count($this->context->cart->getDeliveryOptionList()) == 0) {
                $redirect = true;
            }

            $delivery_option = $this->context->cart->getDeliveryOption();
            if (is_array($delivery_option)) {
                $carrier = explode(',', $delivery_option[(int)$this->context->cart->id_address_delivery]);
            }

            if ( !$redirect && !$this->context->cart->isMultiAddressDelivery() ) {
                foreach ($this->context->cart->getProducts() as $product) {
                    $carrier_list = Carrier::getAvailableCarrierList(new Product($product['id_product']), null, $this->context->cart->id_address_delivery);
                    foreach ($carrier as $id_carrier) {
                        if (!in_array($id_carrier, $carrier_list)) {
                            $redirect = true;
                        } else {
                            $redirect = false;
                            break;
                        }
                    }
                    if ($redirect) {
                    
                        break;
                    }
                }
            }

            if ($redirect) {
                Tools::redirect('index.php?controller=order&step=2');
            }
        }

        $delivery = new Address((int)$this->context->cart->id_address_delivery);
        $invoice = new Address((int)$this->context->cart->id_address_invoice);

        if ($delivery->deleted || $invoice->deleted) {
            if ($delivery->deleted) {
                unset($this->context->cart->id_address_delivery);
            }
            if ($invoice->deleted) {
                unset($this->context->cart->id_address_invoice);
            }
            // exit( 'autoStep');
            Tools::redirect('index.php?controller=order&step=1');
        }
    }

    /**
     * Manage address
     */
    public function processAddress()
    {
        $same = Tools::isSubmit('same');
        if (!Tools::getValue('id_address_invoice', false) && !$same) {
            $same = true;
        }

        if (!Customer::customerHasAddress($this->context->customer->id, (int)Tools::getValue('id_address_delivery'))
            || (!$same && Tools::getValue('id_address_delivery') != Tools::getValue('id_address_invoice')
                && !Customer::customerHasAddress($this->context->customer->id, (int)Tools::getValue('id_address_invoice')))) {
            $this->errors[] = Tools::displayError('Invalid address', !Tools::getValue('ajax'));
        } else {
            $this->context->cart->id_address_delivery = (int)Tools::getValue('id_address_delivery');
            $this->context->cart->id_address_invoice = $same ? $this->context->cart->id_address_delivery : (int)Tools::getValue('id_address_invoice');

            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);

            if (!$this->context->cart->update()) {
                $this->errors[] = Tools::displayError('An error occurred while updating your cart.', !Tools::getValue('ajax'));
            }

            if (!$this->context->cart->isMultiAddressDelivery()) {
                $this->context->cart->setNoMultishipping();
            } // If there is only one delivery address, set each delivery address lines with the main delivery address

            if (Tools::isSubmit('message')) {
                $this->_updateMessage(Tools::getValue('message'));
            }

            // Add checking for all addresses
            $errors = array();
            $address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers(false, $errors);
            if (count($address_without_carriers) && !$this->context->cart->isVirtualCart()) {
                $flag_error_message = false;
                foreach ($errors as $error) {
                    if ($error == Carrier::SHIPPING_WEIGHT_EXCEPTION && !$flag_error_message) {
                        $this->errors[] = sprintf(Tools::displayError('The product selection cannot be delivered by the available carrier(s): it is too heavy. Please amend your cart to lower its weight.', !Tools::getValue('ajax')));
                        $flag_error_message = true;
                    } elseif ($error == Carrier::SHIPPING_PRICE_EXCEPTION && !$flag_error_message) {
                        $this->errors[] = sprintf(Tools::displayError('The product selection cannot be delivered by the available carrier(s). Please amend your cart.', !Tools::getValue('ajax')));
                        $flag_error_message = true;
                    } elseif ($error == Carrier::SHIPPING_SIZE_EXCEPTION && !$flag_error_message) {
                        $this->errors[] = sprintf(Tools::displayError('The product selection cannot be delivered by the available carrier(s): its size does not fit. Please amend your cart to reduce its size.', !Tools::getValue('ajax')));
                        $flag_error_message = true;
                    }
                }
                if (count($address_without_carriers) > 1 && !$flag_error_message) {
                    $this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to some addresses you selected.', !Tools::getValue('ajax')));
                } elseif ($this->context->cart->isMultiAddressDelivery() && !$flag_error_message) {
                    $this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to one of the address you selected.', !Tools::getValue('ajax')));
                } elseif (!$flag_error_message) {
                    $this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to the address you selected.', !Tools::getValue('ajax')));
                }
            }
        }

        if ($this->errors) {
            if (Tools::getValue('ajax')) {
                $this->ajaxDie('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
            }
            $this->step = 1;
        }

        if ($this->ajax) {
            $this->ajaxDie(true);
        }
    }

    /**
     * Carrier step
     */
    protected function processCarrier()
    {
        global $orderTotal;
        parent::_processCarrier();

        if (count($this->errors)) {
            $this->context->smarty->assign('errors', $this->errors);
            $this->_assignCarrier();
            $this->step = 2;
            $this->displayContent();
        }
        $orderTotal = $this->context->cart->getOrderTotal();
    }

    /**
     * Address step
     */
    protected function _assignAddress()
    {
        parent::_assignAddress();

        if (Tools::getValue('multi-shipping')) {
            $this->context->cart->autosetProductAddress();
        }

        $this->context->smarty->assign('cart', $this->context->cart);
    }

    /**
     * Carrier step
     */
    protected function _assignCarrier()
    {
        if (!isset($this->context->customer->id)) {
            die(Tools::displayError('Fatal error: No customer'));
        }
        // Assign carrier
        parent::_assignCarrier();
        // Assign wrapping and TOS
        $this->_assignWrappingAndTOS();

        $this->context->smarty->assign(
            array(
                'is_guest' => (isset($this->context->customer->is_guest) ? $this->context->customer->is_guest : 0)
            ));
    }

    /**
     * Payment step
     */
    protected function _assignPayment()
    {
        global $orderTotal;

        // Redirect instead of displaying payment modules if any module are grefted on
        Hook::exec('displayBeforePayment', array('module' => 'order.php?step=3'));

        /* We may need to display an order summary */
        $this->context->smarty->assign($this->context->cart->getSummaryDetails());

        if ((bool)Configuration::get('PS_ADVANCED_PAYMENT_API')) {
            $this->context->cart->checkedTOS = null;
        } else {
            $this->context->cart->checkedTOS = 1;
        }

        // Test if we have to override TOS display through hook
        $hook_override_tos_display = Hook::exec('overrideTOSDisplay');

        $this->context->smarty->assign(array(
            'account_informations' => 'פרטי חשבון בנק להפקדת ההלוואה' ,   // YGPC
            'total_price' => (float)$orderTotal,
            'taxes_enabled' => (int)Configuration::get('PS_TAX'),
            'cms_id' => (int)Configuration::get('PS_CONDITIONS_CMS_ID'),
            'conditions' => (int)Configuration::get('PS_CONDITIONS'),
            'checkedTOS' => (int)$this->context->cart->checkedTOS,
            'override_tos_display' => $hook_override_tos_display
        ));


        parent::_assignPayment();
    }

    public function setMedia()
    {
        parent::setMedia();
        if ($this->step == 2) {
            $this->addJS(_THEME_JS_DIR_.'order-carrier.js');
        }
    }
     /*/////////////////////saveIdentityImg////////////////////////////////*/

   protected function  _saveIdentityImg() 
    {
		        if (!isset($this->context->customer->id)) {
		            die(Tools::displayError('Fatal error: No customer'));
		        }
		       if(   $_POST['change'])
		       	{
		       		// $this->context->smarty->assign( array( 'identity_img_name' => ''  ) );
		       		/*	$sql =  " ";
							$sql =  " SELECT identity_img FROM admin_gmahexpress.ko_address WHERE id_customer = ' ".$id_customer." ' " ;
		
						//var_dump(  ' REQUEST: ',$sql );exit;
						 $img_exists  = Db::getInstance()->executeS( $sql  );
						if ( $img_exists) 
								{
									 $this->context->smarty->assign( 
									 array( 'identity_img_name' => str_replace ( $id_customer.'_' , '' , basename( $img_exists[0]['identity_img'] )  )
									  )  );			
									    exit( ' img !' );	return( TRUE );	  
			    					}*/
		       		 return FALSE ;
		      	}
			        
			$uploaddir = '../../upload/tz/';// image DIR 
			$id_customer = (int)$this->context->customer->id ;	
			if( file_exists ( $_FILES['identity_img']['tmp_name'] ) ) // image was uploaded
					{		 
						 $uploadfile =  $uploaddir .$id_customer; // image file name with id_customer in it 
						 
						$uploadfile .= '_'.basename(  $_FILES['identity_img']['name'] ) ; // name without path 
						$uploadrealfile = '/var/www/vhosts/konim.biz/ps_gmach/upload/tz'.$id_customer.'_'.basename(  $_FILES['identity_img']['name'] ) ;	
						
						$this->context->smarty->assign( array( 'identity_img_name' => $_FILES['identity_img']['name']  ) );
						$this->context->smarty->assign( array(  'identity_img_path' =>  $uploaddir   ) ) ;
						$this->context->smarty->assign( array( 'identity_img_real_name' => $uploadfile ) ) ;
						// smarty  need  $uploadfile relative path it  don't work with full path $uploadrealfile 
						// move_uploaded_file need $uploadrealfile  it don't work with relative path 	$uploadfile
						if (  move_uploaded_file( $_FILES['identity_img']['tmp_name'], $uploadrealfile )  ) 
						  {
								$result = Db::getInstance()->executeS(  'UPDATE  admin_gmahexpress.ko_address SET  identity_img =" '.$uploadfile.' " 											WHERE id_customer = " '.$id_customer.' "  ')  ;
						//	var_dump ($dbh->errorInfo(), $id_customer )  ;	
							}
					   
					 	unset ($_FILES['identity_img']['tmp_name'] )   ;
					 	return( TRUE );
			    	  }
			      else    // image not uploaded yet -> we take the one in db 
			   	   {
			    		  		$sql =  " ";
							$sql =  " SELECT identity_img FROM admin_gmahexpress.ko_address WHERE id_customer = ' ".$id_customer." ' " ;
		
						//var_dump(  ' REQUEST: ',$sql );exit;
						 $img_exists  = Db::getInstance()->executeS( $sql  );
						 
						if ( $img_exists[0]['identity_img']  !== '' ) 
								{	 //exit ('img !!!!!!!!!!!') ;
									 $this->context->smarty->assign( 
									 array( 	'identity_img_name' => str_replace ( $id_customer.'_' , '' , basename( $img_exists[0]['identity_img'] )  ),
									 	 	'identity_img_path' =>  $uploaddir,
									 	 	'identity_img_real_name' => $uploaddir.basename( $img_exists[0]['identity_img'] )  
									  )  );			
									    	return( TRUE );	  
			    					}
			    				
			    		  	return( FALSE );// no image 	
			  	   }
			     	   	
  }
    ////////////////////////////saveBankAccount///////////////////////////////////

   protected function _saveBankAccount()
    { 
	  if (!isset( $this->context->customer->id ) ) {
		            die(Tools::displayError('Fatal error: No customer'));
	    }
		      
	 $id_customer = (int)$this->context->customer->id ;	
		
try{
		//	      throw new Exception(' SQL error BankAccount ');
		if ( isset ( $_POST['bank_name'] ) )
			{
			$sql =  " ";
			$sql =  " UPDATE admin_gmahexpress.ko_address SET bank_name= ' ".$_POST['bank_name'] ." '  , account_number = ' ". 					$_POST['account_number']." ' , bank_number = ' " . $_POST['bank_number']." ' ,
			 		sucursale = ' ".$_POST['sucursale']." ',bank_address1 =' ". $_POST['bank_address1']." ',bank_postcode =' ". $_POST['bank_postcode']." '  , bank_CityName =' ". $_POST['bank_CityName']." ',
			 		bank_country = ' ".$_POST['bank_country']." ',bank_phone =' ". $_POST['bank_phone']." ', identity_number =' ". $_POST['identity_number']." ', message =' ". $_POST['message']." ', cgv =' ". $_POST['cgv']." ' WHERE id_customer = ' ".$id_customer." ' " ;
		
			//var_dump(  ' REQUEST: ',$sql );
			Db::getInstance()->executeS( $sql  );
	
			 $this->context->smarty->assign( array( 'bank_name' => $_POST['bank_name'], 'account_number'=>  $_POST['account_number'] ,'bank_number' =>$_POST['bank_number'],
			 'sucursale' =>$_POST['sucursale']  ,  'bank_address1' =>$_POST['bank_address1'] ,   'bank_postcode' =>$_POST['bank_postcode'] ,  'bank_CityName' =>$_POST['bank_CityName'] ,
			  'bank_country' =>$_POST['bank_country'] ,  'bank_phone' =>$_POST['bank_phone'] , 'identity_number' =>$_POST['identity_number'] ,  'message' =>$_POST['message'] , 'cgv' =>$_POST['cgv'] ) );
					 
		
		}
		else
		{
			$sql =  " ";
			$sql =  " SELECT  bank_name,sucursale,bank_address1,account_number,bank_number,bank_postcode , bank_CityName,bank_country,bank_phone,identity_number,message,cgv FROM admin_gmahexpress.ko_address WHERE id_customer = ' ".$id_customer." ' " ;
		
//var_dump(  ' REQUEST: ',$sql );exit;
		   $bank_datas = Db::getInstance()->executeS( $sql  );
		if ( $bank_datas ) 
			{
			//	var_dump(  ' REQUEST: ',$bank_datas );exit;
			 $this->context->smarty->assign( array( 'bank_name' => $bank_datas[0]['bank_name'], 'account_number'=>  $bank_datas[0]['account_number'] ,'bank_number' =>$bank_datas[0]['bank_number'],
			 'sucursale' =>$bank_datas[0]['sucursale']  ,  'bank_address1' =>$bank_datas[0]['bank_address1'] ,   'bank_postcode' =>$bank_datas[0]['bank_postcode'] ,  'bank_CityName' =>$bank_datas[0]['bank_CityName'] ,
			  'bank_country' =>$bank_datas[0]['bank_country'] ,  'bank_phone' =>$bank_datas[0]['bank_phone'] , 'identity_number' =>$bank_datas[0]['identity_number'] ,  'message' =>$bank_datas[0]['message'] , 'cgv' =>$bank_datas[0]['cgv'] ) );
			}
		}	
			
	} // end of try 
catch( Exception $ex )
		       {	
		       	//print_r( $dbh->errorInfo(), true );		
		        	//echo     'billing_cycle: '.$_SESSION['billing_cycle'];
		       	 exit('END with error saveBankAccount ');
		       }       
   }
    
    /* ********************************_testTz() ****************************************** */
    
    public function _testTz()
    {
    	   $id_customer = (int)$this->context->customer->id ;	
  	   $sql = new DbQuery();

        // Build SELECT
       $sql->select('identity_img'); // test IMG of TZ  file name available 
       // Build FROM
       $sql->from('address');
       // Build WHERE clauses  
       $sql->where(' id_customer = '.  $id_customer  );
          $result = FALSE; 
    	$result = Db::getInstance()->executeS( $sql );
        // var_dump(  $result ); 
     	  return (   (bool)$result ) ;
    
    
    
    
    }
}