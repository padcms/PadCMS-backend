{assign var='counter' value=0}
var locale = {literal}{{/literal}
    {foreach from=$locale key=key item=item}
        '{$key|escape}' : '{$item|escape}'
        {assign var='counter' value=$counter+1}
        {if $locale|@count != $counter},{/if}
    {/foreach}
{literal}}{/literal};
