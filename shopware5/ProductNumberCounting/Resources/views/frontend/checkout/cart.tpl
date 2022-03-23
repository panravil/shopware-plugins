{extends file="parent:frontend/checkout/cart.tpl"}
{block name='frontend_checkout_cart_deliveryfree'}
	{if $sShippingcostsDifference}
        {$basketCnt = 0}
		{$index = 0}
        {foreach $sBasket.content as $basketContent}
			{$basketCnt = $basketCnt + $sBasket.content[$index++].quantity}
        {/foreach}
		{if ($freeShippingLimit - $basketCnt) > 1}
			{$shippingDifferenceContent="{s namespace="frontend/ProductNumberCounting/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceHeader'}{/s} {$sShippingcostsDifference|currency} {s namespace="frontend/ProductNumberCounting/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceContent'}{/s} {$freeShippingLimit - $basketCnt} {s namespace="frontend/ProductNumberCounting/ShippingFreeNotification" name='CartInfoFreeShippingDifferencePlural'}{/s}"}
			{include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
		{else}
			{$shippingDifferenceContent="{s namespace="frontend/ProductNumberCounting/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceHeader'}{/s} {$sShippingcostsDifference|currency} {s namespace="frontend/ProductNumberCounting/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceContent'}{/s} {$freeShippingLimit - $basketCnt} {s namespace="frontend/ProductNumberCounting/ShippingFreeNotification" name='CartInfoFreeShippingDifferenceSingular'}{/s}"}
			{include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
		{/if}
	{/if}
{/block}