<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:26:30
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20720827225783c8b68c7237-01182480%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2547486c84c0e5119c72ccd4095b97a8caaaf067' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/header.tpl',
      1 => 1463480081,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20720827225783c8b68c7237-01182480',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'id_cms_of_terms' => 0,
    'subscribe' => 0,
    'subscription_count' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783c8b6963dc7_14319184',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783c8b6963dc7_14319184')) {function content_5783c8b6963dc7_14319184($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/konim.biz/ps_gmach/tools/smarty/plugins/modifier.escape.php';
?><script>
	var bestkitController = "<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getModuleLink('bestkit_psubscription','front',array()), false);?>
";
	<?php if ($_smarty_tpl->tpl_vars['id_cms_of_terms']->value) {?>
		var bestkitTermsUrl = "<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['link']->value->getCMSLink($_smarty_tpl->tpl_vars['id_cms_of_terms']->value), false);?>
";
		if (bestkitTermsUrl.indexOf('?') == -1) {
			bestkitTermsUrl += '?content_only=1';
		} else {
			bestkitTermsUrl += '&content_only=1';
		}
	<?php } else { ?>
		var bestkitTermsUrl = 0;
	<?php }?>

	if (bestkitController.indexOf('?') == -1) {
		bestkitController += '?';
	} else {
		bestkitController += '&';
	}

	var bestkitTranslator = {
		subscription: '<?php echo smartyTranslate(array('s'=>'Subscription ','mod'=>'bestkit_psubscription','js'=>1),$_smarty_tpl);?>
',
		from: '<?php echo smartyTranslate(array('s'=>' from ','mod'=>'bestkit_psubscription','js'=>1),$_smarty_tpl);?>
',
		subscribe: '<?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['subscribe']->value,'mod'=>'bestkit_psubscription','js'=>1),$_smarty_tpl);?>
',
	}; 

	var bestkitSubscriptionCount = <?php echo intval($_smarty_tpl->tpl_vars['subscription_count']->value);?>
;
</script>
<?php }} ?>
