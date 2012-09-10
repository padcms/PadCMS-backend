{include file="includes/header.tpl" btn_url="/revision/add/iid/"|cat:$issueId btn_title="Add new revision"}

{capture name=js}
    {$smarty.capture.js}
    <script type="text/javascript" src="{$baseUrl}/js/revision/list.js"></script>
    <script type="text/javascript" src="/js/common/clients-list.js"></script>
{/capture}

<div id="main">
<div id="main-inner" class="clear-block {if !$grid.rows}page-list-empty{/if}">

<div id="content-header">
    <div id="content-header-inner">
        {include file="includes/header-button.tpl" btn_url="/revision/add/iid/"|cat:$issueId btn_title="Add new revision"}
        {include file=includes/breadcrumbs.tpl}
    </div>
</div>

<div id="content">
    <div id="content-inner">
        <div id="content-area">
            {foreach from=$grid.rows item=item}
            <div class="hover-item">

                <h2 class="content-title content-title-b"><span><a href="/page-map/show/rid/{$item.id}">{$item.title|escape}</a></span></h2>

                <div class="cblock-clients cblock-revisions">
                    <div class="block-clear cblock-clients-inner">

                        <div class="cblock-clients-user">
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
                                <li class="item-lastrev">
                                    {'last update on'|translate} {$item.updated}
                                </li>
                            </ul>
                        </div>

                        <div class="cblock-issue-status">
                            {if $item.state=="published" || $item.state=="for-review"}
                                <h3 class="title-published">{$item.state}</h3>
                            {elseif $item.state=="work-in-progress"}
                                <h3 class="title-inprogress"><a id="revision-action-{$item.issue}-{$item.id}" href="#">{$item.state}</a></h3>
                            {elseif $item.state=="archived"}
                                <h3 class="title-archived">{$item.state}</h3>
                            {/if}

                            <ul>
                                <li class="item-showrev"><a title="{'Download revision'|translate}" href="/export/revision/id/{$item.id}">{'Download'|translate}</a></li>
                            </ul>
                        </div>

                        <div class="cblock-clients-link">
                            <div class="block-clear cblock-buttons">
                                <a href="/revision/delete/rid/{$item.id}/iid/{$item.issue}" class="cbutton cbutton-delete"><span><span class="ico">{'Delete'|translate}</span></span></a>
                                <a href="/revision/edit/rid/{$item.id}/iid/{$item.issue}" class="cbutton cbutton-green cbutton-edit"><span><span class="ico">{'Edit'|translate}</span></span></a>
                                {if $userInfo.is_admin}
                                <a href="#" class="cbutton cbutton-green cbutton-copy-move" id="move-revision-{$item.id}-{$issueId}"><span><span class="ico">{'Move'|translate}</span></span></a>
                                <a href="#" class="cbutton cbutton-green cbutton-copy-move" id="copy-revision-{$item.id}-{$issueId}"><span><span class="ico">{'Copy'|translate}</span></span></a>
                                {/if}
                                <a href="/page-map/show/rid/{$item.id}" class="cbutton cbutton-green item-gotomap"><span><span class="ico">{'Go to page editor'|translate}</span></span></a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {foreachelse}
                <div class="">
                    <h2 class="content-title content-title-d"><span>{'Chosen issues has no revision yet'|translate}</span></h2>
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

<div id="revision-list-actions" style="display: none;">
    <h4 style="padding-left: 5px; padding-top: 5px; padding-right: 4px; color: #707070;">{'Publish the revision or delete.'|translate}</h4>
    <div class="block-clear cblock-buttons" style="padding-left: 144px; padding-top: 12px; width: 148px; width: 148px;">
        <a id="revision-list-action-publish" href="#" class="cbutton cbutton-green"><span><span class="ico">{'Publish'|translate}</span></span></a>
        <a id="revision-list-action-delete" href="#" class="cbutton cbutton-delete"><span><span class="ico">{'Delete'|translate}</span></span></a>
    </div>
</div>

{include file="includes/footer.tpl"}
