{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block {if !$grid.rows}page-list-empty{/if}">

{capture name=js}
    <script type="text/javascript" src="/js/common/grid.js"></script>
{/capture}

<div id="content-header">
    <div id="content-header-inner">
        {include file="includes/header-button.tpl" btn_url="/client/add" btn_title="Add a new client"}
        {include file=includes/breadcrumbs.tpl}
    </div>
</div>

<div id="content">
    <div id="content-inner">
        <div id="content-area">
            {foreach from=$grid.rows item=item}
                <div class="hover-item">
                    <h2 class="content-title content-title-d">
                        <span>
                            <a class="client-title" href="/application/list/cid/{$item.id}">{$item.title}</a>
                        </span>
                    </h2>

                    <div class="cblock-clients">
                        <div class="block-clear cblock-clients-inner">
                            <div class="cblock-clients-user">
                                <ul>
                                    <li class="item-users">
                                        {'Users count'|translate} : <b><a href="/user/list/cid/{$item.id}">{$item.user_count}</a></b>
                                    </li>
                                    <li class="item-appls">
                                        {'Applications count'|translate} : <b><a href="/application/list/cid/{$item.id}">{$item.application_count}</a></b>
                                    </li>
                                </ul>
                            </div>

                            <div class="cblock-clients-link">
                                <ul>
                                    <li class="item-gotoclt">
                                        <a href="/user/list/cid/{$item.id}">{'Go to client user list'|translate}</a>
                                    </li>
                                </ul>

                                <div class="block-clear cblock-buttons">
                                    <a href="/client/delete/cid/{$item.id}" class="cbutton cbutton-delete">
                                        <span><span class="ico">{'Delete'|translate}</span></span>
                                    </a>

                                    <a href="/client/edit/cid/{$item.id}" class="cbutton cbutton-green cbutton-edit">
                                        <span><span class="ico">{'Edit'|translate}</span></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {assign var=hClass value="content-title content-title-inner content-title-a"}
                </div>
            {foreachelse}
                <div class="">
                    <h2 class="content-title content-title-d"><span>{'Has no client yet'|translate}</span></h2>
                    <div class="cblock-clients cblock-current-users">
                        <div class="block-clear cblock-clients-inner">
                            <br/><br/>
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

{include file="includes/footer.tpl"}