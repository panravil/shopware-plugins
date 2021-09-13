{extends file="parent:frontend/checkout/ajax_cart_item.tpl"}
{namespace name="frontend/checkout/ajax_cart"}


{* Real product *}
{block name='frontend_checkout_ajax_cart_articleimage_product'}
    {if $basketItem.modus == $IS_PRODUCT || $basketItem.modus == $IS_PREMIUM_PRODUCT}
        {$desc = $basketItem.articlename|escape}
        {if $basketItem.additional_details.image.thumbnails}
            {if $basketItem.additional_details.image.description}
                {$desc = $basketItem.additional_details.image.description|escape}
            {/if}
            <img srcset="{$basketItem.additional_details.image.thumbnails[0].sourceSet}" alt="{$desc}" title="{$desc|truncate:160}" class="thumbnail--image" />

        {elseif $basketItem.image.src.0}
            <img src="{$basketItem.image.src.0}" alt="{$desc}" title="{$desc|truncate:160}" class="thumbnail--image" />
        {/if}
    {else}
        <div class="table--media">
            <div class="basket--badge">
                {s name="CartGiftItemInfoFreecustom"}Gratis{/s}
            </div>
        </div>
    {/if}
{/block}