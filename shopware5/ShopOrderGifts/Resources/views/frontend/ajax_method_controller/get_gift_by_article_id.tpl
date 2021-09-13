	<span class="modal-close" onclick="modal_close()">x</span>
	<h3 class="modal-h1 test" >Bitte wählen Sie Ihre(n) Gratis-Artikel:</h3>

	<h4 class="gift-counter-h4" >noch <span id="gift-counter-start">{$taken_gift}</span> Artikel</h4>
	<input type="hidden"  id="gift_limit_var" value='{$gift_selection_limit}'>

	<section class="giftArticles" >
		{if $article_gift|count > 0 }
			<div class="owl-carousel owl-product-slider">
				{foreach $article_gift as $item}
					<form class="gift-range"  {if $theme.offcanvasCart} data-showModal="false" data-addArticleUrl="{url controller=checkout action=ajaxAddArticleCart}"{/if}>
							<div class="{if $item.quantity.quantity_from < $sArticle.attr23 && !$isWhiskey} is--hidden{/if}">
								<img class="gift-product-img" src="{$item.Image[0]}">
								<input type="hidden" class="isf_qty_from" id="quantity_from-{$item.giftId}" readonly name="quantity_from" value='{$item.quantity.quantity_from}' />
								<input type="hidden" id="quantity_to-{$item.giftId}" readonly name="quantity_to" value='{$item.quantity.quantity_to}' />
								<input type="hidden" id="gift-ID" name="sgift" value='{$item.giftId}'>
								<input type="hidden" id="article-{$item.giftId}-{$item.ordernumber}" name="sAdd" value='{$item.ordernumber}'>
								<input type="hidden"  name="sQuantity" value='1'>
								<input type="hidden"  name="giftParentOrderNumber" value='{$item.giftParentOrderNumber}'>

								<input type="hidden"  name="giftArticle" value='1'>
								<p>{$item.name}</p>

								<br>
								<button class="btn is--secondary modal-isf-select-btn" type="button" onclick="giftLimitChecker(),addArticleAjax(this)" >Auswählen</button>
							</div>
					</form>
				{/foreach}
			</div> 
		{/if}
	</section>
