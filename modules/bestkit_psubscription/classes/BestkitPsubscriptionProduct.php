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

class BestkitPsubscriptionProduct extends ObjectModel
{
    public $id;
    public $id_bestkit_psubscription_product;
    public $id_product;
    public $id_shop;
    public $is_enabled = 0;
    public $period_ids;
    public $subscription_only;

    public static $definition = array(
        'table' => 'bestkit_psubscription_product',
        'primary' => 'id_bestkit_psubscription_product',
        'multilang' => FALSE,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'required' => TRUE),
            'id_shop' => array('type' => self::TYPE_INT, 'required' => TRUE),
            'is_enabled' => array('type' => self::TYPE_BOOL, 'required' => TRUE),
            'subscription_only' => array('type' => self::TYPE_BOOL, 'required' => TRUE),
            'period_ids' => array('type' => self::TYPE_STRING, 'required' => TRUE),
            'price' => array('type' => self::TYPE_FLOAT, 'required' => TRUE),
            ),
        );

	public static function getProductData($id_product, $id_shop = 0)
	{
		$id = (int)Db::getInstance()->getValue('
			SELECT `' . self::$definition['primary'] . '`
			FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
			WHERE `id_product` = ' . (int)$id_product . '
			AND `id_shop` = ' . (int)$id_shop . ';
		');
		
		return new self($id);
	}

	public static function setProductData($data)
	{
		if (!$data['id_bestkit_psubscription_product']) {
			$data['id_bestkit_psubscription_product'] = null;
		}

		return Db::getInstance()->autoExecute(_DB_PREFIX_ . self::$definition['table'], $data, 'REPLACE', '', 0, false);
	}

	public function getPeriodIds()
	{
		return explode(',', $this->period_ids);
	}
}
