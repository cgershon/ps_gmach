<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:23:55
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/adminKO/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21279671415783c81b393ea6-57320219%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '37d2fec3d0d232de527759b6a614eee6da8dacf9' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/adminKO/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1454833574,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21279671415783c81b393ea6-57320219',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783c81b396d03_36827182',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783c81b396d03_36827182')) {function content_5783c81b396d03_36827182($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><?php echo smartyTranslate(array('s'=>'Recommended Modules and Services'),$_smarty_tpl);?>
</h3>
			</div>
			<div class="modal-body">
				<div id="modules_list_container_tab_modal" style="display:none;"></div>
				<div id="modules_list_loader"><i class="icon-refresh icon-spin"></i></div>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
