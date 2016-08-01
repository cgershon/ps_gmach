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

{capture name=path}{l s='PayPal Subscription' mod='bestkit_psubscription'}{/capture}

<h1 class="page-heading">{l s='Order summary' mod='bestkit_psubscription'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
	<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='bestkit_psubscription'}</p>
{else}
	<form action="{$link->getModuleLink('bestkit_psubscription', 'front', ['action' => 'go'], true)|escape:'html':'UTF-8'}" method="post">
		<div class="box bestkit_psubscription-box">
			<h3 class="page-subheading">{l s='PayPal Subscription' mod='bestkit_psubscription'}</h3>
			<p class="bestkit_psubscription-indent">
				<strong class="dark">
					{l s='You have chosen to pay by PayPal Subscription.' mod='bestkit_psubscription'} {l s='Here is a short summary of your order:' mod='bestkit_psubscription'}
				</strong>
			</p>
			<p>
				- {l s='The total amount of your order comes to:' mod='bestkit_psubscription'}
				<span id="amount" class="price">{displayPrice price=$total}</span>
				{if $use_taxes == 1}
					{l s='(tax incl.)' mod='bestkit_psubscription'}
				{/if}
			</p>
			<p>
				-
				{l s='You will pay in:' mod='bestkit_psubscription'}&nbsp;<b>{Context::getContext()->currency->name|escape:false}</b>
				<input type="hidden" name="currency_payement" value="{Context::getContext()->currency->id|escape:false}" />
			</p>
			<p>
				- {l s='You will be redirected to the PayPal on the next page.' mod='bestkit_psubscription'}
				<br />
				- {l s='Please confirm your order by clicking \'I confirm my order\'' mod='bestkit_psubscription'}.
			</p>
		</div>
		<p class="cart_navigation clearfix" id="cart_navigation">
			<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" class="button-exclusive btn btn-default">
				<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='bestkit_psubscription'}
			</a>
			<button type="submit" class="button btn btn-default button-medium">
				<span>{l s='I confirm my order' mod='bestkit_psubscription'}<i class="icon-chevron-right right"></i></span>
			</button>
		</p>
	</form>
{/if}
