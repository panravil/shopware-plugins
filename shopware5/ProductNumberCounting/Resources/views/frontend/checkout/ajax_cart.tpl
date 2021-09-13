{extends file='parent:frontend/checkout/ajax_cart.tpl'}
{block name='frontend_checkout_ajax_cart_item_container_inner'}
	{$smarty.block.parent}
	{if isset($sBasket.DiscountPrice) && $sBasket.DiscountPrice > 0}
	<div class="cart--item">
		<div class="thumbnail--container"></div>
		<div class="action--container"></div>
		<a class="item--link">
			<span class="item--name">{s name="credit_note" namespace="backend/static/doc_type"}{/s}</span>
			<span class="item--price">
				{if $sBasket.DiscountPrice > 0}-  {/if}{$sBasket.DiscountPrice|currency}
			</span>
		</a>
	</div>
	{/if}
{/block}
{if {config name=showShippingCostsOffCanvas} == 0}
	{block name='frontend_checkout_ajax_cart_prices_container_without_shipping_costs'}
	    <div class="prices--container">
	        {block name='frontend_checkout_ajax_cart_prices_container_inner'}
	            <div class="small--information">
	                <span>{s name="AjaxCartTotalAmount"}{/s}</span>
	                <span class="small--prices">{$sBasket.Amount|currency}</span>
	            </div>
	            
				{if isset($sBasket.DiscountPrice) && $sBasket.DiscountPrice > 0}
	            <p class="small--information">
	                <span>{s name="credit_note" namespace="backend/static/doc_type"}{/s}</span>
	                <span class="small--prices">{if $sBasket.DiscountPrice > 0}-  {/if}{$sBasket.DiscountPrice|currency}</span>
	            </p>
				{/if}
	        {/block}
	        {block name='frontend_checkout_cart_footer_field_labels_total'}
	            <div class="prices--articles">
	                <span class="prices--articles-text">{s name="CartFooterLabelTotal" namespace="frontend/checkout/cart_footer"}{/s}</span>
	                <span class="prices--articles-amount">{$TotalPrice|currency}</span>
	            </div>
	        {/block}
	        {block name='frontend_checkout_ajax_cart_prices_info'}
	            <p class="prices--tax">
	                {s name="DetailDataPriceInfo" namespace="frontend/detail/data"}{/s}
	            </p>
	        {/block}
	    </div>
	{/block}
{else}
	{block name='frontend_checkout_ajax_cart_prices_container_with_shipping_costs'}
		<div class="prices--container">
	        {block name='frontend_checkout_ajax_cart_prices_container_inner'}
	            <div class="small--information">
	                <span>{s name="AjaxCartTotalAmount"}{/s}</span>
	                <span class="small--prices">{$sBasket.Amount|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}</span>
	            </div>
				{if isset($sBasket.DiscountPrice) && $sBasket.DiscountPrice > 0}
	            <p class="small--information">
	                <span>{s name="discount_name" namespace="backend/static/discounts_surcharges"}{/s}</span>
	                <span class="small--prices">{$sBasket.DiscountPrice|currency}</span>
	            </p>
				{/if}
	        {/block}
	        {if !$sUserLoggedIn && !$sUserData.additional.user.id}
	            {* Shipping costs & Shipping costs pre-calculation *}
	            {if {config name=showShippingCostsOffCanvas} == 1}
	                {block name='frontend_checkout_shipping_costs_country_trigger'}
	                    <a href="#show-hide--shipping-costs" class="table--shipping-costs-trigger">
	                        {s name='CheckoutFooterEstimatedShippingCosts' namespace="frontend/checkout/cart_footer"}{/s}
	                        <i class="icon--arrow-right"></i>
	                    </a>
	                    <span class="small--information">
	                    	{if $free_shipping_flag}
	                    		<span class="small--prices">{0|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}
		                        </span>
	                    	{else}
		                        <span class="small--prices"> {$sShippingcosts|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}
		                        </span>
		                    {/if}
	                    </span>
	                {/block}
	                {block name='frontend_checkout_shipping_costs_country_include'}
	                    {include file="frontend/checkout/shipping_costs.tpl" calculateShippingCosts=$showShippingCalculation}
	                {/block}
	            {/if}
	            {if {config name=showShippingCostsOffCanvas} == 2}
	                {block name='frontend_checkout_shipping_costs_country_include'}
	                    <div class="small--information">
	                        <span>{s name='CheckoutFooterEstimatedShippingCosts' namespace="frontend/checkout/cart_footer"}{/s}</span>
	                        <span class="small--prices"> {$sShippingcosts|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}
	                        </span>
	                    </div>
	                    {include file="frontend/checkout/shipping_costs.tpl" calculateShippingCosts=true}
	                {/block}
	            {/if}
	            {* Total sum *}
	            {block name='frontend_checkout_cart_footer_field_labels_total'}
	                <div class="prices--articles">
	                    <span class="prices--articles-text">{s name="CartFooterLabelTotal" namespace="frontend/checkout/cart_footer"}{/s}</span>
	                    <span class="prices--articles-amount">
	                        {$sBasket.AmountNumberic|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}
	                    </span>
	                </div>
	            {/block}
	            {block name='frontend_checkout_ajax_cart_prices_info'}
	                <p class="prices--tax">
	                    {s name="Star" namespace="frontend/listing/box_article"}{/s}{s name="AjaxDetailDataPriceInfo"}{/s}
	                </p>
	            {/block}
	        {/if}
	    </div>
	{/block}
{/if}