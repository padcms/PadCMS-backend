{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block {if !$grid.rows}page-list-empty{/if}">

{capture name=js}
<script type="text/javascript" src="/js/common/clients-list.js"></script>
<script type="text/javascript" src="/js/application/list.js"></script>
{/capture}
    
<div id="content-header">
    <div id="content-header-inner">
        {include file="includes/header-button.tpl" btn_url="/application/add/cid/"|cat:$clientId btn_title="Add new application"}
        {include file=includes/breadcrumbs.tpl}
    </div>
</div>

<div id="content">
    <div id="content-inner">
        <div id="content-area">
            {foreach from=$grid.rows item=item}

            <div class="hover-item">
                <h2 class="content-title content-title-d"><span><a href="/issue/list/aid/{$item.id}" class="application-title">{$item.title|escape}</a></span></h2>
                <div class="cblock-application">

                    <div class="block-clear cblock-application-inner">
                        <div class="cblock-application-info">
                            <ul>
<!--                                <li class="ico-version">{'Current version'|translate}: <b>{$item.version|escape}</b></li>-->
                                <li class="ico-issues"><b><a href="/issue/list/aid/{$item.id}">{$item.issue_count}</a></b> {'issue(s)'|translate}</li>
                            </ul>
                        </div>
                        <div class="cblock-application-text">
                            {$item.description|wordwrap:25:"\n":true|truncate:500:"...":false|escape}
                            <div class="block-clear cblock-buttons">
                                <a href="/application/delete/aid/{$item.id}" class="cbutton cbutton-delete"><span><span class="ico">{'Delete'|translate}</span></span></a>
                                <a href="/application/edit/aid/{$item.id}/cid/{$item.client}" class="cbutton cbutton-green cbutton-edit"><span><span class="ico">{'Edit'|translate}</span></span></a>
                                {if $userInfo.is_admin}
                                    <a href="#" class="cbutton cbutton-green cbutton-copy-move" id="move-application-{$item.id}-{$clientId}"><span><span class="ico">{'Move'|translate}</span></span></a>
                                    <a href="#" class="cbutton cbutton-green cbutton-copy-move" id="copy-application-{$item.id}-{$clientId}"><span><span class="ico">{'Copy'|translate}</span></span></a>
                                {/if}
                                <a href="/issue/list/aid/{$item.id}" class="cbutton cbutton-green"><span><span class="ico">{'Issues'|translate}</span></span></a>
                                {if $item.type == 'rue98we'}
                                    <a href="/subscription/list/aid/{$item.id}" class="cbutton cbutton-green"><span><span class="ico">{'Subscriptions'|translate}</span></span></a>
                                {/if}

                                <a href="#" class="cbutton cbutton-green cbutton-clear-cache" id="cc-{$item.id}-{$clientId}"><span><span class="ico">{'Clear cache'|translate}</span></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {assign var=hClass value="content-title content-title-inner content-title-a"}
            {foreachelse}
                <div class="">
                    <h2 class="content-title content-title-d"><span>{'You have no applications right now'|translate}</span></h2>
                    <div class="cblock-application">
                        <div class="block-clear cblock-application-inner">
                          <div class="empty-test">
                            <a href="{$baseUrl}/application/add/cid/{$clientId}">{'You can create a new application right now'|translate} &raquo;</a>
                          </div>
                        </div>
                    </div>
                </div>
            {/foreach}

        </div>
    </div>
</div> <!-- /#content-inner, /#content -->
<div id="content-foot">
    <div id="content-foot-inner">
        {include file="includes/elements/pager.tpl" control=$pager}
    </div>
</div>

</div>
</div>

{include file="includes/footer.tpl" include_paginator=true}