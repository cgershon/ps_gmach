<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:28:08
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/shopping-cart-product-line.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13594246665783c91862dc77-21281361%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98d2f1ea4ee1975202f720f84cf5c76425ed8e75' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/shopping-cart-product-line.tpl',
      1 => 1468234628,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13594246665783c91862dc77-21281361',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'quantityDisplayed' => 0,
    'productLast' => 0,
    'ignoreProductLast' => 0,
    'productFirst' => 0,
    'productId' => 0,
    'productAttributeId' => 0,
    'customizedDatas' => 0,
    'odd' => 0,
    'link' => 0,
    'smallSize' => 0,
    'nb_payments' => 0,
    'total_price' => 0,
    'noDeleteButton' => 0,
    'token_cart' => 0,
    'bank_name' => 0,
    'bank_number' => 0,
    'sucursale' => 0,
    'account_number' => 0,
    'bank_address1' => 0,
    'bank_postcode' => 0,
    'bank_CityName' => 0,
    'bank_phone' => 0,
    'identity_number' => 0,
    'identity_img_real_name' => 0,
    'identity_img_name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783c918738714_73944664',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783c918738714_73944664')) {function content_5783c918738714_73944664($_smarty_tpl) {?>
<tr id="product_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0) {?>nocustom<?php } else { ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['gift'])) {?>_gift<?php }?>" class="cart_item<?php if (isset($_smarty_tpl->tpl_vars['productLast']->value)&&$_smarty_tpl->tpl_vars['productLast']->value&&(!isset($_smarty_tpl->tpl_vars['ignoreProductLast']->value)||!$_smarty_tpl->tpl_vars['ignoreProductLast']->value)) {?> last_item<?php }?><?php if (isset($_smarty_tpl->tpl_vars['productFirst']->value)&&$_smarty_tpl->tpl_vars['productFirst']->value) {?> first_item<?php }?><?php if (isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])&&$_smarty_tpl->tpl_vars['quantityDisplayed']->value==0) {?> alternate_item<?php }?> address_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
 <?php if ($_smarty_tpl->tpl_vars['odd']->value) {?>odd<?php } else { ?>even<?php }?>" align="right">
	<td class="cart_product" align="right">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['product']->value['id_product'],$_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['category'],null,null,$_smarty_tpl->tpl_vars['product']->value['id_shop'],$_smarty_tpl->tpl_vars['product']->value['id_product_attribute'],false,false,true), ENT_QUOTES, 'UTF-8', true);?>
">
        <img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['id_image'],'small_default'), ENT_QUOTES, 'UTF-8', true);?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" <?php if (isset($_smarty_tpl->tpl_vars['smallSize']->value)) {?>width="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['width'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['height'];?>
" <?php }?> /></a>
	</td>
	<td class="cart_description" align="right">
    
		<?php $_smarty_tpl->_capture_stack[0][] = array('sep', null, null); ob_start(); ?> : <?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		<?php $_smarty_tpl->_capture_stack[0][] = array('default', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>' : '),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
		<p class="product-name" align="right">
        <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['product']->value['id_product'],$_smarty_tpl->tpl_vars['product']->value['link_rewrite'],$_smarty_tpl->tpl_vars['product']->value['category'],null,null,$_smarty_tpl->tpl_vars['product']->value['id_shop'],$_smarty_tpl->tpl_vars['product']->value['id_product_attribute'],false,false,true), ENT_QUOTES, 'UTF-8', true);?>
">
        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['name'], ENT_QUOTES, 'UTF-8', true);?>

        </a>
       
        </p>
<?php if ($_smarty_tpl->tpl_vars['product']->value['reference']) {?>
            <small  align="right">
            <?php echo smartyTranslate(array('s'=>'SKU'),$_smarty_tpl);?>
  
            <?php echo Smarty::$_smarty_vars['capture']['default'];?>

            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['reference'], ENT_QUOTES, 'UTF-8', true);?>

            </small>
            <small  align="right">
           מספר תשלומים : <?php echo $_smarty_tpl->tpl_vars['nb_payments']->value;?>
		
