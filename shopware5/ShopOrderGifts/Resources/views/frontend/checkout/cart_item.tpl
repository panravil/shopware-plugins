{extends file="parent:frontend/checkout/cart_item.tpl"}


{block name='frontend_checkout_cart_item_additional_type' append}
	{$IS_GESCHENK_PRODUCT = 5}
	{if $sBasketItem.modus == $IS_GESCHENK_PRODUCT}
	    {* Chosen Gift products *}
		{include file="frontend/checkout/items/order-gift-product.tpl" isLast=$isLast}
	{/if}
{/block}
