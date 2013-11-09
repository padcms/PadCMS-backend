{include file="includes/header.tpl"}

<div id="main">
    <div id="main-inner" class="clear-block">

        <div id="content-header">
            <div id="content-header-inner">
                <div class="breadcrumb">
                    <a href="{$baseURL}/application/list/cid/{$iClientId}">{'Application list'|translate}</a> &gt; {'Subscriptions'|translate}
                </div>
            </div>
        </div>

        <div id="content" class="rpt subscription-list">
            <div id="content-inner">
                <div id="content-area">
                    <div class="cblock-form cblock-signin cblock-add-app">

                        <div class="cblock-form-inner">
                            {* **************************************************** *}
                            <div class="grid">
                                <div class="tb-main">
                                    <table>
                                        <tbody>
                                        <tr class="thead">
                                            <th colspan="5">
                                                <div class="tb-title">
                                                    <div class="tb-title-inner">
                                                        <span class="left">{'Subscription list'|translate}</span>
                                                        <span class="right"><a class="active" href="/subscription/add/aid/{$iAppId}">{'Add subscription'|translate}</a></span>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr class="thead">
                                            <th>{"Button title"|translate}</th>
                                            <th>{"Itunes id"|translate}</th>
                                            <th>{"Google id"|translate}</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        {foreach from=$grid.rows item=item}
                                            <tr>
                                                <td class="name">{$item.button_title}</td>
                                                <td class="name">{$item.itunes_id}</td>
                                                <td class="name">{$item.google_id}</td>
                                                <td class="name"><a href="/subscription/edit/sid/{$item.id}">{'Edit'|translate}</a></td>
                                                <td class="name"><a href="/subscription/delete/sid/{$item.id}" class="cbutton-delete">{'Delete'|translate}</a></td>
                                            </tr>
                                        {foreachelse}
                                            {'Chosen application has no subscriptions yet'|translate}
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {* **************************************************** *}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="content-foot">
            <div id="content-foot-inner"></div>
        </div>

    </div>
</div>

{include file="includes/footer.tpl"}
