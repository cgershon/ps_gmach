<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:37:52
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/mails/he/order_conf_product_list.txt" */ ?>
<?php /*%%SmartyHeaderCode:267632065783cb60233705-10637298%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '58273c83e8e2c58bb0df09a6046db4019b16791a' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/mails/he/order_conf_product_list.txt',
      1 => 1467124726,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '267632065783cb60233705-10637298',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'list' => 0,
    'product' => 0,
    'customization' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783cb6038bd89_81128213',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783cb6038bd89_81128213')) {function content_5783cb6038bd89_81128213($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
						<?php echo $_smarty_tpl->tpl_vars['product']->value['reference'];?>


						<?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>


						<?php echo $_smarty_tpl->tpl_vars['product']->value['price'];?>


						<?php echo $_smarty_tpl->tpl_vars['product']->value['quantity'];?>


						<?php echo $_smarty_tpl->tpl_vars['product']->value['price'];?>


	<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['customization']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value) {
$_smarty_tpl->tpl_vars['customization']->_loop = true;
?>
							<?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
 <?php echo $_smarty_tpl->tpl_vars['customization']->value['customization_text'];?>


							<?php echo $_smarty_tpl->tpl_vars['product']->value['price'];?>


							<?php echo $_smarty_tpl->tpl_vars['product']->value['customization_quantity'];?>


							<?php echo $_smarty_tpl->tpl_vars['product']->value['quantity'];?>

	<?php } ?>
<?php } ?><?php }} ?>
