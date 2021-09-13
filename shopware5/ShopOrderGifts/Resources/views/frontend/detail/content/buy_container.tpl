{extends file='parent:frontend/detail/content/buy_container.tpl'}
{block name='frontend_detail_index_buy_container'}
    {$smarty.block.parent}

          <section class="giftArticles">
            {foreach $giftArticles as $item}
                <p>rabab {$item.name}</p>
            {/foreach}
        </section>

{/block}
