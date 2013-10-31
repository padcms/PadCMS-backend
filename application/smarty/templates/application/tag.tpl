{include file="includes/header.tpl"}

{capture name=js}
<script type="text/javascript" src="/js/application/tag.js"></script>
<script type="text/javascript">
    window.appId = {$aid};
</script>
{/capture}
<div id="main">
<div id="main-inner" class="clear-block">

<div id="content-header">
    <div id="content-header-inner">
        {include file=includes/breadcrumbs.tpl}
    </div>
</div>

<div id="content">
    <div id="content-inner">
        <div id="content-area">
            <div class="cblock-form cblock-signin cblock-add-app error-detected">
                <div class="cblock-form-mandatory">&nbsp;</div>
                <div class="cblock-form-inner">

                    {if isset($application) && $application.errors}
                    <div class="errors-block">
                        <div class="error-top"></div>
                        <div class="error-mid">
                            <div class="inner">
                                <h2 class="title">{'Errors'|translate}</h2>
                                <ul>
                                    {foreach from=$application.errors item=error}
                                    <li>{$error|escape:"html"}</li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <div class="error-bot"></div>
                    </div>
                    {/if}

                    <div id="application-tabs">
                        <ul>
                            <li><a href="/application/add/aid/{$aid}/cid/{$cid}">{'Edit'|translate}</a></li>
                            <li><a href="" class="active">{'Tags'|translate}</a></li>
                            <div class="clear"></div>
                        </ul>
                    </div>

                    <div id="existing-tags-wrapper" class="tags-wrapper">
                        {'Application tags:'|translate}
                        <ul id="existing-tags" class="connectedSortable tags">
                            {foreach from=$existingTags item=tag}
                                <li class="ui-state-default" id="{$tag.te_id}"">{$tag.value}</li>
                            {/foreach}
                        </ul>
                    </div>
                    <div id="possible-tags-wrapper" class="tags-wrapper">
                        {'Tags:'|translate}
                        <ul id="possible-tags" class="connectedSortable tags">
                            {foreach from=$possibleTags item=tag}
                                <li class="ui-state-highlight" id="{$tag.te_id}"">{$tag.value}</li>
                            {/foreach}
                        </ul>
                    </div>
                    <div class="clear"></div>

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
