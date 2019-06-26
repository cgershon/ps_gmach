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
define('_SERVICE' , 20 );
class OrderControllerCore extends ParentOrderController
{
    public $step;
    const STEP_SUMMARY_EMPTY_CART = -1;
    const STEP_ADDRESSES = 1;
    const STEP_DELIVERY = 2;
    const STEP_PAYMENT = 3;
    public $carence = 30;// ADDED YGPC 
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
/* *************************************************************************** */    

    public function download_debt()
    {
        // download debt agreement
        $path = "docs/"; // change the path to fit your websites document structure
	 
		$dl_file = 'debt_agree.docx'; // simple file name validation
		//$dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
		$fullPath = $path.$dl_file;
		$good = $fd = fopen ($fullPath, "r") ; //var_dump('good= ', $good , $fullPath ); //exit;
		if ( $good ) 
		{
			//	 var_dump('fullPath', $fullPath  ); //exit;
		    $fsize = filesize($fullPath);
		    $path_parts = pathinfo($fullPath);
		    $ext = strtolower($path_parts["extension"]);
		  //  var_dump('ext', $ext  ); exit;
		    switch ( $ext ) 
		    {
		        case "pdf":
		        header("Content-type: application/pdf");
		        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); 
		        	// use 'attachment' to force a file download
		        break;
		        case "doc":
		        header("Content-type: application/doc");
		        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); 
		        	// use 'attachment' to force a file download
		        break;
		        // add more headers for other content types here
		        default;
		        header("Content-type: application/octet-stream");
		        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
		        break;
		    }
		    header("Content-length: $fsize");
		    header("Cache-control: private"); //use this to open files directly
		    while(!feof($fd)) 
		    {
		        $buffer = fread($fd, 2048);
		        echo $buffer;
		    }
		    
		   
		}// end good 
	fclose ($fd);
	
    }
  	
