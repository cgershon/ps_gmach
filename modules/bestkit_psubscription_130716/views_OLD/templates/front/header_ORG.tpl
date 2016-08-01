<script>
	var bestkitController = "{$link->getModuleLink('bestkit_psubscription', 'front', [])|escape:false}";
	{if $id_cms_of_terms}
		var bestkitTermsUrl = "{$link->getCMSLink($id_cms_of_terms)|escape:false}";
		if (bestkitTermsUrl.indexOf('?') == -1) {
			bestkitTermsUrl += '?content_only=1';
		} else {
			bestkitTermsUrl += '&content_only=1';
		}
	{else}
		var bestkitTermsUrl = 0;
	{/if}

	if (bestkitController.indexOf('?') == -1) {
		bestkitController += '?';
	} else {
		bestkitController += '&';
	}

	var bestkitTranslator = {
		subscription: '{l s='Subscription ' mod='bestkit_psubscription' js=1}',
		from: '{l s=' from ' mod='bestkit_psubscription' js=1}',
		subscribe: '{l s=$subscribe mod='bestkit_psubscription' js=1}',
	};

	var bestkitSubscriptionCount = {$subscription_count|intval};
</script>
