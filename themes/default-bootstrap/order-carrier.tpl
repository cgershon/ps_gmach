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
<script type="text/javascript">
window.scroll(0,1400);
</script>	
	{capture name=path}{l s='Shipping:'}{/capture}
	{assign var='current_step' value='shipping'}
	<div id="carrier_area">
		{include file="$tpl_dir./order-steps.tpl"}
		{include file="$tpl_dir./errors.tpl"}
   </div>  

<div   style="  padding-left:25%; background-color:#FFFFCC;" >
<div style="text-align:right; padding-right:10px;">

<p class="info-title">
			<br/>
            <span> על מנת להפקיד את הסכום המבוקש לחשבונכם, מלאו את הטופס  
           
וצרפו את תמונת תעודת הזהות<u> שלכם </u>( קובץ  JPG או PNG או GIF ) ולאשר אותה .
			</span>
    </p>
</div>
<div  style=" text-align:left; padding-left:32%; background-color:#FFFFCC;" >

{if !$identity_img_name }

<form     action=""   method="post" enctype="multipart/form-data" >
<font size="+2"><span class="blink_text"> * </span></font>
<strong> בחרו תמונה של תעודת הזהות שלכם ואשרו אותה </strong> 

<style type="text/css">
.blink_text {

animation:1s blinker linear infinite;
-webkit-animation:1s blinker linear infinite;
-moz-animation:1s blinker linear infinite;

 color: red;
}

@-moz-keyframes blinker {  
 0% { opacity: 1.0; }
 50% { opacity: 0.2; }
 100% { opacity: 1.0; }
 }

@-webkit-keyframes blinker {  
 0% { opacity: 1.0; }
 50% { opacity: 0.2; }
 100% { opacity: 1.0; }
 }

@keyframes blinker {  
 0% { opacity: 1.0; }
 60% { opacity: 0.2; }
 100% { opacity: 1.0; }
 }
 
 
 </style>
  <span style=" padding-left:70px;" >
    <input type="file" name="identity_img" id="identity_img" value="{$identity_img_name}"  required />
    <input type="hidden" name="step" value="2" />
	<input type="hidden" name="back" value="{$back}"/>
    <input type="hidden" name="identityname" value="{$identity_img_name}"  required />
    <input type="submit" value=" אשר" name="submit" onclick=" save();" />
	</span>
</form>
{else}


  <span style=" padding-left:100px;">
<span style=" padding-left:20px;">  {l s=' תעודת זהות '}</span>
<p >
      <img src="{$identity_img_real_name}"  alt="{$identity_img_name|escape:'html':'UTF-8'}" width="100" height="100"  />
</p>

 <form   action="" method="post" >
  <span style=" padding-left:20px;">
    <input type="submit" value=" לשנות" name="change" id="change">
  </span> 
 	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="back" value="{$back}"/>
</form>
	</span>
{/if}

</div>
<form id="form" action="{$link->getPageLink('order', true, NULL, "{if $multi_shipping}multi-shipping={$multi_shipping}{/if}")|escape:'html':'UTF-8'}" method="post" name=							"carrier_area">



<table>
      <thead>
                 <tr>
                     <th>			<label for ="bank_name"  style="padding-top:50px; padding-left:50px;padding-left:50px;" >שם הבנק<sup>*</sup></label>
                    </th>
                     <th>			<label for="bank_account" style="padding-top:50px; padding-left:50px;">מספר חשבון בנק <sup>*</sup></label>	
                    </th> 								
                     <th>  			<label for="bank_number" style="padding-top:50px; padding-left:50px;">מס הבנק <sup>*</sup></label>  
                    </th>
                     <th>			<label for="bank_sucursale" style="padding-top:50px; padding-left:50px;"> מס הסניף <sup>*</sup></label>
			        </th>
                   
                 </tr>
        </thead>
<tr >
    <td ><input   type="text" name="bank_name" id="bank_name" value="{$bank_name}"  required  autofocus /></td>
    <td><input  type="text" id="account_number" name="account_number" value="{$account_number}" {*pattern="[0-9]{literal}{7}{/literal}"*} required /></td>
    <td><input  type="text" id="bank_number" name="bank_number" value="{$bank_number}" {*pattern="[0-9]{literal}{3}{/literal}"*} required /></td>
    <td><input   type="text" id="sucursale" name="sucursale" value="{$sucursale}" {*pattern="[0-9]{literal}{3}{/literal}" *}required /></td>

