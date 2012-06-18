<div class="breadcrumb">
{if isset($breadcrumbs)}
    {if $breadcrumbs.user.role == 'admin'}
        <a href="{$baseURL}/">Client list</a>
        {if $breadcrumbs.breadcrumbs}
        &gt;
        {/if}
    {/if}

    {foreach name=breadcrumbs from=$breadcrumbs.breadcrumbs key=key item=item}
        {if $item.title}
            {if !$smarty.foreach.breadcrumbs.last}
                <a href="{$item.url}">
                {/if}

                {if !isset($item.action) || !$item.action || $item.action == 'list'}
                    {assign var=next value=$item.next|cat:s}
                    {$item.title|escape} : {$next|translate}
                {else}
                    {if $item.action == 'add' || $item.action == 'index'}
                        {$item.action|translate}
                    {else}
                        {$item.title|escape} : {$item.action|translate}
                    {/if}
                {/if}

                {if !$smarty.foreach.breadcrumbs.last}
                </a>
                {/if}

            {if !$smarty.foreach.breadcrumbs.last}&gt;{/if}
        {/if}
    {/foreach}
{/if}
</div>