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

{capture name=path}{l s= $PayPal_Subscription mod='bestkit_psubscription'}{/capture}

<h1 class="page-heading">{l s= $Order_summary mod='bestkit_psubscription'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
	<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='bestkit_psubscription'}</p>
{else}
	<form action="{$link->getModuleLink('bestkit_psubscription', 'front', ['action' => 'go'], true)|escape:'html':'UTF-8'}" method="post">
		<div class="box bestkit_psubscription-box">
			<h3 class="page-subheading">{l s= $PayPal_Subscription mod='bestkit_psubscription'}</h3>
			<p class="bestkit_psubscription-indent">
				<strong class="dark">
					{l s=$choice_of_paypal mod='bestkit_psubscription'} {l s=$short_summary  mod='bestkit_psubscription'}
				</strong>
			</p>
			<p>
				- {l s= $The_total_amount mod='bestkit_psubscription'}
				<span id="amount" class="price">{displayPrice price=$total}</span>
				{if $use_taxes == 1}
					{l s='' mod='bestkit_psubscription'}
				{/if}
			</p>
			<p>
				-
				{l s = $will_pay_in mod='bestkit_psubscription'}&nbsp;{*<b>{Context::getContext()->currency->name|escape:false}</b>*}
				<input type="hidden" name="currency_payement" value="{Context::getContext()->currency->id|escape:false}" />
			</p>
            <p><strong class="dark">
				- {l s= $You_will_pay_with mod='bestkit_psubscription'} {l s=$nb_payments} {l s=$payments } {l s=$sub_total} {l s='ש.ח.'} {* {Context::getContext()->currency->name|escape:false} *}
				<br />
			</strong>
			</p>
			<p>
				- {l s= $be_redirected_to_PayPal  mod='bestkit_psubscription'}
				<br />
				- {l s= $Please_confirm  mod='bestkit_psubscription'}.
			</p>
		</div>
		<p class="cart_navigation clearfix" id="cart_navigation">
		{*	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" class="button-exclusive btn btn-default">
				<i class="icon-chevron-left"></i>{l s='Other payment methods' mod='bestkit_psubscription'}
			</a>
          *}
			<button type="submit" class="button btn btn-default button-medium">
				<span>{l s= $I_confirm_my_order mod='bestkit_psubscription'}<i class="icon-chevron-right right"></i></span>
			</button>
		</p>
	</form>
{/if}
