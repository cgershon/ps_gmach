<?php /*%%SmartyHeaderCode:7428682665783c8b6e4f2d2-85589043%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bf7d1cd81f12a1e66d50fa857d50744ba8fd4cdf' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/modules/blocksearch/blocksearch-top.tpl',
      1 => 1464810609,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7428682665783c8b6e4f2d2-85589043',
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57845823a79119_48193750',
  'has_nocache_code' => false,
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57845823a79119_48193750')) {function content_57845823a79119_48193750($_smarty_tpl) {?><div id="search_block_top" class="col-sm-4 clearfix"><form id="searchbox" method="get" action="//gmach.konim.biz/חפש" > <input type="hidden" name="controller" value="search" /> <input type="hidden" name="orderby" value="position" /> <input type="hidden" name="orderway" value="desc" /> <input class="search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="חפש" value="" /> <button type="submit" name="submit_search" class="btn btn-default button-search"> <span>חפש</span> </button></form></div><?php }} ?>
