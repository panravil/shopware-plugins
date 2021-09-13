{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_articlename_price'}

	{if isset($basketItem.basket_quantity_2) && isset($basketItem.basket_quantity_3)}

		<ol style="list-style: none; padding: 0" class="isf-quantity-span-ajaxcart-container">
			{if $basketItem.basket_quantity_2 gt 0 || $basketItem.basket_quantity_3 gt 0}
				<li class="isf-quantity-span-ajaxcart"> {s namespace='frontend/index/index' name='VmAjaxCartLieferung'}1. Lieferung:{/s} {math equation="(( x - y ) - z )" x=$basketItem.quantity y=$basketItem.basket_quantity_2 z=$basketItem.basket_quantity_3 } {s namespace='frontend/index/index' name='VmAjaxCartFl'} Fl.{/s}</li>
			{/if}
			{*if $basketItem.basket_quantity_2 gt 0*}
				<li class="isf-quantity-span-ajaxcart"> {s namespace='frontend/index/index' name='VmAjaxCartLieferung2'}2. Lieferung:{/s} {$basketItem.basket_quantity_2} {s namespace='frontend/index/index' name='VmAjaxCartFl'} Fl.{/s}</li>
			{*/if*}
			
			{*if $basketItem.basket_quantity_3 gt 0*}
				<li class="isf-quantity-span-ajaxcart"> {s namespace='frontend/index/index' name='VmAjaxCartLieferung3'}3. Lieferung:{/s} {$basketItem.basket_quantity_3}  {s namespace='frontend/index/index' name='VmAjaxCartFl'} Fl.{/s}</li>
			{*/if*}
			
		</ol>
		
	{/if}
	
	{$smarty.block.parent}
{/block} 