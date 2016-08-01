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
* @package    bestkit_paypal
* @author     BEST-KIT.COM (contact@best-kit.com)
* @copyright  http://best-kit.com
**/

var only_subscription = false;

(function($){
	$(document).ready(function(){
		if (bestkitSubscriptionCount > 0) {
			$('.cart_navigation .button-exclusive').hide();
			if (bestkitTermsUrl != 0) {
				$('.order_carrier_content .iframe').attr('href', bestkitTermsUrl);
			}
		}

		var items = [];
		$('#cart_summary tr.cart_item').each(function(){
			items.push($(this).attr('id').replace('product_', ''));
		});

		if (items.length > 0) {
			$.ajax({
				url: bestkitController+'action=cartDetails',
				type: 'POST',
				dataType: 'JSON',
				data: {'items': items},
				success: function(json){
					for (var i in json) {
						$('#'+json[i].row+' .cart_description').append('<small><b>'+bestkitTranslator.subscription+'</b> '+json[i].period+' '+bestkitTranslator.from+' '+json[i].start_date+'</small>');
						
						if (json[i].only_subscription == 1) {
							only_subscription = true;
						}
					}
				}
			});
		}
	});
})(jQuery);