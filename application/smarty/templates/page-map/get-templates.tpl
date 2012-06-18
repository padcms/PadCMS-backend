<div class="cont-popup" id="template-list">
    <div class="templ-choose">

        {assign var="counter" value=0}
        {foreach from=$templateList item=item}

            {assign var="counter" value=$counter+1}

            {if $item.disabled}
                <span href="#" class="template" title="{$item.description}">
                    <span class="name">{$item.description}</span>
                    <img alt="{$item.description}" src="{$item.imageUrl}">
                </span>
            {else}
                <a href="#" class="template" id="template-{$item.id}" title="{$item.description}">
                    <span class="name">{$item.description}</span>
                    <img alt="{$item.description}" src="{$item.imageUrl}">
                </a>
            {/if}

            {if $counter>=4}
                <div class="border-div"></div>
                {assign var="counter" value=0}
            {/if}

        {/foreach}

    </div>
</div>
<div class="cont-popup-bot"></div>
