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
*  @author PrestaShop SA 
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="row bestkit_psubscription_payment" align="center">
	<div class="col-xs-12 col-md-6">
		<p class="payment_module">
			<a id="bestkit_psubscription_button" class="bankwire paypal-subscription" href="{$link->getModuleLink('bestkit_psubscription', 'front', ['action' => 'payment'], true)|escape:'html'}" title="{l s='PayPal בתשלומים' mod='bestkit_psubscription'}">
				<img src="{$this_path|escape:false}img/paypal-payment.png" alt="{l s='PayPal בתשלומים' mod='bestkit_psubscription'}" width="86" height="49" />
				{l s=$payments mod='bestkit_psubscription'} <span>{l s='' mod='bestkit_psubscription'}</span>
			</a>
		</p>
	</div>
</div>
<script>
	(function($){
		{if $totalCartCount > 1}
			$('#bestkit_psubscription_button').click(function(){
				alert('{l s='Your cart must exists only one PayPal subscription!' mod='bestkit_psubscription'}');
				return false;
			});
		{/if}

		
		$('#HOOK_PAYMENT .row:not(.bestkit_psubscription_payment)').hide();
		setInterval(function(){
			if (only_subscription) {
				$('#HOOK_PAYMENT .row:not(.bestkit_psubscription_payment)').hide();
			} else {
				$('#HOOK_PAYMENT .row:not(.bestkit_psubscription_payment)').show();
			}
		}, 1000);
	})(jQuery);
</script>