/* *************************************************************************** */	
	
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
	  $this->context->smarty->assign('dec_part',$_SESSION['decimale_part_excl'] ); // ADDED YGPC

		$orderTotal = $this->context->cart->getOrderTotal();
		$paypal = $orderTotal * 3.4/100 + $_SESSION['billing_cycles'] * 1.2;
		$total_price_adjusted = (int)( $orderTotal *0.966 -  $_SESSION['billing_cycles'] * 1.2 -_SERVICE - $_SESSION['decimale_part_excl'] );
		$_SESSION['loan'] = $orderTotal*$_SESSION['billing_cycles'];
        $this->context->smarty->assign(array(
            'account_informations' => 'פרטי חשבון בנק להפקדת ההלוואה' ,   // YGPC
            'total_price_adjusted' => $total_price_adjusted,
            'paypal' =>     $paypal ,
            'management_fees'=> _SERVICE 
        ));



        // Check for alternative payment api
        $is_advanced_payment_api = (bool)Configuration::get('PS_ADVANCED_PAYMENT_API');
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
                unset( $_POST['down_debt'] ) ; 
            break;

            case OrderController::STEP_DELIVERY:
                if (Tools::isSubmit('processAddress')) {
                    $this->processAddress();
                }
                
    // test if download debt doc  request 
    	  $down_debt = $_POST['down_debt'];   
        if ( $down_debt !== NULL   ) {
      	//var_dump('down_debt', $down_debt );
        //   $this-> download_debt(); 
          // exit( "Compteur" ) ;
	     include( "././compteur/index.php" ); // count of downloads      
        }
                 /* save identity IMG  Added YGPC */
       	 
	          	$img_exists  =	$this-> _saveIdentityImg() ; 
	          	$img_debt_exists  =	$this-> _saveDebtImg() ;
	          	$img_address_proof_exists  =	$this-> _saveAddressProofImg() ;
                //	$img_address_proof_exists  =	'1';//$this->_saveAddressProofImg() ;
	         
	           if(   !$img_exists || !$img_debt_exists  || !$img_address_proof_exists )  // upload or change image
	          	{    //exit ('img') ;
	          		Tools::redirect('index.php?controller=order&step=2');
	           	}
             //   $this->autoStep();
         	$this-> _saveBankAccount() ; 
             $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
            break;

            case OrderController::STEP_PAYMENT:
                // Check that the conditions (so active) were accepted by the customer
                	$cgv = Tools::getValue('cgv') || $this->context->cookie->check_cgv;
                
                
                	$img_exists  =	$this-> _saveIdentityImg() ; 
                	$img_debt_exists  =	$this-> _saveDebtImg() ; 
                	$img_address_proof_exists  =	$this->_saveAddressProofImg() ;
                	//$img_address_proof_exists  =	'1';//$this->_saveAddressProofImg() ;
	           // var_dump($img_exists  ,'   ',$img_debt_exists);exit;
	           if(   !$img_exists   || !$img_debt_exists ||  !$img_address_proof_exists)  // upload or change image
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
                //var_dump(   $available_debt  );exit;
	        if ( $available_tz )
		    {	
		    		global $cookie;
		     	// if the manager give his agrement  according to the identity card and if there is no current loan active, process to payment .	
				if( $this->_validate( $available_tz ) )   
					{
						
						    $available_debt = FALSE;
            				    $available_debt = $this->_testDebt();	// test if there is an image of debt
            				  if ( !$available_debt )
							{
			              		 	 echo ' <script type=text/javascript>alert("נא להכניס תמונה של שטר הלוואה בבקשה !")</script></Div>';
			              		 	 $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl'); // loop to the bank form
			              	 	 	return;		             	 	 
							}
						    $available_address_proof = FALSE;
            				    $available_address_proof = $this->_testAddress_proof();	// test if there is an image of address proof
            				  if ( !$available_address_proof )
							{
			              		 	 echo ' <script type=text/javascript>alert("בבקשה נא להכניס תמונה של הוכחת מקום מגורים !")</script></Div>';
			              		 	 $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl'); // loop to the bank form
			              	 	 	return;		             	 	 
							}
		               	          $this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
		               	   
		       		 }
		             else
		               	   $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl'); // loop to the bank form
		     }	 
             else
			{
		              		  echo ' <script type=text/javascript>alert("נא להכניס תמונה של תעודת זהות בבקשה !")</script></Div>';
		             	 	  $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl'); // loop to the bank form
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
            'total_price' => $orderTotal,
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
    
    /* *************************************************************************************** */
 /*   public function WordtoHtml( $wordfile )
    {
    	
    	i	include("const.php");
		$p2fServ = new COM("Print2Flash4.Server2");
		$p2fServ->DefaultProfile->DocumentType=HTML5;
		$p2fServ->ConvertFile($wordfile,$htmlFile);
		    
    		return( $htmlFile );
    }*/
     /*///////////////////// _saveIdentityImg ////////////////////////////////*/

   protected function  _saveIdentityImg() 
    {
		        if (!isset($this->context->customer->id)) {
		            die(Tools::displayError('Fatal error: No customer'));
		        }
		       if(   $_POST['change'])
		       	{
		       				unset( $_SESSION['identity_img'] );
							unset( $_SESSION['uploadrealfile'] );	
		       		 return FALSE ;
		      	}
		        
			$uploaddir = '../../upload/tz/';// image DIR 
			$id_customer = (int)$this->context->customer->id ;	
			if( file_exists ( $_FILES['identity_img']['tmp_name'] ) ) // image was uploaded
					{	 
						 $uploadfile =  $uploaddir .$id_customer; // image file name with id_customer in it             ../../upload/tz/1
						 
						$uploadfile .= '_'.basename(  $_FILES['identity_img']['name'] ) ; // name without path         ../../upload/tz/1_img_name.jpg
						$uploadrealfile = _PS_ROOT_DIR_."/upload/tz/".$id_customer.'_'.basename(  $_FILES['identity_img']['name'] ) ;	
						
						$this->context->smarty->assign( array( 'identity_img_name' => $_FILES['identity_img']['name']  ) );
						$this->context->smarty->assign( array(  'identity_img_path' =>  $uploaddir   ) ) ;
						$this->context->smarty->assign( array( 'identity_img_real_name' => $uploadfile ) ) ;
						// smarty  need  $uploadfile relative path it  don't work with full path $uploadrealfile 
						// move_uploaded_file need $uploadrealfile  it don't work with relative path 	$uploadfile
						if (  move_uploaded_file( $_FILES['identity_img']['tmp_name'], $uploadrealfile )  ) // copy the tmp file to the real path 
						  {
						
							$_SESSION['identity_img'] =$uploadfile;
							$_SESSION['uploadrealfile'] =$uploadrealfile;	
							}
					 	$_SESSION['img_tmp_name'] =   'identity_img';
					 	unset( $_FILES['identity_img']['tmp_name']  );
					 	unset($_FILES['identity_img']['name'] );
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
						unset( $_FILES['identity_img']['tmp_name']  );
					 	unset($_FILES['identity_img']['name'] );
						return( TRUE );			   	   	   
			   	   	   	}
			   	   	    //if  image not uploaded yet -> we take the one in db 
		    		  	$sql =  " ";
					$sql =  " SELECT identity_img FROM admin_gmahexpress.ko_message WHERE id_customer = '".$id_customer."'  LIMIT 1" ;
						
					 $img_exists  = Db::getInstance()->executeS( $sql  );
				
					if ( $img_exists[0]['identity_img']  !== '' ) 
							{	
								 $this->context->smarty->assign( 
								 array( 	'identity_img_name' => str_replace ( $id_customer.'_' , '' , basename( $img_exists[0]['identity_img'] )  ),
								 	 	'identity_img_path' =>  $uploaddir,
								 	 	'identity_img_real_name' => $uploaddir.basename( $img_exists[0]['identity_img'] )  
								  )  );
								 $_SESSION['uploadrealfile'] = _PS_ROOT_DIR_."/upload/tz/".
								 								$id_customer.'_'.basename( $img_exists[0]['identity_img'] ) ;	

								 $_SESSION['identity_img'] =$img_exists[0]['identity_img'];	
								 //	var_dump (__LINE__,	$_SESSION['identity_img']  )  ;	exit;
								 unset( $_FILES['identity_img']['tmp_name']  );
					 			 unset($_FILES['identity_img']['name'] );			
								 return( TRUE );	  
		    					}
			    		//var_dump(  ' REQUEST: ',$sql,$img_exists[0]['identity_img'] );exit;	
			    			unset( $_FILES['identity_img']['tmp_name']  );
					 	unset($_FILES['identity_img']['name'] );
			    		  	return( FALSE );// no image 	
			  	   }
			     	   	
  }
   /*/////////////////////_saveDebtImg ////////////////////////////////*/

   protected function  _saveDebtImg() 
    {
		        if (!isset($this->context->customer->id)) {
		            die(Tools::displayError('Fatal error: No customer'));
		        }
		       if(   $_POST['change_debt'] )
		       	{
		       		unset( $_SESSION['debt_img'] );
					unset( $_SESSION['uploadrealfile_debt'] ) ;
		       		 return FALSE ;
		      	}
		        
			$uploaddir = '../../upload/debt/';//  debt image DIR 
			$id_customer = (int)$this->context->customer->id ;	
			if( file_exists ( $_FILES['debt_img']['tmp_name'] ) ) // image was uploaded
					{	
						 $uploadfile =  $uploaddir .$id_customer; // image file name with id_customer in it             ../../upload/tz/1
						 
						$uploadfile .= '_'.basename(  $_FILES['debt_img']['name'] ) ; // name without path         ../../upload/debt/1_img_name.jpg
						$uploadrealfile = _PS_ROOT_DIR_."/upload/debt/".$id_customer.'_'.basename(  $_FILES['debt_img']['name'] ) ;	
						
						$this->context->smarty->assign( array( 'debt_img_name' => $_FILES['debt_img']['name']  ) );
						$this->context->smarty->assign( array(  'debt_img_path' =>  $uploaddir   ) ) ;
						$this->context->smarty->assign( array( 'debt_img_real_name' => $uploadfile ) ) ;
						// smarty  need  $uploadfile relative path it  don't work with full path $uploadrealfile 
						// move_uploaded_file need $uploadrealfile  it don't work with relative path 	$uploadfile
						if (  move_uploaded_file( $_FILES['debt_img']['tmp_name'], $uploadrealfile )  ) // copy the tmp file to the real path 
						  {
						
							$_SESSION['debt_img'] =$uploadfile;
							$_SESSION['uploadrealfile_debt'] =$uploadrealfile;	
						  }
					 		$_SESSION['img_debt_tmp_name'] =   'debt_img';
					 	unset( $_FILES['debt_img']['tmp_name']  );
					 	unset($_FILES['debt_img']['name'] );
					  	
					 	//$this->WordtoHtml( $uploadfile );
					 	return( TRUE );
			    	  }
			      else    //if  image not uploaded -> we take the one in cache 
			   	   {
			   	   	 
			   	   	   if( $_SESSION['uploadrealfile_debt'] !== ''  )
			   	   	   	{	
			   	   	   		   
			   	   	   	 $debt_img = $_SESSION['debt_img'] ;
			   	   	   	 $this->context->smarty->assign( 
									 array( 	'debt_img_name' => str_replace ( $id_customer.'_' , '' , basename( $debt_img )  ),
									 	 	'debt_img_path' =>  $uploaddir,
									 	 	'debt_img_real_name' => $debt_img
									  )  );
						unset( $_FILES['debt_img']['tmp_name']  );
					 	unset($_FILES['debt_img']['name'] );
						return( TRUE );			   	   	   
			   	   	   	}
			   	   	    //if  image not uploaded yet -> we take the one in db 
		    		  	$sql =  " ";
					$sql =  " SELECT debt_img FROM admin_gmahexpress.ko_message WHERE id_customer = '".$id_customer."'  LIMIT 1" ;
						
					 $img_exists  = Db::getInstance()->executeS( $sql  );
				
					if ( $img_exists[0]['debt_img']  !== '' ) 
							{	
								 $this->context->smarty->assign( 
								 array( 	'debt_img_name' => str_replace ( $id_customer.'_' , '' , basename( $img_exists[0]['debt_img'] )  ),
								 	 	'debt_img_path' =>  $uploaddir,
								 	 	'debt_img_real_name' => $uploaddir.basename( $img_exists[0]['debt_img'] )  
								  )  );
								 $_SESSION['uploadrealfile_debt'] = _PS_ROOT_DIR_."/upload/debt/".
								 $id_customer.'_'.basename( $img_exists[0]['debt_img'] ) ;	

								 $_SESSION['debt_img'] =$img_exists[0]['debt_img'];	
								 //	var_dump (__LINE__,	$_SESSION['identity_img']  )  ;	exit;
								 	unset( $_FILES['debt_img']['tmp_name']  );
					 				unset($_FILES['debt_img']['name'] );			
								    	return( TRUE );	  
		    					}
			    		//var_dump(  ' REQUEST: ',$sql,$img_exists[0]['identity_img'] );exit;	
			    			unset( $_FILES['debt_img']['tmp_name']  );
					 	unset($_FILES['debt_img']['name'] );
			    		  	return( FALSE );// no image 	
			  	   }
			     	   	
  }
   /*/////////////////////_saveAddressProofImg////////////////////////////////*/

   protected function  _saveAddressProofImg() 
    {
		        if (!isset($this->context->customer->id)) {
		            die(Tools::displayError('Fatal error: No customer'));
		        }
		       if(   $_POST['change_address_proof'] )
		       	{
		       		unset( $_SESSION['address_proof_img'] );
					unset( $_SESSION['uploadrealfile_address_proof'] ) ;
		       		 return FALSE ;
		      	}
		        
			$uploaddir = '../../upload/address_proof/';//  address image DIR 
			$id_customer = (int)$this->context->customer->id ;	
			if( file_exists ( $_FILES['address_proof_img']['tmp_name'] ) ) // image was uploaded
					{	
						 $uploadfile =  $uploaddir .$id_customer; // image file name with id_customer in it             ../../upload/address_proof/1
						 
						$uploadfile .= '_'.basename(  $_FILES['address_proof_img']['name'] ) ; 
							// name without path         ../../upload/address_proof/1_img_name.jpg
						$uploadrealfile = _PS_ROOT_DIR_."/upload/address_proof/".$id_customer.'_'.basename(  $_FILES['address_proof_img']['name'] ) ;	
						
						$this->context->smarty->assign( array( 'address_proof_img_name' => $_FILES['address_proof_img']['name']  ) );
						$this->context->smarty->assign( array(  'address_proof_img_path' =>  $uploaddir   ) ) ;
						$this->context->smarty->assign( array( 'address_proof_img_real_name' => $uploadfile ) ) ;
						// smarty  need  $uploadfile relative path it  don't work with full path $uploadrealfile 
						// move_uploaded_file need $uploadrealfile  it don't work with relative path 	$uploadfile
						//var_dump( $_FILES['address_proof_img']['tmp_name'] ,' -- ',$uploadrealfile); exit;
						if (  move_uploaded_file( $_FILES['address_proof_img']['tmp_name'], $uploadrealfile )  ) // copy the tmp file to the real path 
						  {
						
							$_SESSION['address_proof_img'] =$uploadfile;
							$_SESSION['uploadrealfile_address_proof'] =$uploadrealfile;	
						  }
					 		$_SESSION['img_address_proof_tmp_name'] =   'address_proof_img';
					 	unset( $_FILES['address_proof_img']['tmp_name']  );
					 	unset($_FILES['address_proof_img']['name'] );
					 	return( TRUE );
			    	  }
			      else    //if  image not uploaded -> we take the one in cache 
			   	   {
			   	   	 
			   	   	   if( $_SESSION['uploadrealfile_address_proof'] !== ''  )
			   	   	   	{	
			   	   	   		   //exit( ' saveaddress_proofImg ' );
			   	   	   	 $address_proof_img = $_SESSION['address_proof_img'] ;
			   	   	   	 $this->context->smarty->assign( 
									 array( 	'address_proof_img_name' => str_replace ( $id_customer.'_' , '' , basename( $address_proof_img )  ),
									 	 	'address_proof_img_path' =>  $uploaddir,
									 	 	'address_proof_img_real_name' => $address_proof_img
									  )  );
						unset( $_FILES['address_proof_img']['tmp_name']  );
					 	unset($_FILES['address_proof_img']['name'] );
						return( TRUE );			   	   	   
			   	   	   	}
			   	   	    //if  image not uploaded yet -> we take the one in db 
		    		  	$sql =  " ";
					$sql =  " SELECT address_proof_img FROM admin_gmahexpress.ko_message WHERE id_customer = '".$id_customer."'  LIMIT 1" ;
						
					 $img_exists  = Db::getInstance()->executeS( $sql  );
				
					if ( $img_exists[0]['address_proof_img']  !== '' ) 
							{	
								 $this->context->smarty->assign( 
								 array( 	'address_proof_img_name' => str_replace ( $id_customer.'_' , '' , basename( $img_exists[0]['address_proof_img'] )  ),
								 	 	'address_proof_img_path' =>  $uploaddir,
								 	 	'address_proof_img_real_name' => $uploaddir.basename( $img_exists[0]['address_proof_img'] )  
								  )  );
								 $_SESSION['uploadrealfile_address_proof'] = _PS_ROOT_DIR_."/upload/address_proof/".
								 $id_customer.'_'.basename( $img_exists[0]['address_proof_img'] ) ;	

								 $_SESSION['address_proof_img'] =$img_exists[0]['identity_img'];	
								 //	var_dump (__LINE__,	$_SESSION['address_proof_img']  )  ;	exit;
								 	unset( $_FILES['address_proof_img']['tmp_name']  );
					 				unset($_FILES['address_proof_img']['name'] );			
								    	return( TRUE );	  
		    					}
			    		//var_dump(  ' REQUEST: ',$sql,$img_exists[0]['identity_img'] );exit;	
			    			unset( $_FILES['address_proof_img']['tmp_name']  );
					 	unset($_FILES['address_proof_img']['name'] );
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
		if ( isset ( $_POST['bank_name'] ) )
			{

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
			
				$this->context->smarty->assign( array( 'bank_name' => $_POST['bank_name'], 'account_number'=>  $_POST['account_number'] ,'bank_number' =>$_POST['bank_number'], 'sucursale' =>$_POST['sucursale']  ,  'bank_address1' =>$_POST['bank_address1'] ,   'bank_postcode' =>$_POST['bank_postcode'] ,  'bank_CityName' =>$_POST['bank_CityName'] ,  'bank_country' =>$_POST['bank_country'] ,  'bank_phone' =>$_POST['bank_phone'] , 'identity_number' =>$_POST['identity_number'] ,  'message' =>$_POST['message'] , 'cgv' =>$_POST['cgv']  ) );
				
		}
		else
		{
			   
		if (  $_SESSION['bank_name']  ) 
			{
				 $this->context->smarty->assign( array( 'bank_name' => $_SESSION['bank_name'], 
				 	 'account_number'=> $_SESSION['account_number'] ,'bank_number' =>$_SESSION['bank_number'],
					 'sucursale' =>$_SESSION['sucursale']  ,  'bank_address1' =>$_SESSION['bank_address1'] ,  
					  'bank_postcode' =>$_SESSION['bank_postcode'] ,  'bank_CityName' =>$_SESSION['bank_CityName'] ,
					  'bank_country' =>$_SESSION['bank_country'] ,  'bank_phone' =>$_SESSION['bank_phone'] ,
					  'identity_number' =>$_SESSION['identity_number'] ,  'message' =>$_SESSION['message'] , 
					  'cgv' =>$_SESSION['cgv']  ) );
			}
		}	
			
	} // end of try 
catch( Exception $ex )
		       {	
		       	 exit('END with error saveBankAccount ');
		       }       
   }
    
    /* ********************************_testTz() ****************************************** */
    
    public function _testTz()
    {
    	/*
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
    	 */
        if( !$_SESSION['uploadrealfile'] ) //   if( $result !== 'NULL' )  
	  		{   
     			return (  FALSE ) ;
     		 }
     	else
     		{   	if ( filesize ( $_SESSION['uploadrealfile'] ) === FALSE  ||  filesize ( $_SESSION['uploadrealfile'] ) > 4000000   ) 
     				{	echo'<script type=text/Javascript> alert(" תמו נה לא חוקית גודל צריך ליהיות בן 500K ל4MB ");</script>';
     					return( false);
     				}
     			return ( $_SESSION['identity_img']  ) ;
     		 }	
        return (  FALSE ) ; 
    }
        /* ********************************_testDebt() ****************************************** */
    
    public function _testDebt()
    {
        if( !$_SESSION['uploadrealfile_debt'] ) //   if( $result !== 'NULL' )  
	  		{   
     			return (  FALSE ) ; 
     		}
     	else
     		{   	if ( filesize ( $_SESSION['uploadrealfile_debt'] ) === FALSE  ||  filesize ( $_SESSION['uploadrealfile_debt'] ) > 4000000   ) 
     				{	echo'<script type=text/Javascript> alert(" תמו נה לא חוקית גודל צריך ליהיות בן 500K ל4MB ");</script>';
     					return( false);
     				}
     			return ( $_SESSION['debt_img']  ) ;
     		 }	
        return (  FALSE ) ; 
    }

    	  /* ********************************_testAddress_proof() ****************************************** */
    
    public function  _testAddress_proof()
    {
        if( !$_SESSION['uploadrealfile_address_proof'] ) //   if( $result !== 'NULL' )  
	  		{   
     			return (  FALSE ) ; 
     		}
     	else
     		{   	if ( filesize ( $_SESSION['uploadrealfile_address_proof'] ) === FALSE  ||  filesize ( $_SESSION['uploadrealfile_address_proof'] ) > 4000000   ) 
     				{	echo'<script type=text/Javascript> alert(" תמו נה לא חוקית גודל צריך ליהיות בן 500K ל4MB ");</script>';
     					return( false);
     				}
     			return ( $_SESSION['address_proof_img']  ) ;
     		 }	
        return (  FALSE ) ; 
    }
   
     /* ******************************* _validate() ****************************************** */
    //  conditions to validate:
    // 1. got identity card image 
    // 2.it is that of the loaner
    // 3.there is not another loan active 
    // 4.passed one month from the last loan
    // 5.loan quantity type positive
    // 6. client not disbarred  -> probleme with him = freeze 
   // 7. must provide debt acknowlegde with signature
   //8. must provide address proof 
   
    public function _validate( $available_tz )
    {
    	 $id_customer = (int)$this->context->customer->id ;	
	 $sql = new DbQuery();
    	 $sql =  '';
	 $sql = " SELECT ko_orders.`id_order`,ko_orders.`date_add`,`active`,`billing_cycles` FROM admin_gmahexpress.ko_orders LEFT JOIN admin_gmahexpress.ko_customer USING( `id_customer` ) JOIN admin_gmahexpress.ko_message ON( ko_orders.`id_customer` = ko_message.`id_customer` AND ko_orders.`id_order`= ko_message.`id_order`  ) WHERE ko_orders.`id_customer` = '".$id_customer."' AND `active`='1' ORDER BY `date_add` DESC LIMIT 1 " ;
						
	 $result= Db::getInstance()->executeS( $sql  );
			//	var_dump(  ' REQUEST: ', $sql, $result );exit;
	if( $result)
	 {
		if( ! $result[0]['active'] )
		{
			echo( '<script type=text/javascript> alert( "חשבון מוקפא ! " ) </script></Div>' );
   	 		return false;
			}
	    	$date1=new datetime ( $result[0]['date_add'] ); 
	    	$date2 =new datetime( date('Y-m-d h:m:s') );
	    	$interval = $date1->diff( $date2 );
		
		//var_dump(  $interval->format( '%R%a days' ) ,$result[0]['active']  );
		if(  $result === NULL || $interval->format( '%R%a days' ) >   ( $result[0]['billing_cycles'] +1 )* 30  )
	  		  {
  		  	 
		    	$to =  $this->context->customer->email ;//"somebody@example.com";
				$tpl_name= 'gmach_validation';
				$options['subject'] = "בקשה בטיפול";  
				$tz_path= "http://".$_SERVER['SERVER_NAME']."/upload/tz/";
				$wait_path= "http://".$_SERVER['SERVER_NAME']."/upload/wait/";
				$options['datas'] = array('{nom}'  => $this->context->customer->firstname,  '{prenom}'  => $this->context->customer->lastname ,			
				'{tz_path}'=>$tz_path. basename( $available_tz ) ,  'tz_path'=>$tz_path. basename( $available_tz )  );
				$options['dir_tpl']= _PS_MAIL_DIR_;
				$options['{firstname}'] = $this->context->customer->firstname;
				$options['{lastname}'] = $this->context->customer->lastname;
				$options['{tz}'] = $_SESSION['identity_number'];
				$options['{tz_path}'] = 	$tz_path. basename( $available_tz ) ;
				$options['{wait_path}'] = $wait_path.'wait.png' ;
				$options['{uploadrealfile}'] = $_SESSION['uploadrealfile'];
				$options['{uploadfile}'] = $_SESSION['identity_img'];
				$options['{uploadrealfile_debt}'] = $_SESSION['uploadrealfile_debt'];
				$options['{uploadfile_debt}'] = $_SESSION['identity_img_debt'];
			
		
				$result = $this->send_mail( $to, $tpl_name,$options ,null ) ;  // send a mail to the client  saying that the asked loan  is in process
				 //var_dump( __LINE__ ,$to, $tpl_name,$options , $result );exit;
				// inform the manager 
				$Gmach_mail="gmach@ygpc.net";
				$tpl_name='manager_validation';
                          $to=$Gmach_mail;
                          $filename =basename( $available_tz );
                          $file_attachment['rename'] = uniqid().Tools::strtolower(substr(   $filename , -5 ) );
                          //$file_attachment['content'] = file_get_contents(  $file_tmp_name );
		            
		            $file_attachment['tmp_name'] =  $_SESSION['uploadrealfile'];
		            $file_attachment['tmp_name_debt'] =  $_SESSION['uploadrealfile_debt'];
		            $file_attachment['name']     =   $filename;
		            $file_attachment['name_debt']     =   $filename;
		            $file_attachment['mime']     =  filetype( $_SESSION['uploadrealfile_debt'] );
		            $file_attachment['mime_debt']     =  filetype( $_SESSION['uploadrealfile_debt'] );
		            $file_attachment['error']    =  'tz_error';
		            $file_attachment['size_debt']     = filesize( $_SESSION['uploadrealfile_debt'] );
             	      //   var_dump( __LINE__ , $file_attachment);exit;
				$result = $this->send_mail( $to, $tpl_name,$options ,null ); // $file_attachment  )  ;  // send a mail to the manager asking him to valid the loan .
				//	  var_dump( __LINE__ , $result );exit; 
  			return true;
  		  }
    	else
   	 	{	// waiting end of loan plus 30days 
   	 		$permission = 	( $result[0]['billing_cycles'] +1 ) * $this->carence  - $interval->format( '%R%a days' ) ; 
   	 		$message = '   הלוואה קיימת לא ניתן לקחת עוד אחד !  \n ' ;
   	 		$message .= 'לנסות בעוד '.$permission.' ימים \nתודה .'  ; 
   	 	//	echo( ' '. $message.'\n' );//sleep( 5 );
   	 		echo( '<script type=text/javascript> alert("'. $message.'" ) </script></Div>' );
   	 	
   	 		return false;
   	 	}	
   	 	
   	}	return true;  // didn't get a result => no orders for this client 
    }	
 /* ******************************* send_mail() ********************************************* */   
  public function send_mail( $_to='', $tpl_name='', $options='', $_file_attachment=NULL )
 	{				
                                 
                $id_lang=  1;//intval($cookie->id_lang);
	          	$template= $tpl_name;
	          	$subject=  $options['subject'];
	          	$template_vars= $options;
	          	$to=$_to;
	          	$to_name = $options['{firstname}'].' '.$options['{lastname}'];
	         	$from='gmach@ygpc.net';
	          	$from_name =  'gmachexpress';
	          	$file_attachment =  $_file_attachment;
   				$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = (int)$order->id_shop;
        		$bcc = null;
        		$reply_to = null;
        		
	           	if( $file_attachment )
	                  {
	                      $file_attachment_coded  = Tools::fileAttachment( $file_attachment );	
	                 
	                  }
	           /* var_dump($id_lang, $template, $subject, $template_vars, $to,
							$to_name , $from , $from_name , $file_attachment , $mode_smtp ,
							$template_path , $ddie, $id_shop , $bcc , $reply_to, $file_attachment_coded)  ; exit(__LINE__);
                    */
                    	 @Mail::Send(	$id_lang, $template, $subject, $template_vars, $to,
							$to_name , $from , $from_name , $file_attachment , $mode_smtp ,
							$template_path , $ddie, $id_shop , $bcc , $reply_to  );
                                   
        }

  /* **************************************************************************************************** */  
}
