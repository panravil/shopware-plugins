<div class="panel address-manager--modal">
    <div class="panel--title is--underline">
        {s namespace="frontend/AboSchuler/product" name="labelAboShippingAddressChange"}{/s}
    </div>
    <div class="panel--body address-editor--body">
        <form method="post" action="{url controller=checkout action=$controllerName}" data-eventName="submit">
            <div class="abo-gift--register panel--body is--wide">
                <div class="address--firstname">
                    <input autocomplete="section-personal given-name"
                        name="aboGift[firstname]"
                        type="text"
                        value="{if $aboGiftData} {$aboGiftData['firstname'] } {/if}"
                        required="required"
                        aria-required="true"
                        placeholder="{s name='RegisterPlaceholderFirstname' namespace="frontend/register/personal_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                        id="firstname"
                        maxlength="30"
                        class="address--field is--required{if isset($error_flags.firstname)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                        {if $exist_in_basket } disabled="disabled" {/if}/>
                </div>
                <div class="address--lastname">
                    <input autocomplete="section-personal family-name"
                        name="aboGift[lastname]"
                        type="text"
                        value="{if $aboGiftData} {$aboGiftData['lastname'] } {/if}"
                        required="required"
                        aria-required="true"
                        placeholder="{s name='RegisterPlaceholderLastname' namespace="frontend/register/personal_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                        id="lastname" 
                        maxlength="30"
                        class="address--field is--required{if isset($error_flags.lastname)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                        {if $exist_in_basket } disabled="disabled" {/if}/>
                </div>
                <div class="address--street">
                    <input autocomplete="section-billing billing street-address"
                        name="aboGift[street]"
                        type="text"
                        value="{if $aboGiftData} {$aboGiftData['street'] } {/if}"
                        required="required"
                        aria-required="true"
                        placeholder="{s name='RegisterBillingPlaceholderStreet' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                        id="street"
                        maxlength="30"
                        class="address--field address--field-street is--required{if isset($error_flags.street)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                        {if $exist_in_basket } disabled="disabled" {/if}/>
                </div>
                <div class="address--zip-city">
                    <input autocomplete="section-billing billing postal-code"
                        name="aboGift[zipcode]"
                        type="text"
                        value="{if $aboGiftData} {$aboGiftData['zipcode'] } {/if}"
                        required="required"
                        aria-required="true"
                        placeholder="{s name='RegisterBillingPlaceholderZipcode' namespace="frontend/register/billing_fieldset"}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                        id="zipcode"
                        maxlength="20"
                        class="address--field address--spacer address--field-zipcode is--required{if isset($error_flags.zipcode)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
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
                        class="address--field address--field-city is--required{if isset($error_flags.city)} has--error{/if} {if $exist_in_basket } input--disabled{/if}" 
                        {if $exist_in_basket } disabled="disabled" {/if}/>
                </div>
                <div class="address--country field--select select-field">
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

                        {foreach $countryList as $id => $name}
                        <option value="{$id}" 
                            {if $aboGiftData && $aboGiftData['country_id'] == $id} selected {/if}>
                            {$name}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="panel--actions address--form-actions is--wide" style="padding: 0 1.25rem; text-align: right;">
                <button class="btn is--primary address--form-submit" data-checkformisvalid="true" data-preloader-button="true" type="submit">
                    {s namespace="frontend/AboSchuler/product" name="buttonAboShippingAddressChange"}{/s}
                </button>
            </div>
        </form>
    </div>
</div>