{if $control.totalPages > 1}
<div class="pager">
    <input name="pageSizeURI" type="hidden" value="{$control.pageSizeURI}{$control.gridName}PageSize/"/>
    <ul>
        {if $control.prevPage != $control.currentPage}
        <li class="first"><a href="{$control.pagerURI}{$control.gridName}Page/1">&laquo; {'First'|translate}</a></li>
        <li class="prev"><a href="{$control.pagerURI}{$control.gridName}Page/{$control.prevPage}">&lsaquo; {'Previous'|translate}</a></li>
        {else}
            <li class="first"><a href="#" onclick="return false;" class="off" >&laquo; {'First'|translate}</a></li>
            <li class="prev"><a href="#" onclick="return false;" class="off" >&lsaquo; {'Previous'|translate}</a></li>
        {/if}

        {foreach from=$control.nearestPages item=item}
            {if $item.isCurrentPage}
                <li class="active"><a href="#" onclick="return false;">{$item.page}</a></li>
            {else}
                <li><a href="{$control.pagerURI}{$control.gridName}Page/{$item.page}">{$item.page}</a></li>
            {/if}
        {/foreach}

        {if $control.totalPages != $control.currentPage}
            <li class="next"><a href="{$control.pagerURI}{$control.gridName}Page/{$control.nextPage}">{'Next'|translate} &rsaquo;</a></li>
            <li class="last"><a href="{$control.pagerURI}{$control.gridName}Page/{$control.totalPages}">{'Last'|translate} &raquo;</a></li>
        {else}
            <li class="next"><a href="#" onclick="return false;" class="off">{'Next'|translate} &rsaquo;</a></li>
            <li class="last"><a href="#" onclick="return false;" class="off">{'Last'|translate} &raquo;</a></li>
        {/if}
{*
        <li class="perpage">{'Per page'|translate}:
            <select class="normal">
                <option value="1" {if $control.perPage == 1}selected{/if}>1</option>
                <option value="2" {if $control.perPage == 2}selected{/if}>2</option>
                <option value="3" {if $control.perPage == 3}selected{/if}>3</option>
                <option value="4" {if $control.perPage == 4}selected{/if}>4</option>
                <option value="10" {if $control.perPage == 10}selected{/if}>10</option>
                <option value="25" {if $control.perPage == 25}selected{/if}>25</option>
                <option value="50" {if $control.perPage == 50}selected{/if}>50</option>
                <option value="100" {if $control.perPage == 100}selected{/if}>100</option>
            </select>
        </li>*}
    </ul>
</div>
{/if}