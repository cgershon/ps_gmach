<?php
/*
* 2007-2015 PrestaShop modify YGPC 2/02/2017 
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

class HistoryControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'history';
    public $authRedirection = 'history';
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(array(
            _THEME_CSS_DIR_.'history.css',
            _THEME_CSS_DIR_.'addresses.css'
        ));
        $this->addJS(array(
            _THEME_JS_DIR_.'history.js',
            _THEME_JS_DIR_.'tools.js' // retro compat themes 1.5
        ));
        $this->addJqueryPlugin(array('scrollTo', 'footable', 'footable-sort'));
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if ($orders = Order::getCustomerOrders($this->context->customer->id)) {
            foreach ($orders as &$order) {
                $myOrder = new Order((int)$order['id_order']);
                if (Validate::isLoadedObject($myOrder)) {
                    $order['virtual'] = $myOrder->isVirtual(false);
                }
            }
        }
        $this->context->smarty->assign(array(
            'orders' => $orders,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'reorderingAllowed' => !(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
            'slowValidation' => Tools::isSubmit('slowvalidation')
        ));


/* ******************************************** loan payments********************************* */      

  $order_reference = $orders[0]['reference'] ; 

			$sql= 'SELECT order_reference,DATE(`ko_order_payment`.date_add) as date ,CAST( amount AS INT ),payment_method,CAST( total_products AS int ) as loan FROM `ko_order_payment` JOIN `ko_orders`  ON (`order_reference` = `reference` )  WHERE `ko_order_payment`.`order_reference` = "'.$order_reference.'"' ;
	      
	      	$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS( $sql ) ;
	      	$cpt=1;
		      foreach(  $result as $payment )
		      {
		      	 
				$data[$cpt] = array( 'payment'.$cpt=> $payment );
		     
		     	 $cpt++ ;
			}
		
			 $this->context->smarty->assign( 'payment', $data   )     ;  	// $data is an array of array 
//	exit;
        $this->setTemplate(_PS_THEME_DIR_.'history.tpl');
    }
}
