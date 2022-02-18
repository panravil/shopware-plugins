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
		{$currentPrice = floatval(str_replace(',', '.', str_replace('.', '', $sBasket.content[count($sBasket.content) - 1].price)))}
		{$moreCount = 1}

		{if $sArticle.sBlockPrices}
			{$totalDifference = $sShippingcostsDifference + $currentPrice * $sBasket.content[count($sBasket.content) - 1].quantity}
			{$loopFlag = 0}
			{foreach $sArticle.sBlockPrices as $blockPrice}
				{if ($blockPrice.price == $currentPrice)}
					{$loopFlag = 1}
				{/if}

				{if $loopFlag == 1}
					{if !$blockPrice.to}
						{$moreCount = intval($totalDifference / $blockPrice.price) + 1 - $sBasket.content[count($sBasket.content) - 1].quantity}
						{$loopFlag = 0}
					{else}
						{if intval($totalDifference / $blockPrice.price) + 1 <= $blockPrice.to}
							{$moreCount = intval($totalDifference / $blockPrice.price) + 1 - $sBasket.content[count($sBasket.content) - 1].quantity}
							{$loopFlag = 0}
						{/if}
					{/if}
				{/if}
			{/foreach}
		{else}
			{$moreCount = intval($sShippingcostsDifference / $currentPrice) + 1}
		{/if}

		{if $moreCount > 1}
			{$shippingDifferenceContent="{s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceHeader'}{/s} {$moreCount} {s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferencePlural'}{/s}"}
			{include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
		{else}
			{$shippingDifferenceContent="{s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceHeader'}{/s} {$moreCount} {s namespace="frontend/ShopOrderGifts/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceSingular'}{/s}"}
			{include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
		{/if}
	{/if}
{/block}