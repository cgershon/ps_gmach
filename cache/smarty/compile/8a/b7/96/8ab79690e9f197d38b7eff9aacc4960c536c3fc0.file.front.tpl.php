<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:41:07
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/front.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5881940905783cc23db8ae0-42380207%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8ab79690e9f197d38b7eff9aacc4960c536c3fc0' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/front.tpl',
      1 => 1467342728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5881940905783cc23db8ae0-42380207',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783cc23e66d77_10740857',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783cc23e66d77_10740857')) {function content_5783cc23e66d77_10740857($_smarty_tpl) {?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'תשלומים עם פייפל','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<h1 class="page-heading bottom-indent"><?php echo smartyTranslate(array('s'=>'תשלום בוטל','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</h1>
<div class="block-center" id="block-history">
	<p class="alert alert-danger">
		<?php echo smartyTranslate(array('s'=>' אתם ביטלתם ההלוואה'),$_smarty_tpl);?>

	</p>
</div>
<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['base_dir']->value, ENT_QUOTES, 'UTF-8', true);?>
">
			<span><i class="icon-chevron-left"></i> <?php echo smartyTranslate(array('s'=>'המשך לדף הבית','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</span>
		</a>
	</li>
</ul><?php }} ?>
