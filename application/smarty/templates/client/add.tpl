{include file="includes/header.tpl"}

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
                <div class="cblock-form-mandatory">{'Mandatory fields'|translate} *</div>
                <div class="cblock-form-inner">

                    {if $client.errors}
                    <div class="errors-block">
                        <div class="error-top"></div>
                        <div class="error-mid">
                            <div class="inner">
                                <h2 class="title">{'Errors'|translate}</h2>
                                <ul>
                                    {foreach from=$client.errors item=error}
                                    <li>{$error|escape:"html"}</li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <div class="error-bot"></div>
                    </div>
                    {/if}

                    <form method="POST" enctype="multipart/form-data" action="">
                        <input type="hidden" name="form" value="{$client.name}"/>
                        <div class="form-item{if $title.errors} error{/if}" >
                            <label>{$title.title|escape} <span>*</span></label>
                            <div class="form-item-wrapper">
                                {include file="Volcano/input.tpl" control=$title _class="form-text"}
                            </div>
                        </div>

                        <div class="block-clear cblock-buttons">
                            <input type="submit" class="orange-but" value="{'Save'|translate}" />
                        </div>

                    </form>
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