</tr>
 </table>
<table>           
                  <tr>
                    
                    <th>		<label for="address1" style="padding-top:50px; padding-left:50px;">כתובת הבנק<sup>*</sup></label>
                    </th>
                      <th>	  <label for="postcode" style="padding-top:50px; padding-left:50px;">מיקוד <sup></sup></label>
                    </th>
                    <th>       <label for="city" style="padding-top:50px; padding-left:50px;">עיר <sup>*</sup></label>
                    </th>
                    <th>	   <label for="id_country" style="padding-top:50px; padding-left:50px;">ארץ</label>
                    </th>
                     
                 </tr>
 	       
  <tr>
    <td>   
					<input data-validate="isAddress" type="text" id="bank_address1" name="bank_address1" value="{$bank_address1}" required />
	</td>				
	<td>				<input  data-validate="isPostCode" type="text" id="bank_postcode" name="bank_postcode" value="{$bank_postcode}" />
																																																		
	</td>				
	<td>				<input data-validate="isCityName" type="text" name="bank_CityName" id="bank_CityName" value="{$bank_CityName}" {*pattern="[ת-א]{literal}{6}{/literal}"*}  maxlength="64"  required />
	</td>	
<td>			
					<input  id="bank_country" readonly name="bank_country" value="ישראל" />
				
</td>
  </tr>
  
  </table>
<table align="center">

                 <tr>
                    
                    <th>					<label for="phone" style="padding-top:50px; padding-left:50px;">טלפון הבנק<sup>*</sup></label>

                    </th>
                      <th>					<label for="tz" style="padding-top:50px; padding-left:50px;">מספר תעודת  זיהות<sup>*</sup></label> 
  					 </th>
		             <th>        		<label for="other" style="padding-top:50px; padding-left:120px;">מידע נוסף</label>
                    </th>
                  </tr>   
         
  <tr>       
 <td   style="text-align:center">     
			
					<input data-validate="isPhoneNumber" type="tel" id="bank_phone" name="bank_phone" value="{$bank_phone}" {*pattern="[0-9]{literal}{10}{/literal}" *}required />
		
	</td>
    <td>	
				
				<input  data-validate="isNumber" type="text" name="identity_number" id="identity_number" value="{$identity_number}" {*pattern="[0-9]{literal}{10}{/literal}" *}required />
			
	</td>			
              

 	<td>		
			<textarea data-validate="isMessage" id="other" name="message" cols="46" rows="3"  >{$message }</textarea>
	</td>
  
 </tr>
 
  </table> 

 <div> 

    		<p class="checkbox" >
              
             <label for="cgv"> &nbsp;{l s='I agree to the terms of service and will adhere to them unconditionally.'}    </label>  
             
                          {*  <a href="{$link_conditions|escape:'html':'UTF-8'}" class="iframe" rel="nofollow">{l s='(Read the Terms of Service)'}</a> *}
                            <a href="http://gmach.konim.biz/index.php?id_cms=3&controller=cms&id_lang=1"  rel="nofollow" dir="rtl">
                            {l s='(Read the Terms of Service)'}</a>
                         	<input type="checkbox"  name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if}  required />
                            <input type="hidden" name="step" value="3" />
							<input type="hidden" name="back" value="{$back}" />
                          	<input type="hidden" name="img_name" value="{$identity_img_name}"  required />    
      	   </p>                                      
	<span 	style="padding-right:20px; padding-top:2px;">			
	{if isset($virtual_cart) && $virtual_cart || (isset($delivery_option_list) && !empty($delivery_option_list))}
  					
							<button type="submit" name="processCarrier" class="button btn btn-default standard-checkout button-medium">
							 <span >
								{l s='Proceed to checkout'}
								<i class="icon-chevron-right right"></i>
							</span>
						</button>
	{/if}
 	<sup style="padding-right:30px;">*</sup>שדה חובה

  	</span>
</form>  




</div>       
   


