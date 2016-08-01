<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:26:31
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/productButtons.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2818547955783c8b7031714-10513293%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5726396b5045ed4596e4295a6ff4a8146c50607f' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/productButtons.tpl',
      1 => 1466453781,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2818547955783c8b7031714-10513293',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'bestkitPath' => 0,
    'Subscription' => 0,
    'Period_type' => 0,
    'periods' => 0,
    'period' => 0,
    'text_start_date' => 0,
    'Start_date' => 0,
    'paypalProduct' => 0,
    'subscribe' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783c8b70604a9_33673594',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783c8b70604a9_33673594')) {function content_5783c8b70604a9_33673594($_smarty_tpl) {?>

<div class="bestkit_paypal">
	<input type="hidden" name="bestkit_psubscription" value="1" />
	<h4><img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bestkitPath']->value, ENT_QUOTES, 'UTF-8', true);?>
logo.png"/> <?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['Subscription']->value;?>
<?php $_tmp1=ob_get_clean();?><?php echo smartyTranslate(array('s'=>$_tmp1,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</h4>
	<div class="inner">
		<label><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['Period_type']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</label>
		<select class="form-control no-print" id="paypal_period_selection" name="period">
		<?php  $_smarty_tpl->tpl_vars['period'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['period']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['periods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['period']->key => $_smarty_tpl->tpl_vars['period']->value) {
$_smarty_tpl->tpl_vars['period']->_loop = true;
?>
			<option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['period']->value->id, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['period']->value->title, ENT_QUOTES, 'UTF-8', true);?>
</option>
		<?php } ?>
		</select>
		<br/>
		<label><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['text_start_date']->value;?>
<?php $_tmp2=ob_get_clean();?><?php echo smartyTranslate(array('s'=>$_tmp2,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</label>
	
	
<input type="text" readonly="readonly" name="start_date" value= <?php echo $_smarty_tpl->tpl_vars['Start_date']->value;?>
  style="border: 1px solid rgb(189, 194, 201);">
	</div>
</div>
<script>
	var bestkitAllowedDays = [];
	var bestkitSubscriptionOnly = <?php echo intval($_smarty_tpl->tpl_vars['paypalProduct']->value->subscription_only);?>
;
	var bestkitSubscribeTranslate = '<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['subscribe']->value;?>
<?php $_tmp3=ob_get_clean();?><?php echo smartyTranslate(array('s'=>$_tmp3,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
';
	<?php  $_smarty_tpl->tpl_vars['period'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['period']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['periods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['period']->key => $_smarty_tpl->tpl_vars['period']->value) {
$_smarty_tpl->tpl_vars['period']->_loop = true;
?>
		bestkitAllowedDays[<?php echo $_smarty_tpl->tpl_vars['period']->value->id;?>
] = <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['period']->value->getAllowedStartDays());?>
;
	<?php } ?>
</script><?php }} ?>
