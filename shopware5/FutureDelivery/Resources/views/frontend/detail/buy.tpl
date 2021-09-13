{extends file="parent:frontend/detail/buy.tpl"}

{* Quantity selection *}
{block name='frontend_detail_buy_quantity'}

{if (isset($sArticle.delivery_date_2) AND !empty($sArticle.delivery_date_2)) OR (isset($sArticle.delivery_date_3) AND !empty($sArticle.delivery_date_3))}
	<div class="isf-quantity-label-wrapper" >
	{if !empty($sArticle.delivery_date_2) OR !empty($sArticle.delivery_date_3)}
		<div class="isf-quantity-label">{s namespace='frontend/index/index' name='VmCheckoutProductPerSofort'}Per sofort{/s}<i class="icon--info2" aria-label="{s name='Die1'}Die 1. Lieferung erfolgt sofort, Bezahlung per Rechnung.{/s}" style="display:block;"></i>  </div>
	{/if}
		{if !empty($sArticle.delivery_date_2)}<div class="isf-quantity-label">{$sArticle.delivery_date_2}  <i class="icon--info2" aria-label="{s name='der2'}Zeitraum der 2. Lieferung, Bezahlung per Rechnung nach Erhalt.{/s}" style="display:block;"></i> </div>{/if}
		{if !empty($sArticle.delivery_date_3)}<div class="isf-quantity-label">{$sArticle.delivery_date_3}  <i class="icon--info2" aria-label="{s name='der3'}Zeitraum der 3. Lieferung, Bezahlung per Rechnung nach Erhalt.{/s}" style="display:block;"></i>  </div>{/if}
	</div>
{/if}

	{$smarty.block.parent}
{/block}


{block name='frontend_detail_buy_quantity_select'}
    {$maxQuantity=$sArticle.maxpurchase+1}
    {if $sArticle.laststock && $sArticle.instock < $sArticle.maxpurchase}
        {$maxQuantity=$sArticle.instock+1}
    {/if}

    {if ($sArticle.attr22 eq '6er Holzkiste') or
    ($sArticle.attr22 eq '12er Holzkiste') or
    ($sArticle.attr22 eq 'caisse 6 bts.') or
    ($sArticle.attr22 eq 'caisse 12 bts.')
    }
        {$containerMaterial = 'true'}

    {else}
        {$containerMaterial = 'false'}
    {/if}

    {$isWhiskey = false}
    {if $sArticle.is_article_spirit eq '1'}
        {$isWhiskey = true}
    {/if}
    {*Original quantity box start*}
		<input class="vm-listing-quantity qty1" type="text" name="sQuantity" min="{$sArticle.minpurchase}" max="{$maxQuantity}"
			   value="{if $sArticle.attr23 > 0 and !$isWhiskey}{$sArticle.attr23}{else}1{/if}">

		<div class="vm-box-product-quantity isf-quantity-box {if $sArticle.attr38 eq '1'} vm-is-container-only{/if}{if $sArticle.attr22} vm-is-container-or-bottle{/if} vm-box-is-wood-{$containerMaterial}"
				{if $sArticle.attr23 > 0 and !$isWhiskey} data-container-size="{$sArticle.attr23}"{/if}
			 data-container-is-wood="{$containerMaterial}">
			<div class="vm-max-order-value" style="display: none">
				{$sArticle.maxpurchase}
			</div>
			<div class="vm-box-product-quantity-options" style="left:-200px;" id="vm-box-product-quantity-options">
				<div id="vm-box-product-quantity-options-wrapper">
				</div>
			</div>
            <span id="qty1" onclick="ToggleQuantityOptionsVisibility(false,''), $(this)">{if $sArticle.attr23 > 0 and !$isWhiskey}{$sArticle.attr23}{else}1{/if}</span>
			<!--<span onclick="ToggleQuantityOptionsVisibility(false,''), $(this)">0</span>-->
		</div>
	{*Original quantity box end*}
	
	{if isset($sArticle.delivery_date_2) AND !empty($sArticle.delivery_date_2)}
		<input class="" id="sQuantity2" type="hidden" name="sQuantity2" min="{$sArticle.minpurchase}" max="{$maxQuantity}"
			   value="0">

		<div class="vm-box-product-quantity isf-quantity-box futureDeliveryBox {if $sArticle.attr38 eq '1'} vm-is-container-only{/if}{if $sArticle.attr22} vm-is-container-or-bottle{/if} vm-box-is-wood-{$containerMaterial}"
				{if $sArticle.attr23 > 0 and !$isWhiskey} data-container-size="{$sArticle.attr23}"{/if}
			 data-container-is-wood="{$containerMaterial}">
			<div class="vm-max-order-value" style="display: none">
				{$sArticle.maxpurchase}
			</div>
			<div class="vm-box-product-quantity-options"  style="left:-200px;" id="vm-box-product-quantity-options">
				<div id="vm-box-product-quantity-options-wrapper">
				</div>
			</div>
			<span id="sQuantity2Span" onclick="ToggleQuantityOptionsVisibility(false,''), $(this)">0</span>
			
		</div>
	{/if}
	
	{if isset($sArticle.delivery_date_3) AND !empty($sArticle.delivery_date_3)}
	<input   id="sQuantity3" type="hidden" name="sQuantity3" min="{$sArticle.minpurchase}" max="{$maxQuantity}"
		   value="0">

	<div class="vm-box-product-quantity isf-quantity-box futureDeliveryBox {if $sArticle.attr38 eq '1'} vm-is-container-only{/if}{if $sArticle.attr22} vm-is-container-or-bottle{/if} vm-box-is-wood-{$containerMaterial}"
			{if $sArticle.attr23 > 0 and !$isWhiskey} data-container-size="{$sArticle.attr23}"{/if}
		 data-container-is-wood="{$containerMaterial}">
		<div class="vm-max-order-value" style="display: none">
			{$sArticle.maxpurchase}
		</div>
		<div class="vm-box-product-quantity-options" style="left:-190px;" id="vm-box-product-quantity-options">
			<div id="vm-box-product-quantity-options-wrapper">
			</div>
		</div>
		<span id="sQuantity3Span" onclick="ToggleQuantityOptionsVisibility(false,''), $(this)">0</span>
		
	</div>
	{/if}
	
{/block}


{* "Buy now" button *}
{block name="frontend_detail_buy_button"}
<div class="isf_buy_button_container">
	{$smarty.block.parent}
</div>
{/block}