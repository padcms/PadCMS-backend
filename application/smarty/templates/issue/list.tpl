{include file="includes/header.tpl"}

{capture name=js}
    {$smarty.capture.js}
    <script type="text/javascript" src="/js/issue/list.js"></script>
    <script type="text/javascript" src="/js/common/clients-list.js"></script>
{/capture}

<div id="main">
<div id="main-inner" class="clear-block {if !$grid.rows}page-list-empty{/if}">

<div id="content-header">
    <div id="content-header-inner">
        {include file="includes/header-button.tpl" btn_url="/issue/add/aid/"|cat:$appId btn_title="Add new issue"}
        {include file=includes/breadcrumbs.tpl}
    </div>
</div>

<div id="content">
    <div id="content-inner">
        <div id="content-area">
            {foreach from=$grid.rows item=item}
            <div class="hover-item">
                <h2 class="content-title content-title-b"><span><a href="/revision/list/iid/{$item.id}">{$item.title|escape}</a></span></h2>
                <div class="cblock-issue">
                    <div class="block-clear cblock-issue-inner">

                        <div class="cblock-issue-id">
                            <div class="title">
                                {'ID'|translate}: <b><span title="{$item.number}">{$item.number|truncate:10:"...":true}</span></b>
                            </div>
                        </div>

                        <div class="cblock-issue-info">
                            <ul>
                                <li class="item-created">
                                    {if $item.creator_full_name == " "}
                                        {assign var="creator_name" value=$item.creator_login}
                                    {else}
                                        {assign var="creator_name" value=$item.creator_full_name}
                                    {/if}
                                    {'Created by'|translate}
                                    {if $item.creator_role != 'admin'}<a href="/user/index/uid/{$item.creator_uid}">{/if}<span title="{$creator_name}">{$creator_name|truncate:20:"...":true}</span>{if $item.creator_role != 'admin'}</a>{/if}
                                    {'on'|translate} {$item.created}
                                </li>
                                <li class="item-lastupd">{'Last update'|translate} {$item.updated_date} {'at'|translate} {$item.updated_time}</li>
                                <li class="item-lastrev">{'Last revision'|translate}
                                    #<a href="/page-map/show/rid/{$item.last_revision}">{$item.last_revision}</a>
                                </li>
                            </ul>
                        </div>

                        <div class="cblock-issue-status">
                            {if $item.state=="published"}
                                <h3 class="title-published">{$item.state}</h3>
                            {elseif $item.state=="work-in-progress"}
                                <h3 class="title-inprogress"><a title="{'Click to publish'|translate}" id="issue-action-{$item.application_id}-{$item.id}" href="#">{$item.state}</a></h3>
                            {elseif $item.state=="archived"}
                                <h3 class="title-archived">{$item.state}</h3>
                            {/if}

                            {if $item.published_revision}
                                <ul>
                                  <li class="item-showrev"><a title="{'Download published revision'|translate}" href="/export/revision/id/{$item.published_revision}">{'Download'|translate}</a></li>
                                  {*<li class="item-gotomap"><a title="{'Go to published revision map'|translate}" href="/page-map/show/rid/{$item.published_revision}">{'Go to page map'|translate}</a></li>*}
                                </ul>
                            {/if}
                        </div>

                        <div class="cblock-clients-link">
                            <div class="block-clear cblock-buttons">
                                <a href="/issue/delete/iid/{$item.id}/aid/{$item.application_id}" class="cbutton cbutton-delete"><span><span class="ico">{'Delete'|translate}</span></span></a>
                                <a href="/issue/edit/iid/{$item.id}/aid/{$item.application_id}" class="cbutton cbutton-green cbutton-edit"><span><span class="ico">{'Edit'|translate}</span></span></a>
                                {if $userInfo.is_admin}
                                <a href="#" class="cbutton cbutton-green cbutton-copy-move" id="move-issue-{$item.id}-{$appId}"><span><span class="ico">{'Move'|translate}</span></span></a>
                                <a href="#" class="cbutton cbutton-green cbutton-copy-move" id="copy-issue-{$item.id}-{$appId}"><span><span class="ico">{'Copy'|translate}</span></span></a>
                                {/if}
                                {if empty($item.last_revision) }
                                    <a href="javascript:void(0)" class="cbutton cbutton-green" onclick="window.ui.popupMesage('there_are_no_revisions')">
                                {else}
                                    <a href="/page-map/show/rid/{$item.last_revision}" class="cbutton cbutton-green">
                                {/if}
                                    <span><span class="ico">{'Go to page editor'|translate}</span></span>
                                </a>
                                <a href="/revision/list/iid/{$item.id}" class="cbutton cbutton-green"><span><span class="ico">{'List revisions'|translate}</span></span></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            {assign var=hClass value="content-title content-title-inner content-title-c"}
            {foreachelse}
                <div class="">
                    <h2 class="content-title content-title-d"><span>{'Chosen application has no issue yet'|translate}</span></h2>
                    <div class="cblock-clients cblock-current-users">
                        <div class="block-clear cblock-clients-inner">
                            <br/><br/>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>

<div id="content-foot">
    <div id="content-foot-inner">
        {include file="includes/elements/pager.tpl" control=$pager}
    </div>
</div>

</div>
</div>

<div id="issue-list-actions" style="display: none;">
    <h4 style="padding-left: 5px; padding-top: 5px; padding-right: 4px; color: #707070;">{'Publish the issue or delete.'|translate}</h4>
    <div class="block-clear cblock-buttons" style="padding-left: 144px; padding-top: 12px; width: 148px; width: 148px;">
        <a id="issue-list-action-publish" href="#" class="cbutton cbutton-green"><span><span class="ico">{'Publish'|translate}</span></span></a>
        <a id="issue-list-action-delete" href="#" class="cbutton cbutton-delete"><span><span class="ico">{'Delete'|translate}</span></span></a>
    </div>
</div>

{include file="includes/footer.tpl"}