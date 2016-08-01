<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:23:55
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/adminKO/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3739388865783c81b27cc98-81287082%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '884ba61986e57b40dd7cb932aa757e64d4e5be11' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/adminKO/themes/default/template/content.tpl',
      1 => 1454833574,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3739388865783c81b27cc98-81287082',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783c81b283b33_86065864',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783c81b283b33_86065864')) {function content_5783c81b283b33_86065864($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
