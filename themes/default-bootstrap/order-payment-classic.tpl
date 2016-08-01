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
<div class="paiement_block" align="center">
    <div id="HOOK_TOP_PAYMENT" align="center">{$HOOK_TOP_PAYMENT}</div>
  {if $HOOK_PAYMENT}
        {if !$opc}
            <div id="order-detail-content" class="table_block table-responsive" align="center">
                <table id="cart_summary" class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="cart_product first_item">{l s='Product'}</th>
                        <th class="cart_description item text-right">{l s='Description'}</th>
                       	 {if $bank_name}                
                      		 <th class="cart_total last_item text-right">{l s='חשבון בנק להעברה'}</th>
                      	 {/if}
                      {*  <th class="cart_total last_item text-right">{l s='Total'}</th> *}
                    </tr>
                    </thead>
                    <tfoot>
               
      <!--  TOTAL   -->                       
                        <tr class="cart_total_price">
                            <td colspan="4" class="total_price_container text-right"><span>{l s='Total'}</span></td>
                            <td colspan="2" class="price" id="total_price_container">
                                <span id="total_price" data-selenium-total-price="{$total_price}">{displayPrice price=$total_price}</span>
                            </td>
                        </tr>
                   </tfoot>

                    <tbody>
                    {foreach from=$products item=product name=productLoop}
                        {assign var='productId' value=$product.id_product}
                        {assign var='productAttributeId' value=$product.id_product_attribute}
                        {assign var='quantityDisplayed' value=0}
                        {assign var='cannotModify' value=0}
                        {assign var='odd' value=$product@iteration%2}
                        {assign var='noDeleteButton' value=1}

                        {* Display the product line *}
                        {include file="$tpl_dir./shopping-cart-product-line.tpl"}
                      
                    {/foreach}
                    {assign var='last_was_odd' value=$product@iteration%2}
                      </tbody>
               </table>
         <div id="HOOK_PAYMENT" > 
			 <table align="right" >
             <tr ><td  width="800">{$HOOK_PAYMENT}</td>
             </tr>
             </table> 
    	</div>
                                  
            </div> <!-- end order-detail-content -->
        {/if}
      
   {else}
        <p class="alert alert-warning">{l s='No payment modules have been installed.'}</p>
   {/if}
    
 	 {if !$opc}
        <p class="cart_navigation clearfix">
            <a href="{$link->getPageLink('order', true, NULL, "step=2")|escape:'html':'UTF-8'}" title="{l s='Previous'}" class="button-exclusive btn btn-default">
                <i class="icon-chevron-left"></i>
                {l s='Continue shopping'}
            </a>
        </p>
      
		</div> <!-- end opc_payment_methods -->
	{/if}
<!-- end HOOK_TOP_PAYMENT -->
