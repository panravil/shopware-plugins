{extends file="parent:frontend/detail/content.tpl"}

{block name='frontend_index_content_inner'}
    <div class="content product--details vm-product-detail-non-paket {if !$sArticle.attr55 and !$sArticle.attr56 and !$sArticle.attr57} vm-product-detail-no-mood-img{/if}" itemscope itemtype="http://schema.org/Product"{if !{config name=disableArticleNavigation}} data-product-navigation="{url module="widgets" controller="listing" action="productNavigation"}" data-category-id="{$sArticle.categoryID}" data-main-ordernumber="{$sArticle.mainVariantNumber}"{/if} data-ajax-wishlist="true" data-compare-ajax="true"{if $theme.ajaxVariantSwitch} data-ajax-variants-container="true"{/if}>

        {* The configurator selection is checked at this early point
           to use it in different included files in the detail template. *}
        {block name='frontend_detail_index_configurator_settings'}

            {* Variable for tracking active user variant selection *}
            {$activeConfiguratorSelection = true}

            {if $sArticle.sConfigurator && ($sArticle.sConfiguratorSettings.type == 1 || $sArticle.sConfiguratorSettings.type == 2)}
                {* If user has no selection in this group set it to false *}
                {foreach $sArticle.sConfigurator as $configuratorGroup}
                    {if !$configuratorGroup.selected_value}
                        {$activeConfiguratorSelection = false}
                    {/if}
                {/foreach}
            {/if}
        {/block}

        {* Product header *}
        <div class="vm-detail-hedline-mobile">
        	{include file="frontend/detail/content/header.tpl"}

            {if $sArticle.attr33}
                <div class="vm-short-description">{$sArticle.attr33}</div>
            {/if}
        </div>
       
       <!-- <p class="vm-Bestellkonditionen vm-test"><a href="#Bestellkonditionen"></br><strong>{s namespace='frontend/index/index' name='vmBestellkonditionen'}{/s}</strong></a></p>-->
        <div class="product--detail-upper block-group">
            {* Product image *}
            {block name='frontend_detail_index_image_container'}
                <div class="product--image-container image-slider{if $sArticle.image && {config name=sUSEZOOMPLUS}} product--image-zoom{/if}"
                    {if $sArticle.image}
                    data-image-slider="true"
                    data-image-gallery="true"
                    data-maxZoom="{$theme.lightboxZoomFactor}"
                    data-thumbnails=".image--thumbnails"
                    {/if}>
                    {include file="frontend/detail/image.tpl"}

                    <div class="vm-detail-custom-badges-wrapper" style="display: grid;">

                        {if $sArticle.attr11 && $sArticle.attr11 != "NULL"}
                            <div class="vm-detail-custom-badges-barrel">
                                <img src="/media/image/27/12/1d/icn-fass.png" />

                                <div class="vm-detail-custom-badges-barrel-text">
                                    {s namespace='frontend/index/index' name='VmProductBadgeBarrelText'}Ausbau in Barrique{/s}
                                </div>
                            </div>
                        {/if}

						{* isf code start *}
						{if (isset($sArticle.delivery_date_2) AND !empty($sArticle.delivery_date_2)) OR (isset($sArticle.delivery_date_3) AND !empty($sArticle.delivery_date_3))}
                            <div class="vm-detail-custom-badges-barrel isf-calander-img">
                                <img src="/media/image/2f/e3/03/Terminlieferung-icon.png" />

                                <div class="vm-detail-custom-badges-barrel-text">
                                    {s namespace="frontend/index/index" name="VmTerminlieferungBadge"}Terminlieferung{/s}
                                </div>
                            </div>
                        {/if}
						{* isf code end *}

                        {if $sArticle.attr5 && $sArticle.attr5 != "NULL"}
                            <div class="vm-detail-custom-badges-medal">
                                <img src="/media/image/2a/ba/5d/icn-medaille.png" />

                                <div class="vm-detail-custom-badges-medal-text">
                                    {s namespace='frontend/index/index' name='VmProductBadgePremierText'}Prämierter Wein{/s}
                                </div>
                            </div>
                        {/if}

                        {if $sArticle.attr38 eq '1' and ($sArticle.attr22 eq '6er Holzkiste' or $sArticle.attr22 eq '12er Holzkiste' or $sArticle.attr22 eq '6er Karton' or $sArticle.attr22 eq '12er Karton')}
                            <div class="vm-detail-custom-badges-box">
                            	{if $sArticle.attr22 eq '6er Holzkiste'}
                                	<img src="/media/image/85/0d/d6/icon_wood_6.png" />

                                    <div class="vm-detail-custom-badges-box-text">
                                        {s namespace='frontend/index/index' name='VmProductBadgeWoodenBoxOnlyTextFirst'}Ausschliesslich in Original-Holzkiste {/s}
                                        ({$sArticle.attr23}{s namespace='frontend/index/index' name='VmProductBadgeBoxTextLast'}&nbsp;Flaschen) erhältlich.{/s}
                                    </div>
                                {else if $sArticle.attr22 eq '12er Holzkiste'}
                                	<img src="media/image/f4/c6/b3/icon_wood_12.png" />

                                    <div class="vm-detail-custom-badges-box-text">
                                        {s namespace='frontend/index/index' name='VmProductBadgeWoodenBoxOnlyTextFirst'}{/s}
                                        ({$sArticle.attr23}{s namespace='frontend/index/index' name='VmProductBadgeBoxTextLast'}{/s}
                                    </div>
                                {else if $sArticle.attr22 eq '6er Karton'}
                                	<img src="/media/image/17/68/3b/icon_carton_6.png" />

                                    <div class="vm-detail-custom-badges-box-text">
                                        {s namespace='frontend/index/index' name='VmProductBadgeCartonBoxTextFirst'}Ausschliesslich in Original-Karton {/s}
                                        ({$sArticle.attr23}{s namespace='frontend/index/index' name='VmProductBadgeBoxTextLast'}{/s}
                                    </div>
                                {else if $sArticle.attr22 eq '12er Karton'}
                                	<img src="/media/image/4d/d3/d2/icon_carton_12.png" />

                                    <div class="vm-detail-custom-badges-box-text">
                                        {s namespace='frontend/index/index' name='VmProductBadgeCartonBoxTextFirst'}{/s}
                                        ({$sArticle.attr23}{s namespace='frontend/index/index' name='VmProductBadgeBoxTextLast'}{/s}
                                    </div>
                                {/if}
                            </div>
                        {else if ($sArticle.attr22 eq '6er Holzkiste') or ($sArticle.attr22 eq '12er Holzkiste')}
                            <div class="vm-detail-custom-badges-box">
                                {if $sArticle.attr22 eq '6er Holzkiste'}
                                	<img src="/media/image/85/0d/d6/icon_wood_6.png" />
                                    <div class="vm-detail-custom-badges-box-text">
                                        {s namespace='frontend/index/index' name='VmProductBadgeWoodenBoxTextFirst'}in Original-Holzkiste {/s}
                                        ({$sArticle.attr23}{s namespace='frontend/index/index' name='VmProductBadgeBoxTextLast'}{/s}
                                    </div>
                                {else if $sArticle.attr22 eq '12er Holzkiste'}
                                	<img src="media/image/f4/c6/b3/icon_wood_12.png" />
                                    <div class="vm-detail-custom-badges-box-text">
                                        {s namespace='frontend/index/index' name='VmProductBadgeWoodenBoxTextFirst'}{/s}
                                        ({$sArticle.attr23}{s namespace='frontend/index/index' name='VmProductBadgeBoxTextLast'}{/s}
                                    </div>
                                {/if}
                            </div>
                        {/if}

                    </div>

                </div>
            {/block}

            {* Product header *}
            {include file="frontend/detail/content/header.tpl"}

            {* "Buy now" box container *}
            {include file="frontend/detail/content/buy_container.tpl"}
        </div>

        {* Product bundle hook point *}
        {block name="frontend_detail_index_bundle"}{/block}

        {if $sArticle.is_abo_article eq 1 && sizeof($aboPakets) > 0 }
            <div class="tab-menu--cross-selling" data-scrollable="true">
                <div class="tab--navigation abo-tab--navigation">
                    <a>{s namespace='frontend/index/index' name='labelAboNavigation'}Die Weine aus dem 1. Paket:{/s}</a>
                </div>
                <div class="tab--container-list">
                    <div class="tab--container" data-tab-id="">
                        <div class="tab--header">
                            <a href="#" class="tab--title">{s namespace='frontend/index/index' name='labelAboNavigation'}Die Weine aus dem 1. Paket{/s}</a>
                        </div>
                        <div class="tab--content content--abo-package">
                            {include file="frontend/detail/tabs/abo_package.tpl" articles=$aboPakets}
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        {block name="frontend_detail_index_detail"}
            
         

            {if $smarty.server.HTTP_HOST eq 'www.schulerweine.de' or $smarty.server.HTTP_HOST eq 'schulerweine.de'}
                {* FTF for banner on ABO *}
                {$dividerPath = "/media/image/"|cat:$sArticle.attr59}
                    {if $sArticle.attr56}
                        <img id='Bestellkonditionen' class='vm-abo-img' src='{media path=$dividerPath}'>
                    {/if}
                        <div class="vm-left-side-text-area-and-images-abo">
                        {if $sArticle.leftabotextarea !=""}
                            <div class="vm-left-side-text-area-abo-has-content">
                                <h2 id="Bestellkonditionen">{s namespace='frontend/index/index' name='vmBestellkonditionenContent'}Bestellkonditionen{/s}</h2>
                                <p class="abo-left-side-text"> 
                                {$sArticle.leftabotextarea}   
                                </p>
                            
                            </div>
                        {/if}
                        {* FTF for Image on right side *}
                        <div class="vm-two-abo-images">
                            {$degustation1Path = "/media/image/"|cat:$sArticle.attr58}
                            {if $sArticle.attr55}
                                <img id='abo-img-right-side1' class='vm-abo-img-right-side1' src='{media path=$degustation1Path}'>
                            {/if}
                           
                            {$degustation2Path = "/media/image/"|cat:$sArticle.attr60}
                            {if $sArticle.attr57}
                                <img id='abo-img-right-side2' class='vm-abo-img-right-side2' src='{media path=$degustation2Path}'>
                            {/if}
                           
                        </div>
                    </div>
            {else}
                    {* FTF for banner on ABO *}
                {$dividerPath = "/media/image/"|cat:$sArticle.attr56}
                {if $sArticle.attr56}
                    <img id='Bestellkonditionen' class='vm-abo-img' src='{media path=$dividerPath}'>
                {/if}
                
                    <div class="vm-left-side-text-area-and-images-abo">
                    {if $sArticle.leftabotextarea !=""}
                        <div class="vm-left-side-text-area-abo-has-content">
                            <h2 id="Bestellkonditionen">{s namespace='frontend/index/index' name='vmBestellkonditionenContent'}Bestellkonditionen{/s}</h2>
                            <p class="abo-left-side-text"> 
                            {$sArticle.leftabotextarea}   
                            </p>
                        
                        </div>
                    {/if}
                    {* FTF for Image on right side *}
                    <div class="vm-two-abo-images">
                        {$degustation1Path = "/media/image/"|cat:$sArticle.attr55}
                        {if $sArticle.attr55}
                            <img id='abo-img-right-side1' class='vm-abo-img-right-side1' src='{media path=$degustation1Path}'>
                        {/if}
                      
                        {$degustation2Path = "/media/image/"|cat:$sArticle.attr57}
                        {if $sArticle.attr57}
                            <img id='abo-img-right-side2' class='vm-abo-img-right-side2' src='{media path=$degustation2Path}'>
                        {/if}
                       
                    </div>
                </div>
            {/if}

            
            <div class="vm-detail-custom-description-wrapper">
              <h1 class="vm-degustations-karte">
                    {s namespace='frontend/index/index' name='vmDegustationsKarteLabel'} Degustations - Karte{/s}
              </h1>

               {if $Locale eq 'fr_CH' or $Locale eq 'fr_FR'}
                <div class="vm-logo-media-print block">
                    <picture>
                        <img src="/media/image/2e/89/c2/logo-fr.png" alt="LogoMediaPrintFr">
                    </picture>
                </div>
                {else}
                <div class="vm-logo-media-print block">
                    <picture>
                        <img src="/media/image/ef/3c/2b/logo-neu.png" alt="LogoMediaPrint">
                    </picture>
                </div>
                {/if}
                {* Tab navigation *}
                {block name="frontend_detail_index_tabs"}
                    <div class="related-product-list">
                        {include file="frontend/detail/tabs.tpl"}
                    </div>
                {/block}

            </div>

        {/block}

        {* Crossselling tab panel *}
        {block name="frontend_detail_index_tabs_cross_selling"}

            {$showAlsoViewed = {config name=similarViewedShow}}
            {$showAlsoBought = {config name=alsoBoughtShow}}
            <div class="tab-menu--cross-selling related-product-list"{if $sArticle.relatedProductStreams} data-scrollable="true"{/if}>

                {* Tab navigation *}
                {include file="frontend/detail/content/tab_navigation.tpl"}

                {* Tab content container *}
                {include file="frontend/detail/content/tab_container.tpl"}
            </div>
        {/block}
    </div>
 

{/block}





