<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:37:30
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/payment_execution.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4651552655783cb4a29ad58-73610108%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7083c1be421f379cad5327fe9a1d731552ed8669' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/payment_execution.tpl',
      1 => 1468155774,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4651552655783cb4a29ad58-73610108',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'PayPal_Subscription' => 0,
    'Order_summary' => 0,
    'nbProducts' => 0,
    'link' => 0,
    'choice_of_paypal' => 0,
    'short_summary' => 0,
    'The_total_amount' => 0,
    'total' => 0,
    'use_taxes' => 0,
    'will_pay_in' => 0,
    'You_will_pay_with' => 0,
    'nb_payments' => 0,
    'payments' => 0,
    'sub_total' => 0,
    'be_redirected_to_PayPal' => 0,
    'Please_confirm' => 0,
    'I_confirm_my_order' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783cb4a386fa3_63428075',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783cb4a386fa3_63428075')) {function content_5783cb4a386fa3_63428075($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/konim.biz/ps_gmach/tools/smarty/plugins/modifier.escape.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['PayPal_Subscription']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<h1 class="page-heading"><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['Order_summary']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</h1>

<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('payment', null, 0);?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>


<?php if (isset($_smarty_tpl->tpl_vars['nbProducts']->value)&&$_smarty_tpl->tpl_vars['nbProducts']->value<=0) {?>
	<p class="alert alert-warning"><?php echo smartyTranslate(array('s'=>'Your shopping cart is empty.','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</p>
<?php } else { ?>
	<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('bestkit_psubscription','front',array('action'=>'go'),true), ENT_QUOTES, 'UTF-8', true);?>
" method="post">
		<div class="box bestkit_psubscription-box">
			<h3 class="page-subheading"><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['PayPal_Subscription']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</h3>
			<p class="bestkit_psubscription-indent">
				<strong class="dark">
					<?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['choice_of_paypal']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['short_summary']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>

				</strong>
			</p>
			<p>
				- <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['The_total_amount']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>

				<span id="amount" class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total']->value),$_smarty_tpl);?>
</span>
				<?php if ($_smarty_tpl->tpl_vars['use_taxes']->value==1) {?>
					<?php echo smartyTranslate(array('s'=>'','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>

				<?php }?>
			</p>
			<p>
				-
				<?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['will_pay_in']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
&nbsp;
				<input type="hidden" name="currency_payement" value="<?php echo smarty_modifier_escape(Context::getContext()->currency->id, false);?>
" />
			</p>
            <p><strong class="dark">
				- <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['You_will_pay_with']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['nb_payments']->value),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['payments']->value),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['sub_total']->value),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'ש.ח.'),$_smarty_tpl);?>
 
				<br />
			</strong>
			</p>
			<p>
				- <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['be_redirected_to_PayPal']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>

				<br />
				- <?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['Please_confirm']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
.
			</p>
		</div>
		<p class="cart_navigation clearfix" id="cart_navigation">
		
			<button type="submit" class="button btn btn-default button-medium">
				<span><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['I_confirm_my_order']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
<i class="icon-chevron-right right"></i></span>
			</button>
		</p>
	</form>
<?php }?>
<?php }} ?>
