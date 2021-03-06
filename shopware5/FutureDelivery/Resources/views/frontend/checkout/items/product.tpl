{extends file="parent:frontend/checkout/items/product.tpl"}

{block name='frontend_checkout_cart_item_quantity_selection'}
	{if !$sBasketItem.additional_details.laststock || ($sBasketItem.additional_details.laststock && $sBasketItem.additional_details.instock > 0)}
		<form  {*{if isset($sBasketItem.basket_quantity_2) || isset($sBasketItem.basket_quantity_3)}{if $sBasketItem.basket_quantity_2 gt 0 || $sBasketItem.basket_quantity_3 gt 0} onsubmit="return formSubmitStoper(event)" {/if}{/if}*} id="vmCartQuantity_{$sBasketItem.ordernumber}" name="basket_change_quantity{$sBasketItem.id}" class="select-field isf-select-field" method="post" action="{url action='changeQuantity' sTargetAction=$sTargetAction}">

			{if ($sBasketItem.additional_details.attr22 eq '6er Holzkiste') or 
				($sBasketItem.additional_details.attr22 eq '12er Holzkiste') or
				($sBasketItem.additional_details.attr22 eq 'caisse 6 bts.') or
				($sBasketItem.additional_details.attr22 eq 'caisse 12 bts.')
			}
				{$containerMaterial = 'true'}

			{else}
				{$containerMaterial = 'false'}
			{/if}
			

			{if (isset($sBasketItem.additional_details.delivery_date_2) AND !empty($sBasketItem.additional_details.delivery_date_2)) OR (isset($sBasketItem.additional_details.delivery_date_3) AND !empty($sBasketItem.additional_details.delivery_date_3))}
				<span class="isf-futureDeliveryBox-span one" >{s namespace='frontend/index/index' name='VmCheckoutProductPerSofort'}Per sofort{/s} <i class="icon--info2" aria-label="{s name='Die1'}Die 1. Lieferung erfolgt sofort, Bezahlung per Rechnung.{/s}" ></i></span>
			{/if}

            {$firstvalue = $sBasketItem.quantity}
			{if isset($sBasketItem.basket_quantity_2) && isset($sBasketItem.basket_quantity_3)}
				{*if $sBasketItem.basket_quantity_2 gt 0 || $sBasketItem.basket_quantity_3 gt 0*}
					{math equation="(( x - y ) - z )" x=$sBasketItem.quantity y=$sBasketItem.basket_quantity_2 z=$sBasketItem.basket_quantity_3 }
					{$firstvalue = ($sBasketItem.quantity - $sBasketItem.basket_quantity_2) - $sBasketItem.basket_quantity_3 }
				{*/if*}
			{/if}
			
			
			
			<input class="vm-cart-item-quantity qty1" data-auto-submit="true" type="hidden" name="sQuantity" min="{$sBasketItem.minpurchase}" max="{$sBasketItem.maxpurchase}" value='{$firstvalue}'>

			<div class="vm-box-product-quantity {if $sBasketItem.additional_details.attr38 eq '1'} vm-is-container-only{/if}{if $sBasketItem.additional_details.attr22} vm-is-container-or-bottle{/if}vm-box-is-wood-{$containerMaterial}" {if $sBasketItem.additional_details.attr23 > 0} data-container-size="{$sBasketItem.additional_details.attr23}"{/if} data-container-is-wood="{$containerMaterial}">
                <div class="vm-max-order-value" style="display: none">
                    {$sBasketItem.maxpurchase}
                </div>
                <div class="vm-box-product-quantity-options" id="vm-box-product-quantity-options">
					<div id="vm-box-product-quantity-options-wrapper" data-current-quantity='{if isset($sBasketItem.basket_quantity_2) && isset($sBasketItem.basket_quantity_3)}{if $sBasketItem.basket_quantity_2 gt 0 || $sBasketItem.basket_quantity_3 gt 0}{math equation="(( x - y ) - z )" x=$sBasketItem.quantity y=$sBasketItem.basket_quantity_2 z=$sBasketItem.basket_quantity_3 }{/if}{else}{$sBasketItem.quantity}{/if}'>
					</div>
				</div>
				
				<span class="checkOutSpan" id="qty1" onclick="ToggleQuantityOptionsVisibility(false, '#vmCartQuantity_{$sBasketItem.ordernumber} .vm-box-product-quantity', $(this))">
					{$firstvalue}
				</span>
				
			</div>
			
			
			{if isset($sBasketItem.additional_details.delivery_date_2) AND !empty($sBasketItem.additional_details.delivery_date_2)}			
			<span class="isf-futureDeliveryBox-span two">{$sBasketItem.additional_details.delivery_date_2} <i class="icon--info2" aria-label="{s name='der2'}Zeitraum der 2. Lieferung, Bezahlung per Rechnung nach Erhalt.{/s}" ></i></span>
			
			<input  class="sQuantity2Span qty2" type="hidden" name="sQuantity2" min="{$sBasketItem.minpurchase}" max="{$sBasketItem.maxpurchase}" value='{if isset($sBasketItem.basket_quantity_2)}{$sBasketItem.basket_quantity_2}{else}0{/if}'>

			<div class="vm-box-product-quantity futureDeliveryBox {if $sBasketItem.additional_details.attr38 eq '1'} vm-is-container-only{/if}{if $sBasketItem.additional_details.attr22} vm-is-container-or-bottle{/if}vm-box-is-wood-{$containerMaterial}" {if $sBasketItem.additional_details.attr23 > 0} data-container-size="{$sBasketItem.additional_details.attr23}"{/if} data-container-is-wood="{$containerMaterial}">
                <div class="vm-max-order-value" style="display: none">
                    {$sBasketItem.maxpurchase}
                </div>
                <div class="vm-box-product-quantity-options" id="vm-box-product-quantity-options">
					<div id="vm-box-product-quantity-options-wrapper" data-current-quantity='{if isset($sBasketItem.basket_quantity_2) && isset($sBasketItem.basket_quantity_3)}{if $sBasketItem.basket_quantity_2 gt 0 || $sBasketItem.basket_quantity_3 gt 0}{math equation="(( x - y ) - z )" x=$sBasketItem.quantity y=$sBasketItem.basket_quantity_2 z=$sBasketItem.basket_quantity_3 }{/if}{else}{$sBasketItem.quantity}{/if}'>
					</div>
				</div>
				
				<span  class="sQuantitySpan" id="qty2" onclick="ToggleQuantityOptionsVisibility(false, '#vmCartQuantity_{$sBasketItem.ordernumber} .vm-box-product-quantity', $(this))">
					{if isset($sBasketItem.basket_quantity_2)}{$sBasketItem.basket_quantity_2}{else}0{/if}
				</span>
				
			</div>
			{/if}
			

			{if isset($sBasketItem.additional_details.delivery_date_3) AND !empty($sBasketItem.additional_details.delivery_date_3)}
			<span class="isf-futureDeliveryBox-span three">{$sBasketItem.additional_details.delivery_date_3} <i class="icon--info2" aria-label="{s name="der3"}Zeitraum der 3. Lieferung, Bezahlung per Rechnung nach Erhalt.{/s}" ></i></span>
			
			<input  class="sQuantity3Span qty3" type="hidden" name="sQuantity3" min="{$sBasketItem.minpurchase}" max="{$sBasketItem.maxpurchase}" value='{if isset($sBasketItem.basket_quantity_3)}{$sBasketItem.basket_quantity_3}{else}0{/if}'>

			<div class="vm-box-product-quantity futureDeliveryBox {if $sBasketItem.additional_details.attr38 eq '1'} vm-is-container-only{/if}{if $sBasketItem.additional_details.attr22} vm-is-container-or-bottle{/if}vm-box-is-wood-{$containerMaterial}" {if $sBasketItem.additional_details.attr23 > 0} data-container-size="{$sBasketItem.additional_details.attr23}"{/if} data-container-is-wood="{$containerMaterial}">
                <div class="vm-max-order-value" style="display: none">
                    {$sBasketItem.maxpurchase}
                </div>
                <div class="vm-box-product-quantity-options" id="vm-box-product-quantity-options">
					<div id="vm-box-product-quantity-options-wrapper" data-current-quantity='{if isset($sBasketItem.basket_quantity_2) && isset($sBasketItem.basket_quantity_3)}{if $sBasketItem.basket_quantity_2 gt 0 || $sBasketItem.basket_quantity_3 gt 0}{math equation="(( x - y ) - z )" x=$sBasketItem.quantity y=$sBasketItem.basket_quantity_2 z=$sBasketItem.basket_quantity_3 }{/if}{else}{$sBasketItem.quantity}{/if}'>
					</div>
				</div>
				
				<span  class="sQuantitySpan" id="qty3" onclick="ToggleQuantityOptionsVisibility(false, '#vmCartQuantity_{$sBasketItem.ordernumber} .vm-box-product-quantity', $(this))" >
					{if isset($sBasketItem.basket_quantity_3)}{$sBasketItem.basket_quantity_3}{else}0{/if}
				</span>
				
			</div>
			{/if}
			
			<input type="hidden" name="sArticle" value="{$sBasketItem.id}" />
			
		</form>
	{else}
		{s name="CartColumnQuantityEmpty" namespace="frontend/checkout/cart_item"}{/s}
	{/if}
{/block}

