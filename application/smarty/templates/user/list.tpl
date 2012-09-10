{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block {if !$grid.rows}page-list-empty{/if}">

{capture name=js}
    <script type="text/javascript" src="/js/common/grid.js"></script>
{/capture}

<div id="main">
    <div id="main-inner" class="clear-block">
        <div id="content-header">
            <div id="content-header-inner">
                {include file="includes/header-button.tpl" btn_url="/user/add/cid/"|cat:$clientId btn_title="Add new user"}
                {include file=includes/breadcrumbs.tpl}
            </div>
        </div>

        <div id="content">
            <div id="content-inner">
                <div id="content-area">
                    {foreach from=$grid.rows item=item}
                        <div class="hover-item">
                            <h2 class="content-title content-title-b">
                                <span><a href="/user/edit/uid/{$item.id}/cid/{$item.client}">{$item.login}</a></span>
                            </h2>

                            <div class="cblock-clients cblock-current-users">
                                <div class="block-clear cblock-clients-inner">
                                    <div class="cblock-clients-user">
                                        <ul>
                                            <li class="item-users">
                                                <b>Full Name: </b><span title="{$item.creator_full_name}">{$item.creator_full_name|truncate:30:"...":true}</span>
                                            </li>
                                            <li class="item-appls">
                                                <b>Email: </b>{$item.email}
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="cblock-clients-link">
                                        <div class="block-clear cblock-buttons">
                                            <a href="/user/delete/uid/{$item.id}/cid/{$item.client}" class="cbutton cbutton-delete"><span><span class="ico">Delete</span></span></a>
                                            <a href="/user/edit/uid/{$item.id}/cid/{$item.client}" class="cbutton cbutton-green cbutton-edit"><span><span class="ico">{'Edit'|translate}</span></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {foreachelse}
                        <div class="">
                            <h2 class="content-title content-title-d"><span>{'There is no users yet'|translate}</span></h2>
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
    </div>
</div>

<div id="content-foot">
    <div id="content-foot-inner">
        {include file="includes/elements/pager.tpl" control=$pager}
    </div>
</div>

</div>
</div>

{include file="includes/footer.tpl"}
