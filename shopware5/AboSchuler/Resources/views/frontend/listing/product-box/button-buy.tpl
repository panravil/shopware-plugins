{extends file="parent:frontend/listing/product-box/button-buy.tpl"}
{block name="frontend_listing_product_box_button_buy_button"}
    <button class="buybox--button {if $addBtnStatus}is--disabled{/if} block btn is--primary is--icon-right is--center is--large" {if $addBtnStatus}disabled="disabled"{/if}>
        {block name="frontend_listing_product_box_button_buy_button_text"}
            {s namespace="frontend/listing/box_article" name="ListingBuyActionAdd"}{/s}<i class="icon--basket"></i> <i class="icon--arrow-right"></i>
        {/block}
    </button>
{/block}