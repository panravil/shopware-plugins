{extends file='parent:frontend/detail/content/buy_container.tpl'}
{block name='frontend_detail_index_buy_container'}
    {$smarty.block.parent}
        {block name="frontend_index_content"}
          <section class="giftArticles">
              {giftArticles|print_r}
            {foreach $giftArticles as $item}
                <p>{$item.name}</p>
            {/foreach}
        </section>
        {/block}
{/block}
