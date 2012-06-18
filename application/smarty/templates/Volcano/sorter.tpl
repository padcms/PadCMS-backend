{if $grid.sortOrder == $name}
{if $grid.sortDirection == "ASC"}
<a id="activeSorter" href="{$grid.sorterURI}{$grid.name}Order/{$name}/{$grid.name}Dir/Desc/">{$text|escape} <img src="{$baseUrl}/img/Volcano/down.png" border=0/></a>
{else}
<a id="activeSorter" href="{$grid.sorterURI}{$grid.name}Order/{$name}/">{$text|escape} <img src="{$baseUrl}/img/Volcano/up.png" border=0/></a>
{/if}
{else}
<a href="{$grid.sorterURI}{$grid.name}Order/{$name}/">{$text|escape}</a>
{/if}