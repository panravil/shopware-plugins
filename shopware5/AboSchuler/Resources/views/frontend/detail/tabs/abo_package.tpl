<div class="related--content">
    <div class="product-slider" data-initonevent="onShowContent-alsobought" data-product-slider="true"  data-itemsPerSlide="1" data-itemMinWidth="500">
        <div class="product-slider--container">
           <div id="abo-count" style="display: none">{$articles|count}</div>
            {foreach $articles as $article}
                <div class="product-slider--item">
                    {include file="frontend/listing/product-box/abo-box-basic.tpl" sArticle=$article  productBoxLayout="slider" fixedImageSize=""}
                </div>
            {/foreach}
        </div>
    </div>
</div>