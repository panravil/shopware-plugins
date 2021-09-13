{extends file="parent:frontend/checkout/cart.tpl"}

{block name='frontend_checkout_cart_panel' append}
		{foreach $sGifts as $sGiftArticles}
			{if $sGiftArticles.sGiftArticles|count > 1}
				{include file='frontend/checkout/order_gift.tpl' sArticles=$sGiftArticles.sGiftArticles}
			{/if}
	    {/foreach}
{/block}
