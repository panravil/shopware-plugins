{extends file='parent:frontend/detail/index.tpl'}

{block name='frontend_index_content'}
	{$isWhiskey = false}
	{foreach $sBreadcrumb as $crumb}
    	{if $crumb.id eq 1291 or $crumb.id eq 1489}
        	{$isWhiskey = true}
        {/if}        
    {/foreach}

    {if $sArticle.is_abo_article eq 1}
        {include file="frontend/detail/content_abo.tpl"}
	{else if $isWhiskey}
		{include file="frontend/detail/content_spirit.tpl"}
	{else if !$sArticle.attr34 eq 1 and $sArticle.attr36 eq 0}
    	{include file="frontend/detail/content.tpl"}
    {else if $sArticle.attr36 eq 1}
    	{include file="frontend/detail/content_simple.tpl"}
    {else}
    	{include file="frontend/detail/content_paket.tpl"}
    {/if}    
{/block}