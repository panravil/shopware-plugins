{block name='frontend_checkout_order_gift_body'}

    {if $sArticles|@count}
        <div class="premium-product panel has--border is--rounded">

            {* Headline *}
            <div class="premium-product--title panel--title is--underline">
                {s name="CartOrderGiftsHeadline" namespace="frontend/checkout/cart"}Geschenkartikelen wählen{/s} {$sGiftArticles.name}
            </div>

            {* Product slider *}
            <div class="premium-product--content product-slider" data-product-slider="true" data-itemMinWidth="280">

                {* Product slider container *}
                <div class="product-slider--container is--horizontal">
                    {foreach $sArticles as $giftArticle}

                        {* Product slider item *}
                        <div class="premium-product--product product-slider--item" style="width: 25%;">

                            <div class="product--inner">
                                {if $giftArticle}
                                    <p class="premium-product--free">{s name="OrderGiftInfoFreeProduct"}Jetzt Gratis verfügbar{/s}</p>
                                {else}
                                    <p class="premium-product--info">{s name="OrderGiftsInfoAtAmount"}ab{/s} {$giftArticle.startprice|currency} {s name="OrderGiftInfoBasketValue"}Warenkorbwert{/s}</p>
                                {/if}

                                {* Product image *}
                                {block name='frontend_checkout_order_gift_image'}
                                    <a href="{$giftArticle.sArticle.linkDetails}" title="{$giftArticle.sArticle.articleName|escape}" class="product--image">
                                        {if $giftArticle.selected}
                                            <div class="premium-product--badge">
                                                <i class="icon--check"></i>
                                            </div>
                                        {/if}

										{block name='frontend_checkout_order_gift_image_element'}
											<span class="image--element">
                                                {if $giftArticle.sArticle.image.thumbnails}
                                                    <img srcset="{$giftArticle.sArticle.image.thumbnails[0].sourceSet}"
                                                         alt="{$giftArticle.sArticle.articleName|escape}" />
												{else}
													<img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
														 alt="{"{s name="OrderGiftInfoNoPicture"}Kein Bild vorhanden{/s}"|escape}">
												{/if}
											</span>
										{/block}
                                    </a>
                                {/block}

                                {if $giftArticle}
                                    {block name='frontend_checkout_order_gift_form'}
                                        <form action="{url controller='ShopOrderGifts' action='addOrderGift' sTargetAction=$sTargetAction}" method="post" id="sAddOrderGiftForm{$key}" name="sAddOrderGiftForm{$key}">
                                            <input type="hidden" name="giftId" value="{$sGiftArticles.id}"/>
                                            {block name='frontend_checkout_order_gift_select_article'}
                                                {if $giftArticle.sVariants && $giftArticle.sVariants|@count > 1}
                                                    <div class="premium--variant">
                                                        {* <input type="hidden" id="giftVariantID" name="variantID" value="{$giftArticle.mainDetailId}"/> *}
														<select class="premium--selection" id="sAddOrderGift{$key}" name="ordernumber" required>
															<option value="">{s name="OrderGiftInfoSelect"}Bitte wählen{/s}</option>
															{foreach from=$giftArticle.sVariants item=variant}
																<option value="{$variant.ordernumber}" data-variantID="{$variant.variantId}">{$variant.additionaltext}</option>
															{/foreach}
														</select>
														{block name='frontend_checkout_order_gift_info_button_small'}
															<button class="premium--button btn is--primary is--align-center" type="submit">
																<i class="icon--arrow-right is--large"></i>
															</button>
														{/block}
													</div>
                                                {else}
                                                    {* <input type="hidden" name="variantID" value="{$giftArticle.mainDetailId}"/> *}
                                                    <input type="hidden" name="ordernumber" value="{$giftArticle.sArticle.ordernumber}"/>
													{block name='frontend_checkout_order_gift_info_button'}
														<button class="btn is--primary is--align-center is--icon-right" type="submit">
															{s name='OrderGiftActionAdd'}Geschenkartikelen auswählen{/s}
															<i class="icon--arrow-right"></i>
														</button>
													{/block}
                                                {/if}
                                            {/block}
                                        </form>
                                    {/block}
                                {else}
                                    <div class="btn premium-product--difference is--align-center is--disabled">
                                        {s name="OrderGiftsInfoDifference"}noch{/s} <span class="difference--price">{$giftArticle.sDifference|currency}</span>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    {/if}
{/block}
