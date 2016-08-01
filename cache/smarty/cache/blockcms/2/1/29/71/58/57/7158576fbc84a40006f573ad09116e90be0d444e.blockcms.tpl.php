<?php /*%%SmartyHeaderCode:3387004125783c8b7221430-87387527%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7158576fbc84a40006f573ad09116e90be0d444e' => 
    array (
      0 => '/var/www/vhosts/konim.biz/ps_gmach/themes/default-bootstrap/modules/blockcms/blockcms.tpl',
      1 => 1467107557,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3387004125783c8b7221430-87387527',
  'variables' => 
  array (
    'block' => 0,
    'cms_titles' => 0,
    'cms_key' => 0,
    'cms_title' => 0,
    'cms_page' => 0,
    'link' => 0,
    'show_price_drop' => 0,
    'PS_CATALOG_MODE' => 0,
    'show_new_products' => 0,
    'show_best_sales' => 0,
    'display_stores_footer' => 0,
    'show_contact' => 0,
    'contact_url' => 0,
    'cmslinks' => 0,
    'cmslink' => 0,
    'show_sitemap' => 0,
    'footer_text' => 0,
    'display_poweredby' => 0,
  ),
  'has_nocache_code' => true,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5783c8b736fd58_51875522',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5783c8b736fd58_51875522')) {function content_5783c8b736fd58_51875522($_smarty_tpl) {?>
	<!-- Block CMS module footer -->
	<section class="footer-block col-xs-12 col-sm-2" id="block_various_links_footer">
		<h4>מידע</h4>
		<ul class="toggle-footer">
																		<li class="item">
				<a href="https://gmach.konim.biz/צור-קשר" title="צור קשר">
					צור קשר
				</a>
			</li>
																										<li>
				<a href="https://gmach.konim.biz/מפת-האתר" title="מפת אתר">
					מפת אתר
				</a>
			</li>
					</ul>
		
	</section>
		<section class="bottom-footer col-xs-12">
		<div>
			<?php echo smartyTranslate(array('s'=>'[1] %3$s %2$s - Software by %1$s [/1]','mod'=>'blockcms','sprintf'=>array('YGPC™',date('Y'),'©'),'tags'=>array('<a class="_blank" href="http://www.konim.biz">')),$_smarty_tpl);?>

		</div>
	</section>
		<!-- /Block CMS module footer -->
<?php }} ?>
