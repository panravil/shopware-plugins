{extends file='parent:frontend/detail/config_variant.tpl'}

{$configurator = $sArticle.sConfigurator}

{block name='frontend_detail_configurator_variant'}
    <div class="configurator--variant">
        {block name='frontend_detail_configurator_variant_form'}
            <form method="post" action="{url sArticle=$sArticle.articleID sCategory=$sArticle.categoryID}" class="configurator--form">

                {foreach $configurator as $configuratorGroup}
                    {block name='frontend_detail_configurator_variant_group'}
                        <div class="variant--group {if $sArticle.is_abo_article eq 1} abo-variant--group hide-ad {/if}">

                            {block name='frontend_detail_configurator_variant_group_name'}
                                <p class="variant--name">{$configuratorGroup.groupname}</p>
                            {/block}

                            {block name='frontend_detail_configurator_variant_group_options'}
                                {foreach $configuratorGroup.values as $option}

                                    {block name='frontend_detail_configurator_variant_group_option'}
                                        <div class="variant--option{if $option.media} is--image{/if}">

                                            {block name='frontend_detail_configurator_variant_group_option_input'}
                                                <input type="radio"
                                                       class="option--input"
                                                       id="group[{$option.groupID}][{$option.optionID}]"
                                                       name="group[{$option.groupID}]"
                                                       value="{$option.optionID}"
                                                       title="{$option.optionname}"
                                                       {if $theme.ajaxVariantSwitch}data-ajax-select-variants="true"{else}data-auto-submit="true"{/if}
                                                       {if !$sArticle.notification && !$option.selectable}disabled="disabled"{/if}
                                                       {if $option.selected && ($sArticle.notification || $option.selectable)}checked="checked"{/if} />
                                            {/block}

                                            {block name='frontend_detail_configurator_variant_group_option_label'}
                                                <label for="group[{$option.groupID}][{$option.optionID}]" class="option--label{if !$sArticle.notification && !$option.selectable} is--disabled{/if}">

                                                    {if $option.media}
                                                        {$media = $option.media}

                                                        {block name='frontend_detail_configurator_variant_group_option_label_image'}
                                                            <span class="image--element">
                                                                <span class="image--media">
                                                                    {if isset($media.thumbnails)}
                                                                        <img srcset="{$media.thumbnails[0].sourceSet}" alt="{$option.optionname}" />
                                                                    {else}
                                                                        <img src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$option.optionname}">
                                                                    {/if}
                                                                </span>
                                                            </span>
                                                        {/block}
                                                    {else}
                                                        {block name='frontend_detail_configurator_variant_group_option_label_text'}
                                                            {$option.optionname}
                                                        {/block}
                                                    {/if}
                                                </label>
                                            {/block}
                                        </div>
                                    {/block}
                                {/foreach}
                            {/block}
                        </div>
                    {/block}
                {/foreach}
            </form>
        {/block}
    </div>
{/block}

{block name='frontend_detail_configurator_variant_reset'}
    {if $sArticle.is_abo_article eq 1}
        <div class="hide-ad">
            {include file="frontend/detail/config_reset.tpl"}
        </div>
    {else}
        {include file="frontend/detail/config_reset.tpl"}
    {/if}
{/block}
