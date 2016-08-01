<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:34:28
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/order-payment-classic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2426716545783ca9445a3e9-08658667%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b2a0d11422afdcdbcd49f80e623732f00cc11ee7' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/order-payment-classic.tpl',
      1 => 1468152942,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2426716545783ca9445a3e9-08658667',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'HOOK_TOP_PAYMENT' => 0,
    'HOOK_PAYMENT' => 0,
    'opc' => 0,
    'bank_name' => 0,
    'total_price' => 0,
    'products' => 0,
    'product' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783ca944f0712_39308951',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783ca944f0712_39308951')) {function content_5783ca944f0712_39308951($_smarty_tpl) {?>
<div class="paiement_block" align="center">
    <div id="HOOK_TOP_PAYMENT" align="center"><?php echo $_smarty_tpl->tpl_vars['HOOK_TOP_PAYMENT']->value;?>
</div>
  <?php if ($_smarty_tpl->tpl_vars['HOOK_PAYMENT']->value) {?>
        <?php if (!$_smarty_tpl->tpl_vars['opc']->value) {?>
            <div id="order-detail-content" class="table_block table-responsive" align="center">
                <table id="cart_summary" class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="cart_product first_item"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</th>
                        <th class="cart_description item text-right"><?php echo smartyTranslate(array('s'=>'Description'),$_smarty_tpl);?>
</th>
                       	 <?php if ($_smarty_tpl->tpl_vars['bank_name']->value) {?>                
                      		 <th class="cart_total last_item text-right"><?php echo smartyTranslate(array('s'=>'חשבון בנק לעברה'),$_smarty_tpl);?>
</th>
                      	 <?php }?>
                      
                    </tr>
                    </thead>
                    <tfoot>
               
      <!--  TOTAL   -->                       
                        <tr class="cart_total_price">
                            <td colspan="4" class="total_price_container text-right"><span><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</span></td>
                            <td colspan="2" class="price" id="total_price_container">
                                <span id="total_price" data-selenium-total-price="<?php echo $_smarty_tpl->tpl_vars['total_price']->value;?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_price']->value),$_smarty_tpl);?>
</span>
                            </td>
                        </tr>
                   </tfoot>

                    <tbody>
                    <?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['product']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['product']->iteration++;
?>
                        <?php $_smarty_tpl->tpl_vars['productId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product'], null, 0);?>
                        <?php $_smarty_tpl->tpl_vars['productAttributeId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['id_product_attribute'], null, 0);?>
                        <?php $_smarty_tpl->tpl_vars['quantityDisplayed'] = new Smarty_variable(0, null, 0);?>
                        <?php $_smarty_tpl->tpl_vars['cannotModify'] = new Smarty_variable(0, null, 0);?>
                        <?php $_smarty_tpl->tpl_vars['odd'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->iteration%2, null, 0);?>
                        <?php $_smarty_tpl->tpl_vars['noDeleteButton'] = new Smarty_variable(1, null, 0);?>

                        
                        <?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./shopping-cart-product-line.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

                      
                    <?php } ?>
                    <?php $_smarty_tpl->tpl_vars['last_was_odd'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->iteration%2, null, 0);?>
                      </tbody>
               </table>
         <div id="HOOK_PAYMENT" > 
			 <table align="right" >
             <tr ><td  width="800"><?php echo $_smarty_tpl->tpl_vars['HOOK_PAYMENT']->value;?>
</td>
             </tr>
             </table> 
    	</div>
                                  
            </div> <!-- end order-detail-content -->
        <?php }?>
      
   <?php } else { ?>
        <p class="alert alert-warning"><?php echo smartyTranslate(array('s'=>'No payment modules have been installed.'),$_smarty_tpl);?>
</p>
   <?php }?>
    
 	 <?php if (!$_smarty_tpl->tpl_vars['opc']->value) {?>
        <p class="cart_navigation clearfix">
            <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true,null,"step=2"), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Previous'),$_smarty_tpl);?>
" class="button-exclusive btn btn-default">
                <i class="icon-chevron-left"></i>
                <?php echo smartyTranslate(array('s'=>'Continue shopping'),$_smarty_tpl);?>

            </a>
        </p>
      
		</div> <!-- end opc_payment_methods -->
	<?php }?>
<!-- end HOOK_TOP_PAYMENT -->
<?php }} ?>
