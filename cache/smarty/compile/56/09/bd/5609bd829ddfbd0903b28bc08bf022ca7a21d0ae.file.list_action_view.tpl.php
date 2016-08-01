<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:45:00
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/adminKO/themes/default/template/helpers/list/list_action_view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20876645445783cd0c8f89a2-44264172%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5609bd829ddfbd0903b28bc08bf022ca7a21d0ae' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/adminKO/themes/default/template/helpers/list/list_action_view.tpl',
      1 => 1454833574,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20876645445783cd0c8f89a2-44264172',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783cd0c9182e5_27332030',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783cd0c9182e5_27332030')) {function content_5783cd0c9182e5_27332030($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
	<i class="icon-search-plus"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
