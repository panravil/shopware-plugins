{extends file='parent:frontend/checkout/cart_footer.tpl'}
{* Shipping costs *}
{block name='frontend_checkout_cart_footer_field_labels_shipping'}
    <li class="list--entry block-group entry--shipping">

        {block name='frontend_checkout_cart_footer_field_labels_shipping_label'}
            <div class="entry--label block">
                {s namespace='frontend/index/index' name="VmCartPricesShipping"}Lieferkosten{/s}
            </div>
        {/block}

        {block name='frontend_checkout_cart_footer_field_labels_shipping_value'}
            <div class="entry--value block">
                {$sShippingcosts|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}
            </div>
        {/block}
    </li>
	{if isset($sBasket.DiscountPrice) && $sBasket.DiscountPrice > 0}
    <li class="list--entry block-group entry--discount">
        <div class="entry--label block">
            {s name="credit_note" namespace="backend/static/doc_type"}{/s}
        </div>

        <div class="entry--value block">
            {if $sBasket.DiscountPrice > 0}-  {/if}{$sBasket.DiscountPrice|currency}
        </div>
    </li>
    {/if}
{/block}

{* Total sum *}
{block name='frontend_checkout_cart_footer_field_labels_total'}
    <li class="list--entry block-group entry--total">

        {block name='frontend_checkout_cart_footer_field_labels_total_label'}
            <div class="entry--label block">
                {s name="CartFooterLabelTotal"}{/s}
            </div>
        {/block}

        {block name='frontend_checkout_cart_footer_field_labels_total_value'}
            <div class="entry--value block is--no-star">
                {$sBasket.AmountNumeric|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}
            </div>
        {/block}
    </li>
{/block}