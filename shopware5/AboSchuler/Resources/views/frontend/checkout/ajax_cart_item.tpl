{extends file="parent:frontend/checkout/ajax_cart_item.tpl"}
{block name="frontend_checkout_ajax_cart_articlename_price"}
    <span class="item--price">{if $basketItem.amount}{$basketItem.amount|currency}{else}{s name="AjaxCartInfoFree"}{/s}{/if}{s name="Star"}{/s}</span>
    {if $aboGiftData} 
        <div class="abo-shipping-address" style="border-top: 1px solid #dadae5; margin-top: 5px; padding-top: 7px;">
            <div class="abo-shipping-item">
                <span>Addresse:</span>
            </div>
            <div class="abo-shipping-item">
                <span>{$aboGiftData['street']}</span>
            </div>
            <div class="abo-shipping-item">
                <span style="margin-right: 5px;">{$aboGiftData['zipcode']}</span>
                <span>{$aboGiftData['city']}</span>
            </div>
            <div class="abo-shipping-item">
                <span>{$aboGiftData['country']}</span>
            </div>
        </div>
    {/if}
{/block}