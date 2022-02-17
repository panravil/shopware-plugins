{extends file="parent:frontend/checkout/cart.tpl"}

{block name='frontend_checkout_cart_panel' append}
		{foreach $sGifts as $sGiftArticles}
			{if $sGiftArticles.sGiftArticles|count > 1}
				{include file='frontend/checkout/order_gift.tpl' sArticles=$sGiftArticles.sGiftArticles}
			{/if}
	    {/foreach}
{/block}

{block name='frontend_checkout_cart_deliveryfree'}
	{if $sShippingcostsDifference}
		{if (intval($sShippingcostsDifference / $sBasket.content[count($sBasket.content) - 1].price) + 1) > 1}
			{$shippingDifferenceContent="{s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceHeader'}{/s} {intval($sShippingcostsDifference / $sBasket.content[count($sBasket.content) - 1].price) + 1} {s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferencePlural'}{/s}"}
			{include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
		{else}
			{$shippingDifferenceContent="{s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceHeader'}{/s} {intval($sShippingcostsDifference / $sBasket.content[count($sBasket.content) - 1].price) + 1} {s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceSingular'}{/s}"}
			{include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
		{/if}
	{/if}
{/block}