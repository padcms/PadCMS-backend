<div class="title">
    <span class="title">#{$page.id}</span><span title="{$page.template_description}" class="title">{$page.template_description|truncate:24:"...":true}</span>

    <a class="ico reload" id="page-refresh-button" href="#" ></a>
    {if $page.canChangeTemplate}
        <a class="ico change-template" id="page-change-template-button" href="#" ></a>
    {/if}
    {if $page.canDelete}
        <a class="ico trash"  id="page-delete-button"  href="#" ></a>
    {/if}
    <span class="move {if $panel_place}to-{$panel_place|escape}{/if}">
        <a id="toggle-etitor-postion" class="" href="#">{'Move' | translate}</a>
    </span>
    <a class="hide"></a>
</div>

<div class="cont" {*style="display:none;"*}>

    <div class="selects">

        <span class="labe">{'Title'|translate}</span>
        <div class="toc">
            <div class="form-item-wrapper">
                <input id="page-title-input" value="{$page.title}" type="text" class="form-text" />
            </div>
            <a id="page-title-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
        </div>

        <span class="labe">{'Machine name'|translate}</span>
        <div class="toc">
            <div class="form-item-wrapper">
                <input id="page-machine-name-input" value="{$page.machine_name}" type="text" class="form-text" />
            </div>
            <a id="page-machine-name-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
        </div>

        {if $page.showPdfPage}
            <span class="labe">{'PDF page'|translate}</span>
            <div class="pdf-page">
                <div class="form-item-wrapper">
                    <input id="page-pdf-page-input" value="{$page.pdf_page}" type="text" class="form-text" />
                </div>
                <a id="page-pdf-select-btn" href="#" class="cbutton">
                    <span><span class="ico">{'Select'}</span></span>
                </a>
                <a id="page-pdf-page-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
                <div class="clr"></div>
            </div>
        {/if}

        <span class="labe">{'TOC'|translate}</span>

        <div class="toc select-themed">
            <select id="page-toc-select" onchange="pageInfo.onSaveToc();">
                {foreach from=$page.tocList item=item key=key}
                    <option
                        {if $key == $page.tocItem}selected="selected"{/if}
                        value="{$key}">{$item}</option>
                {/foreach}
            </select>

            <a id="show-toc-dialog" href="#" class="cbutton">
                <span><span class="ico">{'Edit'}</span></span>
            </a>
        </div>

    </div>

    <span class="labe">{'Tags'|translate}</span>

    <div id="page-tag-list" class="tags">
        {foreach from=$page.tags item=item}
        <span id="tag-{$item.id}" class="tag level-one">{$item.title}<a href="/editor/delete-tag/id/{$item.id}"></a></span>
        {/foreach}
    </div>

    <div class="tags">
        <div class="add-tag">
            <div class="form-item-wrapper">
                <input id="page-tag-input" type="text" class="form-text" />
            </div>
            <a id="page-tag-btn" class="cbutton" href="#"><span><span class="ico">{'Add'|translate}</span></span></a>
        </div>
    </div>

</div>

