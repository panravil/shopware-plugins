{namespace name="frontend/checkout/cart_item"}

<div class="table--tr block-group row--product row--order-gift-product{if $isLast} is--last-row{/if}">

    {* Product information column *}
    {block name='frontend_checkout_cart_item_order_gift_name'}
        <div class="column--product">

            {* Product image *}
            {block name='frontend_checkout_cart_item_order_gift_image'}
                <div class="panel--td column--image">
                    <div class="table--media">
                        {if $sBasketItem.image.src.2}
                            <div class="table--media-outer">
                                <div class="table--media-inner">
                                    {$desc = $sBasketItem.articlename|escape}
                                    {if $sBasketItem.image.description}
                                        {$desc = $sBasketItem.image.description|escape}
                                    {/if}
                                    <img src="{$sBasketItem.image.src.2}" alt="{$desc}" title="{$desc|truncate:160}" />
                                    <span class="cart--badge">
                                        <span>{s name="CartGiftItemInfoFree"}GRATIS!{/s}</span>
                                    </span>
                                </div>
                            </div>
                        {else}
                            <div class="table--media">
                                <div class="basket--badge">
                                    {s name="CartGiftItemInfoFree"}{/s}
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            {/block}

            {* Product information *}
            {block name='frontend_checkout_cart_item_order_gift_details'}
                <div class="panel--td table--content">
                    {* Product name *}
                    <div class="content--title">
                        {$sBasketItem.articlename|strip_tags|truncate:60}
                    </div>

                    {* Additional product information *}
                    {block name='frontend_checkout_cart_item_order_gift_details_inline'}{/block}
                </div>
            {/block}
        </div>
    {/block}


    <div class="panel--td column--quantity is--align-right">

            <div class="vm-box-product-quantity  vm-is-container-or-bottlevm-box-is-wood-false">
                <span>
                   {$sBasketItem.quantity}
                </span>
            </div>

    </div>

    <div class="column--unit-price-custom">
        <div class="panel--td column--unit-price is--align-right">
            <div class="column--label unit-price--label">
                Stückpreis
            </div>
            &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;
        </div>
    </div>





    {* Product tax rate *}
    {block name='frontend_checkout_cart_item_premium_tax_price'}{/block}

    {* Accumulated product price *}
    {block name='frontend_checkout_cart_item_order_gift_total_sum'}
        <div class="column--total-price-custom">
            <div class="panel--td column--total-price is--align-right">
                <div class="column--label total-price--label">
                    {s name="CartColumnTotal" namespace="frontend/checkout/cart_header"}{/s}
                </div>

                {if $sBasketItem.price}
                    {$sBasketItem.price|currency}{block name='frontend_checkout_cart_tax_symbol'}{s name="Star" namespace="frontend/listing/box_article"}{/s}{/block}
                {else}
                    {s name="CartGiftItemInfoFree"}{/s}
                {/if}
            </div>
        </div>
    {/block}

    {* Remove product from basket *}
    {block name='frontend_checkout_cart_item_order_gift_delete_article'}
        <div class="panel--td column--actions" style="display: block;">
            {* {if !$sBasketItem.price} *}
            <form action="{url action='deleteArticle' sDelete=$sBasketItem.id sTargetAction=$sTargetAction}"
                  method="post">
                <button type="submit" class="btn is--small column--actions-link"
                        title="{"{s name='CartGiftItemLinkDelete'}Löschen{/s}"|escape}">
                    <i class="icon--cross"></i>
                </button>
            </form>
            {* {/if} *}
        </div>
    {/block}
</div>
