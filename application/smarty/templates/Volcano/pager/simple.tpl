<span>
    {if $grid.page == 1}
    <span style="color: #999" ><img src="{$baseUrl}/img/Volcano/left_gray.png" border=0/></span>
    {else}
    <a style="text-decoration: none" href="{$grid.pagerURI}{$grid.name}Page/{math equation="page - 1" page=$grid.page}/"><img src="{$baseUrl}/img/Volcano/left.png" border=0/></a>
    {/if}
</span>
{if $grid.totalPages > 0}
<span>Page {$grid.page} of {$grid.totalPages}</span>
{/if}
<span>
    {if $grid.page >= $grid.totalPages}
    <span style="color: #999" ><img src="{$baseUrl}/img/Volcano/right_gray.png" border=0/></a>
    {else}
    <a style="text-decoration: none" href="{$grid.pagerURI}{$grid.name}Page/{math equation="page + 1" page=$grid.page}/"><img src="{$baseUrl}/img/Volcano/right.png" border=0/></a>
    {/if}
</span>