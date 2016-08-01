<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:33:30
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/order-carrier.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6741285785783ca5af14d61-91490899%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '037357db6153476251df7ce3e1c38d3ebecb12b7' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/order-carrier.tpl',
      1 => 1468153966,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6741285785783ca5af14d61-91490899',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'identity_img_name' => 0,
    'back' => 0,
    'multi_shipping' => 0,
    'link' => 0,
    'bank_name' => 0,
    'account_number' => 0,
    'bank_number' => 0,
    'sucursale' => 0,
    'bank_address1' => 0,
    'bank_postcode' => 0,
    'bank_CityName' => 0,
    'bank_phone' => 0,
    'identity_number' => 0,
    'message' => 0,
    'link_conditions' => 0,
    'checkedTOS' => 0,
    'virtual_cart' => 0,
    'delivery_option_list' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783ca5b11bda5_97634470',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783ca5b11bda5_97634470')) {function content_5783ca5b11bda5_97634470($_smarty_tpl) {?>
	
	<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Shipping:'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('shipping', null, 0);?>
	<div id="carrier_area">
		<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

   </div>  
   
<div   align="center" style=" text-align:center; padding-left:15%; background-color:#FFFFCC;" >
<div  align="left" style=" text-align:left; padding-left:40%; background-color:#FFFFCC;" >
<?php if (!$_smarty_tpl->tpl_vars['identity_img_name']->value) {?>

<form     action=""   method="post" enctype="multipart/form-data" >
<span class="blink_text"> * </span>
<strong> בחר תמונה של תעודת זאות ואשר אותה </strong> 
<span class="blink_text"> * </span>
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
   
    <input type="file" name="identity_img" id="identity_img" value="<?php echo $_smarty_tpl->tpl_vars['identity_img_name']->value;?>
"  required />
    <input type="hidden" name="step" value="2" />
	<input type="hidden" name="back" value="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
"/>
    <input type="hidden" name="identityname" value="<?php echo $_smarty_tpl->tpl_vars['identity_img_name']->value;?>
"  required />
    <input type="submit" value=" אשר" name="submit" onclick=" save();" />

</form>
<?php } else { ?>

<?php echo smartyTranslate(array('s'=>' תעודת זאות: '),$_smarty_tpl);?>

 <?php echo $_smarty_tpl->tpl_vars['identity_img_name']->value;?>

 
 <form   action="" method="post" >
   
    <input type="submit" value=" שנה" name="change" id="change">
 	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="back" value="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
"/>
</form>
<?php }?>

</div>       
<form id="form" action="<?php ob_start();?><?php if ($_smarty_tpl->tpl_vars['multi_shipping']->value) {?><?php echo "multi-shipping=";?><?php echo (string)$_smarty_tpl->tpl_vars['multi_shipping']->value;?><?php }?><?php $_tmp1=ob_get_clean();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,$_tmp1), ENT_QUOTES, 'UTF-8', true);?>
" method="post" name=							"carrier_area">


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
    <td ><input   type="text" name="bank_name" id="bank_name" value="<?php echo $_smarty_tpl->tpl_vars['bank_name']->value;?>
"  required  autofocus /></td>
    <td><input  type="text" id="account_number" name="account_number" value="<?php echo $_smarty_tpl->tpl_vars['account_number']->value;?>
"  required /></td>
    <td><input  type="text" id="bank_number" name="bank_number" value="<?php echo $_smarty_tpl->tpl_vars['bank_number']->value;?>
"  required /></td>
    <td><input   type="text" id="sucursale" name="sucursale" value="<?php echo $_smarty_tpl->tpl_vars['sucursale']->value;?>
" required /></td>

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
					<input data-validate="isAddress" type="text" id="bank_address1" name="bank_address1" value="<?php echo $_smarty_tpl->tpl_vars['bank_address1']->value;?>
" required />
	</td>				
	<td>				<input  data-validate="isPostCode" type="text" id="bank_postcode" name="bank_postcode" value="<?php echo $_smarty_tpl->tpl_vars['bank_postcode']->value;?>
" />
																																																		
	</td>				
	<td>				<input data-validate="isCityName" type="text" name="bank_CityName" id="bank_CityName" value="<?php echo $_smarty_tpl->tpl_vars['bank_CityName']->value;?>
"   maxlength="64"  required />
	</td>	
<td>			
					<input  id="bank_country" readonly name="bank_country" value="ישראל" />
				
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
			
					<input data-validate="isPhoneNumber" type="tel" id="bank_phone" name="bank_phone" value="<?php echo $_smarty_tpl->tpl_vars['bank_phone']->value;?>
" required />
		
	</td>
    <td>	
				
				<input  data-validate="isNumber" type="text" name="identity_number" id="identity_number" value="<?php echo $_smarty_tpl->tpl_vars['identity_number']->value;?>
" required />
			
	</td>			
              

 	<td>		
			<textarea data-validate="isMessage" id="other" name="message" cols="26" rows="3"  ><?php echo $_smarty_tpl->tpl_vars['message']->value;?>
</textarea>
	</td>
    <td>
  		<sup>*</sup>שדה חובה
    </td>
 </tr>
 
  </table> 

 <div  align="center">
                        <p class="checkbox"> 
                            <label for="cgv"><?php echo smartyTranslate(array('s'=>'I agree to the terms of service and will adhere to them unconditionally.'),$_smarty_tpl);?>
</label>
                            <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link_conditions']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="iframe" rel="nofollow"><?php echo smartyTranslate(array('s'=>'(Read the Terms of Service)'),$_smarty_tpl);?>
</a>
                        </p>              
                         <span style="padding-left:205px;">   
                         	<input type="checkbox" style="border:1px solid #998833;"align="right" name="cgv" id="cgv" value="1" <?php if ($_smarty_tpl->tpl_vars['checkedTOS']->value) {?>checked="checked"<?php }?>  required />
                           	<input type="hidden" name="step" value="3" />
							<input type="hidden" name="back" value="<?php echo $_smarty_tpl->tpl_vars['back']->value;?>
" />
                          	<input type="hidden" name="img_name" value="<?php echo $_smarty_tpl->tpl_vars['identity_img_name']->value;?>
"  required />
                         </span>
  </div>
	<?php if (isset($_smarty_tpl->tpl_vars['virtual_cart']->value)&&$_smarty_tpl->tpl_vars['virtual_cart']->value||(isset($_smarty_tpl->tpl_vars['delivery_option_list']->value)&&!empty($_smarty_tpl->tpl_vars['delivery_option_list']->value))) {?>
						<button type="submit" name="processCarrier" class="button btn btn-default standard-checkout button-medium">
							<span>
								<?php echo smartyTranslate(array('s'=>'Proceed to checkout'),$_smarty_tpl);?>

								<i class="icon-chevron-right right"></i>
							</span>
						</button>
	<?php }?>
				
</form>  

</div>

<script type="text/javascript">
window.scroll(0,1400);
</script><?php }} ?>
