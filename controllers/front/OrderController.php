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
	          	{ //	var_dump($img_exists  );exit;
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
                if (($id_order = $this->_checkFreeOrder()    ) &&  $id_order  ) {
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
                $available_tz = $this->_testTz();	// test if there is an image of tz
               // SEND MAIL WAIT FOR AGREMENT ADDED YGPC
	        if ( $available_tz )
		    {	
		    		global $cookie;
		    
		    		$to =  $this->context->customer->email ;//"somebody@example.com";
				$tpl_name= 'gmach_validation';
				$options['subject'] = "בקשה בטיפול";  
				$tz_path= "/var/www/vhosts/konim.biz/ps_gmach/upload/tz/";
				$options['datas'] = array('{nom}'  => $this->context->customer->firstname,  '{prenom}'  => $this->context->customer->lastname ,				
				'{tz}'=>$tz_path. basename( $available_tz ) ,  'tz'=>$tz_path. basename( $available_tz )  );
				$options['dir_tpl']= _PS_MAIL_DIR_;
				$options['{firstname}'] = $this->context->customer->firstname;
				$options['{lastname}'] = $this->context->customer->lastname;
				$options['{tz}'] = $_SESSION['identity_number'];
				$options['{uploadrealfile}'] = $_SESSION['uploadrealfile'];
				$options['{uploadfile}'] = $_SESSION['identity_img'];
				
				$result = $this->send_mail( $to, $tpl_name,$options ,null ) ;  // send a mail to say that the asked loan  is in process
				
				// inform the manager 
				$Gmach_mail="gmach@ygpc.net";
				$tpl_name='manager_validation';
                          $to=$Gmach_mail;
                          $filename =basename( $available_tz );
                          $file_attachment['rename'] = uniqid().Tools::strtolower(substr(   $filename , -5 ) );
                          //$file_attachment['content'] = file_get_contents(  $file_tmp_name );
		            
		            $file_attachment['tmp_name'] =  $_SESSION['uploadrealfile'];
		            $file_attachment['name']     =   $filename;
		            $file_attachment['mime']     =  filetype( $_SESSION['uploadrealfile'] );
		            $file_attachment['error']    =  'tz_error';
		            $file_attachment['size']     = filesize( $_SESSION['uploadrealfile'] );
                      //   var_dump( __LINE__ , $file_attachment);exit;
				$result = $this->send_mail( $to, $tpl_name,$options , $file_attachment  )  ;  // send a mail to ask the manager to valid the loan .
			  var_dump( __LINE__ , $result );exit;
			   if( $this->validate() )    // test if the manager give his agrement  according to the identity card and if there is no current loan active.	
	               	   $this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
	          	   else
	               	   $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');// loop to the bank form
	      	 }	 
                else
              	{
              		  echo ' <script type=text/javascript>alert("נא להכניס תמונה של תעודת זהות בבקשה !")</script></Div>';
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
							$sql =  " SELECT identity_img FROM admin_gmahexpress.ko_message WHERE id_customer = ' ".$id_customer." ' " ;
		
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
					{	//var_dump (__LINE__,	$_SESSION['identity_img']  )  ;	exit;	 
						 $uploadfile =  $uploaddir .$id_customer; // image file name with id_customer in it             ../../upload/tz/1
						 
						$uploadfile .= '_'.basename(  $_FILES['identity_img']['name'] ) ; // name without path         ../../upload/tz/1_img_name.jpg
						$uploadrealfile = '/var/www/vhosts/konim.biz/ps_gmach/upload/tz/'.$id_customer.'_'.basename(  $_FILES['identity_img']['name'] ) ;	
						
						$this->context->smarty->assign( array( 'identity_img_name' => $_FILES['identity_img']['name']  ) );
						$this->context->smarty->assign( array(  'identity_img_path' =>  $uploaddir   ) ) ;
						$this->context->smarty->assign( array( 'identity_img_real_name' => $uploadfile ) ) ;
						// smarty  need  $uploadfile relative path it  don't work with full path $uploadrealfile 
						// move_uploaded_file need $uploadrealfile  it don't work with relative path 	$uploadfile
						if (  move_uploaded_file( $_FILES['identity_img']['tmp_name'], $uploadrealfile )  ) // copy the tmp file to the real path 
						  {
							/*
								$result = Db::getInstance()->executeS(  'UPDATE  admin_gmahexpress.ko_address SET  identity_img =" '.$uploadfile.' " 											WHERE id_customer = " '.$id_customer.' "  ')  ;
								
							*/
							$_SESSION['identity_img'] =$uploadfile;
							$_SESSION['uploadrealfile'] =$uploadrealfile;	
					//	var_dump (__LINE__,	$_SESSION['identity_img']  )  ;	exit;
							}
					 	$_SESSION['img_tmp_name'] =   'identity_img';
					 //	unset ($_FILES['identity_img']['tmp_name'] )   ;
					 	return( TRUE );
			    	  }
			      else    //if  image not uploaded -> we take the one in cache 
			   	   {
			   	   	   
			   	   	   if( $_SESSION['uploadrealfile'] !== ''  )
			   	   	   	{	
			   	   	   	 $identity_img = $_SESSION['identity_img'] ;
			   	   	   	 $this->context->smarty->assign( 
									 array( 	'identity_img_name' => str_replace ( $id_customer.'_' , '' , basename( $identity_img )  ),
									 	 	'identity_img_path' =>  $uploaddir,
									 	 	'identity_img_real_name' => $identity_img
									  )  );
						//	var_dump (__LINE__,	$_SESSION['identity_img'] ,$_SESSION['uploadrealfile'] )  ;	exit;				
									    	return( TRUE );			   	   	   
			   	   	   	}
			   	   	    //if  image not uploaded yet -> we take the one in db 
		    		  	$sql =  " ";
					$sql =  " SELECT identity_img FROM admin_gmahexpress.ko_message WHERE id_customer = '".$id_customer."'  LIMIT 1" ;
						
					 $img_exists  = Db::getInstance()->executeS( $sql  );
				//var_dump(  ' REQUEST: ',$sql,$img_exists[0]['identity_img'] );exit;
				
					if ( $img_exists[0]['identity_img']  !== '' ) 
							{	
								 $this->context->smarty->assign( 
								 array( 	'identity_img_name' => str_replace ( $id_customer.'_' , '' , basename( $img_exists[0]['identity_img'] )  ),
								 	 	'identity_img_path' =>  $uploaddir,
								 	 	'identity_img_real_name' => $uploaddir.basename( $img_exists[0]['identity_img'] )  
								  )  );
								 $_SESSION['uploadrealfile'] =  '/var/www/vhosts/konim.biz/ps_gmach/upload/tz/'.
								 								$id_customer.'_'.basename( $img_exists[0]['identity_img'] ) ;	

								 $_SESSION['identity_img'] =$img_exists[0]['identity_img'];	
								 	var_dump (__LINE__,	$_SESSION['identity_img']  )  ;	exit;			
								    	return( TRUE );	  
		    					}
			    		//var_dump(  ' REQUEST: ',$sql,$img_exists[0]['identity_img'] );exit;	
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
//	var_dump( 'ORDER:',	$_SESSION['order'] ); 	exit; // 	var_dump( 'ORDER:',$this->context);
try{
		//	      throw new Exception(' SQL error BankAccount ');
		if ( isset ( $_POST['bank_name'] ) )
			{
	
	/*		$sql =  " ";
			$sql =  " UPDATE admin_gmahexpress.ko_message SET bank_name= ' ".$_POST['bank_name'] ." '  , account_number = ' ". 					$_POST['account_number']." ' , bank_number = ' " . $_POST['bank_number']." ' ,
			 		sucursale = ' ".$_POST['sucursale']." ',bank_address1 =' ". $_POST['bank_address1']." ',bank_postcode =' ". $_POST['bank_postcode']." '  , bank_CityName =' ". $_POST['bank_CityName']." ',
			 		bank_country = ' ".$_POST['bank_country']." ',bank_phone =' ". $_POST['bank_phone']." ', identity_number =' ". $_POST['identity_number']." ', bank_message =' ". $_POST['message']." ', bank_cgv =' ". $_POST['cgv']." ' WHERE id_customer = ' ".$id_customer." ' " ;
		
			//var_dump(  ' REQUEST: ',$sql );
			Db::getInstance()->executeS( $sql  );
	*/
		$_SESSION['bank_name'] =$_POST['bank_name'] ;
		$_SESSION['account_number'] =	$_POST['account_number'];
		$_SESSION['bank_number']	 = $_POST['bank_number'];
		$_SESSION['sucursale'] =$_POST['sucursale']; 
		$_SESSION['bank_address1']=$_POST['bank_address1'];
		$_SESSION['bank_postcode'] =$_POST['bank_postcode'];
		$_SESSION['bank_CityName']=$_POST['bank_CityName'];
		$_SESSION['bank_country']= $_POST['bank_country'];
		$_SESSION['bank_phone']=$_POST['bank_phone'];
		$_SESSION['identity_number']	 =$_POST['identity_number'];
		$_SESSION['message']	 =$_POST['message'];
		$_SESSION['cgv']	 = $_POST['cgv'];
	
		 $this->context->smarty->assign( array( 'bank_name' => $_POST['bank_name'], 'account_number'=>  $_POST['account_number'] ,'bank_number' =>$_POST['bank_number'],
			 'sucursale' =>$_POST['sucursale']  ,  'bank_address1' =>$_POST['bank_address1'] ,   'bank_postcode' =>$_POST['bank_postcode'] ,  'bank_CityName' =>$_POST['bank_CityName'] ,
			  'bank_country' =>$_POST['bank_country'] ,  'bank_phone' =>$_POST['bank_phone'] , 'identity_number' =>$_POST['identity_number'] ,  'message' =>$_POST['message'] , 'cgv' =>$_POST['cgv'] ) );
					 
		
		}
		else
		{
		/*	$sql =  " ";
			$sql =  " SELECT  bank_name,sucursale,bank_address1,account_number,bank_number,bank_postcode , bank_CityName,bank_country,bank_phone,identity_number,message,cgv FROM admin_gmahexpress.ko_message WHERE id_customer = ' ".$id_customer." ' AND id_order=' ".$id_order." ' " ;
		
//var_dump(  ' REQUEST: ',$sql );exit;
		   $bank_datas = Db::getInstance()->executeS( $sql  );
		   */
		   
		if (  $_SESSION['bank_name']  ) 
			{
			//	var_dump(  ' REQUEST: ',$bank_datas );exit;
				 $this->context->smarty->assign( array( 'bank_name' => $_SESSION['bank_name'], 
				 	 'account_number'=> $_SESSION['account_number'] ,'bank_number' =>$_SESSION['bank_number'],
					 'sucursale' =>$_SESSION['sucursale']  ,  'bank_address1' =>$_SESSION['bank_address1'] ,  
					  'bank_postcode' =>$_SESSION['bank_postcode'] ,  'bank_CityName' =>$_SESSION['bank_CityName'] ,
					  'bank_country' =>$_SESSION['bank_country'] ,  'bank_phone' =>$_SESSION['bank_phone'] ,
					  'identity_number' =>$_SESSION['identity_number'] ,  'message' =>$_SESSION['message'] , 'cgv' =>$_SESSION['cgv'] ) );
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
       $sql->from('message');
       // Build WHERE clauses  
       $sql->where(' id_customer = '.  $id_customer  );
       $result = FALSE; 
    	 $result = Db::getInstance()->executeS( $sql );
       //  var_dump(  $result ); exit(' from   _testTz');
        if( !$_SESSION['uploadrealfile'] ) //   if( $result !== 'NULL' )  
	  		{   
     			// var_dump($_SESSION['identity_img']); exit;
     			return (  FALSE ) ; }
     	else
     		{   	if ( filesize ( $_SESSION['uploadrealfile'] ) === FALSE  ||  filesize ( $_SESSION['uploadrealfile'] ) > 4000000   ) 
     				{	echo'<script type=text/Javascript> alert(" תמונה לא חוקית גודל צריך ליהיות בן500K ל4MB");</script>';
     					return( false);
     					//var_dump($_SESSION['identity_img'], filesize ( $_SESSION['identity_img'] ) );exit ('No image ');
     				}
     			//var_dump($_SESSION['uploadrealfile'] , filesize ( $_SESSION['uploadrealfile'] ) );exit (' image ');
     			return ( $_SESSION['identity_img']  ) ; }	
        return (  FALSE ) ; 
    }
     /* ********************************validate() ****************************************** */
    //  conditions to validate:
    // 1. got identity card image 
    // 2.it is that of the loaner
    // 3.there is not another loan active 
    // 4.passed one month from the last loan
    // 5.loan quantity type positive
    // 6. client not disbarred  -> probleme with him = deleted 
    public function validate()
    {
    	
    	return true;
    }	
 /* ******************************* send_mail() ********************************************* */   
  public function send_mail( $to="", $tpl_name="", $options="", $file_attachment=NULL )
 	{
 				
                                  /*  $customer = new Customer((int)($order->id_customer));
                                    $params['{lastname}'] = $customer->lastname;
                                    $params['{firstname}'] = $customer->firstname;
                                    $params['{id_order}'] = $order->id;
                                    $params['{order_name}'] = $order->getUniqReference();
                                    $params['{voucher_amount}'] = Tools::displayPrice($cart_rule->reduction_amount, $currency, false);
                                    $params['{voucher_num}'] = $cart_rule->code;
                                    */
                                 /*   if($file_attachment){
                      			  var_dump(__LINE__,$to,$tpl_name,$options,' FILE ATTACHEMENT:',$file_attachment);
                      			exit (' Mail Send ');
	                      			}*/
	                      	if( $file_attachment ){
	                      	    $file_attachment_coded  = Tools::fileAttachment( $file_attachment );	
	                      //	var_dump($file_attachment, $file_attachment_coded)  ; exit(__LINE__);
	                      	}
                               @Mail::Send(1, $tpl_name, $options['subject'], $options, $to, $options['{firstname}'].' '.$options['{lastname}'], null, null, $file_attachment_coded , null, _PS_MAIL_DIR_, true, (int)$order->id_shop);
                                   
        }
 /* ****************************************************************************************************** */
 public function send_mail_test( $to,$tpl_name,$options )
 	{
 	 	 	global $cookie;
	           
	             $passage_ligne = '\r\n';  
	          	$id_customer = (int)$this->context->customer->id ;
	          	$customer_name = $this->context->customer->firstname.  ' '.$this->context->customer->lastname  ;
	          	$Gmach_mail="gmach@ygpc.net";
	          
	          	$id_lang=  intval($cookie->id_lang);
	          	$template= $tpl_name;
	          	$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$to;
	          	$to_name = $customer_name;
	         	$from='gmach@ygpc.net';
	          	$from_name =  'gmachexpress';
	          	$file_attachment = NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;
			//=====Déclaration des messages au format texte et au format HTML.
	             $txt =' בקשתכם בטיפול    לאישור . שרות לקוחות של הגמ"ח';
			$message_html = '<p align="center"> '.$customer_name.'</p><div style="text-align:right; padding-right:10px;"></div>';
			//==========
			 
			//=====Création de la boundary
			$boundary = "-----=".md5(rand());
			//==========
			 
			//=====Définition du sujet.
			$preferences = ['input-charset' => 'UTF-8', 'output-charset' => 'UTF-8'];
			$encoded_subject = iconv_mime_encode('Subject', $subject, $preferences);
			$encoded_subject = substr($encoded_subject, strlen('Subject: '));
			//=========
			 
			//=====Création du header de l'e-mail.
			$headers = 'From: '.$from.    $passage_ligne;    //  .' Bcc:< info@ygpc.net>';
			//$headers.= ' Reply-to: Gmach_Express<'.$from.'> ';
			$headers.= ' MIME-Version: 1.0 '.   $passage_ligne;
			$headers .= "Content-type:  text/html;   charset=UTF-8";
			//$headers.= ' Content-Type: multipart'.' boundary='.$boundary;
                	$headers .=' Content-Transfer-Encoding: quoted-printable';
              	//$headers .=' Content-transfer-encoding:8bit ';
                 	//$headers .=' Content-Type: image/jpg ';
		 
			//=====Création du message. 
			$message = "--";//.$boundary;
			//=====Ajout du message au format texte.
			//$message.= ' Content-Type: text/plain; charset=UTF-8 ';
			$message.=$txt;
			//==========
		//	$message.= "--".$boundary;
			//=====Ajout du message au format HTML
		//	$message.= ' Content-Type: text/html; charset=UTF-8 ';
			$message.= $message_html;
			//==========
		//	$message.= "--".$boundary."--";
		//	$message.= "--".$boundary."--";
			//==========
			 
			//=====Envoi de l'e-mail.
	             $result=mail( $to, $encoded_subject,$message,$headers );     // ADDED YGPC
			//==========
 	
			// Inform the manager
	 		$id_lang=  intval($cookie->id_lang);
	          	$template= 'validate_bank_datas';
	          	//$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$Gmach_mail;
	          	$to_name = 'שרות לקוחות';
	         	$from="gmach@ygpc.net";
	          	$from_name =  'gmachexpress';
	          	$file_attachment =  NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;   
	             $txt ='בקשה התקבלה  לאשר אותה    : עבור שרות לקוחות של הגמ"ח ';
	           
			//=====Création du message. 
			$message = "--";//.$boundary;
			//=====Ajout du message au format texte.
			//$message.= ' Content-Type: text/plain; charset=UTF-8 ';
			$message.= $txt;
			//==========
		//	$message.= "--".$boundary;
			//=====Ajout du message au format HTML
			$message_html='<p align="center" שם הלווה >'.$customer_name.'   תהודת  זהות '.$_SESSION["identity_number"].'<img src="'.$options['datas']["tz"].'"  alt="gmachexpress" width="100"height="100"  /></p>
	           	 <div style="text-align:right; padding-right:10px;"> בקשה התקבלה נא לאשר אותה  <br/>  : עבור שרות לקוחות של הגמ"ח </div>';
			$message.= ' Content-Type: text/html; charset=UTF-8 ';
			$message.= $message_html;
			//==========
			//$message.= "--".$boundary."--";
			//$message.= 'Content-Type: image/jpg ';

			//$message.= "--".$boundary."--";
			//==========
		//	$message.= "--".$boundary."--";
		//	$message.= "--".$boundary."--";
			//========== 
			//=====Envoi de l'e-mail.
	             $result=mail( $to, $encoded_subject,$message,$headers );     // ADDED YGPC
			//==========
	
 	}
    /* ******************************************************************************************** */
/* http://emilienmalbranche.fr/prestashop-ecommerce-tutoriels/tutoriels/envoyer-des-mails-grace-a-la-fonction-mailsend-de-prestashop/
Dans le cas d’une boutique en ligne l’envoi d’ emails est très important pour communiquer avec vos clients.
Je vais vous présenter une fonction de prestashop très simple et utile pour envoyer des mails avec en complément un template que vous pourrez mettre en forme facilement !

La classe Mail et sa fonction Send()
Commençons par le code, je vous expliquerai ensuite le fonctionnement de ce dernier.

global $cookie;
 
$subject = 'Bonjour';
$donnees = array('{nom}'  => 'Jobs' ,  '{prenom}'  => 'Steve' );
$destinataire = 'mail@destinataire.com';
 
Mail::Send(intval($cookie->id_lang), 'montemplate', $sujet , $donnees, $destinataire, NULL, NULL, NULL, NULL, NULL, 'mails/');
Dans un premier temps, nous initialisons diverses variables qui contiennent le sujet du mail, les données que ce dernier comprendra (oui oui, on peut mettre des variables dans les mails :) ) ainsi que l’adresse mail du destinataire.

Ces variables seront utilisées dans la fonction ‘Send()’ de la classe ‘Mail’. J’utilise des variables pour mettre toutes les informations d’envoi du mail, cela permet une meilleure clarté.

Voyons maintenant la fonction en détails, elle comprends beaucoup de paramètres :

L’id de la langue [ ici intval($cookie->id_lang), variable cookie qui récupère l'id de la langue actuelle ]
Le nom du template [ ici 'montemplate' ]
Le sujet [ ici 'Bonjour' ]
Un tableau contenant les données à placer dans le template [ ici $donnees ]
Le destinataire [ ici 'mail@destinataire.com' ]
Le nom du destinataire [ ici NULL ]
L’adresse mail de l’émetteur [ ici NULL ]
Le nom de l’émetteur [ ici NULL ]
Une pièce jointe [ ici NULL ]
Le mode SMTP [ ici NULL ]
Le chemin vers le dossier contenant le template [ ici le dossier mails à la racine ]
Le template
Rendez-vous dans le dossier mails/fr , c’est là que notre fonction va aller chercher le fichier template que nous lui avons indiqué. (rappelez-vous : ‘mails/’)

Vous devez impérativement créer deux fichiers, un .txt et un .html portant tout les deux le nom de votre template ( ici  ‘montemplate’ ).
Créez donc les fichiers montemplate.txt et montemplate.html dans le dossier mails/fr/.

Le fichier html est celui utilisé pour le template du mail, avec donc du code html.
Le fichier txt est utilisé au cas où le destinataire n’arrive pas à lire le mail, il contient alors du texte brut.

Ajoutons maintenant du contenu dans notre fichier montemplate.html :

<h1>Bonjour {prenom} {nom}</h1>

Puis pour notre fichier montemplate.txt :

Bonjour {prenom} {nom}

  public static function Send(  $id_lang, $template, $subject, $template_vars, $to,
    							    $to_name = null, $from = null, $from_name = null, $file_attachment = null, $mode_smtp = null,
   							    $template_path = _PS_MAIL_DIR_, $die = false, $id_shop = null, $bcc = null, $reply_to = null)
*/    
// ADDED YGPC
public  function send_mail_swift($to,$tpl_name,$options) 
	 {
	 		global $cookie;
	          	$id_customer = (int)$this->context->customer->id ;
	          	$customer_name = $this->context->customer->firstname.  ' '.$this->context->customer->lastname  ;
	          	$Gmach_mail="gmach@ygpc.net";
	          
	          	$id_lang=  intval($cookie->id_lang);
	          	$template= $tpl_name;
	          	$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$to;
	          	$to_name = $customer_name;
	         	$from="gmach@ygpc.net";
	          	$from_name =  'gmachexpress';
	          	$file_attachment = NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;
	          	
	          	$result = 	Mail::Send(	$id_lang, $template, $subject, $template_vars, $to,
										$to_name , $from , $from_name , $file_attachment , $mode_smtp ,
										$template_path , $ddie, $id_shop , $bcc , $reply_to  );
	         	//	var_dump($result ,$to,$tpl_name,$options); exit;	
	 		// Inform the Director 
	 		$id_lang=  intval($cookie->id_lang);
	          	$template= 'validate_bank_datas';
	          	$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$Gmach_mail;
	          	$to_name = 'שרות לקוחות';
	         	$from="gmach@ygpc.net";
	          	$from_name =  'gmachexpress';
	          	$file_attachment =  NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;
        		
	 		$result = 	Mail::Send(	$id_lang, $template, $subject, $template_vars, $to,
										$to_name , $from , $from_name , $file_attachment , $mode_smtp ,
										$template_path , $ddie, $id_shop , $bcc , $reply_to  );
	        //	var_dump($result ,$to,$tpl_name,$options); exit;	
	              return $result;	
	         //		var_dump($result ,$to,$tpl_name,$options); exit;	
	        //  	var_dump(intval($cookie->id_lang),	$result ,$to,$tpl_name,$options); exit;		 
		
	 }   
  /* **************************************************************************************************** */  
}
