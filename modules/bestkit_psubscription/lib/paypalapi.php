<?php	@session_start();
/*		https://developer.paypal.com/docs/classic/express-checkout/integration-guide/ECRecurringPayments/
 * https://www.paypal.com/il/webapps/mpp/paypal-fees?locale.x=he_IL
 * 2007-2013 PrestaShop
 * modifed YGPC  9/04/16  :  $_SESSION['billing_cycles']      from the input field whose is chosen by the client 
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
 define( '_FORCE_PROFIL ' , TRUE ); // ADDED
 define( '_ITEM_CATEGORY', 'Digital' );// ADDED
if ( !$_SESSION['billing_cycles'] )
      $_SESSION['billing_cycles'] = '4';
/* ******************************************************************************************************** */      
class paypal_recurring
{
	protected function generatePaymentRequest($order, $addBillingFreq = false, $descOnly = false)
	{
		$_cart = new Cart($order->id_cart);
		$products = Db::getInstance()->executeS('
			SELECT * FROM `' . _DB_PREFIX_ . 'cart_product`
			WHERE `id_cart` = ' . $_cart->id . '
		');

		$request = '';
		$i = 0;
		foreach ($products as $product) {
			if ($addBillingFreq || _FORCE_PROFIL ) 
      {
				$data = unserialize($product['bestkit_psubscription']);
				$period = new BestkitPsubscriptionPeriod($data['id_period'], Context::getContext()->language->id);

				$time = strtotime($data['start_date']);
				if ($time < time()) {
					$time = time();
				}

				$date = gmdate("Y-m-d\TH:i:s\Z", $time);
				if (date("Y-m-d", time()) == date("Y-m-d", $time)) {
					$date = date("Y-m-d\TH:i:s\Z", $time+24*60*60-1);
				}

				$request .= '&PROFILESTARTDATE=' . urlencode($date);
				$request .= '&BILLINGPERIOD=' . urlencode($period->billing_period);
				$request .= '&BILLINGFREQUENCY=' . urlencode($period->billing_freq);
			 // ADDED  YGPC 9/04/2016 
      	 	 if( (int)$_SESSION['billing_cycles']  > 0 ) 
      	    		{     
          				$request .= '&TOTALBILLINGCYCLES=' . urlencode( $_SESSION['billing_cycles']  );
          
			         	} 
			 // Important fixing the amount of each cycle        	
			$request .= '&AMT=' . urlencode( $this->paymentAmount ) / $_SESSION['billing_cycles']  ; 	// also added by YGPC not included in original script downloaded !
	
				// End added 
			} // 	if ($addBillingFreq || _FORCE_PROFIL )

	if ($descOnly)
        {
				return $order->reference;
	  }

			$desc = urlencode(Tools::substr(Product::getProductName($product['id_product'], $product['id_product_attribute']), 0, 120));
			$request .= '&L_BILLINGAGREEMENTDESCRIPTION0=' . $order->reference;
			$request .= '&PAYMENTREQUEST_' . $i . '_DESC=' . $order->reference;
			$request .= '&DESC=' . $order->reference;

			$request .= '&L_PAYMENTREQUEST_' . $i . '_NAME0=' . $desc;
			$request .= '&L_PAYMENTREQUEST_' . $i . '_AMT0=' . $this->paymentAmount; // Total amount of the all cycles 
			//$request .= '&L_PAYMENTREQUEST_' . $i . '_QTY0=' . urlencode($product['quantity']);
	     //	$request .= '&L_PAYMENTREQUEST_0_ITEMCATEGORY0=' . _ITEM_CATEGORY; //  ADDED YGPC
		}     // end foreach
//	var_dump(  	$request     ); exit;
		return $request;
	}
/* **************************************************************************************************************** */
	public function setExpressCheckout()
	{
		$nvpStr = 
		"&PAYMENTREQUEST_0_AMT=".$this->paymentAmount
		."&ReturnUrl=".$this->returnURL
		."&CANCELURL=".$this->cancelURL
		."&PAYMENTREQUEST_0_PAYMENTACTION=".$this->paymentType
		."&PAYMENTREQUEST_0_CURRENCYCODE=".$this->currencyID
		.$this->generatePaymentRequest($this->order);

		$httpParsedResponseAr = $this->fn_setExpressCheckout('SetExpressCheckout', $nvpStr);

		if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
			|| "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])
		) {
			$token = urldecode($httpParsedResponseAr["TOKEN"]);
			$payPalURL = "https://www.paypal.com/cgi-bin/webscr&cmd=_express-checkout&token=$token";

			if("sandbox" === $this->environment || "beta-sandbox" === $this->environment) {
				$payPalURL = "https://www.".$this->environment.".paypal.com/cgi-bin/webscr&cmd=_express-checkout&token=$token";
			}

			header("Location: $payPalURL"); // redirect to paypal with parameters in $_POST 
			exit;
		} else  {
			$httpParsedResponseAr['order'] = $this->order;
			return $httpParsedResponseAr;
		}
	}

