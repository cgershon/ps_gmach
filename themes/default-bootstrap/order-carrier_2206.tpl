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
*  International Registered Trademark &   of PrestaShop SA
* YGPC -> added datas of bank form  modify page properties Select the Title/Encoding category, and change 
Encoding to Unicode UTF-8.
*}
	
	{capture name=path}{l s='Shipping:'}{/capture}
	{assign var='current_step' value='shipping'}
	<div id="carrier_area">
		{include file="$tpl_dir./order-steps.tpl"}
		{include file="$tpl_dir./errors.tpl"}
   </div>  
   
<div   align="center" style=" text-align:center; padding-left:15%; background-color:#FFFFCC;" >
<div  align="left" style=" text-align:left; padding-left:40%; background-color:#FFFFCC;" >
{if !$identity_img_name }
<form   action="https://gmach.konim.biz/he/הזמנה" method="post" enctype="multipart/form-data">
   בחר תמונה של תעודת זאות ואשר אותה 
  
    <input type="file" name="identity_img" id="identity_img" value="{$identity_img_name}"  >
    <input type="hidden" name="step" value="2" />
	<input type="hidden" name="back" value="{$back}"/>
    <input type="submit" value=" אשר" name="submit">

</form>
{else}

{l s=' תעודת זאות '}
 {$identity_img_name}
 
 <form   action="https://gmach.konim.biz/he/הזמנה" method="post" >
   
    <input type="submit" value=" שנה" name="change" id="change">
 	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="back" value="{$back}"/>
</form>
{/if}

</div>       
<form id="form" action="{$link->getPageLink('order', true, NULL, "{if $multi_shipping}multi-shipping={$multi_shipping}{/if}")|escape:'html':'UTF-8'}" method="post" name=							"carrier_area">


<div style="text-align:right; padding-right:50px;">

<p class="info-title">
			<br/> על מנת להפקיד הסכום המבוקש לחשבון שלך ,מלא הטופס למטה 
וצרף התעודת זאות שאתה סרקתה לקובץ JPG או PGN או PDF או GIF
    </p>
</div>


<table>
      <thead>
                 <tr>
                     <th>			<label for ="bank_name"  >שם הבנק<sup>*</sup></label>
                    </th>
                     <th>			<label for="address1">מספר חשבון בנק <sup>*</sup></label>	
                    </th> 								
                     <th>  			<label for="company">מס הבנק <sup>*</sup></label>  
                    </th>
                     <th>			<label for="address2"> מס הסניף <sup>*</sup></label>
			        </th>
                   
                 </tr>
        </thead>
<tr >
    <td ><input   type="text" name="bank_name" id="bank_name" value=""  required  autofocus /></td>
    <td><input  type="text" id="account_number" name="account_number" value="" {*pattern="[0-9]{literal}{7}{/literal}"*} required /></td>
    <td><input  type="text" id="bank_number" name="bank_number" value="" {*pattern="[0-9]{literal}{3}{/literal}"*} required /></td>
    <td><input   type="text" id="sucursale" name="sucursale" value="" {*pattern="[0-9]{literal}{3}{/literal}" *}required /></td>

</tr>
 </table>
<table>           
                  <tr>
                    
                    <th>		<label for="address1">כתובת הבנק<sup>*</sup></label>
                    </th>
                      <th>	  <label for="postcode">מיקוד <sup>*</sup></label>
                    </th>
                    <th>       <label for="city">עיר <sup>*</sup></label>
                    </th>
                    <th>	   <label for="id_country" >ארץ</label>
                    </th>
                     
                 </tr>
 	       
  <tr>
    <td>   
					<input class="is_required validate form-control" data-validate="isAddress" type="text" id="address1" name="address1" value="" required />
	</td>				
	<td>				<input  data-validate="isPostCode" type="text" id="postcode" name="postcode" value="" />
																																																		
	</td>				
	<td>				<input data-validate="isCityName" type="text" name="bank_CityName" id="city" value="" {*pattern="[ת-א]{literal}{6}{/literal}"*}  maxlength="64"  required />
	</td>	
<td>			
					<input  id="id_country" readonly name="id_country" value="ישראל" />
				
</td>
  </tr>
  
  </table>
<table align="center">

                 <tr>
                    
                    <th>					<label for="phone">טלפון הבנק<sup>*</sup></label>

                    </th>
                      <th>					<label for="tz">מספר תעודת  זיהות<sup>*</sup></label> 
  					 </th>
		             <th>        		<label for="other">מידע נוסף</label>
                    </th>
                  </tr>   
          
  <tr>       
 <td   style="text-align:center">     
			
					<input data-validate="isPhoneNumber" type="tel" id="bank_phone" name="bank_phone" value="" {*pattern="[0-9]{literal}{10}{/literal}" *}required />
		
	</td>
    <td>	
				
				<input  data-validate="isNumber" type="text" name="identity_number" id="identity_number" value="" {*pattern="[0-9]{literal}{10}{/literal}" *}required />
			
	</td>			
              

 	<td>		
			<textarea data-validate="isMessage" id="other" name="message" cols="26" rows="3" ></textarea>
	</td>
    <td>
  		<sup>*</sup>שדה חובה
    </td>
 </tr>
 
  </table> 

 <div  align="center">
                        <p class="checkbox"> 
                            <label for="cgv">{l s='I agree to the terms of service and will adhere to them unconditionally.'}</label>
                            <a href="{$link_conditions|escape:'html':'UTF-8'}" class="iframe" rel="nofollow">{l s='(Read the Terms of Service)'}</a>

                        </p>
              
                         <span style="padding-left:205px;">   
                         	<input type="checkbox" style="border:1px solid #998833;"align="right" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if}  required />
                           	<input type="hidden" name="step" value="3" />
							<input type="hidden" name="back" value="{$back}" />
                         </span>
  </div>
	{if isset($virtual_cart) && $virtual_cart || (isset($delivery_option_list) && !empty($delivery_option_list))}
						<button type="submit" name="processCarrier" class="button btn btn-default standard-checkout button-medium">
							<span>
								{l s='Proceed to checkout'}
								<i class="icon-chevron-right right"></i>
							</span>
						</button>
	{/if}
				
</form>  

</div>

<script type="text/javascript">
window.scroll(0,1400);
</script>