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

{capture name=path}{l s='PayPal Subscriptions' mod='bestkit_psubscription'}{/capture}
<h1 class="page-heading bottom-indent">{l s='PayPal Subscription Error' mod='bestkit_psubscription'}</h1>
<div class="block-center" id="block-history">
	<p class="alert alert-danger">
		{l s='The order has been canceled'}
	</p>
</div>
<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="{$base_dir|escape:'html':'UTF-8'}">
			<span><i class="icon-chevron-left"></i> {l s='Continue shopping' mod='bestkit_psubscription'}</span>
		</a>
	</li>
</ul>