	public function getExpressCheckout()
	{
		if (!array_key_exists('token', $_REQUEST)) {
			exit('Paypal token is not received.');
		}

		$token = urlencode(htmlspecialchars($_REQUEST['token']));
		$nvpStr = "&TOKEN=$token";
		$httpParsedResponseAr = $this->fn_getExpressCheckout('GetExpressCheckoutDetails', $nvpStr);

		if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
			|| "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])
		) {
			$payerID = $httpParsedResponseAr['PAYERID'];
			return $this->doExpressCheckout($payerID, $token, $httpParsedResponseAr);
		} else  {
			return $httpParsedResponseAr;
		}
	}

	public function doExpressCheckout($payerID, $token, $payerData)
	{
		$nvpStr = 
		"&TOKEN=$token&PAYERID=$payerID&PAYMENTACTION=".$this->paymentType
		."&PAYMENTREQUEST_0_AMT=".$this->paymentAmount
		."&PAYMENTREQUEST_0_CURRENCYCODE=".$this->currencyID
		."&PAYMENTREQUEST_0_NOTIFYURL=".$this->notify_url;

		$httpParsedResponseAr = $this->fn_doExpressCheckout('DoExpressCheckoutPayment', $nvpStr);
		
		if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
			|| "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])
		) {
			return $this->createRecurringPaymentsProfile($token, $payerData);
		} else  {
			return $httpParsedResponseAr;
		}
	}

	public function createRecurringPaymentsProfile($token, $payerData)
	{
		$token = $_REQUEST['token'];

		$nvpStr = 
		"&TOKEN=$token&AMT=".$this->paymentAmount
		."&CURRENCYCODE=".$this->currencyID
		."&DESC=".$this->generatePaymentRequest($this->order, false, true)
		.$this->generatePaymentRequest($this->order, true);

		$httpParsedResponseAr = $this->fn_createRecurringPaymentsProfile('CreateRecurringPaymentsProfile', $nvpStr);
		
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) 
			|| "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])
		) {
			return array_merge($payerData, $httpParsedResponseAr);
		} else  {
			return $httpParsedResponseAr;
		}
	}

	public function fn_createRecurringPaymentsProfile($methodName_, $nvpStr_)
	{
		$version = urlencode('104.0');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// NVPRequest for submitting to server
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD="
		.$this->API_Password."&USER=".$this->API_UserName
		."&SIGNATURE=".$this->API_Signature."$nvpStr_";

		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
		// getting response from server
		$httpResponse = curl_exec($ch);
		
		if(!$httpResponse) {
			exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}
		
		// Extract the RefundTransaction response details
		$httpResponseAr = explode("&", $httpResponse);
		
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
	
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
		
		return $httpParsedResponseAr;
	}
	
	public function fn_setExpressCheckout($methodName_, $nvpStr_)
	{
		$version = urlencode('104.0');
		
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=".$this->API_Password
		."&USER=".$this->API_UserName
		."&SIGNATURE="
		.$this->API_Signature."$nvpStr_&L_BILLINGTYPE0=RecurringPayments";

		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		$httpResponse = curl_exec($ch);
		
		if(!$httpResponse) {
			exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}
		
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
		
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
		
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
	
		return $httpParsedResponseAr;
	}
	
	public function fn_getExpressCheckout($methodName_, $nvpStr_)
	{
		$version = urlencode('104.0');
		
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=".$this->API_Password
		."&USER=".$this->API_UserName
		."&SIGNATURE=".$this->API_Signature
		."$nvpStr_&L_BILLINGTYPE0=RecurringPayments&L_BILLINGAGREEMENTDESCRIPTION0="
		.$this->generatePaymentRequest($this->order, false, true);
		
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
		// Get response from the server.
		$httpResponse = curl_exec($ch);
		
		if(!$httpResponse) {
			exit('$methodName_ failed: '.curl_error($ch).'('.curl_errno($ch).')');
		}
		
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
		
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
		
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
	
		return $httpParsedResponseAr;
	}
	
	public function fn_doExpressCheckout($methodName_, $nvpStr_)
	{
		$version = urlencode('104.0');
		
		// setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		// Set the curl parameters.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=".$this->API_Password
		."&USER=".$this->API_UserName
		."&SIGNATURE=".$this->API_Signature
		."$nvpStr_&L_BILLINGTYPE0=RecurringPayments&L_BILLINGAGREEMENTDESCRIPTION0="
		.$this->generatePaymentRequest($this->order, false, true);
		
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
		// Get response from the server.
		$httpResponse = curl_exec($ch);
		
		if (!$httpResponse) {
			exit('$methodName_ failed: '.curl_error($ch).'('.curl_errno($ch).')');
		}
		
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
		
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if (sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
		
		if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
		
		return $httpParsedResponseAr;
	}
}
