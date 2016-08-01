{*
* 2007-2014 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if !isset($error)}
	<p class="alert alert-success">{l s='Your subscription on %s is created.' sprintf=$shop_name mod='bestkit_psubscription'}</p>
	<div class="box order-confirmation">
		- {l s='PayPal Subscription amount' mod='bestkit_psubscription'} <span class="price"><strong>{$total_to_pay|escape:false}</strong></span>		
		<br />{l s='For any questions or for further information, please contact our' mod='bestkit_psubscription'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='bestkit_psubscription'}</a>.
	</div>
{else}
	<p class="alert alert-danger">
		{$error|escape:'html':'UTF-8'}<br><br>
		<a style="color: white" href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Leave message to Customer service department' mod='bestkit_psubscription'}</a>
	</p>
{/if}
