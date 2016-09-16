{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="contact-link" {if isset($is_logged) && $is_logged} class="is_logged"{/if}>
	<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="{l s='Contact us' mod='blockcontact'}">{l s='Contact us' mod='blockcontact'}</a>
</div>
{if $telnumber}
	<span class="shop-phone{if isset($is_logged) && $is_logged} is_logged{/if}">
		<i class="icon-phone"></i>{l s='Call us now:' mod='blockcontact'} <strong>{$telnumber}</strong>
     </span>  
     <center>
     <br/>
     <span style="text-align:center">
	<img src="../../img/פסוק.png" alt="פסוק" />
    </span>
    </span>
 <font color="#FFFFFF">    	
 <marquee  direction="right"  width="1000" scrolldelay="70">
	&nbsp;ידוע ללווה שעמלת חברת " פייפאל " תרד מהסכום המופקד בחשבונו ולא נחשבת ריבית
&nbsp; אם אין לכם כרטיס אשראי או להלוואות יותר גדולות נא להסתכל בקישור "עוד גמ'חים" -	&nbsp;מבקשים מהלווים לקחת בחשבון שיש מקרים מאוד דחופים ולא לקחת הלוואה להסגת מותרות.</marquee>
</font>

{/if}
