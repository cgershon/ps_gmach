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

require_once (_PS_MODULE_DIR_ . 'bestkit_psubscription/bestkit_psubscription.php');

class AdminBestkitPsubscriptionPeriodController extends ModuleAdminController
{
    protected $_module = NULL;

	protected $position_identifier = 'id_bestkit_psubscription_period';

    public function __construct()
    {
    	$this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'bestkit_psubscription_period';
        $this->identifier = 'id_bestkit_psubscription_period';
        $this->className = 'BestkitPsubscriptionPeriod';
        $this->lang = TRUE;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

		$this->fields_list = array(
			'id_bestkit_psubscription_period' => array('title' => $this->l('ID'), 'width' => 25),
			'title' => array('title' => $this->l('Title'), 'width' => 150),
			'billing_period' => array('title' => $this->l('Billing Period'), 'width' => 150),
			'billing_freq' => array('title' => $this->l('Billing Freq'), 'width' => 150),
			'billing_cycles' => array('title' => $this->l('Billing Cycles'), 'width' => 150),
			'allowed_start_days' => array('title' => $this->l('Allowed Start Days'), 'width' => 150),
		);

        parent::__construct();
    }

    public function renderForm()
    {
        $this->display = 'edit';
        $this->initToolbar();
        if (!$obj = $this->loadObject(TRUE)) {
            return;
        }

        $this->fields_form = array(
            'tinymce' => TRUE,
            'legend' => array('title' => $this->l('Billing Period'), 'image' =>
                    '../img/admin/tab-categories.gif'),
            'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Title:'),
                    'required' => TRUE,
                    'lang' => TRUE,
					'name' => 'title',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Billing Period:'),
                    'name' => 'billing_period',
                    'style' => 'width:100px',
                    'options' => array(
                        'query' => bestkit_psubscription::prepareArrayConfig(BestkitPsubscriptionPeriod::getBillingPeriods()),
                        'id' => 'id',
                        'name' => 'label',
                    )
                ),
				array(
					'type' => 'text',
					'label' => $this->l('Billing Freq:'),
                    'required' => TRUE,
					'name' => 'billing_freq',
                ),
                array(
					'type' => 'text',
					'label' => $this->l('Billing Cycles:'),
					'name' => 'billing_cycles',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Allowed Start Weekdays:'),
                    'name' => 'allowed_start_days[]',
                    'required' => TRUE,
                    'multiple' => true,
                    'size' => 7,
                    'style' => 'width:100px',
                    'options' => array(
                        'query' => BestkitPsubscriptionPeriod::getWeekdays(),
                        'id' => 'name',
                        'name' => 'title',
                    )
                )
            ), 'submit' => array('title' => $this->l('   Save   '))
        );

        if (!is_null($obj->id)) {
            $this->fields_value['allowed_start_days[]'] = $obj->getAllowedStartDays();
        }

        return parent::renderForm();
    }

    public function processSave()
    {
    	$_POST['allowed_start_days'] = implode(',', (array)Tools::getValue('allowed_start_days', array()));
        return parent::processSave();
    }
}