של <?php echo $_smarty_tpl->tpl_vars['total_price']->value/$_smarty_tpl->tpl_vars['nb_payments']->value;?>
 ש.ח.
            </small>
        <small  align="right">
          
          עמלה פייפל :
של <?php echo $_smarty_tpl->tpl_vars['total_price']->value*3.4/100+1.2;?>
 ש.ח.
            </small> 
              <small  align="right">
          
          עמלה עברה בנקאית :
של <?php echo smartyTranslate(array('s'=>' 1.35 '),$_smarty_tpl);?>
 ש.ח.
            </small>   
      <strong>      
      <small  align="right">
    
          סכום  לעברה  :
<?php echo $_smarty_tpl->tpl_vars['total_price']->value*0.966-1.2-1.35;?>
 ש.ח.

     </small>   
     </strong>             
<?php }?>
	</td>
<?php if (!isset($_smarty_tpl->tpl_vars['noDeleteButton']->value)||!$_smarty_tpl->tpl_vars['noDeleteButton']->value) {?>
		<td class="cart_delete text-center" data-title="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
">
      
		<?php if ((!isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])||$_smarty_tpl->tpl_vars['quantityDisplayed']->value>0)&&empty($_smarty_tpl->tpl_vars['product']->value['gift'])) {?>
			<div>
				<a rel="nofollow" title="<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
" class="cart_quantity_delete" id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_product_attribute'];?>
_<?php if ($_smarty_tpl->tpl_vars['quantityDisplayed']->value>0) {?>nocustom<?php } else { ?>0<?php }?>_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
" href="<?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
<?php $_tmp9=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']);?>
<?php $_tmp10=ob_get_clean();?><?php ob_start();?><?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_address_delivery']);?>
<?php $_tmp11=ob_get_clean();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('cart',true,null,"delete=1&amp;id_product=".$_tmp9."&amp;ipa=".$_tmp10."&amp;id_address_delivery=".$_tmp11."&amp;token=".((string)$_smarty_tpl->tpl_vars['token_cart']->value)), ENT_QUOTES, 'UTF-8', true);?>
"><i class="icon-trash"></i></a>
			</div>
		<?php } else { ?>

		<?php }?>
		</td>
	<?php }?>
<?php if ($_smarty_tpl->tpl_vars['bank_name']->value) {?>
	<td class="text-center" data-title="<?php echo smartyTranslate(array('s'=>'Bank account'),$_smarty_tpl);?>
">
	  <?php echo smartyTranslate(array('s'=>' בנק '),$_smarty_tpl);?>
    <?php echo $_smarty_tpl->tpl_vars['bank_name']->value;?>
   <?php echo smartyTranslate(array('s'=>'מספר בנק  '),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['bank_number']->value;?>
   <?php echo smartyTranslate(array('s'=>' סניף '),$_smarty_tpl);?>
  <?php echo $_smarty_tpl->tpl_vars['sucursale']->value;?>
 <br/> <?php echo smartyTranslate(array('s'=>'   חשבון '),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['account_number']->value;?>
  
		    <br/>   <?php echo smartyTranslate(array('s'=>' כתובת:  '),$_smarty_tpl);?>
 		 <?php echo $_smarty_tpl->tpl_vars['bank_address1']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['bank_postcode']->value;?>
  <?php echo $_smarty_tpl->tpl_vars['bank_CityName']->value;?>
 
    <br/>    <?php echo smartyTranslate(array('s'=>' טל :'),$_smarty_tpl);?>
              <?php echo $_smarty_tpl->tpl_vars['bank_phone']->value;?>
 
                   <br/>      <?php echo smartyTranslate(array('s'=>'    תעודת זאות    '),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['identity_number']->value;?>


      <img src="<?php echo $_smarty_tpl->tpl_vars['identity_img_real_name']->value;?>
"  alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['identity_img_name']->value, ENT_QUOTES, 'UTF-8', true);?>
" width="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['width'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['smallSize']->value['height'];?>
"  />
    </td>
<?php }?>
</tr>
<?php }} ?>
