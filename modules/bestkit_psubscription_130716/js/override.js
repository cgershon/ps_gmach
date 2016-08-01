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

var gotReadyBestkit = false;
var overrideAddToSubscribeBestkitProducts = [];

var _overrideAddToSubscribeBestkit = function(id_product){
	var block = $('.ajax_add_to_cart_button[data-id-product="'+id_product+'"]');
	block.find('span').text(bestkitTranslator.subscribe);
	block.click(function(){
		window.location.href = block.parent().find('.lnk_view').attr('href');
		return false;
	});
}

var overrideAddToSubscribeBestkit = function(id_product){
	if (gotReadyBestkit) {
		_overrideAddToSubscribeBestkit(id_product);
	} else {
		overrideAddToSubscribeBestkitProducts.push(id_product);
	}
}

$(document).ready(function(){
	for (var i in overrideAddToSubscribeBestkitProducts) {
		var id_product = overrideAddToSubscribeBestkitProducts[i];
		_overrideAddToSubscribeBestkit(id_product);
	}

	gotReadyBestkit = true;
});