{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block">

<div id="content-header">
    <div id="content-header-inner">
        <div class="breadcrumb">&nbsp;</div>
    </div>
</div>

<div id="content">
    <div id="content-inner">
        <div id="content-area">
            <div class="cblock-form cblock-signin cblock-add-app">
                <div class="cblock-form-mandatory"></div>
                <div class="cblock-form-inner">

                    {if $error}
                    <div class="errors-block">
                        <div class="error-top"></div>
                        <div class="error-mid">
                            <div class="inner">
                                <h2 class="title">{'Error'|translate}</h2>
                                <ul>
                                    <li>{'Error when downloading static pdf file'|translate}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="error-bot"></div>
                    </div>
                    {/if}
                    <a href="/issue/edit/iid/{$issueId}/aid/{$appId}">{'Back to edit'|translate}</a>
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
