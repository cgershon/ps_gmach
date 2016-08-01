{**
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
* @package    bestkit_psubscription
* @author     BEST-KIT.COM (contact@best-kit.com)
* @copyright  http://best-kit.com
**}

<div class="bestkit_paypal">
	<input type="hidden" name="bestkit_psubscription" value="1" />
	<h4><img src="{$bestkitPath|escape:'html':'UTF-8'}logo.png"/> {l s={$Subscription} mod='bestkit_psubscription'}</h4>
	<div class="inner">
		<label>{l s=$Period_type  mod='bestkit_psubscription'}</label>
		<select class="form-control no-print" id="paypal_period_selection" name="period">
		{foreach from=$periods item=period}
			<option value="{$period->id|escape:'html':'UTF-8'}">{$period->title|escape:'html':'UTF-8'}</option>
		{/foreach}
		</select>
		<br/>
		<label>{l s={$text_start_date}    mod='bestkit_psubscription'}</label>
	
	{* <input type="text" class="text start_date" readonly="readonly" name="start_date" style="border: 1px solid rgb(189, 194, 201);">   ADDED YGPC *}
<input type="text" readonly="readonly" name="start_date" value= {$Start_date}  style="border: 1px solid rgb(189, 194, 201);">
	</div>
</div>
<script>
	var bestkitAllowedDays = [];
	var bestkitSubscriptionOnly = {$paypalProduct->subscription_only|intval};
	var bestkitSubscribeTranslate = '{l s={$subscribe} mod='bestkit_psubscription'}';
	{foreach from=$periods item=period}
		bestkitAllowedDays[{$period->id}] = {$period->getAllowedStartDays()|json_encode};
	{/foreach}
</script>