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
<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" class="cart_item{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)} last_item{/if}{if isset($productFirst) && $productFirst} first_item{/if}{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} alternate_item{/if} address_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if}" align="right">
	<td class="cart_product" align="right">
		<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">
        <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
	</td>
	<td class="cart_description" align="right">
    {if $last_loan} 
  	  <p align="right">
		(תאריך הלוואה אחרונה:  {$last_loan} ) 
 		</p>  
    {/if}
		{capture name=sep} : {/capture}
		{capture}{l s=' : '}{/capture}
		<p class="product-name" align="right">
        <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">
        {$product.name|escape:'html':'UTF-8'}
        </a>
    
        </p>
{if $product.reference}
            <small  align="right">
            {l s='SKU'}  
            {$smarty.capture.default}
            {$product.reference|escape:'html':'UTF-8'}
            </small>
            <small  align="right">
           מספר תשלומים : {$nb_payments}		
של {$total_price/$nb_payments} ש.ח.
            </small>
        <small  align="right">
          
          עמלה פייפאל :
של {$total_price * 3.14/100 + $nb_payments*1.2 } ש.ח.
            </small> 
              <small  align="right">
          
          עמלת העברה בנקאית :
של 1.35 ש.ח.
            </small>   
      <strong>      
      <small  align="right">
    {* paypal fees = 3.14% + nb_payments*1.2 shq *}
          סכום  להעברה  :
{$total_price *0.9686 - $nb_payments*1.2 -1.35  } ש.ח.

     </small>   
     </strong>             
{/if}
	</td>
{if !isset($noDeleteButton) || !$noDeleteButton}
		<td class="cart_delete text-center" data-title="{l s='Delete'}">
      
		{if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0) && empty($product.gift)}
			<div>
				<a rel="nofollow" title="{l s='Delete'}" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"><i class="icon-trash"></i></a>
			</div>
		{else}

		{/if}
		</td>
	{/if}
{if $bank_name}
	<td class="text-center" data-title="{l s='Bank account'}">
    <font color="#dd9933">
	  {l s=' בנק: '}    {$bank_name }  <br/>  {l s='מספר בנק:  '}  {$bank_number } <br/>  {l s=' סניף :'}  {$sucursale} <br/> {l s='   חשבון: '} {$account_number}  
		    <br/>   {l s=' כתובת:  '} 		 {$bank_address1}  <br/>{$bank_postcode}  {$bank_CityName} 
    <br/>    {l s= ' טל :' }              {$bank_phone} 
                   <br/>      {l s= '    תעודת זהות :   ' } {$identity_number}
   </font>
<p align="center">
      <img src="{$identity_img_real_name}"  alt="{$identity_img_name|escape:'html':'UTF-8'}" width="{$smallSize.width}" height="{$smallSize.height}"  />
</p>
<font size="+2" color="#660066">
<a href="javascript:history.back()" >
 לתקן >
</a>
</font>
    </td>
{/if}
</tr>
	