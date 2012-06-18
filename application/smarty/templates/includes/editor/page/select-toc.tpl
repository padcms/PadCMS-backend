{if !$_step}
    {assign var=_step value=0}
{/if}
{assign var=spaces value=$_step*4}

{foreach from=$_items item=item}
    <option value="{$item.id}" {if $_value==$item.id}selected{/if} {if $item.deleted=='yes'}disabled{/if}>{section name=space start=0 loop=$spaces}-{/section}&nbsp;{$item.title}</option>
    {if $item.child}
        {include file="includes/editor/page/select-toc.tpl" _items=$item.child _step=$_step+1}
    {/if}

{/foreach}