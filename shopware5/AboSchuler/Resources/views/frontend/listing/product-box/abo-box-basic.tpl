{namespace name="frontend/listing/abo_box_article"}

{block name="frontend_listing_box_article"}
    <div class="product--box box--{$productBoxLayout}"
         data-page-index="{$pageIndex}"
         data-ordernumber="{$sArticle.ordernumber}"
         {if !{config name=disableArticleNavigation}} data-category-id="{$sCategoryCurrent}"{/if}>

        {block name="frontend_listing_box_article_content"}
            <div class="box--content is--rounded">

                {block name='frontend_listing_box_article_info_container'}
                    <div class="product--info">
                        <div class="abo-package-image">
                            <span class="image--element">
                                <span class="image--media">
                                    {$desc = $sArticle.articleName|escape}
                                    {if isset($sArticle.image.thumbnails)}
                                        {if $sArticle.image.description}
                                            {$desc = $sArticle.image.description|escape}
                                        {/if}
                                        <img srcset="{$sArticle.image.thumbnails[1].sourceSet}"
                                            alt="{$desc}"
                                            title="{$desc|truncate:160}" />
                                    {else}
                                        <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                                            alt="{$desc}"
                                            title="{$desc|truncate:160}" />
                                    {/if}
                                </span>
                            </span>
                        </div>
                        <div class="abo-package-content">
                            <h2 class="abo-package-title">
                                {$sArticle.articleName|truncate:50|escapeHtml} 
                            </h2>
                            <span class="is--bold">      
                                {$sArticle.attr33|truncate:50|escapeHtml} {$sArticle.purchaseunit} {$sArticle.sUnit.description}          
                            </span>
                            <div class="abo-package-description">
                                {$sArticle.description_long}
                            </div>
                            <div class="abo-variant-table">
                                <div class="abo-variant-row">
                                    <label class="abo-variant-title">{s namespace='frontend/index/index' name='ProductAttr2'}Traubensorte:{/s}</label>
                                    <div class="abo-variant-value">{$sArticle.attr2}</div>
                                </div>
                                <div class="abo-variant-row">
                                    <label class="abo-variant-title">{s namespace='frontend/index/index' name='ProductAttr11'}Ausbau in Barrique:{/s}</label>
                                    <div class="abo-variant-value">
                                        {if $sArticle.attr11} 
                                            {$sArticle.attr11} 
                                        {else }
                                            {$sArticle.attr46} 
                                        {/if}
                                    </div>
                                </div>
                                <div class="abo-variant-row">
                                    <label class="abo-variant-title">{s namespace='frontend/index/index' name='ProductAttr13'}Alkoholgehalt:{/s}</label>
                                    <div class="abo-variant-value">{$sArticle.attr13}</div>
                                </div>
                                <div class="abo-variant-row">
                                    <label class="abo-variant-title">{s namespace='frontend/index/index' name='VmDetailUpperDescLagerpotenzial'}Lagerpotenzial:{/s}</label>
                                    <div class="abo-variant-value">{$sArticle.attr21}</div>
                                </div>
                                <div class="abo-variant-row">
                                    <label class="abo-variant-title">{s namespace='frontend/index/index' name='VmDetailUpperDescGenussZu'}Genuss zu:{/s}</label>
                                    <div class="abo-variant-value">{$sArticle.attr8}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/block}
            </div>
        {/block}
    </div>
{/block}
