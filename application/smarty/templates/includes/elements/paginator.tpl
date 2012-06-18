{foreach from=$pages item=item}
    {if $item.current}
        <li><span>{$key}</span></li>
    {else}
        <li><a href="/stories/index{if $sort}/sort/{$sort}{/if}/page/{$key}">{$key}</a></li>
    {/if}
{/foreach}
