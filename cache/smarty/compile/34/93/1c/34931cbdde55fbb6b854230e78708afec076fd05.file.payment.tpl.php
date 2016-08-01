<?php /* Smarty version Smarty-3.1.19, created on 2016-07-11 19:34:28
         compiled from "/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6606078825783ca9418ab29-05460254%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '34931cbdde55fbb6b854230e78708afec076fd05' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/modules/bestkit_psubscription/views/templates/front/payment.tpl',
      1 => 1468235673,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6606078825783ca9418ab29-05460254',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'this_path' => 0,
    'payments' => 0,
    'totalCartCount' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783ca942cf398_94899521',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783ca942cf398_94899521')) {function content_5783ca942cf398_94899521($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/konim.biz/ps_gmach/tools/smarty/plugins/modifier.escape.php';
?>

<div class="row bestkit_psubscription_payment" align="center">
	<div class="col-xs-12 col-md-6">
		<p class="payment_module">
			<a id="bestkit_psubscription_button" class="bankwire paypal-subscription" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('bestkit_psubscription','front',array('action'=>'payment'),true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'PayPal בתשלומים','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
">
				<img src="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['this_path']->value, false);?>
img/paypal-payment.png" alt="<?php echo smartyTranslate(array('s'=>'PayPal בתשלומים','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
" width="86" height="49" />
				<?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['payments']->value,'mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
 <span><?php echo smartyTranslate(array('s'=>'','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
</span>
			</a>
		</p>
	</div>
</div>
<script>
	(function($){
		<?php if ($_smarty_tpl->tpl_vars['totalCartCount']->value>1) {?>
			$('#bestkit_psubscription_button').click(function(){
				alert('<?php echo smartyTranslate(array('s'=>'Your cart must exists only one PayPal subscription!','mod'=>'bestkit_psubscription'),$_smarty_tpl);?>
');
				return false;
			});
		<?php }?>

		
		$('#HOOK_PAYMENT .row:not(.bestkit_psubscription_payment)').hide();
		setInterval(function(){
			if (only_subscription) {
				$('#HOOK_PAYMENT .row:not(.bestkit_psubscription_payment)').hide();
			} else {
				$('#HOOK_PAYMENT .row:not(.bestkit_psubscription_payment)').show();
			}
		}, 1000);
	})(jQuery);
</script>
<?php }} ?>
