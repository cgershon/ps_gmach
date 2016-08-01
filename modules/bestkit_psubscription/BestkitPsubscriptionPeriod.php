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

class BestkitPsubscriptionPeriod extends ObjectModel
{
    public $id;
    public $id_bestkit_psubscription_period;
    public $billing_period;
    public $billing_freq;
    public $billing_cycles;
    public $allowed_start_days;
    public $title;
    
    protected static $_billing_periods = array('Day', 'Week', 'Month', 'Year');

    protected static $_allowed_start_days = array(
        array('name' => 'sunday', 'title' => 'Sunday'),
        array('name' => 'monday', 'title' => 'Monday'),
        array('name' => 'tuesday', 'title' => 'Tuesday'),
        array('name' => 'wednesday', 'title' => 'Wednesday'),
        array('name' => 'thursday', 'title' => 'Thursday'),
        array('name' => 'friday', 'title' => 'Friday'),
        array('name' => 'saturday', 'title' => 'Saturday'),
	);

    public static $definition = array(
        'table' => 'bestkit_psubscription_period',
        'primary' => 'id_bestkit_psubscription_period',
        'multilang' => TRUE,
        'fields' => array(
        	'title' => array('lang' => TRUE, 'type' => self::TYPE_STRING, 'required' => TRUE, 'validate' => 'isCatalogName'),
            'billing_period' => array('type' => self::TYPE_STRING, 'required' => TRUE),
            'billing_freq' => array('type' => self::TYPE_INT, 'required' => TRUE, 'validate' => 'isInt'),
            'billing_cycles' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'allowed_start_days' => array('type' => self::TYPE_STRING, 'required' => TRUE),
            ),
        );

	public static function getConfigCollection()
	{
		return Db::getInstance()->ExecuteS('
			SELECT a.`id_bestkit_psubscription_period`, l.`title` FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` a
			LEFT JOIN `' . _DB_PREFIX_ . self::$definition['table'] . '_lang` l 
			ON (a.`id_bestkit_psubscription_period` = l.`id_bestkit_psubscription_period`)
			WHERE l.`id_lang` = ' . (int)Context::getContext()->language->id . '
		;');
	}

	public static function getBillingPeriods()
	{
		return self::$_billing_periods;
	}

    public static function getWeekdays()
    {
        return self::$_allowed_start_days;
    }

	public function getAllowedStartDays()
	{
		return explode(',', $this->allowed_start_days);
	}
}
