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

(function($){
	$(document).ready(function(){
		$('.bestkit_paypal').parent().prepend($('.bestkit_paypal'));
		
		if (typeof bestkitAllowedDays == 'undefined') {
			return false;
		}

		if (bestkitSubscriptionOnly == 1) {
			$('#add_to_cart span').text(bestkitSubscribeTranslate);
			$('#layer_cart .button-container .continue').remove();
		}
		
		//$('#add_to_cart').attr('id', 'subscription_add_to_cart');

		var getSelectedDate = function(){
			return $('.bestkit_paypal .start_date').datepicker( "getDate" );
		}

		var days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
		$('#paypal_period_selection').change(function(){
			var period_id = $(this).val();
			$('.bestkit_paypal .start_date').datepicker("destroy");
			$('.bestkit_paypal .start_date').datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: new Date(),
				beforeShowDay: function (date) {
					var dayName = days[date.getDay()];
					if (bestkitAllowedDays[period_id].indexOf(dayName) == -1) {
						return [false, ''];
					}
					
					return [true, ''];
				}
			});
		});

		$('#paypal_period_selection').change();

		$('#add_to_cart').click(function(e){
			if (bestkitSubscriptionOnly == 1) {
				if (getSelectedDate() == null) {
					$('.bestkit_paypal .start_date').focus();
					e.stopPropagation();
					return false;
				}
			}
		});

		$(document).ajaxSend(function(event, jqxhr, settings ){
			if (typeof settings.data != 'undefined' && settings.data.indexOf('controller=cart') != -1) {
				var period = ($('.bestkit_paypal select').val() || 0);
				var start_date = getSelectedDate();
				if (bestkitSubscriptionOnly == 1 && start_date == null) {
					event.abort();
				}

				if (start_date != null) {
					settings.data += '&bestkit_psubscription=1&period='+period+'&start_date='+$('.bestkit_paypal .start_date').val();
				}
			}
		});
	});
})(jQuery);
