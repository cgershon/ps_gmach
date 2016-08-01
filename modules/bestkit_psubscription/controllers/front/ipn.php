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

class Bestkit_psubscriptionipnModuleFrontController extends
    ModuleFrontController
{
    public $ssl = true;

	private function duplicateOrder($oldOrder)
	{
		//New Order:
		$newOrder = $oldOrder->duplicateObject();
		$newOrder->date_add = date('Y-m-d H:i:s');
		$newOrder->update();

		//New Carrier:
		$oldCarrier = new OrderCarrier($oldOrder->getIdOrderCarrier());
		$newCarrier = $oldCarrier->duplicateObject();
		$newCarrier->id_order = $newOrder->id;
		$newCarrier->date_add = date('Y-m-d H:i:s');
		$newCarrier->update();

		//Order Details:
		$oldProducts = OrderDetail::getList($oldOrder->id);
		foreach ($oldProducts as $oldProduct) {
			$oldDetail = new OrderDetail($oldProduct['id_order_detail']);
			$newDetail = $oldDetail->duplicateObject();
			$newDetail->id_order = $newOrder->id;
			$newDetail->update();
			$newDetail->updateTaxAmount($newOrder);
		}

		//Set order to Paid:
		$newOrder->setCurrentState(Configuration::get('PS_OS_PAYMENT'));

		//Generate Invoice:
		$newOrder->setInvoice(true);
	}

	public function initContent()
	{
		if ($this->module->getIpnToken() != Tools::getValue('token')) {
			die();
		}

		@file_put_contents(_PS_MODULE_DIR_ . 'bestkit_psubscription/log/' . time() . rand(1, 1000) . '_' . date('r'), print_r($_POST, 1));
		if (Tools::getValue('payment_status') == 'Completed') {
			$reference = Tools::getValue('transaction_subject');
			$orders = Order::getByReference($reference);
			$orders->orderBy('date_add');

			if (count($orders) > 0) {
				$_order = $orders->getFirst();
            	if (count($orders) == 1 && $_order->getCurrentState() != Configuration::get('PS_OS_PAYMENT')) {
                	$_order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                	return;
            	}

            	$this->duplicateOrder($_order);
			}
		}

		die;
	}
}
