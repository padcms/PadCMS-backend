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
            <div class="cblock-form cblock-signin cblock-add-app">
                <div class="cblock-form-mandatory">{'Mandatory fields'|translate} *</div>
                <div class="cblock-form-inner">

                    {if isset($revision) && $revision.errors}
                    <div class="errors-block">
                        <div class="error-top"></div>
                        <div class="error-mid">
                            <div class="inner">
                                <h2 class="title">{'Errors'|translate}</h2>
                                <ul>
                                    {foreach from=$revision.errors item=error}
                                    <li>{$error|escape:"html"}</li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <div class="error-bot"></div>
                    </div>
                    {/if}

                    <form method="POST" enctype="multipart/form-data" action="">
                        <input type="hidden" name="form" value="{if isset($revision)}{$revision.name}{/if}"/>

                        <div class="form-item{if isset($title) && $title.errors} error{/if}">
                            <label>{if isset($title)}{$title.title|translate|escape}{/if} <span>*</span></label>
                            <div class="form-item-wrapper">{if isset($title)}{include file="Volcano/input.tpl" control=$title _class="form-text"}{/if}</div>
                        </div>

                        {if isset($revision) && $revision.copy_from_revisions && !$revision.primaryKeyValue}
                        <div class="form-item select-themed{if $revision.errors} error{/if}">
                            <label>{$copy_from.title|translate|escape}</label>
                            <div class="form-item-wrapper">{include file="Volcano/select.tpl" control=$copy_from _values=$revision.copy_from_revisions _class="select"}</div>
                        </div>
                        {/if}

                        {if isset($revision) && $revision.states}
                        <div class="form-item select-themed{if $state.errors} error{/if}">
                            <label>{$state.title|translate|escape} <span>*</span></label>
                            <div class="form-item-wrapper">{include file="Volcano/select.tpl" control=$state _values=$revision.states _class="select"}</div>
                        </div>
                        {/if}

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
