{extends file="parent:frontend/detail/buy.tpl"}

{block name="frontend_detail_buy"}
<input type="hidden" id="artID"  value="{{$sArticle.articleID}}">
	<!-- The Modal -->
	<div id="giftsModal" class="modal">
	    <!-- Modal content -->
	    <div class="modal-content" id="giftArticlesSection">
	    {*<span class="giftsClose">&times;</span>*}
            <span class="modal-close" onclick="modal_close()">x</span>
            <h1 class="modal-h1" >Bitte wählen Sie Ihre(n) Gratis-Artikel:</h1>
            <h4 class="gift-counter-h4" >noch <span id="gift-counter-start">{$taken_gift}</span> Artikel</h4>
            <input type="hidden"  id="gift_limit_var" value='{$gift_selection_limit}'>

			<section class="giftArticles" >
				{if $gift_selection_limit > 0 }
					{if $article_gift|count > 0 }
						{foreach $article_gift as $item}
                            <form class="gift-range"  {if $theme.offcanvasCart} data-showModal="false" data-addArticleUrl="{url controller=checkout action=ajaxAddArticleCart}"{/if}>
								{if $article_gift|count > 0 }
									<div class="{if $item.quantity.quantity_from < $sArticle.attr23 && !$isWhiskey} is--hidden{/if}">
										<img src="{$item.Image[0]}">
										<input type="hidden" class="isf_qty_from" id="quantity_from-{$item.giftId}" readonly name="quantity_from" value='{$item.quantity.quantity_from}' />
										<input type="hidden" id="quantity_to-{$item.giftId}" readonly name="quantity_to" value='{$item.quantity.quantity_to}' />
										<input type="hidden" id="gift-ID" name="sgift" value='{$item.giftId}'>
										<input type="hidden" id="article-{$item.giftId}-{$item.ordernumber}" name="sAdd" value='{$item.ordernumber}'>
										<input type="hidden"  name="sQuantity" value='1'>
										<input type="hidden"  name="giftParentOrderNumber" value='{$sArticle.ordernumber}'>
										<input type="hidden"  name="giftArticle" value='1'>{$item.name}<br>
                                        <button class="btn is--secondary modal-isf-select-btn" type="button" onclick="giftLimitChecker(),addArticleAjax(this)" >Auswählen</button>
									</div>
							    {/if}
							</form>
					   {/foreach}
					{/if}
				{/if}
			</section>
	    </div>
	</div>

    {if $sArticle.instock >= $sArticle.minpurchase}
        <form id="sAddToBasket" name="sAddToBasket" method="post" action="{url controller=checkout action=addArticle}" class="buybox--form  {if $sArticle.is_abo_article eq 1} abo-buybox--form {/if}" data-add-article="true" data-eventName="submit"{if $theme.offcanvasCart} data-showModal="false" data-addArticleUrl="{url controller=checkout action=ajaxAddArticleCart}"{/if}>
            {block name="frontend_detail_buy_configurator_inputs"}
                {if $sArticle.sConfigurator&&$sArticle.sConfiguratorSettings.type==3}
                    {foreach $sArticle.sConfigurator as $group}
                        <input type="hidden" name="group[{$group.groupID}]" value="{$group.selected_value}"/>
                    {/foreach}
                {/if}
            {/block}
            {if $sArticle.is_abo_article eq 1}
            
                <p class='vm--second-Bestellkonditionen'>
                <br/>
                    <a href='#Bestellkonditionen'><strong>{s namespace='frontend/index/index' name='VmSecondBestellkonditionen'}Bestellkonditionen >{/s}</strong>
                    </a>
                </p>
            {/if}
            <input type="hidden" name="sActionIdentifier" value="{$sUniqueRand}"/>
            
            <input type="hidden" name="sAddAccessories" id="sAddAccessories" value=""/>

            <input type="hidden" name="sAdd" value="{$sArticle.ordernumber}"/>
                {$productType = "standard"}
                {$productTypeQuantity = "1"}
                
            <!-- {if $sArticle.attr34}
                    {$productType = "packets"}
                    {$productTypeQuantity = $sArticle.abo_inhalt}
                {/if}
                
                {if $sArticle.attr38}
                    {$productType = "carton"}
                    {$productTypeQuantity = $sArticle.attr23}
                {/if} -->
            
            <input type="hidden" name="productType" value="{$productType}"/>
            <input type="hidden" id="productTypeQuantity" name="productTypeQuantity" value="{$productTypeQuantity}"/>

            {if $sArticle.is_abo_article eq 1}
                <input type="hidden" name="abo_product_quantity" value="1" />
                <div class="vm-warning-tab" style="width: 80%">
                    {$abo_gift_selected = false}
                    {foreach $sArticle.sConfigurator as $configuratorGroup}
                        {foreach $configuratorGroup.values as $option}
                            {if $option.optionname == 'Geschenk Abo' && $option.selected }
                                {$abo_gift_selected = true}
                                {$shippingDifferenceContent="{s namespace='frontend/index/index' name='VmGeschenkAboWarning'} {/s}"}
                                {include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
                            {else if $option.optionname == 'Standard Abo' && $option.selected}
                                {$shippingDifferenceContent="{s namespace='frontend/index/index' name='VmMeinAboWarning'} {/s}"}
                                {include file="frontend/_includes/messages.tpl" type="warning" content="{$shippingDifferenceContent}"}
                            {/if}
                        {/foreach}
                    {/foreach}
                </div>
                <div class="abo-gift-choose--btns">
                    <div class="block btn is--center is--icon-right is--large is--primary btn-abo-gift" onclick="selectAboGift(this);">
                        <span class="fas fa-gift"></span>
                        {s namespace="frontend/AboSchuler/product" name="buttonAboGift"}{/s}
                    </div>
                    <div class="block btn is--center is--icon-right is--large is--primary btn-abo-not-gift" onclick="selectAboNotGift(this);">{s namespace="frontend/AboSchuler/product" name="buttonAboMy"}{/s}</div>
                </div>
                {if $abo_gift_selected }
                    <div class="abo-gift--register">
                        <div class="register--firstname">
                            <input autocomplete="section-personal given-name"
                                name="aboGift[firstname]"
                                type="text"
                                value="{if $aboGiftData} {$aboGiftData['firstname'] } {/if}"
                                required="required"
                                aria-required="true"
                                placeholder="{s name='RegisterPlaceholderFirstname' namespace="frontend/register/personal_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                                id="firstname"
                                maxlength="30"
                                class="register--field is--required{if isset($error_flags.firstname)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                                {if $exist_in_basket } disabled="disabled" {/if}/>
                        </div>
                        <div class="register--lastname">
                            <input autocomplete="section-personal family-name"
                                name="aboGift[lastname]"
                                type="text"
                                value="{if $aboGiftData} {$aboGiftData['lastname'] } {/if}"
                                required="required"
                                aria-required="true"
                                placeholder="{s name='RegisterPlaceholderLastname' namespace="frontend/register/personal_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                                id="lastname" 
                                maxlength="30"
                                class="register--field is--required{if isset($error_flags.lastname)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                                {if $exist_in_basket } disabled="disabled" {/if}/>
                        </div>
                        <div class="register--street">
                            <input autocomplete="section-billing billing street-address"
                                name="aboGift[street]"
                                type="text"
                                value="{if $aboGiftData} {$aboGiftData['street'] } {/if}"
                                required="required"
                                aria-required="true"
                                placeholder="{s name='RegisterBillingPlaceholderStreet' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                                id="street"
                                maxlength="30"
                                class="register--field register--field-street is--required{if isset($error_flags.street)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                                {if $exist_in_basket } disabled="disabled" {/if}/>
                        </div>
                        <div class="register--zip-city">
                            <input autocomplete="section-billing billing postal-code"
                                name="aboGift[zipcode]"
                                type="text"
                                value="{if $aboGiftData} {$aboGiftData['zipcode'] } {/if}"
                                required="required"
                                aria-required="true"
                                placeholder="{s name='RegisterBillingPlaceholderZipcode' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                                id="zipcode"
                                maxlength="20"
                                class="register--field register--spacer register--field-zipcode is--required{if isset($error_flags.zipcode)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                                {if $exist_in_basket } disabled="disabled" {/if}/>

                                <input autocomplete="section-billing billing address-level2"
                                name="aboGift[city]"
                                type="text"
                                value="{if $aboGiftData} {$aboGiftData['city'] } {/if}"
                                required="required"
                                aria-required="true"
                                placeholder="{s name='RegisterBillingPlaceholderCity' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                                id="city"
                                size="25"
                                maxlength="30"
                                class="register--field register--field-city is--required{if isset($error_flags.city)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                                {if $exist_in_basket } disabled="disabled" {/if}/>
                        </div>
                        <div class="register--country field--select select-field">
                            <select name="aboGift[country]"
                                data-address-type="billing"
                                id="country"
                                required="required"
                                aria-required="true"
                                class="select--country is--required{if isset($error_flags.country)} has--error{/if} {if $exist_in_basket } is--disabled{/if}" 
                                {if $exist_in_basket } disabled="disabled" {/if}>

                                <option disabled="disabled"
                                    value=""
                                    selected="selected">
                                {s name='RegisterBillingPlaceholderCountry' namespace="frontend/register/billing_fieldset"}{/s}
                                {s name="RequiredField" namespace="frontend/register/index"}{/s}
                                </option>
                                {if $smarty.server.HTTP_HOST|trim eq 'www.schulerweine.de' or $smarty.server.HTTP_HOST|trim eq 'schulerweine.de'}
                                    <option value="2">Deutschland</option>
                                {else}
                                    <option value="26">Schweiz</option>
                                {/if}
                            <!-- {foreach $countryList as $id => $name}
                                <option value="{$id}" 
                                    {if $aboGiftData && $aboGiftData['country_id'] == $id} selected {/if}>
                                    {$name}
                                </option>
                                {/foreach}-->
                            </select>
                        </div>
                    </div>
                {/if}
            {/if}
            {if $sArticle.is_abo_article eq 1}
                <div class="vm-dummy-number-chooser">1</div>
                <div class="vm-inkl-price">{s namespace="frontend/AboSchuler/product" name="vmInklPrice"}{/s}</div>
                <div class="vm-choose-variant">{s namespace="frontend/AboSchuler/product" name="vmChooseVariant"}{/s}</div>
            <!-- <div class="vm-red-text">{s namespace="frontend/AboSchuler/product" name="vmRedText"}{/s}</div>-->
            {/if}
            {* @deprecated - Product variants block *}
            {block name='frontend_detail_buy_variant'}{/block}
            
            {* Article accessories *}
            {block name="frontend_detail_buy_accessories_outer"}
                {if $sArticle.sAccessories}
                    {block name='frontend_detail_buy_accessories'}
                        <div class="buybox--accessory">
                            {foreach $sArticle.sAccessories as $sAccessory}
                                {* Group name *}
                                <h2 class="accessory--title">{$sAccessory.groupname}</h2>
                                <div class="accessory--group">
                                    {* Group description *}
                                    <p class="group--description">
                                        {$sAccessory.groupdescription}
                                    </p>

                                    {foreach $sAccessory.childs as $sAccessoryChild}
                                        <input type="checkbox" class="sValueChanger chkbox" name="sValueChange"
                                            id="CHECK{$sAccessoryChild.ordernumber}" value="{$sAccessoryChild.ordernumber}"/>
                                        <label for="CHECK{$sAccessoryChild.ordernumber}">{$sAccessoryChild.optionname|truncate:35}
                                            ({s name="DetailBuyLabelSurcharge"}{/s}
                                            : {$sAccessoryChild.price} {$sConfig.sCURRENCYHTML})
                                        </label>
                                        <div id="DIV{$sAccessoryChild.ordernumber}" class="accessory--overlay">
                                            {include file="frontend/detail/accessory.tpl" sArticle=$sAccessoryChild.sArticle}
                                        </div>
                                    {/foreach}
                                </div>
                            {/foreach}
                        </div>
                    {/block}
                {/if}
            {/block}

            {$sCountConfigurator=$sArticle.sConfigurator|@count}

            {block name="frontend_detail_buy_button_container_outer"}
                {if (!isset($sArticle.active) || $sArticle.active)}
                    {if $sArticle.isAvailable}
                        {block name="frontend_detail_buy_button_container"}
                            <div class="buybox--button-container block-group{if $NotifyHideBasket && $sArticle.notification && $sArticle.instock < $sArticle.minpurchase} is--hidden{/if}">
                                {if $sArticle.is_abo_article eq 1}
                                
                                <input class="vm-listing-quantity" type="hidden" name="sQuantity" value="1">
                                {else}
                                {* Quantity selection *}
                                {block name='frontend_detail_buy_quantity'}
                                    <div class="buybox--quantity block">
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

                                        {block name='frontend_detail_buy_quantity_select'}
                                            <input class="vm-listing-quantity" type="text" name="sQuantity" min="$sArticle.minpurchase" max="$maxQuantity"
                                                value="{if $sArticle.attr23 > 0 and !$isWhiskey}{$sArticle.attr23}{else}1{/if}">

                                            <div class="vm-box-product-quantity isf-quantity-box {if $sArticle.attr38 eq '1'} vm-is-container-only{/if}{if $sArticle.attr22} vm-is-container-or-bottle{/if} vm-box-is-wood-{$containerMaterial}"
                                                    {if $sArticle.attr23 > 0 and !$isWhiskey} data-container-size="{$sArticle.attr23}"{/if}
                                                data-container-is-wood="{$containerMaterial}">
                                                <div class="vm-max-order-value" style="display: none">
                                                    {$sArticle.maxpurchase}
                                                </div>
                                                <div class="vm-box-product-quantity-options" id="vm-box-product-quantity-options">
                                                    <div id="vm-box-product-quantity-options-wrapper">
                                                    </div>
                                                </div>
                                                <span onclick="ToggleQuantityOptionsVisibility(false,''), $(this)">{if $sArticle.attr23 > 0 and !$isWhiskey}{$sArticle.attr23}{else}1{/if}</span>
                                            </div>
                                        {/block}
                                    </div>
                                {/block}
                                {/if}
                                {* "Buy now" button *}
                                {block name="frontend_detail_buy_button"}
                                {$addBtnStatus}
                                    {if ($exist_in_basket && $abo_gift_selected) || $addBtnStatus}
                                        <button  class="disabled-buybox--button block btn is--disabled is--primary is--icon-right is--large is--center" disabled="disabled" name="{s name="DetailBuyActionAddName"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
                                            <i class="icon--basket"></i>{s name="DetailBuyActionAdd"}{/s}
                                        </button>
                                    {else if $sArticle.sConfigurator && !$activeConfiguratorSelection}
                                        <button  class="buybox--button block btn is--disabled is--icon-right is--large" disabled="disabled" aria-disabled="true" name="{s name="DetailBuyActionAddName"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
                                            <i class="icon--basket"></i>{s name="DetailBuyActionAdd"}{/s}
                                        </button>
                                    {else}
                                        <button  class="buybox--button block btn is--primary is--icon-right is--center is--large" name="{s name="DetailBuyActionAddName"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
                                            <i class="icon--basket"></i>{s name="DetailBuyActionAdd"}{/s}
                                        </button>
                                    {/if}
                                {/block}
                            </div>
                        {/block}
                    {/if}
                {/if}
            {/block}
        </form>
    {/if}


    {if $sArticle.shippingfree neq "1"}
        <p class="product--tax {if $sArticle.is_abo_article eq 1}abo-product--tax{/if}" data-content="" data-modalbox="true" data-targetSelector="a" data-mode="ajax">
            {s namespace='frontend/index/index' name="VmDetailTyxInfo"}inkl. MwSt. exkl. Versand{/s}
        </p>
    {else}
        <p class="product--tax {if $sArticle.is_abo_article eq 1}abo-product--tax{/if}" data-content="" data-modalbox="true" data-targetSelector="a" data-mode="ajax">
            {s namespace='frontend/index/index' name="VmDetailTyxInfoMwst"}inkl. MwSt.{/s}
        </p>
    {/if}

{/block}









